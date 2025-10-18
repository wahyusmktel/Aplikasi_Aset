<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'position',
    ];

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function vehicleLogs()
    {
        return $this->hasMany(VehicleLog::class)->orderBy('departure_time', 'desc');
    }
}
