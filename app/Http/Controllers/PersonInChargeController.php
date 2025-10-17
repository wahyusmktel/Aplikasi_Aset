<?php

namespace App\Http\Controllers;

use App\Models\PersonInCharge;
use Illuminate\Http\Request;
use App\Imports\PersonsInChargeImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PersonInChargeController extends Controller
{
    /**
     * Menampilkan daftar penanggung jawab.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $personsInCharge = PersonInCharge::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('persons-in-charge.index', compact('personsInCharge'));
    }

    /**
     * Menyimpan data penanggung jawab baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:persons_in_charge,name',
        ]);

        // Logika kode otomatis (001, 002, dst.)
        $latestPerson = PersonInCharge::orderBy('code', 'desc')->first();
        $newNumber = $latestPerson ? intval($latestPerson->code) + 1 : 1;
        $newCode = sprintf('%03d', $newNumber);

        PersonInCharge::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data penanggung jawab berhasil ditambahkan.');
        return redirect()->route('persons-in-charge.index');
    }

    /**
     * Memperbarui data penanggung jawab.
     */
    public function update(Request $request, PersonInCharge $personInCharge)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:persons_in_charge,name,' . $personInCharge->id,
        ]);

        $personInCharge->update($request->only('name'));

        alert()->success('Berhasil!', 'Data penanggung jawab berhasil diperbarui.');
        return redirect()->route('persons-in-charge.index');
    }

    /**
     * Menghapus data penanggung jawab.
     */
    public function destroy(PersonInCharge $personInCharge)
    {
        $personInCharge->delete();

        alert()->success('Berhasil!', 'Data penanggung jawab berhasil dihapus.');
        return redirect()->route('persons-in-charge.index');
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
            Excel::import(new PersonsInChargeImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data penanggung jawab berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('persons-in-charge.index');
    }
}
