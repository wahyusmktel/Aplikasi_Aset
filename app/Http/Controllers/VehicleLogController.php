<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\VehicleLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use App\Exports\VehicleLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VehicleLogNotification;
use Illuminate\Support\Facades\Log;

class VehicleLogController extends Controller
{
    /**
     * Membuat nomor surat unik yang sekuensial per tahun di seluruh aplikasi.
     * Format: TIPE/SMKTL/BULAN_ROMawi/TAHUN/URUTAN
     *
     * @param string $type Tipe dokumen (e.g., BASTK, BAPK)
     * @return string Nomor surat yang unik
     */
    private function generateDocumentNumber($type)
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->toRoman($month);

        // Hitung semua nomor surat yang sudah ada di tahun ini dari SEMUA tabel
        // untuk memastikan nomor urut berikutnya benar-benar unik.
        $count1 = \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count();
        $count2 = \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('return_doc_number')->count();
        $count3 = \App\Models\AssetInspection::whereYear('created_at', $year)->whereNotNull('inspection_doc_number')->count();
        $count4 = \App\Models\VehicleLog::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count();
        $count5 = \App\Models\VehicleLog::whereYear('created_at', $year)->whereNotNull('checkin_doc_number')->count();

        $totalDocsThisYear = $count1 + $count2 + $count3 + $count4 + $count5;

        // Nomor urut berikutnya adalah total dokumen + 1
        $sequence = sprintf('%04d', $totalDocsThisYear + 1);

