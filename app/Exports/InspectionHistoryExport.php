<?php

namespace App\Exports;

use App\Models\AssetInspection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class InspectionHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return AssetInspection::with(['asset', 'inspector'])
            ->when($this->search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('condition', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('inspector', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest('inspection_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Aset',
            'Kode Aset YPT',
            'Kondisi',
            'Catatan',
            'Diperiksa Oleh',
        ];
    }

    public function map($inspection): array
    {
        return [
            Carbon::parse($inspection->inspection_date)->isoFormat('D MMM YYYY'),
            $inspection->asset->name ?? '-',
            $inspection->asset->asset_code_ypt ?? '-',
            $inspection->condition,
            $inspection->notes ?? '-',
            $inspection->inspector->name ?? 'Sistem',
        ];
    }
}
