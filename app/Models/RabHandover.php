<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabHandover extends Model
{
    protected $fillable = [
        'rab_id',
        'department_id',
        'document_number',
        'handover_date',
        'handed_by',
        'handed_by_jabatan',
        'received_by',
        'received_by_jabatan',
        'notes',
    ];

    protected $casts = [
        'handover_date' => 'date',
    ];

    public function rab()
    {
        return $this->belongsTo(Rab::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function items()
    {
        return $this->hasMany(RabHandoverItem::class);
    }
}
