<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDamageReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'report_doc_number',
        'description',
        'reported_condition',
        'image_path',
        'status',
        'admin_note',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
