<?php

namespace App\Imports;

use App\Models\FundingSource;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FundingSourcesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestSource = FundingSource::orderBy('code', 'desc')->first();
        $newNumber = $latestSource ? intval($latestSource->code) + 1 : 1;

        return new FundingSource([
            'name' => $row['nama_jenis_pendanaan'],
            'code' => $newNumber,
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
            'nama_jenis_pendanaan' => 'required|string|max:255|unique:funding_sources,name',
        ];
    }
}
