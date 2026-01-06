<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabDetail extends Model
{
    protected $fillable = [
        'rab_id',
        'rkas_id',
        'alias_name',
        'specification',
        'quantity',
        'unit',
        'price',
        'amount'
    ];

    public function rab()
    {
        return $this->belongsTo(Rab::class);
    }

    public function rkas()
    {
        return $this->belongsTo(Rkas::class);
    }
}
