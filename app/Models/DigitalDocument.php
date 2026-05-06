<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
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
     * Buat atau perbarui satu record dokumen per penandatangan.
     * Setiap (document_type, reference_id, signed_by) = satu record unik.
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

        // Lookup per penandatangan — satu record per (type, ref, user)
        $existing = self::where('document_type', $type)
            ->where('reference_id', $refId)
            ->where('signed_by', $user->id)
            ->first();

        if ($existing) {
            $existing->update($data);
            return $existing->refresh();
        }

        return self::create(array_merge($data, [
            'document_type' => $type,
            'reference_id'  => $refId,
        ]));
    }

    /**
     * Auto-sign dokumen BAST untuk satu Employee (jika sudah setup TTD digital),
     * lalu kembalikan array ['sig', 'doc', 'qr'] untuk dipakai di Blade PDF.
     *
     * @param  Employee|null  $employee
     * @param  string         $docType     cth: 'BAST_PROC_V2S'
     * @param  string         $docTitle    judul dokumen
     * @param  string         $refId       nomor dokumen / ID referensi
     * @param  array          $hashParts   komponen hash
     * @return array{sig: UserDigitalSignature|null, doc: DigitalDocument|null, qr: string|null}
     */
    public static function bastSignerData(
        ?Employee $employee,
        string $docType,
        string $docTitle,
        string $refId,
        array $hashParts
    ): array {
        if (!$employee) {
            return ['sig' => null, 'doc' => null, 'qr' => null];
        }

        $user = $employee->user;
        if (!$user) {
            return ['sig' => null, 'doc' => null, 'qr' => null];
        }

        $sig = UserDigitalSignature::where('user_id', $user->id)->first();
        if (!$sig || !$sig->isReady()) {
            return ['sig' => null, 'doc' => null, 'qr' => null];
        }

        $doc = self::signOrUpdate($user, $docType, $docTitle, $refId, $hashParts);

        $verifyUrl = url('/verify/signature/' . $doc->token);
        $options   = new QROptions([
            'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'    => QRCode::ECC_M,
            'scale'       => 4,
            'imageBase64' => true,
        ]);
        $qr = (new QRCode($options))->render($verifyUrl);

        return ['sig' => $sig, 'doc' => $doc, 'qr' => $qr];
    }
}
