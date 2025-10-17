<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DepartmentsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestDepartment = Department::orderBy('code', 'desc')->first();
        $newNumber = $latestDepartment ? intval($latestDepartment->code) + 1 : 1;
        $newCode = sprintf('%03d', $newNumber);

        return new Department([
            'name' => $row['nama_prodi'],
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
            'nama_prodi' => 'required|string|max:255|unique:departments,name',
        ];
    }
}
