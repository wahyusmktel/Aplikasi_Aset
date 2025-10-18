<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class DisposedAssetsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Asset::whereNotNull('disposal_date')
            ->with(['category', 'institution']) // Load relasi yang relevan
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code_ypt', 'like', "%{$search}%")
                    ->orWhere('disposal_method', 'like', "%{$search}%")
                    ->orWhere('disposal_reason', 'like', "%{$search}%");
            })
            ->latest('disposal_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Hapus',
            'Kode Aset YPT',
            'Nama Aset',
            'Tahun Beli',
            'Metode Hapus',
            'Alasan',
            'Nilai Jual (Rp)',
            'No. BAPh',
        ];
    }

    public function map($asset): array
    {
        return [
            Carbon::parse($asset->disposal_date)->isoFormat('D MMM YYYY'),
            $asset->asset_code_ypt ?? '-',
            $asset->name ?? '-',
            $asset->purchase_year ?? '-',
            $asset->disposal_method ?? '-',
            $asset->disposal_reason ?? '-',
            ($asset->disposal_method == 'Dijual') ? ($asset->disposal_value ?? 0) : '-',
            $asset->disposal_doc_number ?? '-',
        ];
    }
}
