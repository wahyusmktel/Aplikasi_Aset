<?php

namespace App\Exports;

use App\Models\Institution;
use App\Models\Category;
use App\Models\Building;
use App\Models\Room;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\PersonInCharge;
use App\Models\AssetFunction;
use App\Models\FundingSource;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AssetImportTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Template Impor Aset';
    }

    public function headings(): array
    {
        return [
            'nama_barang',
            'quantity',
            'tahun_pembelian',
            'nama_lembaga',
            'nama_kategori',
            'nama_gedung',
            'nama_ruangan',
            'nama_fakultas',
            'nama_prodi_unit',
            'nama_penanggung_jawab',
            'nama_fungsi_barang',
            'nama_jenis_pendanaan',
        ];
    }

    public function array(): array
    {
        // Ambil data master yang sudah ada di database untuk sampel yang realistis
        $institution    = Institution::first();
        $categories     = Category::take(3)->get();
        $buildings      = Building::take(3)->get();
        $rooms          = Room::take(3)->get();
        $faculties      = Faculty::take(3)->get();
        $departments    = Department::take(3)->get();
        $pics           = PersonInCharge::take(3)->get();
        $assetFunctions = AssetFunction::take(3)->get();
        $fundingSources = FundingSource::take(3)->get();

        $institutionName = $institution->name ?? 'SMK Telkom Lampung';

        // Buat 3 baris sampel dengan data master yang berbeda-beda
        $samples = [];
        $sampleNames = ['Komputer Desktop', 'Meja Guru', 'Proyektor'];
        $sampleQty   = [5, 10, 2];
        $sampleYears = [2025, 2025, 2024];

        for ($i = 0; $i < 3; $i++) {
            $samples[] = [
                $sampleNames[$i],
                $sampleQty[$i],
                $sampleYears[$i],
                $institutionName,
                $categories[$i]->name   ?? 'Peralatan & Mesin',
                $buildings[$i]->name    ?? 'Gedung A',
                $rooms[$i]->name        ?? 'Ruang 101',
                $faculties[$i]->name    ?? 'Teknik',
                $departments[$i]->name  ?? 'TKJ',
                $pics[$i]->name         ?? 'Admin Sarpras',
                $assetFunctions[$i]->name ?? 'Operasional',
                $fundingSources[$i]->name ?? 'BOS',
            ];
        }

        return $samples;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, // nama_barang
            'B' => 12, // quantity
            'C' => 18, // tahun_pembelian
            'D' => 22, // nama_lembaga
            'E' => 22, // nama_kategori
            'F' => 18, // nama_gedung
            'G' => 18, // nama_ruangan
            'H' => 18, // nama_fakultas
            'I' => 22, // nama_prodi_unit
            'J' => 25, // nama_penanggung_jawab
            'K' => 22, // nama_fungsi_barang
            'L' => 22, // nama_jenis_pendanaan
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'L';
        $lastRow = 4; // 1 header + 3 sample rows

        return [
            // Style untuk header row
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC2626'], // Red-600
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Style untuk seluruh area data
            "A1:{$lastCol}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D1D5DB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Style untuk baris sampel (highlight kuning muda)
            "A2:{$lastCol}{$lastRow}" => [
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF9C3'], // Yellow-100
                ],
                'font' => [
                    'italic' => true,
                    'color'  => ['rgb' => '92400E'], // Amber-800
                ],
            ],
        ];
    }
}
