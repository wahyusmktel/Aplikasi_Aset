<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use Illuminate\Http\Request;

class AssignedAssetController extends Controller
{
    /**
     * Menampilkan daftar aset yang sedang ditugaskan (belum dikembalikan).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assignedAssets = AssetAssignment::whereNull('returned_date') // <-- Kuncinya di sini
            ->with(['asset', 'employee']) // Ambil data aset dan pegawai
            ->when($search, function ($query, $search) {
                // Pencarian berdasarkan nama aset atau nama pegawai
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest('assigned_date') // Urutkan berdasarkan tanggal penugasan terbaru
            ->paginate(15)
            ->withQueryString();

        return view('assigned-assets.index', compact('assignedAssets'));
    }
}
