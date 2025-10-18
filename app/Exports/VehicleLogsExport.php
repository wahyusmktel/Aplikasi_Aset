<?php

namespace App\Exports;

use App\Models\VehicleLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class VehicleLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil data log kendaraan dengan relasi asset dan employee
        return VehicleLog::with(['asset', 'employee'])
            // Terapkan filter pencarian jika ada
            ->when($this->search, function ($query, $search) {
                $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%")) // Cari berdasarkan nama aset
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%")) // Cari berdasarkan nama pegawai
                    ->orWhere('destination', 'like', "%{$search}%") // Cari berdasarkan tujuan
                    ->orWhere('purpose', 'like', "%{$search}%"); // Cari berdasarkan keperluan
            })
            // Urutkan berdasarkan waktu berangkat terbaru
            ->latest('departure_time')
            // Ambil semua data yang cocok (tanpa paginasi)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Kendaraan',
            'Pegawai',
            'Tujuan',
            'Keperluan',
            'Waktu Berangkat',
            'KM Awal',
            'Kondisi Awal',
            'Waktu Kembali',
            'KM Akhir',
            'Kondisi Akhir',
            'No Surat',
        ];
    }

    public function map($log): array
    {
        return [
            $log->asset->name ?? '-',
            $log->employee->name ?? '-',
            $log->destination,
            $log->purpose,
            $log->departure_time ? Carbon::parse($log->departure_time)->isoFormat('D MMM YYYY, HH:mm') : '-',
            $log->start_odometer,
            $log->condition_on_checkout,
            $log->return_time ? Carbon::parse($log->return_time)->isoFormat('D MMM YYYY, HH:mm') : 'Belum Kembali',
            $log->end_odometer ?? '-',
            $log->condition_on_checkin ?? '-',
            $log->checkin_doc_number ?? $log->checkout_doc_number ?? '-',
        ];
    }
}
