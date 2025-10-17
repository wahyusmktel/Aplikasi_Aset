<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BooksExport; // We will create this next
use Barryvdh\DomPDF\Facade\Pdf; // Use the PDF facade

class BookAssetController extends Controller
{
    /**
     * Display the book asset management dashboard.
     */
    public function index(Request $request)
    {
        // Find the 'Buku' category ID. Handle case where it might not exist.
        $bookCategory = Category::where('name', 'Buku')->first();
        $bookCategoryId = $bookCategory ? $bookCategory->id : -1; // Use -1 if not found

        // Base query for all book assets
        $baseQuery = Asset::where('category_id', $bookCategoryId);

        // 1. WIDGET: Total Books
        $totalBooks = $baseQuery->count();

        // 2. WIDGET: Books by acquisition year
        $booksByYear = (clone $baseQuery)
            ->select('purchase_year', DB::raw('count(*) as total'))
            ->groupBy('purchase_year')
            ->orderBy('purchase_year', 'desc')
            ->get();

        // 3. CHART: Line chart data
        $chartData = (clone $baseQuery)
            ->select('purchase_year', DB::raw('count(*) as total'))
            ->groupBy('purchase_year')
            ->orderBy('purchase_year', 'asc')
            ->pluck('total', 'purchase_year');

        $chartLabels = $chartData->keys();
        $chartValues = $chartData->values();

        // 4. TABLE: Paginated list of books with search
        $search = $request->input('search');
        $books = (clone $baseQuery)
            ->with(['institution', 'building', 'room'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code_ypt', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('assets.books.index', compact(
            'totalBooks',
            'booksByYear',
            'chartLabels',
            'chartValues',
            'books'
        ));
    }

    /**
     * Handle the export of book assets to an Excel file.
     */
    public function exportExcel(Request $request)
    {
        $ids = $request->input('ids') ? explode(',', $request->input('ids')) : null;
        return Excel::download(new BooksExport($ids), 'laporan-aset-buku.xlsx');
    }

    /**
     * Handle the download of a book asset report as a PDF file.
     */
    public function downloadPDF(Request $request)
    {
        $bookCategory = Category::where('name', 'Buku')->first();
        $query = Asset::where('category_id', $bookCategory ? $bookCategory->id : -1)
            ->with(['institution', 'category', 'building', 'room', 'faculty', 'department', 'personInCharge', 'assetFunction', 'fundingSource']);

        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }

        $books = $query->get();
        $title = $request->has('ids') ? 'Laporan Aset Buku Terpilih' : 'Laporan Keseluruhan Aset Buku';

        if ($books->isEmpty()) {
            alert()->info('Info', 'Tidak ada data buku untuk dilaporkan.');
            return redirect()->route('books.index');
        }

        $pdf = Pdf::loadView('assets.books.report-pdf', [
            'books' => $books,
            'title' => $title
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-aset-buku.pdf');
    }
}
