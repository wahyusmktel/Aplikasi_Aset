<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BorrowRequestApiController extends Controller
{
    /**
     * POST /api/borrow-requests
     * Buat permintaan peminjaman baru (dari Aplikasi-Izin).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_id'          => 'required|exists:assets,id',
            'requester_user_id' => 'required|string',
            'requester_name'    => 'required|string|max:255',
            'requester_role'    => 'nullable|string|max:100',
            'requester_app'     => 'nullable|string|max:100',
            'purpose'           => 'required|string|max:1000',
            'start_date'        => 'required|date|after_or_equal:today',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $asset = Asset::find($validated['asset_id']);

        // Cek apakah aset masih tersedia
        if ($asset->current_status !== 'Tersedia') {
            return response()->json([
                'message' => 'Aset sedang tidak tersedia untuk dipinjam.',
                'current_status' => $asset->current_status,
            ], 422);
        }

        // Cek apakah user ini sudah punya permintaan pending/approved untuk aset yang sama
        $existing = AssetBorrowRequest::where('asset_id', $validated['asset_id'])
            ->where('requester_user_id', $validated['requester_user_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah memiliki permintaan peminjaman aktif untuk aset ini.',
            ], 422);
        }

        $borrowRequest = AssetBorrowRequest::create([
            'asset_id'          => $validated['asset_id'],
            'requester_user_id' => $validated['requester_user_id'],
            'requester_name'    => $validated['requester_name'],
            'requester_role'    => $validated['requester_role'] ?? null,
            'requester_app'     => $validated['requester_app'] ?? 'aplikasi-izin',
            'purpose'           => $validated['purpose'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'] ?? null,
            'notes'             => $validated['notes'] ?? null,
            'status'            => 'pending',
        ]);

        return response()->json([
            'message'      => 'Permintaan peminjaman berhasil diajukan.',
            'borrow_request' => [
                'id'         => $borrowRequest->id,
                'asset_id'   => $borrowRequest->asset_id,
                'status'     => $borrowRequest->status,
                'start_date' => $borrowRequest->start_date?->toDateString(),
                'end_date'   => $borrowRequest->end_date?->toDateString(),
                'created_at' => $borrowRequest->created_at->toDateTimeString(),
            ],
        ], 201);
    }

    /**
     * GET /api/borrow-requests/{id}
     * Cek status satu permintaan.
     */
    public function show(int $id): JsonResponse
    {
        $req = AssetBorrowRequest::with('asset:id,name,asset_code_ypt,current_status')->find($id);

        if (!$req) {
            return response()->json(['message' => 'Permintaan tidak ditemukan.'], 404);
        }

        return response()->json([
            'borrow_request' => [
                'id'               => $req->id,
                'status'           => $req->status,
                'status_label'     => $req->status_label,
                'purpose'          => $req->purpose,
                'start_date'       => $req->start_date?->toDateString(),
                'end_date'         => $req->end_date?->toDateString(),
                'approved_by'      => $req->approved_by,
                'approved_at'      => $req->approved_at?->toDateTimeString(),
                'rejection_reason' => $req->rejection_reason,
                'returned_at'      => $req->returned_at?->toDateTimeString(),
                'return_notes'     => $req->return_notes,
                'created_at'       => $req->created_at->toDateTimeString(),
                'asset' => [
                    'id'             => $req->asset?->id,
                    'name'           => $req->asset?->name,
                    'asset_code_ypt' => $req->asset?->asset_code_ypt,
                    'current_status' => $req->asset?->current_status,
                ],
            ],
        ]);
    }

    /**
     * GET /api/borrow-requests?requester_user_id=xxx&app=aplikasi-izin
     * Riwayat permintaan peminjaman milik user tertentu.
     */
    public function index(Request $request): JsonResponse
    {
        $userId    = $request->input('requester_user_id');
        $app       = $request->input('app', 'aplikasi-izin');
        $status    = $request->input('status');
        $perPage   = min((int) $request->input('per_page', 10), 50);

        if (!$userId) {
            return response()->json(['message' => 'requester_user_id wajib diisi.'], 422);
        }

        $query = AssetBorrowRequest::with('asset:id,name,asset_code_ypt,current_status')
            ->where('requester_user_id', $userId)
            ->where('requester_app', $app)
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest();

        $result = $query->paginate($perPage)->withQueryString();

        $result->getCollection()->transform(fn($r) => [
            'id'               => $r->id,
            'status'           => $r->status,
            'status_label'     => $r->status_label,
            'purpose'          => $r->purpose,
            'start_date'       => $r->start_date?->toDateString(),
            'end_date'         => $r->end_date?->toDateString(),
            'approved_by'      => $r->approved_by,
            'approved_at'      => $r->approved_at?->toDateTimeString(),
            'rejection_reason' => $r->rejection_reason,
            'returned_at'      => $r->returned_at?->toDateTimeString(),
            'created_at'       => $r->created_at->toDateTimeString(),
            'asset' => [
                'id'             => $r->asset?->id,
                'name'           => $r->asset?->name,
                'asset_code_ypt' => $r->asset?->asset_code_ypt,
                'current_status' => $r->asset?->current_status,
            ],
        ]);

        return response()->json($result);
    }
}
