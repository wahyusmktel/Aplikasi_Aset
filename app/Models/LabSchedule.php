<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'teacher_id',
        'subject',
        'class_group',
        'day_of_week',
        'start_time',
        'end_time',
        'semester',
        'academic_year'
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
