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
use App\Models\SavedFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    private function assertAllowedStatusChange(string $from = null, string $to): void
    {
        // definisi transisi yang diizinkan
        $map = [
            'Aktif'       => ['Dipinjam', 'Maintenance', 'Rusak', 'Disposed'],
            'Dipinjam'    => ['Aktif', 'Maintenance', 'Rusak'],
            'Maintenance' => ['Aktif', 'Rusak', 'Disposed'],
            'Rusak'       => ['Aktif', 'Maintenance', 'Disposed'],
            'Disposed'    => [], // final state
            null          => ['Aktif', 'Dipinjam', 'Maintenance', 'Rusak', 'Disposed'], // jaga-jaga
        ];

        $from = $from ?? 'Aktif';
        if (!isset($map[$from]) || !in_array($to, $map[$from], true)) {
            abort(422, "Transisi status tidak diizinkan: {$from} → {$to}");
        }
    }

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
        $yearFilter = $request->input('year');
        $statuses   = array_filter((array) $request->input('status', []));
        $search     = trim((string) $request->get('q'));

        // Load preset by id (opsional)
        if ($presetId = $request->integer('preset_id')) {
            $preset = SavedFilter::where('user_id', Auth::id())
                ->where('scope', 'assets_summary')
                ->find($presetId);

            if ($preset) {
                // merge preset payload ke request
                $payload = $preset->payload ?: [];
                $categoryId = $payload['category_id'] ?? $categoryId;
                $yearFilter = $payload['year'] ?? $yearFilter;
                $statuses   = array_filter((array) ($payload['status'] ?? $statuses));
                $search     = $payload['q'] ?? $search;
            }
        }

        // Dropdown Tahun & Status (seperti sebelumnya)
        $years = \App\Models\Asset::query()
            ->select('purchase_year')->whereNotNull('purchase_year')
            ->distinct()->orderBy('purchase_year', 'desc')->pluck('purchase_year')->toArray();
        $allStatuses = ['Aktif', 'Dipinjam', 'Maintenance', 'Rusak', 'Disposed'];

        // Subquery base (tambahkan purchase_cost untuk agregasi total)
        $base = \App\Models\Asset::query()
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($yearFilter !== null && $yearFilter !== '', fn($q) => $q->where('purchase_year', $yearFilter))
            ->when(!empty($statuses), fn($q) => $q->whereIn('status', $statuses))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code_ypt', 'like', "%{$search}%");
                    if (\Illuminate\Support\Facades\Schema::hasColumn('assets', 'description')) {
                        $qq->orWhere('description', 'like', "%{$search}%");
                    }
                });
            })
            ->selectRaw('
            name,
            COALESCE(purchase_year, 0) as yr,
            asset_code_ypt,
            status,
            COALESCE(purchase_cost,0) as purchase_cost
        ');

        $wrapped = \Illuminate\Support\Facades\DB::query()->fromSub($base, 't');

        $groups = $wrapped
            ->selectRaw('
            name,
            yr,
            COUNT(*)                                 as qty,
            MIN(asset_code_ypt)                      as min_code,
            MAX(asset_code_ypt)                      as max_code,
            SUM(purchase_cost)                       as total_cost,
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

        // Ambil semua preset user untuk dropdown
        $presets = SavedFilter::where('user_id', Auth::id())
            ->where('scope', 'assets_summary')
            ->orderBy('name')->get();

        return view('assets.summary', compact('groups', 'years', 'allStatuses', 'presets'));
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

    public function summaryExportExcel(\Illuminate\Http\Request $request)
    {
        $categoryId = $request->input('category_id');
        $yearFilter = $request->input('year');
        $statuses   = array_filter((array) $request->input('status', []));
        $search     = trim((string) $request->get('q'));

        // Ambil data sesuai filter (bukan grouped; export detail biar lengkap)
        $query = \App\Models\Asset::query()
            ->with(['category', 'building', 'room', 'personInCharge'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($yearFilter !== null && $yearFilter !== '', fn($q) => $q->where('purchase_year', $yearFilter))
            ->when(!empty($statuses), fn($q) => $q->whereIn('status', $statuses))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code_ypt', 'like', "%{$search}%");
                    if (\Illuminate\Support\Facades\Schema::hasColumn('assets', 'description')) {
                        $qq->orWhere('description', 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy('asset_code_ypt', 'asc');

        // Exporter minimalis inline (tanpa bikin file class baru)
        $rows = $query->get()->map(function ($a) {
            return [
                'Kode Aset YPT' => $a->asset_code_ypt,
                'Nama'          => $a->name,
                'Tahun'         => $a->purchase_year,
                'Kategori'      => optional($a->category)->name,
                'Gedung/Ruang'  => optional($a->building)->name . ' / ' . optional($a->room)->name,
                'PIC'           => optional($a->personInCharge)->name,
                'Status'        => $a->status,
                'Nilai Akuisisi (Rp)' => $a->purchase_cost,
            ];
        });

        // Pakai FromArray (tanpa class), supaya cepat
        $export = new class($rows->toArray()) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(private array $data) {}
            public function array(): array
            {
                return $this->data;
            }
            public function headings(): array
            {
                return array_keys($this->data[0] ?? ['Data' => 'Kosong']);
            }
        };

        $file = 'export-ringkasan-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download($export, $file);
    }

    public function summaryExportPdf(\Illuminate\Http\Request $request)
    {
        $categoryId = $request->input('category_id');
        $yearFilter = $request->input('year');
        $statuses   = array_filter((array) $request->input('status', []));
        $search     = trim((string) $request->get('q'));

        $query = \App\Models\Asset::query()
            ->with(['category', 'building', 'room', 'personInCharge'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($yearFilter !== null && $yearFilter !== '', fn($q) => $q->where('purchase_year', $yearFilter))
            ->when(!empty($statuses), fn($q) => $q->whereIn('status', $statuses))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code_ypt', 'like', "%{$search}%");
                    if (\Illuminate\Support\Facades\Schema::hasColumn('assets', 'description')) {
                        $qq->orWhere('description', 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy('asset_code_ypt', 'asc');

        $data = $query->get();

        // Reuse view PDF aktif kamu atau bikin ringkas:
        $pdf = Pdf::loadView('assets.report-all-pdf', [
            'activeAssets' => $data,
            'pj' => \App\Models\Employee::where('position', 'Kaur Sarpras')->first(),
            'ks' => \App\Models\Employee::where('position', 'Kepala Sekolah')->first(),
            'kota' => 'Bandar Lampung',
            'categoryName' => optional(\App\Models\Category::find($categoryId))->name,
        ])->setPaper('a4', 'landscape');

        $fileName = 'laporan-ringkasan-' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    public function saveSummaryPreset(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $payload = [
            'q' => $request->get('q'),
            'category_id' => $request->get('category_id'),
            'year' => $request->get('year'),
            'status' => (array) $request->get('status'),
        ];

        $preset = SavedFilter::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'scope'   => 'assets_summary',
            'name'    => $request->get('name'),
            'payload' => $payload,
        ]);

        return redirect()->route('assets.summary', ['preset_id' => $preset->id])
            ->with('success', 'Preset filter tersimpan.');
    }

    public function deleteSummaryPreset(SavedFilter $preset)
    {
        abort_unless($preset->user_id === \Illuminate\Support\Facades\Auth::id(), 403);
        $preset->delete();
        return redirect()->route('assets.summary')->with('success', 'Preset dihapus.');
    }

    // public function roomsByBuilding(\App\Models\Building $building)
    // {
    //     $rooms = \App\Models\Room::where('building_id', $building->id)
    //         ->orderBy('name')
    //         ->get(['id', 'name']);

    //     return response()->json([
    //         'building_id' => $building->id,
    //         'rooms' => $rooms,
    //     ]);
    // }
    public function bulkMove(Request $request)
    {
        $data = $request->validate([
            'ids'                 => 'required|string',
            'building_id'         => 'nullable|exists:buildings,id',
            'room_id'             => 'nullable|exists:rooms,id',
            'person_in_charge_id' => 'nullable|exists:persons_in_charge,id',
        ]);

        $ids = collect(explode(',', $data['ids']))->filter()->map('intval')->unique()->values();

        if ($ids->isEmpty()) return back()->with('error', 'Tidak ada aset yang dipilih.');
        if (empty($data['building_id']) && empty($data['room_id']) && empty($data['person_in_charge_id'])) {
            return back()->with('error', 'Pilih minimal salah satu: Gedung / Ruang / PIC.');
        }

        $assets = \App\Models\Asset::whereIn('id', $ids)->get();
        $moved = 0;
        $skipped = 0;

        DB::transaction(function () use ($assets, $data, &$moved, &$skipped) {
            foreach ($assets as $a) {
                // aturan: Disposed tidak boleh dipindah
                if (($a->status ?? '') === 'Disposed') {
                    $skipped++;
                    continue;
                }

                $before = [
                    'building_id' => $a->building_id,
                    'room_id'     => $a->room_id,
                    'pic_id'      => $a->person_in_charge_id,
                ];

                $payload = [];
                if (!empty($data['building_id']))         $payload['building_id'] = $data['building_id'];
                if (!empty($data['room_id']))             $payload['room_id'] = $data['room_id'];
                if (!empty($data['person_in_charge_id'])) $payload['person_in_charge_id'] = $data['person_in_charge_id'];

                // skip kalau tidak ada perubahan nyata
                $changed = array_filter($payload, fn($v, $k) => $a->{$k} != $v, ARRAY_FILTER_USE_BOTH);
                if (empty($changed)) {
                    $skipped++;
                    continue;
                }

                $a->update($payload);

                $after = [
                    'building_id' => $a->building_id,
                    'room_id'     => $a->room_id,
                    'pic_id'      => $a->person_in_charge_id,
                ];

                \App\Services\AuditLogger::log($a, 'bulk_move', $before, $after);
                $moved++;
            }
        });

        $msg = "Aset diproses: {$assets->count()}, berhasil: {$moved}, dilewati: {$skipped}.";
        return back()->with('success', "Pindah/Assign selesai. {$msg}");
    }
    public function bulkStatus(Request $request)
    {
        $data = $request->validate([
            'ids'    => 'required|string',
            'status' => 'required|string|in:Aktif,Dipinjam,Maintenance,Rusak,Disposed',
        ]);
        $ids = collect(explode(',', $data['ids']))->filter()->map('intval')->unique()->values();
        if ($ids->isEmpty()) return back()->with('error', 'Tidak ada aset yang dipilih.');

        $assets = \App\Models\Asset::whereIn('id', $ids)->get();
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($assets, $data, &$updated, &$skipped) {
            foreach ($assets as $a) {
                $from = $a->status ?? 'Aktif';
                $to   = $data['status'];

                // validasi transisi status
                try {
                    $this->assertAllowedStatusChange($from, $to);
                } catch (\Throwable $e) {
                    $skipped++;
                    continue;
                }

                if ($from === $to) {
                    $skipped++;
                    continue;
                }

                $before = ['status' => $from];
                $a->update(['status' => $to]);
                $after  = ['status' => $a->status];

                \App\Services\AuditLogger::log($a, 'bulk_status', $before, $after);
                $updated++;
            }
        });

        $msg = "Aset diproses: {$assets->count()}, berhasil: {$updated}, dilewati: {$skipped}.";
        return back()->with('success', "Update Status selesai. {$msg}");
    }

    public function auditsIndex(Request $request)
    {
        $ids = collect(explode(',', (string)$request->get('ids')))
            ->filter()->map('intval')->unique()->values();

        $logs = \App\Models\AssetAudit::with('asset:id,asset_code_ypt,name')
            ->when($ids->isNotEmpty(), fn($q) => $q->whereIn('asset_id', $ids))
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('assets.audits-index', compact('logs', 'ids'));
    }
}
