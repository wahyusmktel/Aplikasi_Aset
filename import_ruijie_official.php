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
use PhpOffice\PhpSpreadsheet\IOFactory;

$excelPath = $argv[1] ?? __DIR__ . '/storage/ruijie_devices.xlsx';
if (!file_exists($excelPath)) {
    // Fallback to uploaded artifact if local storage copy not provided
    $fallback = 'C:/Users/wahyu/.gemini/antigravity/brain/56170126-617a-4122-93de-dcb9325235e9/.user_uploaded/media_1794543480343_7c89f977.xlsx';
    if (file_exists($fallback)) {
        $excelPath = $fallback;
    } else {
        echo "Error: Excel file not found at $excelPath\n";
        exit(1);
    }
}

$spreadsheet = IOFactory::load($excelPath);
$rows = $spreadsheet->getActiveSheet()->toArray();
array_shift($rows); // skip header

// Master Data Resmi Sesuai Sistem YPT
$inst = Institution::where('code', '24')->first() ?? Institution::first();
$fac  = Faculty::where('code', '01')->first() ?? Faculty::first();
$dept = Department::where('code', '007')->first() ?? Department::first(); // IT
$pic  = PersonInCharge::where('code', '002')->first() ?? PersonInCharge::first(); // KAUR IT
$fn   = AssetFunction::where('code', '13')->first() ?? AssetFunction::first(); // Sarana & Prasarana Umum
$fund = FundingSource::where('name', 'like', '%CAPEX Internal%')->first() ?? FundingSource::where('code', '2')->first();

// Categories
$catAp = Category::where('code', '043')->first(); // ACCESS POINT/WIFI LAN/ROUTER/MODEM
$catSw = Category::where('code', '044')->first(); // SWITCH / HUB JARINGAN /ACCESS POINT

// Buildings
$bldgKantor = Building::where('code', '007')->first(); // Gedung Kantor
$bldg1      = Building::where('code', '001')->first(); // Gedung 1
$bldg2      = Building::where('code', '002')->first(); // Gedung 2
$bldg3      = Building::where('code', '003')->first(); // Gedung 3
$bldgUndef  = Building::where('code', '008')->first() ?? Building::first(); // Belum Ditentukan

// Rooms
$roomServer  = Room::where('code', '0051')->first(); // Ruang Server
$roomMasjid  = Room::where('code', '0120')->first(); // Masjid Sekolah
$roomLabSoft = Room::where('code', '0124')->first(); // Lab Software
$roomLabAnms = Room::where('code', '0125')->first(); // Lab Animasi
$roomLabTJAT = Room::where('code', '0126')->first(); // Lab Fiber Optic
$roomLabTKJ  = Room::where('code', '0127')->first(); // Lab Jaringan
$roomUndef   = Room::where('code', '0121')->first() ?? Room::first(); // Belum Ditentukan

$imported = 0;

