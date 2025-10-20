<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Untuk lebar kolom otomatis
use Carbon\Carbon;

class ActiveAssetsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $categoryId;

    public function __construct($categoryId = null)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil aset aktif, filter berdasarkan kategori jika ada
        $query = Asset::whereNull('disposal_date')
            ->with([ // Load semua relasi yang dibutuhkan
                'institution',
                'building',
                'room',
                'department',
                'personInCharge',
                'assetFunction',
                'fundingSource'
            ]);

        // Terapkan filter kategori jika dipilih (dan bukan 'all')
        if ($this->categoryId && $this->categoryId !== 'all') {
            $query->where('category_id', $this->categoryId);
        }

        return $query->orderBy('asset_code_ypt', 'asc')->get();
    }

    public function headings(): array
    {
        // Sesuaikan header kolom
        return [
            // 'No', // Nomor urut biasanya lebih baik ditambahkan manual setelah export
            'Kode Aset YPT',
            'Nama Barang',
            'Tahun Pengadaan',
            'Nama Gedung',
            'Nama Ruangan',
            'Unit',
            'Penanggung Jawab',
            'Fungsi Barang',
            'Pendanaan',
            'No Urut Barang',
        ];
    }

    public function map($asset): array
    {
        // Petakan data sesuai kolom yang baru
        return [
            $asset->asset_code_ypt ?? '-',
            $asset->name ?? '-',
            $asset->purchase_year ?? '-',
            $asset->building->name ?? '-',
            $asset->room->name ?? '-',
            $asset->department->name ?? '-', // Unit
            $asset->personInCharge->name ?? '-', // Penanggung Jawab
            $asset->assetFunction->name ?? '-', // Fungsi Barang
            $asset->fundingSource->name ?? '-', // Pendanaan
            $asset->sequence_number ?? '-', // No Urut Barang
        ];
    }
}
