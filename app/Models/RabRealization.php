<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabRealization extends Model
{
    use HasFactory;

    protected $fillable = [
        'rab_id',
        'total_penerimaan',
        'total_pengeluaran',
        'final_balance',
    ];

    public function rab()
    {
        return $this->belongsTo(Rab::class);
    }

    public function details()
    {
        return $this->hasMany(RabRealizationDetail::class);
    }
}
