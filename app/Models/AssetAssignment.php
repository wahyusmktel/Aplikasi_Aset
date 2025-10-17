<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'checkout_doc_number',
        'return_doc_number',
        'asset_id',
        'employee_id',
        'assigned_date',
        'returned_date',
        'condition_on_assign',
        'condition_on_return',
        'notes',
    ];

    /**
     * Get the asset associated with the assignment.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the employee associated with the assignment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
