<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Rkas;
use Illuminate\Http\Request;
use App\Imports\RkasImport;
use App\Exports\RkasTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class RkasController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->query('academic_year_id');
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        
        $query = Rkas::with('academicYear');
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $rkas = $query->paginate(20)->withQueryString();
        
        return view('pages.rkas.index', compact('rkas', 'academicYears', 'academicYearId'));
    }

    public function create(Request $request)
    {
        $academicYearId = $request->query('academic_year_id');
        if (!$academicYearId) {
            return redirect()->route('rkas.index')->with('error', 'Pilih tahun pelajaran terlebih dahulu.');
        }

        $academicYear = AcademicYear::findOrFail($academicYearId);
        return view('pages.rkas.create', compact('academicYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'kode_lokasi' => 'nullable|string',
            'struktur_pp' => 'nullable|string',
            'kode_pp' => 'nullable|string',
            'nama_pp' => 'nullable|string',
            'kode_rkm' => 'nullable|string',
            'kode_drk' => 'nullable|string',
            'nama_drk' => 'nullable|string',
            'mta' => 'nullable|string',
            'nama_akun' => 'nullable|string',
            'rincian_kegiatan' => 'nullable|string',
            'satuan' => 'nullable|string',
            'tarif' => 'required|numeric',
            'quantity' => 'required|numeric',
            'bulan' => 'nullable|string',
            'sumber_anggaran' => 'nullable|string',
        ]);

        Rkas::create($validated);

        Alert::success('Berhasil', 'Data RKAS berhasil ditambahkan.');
        return redirect()->route('rkas.index', ['academic_year_id' => $request->academic_year_id]);
    }

    public function edit(Rkas $rka)
    {
        return view('pages.rkas.edit', compact('rka'));
    }

    public function update(Request $request, Rkas $rka)
    {
        $validated = $request->validate([
            'kode_lokasi' => 'nullable|string',
            'struktur_pp' => 'nullable|string',
            'kode_pp' => 'nullable|string',
            'nama_pp' => 'nullable|string',
            'kode_rkm' => 'nullable|string',
            'kode_drk' => 'nullable|string',
            'nama_drk' => 'nullable|string',
            'mta' => 'nullable|string',
            'nama_akun' => 'nullable|string',
            'rincian_kegiatan' => 'nullable|string',
            'satuan' => 'nullable|string',
            'tarif' => 'required|numeric',
            'quantity' => 'required|numeric',
            'bulan' => 'nullable|string',
            'sumber_anggaran' => 'nullable|string',
        ]);

        $rka->update($validated);

        Alert::success('Berhasil', 'Data RKAS berhasil diperbarui.');
        return redirect()->route('rkas.index', ['academic_year_id' => $rka->academic_year_id]);
    }

    public function destroy(Rkas $rka)
    {
        $yearId = $rka->academic_year_id;
        $rka->delete();

        Alert::success('Berhasil', 'Data RKAS berhasil dihapus.');
        return redirect()->route('rkas.index', ['academic_year_id' => $yearId]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new RkasTemplateExport, 'template_rkas.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        try {
            Excel::import(new RkasImport($request->academic_year_id), $request->file('file'));
            Alert::success('Berhasil', 'Data RKAS berhasil diimpor.');
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }

        return redirect()->route('rkas.index', ['academic_year_id' => $request->academic_year_id]);
    }
}
