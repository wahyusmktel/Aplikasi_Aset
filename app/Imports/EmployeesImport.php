<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Employee([
            'name'      => $row['nama_pegawai'],
            'nip'       => (string) $row['nip'],
            'position'  => $row['jabatan'],
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
            'nama_pegawai' => 'required|string|max:255',
            // Pastikan baris ini persis seperti di bawah
            'nip'          => ['nullable', 'string', 'max:255', 'unique:employees,nip'],
            'jabatan'      => 'required|string|max:255',
        ];
    }
}
