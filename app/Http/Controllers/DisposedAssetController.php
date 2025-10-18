<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\DisposedAssetsExport; // Akan kita buat
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DisposedAssetController extends Controller
{
    public function index(Request $request)
    {
        // Base query for disposed assets
        $baseQuery = Asset::whereNotNull('disposal_date');

        // 1. Data Widget
        $totalDisposed = $baseQuery->count();
        $disposedByMethod = (clone $baseQuery)
            ->select('disposal_method', DB::raw('count(*) as total'))
            ->groupBy('disposal_method')
            ->pluck('total', 'disposal_method');

        // 2. Data Chart (Disposals per Bulan dalam 12 bulan terakhir)
        $disposalsPerMonth = (clone $baseQuery)
            ->select(
                DB::raw("DATE_FORMAT(disposal_date, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('disposal_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month');

        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels[] = $month->isoFormat('MMM YYYY');
            $chartData[] = $disposalsPerMonth->get($monthKey, 0);
        }

        // 3. Data Tabel
        $search = $request->input('search');
        $disposedAssets = (clone $baseQuery)
            ->with(['category', 'institution']) // Load relasi yang relevan
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code_ypt', 'like', "%{$search}%")
                    ->orWhere('disposal_method', 'like', "%{$search}%")
                    ->orWhere('disposal_reason', 'like', "%{$search}%");
            })
            ->latest('disposal_date') // Urutkan berdasarkan tanggal disposal terbaru
            ->paginate(15)
            ->withQueryString();

        return view('disposed-assets.index', compact(
            'totalDisposed',
            'disposedByMethod',
            'chartLabels',
            'chartData',
            'disposedAssets'
        ));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new DisposedAssetsExport($search), 'riwayat-aset-dihapus.xlsx');
    }

    public function downloadPDF(Request $request)
    {
        $search = $request->input('search');
        $disposedAssets = Asset::whereNotNull('disposal_date')
            ->with(['category', 'institution', 'personInCharge']) // Load relasi
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code_ypt', 'like', "%{$search}%")
                    ->orWhere('disposal_method', 'like', "%{$search}%")
                    ->orWhere('disposal_reason', 'like', "%{$search}%");
            })
            ->latest('disposal_date')
            ->get();

        if ($disposedAssets->isEmpty()) {
            alert()->info('Info', 'Tidak ada data aset dihapus untuk dilaporkan.');
            return redirect()->route('disposedAssets.index');
        }

        $pj = Employee::where('position', 'Kaur Sarpras')->first();
        $ks = Employee::where('position', 'Kepala Sekolah')->first();
        $kota = "Bandar Lampung"; // Ganti jika perlu

        $pdf = Pdf::loadView('disposed-assets.report-pdf', compact('disposedAssets', 'pj', 'ks', 'kota'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-rekap-aset-dihapus.pdf');
    }
}
