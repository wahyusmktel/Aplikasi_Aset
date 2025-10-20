<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ActiveAssetsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil semua aset yang belum di-dispose
        return Asset::whereNull('disposal_date')
            ->with(['category', 'institution', 'building', 'room', 'personInCharge'])
            ->orderBy('asset_code_ypt', 'asc') // Urutkan berdasarkan kode
            ->get();
    }

    public function headings(): array
    {
        // Definisikan header kolom Excel
        return [
            'Kode Aset YPT',
            'Nama Barang',
            'Tahun Beli',
            'Harga Beli (Rp)',
            'Nilai Buku (Rp)',
            'Masa Manfaat (Thn)',
            'Status Saat Ini',
            'Kategori',
            'Lokasi (Gedung/Ruangan)',
            'Penanggung Jawab',
        ];
    }

    public function map($asset): array
    {
        // Petakan data aset ke kolom Excel
        return [
            $asset->asset_code_ypt ?? '-',
            $asset->name ?? '-',
            $asset->purchase_year ?? '-',
            $asset->purchase_cost ?? 0,
            $asset->book_value ?? 0, // Ambil nilai buku dari accessor
            $asset->useful_life ?? '-',
            $asset->current_status ?? '-',
            $asset->category->name ?? '-',
            ($asset->building->name ?? '') . ' / ' . ($asset->room->name ?? ''),
            $asset->personInCharge->name ?? '-',
        ];
    }
}
