<?php

namespace App\Http\Controllers;

use App\Models\AssetFunction;
use Illuminate\Http\Request;
use App\Imports\AssetFunctionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class AssetFunctionController extends Controller
{
    /**
     * Menampilkan daftar fungsi barang.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assetFunctions = AssetFunction::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('asset-functions.index', compact('assetFunctions'));
    }

    /**
     * Menyimpan data fungsi barang baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_functions,name',
        ]);

        // Logika kode otomatis (01, 02, dst.)
        $latestFunction = AssetFunction::orderBy('code', 'desc')->first();
        $newNumber = $latestFunction ? intval($latestFunction->code) + 1 : 1;
        $newCode = sprintf('%02d', $newNumber);

        AssetFunction::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data fungsi barang berhasil ditambahkan.');
        return redirect()->route('asset-functions.index');
    }

    /**
     * Memperbarui data fungsi barang.
     */
    public function update(Request $request, AssetFunction $assetFunction)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_functions,name,' . $assetFunction->id,
        ]);

        $assetFunction->update($request->only('name'));

        alert()->success('Berhasil!', 'Data fungsi barang berhasil diperbarui.');
        return redirect()->route('asset-functions.index');
    }

    /**
     * Menghapus data fungsi barang.
     */
    public function destroy(AssetFunction $assetFunction)
    {
        $assetFunction->delete();

        alert()->success('Berhasil!', 'Data fungsi barang berhasil dihapus.');
        return redirect()->route('asset-functions.index');
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
            Excel::import(new AssetFunctionsImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data fungsi barang berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('asset-functions.index');
    }
}
