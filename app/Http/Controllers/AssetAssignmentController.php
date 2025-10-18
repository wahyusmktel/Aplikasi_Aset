<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AssetAssignmentController extends Controller
{
    /**
     * Helper untuk membuat nomor surat unik.
     */
    private function generateDocumentNumber($type)
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->toRoman($month);

        $latest = AssetAssignment::whereYear('created_at', $year)->count() + 1;
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

    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'condition_on_assign' => 'required|string|max:255',
        ]);

        $docNumber = $this->generateDocumentNumber('BAST');

        // Cek apakah aset sudah dipinjam
        if ($asset->current_status !== 'Tersedia') {
            alert()->error('Gagal!', 'Aset ini sedang tidak tersedia atau sudah dipinjam.');
            return back();
        }

        // Catat penugasan
        $assignment = AssetAssignment::create([
            'checkout_doc_number' => $docNumber,
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'condition_on_assign' => $request->condition_on_assign,
        ]);

        // Update status aset
        $asset->update(['current_status' => 'Dipinjam']);

        $verificationUrl = route('public.verify', $docNumber);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 5,
            'imageBase64' => true,
        ]);

        // Buat QR code dan langsung dapatkan sebagai data URI (base64)
        $qrCode = (new QRCode($options))->render($verificationUrl);

        // Generate PDF
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $employee = Employee::find($request->employee_id);

        $pdf = Pdf::loadView('assets.bast-pdf', [
            'title' => 'Berita Acara Serah Terima Aset',
            'assignment' => $assignment,
            'asset' => $asset,
            'employee' => $employee,
            'headmaster' => $headmaster,
            'isReturn' => false,
            'qrCode' => $qrCode,
        ]);

        $safeFilename = str_replace('/', '-', $docNumber);

        alert()->success('Berhasil!', 'Aset telah diserahkan. PDF Berita Acara akan diunduh.');
        return $pdf->download($safeFilename . '.pdf');
    }

    /**
     * Menangani proses pengembalian aset (check-in).
     */
    public function returnAsset(Request $request, AssetAssignment $assignment)
    {
        $request->validate([
            'returned_date' => 'required|date',
            'condition_on_return' => 'required|string|max:255',
        ]);

        $docNumber = $this->generateDocumentNumber('BAP'); // BAP = Berita Acara Pengembalian

        // Update catatan penugasan dengan data pengembalian
        $assignment->update([
            'return_doc_number' => $docNumber,
            'returned_date' => $request->returned_date,
            'condition_on_return' => $request->condition_on_return,
            'notes' => $request->notes,
        ]);

        // Update status aset kembali menjadi "Tersedia"
        $asset = $assignment->asset;
        $asset->update(['current_status' => 'Tersedia']);

        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 5,
            'imageBase64' => true,
        ]);

        $qrCode = (new QRCode($options))->render($verificationUrl);

        // Generate PDF
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $employee = $assignment->employee;

        $pdf = Pdf::loadView('assets.bast-pdf', [
            'title' => 'Berita Acara Pengembalian Aset',
            'assignment' => $assignment,
            'asset' => $asset,
            'employee' => $employee,
            'headmaster' => $headmaster,
            'isReturn' => true,
            'qrCode' => $qrCode,
        ]);

        $safeFilename = str_replace('/', '-', $docNumber);

        alert()->success('Berhasil!', 'Aset telah dikembalikan. PDF Berita Acara akan diunduh.');
        return $pdf->download($safeFilename . '.pdf');
    }

    /**
     * Menangani download PDF BAST untuk assignment tertentu, berdasarkan jenisnya.
     */
    public function downloadBast(AssetAssignment $assignment, string $type) // Tambahkan $type
    {
        // Tentukan jenis BAST berdasarkan parameter $type
        $isReturn = ($type === 'return');

        // Ambil nomor surat yang sesuai
        $docNumber = $isReturn ? $assignment->return_doc_number : $assignment->checkout_doc_number;

        // Jika nomor surat belum ada, beri error
        if (!$docNumber) {
            $bastType = $isReturn ? 'Pengembalian' : 'Serah Terima';
            alert()->error('Gagal!', "Dokumen BAST {$bastType} untuk riwayat ini tidak ditemukan.");
            return back();
        }

        // Tentukan judul PDF
        $title = $isReturn ? 'Berita Acara Pengembalian Aset' : 'Berita Acara Serah Terima Aset';

        // Generate QR Code (URL tetap sama, mengarah ke nomor surat yang relevan)
        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => true,
            'scale' => 5,
        ]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        // Load data yang dibutuhkan
        $asset = $assignment->asset()->with('personInCharge')->first();
        $employee = $assignment->employee;
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();

        // Generate PDF
        $pdf = Pdf::loadView('assets.bast-pdf', [
            'title' => $title,
            'assignment' => $assignment,
            'asset' => $asset,
            'employee' => $employee,
            'headmaster' => $headmaster,
            'isReturn' => $isReturn, // Gunakan $isReturn yang sudah ditentukan
            'qrCode' => $qrCode,
        ]);

        // Buat nama file yang aman
        $safeFilename = str_replace('/', '-', $docNumber);

        // Langsung download
        return $pdf->download($safeFilename . '.pdf');
    }
}
