<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class PublicAssetController extends Controller
{
    /**
     * Menampilkan halaman detail aset publik.
     */
    public function show(string $asset_code_ypt)
    {
        // Cari aset berdasarkan kode unik, jika tidak ada maka tampilkan halaman 404
        $asset = Asset::where('asset_code_ypt', $asset_code_ypt)->firstOrFail();

        // Load semua relasi yang dibutuhkan
        $asset->load('institution', 'category', 'building', 'room', 'faculty', 'department', 'personInCharge', 'assetFunction', 'fundingSource');
        
        // Kembalikan view publik baru
        return view('public.asset-detail', compact('asset'));
    }
}
