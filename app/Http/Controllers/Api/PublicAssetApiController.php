<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;

class PublicAssetApiController extends Controller
{
    public function show(string $asset_code_ypt)
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
        ])->where('asset_code_ypt', $asset_code_ypt)->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        $isDisposed = !is_null($asset->disposal_date);

        // Susun payload ringkas & konsisten
        return response()->json([
            'isDisposed' => $isDisposed,
            'asset' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'asset_code_ypt' => $asset->asset_code_ypt,
                'purchase_year' => $asset->purchase_year,
                'sequence_number' => $asset->sequence_number,
                'status' => $asset->status,
                'disposal_date' => $asset->disposal_date,
                'disposal_method' => $asset->disposal_method,
                'disposal_reason' => $asset->disposal_reason,
                'disposal_doc_number' => $asset->disposal_doc_number,
                'institution' => optional($asset->institution)->name,
                'building' => optional($asset->building)->name,
                'room' => optional($asset->room)->name,
                'faculty' => optional($asset->faculty)->name,
                'department' => optional($asset->department)->name,
                'person_in_charge' => optional($asset->personInCharge)->name,
                'asset_function' => optional($asset->assetFunction)->name,
                'funding_source' => optional($asset->fundingSource)->name,
            ],
        ]);
    }
}
