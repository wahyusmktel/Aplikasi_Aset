<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'purchase_year',
        'asset_code_ypt',
        'sequence_number',
        'institution_id',
        'category_id',
        'building_id',
        'room_id',
        'faculty_id',
        'department_id',
        'person_in_charge_id',
        'asset_function_id',
        'funding_source_id',
        'status',
        'current_status',
    ];

    /**
     * Mendapatkan data lembaga yang memiliki aset ini.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Mendapatkan data kategori dari aset ini.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Mendapatkan data gedung tempat aset ini berada.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Mendapatkan data ruangan tempat aset ini berada.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Mendapatkan data fakultas/direktorat yang terkait dengan aset ini.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Mendapatkan data departemen/prodi yang terkait dengan aset ini.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Mendapatkan data penanggung jawab aset ini.
     */
    public function personInCharge(): BelongsTo
    {
        return $this->belongsTo(PersonInCharge::class);
    }

    /**
     * Mendapatkan data fungsi dari aset ini.
     */
    public function assetFunction(): BelongsTo
    {
        return $this->belongsTo(AssetFunction::class);
    }

    /**
     * Mendapatkan data sumber pendanaan aset ini.
     */
    public function fundingSource(): BelongsTo
    {
        return $this->belongsTo(FundingSource::class);
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)->whereNull('returned_date');
    }
}
