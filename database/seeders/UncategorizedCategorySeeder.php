<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class UncategorizedCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Cek dulu apakah sudah ada
        $uncategorized = Category::where('name', 'Belum Ditentukan')->first();

        if (!$uncategorized) {
            // Logika kode otomatis (999, agar selalu di akhir jika diurutkan)
            $latestCategory = Category::orderBy('code', 'desc')->first();
            $newCode = $latestCategory ? intval($latestCategory->code) + 1 : 1;

            // Jika kita mau kode khusus untuk ini, misal '000' atau '999'
            $newCode = '999'; // Kita paksakan 999

            Category::create([
                'name' => 'Belum Ditentukan',
                'code' => $newCode,
            ]);
        }
    }
}
