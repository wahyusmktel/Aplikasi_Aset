<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementHandover extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_id', 'type', 'document_number', 'handover_date', 
        'from_name', 'from_user_id', 'to_name', 'to_user_id', 
        'to_department_id', 'to_person_in_charge_id', 'notes'
    ];

    protected $casts = [
        'handover_date' => 'date',
    ];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function toPersonInCharge()
    {
        return $this->belongsTo(PersonInCharge::class, 'to_person_in_charge_id');
    }
}