foreach ($rows as $r) {
    if (empty($r[0])) continue;

    $sn      = trim($r[0]);
    $alias   = trim($r[1] ?? '');
    $mac     = trim($r[3] ?? '');
    $model   = trim($r[7] ?? '');
    $ip      = trim($r[8] ?? '');
    $firmware= trim($r[6] ?? '');

    // Skip duplicate by SN if already imported
    $existing = Asset::where('spesifikasi->serial_number', $sn)->first();
    if ($existing) {
        echo "Skip (already exists): SN $sn ({$existing->name})\n";
        continue;
    }

    // Klasifikasi Jenis Perangkat & Kategori Resmi
    $lowerModel = strtolower($model);
    $lowerAlias = strtolower($alias);

    if (str_starts_with($lowerModel, 'ap')) {
        $type = 'Access Point';
        $category = $catAp;
        $name = "Access Point Ruijie {$model}";
    } elseif (str_starts_with($lowerModel, 's') || str_starts_with($lowerModel, 'nbs') || str_starts_with($lowerModel, 'cs')) {
        $type = 'Switch';
        $category = $catSw;
        $name = "Switch Ruijie {$model}";
    } elseif (str_starts_with($lowerModel, 'eg')) {
        $type = 'Gateway';
        $category = $catAp; // Sesuai kategori Router/Modem/AP (043)
        $name = "Gateway Ruijie Reyee {$model}";
    } else {
        $type = 'Access Point';
        $category = $catAp;
        $name = "Perangkat Jaringan Ruijie {$model}";
    }

    if (!empty($alias)) {
        $cleanAlias = str_replace(['_', '-'], ' ', $alias);
        $name .= " - " . ucwords($cleanAlias);
    }

    // Mapping Gedung dan Ruangan dari Alias Perangkat
    $building = $bldgUndef;
    $room = $roomUndef;

    if (str_contains($lowerAlias, 'mushola') || str_contains($lowerAlias, 'masjid')) {
        $building = $bldgKantor ?? $bldgUndef;
        $room = $roomMasjid ?? $roomUndef;
    } elseif (str_contains($lowerAlias, 'server') || str_contains($lowerAlias, 'core') || str_starts_with($lowerModel, 'eg')) {
        $building = $bldgKantor ?? $bldgUndef;
        $room = $roomServer ?? $roomUndef;
    } elseif (str_contains($lowerAlias, 'kantor')) {
        $building = $bldgKantor ?? $bldgUndef;
    } elseif (str_contains($lowerAlias, 'lab_soft') || str_contains($lowerAlias, 'lab soft')) {
        $building = $bldg1 ?? $bldgUndef;
        $room = $roomLabSoft ?? $roomUndef;
    } elseif (str_contains($lowerAlias, 'anms') || str_contains($lowerAlias, 'animasi')) {
        $building = $bldg1 ?? $bldgUndef;
        $room = $roomLabAnms ?? $roomUndef;
    } elseif (str_contains($lowerAlias, 'tkj')) {
        $building = $bldg2 ?? $bldgUndef;
        $room = $roomLabTKJ ?? $roomUndef;
    } elseif (str_contains($lowerAlias, 'tjat')) {
        $building = $bldg2 ?? $bldgUndef;
        $room = $roomLabTJAT ?? $roomUndef;
    } elseif (str_starts_with($lowerAlias, 'a1') || str_starts_with($lowerAlias, 'a2')) {
        $building = $bldg1 ?? $bldgUndef;
    } elseif (str_starts_with($lowerAlias, 'b1') || str_starts_with($lowerAlias, 'b2')) {
        $building = $bldg2 ?? $bldgUndef;
    } elseif (str_starts_with($lowerAlias, 'c1') || str_starts_with($lowerAlias, 'c2') || str_starts_with($lowerAlias, 'c3')) {
        $building = $bldg3 ?? $bldgUndef;
    }

    // Sequence Number otomatis mengikuti logika Tambah Aset Baru
    $latestAsset = Asset::orderByRaw('CAST(sequence_number AS UNSIGNED) DESC')->first();
    $newSeq = $latestAsset ? (intval($latestAsset->sequence_number) + 1) : 1;
    $formattedSeq = sprintf('%04d', $newSeq);

    // Format Kode Aset YPT Standar:
    // [Lembaga].[Tahun].[Kategori].[Gedung].[Ruangan].[Fakultas].[Prodi].[PIC].[Fungsi].[SumberDana].[NomorUrut]
    $year2 = '00'; // Dikosongkan sesuai instruksi user
    $asset_code_ypt = implode('.', [
        $inst->code,
        $year2,
        $category->code,
        $building->code,
        $room->code,
        $fac->code,
        $dept->code,
        $pic->code,
        $fn->code,
        $fund->code,
        $formattedSeq
    ]);

    Asset::create([
        'name'                => $name,
        'purchase_year'       => null,
        'purchase_cost'       => 0,
        'useful_life'         => 5,
        'salvage_value'       => 0,
        'asset_code_ypt'      => $asset_code_ypt,
        'sequence_number'     => $formattedSeq,
        'institution_id'      => $inst->id,
        'category_id'         => $category->id,
        'building_id'         => $building->id,
        'room_id'             => $room->id,
        'faculty_id'          => $fac->id,
        'department_id'       => $dept->id,
        'person_in_charge_id' => $pic->id,
        'asset_function_id'   => $fn->id,
        'funding_source_id'   => $fund->id,
        'status'              => 'Aktif',
        'current_status'      => 'Digunakan',
        'spesifikasi'         => [
            'serial_number'   => $sn,
            'mac_address'     => $mac,
            'management_ip'   => $ip,
            'model'           => $model,
            'alias_cloud'     => $alias,
            'firmware'        => $firmware,
            'tipe_perangkat'  => $type,
        ]
    ]);

    $imported++;
}

echo "Proses selesai. Berhasil menambahkan $imported aset baru.\n";
