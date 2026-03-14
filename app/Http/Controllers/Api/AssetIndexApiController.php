<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AssetIndexApiController extends Controller
{
    /**
     * Menampilkan daftar aset aktif dengan filter & paginasi.
     * Endpoint: GET /api/assets
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 100);
        $search      = $request->input('search');
        $categoryId  = $request->input('category_id');
        $status      = $request->input('status');
        $purchaseYear = $request->input('purchase_year');

        $query = Asset::with([
                'category',
                'institution',
                'building',
                'room',
                'personInCharge',
            ])
            ->whereNull('disposal_date')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('asset_code_ypt', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($status, fn($q) => $q->where('current_status', $status))
            ->when($purchaseYear && $purchaseYear !== 'all', fn($q) => $q->where('purchase_year', $purchaseYear))
            ->latest();

        $assets = $query->paginate($perPage)->withQueryString();

        // Statistik ringkas
        $stats = [
            'total'     => Asset::whereNull('disposal_date')->count(),
            'tersedia'  => Asset::whereNull('disposal_date')->where('current_status', 'Tersedia')->count(),
            'dipinjam'  => Asset::whereNull('disposal_date')->whereIn('current_status', ['Dipinjam', 'Digunakan'])->count(),
            'rusak'     => Asset::whereNull('disposal_date')->where('current_status', 'Rusak')->count(),
        ];

        // Daftar kategori untuk filter
        $categories = Category::orderBy('name')->get(['id', 'name']);

        // Daftar tahun pengadaan
        $years = Asset::whereNull('disposal_date')
            ->whereNotNull('purchase_year')
            ->distinct()
            ->orderBy('purchase_year', 'desc')
            ->pluck('purchase_year');

        return response()->json([
            'stats'      => $stats,
            'categories' => $categories,
            'years'      => $years,
            'assets'     => $assets->through(fn($asset) => [
                'id'             => $asset->id,
                'name'           => $asset->name,
                'asset_code_ypt' => $asset->asset_code_ypt,
                'purchase_year'  => $asset->purchase_year,
                'current_status' => $asset->current_status,
                'status'         => $asset->status,
                'category'       => optional($asset->category)->name,
                'institution'    => optional($asset->institution)->name,
                'building'       => optional($asset->building)->name,
                'room'           => optional($asset->room)->name,
                'person_in_charge' => optional($asset->personInCharge)->name,
            ]),
        ]);
    }

    /**
     * Menampilkan detail satu aset berdasarkan ID.
     * Endpoint: GET /api/assets/{id}
     */
    public function show(int $id): JsonResponse
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
            'fundingSource',
        ])->find($id);

        if (!$asset) {
            return response()->json(['message' => 'Aset tidak ditemukan.'], 404);
        }

        $isDisposed = !is_null($asset->disposal_date);

        return response()->json([
            'isDisposed' => $isDisposed,
            'asset'      => [
                'id'                  => $asset->id,
                'name'               => $asset->name,
                'asset_code_ypt'     => $asset->asset_code_ypt,
                'purchase_year'      => $asset->purchase_year,
                'purchase_cost'      => $asset->purchase_cost,
                'useful_life'        => $asset->useful_life,
                'salvage_value'      => $asset->salvage_value,
                'book_value'         => $asset->book_value,
                'sequence_number'    => $asset->sequence_number,
                'current_status'     => $asset->current_status,
                'status'             => $asset->status,
                'disposal_date'      => $asset->disposal_date,
                'disposal_method'    => $asset->disposal_method,
                'disposal_reason'    => $asset->disposal_reason,
                'disposal_value'     => $asset->disposal_value,
                'disposal_doc_number' => $asset->disposal_doc_number,
                'institution'        => optional($asset->institution)->name,
                'category'           => optional($asset->category)->name,
                'building'           => optional($asset->building)->name,
                'room'               => optional($asset->room)->name,
                'faculty'            => optional($asset->faculty)->name,
                'department'         => optional($asset->department)->name,
                'person_in_charge'   => optional($asset->personInCharge)->name,
                'asset_function'     => optional($asset->assetFunction)->name,
                'funding_source'     => optional($asset->fundingSource)->name,
                'created_at'         => $asset->created_at?->toDateTimeString(),
                'updated_at'         => $asset->updated_at?->toDateTimeString(),
            ],
        ]);
    }
}
