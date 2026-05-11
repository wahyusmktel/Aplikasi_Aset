<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nip',
        'position',
        'is_sarpra_it_lab',
        'is_headmaster',
        'is_kaur_it',
        // Dapodik fields
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_perkawinan',
        'nuptk',
        'status_kepegawaian',
        'golongan_pangkat',
        'tmt_pengangkatan',
        'pendidikan_terakhir',
        'bidang_studi',
        'lembaga_pendidikan',
        'alamat',
        'no_hp',
    ];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'tmt_pengangkatan' => 'date',
        'is_sarpra_it_lab' => 'boolean',
        'is_headmaster'    => 'boolean',
        'is_kaur_it'       => 'boolean',
    ];

    public function dapodikChangeRequests()
    {
        return $this->hasMany(DapodikChangeRequest::class);
    }

    public function pendingChangeRequest()
    {
        return $this->hasOne(DapodikChangeRequest::class)->where('status', 'pending')->latest();
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function vehicleLogs()
    {
        return $this->hasMany(VehicleLog::class)->orderBy('departure_time', 'desc');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
