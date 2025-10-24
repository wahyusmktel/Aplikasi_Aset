<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Building;
use App\Models\Category;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FundingSource;
use App\Models\Institution;
use App\Models\PersonInCharge;
use App\Models\Room;
use App\Models\AssetFunction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\AssetsBatchImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActiveAssetsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssetController extends Controller
{
    /**
     * Menampilkan daftar semua aset.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $assets = Asset::with([
            'category',
            'institution',
            'building',
            'room'
        ])
            ->whereNull('disposal_date')
            ->when($categoryId && $categoryId !== 'all', function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($search, function ($query, $search) {
                // Pencarian berdasarkan nama aset atau kode YPT
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code_ypt', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('assets.index', compact('assets', 'categories'));
    }

    /**
     * Menampilkan form untuk membuat aset baru.
     */
    public function create()
    {
        // Ambil semua data master untuk dropdown
        $institutions = Institution::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $personsInCharge = PersonInCharge::orderBy('name')->get();
        $assetFunctions = AssetFunction::orderBy('name')->get();
        $fundingSources = FundingSource::orderBy('name')->get();

        return view('assets.create', compact(
            'institutions',
            'categories',
            'buildings',
            'rooms',
            'faculties',
            'departments',
            'personsInCharge',
            'assetFunctions',
            'fundingSources'
        ));
    }

    /**
     * Menyimpan aset baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_year' => 'required|digits:4|integer|min:1900',
            // 'sequence_number' => 'required|digits:4',
            'institution_id' => 'required|exists:institutions,id',
            'category_id' => 'required|exists:categories,id',
            'building_id' => 'required|exists:buildings,id',
            'room_id' => 'required|exists:rooms,id',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'person_in_charge_id' => 'required|exists:persons_in_charge,id',
            'asset_function_id' => 'required|exists:asset_functions,id',
            'funding_source_id' => 'required|exists:funding_sources,id',
            'purchase_cost' => 'required|numeric|min:0',
            'useful_life' => 'required|integer|min:1',
            'salvage_value' => 'required|numeric|min:0|lte:purchase_cost', // Nilai sisa <= Harga beli
        ]);

        $latestAsset = Asset::orderBy('id', 'desc')->first();
        $newSequenceNumber = $latestAsset ? intval($latestAsset->sequence_number) + 1 : 1;
        $formattedSequence = sprintf('%04d', $newSequenceNumber);

        $asset = Asset::create(array_merge($request->all(), [
            'sequence_number' => $formattedSequence // Tambahkan sequence number otomatis
        ]));

        // Logika Generate Kode Aset YPT (akan kita sempurnakan nanti)
        // Contoh: TL.25.101.G01.1101.SP.U01.P01.IK.1.0001
        $institution = Institution::find($request->institution_id);
        $year = substr($request->purchase_year, -2);
        $category = Category::find($request->category_id);
        $building = Building::find($request->building_id);
        $room = Room::find($request->room_id);
        $faculty = Faculty::find($request->faculty_id);
        $department = Department::find($request->department_id);
        $personInCharge = PersonInCharge::find($request->person_in_charge_id);
        $assetFunction = AssetFunction::find($request->asset_function_id);
        $fundingSource = FundingSource::find($request->funding_source_id);

        $asset_code_ypt = implode('.', [
            $institution->code,
            $year,
            $category->code,
            $building->code,
            $room->code,
            $faculty->code,
            $department->code,
            $personInCharge->code,
            $assetFunction->code,
            $fundingSource->code,
            $formattedSequence
        ]);

        $asset->update(['asset_code_ypt' => $asset_code_ypt, 'status' => 'Aktif']);


        alert()->success('Berhasil!', 'Data aset berhasil ditambahkan.');
        return redirect()->route('assets.index');
    }

    /**
     * Menampilkan form untuk mengedit aset.
     */
    public function edit(Asset $asset)
    {
        // Ambil semua data master untuk dropdown
        $institutions = Institution::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $personsInCharge = PersonInCharge::orderBy('name')->get();
        $assetFunctions = AssetFunction::orderBy('name')->get();
        $fundingSources = FundingSource::orderBy('name')->get();

        return view('assets.edit', compact(
            'asset',
            'institutions',
            'categories',
            'buildings',
            'rooms',
            'faculties',
            'departments',
            'personsInCharge',
            'assetFunctions',
            'fundingSources'
        ));
    }

    /**
     * Memperbarui data aset di database.
     */
    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_year' => 'required|digits:4|integer|min:1900',
            // Pastikan sequence_number unik untuk kombinasi tertentu jika perlu
            // 'sequence_number' => ['required', 'digits:4'],
            'institution_id' => 'required|exists:institutions,id',
            'category_id' => 'required|exists:categories,id',
            'building_id' => 'required|exists:buildings,id',
            'room_id' => 'required|exists:rooms,id',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'person_in_charge_id' => 'required|exists:persons_in_charge,id',
            'asset_function_id' => 'required|exists:asset_functions,id',
            'funding_source_id' => 'required|exists:funding_sources,id',
            'purchase_cost' => 'required|numeric|min:0',
            'useful_life' => 'required|integer|min:1',
            'salvage_value' => 'required|numeric|min:0|lte:purchase_cost',
        ]);

        $asset->update($request->except('sequence_number'));

        // Regenerate Kode Aset YPT setelah update
        $institution = Institution::find($request->institution_id);
        $year = substr($request->purchase_year, -2);
        $category = Category::find($request->category_id);
        $building = Building::find($request->building_id);
        $room = Room::find($request->room_id);
        $faculty = Faculty::find($request->faculty_id);
        $department = Department::find($request->department_id);
        $personInCharge = PersonInCharge::find($request->person_in_charge_id);
        $assetFunction = AssetFunction::find($request->asset_function_id);
        $fundingSource = FundingSource::find($request->funding_source_id);

        $asset_code_ypt = implode('.', [
            $institution->code,
            $year,
            $category->code,
            $building->code,
            $room->code,
            $faculty->code,
            $department->code,
            $personInCharge->code,
            $assetFunction->code,
            $fundingSource->code,
            $asset->sequence_number
        ]);

        $asset->update(['asset_code_ypt' => $asset_code_ypt]);

        alert()->success('Berhasil!', 'Data aset berhasil diperbarui.');
        return redirect()->route('assets.index');
    }

    /**
     * Menghapus data aset dari database.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        alert()->success('Berhasil!', 'Data aset berhasil dihapus.');
        return redirect()->route('assets.index');
    }

    /**
     * Menampilkan detail satu aset spesifik.
     */
    public function show(Asset $asset)
    {
        // Load relasi agar data master bisa ditampilkan di view
        $asset->load('institution', 'category', 'building', 'room', 'faculty', 'department', 'personInCharge', 'assetFunction', 'fundingSource');

        return view('assets.show', compact('asset'));
    }

    /**
     * Menyiapkan data dan menampilkan halaman untuk mencetak label aset.
     */
    public function printLabels(Request $request)
    {
        $query = Asset::with('institution', 'building', 'room');

        // Cek apakah ada ID yang dikirim dari checkbox
        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }

        $assets = $query->get();

        if ($assets->isEmpty()) {
            alert()->info('Info', 'Tidak ada data aset yang dipilih untuk dicetak.');
            return redirect()->route('assets.index');
        }

        return view('assets.print-labels', compact('assets'));
    }

    /**
     * Menampilkan form untuk batch entry aset.
     */
    public function batchCreate()
    {
        // Logikanya sama persis dengan method create()
        $institutions = Institution::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $personsInCharge = PersonInCharge::orderBy('name')->get();
        $assetFunctions = AssetFunction::orderBy('name')->get();
        $fundingSources = FundingSource::orderBy('name')->get();

        return view('assets.batch-create', compact(
            'institutions',
            'categories',
            'buildings',
            'rooms',
            'faculties',
            'departments',
            'personsInCharge',
            'assetFunctions',
            'fundingSources'
        ));
    }

    /**
     * Menyimpan beberapa aset baru (batch) ke database.
     */
    public function batchStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1', // Validasi untuk jumlah
            // 'start_sequence_number' => 'required|digits:4', // Validasi untuk nomor urut mulai
            'purchase_year' => 'required|digits:4|integer|min:1900',
            'institution_id' => 'required|exists:institutions,id',
            'category_id' => 'required|exists:categories,id',
            'building_id' => 'required|exists:buildings,id',
            'room_id' => 'required|exists:rooms,id',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'person_in_charge_id' => 'required|exists:persons_in_charge,id',
            'asset_function_id' => 'required|exists:asset_functions,id',
            'funding_source_id' => 'required|exists:funding_sources,id',
        ]);

        // Ambil data master sekali saja untuk efisiensi di dalam loop
        $institution = Institution::find($request->institution_id);
        $year = substr($request->purchase_year, -2);
        $category = Category::find($request->category_id);
        $building = Building::find($request->building_id);
        $room = Room::find($request->room_id);
        $faculty = Faculty::find($request->faculty_id);
        $department = Department::find($request->department_id);
        $personInCharge = PersonInCharge::find($request->person_in_charge_id);
        $assetFunction = AssetFunction::find($request->asset_function_id);
        $fundingSource = FundingSource::find($request->funding_source_id);

        $quantity = $request->quantity;
        $latestAsset = Asset::orderBy('id', 'desc')->first();
        $startSequence = $latestAsset ? intval($latestAsset->sequence_number) + 1 : 1;

        // Loop untuk membuat aset sebanyak quantity
        for ($i = 0; $i < $quantity; $i++) {
            $currentSequence = $startSequence + $i;
            $formattedSequence = sprintf('%04d', $currentSequence);

            // Buat aset baru
            $asset = Asset::create(array_merge($request->except(['quantity']), [
                'sequence_number' => $formattedSequence,
            ]));

            // Generate Kode Aset YPT unik untuk setiap aset
            $asset_code_ypt = implode('.', [
                $institution->code,
                $year,
                $category->code,
                $building->code,
                $room->code,
                $faculty->code,
                $department->code,
                $personInCharge->code,
                $assetFunction->code,
                $fundingSource->code,
                $formattedSequence
            ]);

            $asset->update(['asset_code_ypt' => $asset_code_ypt, 'status' => 'Aktif']);
        }

        alert()->success('Berhasil!', "{$quantity} data aset berhasil ditambahkan.");
        return redirect()->route('assets.index');
    }

    /**
     * Menangani proses impor data aset secara massal dari file Excel.
     */
    public function importBatch(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new AssetsBatchImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data aset massal berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            // Menangkap error umum lainnya
            alert()->error('Impor Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('assets.index');
    }

    /**
     * Menangani ekspor semua aset aktif ke Excel.
     */
    public function exportActiveExcel(Request $request) // Tambahkan Request
    {
        $categoryId = $request->input('category_id'); // Ambil category_id dari request
        $fileName = 'daftar-aset-aktif';
        if ($categoryId && $categoryId !== 'all') {
            $category = Category::find($categoryId);
            if ($category) $fileName .= '-' . Str::slug($category->name); // Tambahkan nama kategori ke nama file
        }
        $fileName .= '.xlsx';

        // Kirim categoryId ke class Export
        return Excel::download(new ActiveAssetsExport($categoryId), $fileName);
    }

    /**
     * Menangani download laporan PDF semua aset aktif.
     */
    public function downloadActivePDF(Request $request)
    {
        $categoryId = $request->input('category_id');

        $query = Asset::whereNull('disposal_date')
            // Load SEMUA relasi yang dibutuhkan di PDF
            ->with(['category', 'institution', 'building', 'room', 'department', 'personInCharge', 'assetFunction', 'fundingSource']);

        // Terapkan filter kategori
        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $activeAssets = $query->orderBy('asset_code_ypt', 'asc')->get();

        if ($activeAssets->isEmpty()) {
            alert()->info('Info', 'Tidak ada data aset aktif untuk dilaporkan.');
            return redirect()->route('assets.index');
        }

        // Ambil nama kategori untuk judul PDF
        $categoryName = null;
        if ($categoryId && $categoryId !== 'all') {
            $category = Category::find($categoryId);
            if ($category) $categoryName = $category->name;
        }

        $pj = Employee::where('position', 'Kaur Sarpras')->first();
        $ks = Employee::where('position', 'Kepala Sekolah')->first();
        $kota = "Bandar Lampung"; // Ganti jika perlu

        $pdf = Pdf::loadView('assets.report-all-pdf', compact('activeAssets', 'pj', 'ks', 'kota', 'categoryName')) // Kirim categoryName
            ->setPaper('a4', 'landscape');

        $fileName = 'laporan-daftar-aset-aktif';
        if ($categoryName) $fileName .= '-' . Str::slug($categoryName);
        $fileName .= '.pdf';

        return $pdf->download($fileName);
    }

    public function summary(\Illuminate\Http\Request $request)
    {
        $categoryId = $request->integer('category_id');
        $yearFilter = $request->input('year'); // ← filter tahun dari form
        $search = trim((string) $request->get('q'));

        // Ambil daftar tahun untuk dropdown (urut desc)
        $years = \App\Models\Asset::query()
            ->select('purchase_year')
            ->whereNotNull('purchase_year')
            ->distinct()
            ->orderBy('purchase_year', 'desc')
            ->pluck('purchase_year')
            ->toArray();

        // Subquery normalize kolom
        $base = \App\Models\Asset::query()
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($yearFilter !== null && $yearFilter !== '', fn($q) => $q->where('purchase_year', $yearFilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code_ypt', 'like', "%{$search}%");

                    // 🔐 Aman untuk skema tanpa kolom 'description'
                    if (Schema::hasColumn('assets', 'description')) {
                        $qq->orWhere('description', 'like', "%{$search}%");
                    }
                });
            })
            ->selectRaw('
        name,
        COALESCE(purchase_year, 0) as yr,
        asset_code_ypt,
        status
    ');

        $wrapped = DB::query()->fromSub($base, 't');

        $groups = $wrapped
            ->selectRaw('
            name,
            yr,
            COUNT(*)                                 as qty,
            MIN(asset_code_ypt)                      as sample_code,
            CASE WHEN COUNT(DISTINCT status)=1
                 THEN MIN(status)
                 ELSE "Campuran"
            END                                      as status_label,
            MD5(CONCAT(name,"|",yr))                 as group_key
        ')
            ->groupBy('name', 'yr')
            ->orderBy('name')
            ->orderBy('yr')
            ->paginate(25)
            ->withQueryString();

        // Pass $years ke view
        return view('assets.summary', compact('groups', 'years'));
    }


    public function summaryShow(string $group)
    {
        $items = \App\Models\Asset::query()
            ->whereRaw('MD5(CONCAT(`name`,"|", COALESCE(purchase_year,0))) = ?', [$group])
            ->orderBy('sequence_number')
            ->orderBy('id')
            ->get();

        abort_if($items->isEmpty(), 404);

        $title = $items->first()->name;
        $yr    = $items->first()->purchase_year;

        return view('assets.summary_show', compact('items', 'title', 'yr'));
    }
}
