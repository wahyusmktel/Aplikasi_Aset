<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\AssetInspection;
use App\Models\VehicleLog;
use App\Models\Asset;
use App\Models\LabUsageLog;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    public function verify(string $docNumber)
    {
        // 1. Coba cari di AssetAssignment
        $assignment = AssetAssignment::where('checkout_doc_number', $docNumber)
            ->orWhere('return_doc_number', $docNumber)
            ->with(['asset', 'employee'])
            ->first();

        if ($assignment) {
            $isReturn = ($assignment->return_doc_number === $docNumber);
            $documentType = $isReturn ? 'Berita Acara Pengembalian Aset' : 'Berita Acara Serah Terima Aset';
            $assetName = $assignment->asset->name ?? 'Aset Tidak Ditemukan';
            $employeeName = $assignment->employee->name ?? 'Pegawai Tidak Ditemukan';
            $transactionDate = $isReturn ? $assignment->returned_date : $assignment->assigned_date;

            // Kirim variabel $assignment agar view bisa cek detail spesifik jika perlu
            return view('public.verification', compact('assignment', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
        }

        // 2. Jika tidak ketemu, cari di AssetInspection
        $inspection = AssetInspection::where('inspection_doc_number', $docNumber)
            ->with(['asset', 'inspector'])
            ->first();

        if ($inspection) {
            $documentType = 'Berita Acara Pemeriksaan Kondisi';
            $assetName = $inspection->asset->name ?? 'Aset Tidak Ditemukan';
            $employeeName = $inspection->inspector->name ?? 'Sistem';
            $transactionDate = $inspection->inspection_date;
            $isReturn = null; // Tidak relevan

            // Kirim variabel $inspection
            return view('public.verification', compact('inspection', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
        }

        // 3. Jika tidak ketemu juga, cari di VehicleLog
        $vehicleLog = VehicleLog::where('checkout_doc_number', $docNumber)
            ->orWhere('checkin_doc_number', $docNumber)
            ->with(['asset', 'employee'])
            ->first(); // Ubah dari firstOrFail() ke first()

        if ($vehicleLog) {
            $isReturn = ($vehicleLog->checkin_doc_number === $docNumber);
            $documentType = $isReturn ? 'Berita Acara Pengembalian Kendaraan' : 'Berita Acara Penggunaan Kendaraan';
            $assetName = $vehicleLog->asset->name ?? 'Kendaraan Tidak Ditemukan';
            $employeeName = $vehicleLog->employee->name ?? 'Pegawai Tidak Ditemukan';
            $transactionDate = $isReturn ? $vehicleLog->return_time : $vehicleLog->departure_time;

            // Kirim variabel $vehicleLog
            return view('public.verification', compact('vehicleLog', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
        }

        $labLog = LabUsageLog::where('checkin_doc_number', $docNumber)
            ->orWhere('checkout_doc_number', $docNumber)
            ->with(['room', 'teacher'])
            ->first();

        if ($labLog) {
            $isReturn = ($labLog->checkout_doc_number === $docNumber); // Checkout di Lab artinya Selesai/Keluar (Return logic)
            $documentType = $isReturn ? 'Berita Acara Selesai Penggunaan Lab' : 'Berita Acara Penggunaan Lab';
            $assetName = $labLog->room->name ?? 'Ruangan Tidak Ditemukan';
            $employeeName = $labLog->teacher->name ?? 'Guru Tidak Ditemukan';
            $transactionDate = $labLog->usage_date;

            // Kirim variabel $labLog
            return view('public.verification', compact('labLog', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
        }

        // 4. Jika masih tidak ketemu, cari di Asset (untuk BAPh)
        $asset = Asset::where('disposal_doc_number', $docNumber)
            ->with(['personInCharge']) // Load PJ Aset
            ->firstOrFail(); // Gunakan firstOrFail() di sini sebagai fallback terakhir

        // Jika ketemu di Asset (artinya BAPh)
        $documentType = 'Berita Acara Penghapusan Aset';
        $assetName = $asset->name;
        // Kita bisa asumsikan employeeName adalah Penanggung Jawab Aset untuk BAPh
        $employeeName = $asset->personInCharge->name ?? 'Penanggung Jawab Tidak Ditemukan';
        $transactionDate = $asset->disposal_date;
        $isReturn = null; // Tidak relevan

        // Kirim variabel $asset
        return view('public.verification', compact('asset', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
    }

    public function verifyMaintenance(string $docNumber)
    {
        $maintenance = AssetMaintenance::where('doc_number', $docNumber)
            ->with(['asset'])
            ->firstOrFail();

        return view('public.verification-maintenance', compact('maintenance'));
    }
}
