<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use App\Imports\InstitutionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class InstitutionController extends Controller
{
    /**
     * Menampilkan daftar lembaga.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $institutions = Institution::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('institutions.index', compact('institutions'));
    }

    /**
     * Menyimpan data lembaga baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name',
        ]);

        // Logika kode otomatis (01, 02, dst.)
        $latestInstitution = Institution::orderBy('code', 'desc')->first();
        $newNumber = $latestInstitution ? intval($latestInstitution->code) + 1 : 1;
        // Format kode baru menjadi 2 digit
        $newCode = sprintf('%02d', $newNumber);

        Institution::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data lembaga berhasil ditambahkan.');
        return redirect()->route('institutions.index');
    }

    /**
     * Memperbarui data lembaga.
     */
    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name,' . $institution->id,
        ]);

        $institution->update($request->only('name'));

        alert()->success('Berhasil!', 'Data lembaga berhasil diperbarui.');
        return redirect()->route('institutions.index');
    }

    /**
     * Menghapus data lembaga.
     */
    public function destroy(Institution $institution)
    {
        $institution->delete();

        alert()->success('Berhasil!', 'Data lembaga berhasil dihapus.');
        return redirect()->route('institutions.index');
    }

    /**
     * Menangani proses impor data dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new InstitutionsImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data lembaga berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('institutions.index');
    }
}
