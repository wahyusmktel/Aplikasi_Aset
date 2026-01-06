<?php

namespace App\Imports;

use App\Models\Rkas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RkasImport implements ToModel, WithHeadingRow
{
    protected $academicYearId;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    public function model(array $row)
    {
        // Skip if empty row
        if (!isset($row['kode_lokasi']) && !isset($row['nama_akun'])) {
            return null;
        }

        return new Rkas([
            'academic_year_id' => $this->academicYearId,
            'kode_lokasi'      => $row['kode_lokasi'] ?? null,
            'struktur_pp'      => $row['struktur_pp'] ?? null,
            'kode_pp'          => $row['kode_pp'] ?? null,
            'nama_pp'          => $row['nama_pp'] ?? null,
            'kode_rkm'         => $row['kode_rkm'] ?? null,
            'kode_drk'         => $row['kode_drk'] ?? null,
            'nama_drk'         => $row['nama_drk'] ?? null,
            'mta'              => $row['mta'] ?? null,
            'nama_akun'        => $row['nama_akun'] ?? null,
            'rincian_kegiatan' => $row['rincian_kegiatan'] ?? null,
            'satuan'           => $row['satuan'] ?? null,
            'tarif'            => $row['tarif'] ?? 0,
            'quantity'         => $row['quantity'] ?? 0,
            'bulan'            => $row['bulan'] ?? null,
            'sumber_anggaran'  => $row['sumber_anggaran'] ?? null,
        ]);
    }
}
