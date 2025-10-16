<?php

namespace App\Imports;

use App\Models\Room;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RoomsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Logika kode otomatis (sama seperti di controller)
        $latestRoom = Room::orderBy('code', 'desc')->first();
        $newNumber = $latestRoom ? intval($latestRoom->code) + 1 : 1;
        $newCode = sprintf('%04d', $newNumber);

        return new Room([
            'name' => $row['nama_ruangan'], // 'nama_ruangan' harus cocok dengan header di file Excel
            'code' => $newCode,
        ]);
    }

    /**
     * Terapkan validasi untuk setiap baris di Excel.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_ruangan' => 'required|string|max:255|unique:rooms,name',
        ];
    }
}
