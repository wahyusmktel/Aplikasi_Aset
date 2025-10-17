<?php

namespace App\Imports;

use App\Models\PersonInCharge;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PersonsInChargeImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestPerson = PersonInCharge::orderBy('code', 'desc')->first();
        $newNumber = $latestPerson ? intval($latestPerson->code) + 1 : 1;
        $newCode = sprintf('%03d', $newNumber);

        return new PersonInCharge([
            'name' => $row['nama_penanggung_jawab'],
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
            'nama_penanggung_jawab' => 'required|string|max:255|unique:persons_in_charge,name',
        ];
    }
}
