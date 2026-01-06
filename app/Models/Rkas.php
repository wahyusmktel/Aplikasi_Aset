<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rkas extends Model
{
    protected $fillable = [
        'academic_year_id',
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

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
