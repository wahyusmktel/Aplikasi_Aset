<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDamageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AssetReportController extends Controller
{
    public function index()
    {
        $reports = AssetDamageReport::with('asset')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('asset-reports.index', compact('reports'));
    }

    public function scan()
    {
        return view('asset-reports.scan');
    }

    public function create(Request $request)
    {
        $asset = null;
        if ($request->has('asset_code')) {
            $asset = Asset::where('asset_code_ypt', $request->asset_code)->first();
        }

        $assets = Asset::select('id', 'name', 'asset_code_ypt')->get();

        return view('asset-reports.create', compact('asset', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'description' => 'required|string|min:10',
            'reported_condition' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('damage-reports', 'public');
        }

        AssetDamageReport::create([
            'asset_id' => $request->asset_id,
            'user_id' => Auth::id(),
            'report_doc_number' => 'REP-' . strtoupper(Str::random(8)),
            'description' => $request->description,
            'reported_condition' => $request->reported_condition,
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()->route('asset-reports.index')
            ->with('success', 'Laporan kerusakan berhasil dikirim. Terima kasih atas laporannya.');
    }

    public function adminIndex()
    {
        $reports = AssetDamageReport::with(['asset', 'user'])->latest()->paginate(15);
        return view('asset-reports.admin-index', compact('reports'));
    }

    public function updateStatus(Request $request, AssetDamageReport $report)
    {
        $request->validate([
            'status' => 'required|in:pending,verifying,processed,fixed,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }
}
