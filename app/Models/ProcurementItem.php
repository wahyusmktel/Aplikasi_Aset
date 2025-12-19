<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_id', 'name', 'category_id', 'institution_id', 
        'quantity', 'received_quantity', 'unit_price', 'total_price', 
        'specs', 'is_converted_to_asset'
    ];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
