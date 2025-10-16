<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoriesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $latestCategory = Category::orderBy('code', 'desc')->first();
        $newNumber = $latestCategory ? intval($latestCategory->code) + 1 : 1;
        $newCode = sprintf('%03d', $newNumber);

        return new Category([
            'name' => $row['nama_kategori'],
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
            'nama_kategori' => 'required|string|max:255|unique:categories,name',
        ];
    }
}
