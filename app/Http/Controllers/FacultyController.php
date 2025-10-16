<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Imports\FacultiesImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class FacultyController extends Controller
{
    /**
     * Menampilkan daftar fakultas.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $faculties = Faculty::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('faculties.index', compact('faculties'));
    }

    /**
     * Menyimpan data fakultas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name',
        ]);

        // Logika kode otomatis (01, 02, dst.)
        $latestFaculty = Faculty::orderBy('code', 'desc')->first();
        $newNumber = $latestFaculty ? intval($latestFaculty->code) + 1 : 1;
        // Format kode baru menjadi 2 digit
        $newCode = sprintf('%02d', $newNumber);

        Faculty::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data fakultas berhasil ditambahkan.');
        return redirect()->route('faculties.index');
    }

    /**
     * Memperbarui data fakultas.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name,' . $faculty->id,
        ]);

        $faculty->update($request->only('name'));

        alert()->success('Berhasil!', 'Data fakultas berhasil diperbarui.');
        return redirect()->route('faculties.index');
    }

    /**
     * Menghapus data fakultas.
     */
    public function destroy(Faculty $faculty)
    {
        $faculty->delete();

        alert()->success('Berhasil!', 'Data fakultas berhasil dihapus.');
        return redirect()->route('faculties.index');
    }

    /**
     * Menangani proses impor data fakultas dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new FacultiesImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data fakultas berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('faculties.index');
    }
}
