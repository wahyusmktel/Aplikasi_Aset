<?php

namespace App\Imports;

use App\Models\Faculty;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FacultiesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestFaculty = Faculty::orderBy('code', 'desc')->first();
        $newNumber = $latestFaculty ? intval($latestFaculty->code) + 1 : 1;
        $newCode = sprintf('%02d', $newNumber);

        return new Faculty([
            'name' => $row['nama_fakultas'],
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
            'nama_fakultas' => 'required|string|max:255|unique:faculties,name',
        ];
    }
}
