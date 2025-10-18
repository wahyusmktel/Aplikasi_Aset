<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_doc_number',
        'asset_id',
        'inspection_date',
        'condition',
        'notes',
        'inspector_id',
    ];

    // Relasi ke model Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke model User (yang melakukan inspeksi)
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
