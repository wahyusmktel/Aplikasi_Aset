<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    protected $fillable = [
        'name',
        'academic_year_id',
        'mta',
        'nama_akun',
        'drk',
        'kebutuhan_waktu',
        'total_amount',
        'created_by_id',
        'checked_by_id',
        'approved_by_id',
        'headmaster_id',
        'notes'
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by_id');
    }

    public function checker()
    {
        return $this->belongsTo(Employee::class, 'checked_by_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by_id');
    }

    public function headmaster()
    {
        return $this->belongsTo(Employee::class, 'headmaster_id');
    }

    public function details()
    {
        return $this->hasMany(RabDetail::class);
    }
}
