<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use App\Models\Rkas;
use App\Models\Employee;
use App\Models\AcademicYear;
use App\Models\RabDetail;
use App\Models\RabRealization;
use App\Models\RabRealizationDetail;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Room;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\PersonInCharge;
use App\Models\AssetFunction;
use App\Models\FundingSource;
use App\Models\Institution;
use App\Models\Category;
use App\Models\RabHandover;
use App\Models\RabHandoverItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class RabController extends Controller
{
    public function index()
    {
        $rabs = Rab::with(['academicYear', 'creator', 'realization.details', 'details', 'handovers.department', 'handovers.items'])->latest()->paginate(10);
        $buildings = Building::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $personsInCharge = PersonInCharge::orderBy('name')->get();
        $assetFunctions = AssetFunction::orderBy('name')->get();
        $fundingSources = FundingSource::orderBy('name')->get();
        $institutions = Institution::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get(['id', 'name', 'position']);
        return view('pages.rab.index', compact(
            'rabs', 'buildings', 'rooms', 'faculties', 'departments',
            'personsInCharge', 'assetFunctions', 'fundingSources', 'institutions', 'categories',
            'employees'
        ));
    }

    public function create()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            Alert::error('Error', 'Tidak ada tahun pelajaran aktif. Silakan aktifkan tahun pelajaran terlebih dahulu.');
            return redirect()->route('rkas.index');
        }

        // Ambil MTA unik dari RKAS tahun aktif
        $mtaList = Rkas::where('academic_year_id', $activeYear->id)
            ->select('mta', 'nama_akun')
            ->distinct()
            ->get();

        $employees = Employee::orderBy('name')->get();
        $headmaster = Employee::where('is_headmaster', true)->first();

        return view('pages.rab.create', compact('activeYear', 'mtaList', 'employees', 'headmaster'));
    }

    public function getMtaDetails(Request $request)
    {
        $mta = $request->mta;
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) return response()->json([]);

        $rkasItems = Rkas::where('academic_year_id', $activeYear->id)
            ->where('mta', $mta)
            ->get();

        return response()->json([
            'nama_akun' => $rkasItems->first()->nama_akun ?? '',
            'drk' => $rkasItems->first()->nama_drk ?? '',
            'items' => $rkasItems
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'mta' => 'required',
            'kebutuhan_waktu' => 'required',
            'created_by_id' => 'nullable|exists:employees,id',
            'checked_by_id' => 'nullable|exists:employees,id',
            'approved_by_id' => 'nullable|exists:employees,id',
            'headmaster_id' => 'nullable|exists:employees,id',
            'selected_rkas' => 'required|array',
        ]);

        $rab = Rab::create([
            'name' => $request->name,
            'academic_year_id' => $request->academic_year_id,
            'mta' => $request->mta,
            'nama_akun' => $request->nama_akun_hidden,
            'drk' => $request->drk_hidden,
            'kebutuhan_waktu' => $request->kebutuhan_waktu,
            'total_amount' => 0, // Will be updated
            'created_by_id' => $request->created_by_id,
            'checked_by_id' => $request->checked_by_id,
            'approved_by_id' => $request->approved_by_id,
            'headmaster_id' => $request->headmaster_id,
            'notes' => $request->notes,
        ]);

        $totalAmount = 0;
        foreach ($request->selected_rkas as $rkasId) {
            $rkas = Rkas::find($rkasId);
            $alias = $request->alias[$rkasId] ?? $rkas->rincian_kegiatan;
            $specification = $request->specification[$rkasId] ?? '';
            
            $customVol = $request->custom_vol[$rkasId] ?? $rkas->quantity;
            $customPrice = $request->custom_price[$rkasId] ?? $rkas->tarif;
            $amount = $customVol * $customPrice;

            // Limit validation
            if ($amount > ($rkas->quantity * $rkas->tarif)) {
                $amount = $rkas->quantity * $rkas->tarif;
                $customVol = $rkas->quantity;
                $customPrice = $rkas->tarif;
            }

            $totalAmount += $amount;

            RabDetail::create([
                'rab_id' => $rab->id,
                'rkas_id' => $rkas->id,
                'alias_name' => $alias,
                'specification' => $specification,
                'quantity' => $customVol,
                'unit' => $rkas->satuan,
                'price' => $customPrice,
                'amount' => $amount,
            ]);
        }

        $rab->update(['total_amount' => $totalAmount]);

        Alert::success('Berhasil', 'Data RAB berhasil disimpan.');
        return redirect()->route('rab.index');
    }

    public function edit(Rab $rab)
    {
        $rab->load(['details']);
        $activeYear = $rab->academicYear;
        
        // Ambil MTA unik dari RKAS tahun terkait
        $mtaList = Rkas::where('academic_year_id', $activeYear->id)
            ->select('mta', 'nama_akun')
            ->distinct()
            ->get();

        $employees = Employee::orderBy('name')->get();
        $headmaster = Employee::where('is_headmaster', true)->first();

        // Prepare selected items for JSON (Alpine.js)
        $selectedItemsData = Rkas::where('academic_year_id', $activeYear->id)
            ->where('mta', $rab->mta)
            ->get()
            ->map(function($item) use ($rab) {
                $detail = $rab->details->where('rkas_id', $item->id)->first();
                return [
                    'id' => $item->id,
                    'rincian_kegiatan' => $item->rincian_kegiatan,
                    'quantity' => $item->quantity,
                    'satuan' => $item->satuan,
                    'tarif' => $item->tarif,
                    'is_selected' => !!$detail,
                    'alias_name' => $detail ? $detail->alias_name : $item->rincian_kegiatan,
                    'specification' => $detail ? $detail->specification : '',
                    'customVol' => $detail ? (float)$detail->quantity : (float)$item->quantity,
                    'customPrice' => $detail ? (float)$detail->price : (float)$item->tarif,
                    'customAmount' => $detail ? (float)$detail->amount : (float)($item->quantity * $item->tarif),
                ];
            });

        return view('pages.rab.edit', compact('rab', 'activeYear', 'mtaList', 'employees', 'headmaster', 'selectedItemsData'));
    }

    public function update(Request $request, Rab $rab)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mta' => 'required',
            'kebutuhan_waktu' => 'required',
            'created_by_id' => 'nullable|exists:employees,id',
            'checked_by_id' => 'nullable|exists:employees,id',
            'approved_by_id' => 'nullable|exists:employees,id',
            'headmaster_id' => 'nullable|exists:employees,id',
            'selected_rkas' => 'required|array',
        ]);

        $rab->update([
            'name' => $request->name,
            'mta' => $request->mta,
            'nama_akun' => $request->nama_akun_hidden,
            'drk' => $request->drk_hidden,
            'kebutuhan_waktu' => $request->kebutuhan_waktu,
            'created_by_id' => $request->created_by_id,
            'checked_by_id' => $request->checked_by_id,
            'approved_by_id' => $request->approved_by_id,
            'headmaster_id' => $request->headmaster_id,
            'notes' => $request->notes,
        ]);

        // Sync details: Delete existing and recreate
        $rab->details()->delete();

        $totalAmount = 0;
        foreach ($request->selected_rkas as $rkasId) {
            $rkas = Rkas::find($rkasId);
            $alias = $request->alias[$rkasId] ?? $rkas->rincian_kegiatan;
            $specification = $request->specification[$rkasId] ?? '';
            
            $customVol = $request->custom_vol[$rkasId] ?? $rkas->quantity;
            $customPrice = $request->custom_price[$rkasId] ?? $rkas->tarif;
            $amount = $customVol * $customPrice;

            // Limit validation
            if ($amount > ($rkas->quantity * $rkas->tarif)) {
                $amount = $rkas->quantity * $rkas->tarif;
                $customVol = $rkas->quantity;
                $customPrice = $rkas->tarif;
            }

            $totalAmount += $amount;

            RabDetail::create([
                'rab_id' => $rab->id,
                'rkas_id' => $rkas->id,
                'alias_name' => $alias,
                'specification' => $specification,
                'quantity' => $customVol,
                'unit' => $rkas->satuan,
                'price' => $customPrice,
                'amount' => $amount,
            ]);
        }

        $rab->update(['total_amount' => $totalAmount]);

        Alert::success('Berhasil', 'Data RAB berhasil diperbarui.');
        return redirect()->route('rab.index');
    }

    public function show(Rab $rab)
    {
        $rab->load(['academicYear', 'creator', 'checker', 'approver', 'headmaster', 'details.rkas', 'realization.details']);
        return view('pages.rab.show', compact('rab'));
    }

    public function destroy(Rab $rab)
    {
        $rab->delete();
        Alert::success('Berhasil', 'Data RAB berhasil dihapus.');
        return redirect()->route('rab.index');
    }

    public function exportPdf(Rab $rab)
    {
        $rab->load(['academicYear', 'creator', 'checker', 'approver', 'headmaster', 'details.rkas']);
        $kopSurat = \App\Models\Setting::get('kop_surat');
        $pdf = Pdf::loadView('pages.rab.pdf', compact('rab', 'kopSurat'))->setPaper('a4', 'portrait');
        return $pdf->download('RAB_' . str_replace(' ', '_', $rab->name) . '.pdf');
    }

    public function realizationPdf(Request $request, Rab $rab)
    {
        $rab->load(['academicYear', 'creator', 'checker', 'approver', 'headmaster', 'details.rkas']);
        $kopSurat = \App\Models\Setting::get('kop_surat');
        
        $items = [];
        $totalPenerimaan = 0;
        $totalPengeluaran = 0;

        DB::beginTransaction();
        try {
            // Delete existing realization for this RAB to overwrite
            if ($rab->realization) {
                $rab->realization->details()->delete();
                $rab->realization->delete();
            }

            $realization = $rab->realization()->create([
                'total_penerimaan' => 0, // placeholder
                'total_pengeluaran' => 0, // placeholder
                'final_balance' => 0, // placeholder
            ]);

            if ($request->has('uraian')) {
                foreach ($request->uraian as $index => $uraian) {
                    $penerimaan = (float) str_replace(['Rp', '.', ' '], '', $request->penerimaan[$index] ?? 0);
                    $pengeluaran = (float) str_replace(['Rp', '.', ' '], '', $request->pengeluaran[$index] ?? 0);
                    
                    $detailData = [
                        'tgl' => $request->tgl[$index] ?? '-',
                        'uraian' => $uraian,
                        'qty' => $request->qty[$index] ?? null,
                        'spesifikasi' => $request->spesifikasi[$index] ?? null,
                        'penerimaan' => $penerimaan,
                        'pengeluaran' => $pengeluaran,
                        'keterangan' => $request->keterangan[$index] ?? '-'
                    ];

                    $realization->details()->create($detailData);

                    $items[] = $detailData;
                    $totalPenerimaan += $penerimaan;
                    $totalPengeluaran += $pengeluaran;
                }
            }

            $realization->update([
                'total_penerimaan' => $totalPenerimaan,
                'total_pengeluaran' => $totalPengeluaran,
                'final_balance' => $totalPenerimaan - $totalPengeluaran,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'Gagal menyimpan data realisasi: ' . $e->getMessage());
            return back();
        }

        $pdf = Pdf::loadView('pages.rab.realization-pdf', compact('rab', 'kopSurat', 'items'))->setPaper('a4', 'portrait');
        return $pdf->download('REALISASI_' . str_replace(' ', '_', $rab->name) . '.pdf');
    }

    public function convertToAssets(Request $request, Rab $rab)
    {
        $request->validate(['items' => 'required|array']);

        $allItems = collect($request->items ?? []);
        $selectedItems = $allItems->filter(fn($item) => isset($item['convert']) && $item['convert']);

        if ($selectedItems->isEmpty()) {
            Alert::warning('Peringatan', 'Pilih minimal satu item untuk dikonversi.');
            return back();
        }

        $rules = [];
        foreach ($selectedItems->keys() as $key) {
            $rules["items.{$key}.name"]                = 'required|string';
            $rules["items.{$key}.quantity"]            = 'required|integer|min:1';
            $rules["items.{$key}.category_id"]         = 'required|exists:categories,id';
            $rules["items.{$key}.institution_id"]      = 'required|exists:institutions,id';
            $rules["items.{$key}.building_id"]         = 'required|exists:buildings,id';
            $rules["items.{$key}.room_id"]             = 'required|exists:rooms,id';
            $rules["items.{$key}.faculty_id"]          = 'required|exists:faculties,id';
            $rules["items.{$key}.department_id"]       = 'required|exists:departments,id';
            $rules["items.{$key}.person_in_charge_id"] = 'required|exists:persons_in_charge,id';
            $rules["items.{$key}.asset_function_id"]   = 'required|exists:asset_functions,id';
            $rules["items.{$key}.funding_source_id"]   = 'required|exists:funding_sources,id';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $rab->loadMissing('academicYear');
            $latestAsset = Asset::orderBy('id', 'desc')->first();
            $startSequence = $latestAsset ? intval($latestAsset->sequence_number) : 0;
            $count = 0;
            $purchaseYear = $rab->academicYear->year ?? date('Y');

            foreach ($selectedItems as $detailId => $itemData) {
                $qty = intval($itemData['quantity']);
                for ($i = 0; $i < $qty; $i++) {
                    $count++;
                    $formattedSequence = sprintf('%04d', $startSequence + $count);

                    $asset = Asset::create([
                        'name'                => $itemData['name'],
                        'category_id'         => $itemData['category_id'],
                        'institution_id'      => $itemData['institution_id'],
                        'purchase_year'       => $purchaseYear,
                        'purchase_cost'       => $itemData['purchase_cost'] ?? 0,
                        'sequence_number'     => $formattedSequence,
                        'status'              => 'Aktif',
                        'building_id'         => $itemData['building_id'],
                        'room_id'             => $itemData['room_id'],
                        'faculty_id'          => $itemData['faculty_id'],
                        'department_id'       => $itemData['department_id'],
                        'person_in_charge_id' => $itemData['person_in_charge_id'],
                        'asset_function_id'   => $itemData['asset_function_id'],
                        'funding_source_id'   => $itemData['funding_source_id'],
                    ]);

                    $this->generateAssetCode($asset);
                }
            }

            DB::commit();
            Alert::success('Berhasil!', 'Item RAB berhasil dikonversi menjadi data aset.');
            return redirect()->route('assets.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }
    }

    private function generateAssetCode(Asset $asset): void
    {
        $asset->load([
            'institution', 'category', 'building', 'room',
            'faculty', 'department', 'personInCharge',
            'assetFunction', 'fundingSource'
        ]);

        $year2 = $asset->purchase_year ? substr((string)$asset->purchase_year, -2) : '00';

        $code = implode('.', [
            $asset->institution->code    ?? 'XX',
            $year2,
            $asset->category->code       ?? 'XX',
            $asset->building->code       ?? 'XX',
            $asset->room->code           ?? 'XX',
            $asset->faculty->code        ?? 'XX',
            $asset->department->code     ?? 'XX',
            $asset->personInCharge->code ?? 'XX',
            $asset->assetFunction->code  ?? 'XX',
            $asset->fundingSource->code  ?? 'XX',
            $asset->sequence_number,
        ]);

        $asset->update(['asset_code_ypt' => $code]);
    }

    public function storeHandover(Request $request, Rab $rab)
    {
        $request->validate([
            'handover_date'     => 'required|date',
            'handed_by'         => 'required|string|max:255',
            'handed_by_jabatan' => 'nullable|string|max:255',
            'items'             => 'required|array',
        ]);

        $items = collect($request->items)->filter(fn($i) => isset($i['include']) && $i['include']);

        if ($items->isEmpty()) {
            Alert::warning('Peringatan', 'Pilih minimal satu barang untuk diserahterimakan.');
            return back();
        }

        $grouped = $items->groupBy('dept_id');
        if ($grouped->has('') || $grouped->has(null)) {
            Alert::warning('Peringatan', 'Semua barang yang dipilih harus memiliki unit tujuan.');
            return back();
        }

        DB::beginTransaction();
        try {
            $year      = now()->year;
            $seq       = RabHandover::whereYear('created_at', $year)->count();
            $createdHandovers = [];

            foreach ($grouped as $deptId => $deptItems) {
                $seq++;
                $docNum   = 'BAST-RAB/' . $year . '/' . str_pad($seq, 4, '0', STR_PAD_LEFT);
                $received = $request->input('received_by.' . $deptId);

                $handover = RabHandover::create([
                    'rab_id'              => $rab->id,
                    'department_id'       => $deptId,
                    'document_number'     => $docNum,
                    'handover_date'       => $request->handover_date,
                    'handed_by'           => $request->handed_by,
                    'handed_by_jabatan'   => $request->handed_by_jabatan,
                    'received_by'         => $received,
                    'received_by_jabatan' => $request->input('received_by_jabatan.' . $deptId),
                ]);

                foreach ($deptItems as $item) {
                    RabHandoverItem::create([
                        'rab_handover_id' => $handover->id,
                        'uraian'          => $item['uraian'],
                        'qty'             => $item['qty'] ?? null,
                        'spesifikasi'     => $item['spesifikasi'] ?? null,
                        'keterangan'      => $item['keterangan'] ?? null,
                    ]);
                }

                $createdHandovers[] = $docNum;
            }

            DB::commit();

            $docList = implode(', ', $createdHandovers);
            Alert::success('Berhasil!', count($createdHandovers) . ' BAST dibuat: ' . $docList);
            return redirect()->route('rab.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }
    }

    public function destroyHandover(Rab $rab, RabHandover $handover)
    {
        if ($handover->rab_id !== $rab->id) {
            abort(403);
        }
        $docNum = $handover->document_number;
        $handover->delete();
        Alert::success('Berhasil', "BAST {$docNum} berhasil dihapus.");
        return redirect()->route('rab.index');
    }

    public function downloadHandoverBast(Rab $rab, RabHandover $handover)
    {
        $handover->load(['rab.academicYear', 'department', 'items']);
        $rab->load('headmaster');
        $kopSurat  = \App\Models\Setting::get('kop_surat');
        $headmaster = Employee::where('is_headmaster', true)->first();

        $pdf = Pdf::loadView('pages.rab.handover-bast-pdf', compact('rab', 'handover', 'kopSurat', 'headmaster'))
                   ->setPaper('a4', 'portrait');

        $filename = 'BAST_' . str_replace('/', '-', $handover->document_number) . '.pdf';
        return $pdf->download($filename);
    }
}
