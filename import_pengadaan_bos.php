<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use App\Models\Institution;
use App\Models\Category;
use App\Models\Building;
use App\Models\Room;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\PersonInCharge;
use App\Models\AssetFunction;
use App\Models\FundingSource;
use Illuminate\Support\Facades\DB;

echo "=== MEMULAI IMPORT PENGADAAN DANA BOS (LAB ANIMASI) ===\n\n";

// 1. Master Data
$inst = Institution::where('code', '24')->first() ?? Institution::first();
$fac  = Faculty::where('code', '01')->first() ?? Faculty::first();
$dept = Department::where('code', '008')->first() ?? Department::first(); // Laboratorium
$pic  = PersonInCharge::where('code', '004')->first() ?? PersonInCharge::first(); // KAUR LAB
$fn   = AssetFunction::where('code', '14')->first() ?? AssetFunction::first(); // Sarana Pendidikan Lab
$bldg = Building::where('code', '002')->first() ?? Building::first(); // Gedung 2
$room = Room::where('code', '0125')->first() ?? Room::where('name', 'like', '%Animasi%')->first(); // Lab Animasi
$fund = FundingSource::where('name', 'like', '%Capex - BOS Tahap 2%')->first() ?? FundingSource::find(10);

$catPc  = Category::where('code', '013')->first() ?? Category::find(81);
$catUps = Category::where('code', '039')->first() ?? Category::find(107);

$year = 2026;
$year2 = substr((string)$year, -2);

echo "Master Data Terpilih:\n";
echo "- Lembaga     : {$inst->name} (Kode: {$inst->code})\n";
echo "- Kategori PC : {$catPc->name} (Kode: {$catPc->code})\n";
echo "- Kategori UPS: {$catUps->name} (Kode: {$catUps->code})\n";
echo "- Gedung      : {$bldg->name} (Kode: {$bldg->code})\n";
echo "- Ruang       : {$room->name} (Kode: {$room->code})\n";
echo "- Fakultas    : {$fac->name} (Kode: {$fac->code})\n";
echo "- Departemen  : {$dept->name} (Kode: {$dept->code})\n";
echo "- PIC         : {$pic->name} (Kode: {$pic->code})\n";
echo "- Fungsi Aset : {$fn->name} (Kode: {$fn->code})\n";
echo "- Sumber Dana : {$fund->name} (Kode: {$fund->code})\n";
echo "- Tahun Beli  : {$year} (Format Kode: {$year2})\n\n";

// 2. Daftar Barang yang akan di-import
$itemsToImport = [
    // 2 Unit PC Workstation Ryzen 7
    [
        'category' => $catPc,
        'name' => 'Paket PC Workstation Laboratorium Animasi (Full Set)',
        'qty' => 2,
        'purchase_cost' => 39180780.00,
        'useful_life' => 5,
        'salvage_value' => 0.00,
        'spesifikasi' => [
            'tipe' => 'AGRES X GTID GTR ZERO',
            'prosesor' => 'AMD Ryzen 7 5700X (8 Cores, 16 Threads, up to 4.6 GHz, 32MB Cache)',
            'gpu' => 'ZOTAC GeForce RTX 5060 8GB GDDR7 / Palit RTX 5060 White OC 8G GDDR7',
            'motherboard' => 'MSI MATX PRO B550M-P WiFi 6E DDR4 AM4',
            'ram' => '32GB (2x16GB) ADATA XPG Spectrix D35G DDR4 3200MHz RGB / Corsair 32GB KIT non-RGB',
            'storage' => '1TB Kingston NV3 M.2 NVMe PCIe 4.0 Gen4 (up to 6000MB/s)',
            'cooler' => 'Cooler Master Hyper 212 Flow ARGB',
            'psu' => 'Cooler Master MWE 650 v3 650W 80+ Gold ATX 3.1',
            'casing' => 'Gamemax Spark Air MATX Chassis + 4x Fan ARGB 120mm / Ace Power Artemis TG RGB',
            'monitor' => 'Lenovo Legion 24-10 (23.8 inch IPS FHD 240Hz, 0.5ms MPRT, 99% sRGB) / Legion 27-10',
            'periferal' => 'Ajazz AK820 75% Mechanical Keyboard & Logitech G203 Lightsync RGB Mouse',
            'garansi' => '3 Tahun Hardware & 1 Tahun Agres Protect'
        ]
    ],
    // 5 Unit PC Workstation Ryzen 5
    [
        'category' => $catPc,
        'name' => 'Paket PC Workstation Laboratorium Animasi (Full Set)',
        'qty' => 5,
        'purchase_cost' => 25504470.00,
        'useful_life' => 5,
        'salvage_value' => 0.00,
        'spesifikasi' => [
            'tipe' => 'Workstation Lab Animasi Standar',
            'prosesor' => 'AMD Ryzen 5 5600 tray (3th)',
            'gpu' => 'Gigabyte RTX 3050 WF2OCV2 6GD',
            'motherboard' => 'Gigabyte B550M DS3H AC R2 WiFi',
            'ram' => 'Kingston Fury DDR4 3200 16GB (2x8GB)',
            'storage' => 'SSD 500GB Samsung 870 EVO SATA',
            'cooler' => 'Cooler Master Cooler Hyper 411 Nano ARGB',
            'psu' => 'PSU Cooler Master MWE 650 V3 650W 80+ Gold',
            'casing' => 'Casing Ace Power Artemis TG RGB 4 fan',
            'monitor' => 'Lenovo L24-4e IPS 100Hz',
            'periferal' => 'Keyboard Maxfit8 MK921 & Mouse Logitech G203 LIGHTSYNC RGB'
        ]
    ],
    // 2 Unit UPS Prolink 1000VA
    [
        'category' => $catUps,
        'name' => 'UPS On-Line Double Conversion 1000VA / 900W Pure Sinewave',
        'qty' => 2,
        'purchase_cost' => 8162662.50,
        'useful_life' => 5,
        'salvage_value' => 0.00,
        'spesifikasi' => [
            'merek_model' => 'Prolink UPS PRO901ES 1P/NP Series True On-Line Double Conversion',
            'kapasitas' => '1000 VA / 900 Watt Murni (Power Factor 0.9 Efisiensi Tinggi)',
            'gelombang' => 'Pure Sinewave (Sinus Murni 100% harmonis untuk beban PSU Active PFC)',
            'transfer_time' => '0 ms (Zero Transfer Time, bebas jeda mati mendadak)',
            'input_voltage' => '110-300 VAC, Built-in AVR (Automatic Voltage Regulator)',
            'display' => 'Layar LCD Digital Multifungsi (Load, Battery, Input/Output, Error Code)',
            'port_komunikasi' => 'USB Port, Serial RS232, Slot Cerdas Intelligent SNMP',
            'garansi' => '24 Bulan (2 Tahun) Garansi Resmi Prolink Indonesia'
        ]
    ]
];

