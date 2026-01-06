<?php

namespace App\Exports;

use App\Models\Rkas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RkasTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Return a single sample row for the template
        return collect([
            [
                'kode_lokasi' => '01.01.01',
                'struktur_pp' => 'Pendidikan',
                'kode_pp' => 'PP01',
                'nama_pp' => 'Pengembangan Kurikulum',
                'kode_rkm' => 'RKM01',
                'kode_drk' => 'DRK01',
                'nama_drk' => 'Rapat Kerja Guru',
                'mta' => '5.1.01.01',
                'nama_akun' => 'Belanja ATK',
                'rincian_kegiatan' => 'Konsumsi Rapat Kerja',
                'satuan' => 'Pax',
                'tarif' => 50000,
                'quantity' => 10,
                'bulan' => 'Juli',
                'sumber_anggaran' => 'BOS',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'kode_lokasi',
            'struktur_pp',
            'kode_pp',
            'nama_pp',
            'kode_rkm',
            'kode_drk',
            'nama_drk',
            'mta',
            'nama_akun',
            'rincian_kegiatan',
            'satuan',
            'tarif',
            'quantity',
            'bulan',
            'sumber_anggaran',
        ];
    }

    public function map($row): array
    {
        return [
            $row['kode_lokasi'],
            $row['struktur_pp'],
            $row['kode_pp'],
            $row['nama_pp'],
            $row['kode_rkm'],
            $row['kode_drk'],
            $row['nama_drk'],
            $row['mta'],
            $row['nama_akun'],
            $row['rincian_kegiatan'],
            $row['satuan'],
            $row['tarif'],
            $row['quantity'],
            $row['bulan'],
            $row['sumber_anggaran'],
        ];
    }
}
