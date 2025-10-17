<?php

namespace App\Http\Controllers;

use App\Models\FundingSource;
use Illuminate\Http\Request;
use App\Imports\FundingSourcesImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class FundingSourceController extends Controller
{
    /**
     * Menampilkan daftar jenis pendanaan.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $fundingSources = FundingSource::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('funding-sources.index', compact('fundingSources'));
    }

    /**
     * Menyimpan data jenis pendanaan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:funding_sources,name',
        ]);

        // Logika kode otomatis (1, 2, dst. - satu digit)
        $latestSource = FundingSource::orderBy('code', 'desc')->first();
        $newNumber = $latestSource ? intval($latestSource->code) + 1 : 1;

        FundingSource::create([
            'name' => $request->name,
            'code' => $newNumber,
        ]);

        alert()->success('Berhasil!', 'Data jenis pendanaan berhasil ditambahkan.');
        return redirect()->route('funding-sources.index');
    }

    /**
     * Memperbarui data jenis pendanaan.
     */
    public function update(Request $request, FundingSource $fundingSource)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:funding_sources,name,' . $fundingSource->id,
        ]);

        $fundingSource->update($request->only('name'));

        alert()->success('Berhasil!', 'Data jenis pendanaan berhasil diperbarui.');
        return redirect()->route('funding-sources.index');
    }

    /**
     * Menghapus data jenis pendanaan.
     */
    public function destroy(FundingSource $fundingSource)
    {
        $fundingSource->delete();

        alert()->success('Berhasil!', 'Data jenis pendanaan berhasil dihapus.');
        return redirect()->route('funding-sources.index');
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
            Excel::import(new FundingSourcesImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data jenis pendanaan berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('funding-sources.index');
    }
}
