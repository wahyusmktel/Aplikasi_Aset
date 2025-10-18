<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AssetAssignmentsExport; // Akan kita buat
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AssetAssignmentHistoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data Widget
        $totalAssignments = AssetAssignment::count();
        $activeAssignments = AssetAssignment::whereNull('returned_date')->count();

        // 2. Data Chart (Peminjaman per Bulan dalam 12 bulan terakhir)
        $assignmentsPerMonth = AssetAssignment::select(
            DB::raw("DATE_FORMAT(assigned_date, '%Y-%m') as month"),
            DB::raw('count(*) as total')
        )
            ->where('assigned_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month');

        // Siapkan label untuk 12 bulan terakhir
        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels[] = $month->isoFormat('MMM YYYY'); // Format nama bulan
            $chartData[] = $assignmentsPerMonth->get($monthKey, 0); // Ambil data, default 0 jika tidak ada
        }

        // 3. Data Tabel
        $search = $request->input('search');
        $assignments = AssetAssignment::with(['asset', 'employee'])
            ->when($search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('checkout_doc_number', 'like', "%{$search}%")
                    ->orWhere('return_doc_number', 'like', "%{$search}%");
            })
            ->latest('assigned_date') // Urutkan berdasarkan tanggal pinjam terbaru
            ->paginate(15)
            ->withQueryString();

        return view('inventory-history.index', compact(
            'totalAssignments',
            'activeAssignments',
            'chartLabels',
            'chartData',
            'assignments'
        ));
    }

    public function exportExcel(Request $request)
    {
        // Logika pencarian sama dengan index, tapi tanpa paginasi
        $search = $request->input('search');
        return Excel::download(new AssetAssignmentsExport($search), 'riwayat-inventaris.xlsx');
    }

    public function downloadPDF(Request $request)
    {
        $search = $request->input('search');
        $assignments = AssetAssignment::with(['asset', 'employee'])
            ->when($search, function ($query, $search) {
                // Logika pencarian sama
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('checkout_doc_number', 'like', "%{$search}%")
                    ->orWhere('return_doc_number', 'like', "%{$search}%");
            })
            ->latest('assigned_date')
            ->get();

        if ($assignments->isEmpty()) {
            alert()->info('Info', 'Tidak ada data riwayat untuk dilaporkan.');
            return redirect()->route('inventory.history');
        }

        // Ambil data penanggung jawab dan kepala sekolah
        // Asumsi: Penanggung Jawab default adalah Kaur Sarpras, Kepala Sekolah adalah yang jabatannya 'Kepala Sekolah'
        $pj = Employee::where('position', 'Kaur Sarpras')->first();
        $ks = Employee::where('position', 'Kepala Sekolah')->first();
        $kota = "Bandar Lampung"; // Ganti sesuai lokasi

        $pdf = Pdf::loadView('inventory-history.report-pdf', compact('assignments', 'pj', 'ks', 'kota'))
            ->setPaper('a4', 'landscape'); // Atur ke landscape

        return $pdf->download('laporan-rekap-inventaris.pdf');
    }
}
