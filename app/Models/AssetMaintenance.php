<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_number',
        'asset_id',
        'maintenance_date',
        'type',
        'description',
        'cost',
        'technician',
    ];

    // Definisikan relasi ke model Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
