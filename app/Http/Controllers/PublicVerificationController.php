<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\AssetInspection;
use App\Models\VehicleLog;
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

            return view('public.verification', compact('inspection', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
        }

        // 3. Jika tidak ketemu juga, cari di VehicleLog
        $vehicleLog = VehicleLog::where('checkout_doc_number', $docNumber)
            ->orWhere('checkin_doc_number', $docNumber)
            ->with(['asset', 'employee'])
            ->firstOrFail(); // Jika tidak ketemu di sini, akan 404

        // Jika ketemu di VehicleLog (firstOrFail memastikan $vehicleLog pasti ada di sini)
        $isReturn = ($vehicleLog->checkin_doc_number === $docNumber); // Tentukan apakah ini checkin
        $documentType = $isReturn ? 'Berita Acara Pengembalian Kendaraan' : 'Berita Acara Penggunaan Kendaraan';
        $assetName = $vehicleLog->asset->name ?? 'Kendaraan Tidak Ditemukan';
        $employeeName = $vehicleLog->employee->name ?? 'Pegawai Tidak Ditemukan';
        $transactionDate = $isReturn ? $vehicleLog->return_time : $vehicleLog->departure_time;

        // Kirim data yang relevan ke view
        return view('public.verification', compact('vehicleLog', 'documentType', 'isReturn', 'assetName', 'employeeName', 'transactionDate', 'docNumber'));
    }

    public function verifyMaintenance(string $docNumber)
    {
        $maintenance = AssetMaintenance::where('doc_number', $docNumber)
            ->with(['asset'])
            ->firstOrFail();

        return view('public.verification-maintenance', compact('maintenance'));
    }
}
