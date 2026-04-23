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
        'user_id',
        'borrower_name',
        'borrower_nip',
        'departure_time',
        'estimated_return_time',
        'return_time',
        'destination',
        'purpose',
        'driver_type',
        'driver_employee_id',
        'start_odometer',
        'start_latitude',
        'start_longitude',
        'end_odometer',
        'fuel_level_start',
        'fuel_level_end',
        'condition_on_checkout',
        'condition_on_checkin',
        'notes',
        'return_photos',
        'checkout_doc_number',
        'checkin_doc_number',
        'status',
        'waka_approved_at',
        'kepsek_approved_at',
    ];

    protected $casts = [
        'departure_time'         => 'datetime',
        'estimated_return_time'  => 'datetime',
        'return_time'            => 'datetime',
        'waka_approved_at'       => 'datetime',
        'kepsek_approved_at'     => 'datetime',
        'return_photos'          => 'array',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function driverEmployee()
    {
        return $this->belongsTo(Employee::class, 'driver_employee_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
