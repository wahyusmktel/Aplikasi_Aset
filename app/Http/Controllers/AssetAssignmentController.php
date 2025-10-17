<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'condition_on_assign' => 'required|string|max:255',
        ]);

        // Cek apakah aset sudah dipinjam
        if ($asset->current_status !== 'Tersedia') {
            alert()->error('Gagal!', 'Aset ini sedang tidak tersedia atau sudah dipinjam.');
            return back();
        }

        // Catat penugasan
        AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'condition_on_assign' => $request->condition_on_assign,
        ]);

        // Update status aset
        $asset->update(['current_status' => 'Dipinjam']);

        alert()->success('Berhasil!', 'Aset telah diserahkan kepada pegawai.');
        return redirect()->route('assets.show', $asset->id);
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

        // Update catatan penugasan dengan data pengembalian
        $assignment->update([
            'returned_date' => $request->returned_date,
            'condition_on_return' => $request->condition_on_return,
            'notes' => $request->notes,
        ]);

        // Update status aset kembali menjadi "Tersedia"
        $asset = $assignment->asset;
        $asset->update(['current_status' => 'Tersedia']);

        alert()->success('Berhasil!', 'Aset telah dikembalikan.');
        return redirect()->route('assets.show', $asset->id);
    }
}
