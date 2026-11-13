<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActiveAssetsExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithColumnFormatting, WithEvents, WithTitle
{
    protected array $includeCategoryIds = [];
    protected array $excludeCategoryIds = [];
    protected ?string $purchaseYear = null;
    protected ?string $search = null;
    protected array $statuses = [];
    protected $fundingSourceId = null;
    
    protected $assets;
    protected int $rowNumber = 0;
    protected int $headerRow = 9;
    protected int $dataStartRow = 10;

    public function __construct(
        $includeCategoryIds = [],
        $excludeCategoryIds = [],
        ?string $purchaseYear = null,
        ?string $search = null,
        array $statuses = [],
        $fundingSourceId = null
    ) {
        if (is_numeric($includeCategoryIds)) {
            $this->includeCategoryIds = [(int) $includeCategoryIds];
        } elseif (is_array($includeCategoryIds)) {
            $this->includeCategoryIds = array_values(array_filter(array_map('intval', $includeCategoryIds)));
        }

        if (is_array($excludeCategoryIds)) {
            $this->excludeCategoryIds = array_values(array_filter(array_map('intval', $excludeCategoryIds)));
        }

        $this->purchaseYear = ($purchaseYear && $purchaseYear !== 'all') ? (string) $purchaseYear : null;
        $this->search = $search ? trim($search) : null;
        $this->statuses = array_filter($statuses);
        $this->fundingSourceId = ($fundingSourceId && $fundingSourceId !== 'all') ? $fundingSourceId : null;

        // Fetch collection eagerly so metrics and counts can be calculated for summary cards
        $this->loadAssets();
    }

    protected function loadAssets(): void
    {
        $query = Asset::query()
            ->whereNull('disposal_date')
            ->with([
                'category',
                'institution',
                'building',
                'room',
                'department',
                'personInCharge',
                'assetFunction',
                'fundingSource',
            ])
            ->when(!empty($this->includeCategoryIds), fn($q) => $q->whereIn('category_id', $this->includeCategoryIds))
            ->when(empty($this->includeCategoryIds) && !empty($this->excludeCategoryIds), fn($q) => $q->whereNotIn('category_id', $this->excludeCategoryIds))
            ->when($this->purchaseYear, fn($q) => $q->where('purchase_year', $this->purchaseYear))
            ->when($this->fundingSourceId, fn($q) => $q->where('funding_source_id', $this->fundingSourceId))
            ->when(!empty($this->statuses), fn($q) => $q->whereIn('status', $this->statuses)->orWhereIn('current_status', $this->statuses))
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('asset_code_ypt', 'like', "%{$s}%")
                        ->orWhere('sequence_number', 'like', "%{$s}%");
                });
            })
            ->orderBy('asset_code_ypt', 'asc');

        $this->assets = $query->get();
    }

    public function title(): string
    {
        return 'Daftar Aset Aktif';
    }

    public function startCell(): string
    {
        return 'A' . $this->headerRow;
    }

    public function collection()
    {
        return $this->assets;
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE ASET YPT',
            'NO URUT',
            'NAMA BARANG / ASET',
            'SPESIFIKASI / DETAIL',
            'KATEGORI',
            'GEDUNG',
            'RUANGAN',
            'UNIT / PRODI',
            'PENANGGUNG JAWAB (PIC)',
            'THN BELI',
            'KONDISI FISIK',
            'STATUS OPERASIONAL',
            'HARGA AKUISISI (RP)',
            'MASA MANFAAT',
            'NILAI RESIDU (RP)',
            'ESTIMASI NILAI BUKU (RP)',
            'SUMBER DANA',
        ];
    }

    public function map($asset): array
    {
        $this->rowNumber++;

        // Format spesifikasi
        $spesifikasiText = '-';
        if (is_array($asset->spesifikasi) && !empty($asset->spesifikasi)) {
            $parts = [];
            foreach ($asset->spesifikasi as $k => $v) {
                if (is_scalar($v) && trim((string) $v) !== '') {
                    $label = ucwords(str_replace('_', ' ', $k));
                    $parts[] = "{$label}: {$v}";
                }
            }
            $spesifikasiText = !empty($parts) ? implode('; ', $parts) : '-';
        } elseif (is_string($asset->spesifikasi) && trim($asset->spesifikasi) !== '') {
            $spesifikasiText = trim($asset->spesifikasi);
        }

        $purchaseCost = (float) ($asset->purchase_cost ?? 0);
        $salvageValue = (float) ($asset->salvage_value ?? 0);
        $bookValue    = (float) ($asset->book_value ?? $purchaseCost);
        $usefulLife   = (int) ($asset->useful_life ?? 0);

        return [
            $this->rowNumber,
            $asset->asset_code_ypt ?? '-',
            $asset->sequence_number ?? '-',
            $asset->name ?? '-',
            $spesifikasiText,
            $asset->category->name ?? 'Belum Ditentukan',
            $asset->building->name ?? '-',
            $asset->room->name ?? '-',
            $asset->department->name ?? '-',
            $asset->personInCharge->name ?? '-',
            $asset->purchase_year ? (int) $asset->purchase_year : '-',
            $asset->status ?? 'Baik',
            $asset->current_status ?? 'Tersedia',
            $purchaseCost,
            $usefulLife > 0 ? "{$usefulLife} Thn" : '-',
            $salvageValue,
            $bookValue,
            $asset->fundingSource->name ?? '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'N' => '"Rp "#,##0;("Rp "#,##0);"-"',
            'P' => '"Rp "#,##0;("Rp "#,##0);"-"',
            'Q' => '"Rp "#,##0;("Rp "#,##0);"-"',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                Carbon::setLocale('id');

                $rowCount = $this->assets->count();
                $lastDataRow = ($rowCount > 0) ? ($this->dataStartRow + $rowCount - 1) : $this->dataStartRow;
                $grandTotalRow = $lastDataRow + 1;

                // --- 1. TITLE BLOCK (Rows 1-4) ---
                // Row 1: Header Brand
                $sheet->mergeCells('A1:R1');
                $sheet->setCellValue('A1', "STELLALOG'S PRO — SISTEM MANAJEMEN ASET SARPRAS");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 14, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']], // Telkom Deep Maroon
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Row 2: Sub-Header Institution
                $institutionName = optional($this->assets->first()?->institution)->name ?? 'SMK TELKOM LAMPUNG';
                $sheet->mergeCells('A2:R2');
                $sheet->setCellValue('A2', strtoupper($institutionName));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 11, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7F1D1D']],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // Row 3: Document Title
                $sheet->mergeCells('A3:R3');
                $sheet->setCellValue('A3', 'LAPORAN REKAPITULASI & DAFTAR INVENTARIS ASET AKTIF');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(24);

                // Filter text build
                $filterText = 'Tanggal Unduh: ' . Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';
                if (!empty($this->includeCategoryIds)) {
                    $catNames = Category::whereIn('id', $this->includeCategoryIds)->pluck('name')->implode(', ');
                    $filterText .= '  |  Kategori: ' . ($catNames ?: 'Terpilih');
                } elseif (!empty($this->excludeCategoryIds)) {
                    $catNames = Category::whereIn('id', $this->excludeCategoryIds)->pluck('name')->implode(', ');
                    $filterText .= '  |  Kecuali: ' . ($catNames ?: 'Terpilih');
                } else {
                    $filterText .= '  |  Kategori: Semua';
                }

                $filterText .= '  |  Tahun: ' . ($this->purchaseYear ?: 'Semua Tahun');
                if ($this->search) {
                    $filterText .= '  |  Pencarian: "' . $this->search . '"';
                }
                $filterText .= '  |  Status: Aset Aktif';

                // Row 4: Metadata
                $sheet->mergeCells('A4:R4');
                $sheet->setCellValue('A4', $filterText);
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 9, 'italic' => true, 'color' => ['rgb' => '475569']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(20);

                // Row 5: Spacer
                $sheet->getRowDimension(5)->setRowHeight(8);

                // --- 2. EXECUTIVE DASHBOARD KPI METRIC CARDS (Rows 6-7) ---
                $sheet->getRowDimension(6)->setRowHeight(16);
                $sheet->getRowDimension(7)->setRowHeight(24);

                // Card 1: TOTAL UNIT ASET (B6:D7)
                $sheet->mergeCells('B6:D6');
                $sheet->setCellValue('B6', 'TOTAL UNIT ASET');
                $sheet->getStyle('B6:D6')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 8.5, 'bold' => true, 'color' => ['rgb' => '475569']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                ]);

                $sheet->mergeCells('B7:D7');
                $sheet->setCellValue('B7', $rowCount . ' Unit');
                $sheet->getStyle('B7:D7')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 13, 'bold' => true, 'color' => ['rgb' => '0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                ]);
                $this->applyBoxBorder($sheet, 'B6:D7', 'CBD5E1');

                // Card 2: TOTAL NILAI AKUISISI (F6:H7)
                $sheet->mergeCells('F6:H6');
                $sheet->setCellValue('F6', 'TOTAL HARGA AKUISISI');
                $sheet->getStyle('F6:H6')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 8.5, 'bold' => true, 'color' => ['rgb' => '047857']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
                ]);

                $sheet->mergeCells('F7:H7');
                if ($rowCount > 0) {
                    $sheet->setCellValue('F7', "=SUM(N{$this->dataStartRow}:N{$lastDataRow})");
                } else {
                    $sheet->setCellValue('F7', 0);
                }
                $sheet->getStyle('F7:H7')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 13, 'bold' => true, 'color' => ['rgb' => '065F46']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0;("Rp "#,##0);"-"'],
                ]);
                $this->applyBoxBorder($sheet, 'F6:H7', 'A7F3D0');

                // Card 3: ESTIMASI NILAI BUKU (J6:L7)
                $sheet->mergeCells('J6:L6');
                $sheet->setCellValue('J6', 'ESTIMASI NILAI BUKU (SAAT INI)');
                $sheet->getStyle('J6:L6')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 8.5, 'bold' => true, 'color' => ['rgb' => '1D4ED8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
                ]);

                $sheet->mergeCells('J7:L7');
                if ($rowCount > 0) {
                    $sheet->setCellValue('J7', "=SUM(Q{$this->dataStartRow}:Q{$lastDataRow})");
                } else {
                    $sheet->setCellValue('J7', 0);
                }
                $sheet->getStyle('J7:L7')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 13, 'bold' => true, 'color' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0;("Rp "#,##0);"-"'],
                ]);
                $this->applyBoxBorder($sheet, 'J6:L7', 'BFDBFE');

                // Card 4: STATUS OPERASIONAL (N6:Q7)
                $countTersedia = $this->assets->where('current_status', 'Tersedia')->count();
                $countDipinjam = $this->assets->whereIn('current_status', ['Dipinjam', 'Digunakan'])->count();
                $countRusak    = $this->assets->where('current_status', 'Rusak')->count();

                $sheet->mergeCells('N6:Q6');
                $sheet->setCellValue('N6', 'STATUS OPERASIONAL ASET');
                $sheet->getStyle('N6:Q6')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 8.5, 'bold' => true, 'color' => ['rgb' => 'B45309']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                ]);

                $sheet->mergeCells('N7:Q7');
                $sheet->setCellValue('N7', "Tersedia: {$countTersedia}  |  Dipinjam: {$countDipinjam}  |  Rusak: {$countRusak}");
                $sheet->getStyle('N7:Q7')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '78350F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                ]);
                $this->applyBoxBorder($sheet, 'N6:Q7', 'FDE68A');

                // Row 8: Spacer
                $sheet->getRowDimension(8)->setRowHeight(8);

                // --- 3. TABLE COLUMN HEADERS (Row 9) ---
                $sheet->getRowDimension($this->headerRow)->setRowHeight(28);
                $sheet->getStyle("A{$this->headerRow}:R{$this->headerRow}")->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 9.5, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'], // Dark Navy Slate Header
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']],
                    ],
                ]);

                // Enable AutoFilter on Header Row
                $sheet->setAutoFilter("A{$this->headerRow}:R{$this->headerRow}");

                // --- 4. DATA ROWS FORMATTING ---
                if ($rowCount > 0) {
                    for ($r = $this->dataStartRow; $r <= $lastDataRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(22);

                        // Zebra Striping
                        $isOdd = ($r % 2 !== 0);
                        $rowBg = $isOdd ? 'F8FAFC' : 'FFFFFF';
                        $sheet->getStyle("A{$r}:R{$r}")->applyFromArray([
                            'font' => ['name' => 'Segoe UI', 'size' => 9.5, 'color' => ['rgb' => '1E293B']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                            ],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        // Alignment by column types
                        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B{$r}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                        ]);
                        $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("D{$r}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                            'font' => ['bold' => true],
                        ]);
                        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                        $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Soft badge styling for Kondisi Fisik (Col L)
                        $kondisiVal = (string) $sheet->getCell("L{$r}")->getValue();
                        $sheet->getStyle("L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        if (stripos($kondisiVal, 'Baik') !== false) {
                            $sheet->getStyle("L{$r}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DEF7EC']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '03543F']],
                            ]);
                        } elseif (stripos($kondisiVal, 'Rusak') !== false) {
                            $sheet->getStyle("L{$r}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE8E8']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '9B1C1C']],
                            ]);
                        }

                        // Soft badge styling for Status Operasional (Col M)
                        $statusVal = (string) $sheet->getCell("M{$r}")->getValue();
                        $sheet->getStyle("M{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        if ($statusVal === 'Tersedia') {
                            $sheet->getStyle("M{$r}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DEF7EC']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '03543F']],
                            ]);
                        } elseif (in_array($statusVal, ['Dipinjam', 'Digunakan'])) {
                            $sheet->getStyle("M{$r}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1EFFE']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '1E429F']],
                            ]);
                        } elseif (stripos($statusVal, 'Rusak') !== false) {
                            $sheet->getStyle("M{$r}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE8E8']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '9B1C1C']],
                            ]);
                        } elseif (stripos($statusVal, 'Perbaikan') !== false) {
                            $sheet->getStyle("M{$r}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF08A']],
                                'font' => ['bold' => true, 'color' => ['rgb' => '78350F']],
                            ]);
                        }

                        // Financial Columns: Right align
                        $sheet->getStyle("N{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("O{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("P{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("Q{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("R{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                } else {
                    // Empty state row
                    $sheet->mergeCells("A{$this->dataStartRow}:R{$this->dataStartRow}");
                    $sheet->setCellValue("A{$this->dataStartRow}", 'Tidak ada data aset aktif yang ditemukan dengan kriteria filter yang dipilih.');
                    $sheet->getStyle("A{$this->dataStartRow}")->applyFromArray([
                        'font' => ['name' => 'Segoe UI', 'size' => 10, 'italic' => true, 'color' => ['rgb' => '64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    ]);
                    $sheet->getRowDimension($this->dataStartRow)->setRowHeight(30);
                }

                // --- 5. GRAND TOTAL SUMMARY ROW ---
                $sheet->mergeCells("A{$grandTotalRow}:M{$grandTotalRow}");
                $sheet->setCellValue("A{$grandTotalRow}", 'TOTAL KESELURUHAN ASET');
                $sheet->getRowDimension($grandTotalRow)->setRowHeight(26);

                if ($rowCount > 0) {
                    $sheet->setCellValue("N{$grandTotalRow}", "=SUM(N{$this->dataStartRow}:N{$lastDataRow})");
                    $sheet->setCellValue("O{$grandTotalRow}", '-');
                    $sheet->setCellValue("P{$grandTotalRow}", "=SUM(P{$this->dataStartRow}:P{$lastDataRow})");
                    $sheet->setCellValue("Q{$grandTotalRow}", "=SUM(Q{$this->dataStartRow}:Q{$lastDataRow})");
                    $sheet->setCellValue("R{$grandTotalRow}", '-');
                } else {
                    $sheet->setCellValue("N{$grandTotalRow}", 0);
                    $sheet->setCellValue("O{$grandTotalRow}", '-');
                    $sheet->setCellValue("P{$grandTotalRow}", 0);
                    $sheet->setCellValue("Q{$grandTotalRow}", 0);
                    $sheet->setCellValue("R{$grandTotalRow}", '-');
                }

                $sheet->getStyle("A{$grandTotalRow}:R{$grandTotalRow}")->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 10, 'bold' => true, 'color' => ['rgb' => '0F172A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475569']],
                        'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                        'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                    ],
                ]);

                $sheet->getStyle("A{$grandTotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("N{$grandTotalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'font' => ['color' => ['rgb' => '047857']],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0;("Rp "#,##0);"-"'],
                ]);
                $sheet->getStyle("O{$grandTotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("P{$grandTotalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0;("Rp "#,##0);"-"'],
                ]);
                $sheet->getStyle("Q{$grandTotalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'font' => ['color' => ['rgb' => '1D4ED8']],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0;("Rp "#,##0);"-"'],
                ]);
                $sheet->getStyle("R{$grandTotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // --- 6. FORMAL SIGNATURE BLOCK ---
                $signStartRow = $grandTotalRow + 3;

                // Left signature: Kepala Sekolah (Cols B:D)
                $sheet->mergeCells("B{$signStartRow}:D{$signStartRow}");
                $sheet->setCellValue("B{$signStartRow}", 'Mengetahui,');
                $sheet->mergeCells("B" . ($signStartRow + 1) . ":D" . ($signStartRow + 1));
                $sheet->setCellValue("B" . ($signStartRow + 1), 'Kepala SMK Telkom Lampung');

                $sheet->mergeCells("B" . ($signStartRow + 4) . ":D" . ($signStartRow + 4));
                $sheet->setCellValue("B" . ($signStartRow + 4), '( ............................................. )');
                $sheet->mergeCells("B" . ($signStartRow + 5) . ":D" . ($signStartRow + 5));
                $sheet->setCellValue("B" . ($signStartRow + 5), 'NIP. .....................................');

                // Right signature: Pengelola Sarpras (Cols O:Q)
                $todayFormatted = Carbon::now()->translatedFormat('d F Y');
                $sheet->mergeCells("O{$signStartRow}:Q{$signStartRow}");
                $sheet->setCellValue("O{$signStartRow}", "Pringsewu, {$todayFormatted}");
                $sheet->mergeCells("O" . ($signStartRow + 1) . ":Q" . ($signStartRow + 1));
                $sheet->setCellValue("O" . ($signStartRow + 1), 'Kaur Sarpras / Pengelola Aset');

                $sheet->mergeCells("O" . ($signStartRow + 4) . ":Q" . ($signStartRow + 4));
                $sheet->setCellValue("O" . ($signStartRow + 4), '( ............................................. )');
                $sheet->mergeCells("O" . ($signStartRow + 5) . ":Q" . ($signStartRow + 5));
                $sheet->setCellValue("O" . ($signStartRow + 5), 'NIP. .....................................');

                $sheet->getStyle("B{$signStartRow}:D" . ($signStartRow + 5))->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 9.5],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("O{$signStartRow}:Q" . ($signStartRow + 5))->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 9.5],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- 7. COLUMN WIDTHS & READABILITY ---
                $columnWidths = [
                    'A' => 6,   // NO
                    'B' => 19,  // KODE YPT
                    'C' => 12,  // NO URUT
                    'D' => 30,  // NAMA BARANG
                    'E' => 26,  // SPESIFIKASI
                    'F' => 20,  // KATEGORI
                    'G' => 16,  // GEDUNG
                    'H' => 18,  // RUANGAN
                    'I' => 18,  // UNIT / PRODI
                    'J' => 24,  // PIC
                    'K' => 12,  // THN BELI
                    'L' => 16,  // KONDISI FISIK
                    'M' => 18,  // STATUS OPERASIONAL
                    'N' => 22,  // HARGA AKUISISI
                    'O' => 14,  // MASA MANFAAT
                    'P' => 20,  // NILAI RESIDU
                    'Q' => 24,  // ESTIMASI NILAI BUKU
                    'R' => 18,  // SUMBER DANA
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // --- 8. VIEW & PRINT CONFIGURATION ---
                // Freeze Panes below table header so column names stay locked when scrolling
                $sheet->freezePane("A{$this->dataStartRow}");

                // Ensure Gridlines are visible
                $sheet->setShowGridLines(true);

                // Print settings: Landscape, A4, Fit to 1 page wide
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($this->headerRow, $this->headerRow);
            },
        ];
    }

    protected function applyBoxBorder(Worksheet $sheet, string $range, string $hexColor): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => $hexColor],
                ],
            ],
        ]);
    }
}
