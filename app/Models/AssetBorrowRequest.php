<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetBorrowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'requester_user_id',
        'requester_name',
        'requester_role',
        'requester_app',
        'purpose',
        'start_date',
        'end_date',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'returned_at',
        'return_notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // ──────────────── Relationships ────────────────

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // ──────────────── Scopes ────────────────

    public function scopePending($q)     { return $q->where('status', 'pending'); }
    public function scopeApproved($q)   { return $q->where('status', 'approved'); }
    public function scopeRejected($q)   { return $q->where('status', 'rejected'); }
    public function scopeReturned($q)   { return $q->where('status', 'returned'); }

    public function scopeFromApp($q, string $app = 'aplikasi-izin')
    {
        return $q->where('requester_app', $app);
    }

    // ──────────────── Helpers ────────────────

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
    public function isReturned(): bool { return $this->status === 'returned'; }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
            default    => ucfirst($this->status),
        };
    }
}
