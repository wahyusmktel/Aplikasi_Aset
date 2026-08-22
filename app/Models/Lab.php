<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room_id',
        'department_id',
        'person_in_charge_id',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function personInCharge(): BelongsTo
    {
        return $this->belongsTo(PersonInCharge::class);
    }

    public function labAssets(): HasMany
    {
        return $this->hasMany(LabAsset::class);
    }
}
