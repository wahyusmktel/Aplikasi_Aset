<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_id',
        'asset_id',
        'quantity',
        'specifications', // JSON field storing specs like processor, ram, etc.
    ];

    protected $casts = [
        'specifications' => 'array',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
