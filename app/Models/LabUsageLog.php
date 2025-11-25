<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'checkin_doc_number',
        'checkout_doc_number',
        'teacher_id',
        'class_group',
        'usage_date',
        'check_in_time',
        'check_out_time',
        'activity_description',
        'condition_before',
        'condition_after',
        'notes'
    ];

    protected $casts = [
        'usage_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }
}
