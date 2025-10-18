<?php

namespace App\Http\Controllers;

use App\Models\AssetMaintenance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MaintenanceHistoryExport; // Akan kita buat
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MaintenanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data Widget
        $totalRecords = AssetMaintenance::count();
        $totalCost = AssetMaintenance::sum('cost'); // Sum costs

        // 2. Data Chart (Maintenance per Bulan dalam 12 bulan terakhir)
        $recordsPerMonth = AssetMaintenance::select(
            DB::raw("DATE_FORMAT(maintenance_date, '%Y-%m') as month"),
            DB::raw('count(*) as total')
        )
            ->where('maintenance_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month');

        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels[] = $month->isoFormat('MMM YYYY');
            $chartData[] = $recordsPerMonth->get($monthKey, 0);
        }

        // 3. Data Tabel
        $search = $request->input('search');
        $maintenances = AssetMaintenance::with(['asset']) // Eager load asset
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('technician', 'like', "%{$search}%");
            })
            ->latest('maintenance_date') // Urutkan berdasarkan tanggal terbaru
            ->paginate(15)
            ->withQueryString();

        return view('maintenance-history.index', compact(
            'totalRecords',
            'totalCost',
            'chartLabels',
            'chartData',
            'maintenances'
        ));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new MaintenanceHistoryExport($search), 'riwayat-maintenance.xlsx');
    }

    public function downloadPDF(Request $request)
    {
        $search = $request->input('search');
        $maintenances = AssetMaintenance::with(['asset'])
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('technician', 'like', "%{$search}%");
            })
            ->latest('maintenance_date')
            ->get();

        if ($maintenances->isEmpty()) {
            alert()->info('Info', 'Tidak ada data riwayat maintenance untuk dilaporkan.');
            return redirect()->route('maintenance.history');
        }

        $pj = Employee::where('position', 'Kaur Sarpras')->first(); // Asumsi PJ
        $ks = Employee::where('position', 'Kepala Sekolah')->first(); // Asumsi KS
        $kota = "Bandar Lampung"; // Ganti jika perlu

        $pdf = Pdf::loadView('maintenance-history.report-pdf', compact('maintenances', 'pj', 'ks', 'kota'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-rekap-maintenance.pdf');
    }
}
