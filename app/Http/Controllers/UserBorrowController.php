<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetBorrowRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBorrowController extends Controller
{
    /**
     * Halaman utama peminjaman barang untuk user.
     * Menampilkan daftar aset tersedia & riwayat peminjaman user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab', 'katalog');

        // Kategori untuk filter
        $categories = Category::orderBy('name')->get();

        // Query aset tersedia untuk dipinjam
        $assetsQuery = Asset::with(['category', 'room', 'building'])
            ->where('current_status', 'Tersedia')
            ->where('status', 'aktif');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $assetsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code_ypt', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $assetsQuery->where('category_id', $request->input('category'));
        }

        $assets = $assetsQuery->orderBy('name')->paginate(12)->withQueryString();

        // Riwayat peminjaman user sendiri
        $myRequests = AssetBorrowRequest::with('asset:id,name,asset_code_ypt,current_status')
            ->where('requester_user_id', $user->id)
            ->where('requester_app', 'aset-app')
            ->latest()
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();

        // Hitung status peminjaman user
        $myCounts = [
            'pending'  => AssetBorrowRequest::where('requester_user_id', $user->id)->where('requester_app', 'aset-app')->pending()->count(),
            'approved' => AssetBorrowRequest::where('requester_user_id', $user->id)->where('requester_app', 'aset-app')->approved()->count(),
            'returned' => AssetBorrowRequest::where('requester_user_id', $user->id)->where('requester_app', 'aset-app')->returned()->count(),
            'rejected' => AssetBorrowRequest::where('requester_user_id', $user->id)->where('requester_app', 'aset-app')->rejected()->count(),
        ];

        return view('user.peminjaman.index', compact(
            'assets', 'categories', 'myRequests', 'myCounts', 'tab'
        ));
    }

    /**
     * Simpan permintaan peminjaman baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id'   => 'required|exists:assets,id',
            'purpose'    => 'required|string|max:500',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $asset = Asset::findOrFail($request->asset_id);

        // Pastikan aset masih tersedia
        if ($asset->current_status !== 'Tersedia') {
            return back()->with('error', 'Maaf, aset ini sudah tidak tersedia untuk dipinjam.');
        }

        // Cek apakah user sudah punya request pending untuk aset yang sama
        $existingPending = AssetBorrowRequest::where('requester_user_id', $user->id)
            ->where('requester_app', 'aset-app')
            ->where('asset_id', $asset->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->with('error', 'Anda sudah memiliki permintaan peminjaman yang masih menunggu persetujuan untuk aset ini.');
        }

        AssetBorrowRequest::create([
            'asset_id'          => $asset->id,
            'requester_user_id' => $user->id,
            'requester_name'    => $user->name,
            'requester_role'    => $user->role ?? 'user',
            'requester_app'     => 'aset-app',
            'purpose'           => $request->purpose,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'notes'             => $request->notes,
            'status'            => 'pending',
        ]);

        return redirect()->route('user.peminjaman.index', ['tab' => 'riwayat'])
            ->with('success', "Permintaan peminjaman \"{$asset->name}\" berhasil diajukan! Menunggu persetujuan admin.");
    }
}
