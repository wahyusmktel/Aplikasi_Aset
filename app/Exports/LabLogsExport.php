<?php

namespace App\Exports;

use App\Models\LabUsageLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class LabLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = LabUsageLog::with(['room', 'teacher']);

        // Filter berdasarkan Ruangan
        if (!empty($this->filters['room_id'])) {
            $query->where('room_id', $this->filters['room_id']);
        }

        // Filter Tanggal Mulai
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('usage_date', '>=', $this->filters['start_date']);
        }

        // Filter Tanggal Akhir
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('usage_date', '<=', $this->filters['end_date']);
        }

        return $query->latest('usage_date')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Ruangan',
            'Guru / PJ',
            'Kelas',
            'Materi / Kegiatan',
            'Jam Masuk',
            'Jam Keluar',
            'Durasi (Jam)',
            'Kondisi Awal',
            'Kondisi Akhir',
            'Catatan',
        ];
    }

    public function map($log): array
    {
        $duration = '-';
        if ($log->check_out_time) {
            $diff = $log->check_in_time->diffInMinutes($log->check_out_time);
            $hours = floor($diff / 60);
            $minutes = $diff % 60;
            $duration = "{$hours}j {$minutes}m";
        }

        return [
            Carbon::parse($log->usage_date)->isoFormat('D MMM YYYY'),
            $log->room->name ?? '-',
            $log->teacher->name ?? '-',
            $log->class_group,
            $log->activity_description,
            $log->check_in_time->format('H:i'),
            $log->check_out_time ? $log->check_out_time->format('H:i') : 'Belum Selesai',
            $duration,
            $log->condition_before,
            $log->condition_after ?? '-',
            $log->notes ?? '-',
        ];
    }
}
