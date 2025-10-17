<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $bookCategory = Category::where('name', 'Buku')->first();
        $query = Asset::where('category_id', $bookCategory ? $bookCategory->id : -1)
            ->with(['institution', 'building', 'room']);

        if ($this->ids) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Kode Aset YPT',
            'Nama Barang',
            'Tahun Pembelian',
            'Status',
            'Lembaga',
            'Lokasi',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_code_ypt,
            $asset->name,
            $asset->purchase_year,
            $asset->status,
            $asset->institution->name,
            $asset->building->name . ' / ' . $asset->room->name,
        ];
    }
}
