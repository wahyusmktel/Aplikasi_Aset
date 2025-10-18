<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetInspection;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan PDF
use Carbon\Carbon; // Tambahkan Carbon
use chillerlan\QRCode\QRCode; // Tambahkan QR Code
use chillerlan\QRCode\QROptions; // Tambahkan QR Options

class AssetInspectionController extends Controller
{
    /**
     * Helper untuk membuat nomor surat unik.
     */
    private function generateDocumentNumber($type)
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->toRoman($month);

        $latest = AssetInspection::whereYear('created_at', $year)->count() + 1;
        $sequence = sprintf('%04d', $latest);

        return "{$type}/SMKTL/{$romanMonth}/{$year}/{$sequence}";
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

    /**
     * Menyimpan catatan inspeksi baru.
     */
    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'inspection_date' => 'required|date',
            'condition' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $docNumber = $this->generateDocumentNumber('BAPK'); // BAPK = Berita Acara Pemeriksaan Kondisi

        $inspection = $asset->inspections()->create([
            'inspection_doc_number' => $docNumber, // Simpan nomor surat
            'inspection_date' => $request->inspection_date,
            'condition' => $request->condition,
            'notes' => $request->notes,
            'inspector_id' => Auth::id(),
        ]);

        // Optional: Update status aset jika kondisi 'Rusak Berat'
        if ($request->condition === 'Rusak Berat') {
            $asset->update(['current_status' => 'Rusak']);
        } elseif ($request->condition === 'Baik' && $asset->current_status === 'Rusak') {
            // Jika diperbaiki dan status sebelumnya 'Rusak', kembalikan ke 'Tersedia' (jika tidak sedang dipinjam)
            if (!$asset->currentAssignment) {
                $asset->update(['current_status' => 'Tersedia']);
            }
        }

        // --- GENERATE PDF ---
        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $inspector = Auth::user(); // User yang login
        $asset->load('personInCharge'); // Pastikan PJ Aset ter-load

        $pdf = Pdf::loadView('assets.bast-inspection-pdf', [
            'inspection' => $inspection,
            'asset' => $asset,
            'headmaster' => $headmaster,
            'inspector' => $inspector,
            'qrCode' => $qrCode,
        ]);

        $safeFilename = str_replace('/', '-', $docNumber);
        alert()->success('Berhasil!', 'Catatan inspeksi berhasil ditambahkan. PDF Berita Acara akan diunduh.');
        return $pdf->download($safeFilename . '.pdf');
    }

    /**
     * Menangani download PDF BAPK untuk inspeksi tertentu.
     */
    public function downloadBast(AssetInspection $inspection)
    {
        $docNumber = $inspection->inspection_doc_number;
        if (!$docNumber) {
            alert()->error('Gagal!', 'Dokumen BAPK untuk riwayat ini tidak ditemukan.');
            return back();
        }

        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $asset = $inspection->asset()->with('personInCharge')->first();
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $inspector = $inspection->inspector; // Ambil dari relasi

        $pdf = Pdf::loadView('assets.bast-inspection-pdf', [
            'inspection' => $inspection,
            'asset' => $asset,
            'headmaster' => $headmaster,
            'inspector' => $inspector,
            'qrCode' => $qrCode,
        ]);

        $safeFilename = str_replace('/', '-', $docNumber);
        return $pdf->download($safeFilename . '.pdf');
    }

    // public function destroy(AssetInspection $inspection) { ... } // Buat jika perlu
}
