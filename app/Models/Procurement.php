<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'reference_number', 'procurement_date', 'total_cost', 'status', 'notes'];

    protected $casts = [
        'procurement_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function handovers()
    {
        return $this->hasMany(ProcurementHandover::class);
    }
}
