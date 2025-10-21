<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\VehicleLog;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            return response()->json(['message' => 'Akun tidak terkait dengan data pegawai.'], 403);
        }

        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'start_odometer' => 'required|integer|min:0',
            'condition_on_checkout' => 'required|string|max:255',
            // departure_time kita set otomatis saja
        ]);

        $asset = Asset::find($validated['asset_id']);

        // Validasi tambahan
        if ($asset->category_id != $this->getVehicleCategoryId() || $asset->current_status != 'Tersedia') {
            return response()->json(['message' => 'Kendaraan tidak tersedia atau bukan kendaraan dinas.'], 422);
        }

        // Kita tidak generate BAST di API, cukup catat log
        $log = VehicleLog::create([
            'asset_id' => $asset->id,
            'employee_id' => $user->employee->id,
            'departure_time' => now(), // Waktu saat ini
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            'start_odometer' => $validated['start_odometer'],
            'condition_on_checkout' => $validated['condition_on_checkout'],
            // checkout_doc_number bisa dibuat opsional atau diskip untuk mobile
        ]);

        $asset->update(['current_status' => 'Digunakan']);

        return response()->json([
            'message' => 'Checkout kendaraan berhasil dicatat.',
            'log' => $log->load('asset:id,name') // Kirim kembali data log simpel
        ], 201); // Status 201 Created
    }

    /**
     * Catat pengembalian kendaraan (checkin) via API.
     */
    public function checkin(Request $request, VehicleLog $log) // Gunakan route model binding
    {
        $user = Auth::user();
        // Pastikan log ini milik user yang login dan belum dikembalikan
        if ($log->employee_id != $user->employee?->id || $log->return_time != null) {
            return response()->json(['message' => 'Log tidak valid atau sudah dikembalikan.'], 403);
        }

        $validated = $request->validate([
            'end_odometer' => 'required|integer|min:' . $log->start_odometer,
            'condition_on_checkin' => 'required|string|max:255',
            'notes' => 'nullable|string',
            // return_time kita set otomatis
        ]);

        $log->update([
            'return_time' => now(), // Waktu saat ini
            'end_odometer' => $validated['end_odometer'],
            'condition_on_checkin' => $validated['condition_on_checkin'],
            'notes' => $validated['notes'],
            // checkin_doc_number bisa dibuat opsional atau diskip
        ]);

        $log->asset()->update(['current_status' => 'Tersedia']);

        return response()->json([
            'message' => 'Checkin kendaraan berhasil dicatat.',
            'log' => $log->load('asset:id,name')
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
}
