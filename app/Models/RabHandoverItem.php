<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabHandoverItem extends Model
{
    protected $fillable = [
        'rab_handover_id',
        'uraian',
        'qty',
        'spesifikasi',
        'keterangan',
    ];

    public function handover()
    {
        return $this->belongsTo(RabHandover::class, 'rab_handover_id');
    }
}
