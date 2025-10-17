<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255|unique:employees,nip',
            'position' => 'required|string|max:255',
        ]);
        Employee::create($request->all());
        alert()->success('Berhasil!', 'Data pegawai berhasil ditambahkan.');
        return redirect()->route('employees.index');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255|unique:employees,nip,' . $employee->id,
            'position' => 'required|string|max:255',
        ]);
        $employee->update($request->all());
        alert()->success('Berhasil!', 'Data pegawai berhasil diperbarui.');
        return redirect()->route('employees.index');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        alert()->success('Berhasil!', 'Data pegawai berhasil dihapus.');
        return redirect()->route('employees.index');
    }
    
    /**
     * Menangani proses impor data pegawai dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new EmployeesImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data pegawai berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('employees.index');
    }
}
