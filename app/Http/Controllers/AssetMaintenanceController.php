<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    /**
     * Helper untuk membuat nomor surat unik maintenance.
     */
    private function generateDocumentNumber()
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->toRoman($month);

        // Hitung berdasarkan maintenance di tahun ini
        $latest = AssetMaintenance::whereYear('created_at', $year)->count() + 1;
        $sequence = sprintf('%04d', $latest);

        // BAM = Berita Acara Maintenance
        return "BAM/SMKTL/{$romanMonth}/{$year}/{$sequence}";
    }

    // Fungsi toRoman (salin dari AssetAssignmentController jika belum ada)
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
     * Menyimpan catatan maintenance baru.
     */
    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'maintenance_date' => 'required|date',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'technician' => 'nullable|string|max:255',
        ]);

        $docNumber = $this->generateDocumentNumber();

        // Buat catatan maintenance DENGAN nomor surat
        $maintenance = $asset->maintenances()->create(array_merge($request->all(), [
            'doc_number' => $docNumber
        ]));

        // --- Generate PDF ---
        $verificationUrl = route('public.verifyMaintenance', $docNumber);
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => true,
            'scale' => 5,
        ]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();

        $pdf = Pdf::loadView('assets.maintenance-report-pdf', [
            'title' => 'Berita Acara ' . $maintenance->type,
            'maintenance' => $maintenance,
            'asset' => $asset->load('personInCharge'), // Load PJ Aset
            'headmaster' => $headmaster,
            'qrCode' => $qrCode,
        ]);

        $safeFilename = str_replace('/', '-', $docNumber);
        alert()->success('Berhasil!', 'Catatan maintenance ditambahkan. PDF Berita Acara akan diunduh.');
        // Langsung download setelah menyimpan
        return $pdf->download($safeFilename . '.pdf');
    }

    /**
     * Menghapus catatan maintenance.
     */
    public function destroy(AssetMaintenance $maintenance)
    {
        // Simpan ID aset sebelum dihapus untuk redirect
        $assetId = $maintenance->asset_id;
        $maintenance->delete();

        alert()->success('Berhasil!', 'Catatan maintenance berhasil dihapus.');
        return redirect()->route('assets.show', $assetId);
    }

    /**
     * Menangani download PDF BA Maintenance untuk record tertentu.
     */
    public function downloadReport(AssetMaintenance $maintenance)
    {
        $docNumber = $maintenance->doc_number;
        if (!$docNumber) {
            alert()->error('Gagal!', 'Dokumen Berita Acara untuk riwayat ini tidak ditemukan.');
            return back();
        }

        $verificationUrl = route('public.verifyMaintenance', $docNumber);
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => true,
            'scale' => 5,
        ]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $asset = $maintenance->asset()->with('personInCharge')->first();
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();

        $pdf = Pdf::loadView('assets.maintenance-report-pdf', [
            'title' => 'Berita Acara ' . $maintenance->type,
            'maintenance' => $maintenance,
            'asset' => $asset,
            'headmaster' => $headmaster,
            'qrCode' => $qrCode,
        ]);

        $safeFilename = str_replace('/', '-', $docNumber);
        return $pdf->download($safeFilename . '.pdf');
    }
}
