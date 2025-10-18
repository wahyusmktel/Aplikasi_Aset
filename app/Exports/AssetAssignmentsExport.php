<?php

namespace App\Exports;

use App\Models\AssetAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AssetAssignmentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return AssetAssignment::with(['asset', 'employee'])
            ->when($this->search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('checkout_doc_number', 'like', "%{$search}%")
                    ->orWhere('return_doc_number', 'like', "%{$search}%");
            })
            ->latest('assigned_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Aset',
            'Kode Aset YPT',
            'Nama Pegawai',
            'Tgl Pinjam',
            'Kondisi Pinjam',
            'No Surat Pinjam',
            'Tgl Kembali',
            'Kondisi Kembali',
            'No Surat Kembali',
            'Catatan',
        ];
    }

    public function map($assignment): array
    {
        return [
            $assignment->asset->name ?? '-',
            $assignment->asset->asset_code_ypt ?? '-',
            $assignment->employee->name ?? '-',
            Carbon::parse($assignment->assigned_date)->isoFormat('D MMM YYYY'),
            $assignment->condition_on_assign,
            $assignment->checkout_doc_number ?? '-',
            $assignment->returned_date ? Carbon::parse($assignment->returned_date)->isoFormat('D MMM YYYY') : 'Masih Dipinjam',
            $assignment->condition_on_return ?? '-',
            $assignment->return_doc_number ?? '-',
            $assignment->notes ?? '-',
        ];
    }
}
