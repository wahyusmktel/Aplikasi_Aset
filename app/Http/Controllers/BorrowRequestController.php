<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowRequestController extends Controller
{
    /**
     * Daftar semua permintaan peminjaman (admin view).
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'pending');

        $query = AssetBorrowRequest::with('asset:id,name,asset_code_ypt,current_status')
            ->where('status', $tab)
            ->latest();

        $requests = $query->paginate(20)->withQueryString();

        // Hitung tiap status untuk badge
        $counts = [
            'pending'  => AssetBorrowRequest::pending()->count(),
            'approved' => AssetBorrowRequest::approved()->count(),
            'rejected' => AssetBorrowRequest::rejected()->count(),
            'returned' => AssetBorrowRequest::returned()->count(),
        ];

        return view('borrow-requests.index', compact('requests', 'tab', 'counts'));
    }

    /**
     * Setujui permintaan peminjaman.
     */
    public function approve(Request $request, AssetBorrowRequest $borrowRequest)
    {
        if (!$borrowRequest->isPending()) {
            return back()->with('error', 'Permintaan ini tidak dalam status pending.');
        }

        $asset = $borrowRequest->asset;

        if ($asset->current_status !== 'Tersedia') {
            return back()->with('error', 'Aset sudah tidak tersedia. Tidak dapat menyetujui permintaan.');
        }

        $borrowRequest->update([
            'status'      => 'approved',
            'approved_by' => Auth::user()->name,
            'approved_at' => now(),
        ]);

        // Update status aset
        $asset->update(['current_status' => 'Dipinjam']);

        return back()->with('success', "Permintaan peminjaman dari {$borrowRequest->requester_name} telah disetujui.");
    }

    /**
     * Tolak permintaan peminjaman.
     */
    public function reject(Request $request, AssetBorrowRequest $borrowRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if (!$borrowRequest->isPending()) {
            return back()->with('error', 'Permintaan ini tidak dalam status pending.');
        }

        $borrowRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'approved_by'      => Auth::user()->name,
            'approved_at'      => now(),
        ]);

        return back()->with('success', "Permintaan dari {$borrowRequest->requester_name} telah ditolak.");
    }

    /**
     * Tandai aset sudah dikembalikan.
     */
    public function markReturned(Request $request, AssetBorrowRequest $borrowRequest)
    {
        $request->validate([
            'return_notes' => 'nullable|string|max:500',
        ]);

        if (!$borrowRequest->isApproved()) {
            return back()->with('error', 'Hanya permintaan yang sudah disetujui yang dapat ditandai dikembalikan.');
        }

        $borrowRequest->update([
            'status'       => 'returned',
            'returned_at'  => now(),
            'return_notes' => $request->input('return_notes'),
        ]);

        // Kembalikan status aset ke Tersedia
        $borrowRequest->asset()->update(['current_status' => 'Tersedia']);

        return back()->with('success', "Aset telah berhasil ditandai dikembalikan.");
    }
}
