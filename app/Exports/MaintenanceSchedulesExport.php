<?php

namespace App\Exports;

use App\Models\MaintenanceSchedule;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceSchedulesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    // 1. Terima filter dari Controller
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    // 2. Buat query berdasarkan filter
    public function query()
    {
        $query = MaintenanceSchedule::query()->with(['asset', 'assignedTo']);

        // Terapkan filter (logika yang SAMA seperti di controller index)
        $query->when($this->filters['status'] ?? null, function ($q, $status) {
            return $q->where('status', $status);
        });
        $query->when($this->filters['date_from'] ?? null, function ($q, $date_from) {
            return $q->whereDate('schedule_date', '>=', $date_from);
        });
        $query->when($this->filters['date_to'] ?? null, function ($q, $date_to) {
            return $q->whereDate('schedule_date', '<=', $date_to);
        });

        return $query->latest();
    }

    // 3. Definisikan header kolom
    public function headings(): array
    {
        return [
            'ID',
            'Aset',
            'Kode Aset',
            'Judul Pekerjaan',
            'Tipe',
            'Tanggal Jadwal',
            'Tanggal Selesai',
            'Status',
            'Teknisi',
            'Catatan Teknisi',
        ];
    }

    // 4. Mapping data: Ubah objek Model jadi array
    public function map($schedule): array
    {
        return [
            $schedule->id,
            $schedule->asset->name ?? 'N/A',
            $schedule->asset->asset_code_ypt ?? 'N/A',
            $schedule->title,
            ucfirst($schedule->maintenance_type),
            Carbon::parse($schedule->schedule_date)->format('d-m-Y'),
            $schedule->completed_at ? Carbon::parse($schedule->completed_at)->format('d-m-Y') : '-',
            ucfirst($schedule->status),
            $schedule->assignedTo->name ?? 'Belum Ditugaskan',
            $schedule->notes,
        ];
    }
}
