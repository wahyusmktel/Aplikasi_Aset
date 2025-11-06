<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    // Pastikan $fillable/guarded diatur jika Anda menggunakan Mass Assignment
    protected $fillable = [
        'asset_id',
        'assigned_to_user_id',
        'title',
        'description',
        'maintenance_type',
        'schedule_date',
        'completed_at',
        'status',
        'notes'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
