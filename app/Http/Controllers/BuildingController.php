<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Menampilkan daftar gedung.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $buildings = Building::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('buildings.index', compact('buildings'));
    }

    /**
     * Menyimpan data gedung baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name',
        ]);

        $latestBuilding = Building::orderBy('code', 'desc')->first();
        $newNumber = $latestBuilding ? intval($latestBuilding->code) + 1 : 1;
        $newCode = sprintf('%03d', $newNumber);

        Building::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        // --- PERUBAHAN DI SINI ---
        alert()->success('Berhasil!', 'Data gedung berhasil ditambahkan.');

        return redirect()->route('buildings.index');
    }

    /**
     * Memperbarui data gedung.
     */
    public function update(Request $request, Building $building)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name,' . $building->id,
        ]);

        $building->update($request->only('name'));

        // --- PERUBAHAN DI SINI ---
        alert()->success('Berhasil!', 'Data gedung berhasil diperbarui.');

        return redirect()->route('buildings.index');
    }

    /**
     * Menghapus data gedung.
     */
    public function destroy(Building $building)
    {
        $building->delete();

        // --- PERUBAHAN DI SINI ---
        alert()->success('Berhasil!', 'Data gedung berhasil dihapus.');

        return redirect()->route('buildings.index');
    }
}