DB::beginTransaction();
try {
    $latestAsset = Asset::orderByRaw('CAST(sequence_number AS UNSIGNED) DESC')->first();
    $currentSeq = $latestAsset ? intval($latestAsset->sequence_number) : 0;
    
    echo "Nomor urut terakhir di database: " . sprintf('%04d', $currentSeq) . "\n\n";

    $createdAssets = [];

    foreach ($itemsToImport as $itemGroup) {
        $category = $itemGroup['category'];
        for ($i = 1; $i <= $itemGroup['qty']; $i++) {
            $currentSeq++;
            $formattedSeq = sprintf('%04d', $currentSeq);

            // Format Kode Aset YPT Standar:
            // [Lembaga].[Tahun].[Kategori].[Gedung].[Ruangan].[Fakultas].[Prodi].[PIC].[Fungsi].[SumberDana].[NomorUrut]
            $asset_code_ypt = implode('.', [
                $inst->code,
                $year2,
                $category->code,
                $bldg->code,
                $room->code,
                $fac->code,
                $dept->code,
                $pic->code,
                $fn->code,
                $fund->code,
                $formattedSeq
            ]);

            $asset = Asset::create([
                'name'                => $itemGroup['name'],
                'spesifikasi'         => $itemGroup['spesifikasi'],
                'purchase_year'       => $year,
                'purchase_cost'       => $itemGroup['purchase_cost'],
                'useful_life'         => $itemGroup['useful_life'],
                'salvage_value'       => $itemGroup['salvage_value'],
                'asset_code_ypt'      => $asset_code_ypt,
                'sequence_number'     => $formattedSeq,
                'institution_id'      => $inst->id,
                'category_id'         => $category->id,
                'building_id'         => $bldg->id,
                'room_id'             => $room->id,
                'faculty_id'          => $fac->id,
                'department_id'       => $dept->id,
                'person_in_charge_id' => $pic->id,
                'asset_function_id'   => $fn->id,
                'funding_source_id'   => $fund->id,
                'status'              => 'Aktif',
                'current_status'      => 'Tersedia',
            ]);

            $createdAssets[] = $asset;
            echo "✓ Berhasil dibuat [{$formattedSeq}]: {$asset->asset_code_ypt} | {$asset->name} | Rp " . number_format($asset->purchase_cost, 0, ',', '.') . "\n";
        }
    }

    DB::commit();
    echo "\n=== SUKSES! Total " . count($createdAssets) . " aset baru berhasil di-import ke database local! ===\n";
} catch (\Throwable $e) {
    DB::rollBack();
    echo "\n❌ Terjadi kesalahan: " . $e->getMessage() . "\n";
    exit(1);
}
