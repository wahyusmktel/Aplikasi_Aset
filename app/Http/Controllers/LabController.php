<?php

namespace App\Http\Controllers;

use App\Models\LabSchedule;
use App\Models\LabUsageLog;
use App\Models\Room;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class LabController extends Controller
{
    // --- HELPER NOMOR SURAT & ROMAN (Sama seperti modul lain agar konsisten) ---
    private function generateDocumentNumber($type)
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->toRoman($month);

        // Hitung urutan khusus Lab
        $latest = LabUsageLog::whereYear('created_at', $year)->count() + 1;
        $sequence = sprintf('%04d', $latest);

        return "{$type}/LAB-SMKTL/{$romanMonth}/{$year}/{$sequence}";
    }

    private function toRoman($number)
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
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
    // Halaman Utama Lab (Dashboard Lab)
    public function index()
    {
        // Ambil ruangan yang diasumsikan sebagai lab (bisa difilter nama "Lab" atau "Bengkel")
        // Untuk sekarang kita ambil semua ruangan agar fleksibel
        $rooms = Room::orderBy('name')->get();

        // Ambil Log hari ini
        $todaysLogs = LabUsageLog::with(['room', 'teacher'])
            ->whereDate('usage_date', Carbon::today())
            ->latest('check_in_time')
            ->get();

        // Ambil Jadwal Hari Ini
        $dayName = Carbon::now()->locale('id')->isoFormat('dddd'); // Senin, Selasa...
        $todaysSchedules = LabSchedule::with(['room', 'teacher'])
            ->where('day_of_week', $dayName)
            ->orderBy('start_time')
            ->get();

        return view('labs.index', compact('rooms', 'todaysLogs', 'todaysSchedules'));
    }

    // Simpan Jadwal Baru
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'teacher_id' => 'required|exists:employees,id',
            'subject' => 'required|string',
            'class_group' => 'required|string',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        LabSchedule::create($request->all());
        alert()->success('Berhasil', 'Jadwal Lab berhasil ditambahkan.');
        return back();
    }

    // Catat Masuk (Check-in)
    public function storeLog(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'teacher_id' => 'required|exists:employees,id',
            'class_group' => 'required|string',
            'activity_description' => 'required|string',
        ]);

        // Cek apakah ruangan sedang dipakai (belum checkout)
        $isOccupied = LabUsageLog::where('room_id', $request->room_id)
            ->whereNull('check_out_time')
            ->exists();

        if ($isOccupied) {
            alert()->error('Gagal', 'Ruangan ini sedang digunakan dan belum di-checkout.');
            return back();
        }

        $docNumber = $this->generateDocumentNumber('BA-IN');

        LabUsageLog::create([
            'checkin_doc_number' => $docNumber,
            'room_id' => $request->room_id,
            'teacher_id' => $request->teacher_id,
            'class_group' => $request->class_group,
            'activity_description' => $request->activity_description,
            'usage_date' => now(),
            'check_in_time' => now(),
            'condition_before' => $request->condition_before ?? 'Baik',
        ]);

        alert()->success('Berhasil', 'Penggunaan Lab dimulai. BA Check-In dibuat.');
        return back();
    }

    // Catat Keluar (Check-out)
    public function checkoutLog(Request $request, LabUsageLog $log)
    {
        $docNumber = $this->generateDocumentNumber('BA-OUT');
        $log->update([
            'checkout_doc_number' => $docNumber,
            'check_out_time' => now(),
            'condition_after' => $request->condition_after ?? 'Baik',
            'notes' => $request->notes,
        ]);

        alert()->success('Selesai', 'Penggunaan Lab selesai. BA Check-Out dibuat.');
        return back();
    }

    // Hapus Jadwal
    public function destroySchedule(LabSchedule $schedule)
    {
        $schedule->delete();
        alert()->success('Dihapus', 'Jadwal berhasil dihapus.');
        return back();
    }

    /**
     * Menampilkan halaman riwayat dengan filter.
     */
    public function history(Request $request)
    {
        $rooms = Room::orderBy('name')->get();

        // Default filter: Bulan ini jika tidak ada input
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $roomId = $request->input('room_id');

        $logs = LabUsageLog::with(['room', 'teacher'])
            ->when($roomId, fn($q) => $q->where('room_id', $roomId))
            ->whereDate('usage_date', '>=', $startDate)
            ->whereDate('usage_date', '<=', $endDate)
            ->latest('usage_date')
            ->paginate(20)
            ->withQueryString();

        return view('labs.history', compact('logs', 'rooms', 'startDate', 'endDate', 'roomId'));
    }

    /**
     * Ekspor Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'room_id' => $request->input('room_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LabLogsExport($filters), 'log-penggunaan-lab.xlsx');
    }

    /**
     * Download Laporan PDF.
     */
    public function downloadPDF(Request $request)
    {
        $roomId = $request->input('room_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = LabUsageLog::with(['room', 'teacher'])
            ->when($roomId, fn($q) => $q->where('room_id', $roomId))
            ->when($startDate, fn($q) => $q->whereDate('usage_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('usage_date', '<=', $endDate))
            ->orderBy('usage_date', 'asc')
            ->orderBy('check_in_time', 'asc');

        $logs = $query->get();

        if ($logs->isEmpty()) {
            alert()->info('Info', 'Tidak ada data log untuk periode ini.');
            return back();
        }

        $pj = \App\Models\Employee::where('position', 'Kaur Sarpras')->first(); // Sesuaikan jabatan
        $ks = \App\Models\Employee::where('position', 'Kepala Sekolah')->first();
        $labName = $roomId ? Room::find($roomId)->name : 'Semua Ruang Lab';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('labs.report-pdf', compact('logs', 'pj', 'ks', 'startDate', 'endDate', 'labName'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penggunaan-lab.pdf');
    }

    // --- METHOD BARU: DOWNLOAD BA LAB ---
    public function downloadBast(LabUsageLog $log, string $type)
    {
        $isCheckout = ($type === 'out');
        $docNumber = $isCheckout ? $log->checkout_doc_number : $log->checkin_doc_number;

        if (!$docNumber) {
            alert()->error('Gagal', 'Dokumen tidak ditemukan.');
            return back();
        }

        $title = $isCheckout ? 'Berita Acara Selesai Penggunaan Lab' : 'Berita Acara Penggunaan Lab';

        // Generate QR Code (Untuk verifikasi keaslian)
        // Kita arahkan ke route public verify yang sudah ada (bisa menangani doc number apapun)
        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $kaurLab = Employee::where('position', 'Kaur Lab')->first() ?? Employee::where('position', 'Kaur Sarpras')->first(); // Penanggung Jawab Lab

        $pdf = Pdf::loadView('labs.bast-pdf', compact(
            'log',
            'title',
            'isCheckout',
            'docNumber',
            'qrCode',
            'headmaster',
            'kaurLab'
        ));

        $safeFilename = str_replace('/', '-', $docNumber);
        return $pdf->download($safeFilename . '.pdf');
    }
}
