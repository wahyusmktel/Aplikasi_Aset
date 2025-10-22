<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\VehicleLog;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleApiController extends Controller
{
    /**
     * Helper untuk mendapatkan ID Kategori Kendaraan
     */
    private function getVehicleCategoryId()
    {
        // Cache this lookup if needed
        return \App\Models\Category::where('name', 'KENDARAAN BERMOTOR DINAS / KBM DINAS')->value('id');
    }

    /* ====== Tambahan: penomoran dokumen ====== */
    private function toRoman($number)
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $ret = '';
        while ($number > 0) {
            foreach ($map as $r => $i) {
                if ($number >= $i) {
                    $number -= $i;
                    $ret .= $r;
                    break;
                }
            }
        }
        return $ret;
    }
    private function generateDocumentNumber($type)
    {
        $year = date('Y');
        $romanMonth = $this->toRoman((int)date('m'));
        $count1 = \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count();
        $count2 = \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('return_doc_number')->count();
        $count3 = \App\Models\AssetInspection::whereYear('created_at', $year)->whereNotNull('inspection_doc_number')->count();
        $count4 = \App\Models\VehicleLog::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count();
        $count5 = \App\Models\VehicleLog::whereYear('created_at', $year)->whereNotNull('checkin_doc_number')->count();
        $seq = sprintf('%04d', $count1 + $count2 + $count3 + $count4 + $count5 + 1);
        return "{$type}/SMKTL/{$romanMonth}/{$year}/{$seq}";
    }
    /* ======================================== */

    /**
     * List kendaraan yang tersedia (status Tersedia).
     */
    public function availableVehicles()
    {
        $vehicles = Asset::where('category_id', $this->getVehicleCategoryId())
            ->where('current_status', 'Tersedia')
            ->select('id', 'name', 'asset_code_ypt') // Hanya kirim data yg perlu
            ->orderBy('name')
            ->get();
        return response()->json($vehicles);
    }

    /**
     * Catat penggunaan kendaraan (checkout) via API.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return response()->json(['message' => 'Akun tidak terkait data pegawai.'], 403);
        }

        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'start_odometer' => 'required|integer|min:0',
            'condition_on_checkout' => 'required|string|max:255',
        ]);

        $asset = Asset::find($validated['asset_id']);
        if ($asset->category_id != $this->getVehicleCategoryId() || $asset->current_status != 'Tersedia') {
            return response()->json(['message' => 'Kendaraan tidak tersedia atau bukan kendaraan dinas.'], 422);
        }

        // Buat nomor dokumen & log
        $docNumber = $this->generateDocumentNumber('BASTK');
        $log = VehicleLog::create([
            'checkout_doc_number' => $docNumber,
            'asset_id' => $asset->id,
            'employee_id' => $user->employee->id,
            'departure_time' => now(),
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            'start_odometer' => $validated['start_odometer'],
            'condition_on_checkout' => $validated['condition_on_checkout'],
        ]);
        $asset->update(['current_status' => 'Digunakan']);

        // URL API download
        $bastUrl = route('api.vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkout']);

        return response()->json([
            'message' => 'Checkout kendaraan berhasil.',
            'log' => $log->load('asset:id,name'),
            'bast_url' => $bastUrl,
            'doc_number' => $docNumber,
        ], 201);
    }

    /**
     * Catat pengembalian kendaraan (checkin) via API.
     */
    public function checkin(Request $request, VehicleLog $log)
    {
        $user = Auth::user();
        if ($log->employee_id != $user->employee?->id || $log->return_time != null) {
            return response()->json(['message' => 'Log tidak valid atau sudah dikembalikan.'], 403);
        }

        $validated = $request->validate([
            'end_odometer' => 'required|integer|min:' . $log->start_odometer,
            'condition_on_checkin' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $docNumber = $this->generateDocumentNumber('BAPK');

        $log->update([
            'checkin_doc_number' => $docNumber,
            'return_time' => now(),
            'end_odometer' => $validated['end_odometer'],
            'condition_on_checkin' => $validated['condition_on_checkin'],
            'notes' => $validated['notes'] ?? null,
        ]);
        $log->asset()->update(['current_status' => 'Tersedia']);

        $bastUrl = route('api.vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkin']);

        return response()->json([
            'message' => 'Checkin kendaraan berhasil.',
            'log' => $log->load('asset:id,name'),
            'bast_url' => $bastUrl,
            'doc_number' => $docNumber,
        ]);
    }

    /**
     * Lihat riwayat penggunaan kendaraan oleh pegawai yang login.
     */
    public function myHistory(Request $request)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return response()->json(['message' => 'Akun tidak terkait dengan data pegawai.'], 403);
        }

        $history = VehicleLog::where('employee_id', $user->employee->id)
            ->with('asset:id,name,asset_code_ypt') // Hanya ambil kolom yg perlu dari asset
            ->latest('departure_time')
            ->paginate(20); // Paginasi untuk mobile

        return response()->json($history);
    }

    /**
     * Melihat detail log spesifik.
     */
    public function logDetail(VehicleLog $log)
    {
        // Pastikan log milik user yg login
        if ($log->employee_id != Auth::user()->employee?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($log->load('asset:id,name,asset_code_ypt'));
    }

    /* ====== PDF untuk API ====== */
    private function generateBastPdf(VehicleLog $log, string $type)
    {
        $isCheckin = $type === 'checkin';
        $docNumber = $isCheckin ? $log->checkin_doc_number : $log->checkout_doc_number;
        $title = $isCheckin ? 'Berita Acara Pengembalian Kendaraan Dinas' : 'Berita Acara Penggunaan Kendaraan Dinas';

        $verificationUrl = route('public.verify', $docNumber); // gunakan route verifikasi yang sudah ada
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

    public function downloadBast(VehicleLog $log, string $type)
    {
        $docNumber = $type === 'checkin' ? $log->checkin_doc_number : $log->checkout_doc_number;
        if (!$docNumber) {
            return response()->json(['message' => 'Dokumen belum tersedia untuk log ini.'], 404);
        }
        $pdf = $this->generateBastPdf($log, $type);
        $filename = str_replace('/', '-', $docNumber) . '.pdf';
        return $pdf->download($filename);
    }
}
