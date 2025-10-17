<?php

namespace App\Imports;

use App\Models\AssetFunction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetFunctionsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestFunction = AssetFunction::orderBy('code', 'desc')->first();
        $newNumber = $latestFunction ? intval($latestFunction->code) + 1 : 1;
        $newCode = sprintf('%02d', $newNumber);

        return new AssetFunction([
            'name' => $row['nama_fungsi_barang'],
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
            'nama_fungsi_barang' => 'required|string|max:255|unique:asset_functions,name',
        ];
    }
}
