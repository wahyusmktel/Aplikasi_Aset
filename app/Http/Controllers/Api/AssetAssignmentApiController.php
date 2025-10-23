<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AssetAssignmentApiController extends Controller
{
    // ===== Helpers: nomor dokumen & roman =====
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
    private function generateDocumentNumber(string $type)
    {
        $year = date('Y');
        $romanMonth = $this->toRoman((int)date('m'));
        $latest = AssetAssignment::whereYear('created_at', $year)->count() + 1;
        $seq = sprintf('%04d', $latest);
        return "{$type}/SMKTL/{$romanMonth}/{$year}/{$seq}";
    }

    // ===== Detail aset by code (AUTH) =====
    public function showByCode(string $asset_code_ypt)
    {
        $asset = Asset::with([
            'institution',
            'category',
            'building',
            'room',
            'faculty',
            'department',
            'personInCharge',
            'assetFunction',
            'fundingSource'
        ])->where('asset_code_ypt', $asset_code_ypt)->first();

        if (!$asset) return response()->json(['message' => 'Asset not found'], 404);

        return response()->json([
            'asset' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'asset_code_ypt' => $asset->asset_code_ypt,
                'purchase_year' => $asset->purchase_year,
                'sequence_number' => $asset->sequence_number,
                'status' => $asset->status,
                'current_status' => $asset->current_status, // ADDED (Tersedia/Dipinjam/…)
                'institution' => optional($asset->institution)->name,
                'building' => optional($asset->building)->name,
                'room' => optional($asset->room)->name,
                'category' => optional($asset->category)->name,
                'faculty' => optional($asset->faculty)->name,
                'department' => optional($asset->department)->name,
                'person_in_charge' => optional($asset->personInCharge)->name,
                'asset_function' => optional($asset->assetFunction)->name,
                'funding_source' => optional($asset->fundingSource)->name,
                'disposal_date' => $asset->disposal_date,
                'disposal_method' => $asset->disposal_method,
                'disposal_reason' => $asset->disposal_reason,
                'disposal_doc_number' => $asset->disposal_doc_number,
            ],
            'isDisposed' => !is_null($asset->disposal_date),
        ]);
    }

    // ===== Assign (checkout) =====
    public function assign(Request $request, Asset $asset)
    {
        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee) return response()->json(['message' => 'Akun tidak terkait data pegawai.'], 403);

        // Validasi status aset
        if ($asset->current_status !== 'Tersedia') {
            return response()->json(['message' => 'Aset sedang tidak tersedia.'], 422);
        }

        $validated = $request->validate([
            'condition_on_assign' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $docNumber = $this->generateDocumentNumber('BAST'); // BAST serah terima
        $assignment = AssetAssignment::create([
            'checkout_doc_number' => $docNumber,
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,        // <- ambil dari user login
            'assigned_date' => now(),              // <- set otomatis
            'condition_on_assign' => $validated['condition_on_assign'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $asset->update(['current_status' => 'Dipinjam']);

        return response()->json([
            'message' => 'Aset berhasil dipinjam.',
            'assignment' => $assignment->load('asset:id,name'),
            'doc_number' => $docNumber,
            'bast_url' => route('api.assignments.downloadBast', ['assignment' => $assignment->id, 'type' => 'checkout']),
        ], 201);
    }

    // ===== Return (pengembalian) =====
    public function return(Request $request, AssetAssignment $assignment)
    {
        $user = Auth::user();
        if ($assignment->employee_id !== optional($user->employee)->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($assignment->returned_date) {
            return response()->json(['message' => 'Aset sudah dikembalikan.'], 422);
        }

        $validated = $request->validate([
            'condition_on_return' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $docNumber = $this->generateDocumentNumber('BAP'); // BAP pengembalian

        $assignment->update([
            'return_doc_number' => $docNumber,
            'returned_date' => now(),
            'condition_on_return' => $validated['condition_on_return'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $assignment->asset()->update(['current_status' => 'Tersedia']);

        return response()->json([
            'message' => 'Aset berhasil dikembalikan.',
            'assignment' => $assignment->load('asset:id,name'),
            'doc_number' => $docNumber,
            'bast_url' => route('api.assignments.downloadBast', ['assignment' => $assignment->id, 'type' => 'return']),
        ]);
    }

    // ===== PDF BAST download =====
    private function generateBastPdf(AssetAssignment $assignment, string $type)
    {
        $docNumber = $type === 'return' ? $assignment->return_doc_number : $assignment->checkout_doc_number;
        $title = $type === 'return' ? 'Berita Acara Pengembalian Aset' : 'Berita Acara Serah Terima Aset';

        $verificationUrl = route('public.verify', $docNumber);
        $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode = (new QRCode($options))->render($verificationUrl);

        $asset = $assignment->asset()->with('personInCharge')->first();
        $employee = $assignment->employee;
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();

        // REUSE view PDF yang sudah ada di web (sesuaikan nama blade kamu)
        return Pdf::loadView('assets.bast-pdf', compact(
            'assignment',
            'asset',
            'employee',
            'headmaster',
            'title',
            'qrCode',
            'type'
        ));
    }

    public function downloadBast(AssetAssignment $assignment, string $type)
    {
        $docNumber = $type === 'return' ? $assignment->return_doc_number : $assignment->checkout_doc_number;
        if (!$docNumber) return response()->json(['message' => 'Dokumen belum tersedia.'], 404);

        $pdf = $this->generateBastPdf($assignment, $type);
        $filename = str_replace('/', '-', $docNumber) . '.pdf';
        return $pdf->download($filename);
    }
}
