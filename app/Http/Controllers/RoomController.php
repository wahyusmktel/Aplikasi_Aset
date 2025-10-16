<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Imports\RoomsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Menampilkan daftar ruangan.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $rooms = Room::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('code', 'asc') // Urutkan berdasarkan kode
            ->paginate(10);

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Menyimpan data ruangan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
        ]);

        // Logika kode otomatis (0001, 0002, dst.)
        $latestRoom = Room::orderBy('code', 'desc')->first();
        $newNumber = $latestRoom ? intval($latestRoom->code) + 1 : 1;
        // Format kode baru menjadi 4 digit
        $newCode = sprintf('%04d', $newNumber);

        Room::create([
            'name' => $request->name,
            'code' => $newCode,
        ]);

        alert()->success('Berhasil!', 'Data ruangan berhasil ditambahkan.');
        return redirect()->route('rooms.index');
    }

    /**
     * Memperbarui data ruangan.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
        ]);

        $room->update($request->only('name'));

        alert()->success('Berhasil!', 'Data ruangan berhasil diperbarui.');
        return redirect()->route('rooms.index');
    }

    /**
     * Menghapus data ruangan.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        alert()->success('Berhasil!', 'Data ruangan berhasil dihapus.');
        return redirect()->route('rooms.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new RoomsImport, $request->file('file'));

            alert()->success('Berhasil!', 'Data ruangan berhasil diimpor.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }

            alert()->error('Impor Gagal!', 'Terdapat kesalahan pada data: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->route('rooms.index');
    }
}
