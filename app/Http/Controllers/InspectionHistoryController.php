<?php

namespace App\Http\Controllers;

use App\Models\AssetInspection;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\InspectionHistoryExport; // Akan kita buat
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InspectionHistoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data Widget
        $totalInspections = AssetInspection::count();
        $conditionsCount = AssetInspection::select('condition', DB::raw('count(*) as total'))
            ->groupBy('condition')
            ->pluck('total', 'condition');

        // 2. Data Chart (Inspeksi per Bulan dalam 12 bulan terakhir)
        $inspectionsPerMonth = AssetInspection::select(
            DB::raw("DATE_FORMAT(inspection_date, '%Y-%m') as month"),
            DB::raw('count(*) as total')
        )
            ->where('inspection_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month');

        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels[] = $month->isoFormat('MMM YYYY');
            $chartData[] = $inspectionsPerMonth->get($monthKey, 0);
        }

        // 3. Data Tabel
        $search = $request->input('search');
        $inspections = AssetInspection::with(['asset', 'inspector']) // Eager load asset dan inspector
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('condition', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('inspector', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest('inspection_date') // Urutkan berdasarkan tanggal terbaru
            ->paginate(15)
            ->withQueryString();

        return view('inspection-history.index', compact(
            'totalInspections',
            'conditionsCount',
            'chartLabels',
            'chartData',
            'inspections'
        ));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new InspectionHistoryExport($search), 'riwayat-inspeksi.xlsx');
    }

    public function downloadPDF(Request $request)
    {
        $search = $request->input('search');
        $inspections = AssetInspection::with(['asset', 'inspector', 'asset.institution'])
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('condition', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('inspector', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest('inspection_date')
            ->get();

        if ($inspections->isEmpty()) {
            alert()->info('Info', 'Tidak ada data riwayat inspeksi untuk dilaporkan.');
            return redirect()->route('inspection.history');
        }

        $pj = Employee::where('position', 'Kaur Sarpras')->first();
        $ks = Employee::where('position', 'Kepala Sekolah')->first();
        $kota = "Bandar Lampung"; // Ganti jika perlu

        $pdf = Pdf::loadView('inspection-history.report-pdf', compact('inspections', 'pj', 'ks', 'kota'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-rekap-inspeksi.pdf');
    }
}
