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

class AssetController extends Controller
{
    /**
     * Menampilkan daftar semua aset.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assets = Asset::with([
            'category',
            'institution',
            'building',
            'room'
        ])
            ->when($search, function ($query, $search) {
                // Pencarian berdasarkan nama aset atau kode YPT
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code_ypt', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('assets.index', compact('assets'));
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
}
