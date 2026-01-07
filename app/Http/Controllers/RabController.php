<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use App\Models\Rkas;
use App\Models\Employee;
use App\Models\AcademicYear;
use App\Models\RabDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

class RabController extends Controller
{
    public function index()
    {
        $rabs = Rab::with(['academicYear', 'creator'])->latest()->paginate(10);
        return view('pages.rab.index', compact('rabs'));
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
            
            $amount = $rkas->quantity * $rkas->tarif;
            $totalAmount += $amount;

            RabDetail::create([
                'rab_id' => $rab->id,
                'rkas_id' => $rkas->id,
                'alias_name' => $alias,
                'specification' => $specification,
                'quantity' => $rkas->quantity,
                'unit' => $rkas->satuan,
                'price' => $rkas->tarif,
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
            
            $amount = $rkas->quantity * $rkas->tarif;
            $totalAmount += $amount;

            RabDetail::create([
                'rab_id' => $rab->id,
                'rkas_id' => $rkas->id,
                'alias_name' => $alias,
                'specification' => $specification,
                'quantity' => $rkas->quantity,
                'unit' => $rkas->satuan,
                'price' => $rkas->tarif,
                'amount' => $amount,
            ]);
        }

        $rab->update(['total_amount' => $totalAmount]);

        Alert::success('Berhasil', 'Data RAB berhasil diperbarui.');
        return redirect()->route('rab.index');
    }

    public function show(Rab $rab)
    {
        $rab->load(['academicYear', 'creator', 'checker', 'approver', 'headmaster', 'details.rkas']);
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
}
