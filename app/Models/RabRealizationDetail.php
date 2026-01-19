<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabRealizationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'rab_realization_id',
        'tgl',
        'uraian',
        'penerimaan',
        'pengeluaran',
        'keterangan',
    ];

    public function realization()
    {
        return $this->belongsTo(RabRealization::class, 'rab_realization_id');
    }
}
