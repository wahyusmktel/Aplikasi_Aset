<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalDocument extends Model
{
    protected $fillable = [
        'token',
        'document_type',
        'document_title',
        'reference_id',
        'document_hash',
        'hmac_signature',
        'signed_by',
        'signer_name',
        'signer_nip',
        'signer_role',
        'signed_at',
        'is_valid',
        'revoked_at',
        'revoke_reason',
    ];

    protected $casts = [
        'signed_at'  => 'datetime',
        'revoked_at' => 'datetime',
        'is_valid'   => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = (string) Str::uuid();
            }
        });
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public static function generateHash(array $parts): string
    {
        return hash('sha256', implode('|', $parts));
    }

    public static function generateHmac(string $hash): string
    {
        $key = base64_decode(str_replace('base64:', '', config('app.key')));
        return hash_hmac('sha256', $hash, $key);
    }

    public function verifyHmac(): bool
    {
        return hash_equals(self::generateHmac($this->document_hash), $this->hmac_signature);
    }

    /**
     * Buat atau perbarui dokumen yang ditandatangani.
     */
    public static function signOrUpdate(
        User $user,
        string $type,
        string $title,
        string $refId,
        array $hashParts
    ): self {
        $hash = self::generateHash($hashParts);
        $hmac = self::generateHmac($hash);

        $data = [
            'document_title' => $title,
            'document_hash'  => $hash,
            'hmac_signature' => $hmac,
            'signed_by'      => $user->id,
            'signer_name'    => $user->name,
            'signer_nip'     => $user->employee?->nip,
            'signer_role'    => $user->employee?->position ?? ($user->isAdmin() ? 'Administrator' : 'Staff'),
            'signed_at'      => now(),
            'is_valid'       => true,
            'revoked_at'     => null,
            'revoke_reason'  => null,
        ];

        $existing = self::where('document_type', $type)->where('reference_id', $refId)->first();

        if ($existing) {
            $existing->update($data);
            return $existing->refresh();
        }

        return self::create(array_merge($data, [
            'document_type' => $type,
            'reference_id'  => $refId,
        ]));
    }
}
