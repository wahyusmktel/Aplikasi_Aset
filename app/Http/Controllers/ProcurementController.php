<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\ProcurementHandover;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Department;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Room;
use App\Models\Faculty;
use App\Models\PersonInCharge;
use App\Models\AssetFunction;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $procurements = Procurement::with('vendor')
            ->when($search, function ($query, $search) {
                return $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10);

        return view('procurements.index', compact('procurements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $categories = Category::all();
        $institutions = Institution::all();
        return view('procurements.create', compact('vendors', 'categories', 'institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'procurement_date' => 'required|date',
            'reference_number' => 'required|string|unique:procurements,reference_number',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalCost = 0;
            foreach ($request->items as $item) {
                $totalCost += $item['quantity'] * $item['unit_price'];
            }

            $procurement = Procurement::create([
                'vendor_id' => $request->vendor_id,
                'procurement_date' => $request->procurement_date,
                'reference_number' => $request->reference_number,
                'total_cost' => $totalCost,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                ProcurementItem::create([
                    'procurement_id' => $procurement->id,
                    'name' => $item['name'],
                    'category_id' => $item['category_id'] ?? null,
                    'institution_id' => $item['institution_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'specs' => $item['specs'] ?? null,
                ]);
            }

            DB::commit();
            alert()->success('Berhasil!', 'Data pengadaan berhasil disimpan.');
            return redirect()->route('procurements.index');
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Procurement $procurement)
    {
        $procurement->load(['vendor', 'items.category', 'items.institution', 'handovers.fromUser', 'handovers.toUser', 'handovers.toDepartment']);
        $departments = Department::all();
        $buildings = Building::all();
        $rooms = Room::all();
        $faculties = Faculty::all();
        $personsInCharge = PersonInCharge::all();
        $assetFunctions = AssetFunction::all();
        $fundingSources = FundingSource::all();

        return view('procurements.show', compact(
            'procurement', 'departments', 'buildings', 
            'rooms', 'faculties', 'personsInCharge', 
            'assetFunctions', 'fundingSources'
        ));
    }

    /**
     * Mark procurement as received and generate BAST Vendor -> School.
     */
    public function markAsReceived(Request $request, Procurement $procurement)
    {
        $request->validate([
            'handover_date' => 'required|date',
            'document_number' => 'required|string|unique:procurement_handovers,document_number',
            'from_name' => 'required|string', // Name of Vendor Representative
        ]);

        DB::beginTransaction();
        try {
            $procurement->update(['status' => 'received']);

            ProcurementHandover::create([
                'procurement_id' => $procurement->id,
                'type' => 'vendor_to_school',
                'document_number' => $request->document_number,
                'handover_date' => $request->handover_date,
                'from_name' => $request->from_name,
                'to_user_id' => Auth::id(), // User who receives (e.g., Waka Sarpra)
                'notes' => $request->notes,
            ]);

            DB::commit();
            alert()->success('Berhasil!', 'Barang telah diterima dan BAST Vendor telah dibuat.');
            return redirect()->route('procurements.show', $procurement);
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Generate BAST School -> Unit.
     */
    public function handoverToUnit(Request $request, Procurement $procurement)
    {
        $request->validate([
            'handover_date' => 'required|date',
            'document_number' => 'required|string|unique:procurement_handovers,document_number',
            'to_person_in_charge_id' => 'required|exists:persons_in_charge,id',
            'to_name' => 'required|string', // Name of Unit Representative
        ]);

        DB::beginTransaction();
        try {
            $procurement->update(['status' => 'unit_delivered']);

            ProcurementHandover::create([
                'procurement_id' => $procurement->id,
                'type' => 'school_to_unit',
                'document_number' => $request->document_number,
                'handover_date' => $request->handover_date,
                'from_user_id' => Auth::id(),
                'to_name' => $request->to_name,
                'to_person_in_charge_id' => $request->to_person_in_charge_id,
                'notes' => $request->notes,
            ]);

            DB::commit();
            alert()->success('Berhasil!', 'BAST Unit berhasil dibuat.');
            return redirect()->route('procurements.show', $procurement);
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Convert procurement items to actual assets.
     */
    public function convertToAssets(Request $request, Procurement $procurement)
    {
        if ($procurement->status != 'received' && $procurement->status != 'unit_delivered') {
            alert()->warning('Peringatan!', 'Barang harus diterima terlebih dahulu.');
            return back();
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.building_id' => 'required|exists:buildings,id',
            'items.*.room_id' => 'required|exists:rooms,id',
            'items.*.faculty_id' => 'required|exists:faculties,id',
            'items.*.department_id' => 'required|exists:departments,id',
            'items.*.person_in_charge_id' => 'required|exists:persons_in_charge,id',
            'items.*.asset_function_id' => 'required|exists:asset_functions,id',
            'items.*.funding_source_id' => 'required|exists:funding_sources,id',
        ]);

        DB::beginTransaction();
        try {
            $latestAsset = Asset::orderBy('id', 'desc')->first();
            $startSequence = $latestAsset ? intval($latestAsset->sequence_number) : 0;
            $count = 0;

            foreach ($request->items as $itemId => $details) {
                $item = $procurement->items()->find($itemId);
                
                if (!$item || $item->is_converted_to_asset) continue;

                for ($i = 0; $i < $item->quantity; $i++) {
                    $count++;
                    $formattedSequence = sprintf('%04d', $startSequence + $count);
                    
                    $asset = Asset::create([
                        'name' => $item['name'],
                        'category_id' => $item['category_id'],
                        'institution_id' => $item['institution_id'],
                        'purchase_year' => $procurement->procurement_date->year,
                        'purchase_cost' => $item['unit_price'],
                        'sequence_number' => $formattedSequence,
                        'status' => 'Aktif',
                        
                        'building_id' => $details['building_id'],
                        'room_id' => $details['room_id'],
                        'faculty_id' => $details['faculty_id'],
                        'department_id' => $details['department_id'],
                        'person_in_charge_id' => $details['person_in_charge_id'],
                        'asset_function_id' => $details['asset_function_id'],
                        'funding_source_id' => $details['funding_source_id'],
                    ]);

                    // Generate official YPT Asset Code
                    $this->generateAssetCode($asset);
                }

                $item->update(['is_converted_to_asset' => true]);
            }
            
            DB::commit();

            alert()->success('Berhasil!', 'Item pengadaan telah dikonversi menjadi data aset dengan kode YPT otomatis.');
            return redirect()->route('assets.index');
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Helper to generate YPT Asset Code.
     */
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
            $asset->sequence_number      ?? '0000'
        ]);

        $asset->forceFill(['asset_code_ypt' => $code])->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procurement $procurement)
    {
        // Check if any items have been converted to assets
        $hasConvertedItems = $procurement->items()->where('is_converted_to_asset', true)->exists();
        
        if ($hasConvertedItems) {
            alert()->error('Gagal!', 'Pengadaan ini tidak dapat dihapus karena beberapa item sudah dikonversi menjadi aset.');
            return back();
        }

        DB::beginTransaction();
        try {
            // Delete related handovers (documents)
            $procurement->handovers()->delete();
            
            // Delete related items
            $procurement->items()->delete();
            
            // Delete the procurement record
            $procurement->delete();

            DB::commit();
            alert()->success('Berhasil!', 'Data pengadaan berhasil dihapus.');
            return redirect()->route('procurements.index');
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Download or Print BAST.
     */
    public function downloadBast(Procurement $procurement, $type)
    {
        $handover = $procurement->handovers()->where('type', $type)
            ->with(['toDepartment', 'toPersonInCharge'])
            ->first();
        if (!$handover) {
            alert()->error('Gagal!', 'Dokumen BAST belum tersedia.');
            return back();
        }

        $wakaSarpra = \App\Models\Employee::where('is_sarpra_it_lab', true)->first();
        $headmaster = \App\Models\Employee::where('is_headmaster', true)->first();

        $pdf = Pdf::loadView('procurements.bast-pdf', compact('procurement', 'handover', 'type', 'wakaSarpra', 'headmaster'))
                  ->setPaper('a4', 'portrait');

        $fileName = 'BAST-' . str_replace('/', '-', $handover->document_number) . '.pdf';
        
        return $pdf->download($fileName);
    }
}
