<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAudit extends Model
{
    protected $fillable = [
        'asset_id',
        'action',
        'actor_id',
        'actor_name',
        'ip_address',
        'before',
        'after'
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
