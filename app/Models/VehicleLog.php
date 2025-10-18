<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'departure_time',
        'return_time',
        'destination',
        'purpose',
        'start_odometer',
        'end_odometer',
        'condition_on_checkout',
        'condition_on_checkin',
        'notes',
        'checkout_doc_number',
        'checkin_doc_number',
    ];

    // Cast tanggal agar menjadi objek Carbon
    protected $casts = [
        'departure_time' => 'datetime',
        'return_time' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
