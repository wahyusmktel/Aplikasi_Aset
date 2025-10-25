<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAudit;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(Asset $asset, string $action, array $before = null, array $after = null): void
    {
        AssetAudit::create([
            'asset_id'   => $asset->id,
            'action'     => $action,
            'actor_id'   => Auth::id(),
            'actor_name' => optional(Auth::user())->name,
            'ip_address' => request()?->ip(),
            'before'     => $before,
            'after'      => $after,
        ]);
    }
}
