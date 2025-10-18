<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Untuk user yg login

class AssetDisposalController extends Controller
{
    // --- COPY HELPER NOMOR SURAT & ROMAN DARI CONTROLLER SEBELUMNYA ---
    private function generateDocumentNumber($type)
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->toRoman($month);

        // Hitung semua nomor surat di TAHUN ini
        $count1 = \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count();
        $count2 = \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('return_doc_number')->count();
        $count3 = \App\Models\AssetInspection::whereYear('created_at', $year)->whereNotNull('inspection_doc_number')->count();
        $count4 = \App\Models\VehicleLog::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count();
        $count5 = \App\Models\VehicleLog::whereYear('created_at', $year)->whereNotNull('checkin_doc_number')->count();
        $count6 = \App\Models\Asset::whereYear('disposal_date', $year)->whereNotNull('disposal_doc_number')->count(); // Hitung BAPh

        $totalDocsThisYear = $count1 + $count2 + $count3 + $count4 + $count5 + $count6;
        $sequence = sprintf('%04d', $totalDocsThisYear + 1);

        return "{$type}/SMKTL/{$romanMonth}/{$year}/{$sequence}";
    }

    private function toRoman($number)
    { /* ... kode sama ... */
    }
    // --- ---

    /**
     * Menampilkan form untuk disposal aset.
     */
    public function create(Asset $asset)
    {
        // Cek apakah aset sudah di-dispose
        if ($asset->disposal_date || $asset->current_status == 'Dihapusbukukan' || $asset->current_status == 'Hilang') {
            alert()->info('Info', 'Aset ini sudah dalam proses atau telah dihapusbukukan.');
            return redirect()->route('assets.show', $asset->id);
        }
        // Cek apakah aset sedang dipinjam/digunakan
        if ($asset->current_status != 'Tersedia' && $asset->current_status != 'Rusak') { // Izinkan disposal jika Tersedia atau Rusak
            alert()->warning('Peringatan', 'Aset ini sedang dipinjam/digunakan. Kembalikan terlebih dahulu sebelum disposal.');
            return redirect()->route('assets.show', $asset->id);
        }

        return view('assets.dispose', compact('asset'));
    }

    /**
     * Menyimpan data disposal dan men-generate BAPh.
     */
    public function store(Request $request, Asset $asset)
    {
        // Validasi sama seperti create, pastikan aset bisa di-dispose
        if ($asset->disposal_date || ($asset->current_status != 'Tersedia' && $asset->current_status != 'Rusak')) {
            alert()->error('Gagal!', 'Aset tidak dapat diproses disposal saat ini.');
            return redirect()->route('assets.show', $asset->id);
        }

        $request->validate([
            'disposal_date' => 'required|date',
            'disposal_method' => 'required|string|in:Dijual,Dihapusbukukan (Rusak),Hilang,Dihibahkan',
            'disposal_reason' => 'required|string',
            'disposal_value' => 'nullable|required_if:disposal_method,Dijual|numeric|min:0',
        ]);

        $docNumber = $this->generateDocumentNumber('BAPH'); // BAPH = Berita Acara Penghapusan

        // Tentukan status baru berdasarkan metode
        $newStatus = 'Tersedia'; // Default, seharusnya tidak terjadi
        if ($request->disposal_method == 'Dihapusbukukan (Rusak)') $newStatus = 'Dihapusbukukan';
        if ($request->disposal_method == 'Hilang') $newStatus = 'Hilang';
        if ($request->disposal_method == 'Dijual') $newStatus = 'Terjual';
        if ($request->disposal_method == 'Dihibahkan') $newStatus = 'Dihibahkan';


        // Update data aset
        $asset->update([
            'disposal_doc_number' => $docNumber,
            'disposal_date' => $request->disposal_date,
            'disposal_method' => $request->disposal_method,
            'disposal_reason' => $request->disposal_reason,
            'disposal_value' => ($request->disposal_method == 'Dijual') ? $request->disposal_value : null,
            'current_status' => $newStatus,
        ]);

        // Generate PDF BAPh
        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $disposer = Auth::user(); // User yang melakukan proses
        $asset->load(['category', 'building', 'room', 'personInCharge']); // Load relasi

        $pdf = Pdf::loadView('assets.baph-pdf', compact(
            'asset',
            'headmaster',
            'disposer',
            'qrCode'
        ));

        $safeFilename = str_replace('/', '-', $docNumber);
        alert()->success('Berhasil!', 'Aset telah diproses disposal. PDF Berita Acara akan diunduh.');
        return $pdf->download($safeFilename . '.pdf');
    }

    /**
     * Download ulang BAPh.
     */
    public function downloadBaph(Asset $asset)
    {
        $docNumber = $asset->disposal_doc_number;
        if (!$docNumber) {
            alert()->error('Gagal!', 'Dokumen BAPh untuk aset ini tidak ditemukan.');
            return back();
        }

        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();
        $disposer = User::find($asset->updated_by) ?? Auth::user(); // Coba cari user yg update, jika tidak ada pakai yg login
        $asset->load(['category', 'building', 'room', 'personInCharge']);

        $pdf = Pdf::loadView('assets.baph-pdf', compact(
            'asset',
            'headmaster',
            'disposer',
            'qrCode'
        ));

        $safeFilename = str_replace('/', '-', $docNumber);
        return $pdf->download($safeFilename . '.pdf');
    }
}
