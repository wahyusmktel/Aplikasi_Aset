<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data statistik aset.
     */
    public function index()
    {
        // 1. Core Statistics
        $totalAssets = Asset::count();
        $totalEmployees = \App\Models\Employee::count();
        $totalCategories = Category::count();
        $totalRooms = \App\Models\Room::count();

        // 2. Data untuk diagram Aset per Kategori (Pie Chart)
        $assetsPerCategory = Asset::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $categoryLabels = $assetsPerCategory->pluck('category.name');
        $categoryData = $assetsPerCategory->pluck('total');

        // 3. Data untuk diagram Aset per Tahun Pembelian (Bar Chart)
        $assetsPerYear = Asset::select('purchase_year', DB::raw('count(*) as total'))
            ->groupBy('purchase_year')
            ->orderBy('purchase_year', 'asc')
            ->get();

        $yearLabels = $assetsPerYear->pluck('purchase_year');
        $yearData = $assetsPerYear->pluck('total');

        // 4. Recent Assets
        $recentAssets = Asset::latest()->take(5)->with(['category', 'room'])->get();

        // 5. Total Maintenance Schedules (Pending/Upcoming)
        $pendingMaintenance = \App\Models\MaintenanceSchedule::where('status', 'scheduled')
            ->orWhere('status', 'in_progress')
            ->count();

        return view('dashboard', compact(
            'totalAssets',
            'totalEmployees',
            'totalCategories',
            'totalRooms',
            'categoryLabels',
            'categoryData',
            'yearLabels',
            'yearData',
            'recentAssets',
            'pendingMaintenance'
        ));
    }
}
