<?php

namespace App\Exports;

use App\Models\AssetMaintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class MaintenanceHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return AssetMaintenance::with(['asset'])
            ->when($this->search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('technician', 'like', "%{$search}%");
            })
            ->latest('maintenance_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Aset',
            'Kode Aset YPT',
            'Jenis Pekerjaan',
            'Deskripsi',
            'Biaya (Rp)',
            'Teknisi/Vendor',
        ];
    }

    public function map($maintenance): array
    {
        return [
            Carbon::parse($maintenance->maintenance_date)->isoFormat('D MMM YYYY'),
            $maintenance->asset->name ?? '-',
            $maintenance->asset->asset_code_ypt ?? '-',
            $maintenance->type,
            $maintenance->description,
            $maintenance->cost ?? 0, // Tampilkan 0 jika null
            $maintenance->technician ?? '-',
        ];
    }
}
