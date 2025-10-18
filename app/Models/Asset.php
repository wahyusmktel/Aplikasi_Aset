<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'purchase_cost', // Baru
        'useful_life',   // Baru
        'salvage_value', // Baru
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
        'disposal_date',
        'disposal_method',
        'disposal_reason',
        'disposal_value',
        'disposal_doc_number',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'purchase_cost' => 'decimal:2', // Baru
        'salvage_value' => 'decimal:2', // Baru
    ];

    // Method Accessor untuk menghitung Nilai Buku (Book Value)
    public function getBookValueAttribute(): float
    {
        if (!$this->purchase_cost || !$this->useful_life || $this->useful_life <= 0) {
            return $this->purchase_cost ?? 0; // Jika data tidak lengkap, kembalikan harga beli
        }

        // Hitung penyusutan tahunan (Metode Garis Lurus)
        $depreciableCost = $this->purchase_cost - $this->salvage_value;
        $annualDepreciation = $depreciableCost / $this->useful_life;

        // Hitung umur aset dalam tahun (dengan pecahan)
        $purchaseDate = Carbon::createFromDate($this->purchase_year, 1, 1); // Asumsi beli awal tahun
        $ageInYears = $purchaseDate->diffInYears(Carbon::now()); // Bisa diganti Carbon::today()

        // Hitung total akumulasi penyusutan
        $accumulatedDepreciation = min($annualDepreciation * $ageInYears, $depreciableCost); // Jangan sampai minus

        // Nilai buku = Harga Beli - Akumulasi Penyusutan
        $bookValue = $this->purchase_cost - $accumulatedDepreciation;

        // Nilai buku tidak boleh lebih rendah dari nilai sisa
        return max($bookValue, $this->salvage_value);
    }

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

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class)->orderBy('maintenance_date', 'desc');
    }

    public function inspections()
    {
        return $this->hasMany(AssetInspection::class)->orderBy('inspection_date', 'desc');
    }

    public function vehicleLogs()
    {
        return $this->hasMany(VehicleLog::class)->orderBy('departure_time', 'desc');
    }

    // Relasi untuk mendapatkan log penggunaan yang aktif saat ini
    public function currentVehicleLog()
    {
        return $this->hasOne(VehicleLog::class)->whereNull('return_time');
    }
}
