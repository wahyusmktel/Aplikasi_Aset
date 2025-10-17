<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Imports\DepartmentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DepartmentController extends Controller
{
    /**
     * Menampilkan daftar prodi/unit.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $departments = Department::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('departments.index', compact('departments'));
    }

    /**
     * Menyimpan data prodi/unit baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        // Logika kode otomatis (001, 002, dst.)
        $latestDepartment = Department::orderBy('code', 'desc')->first();
        $newNumber = $latestDepartment ? intval($latestDepartment->code) + 1 : 1;
        $newCode = sprintf('%03d', $newNumber);

        Department::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data prodi/unit berhasil ditambahkan.');
        return redirect()->route('departments.index');
    }

    /**
     * Memperbarui data prodi/unit.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update($request->only('name'));

        alert()->success('Berhasil!', 'Data prodi/unit berhasil diperbarui.');
        return redirect()->route('departments.index');
    }

    /**
     * Menghapus data prodi/unit.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        alert()->success('Berhasil!', 'Data prodi/unit berhasil dihapus.');
        return redirect()->route('departments.index');
    }

    /**
     * Menangani proses impor data prodi/unit dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new DepartmentsImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data prodi/unit berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('departments.index');
    }
}