        return "{$type}/SMKTL/{$romanMonth}/{$year}/{$sequence}";
    }

    /**
     * Mengubah angka bulan menjadi format angka Romawi.
     *
     * @param int $number Angka 1-12
     * @return string Angka Romawi (I-XII)
     */
    private function toRoman($number)
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    /**
     * Menampilkan halaman riwayat log kendaraan.
     */
    public function index(Request $request)
    {
        // Widget, Chart, Table logic - mirip MaintenanceHistoryController
        $totalLogs = VehicleLog::count();
        $activeLogs = VehicleLog::whereNull('return_time')->count();

        // Chart (Penggunaan per Bulan)
        $logsPerMonth = VehicleLog::select(
            DB::raw("DATE_FORMAT(departure_time, '%Y-%m') as month"),
            DB::raw('count(*) as total')
        )
            ->where('departure_time', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')->orderBy('month', 'asc')->pluck('total', 'month');

        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels[] = $month->isoFormat('MMM YYYY');
            $chartData[] = $logsPerMonth->get($monthKey, 0);
        }

        // Tabel Data
        $search = $request->input('search');
        $logs = VehicleLog::with(['asset', 'employee'])
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            })
            ->latest('departure_time')->paginate(15)->withQueryString();

        return view('vehicle-logs.index', compact('logs', 'totalLogs', 'activeLogs', 'chartLabels', 'chartData'));
    }

    /**
     * Menyimpan data checkout kendaraan.
     */
    public function storeCheckout(Request $request, Asset $asset)
    {
        // Validasi khusus kendaraan
        if ($asset->category->name !== 'KENDARAAN BERMOTOR DINAS / KBM DINAS') {
            alert()->error('Gagal!', 'Aset ini bukan kendaraan dinas.');
            return back();
        }
        if ($asset->current_status !== 'Tersedia') {
            alert()->error('Gagal!', 'Kendaraan sedang tidak tersedia.');
            return back();
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'departure_time' => 'required|date',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'start_odometer' => 'required|integer|min:0',
            'condition_on_checkout' => 'required|string|max:255',
        ]);

        $docNumber = $this->generateDocumentNumber('BASTK'); // K = Kendaraan

        $log = VehicleLog::create([
            'checkout_doc_number' => $docNumber,
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'departure_time' => $request->departure_time,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'start_odometer' => $request->start_odometer,
            'condition_on_checkout' => $request->condition_on_checkout,
        ]);

        $asset->update(['current_status' => 'Digunakan']);

        // Generate PDF
        $pdf = $this->generateBastPdf($log, 'checkout');

        // === KIRIM NOTIFIKASI ===
        try {
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                ->notify(new VehicleLogNotification($log, 'checkout')); // <-- Panggil notifikasi baru
        } catch (\Exception $e) {
            Log::error('Telegram notification failed (Vehicle Checkout): ' . $e->getMessage());
        }
        // =======================

        alert()->success('Berhasil!', 'Kendaraan telah dicatat keluar. PDF BAST akan diunduh.');
        return $pdf->download(str_replace('/', '-', $docNumber) . '.pdf');
    }

    /**
     * Menyimpan data checkin kendaraan.
     */
    public function storeCheckin(Request $request, VehicleLog $log)
    {
        $request->validate([
            'return_time' => 'required|date|after_or_equal:departure_time',
            'end_odometer' => 'required|integer|min:' . $log->start_odometer, // Minimal harus sama atau lebih besar dari start
            'condition_on_checkin' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $docNumber = $this->generateDocumentNumber('BAPK'); // K = Kendaraan

        $log->update([
            'checkin_doc_number' => $docNumber,
            'return_time' => $request->return_time,
            'end_odometer' => $request->end_odometer,
            'condition_on_checkin' => $request->condition_on_checkin,
            'notes' => $request->notes,
        ]);

        $log->asset()->update(['current_status' => 'Tersedia']);

        // Generate PDF
        $pdf = $this->generateBastPdf($log, 'checkin');

        // === KIRIM NOTIFIKASI ===
        try {
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                ->notify(new VehicleLogNotification($log, 'checkin')); // <-- Panggil notifikasi baru
        } catch (\Exception $e) {
            Log::error('Telegram notification failed (Vehicle Checkin): ' . $e->getMessage());
        }
        // =======================

        alert()->success('Berhasil!', 'Kendaraan telah dicatat kembali. PDF BAP akan diunduh.');
        return $pdf->download(str_replace('/', '-', $docNumber) . '.pdf');
    }

    /**
     * Generate PDF BAST/BAP Kendaraan.
     */
    private function generateBastPdf(VehicleLog $log, string $type)
    {
        $isCheckin = ($type === 'checkin');
        $docNumber = $isCheckin ? $log->checkin_doc_number : $log->checkout_doc_number;
        $title = $isCheckin ? 'Berita Acara Pengembalian Kendaraan Dinas' : 'Berita Acara Penggunaan Kendaraan Dinas';

        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $asset = $log->asset()->with('personInCharge')->first();
        $employee = $log->employee;
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();

        return Pdf::loadView('vehicle-logs.bast-pdf', compact(
            'log',
            'asset',
            'employee',
            'headmaster',
            'title',
            'isCheckin',
            'qrCode'
        ));
    }

    /**
     * Download BAST/BAP Kendaraan yang sudah ada.
     */
    public function downloadBast(VehicleLog $log, string $type)
    {
        $docNumber = ($type === 'checkin') ? $log->checkin_doc_number : $log->checkout_doc_number;

        if (!$docNumber) {
            $bastType = ($type === 'checkin') ? 'Pengembalian' : 'Penggunaan';
            alert()->error('Gagal!', "Dokumen BAST {$bastType} untuk log ini tidak ditemukan.");
            return back();
        }

        $pdf = $this->generateBastPdf($log, $type);
        return $pdf->download(str_replace('/', '-', $docNumber) . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new VehicleLogsExport($search), 'log-kendaraan.xlsx');
    }

    public function downloadPDF(Request $request)
    {
        $search = $request->input('search');
        $logs = VehicleLog::with(['asset', 'employee'])
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            })
            ->latest('departure_time')->get();

        if ($logs->isEmpty()) { /* ... handle empty ... */
        }

        $pj = Employee::where('position', 'Kaur Sarpras')->first();
        $ks = Employee::where('position', 'Kepala Sekolah')->first();
        $kota = "Bandar Lampung";

        $pdf = Pdf::loadView('vehicle-logs.report-pdf', compact('logs', 'pj', 'ks', 'kota'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-log-kendaraan.pdf');
    }
}
