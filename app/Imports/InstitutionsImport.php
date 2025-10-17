<?php

namespace App\Imports;

use App\Models\Institution;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InstitutionsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestInstitution = Institution::orderBy('code', 'desc')->first();
        $newNumber = $latestInstitution ? intval($latestInstitution->code) + 1 : 1;
        $newCode = sprintf('%02d', $newNumber);

        return new Institution([
            'name' => $row['nama_lembaga'],
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
            'nama_lembaga' => 'required|string|max:255|unique:institutions,name',
        ];
    }
}
