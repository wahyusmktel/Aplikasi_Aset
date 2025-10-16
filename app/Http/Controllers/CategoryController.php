<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\CategoriesImport; // Impor class CategoriesImport
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $categories = Category::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Menyimpan data kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // Logika kode otomatis (101, 102, dst, 3 digit)
        $latestCategory = Category::orderBy('code', 'desc')->first();
        $newNumber = $latestCategory ? intval($latestCategory->code) + 1 : 1; // Mulai dari 101 jika kosong
        $newCode = sprintf('%03d', $newNumber);

        Category::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data kategori berhasil ditambahkan.');
        return redirect()->route('categories.index');
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->only('name'));

        alert()->success('Berhasil!', 'Data kategori berhasil diperbarui.');
        return redirect()->route('categories.index');
    }

    /**
     * Menghapus data kategori.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        alert()->success('Berhasil!', 'Data kategori berhasil dihapus.');
        return redirect()->route('categories.index');
    }

    /**
     * Menangani proses impor data kategori dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new CategoriesImport, $request->file('file'));
            alert()->success('Berhasil!', 'Data kategori berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('categories.index');
    }
}
