<?php

namespace App\Imports;

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
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetsBatchImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $institutions, $categories, $buildings, $rooms, $faculties, $departments, $personsInCharge, $assetFunctions, $fundingSources;
    private static $lastSequenceNumber; // Gunakan static agar nilainya bertahan antar baris

    public function __construct()
    {
        // Ambil semua data master sekali saja untuk efisiensi (caching)
        $this->institutions = Institution::all()->keyBy('name');
        $this->categories = Category::all()->keyBy('name');
        $this->buildings = Building::all()->keyBy('name');
        $this->rooms = Room::all()->keyBy('name');
        $this->faculties = Faculty::all()->keyBy('name');
        $this->departments = Department::all()->keyBy('name');
        $this->personsInCharge = PersonInCharge::all()->keyBy('name');
        $this->assetFunctions = AssetFunction::all()->keyBy('name');
        $this->fundingSources = FundingSource::all()->keyBy('name');

        // Tentukan nomor urut awal dari data yang sudah ada di database
        if (is_null(self::$lastSequenceNumber)) {
            $latestAsset = Asset::orderBy('id', 'desc')->first();
            self::$lastSequenceNumber = $latestAsset ? intval($latestAsset->sequence_number) : 0;
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $quantity = $row['quantity'];

            // Ambil ID dari data master berdasarkan nama yang ada di Excel
            $institution = $this->institutions->get($row['nama_lembaga']);
            $category = $this->categories->get($row['nama_kategori']);
            $building = $this->buildings->get($row['nama_gedung']);
            $room = $this->rooms->get($row['nama_ruangan']);
            $faculty = $this->faculties->get($row['nama_fakultas']);
            $department = $this->departments->get($row['nama_prodi_unit']);
            $personInCharge = $this->personsInCharge->get($row['nama_penanggung_jawab']);
            $assetFunction = $this->assetFunctions->get($row['nama_fungsi_barang']);
            $fundingSource = $this->fundingSources->get($row['nama_jenis_pendanaan']);
            $year = substr($row['tahun_pembelian'], -2);

            // Loop untuk membuat aset sebanyak quantity
            for ($i = 0; $i < $quantity; $i++) {
                // Lanjutkan nomor urut
                self::$lastSequenceNumber++;
                $formattedSequence = sprintf('%04d', self::$lastSequenceNumber);

                // Buat data aset
                $asset = Asset::create([
                    'name' => $row['nama_barang'],
                    'purchase_year' => $row['tahun_pembelian'],
                    'sequence_number' => $formattedSequence,
                    'institution_id' => $institution->id,
                    'category_id' => $category->id,
                    'building_id' => $building->id,
                    'room_id' => $room->id,
                    'faculty_id' => $faculty->id,
                    'department_id' => $department->id,
                    'person_in_charge_id' => $personInCharge->id,
                    'asset_function_id' => $assetFunction->id,
                    'funding_source_id' => $fundingSource->id,
                ]);

                // Generate Kode Aset YPT
                $asset_code_ypt = implode('.', [
                    $institution->code,
                    $year,
                    $category->code,
                    $building->code,
                    $room->code,
                    $faculty->code,
                    $department->code,
                    $personInCharge->code,
                    $assetFunction->code,
                    $fundingSource->code,
                    $formattedSequence
                ]);

                $asset->update(['asset_code_ypt' => $asset_code_ypt, 'status' => 'Aktif']);
            }
        }
    }

    public function rules(): array
    {
        // Validasi untuk memastikan data master yang diinput di Excel benar-benar ada di database
        return [
            'nama_barang' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'tahun_pembelian' => 'required|digits:4',
            'nama_lembaga' => 'required|exists:institutions,name',
            'nama_kategori' => 'required|exists:categories,name',
            'nama_gedung' => 'required|exists:buildings,name',
            'nama_ruangan' => 'required|exists:rooms,name',
            'nama_fakultas' => 'required|exists:faculties,name',
            'nama_prodi_unit' => 'required|exists:departments,name',
            'nama_penanggung_jawab' => 'required|exists:persons_in_charge,name',
            'nama_fungsi_barang' => 'required|exists:asset_functions,name',
            'nama_jenis_pendanaan' => 'required|exists:funding_sources,name',
        ];
    }
}
