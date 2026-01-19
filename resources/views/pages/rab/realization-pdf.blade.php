<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Realisasi Penggunaan Anggaran - {{ $rab->name }}</title>
    <style>
        @page { 
            margin: 40px 0; 
        }
        @page:first {
            margin-top: 0;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #333; 
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .kop-surat img {
            width: 100%;
            display: block;
        }
        .content {
            padding: 0 30px;
        }
        .header { text-align: center; margin-bottom: 15px; text-transform: uppercase; }
        .header h1 { margin: 0; font-size: 12px; display: inline-block; padding-bottom: 2px; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-size: 9px; }
        .info-table td { vertical-align: top; padding: 1px 0; }
        .info-table td.label { width: 120px; font-weight: bold; }
        .info-table td.separator { width: 10px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px; text-align: left; }
        table.data-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 9px; text-align: center; }
        table.data-table .text-right { text-align: right; }
        table.data-table .text-center { text-align: center; }
        
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 15px; page-break-inside: avoid; font-size: 9px; }
        .signature-table th, .signature-table td { border: 1px solid #000; padding: 4px; vertical-align: middle; }
        .signature-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 8px; text-align: center; }
        .signature-table td { height: 30px; }
        .signature-label { font-weight: bold; font-size: 8px; }
        .signature-name { text-align: center; }
        .signature-jabatan { text-align: center; }
        .signature-tanggal { text-align: center; }
        
        .footer-summary {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    @if($kopSurat)
        <div class="kop-surat">
            <img src="{{ public_path('storage/' . $kopSurat) }}" alt="Kop Surat">
        </div>
    @endif

    <div class="content">
        <div class="header">
            <br>
            <h1>LAPORAN REALISASI PENGGUNAAN ANGGARAN</h1>
            <br>
            <h1>{{ $rab->name }}</h1>
            <br>
            <h1>TAHUN ANGGARAN {{ $rab->academicYear->year }}</h1>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">NOMOR AKUN</td>
                <td class="separator">:</td>
                <td>{{ $rab->mta }}</td>
            </tr>
            <tr>
                <td class="label">NAMA AKUN</td>
                <td class="separator">:</td>
                <td>{{ $rab->nama_akun }}</td>
            </tr>                       
            <tr>
                <td class="label">DRK</td>
                <td class="separator">:</td>
                <td>{{ $rab->drk }}</td>
            </tr>
            <tr>
                <td class="label">KEBUTUHAN WAKTU</td>
                <td class="separator">:</td>
                <td>{{ $rab->kebutuhan_waktu }}</td>
            </tr> 
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="20">NO</th>
                    <th width="60">TGL</th>
                    <th>URAIAN</th>
                    <th width="80">PENERIMAAN</th>
                    <th width="80">PENGELUARAN</th>
                    <th width="80">SALDO</th>
                    <th width="100">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $balance = 0;
                    $totalPenerimaan = 0;
                    $totalPengeluaran = 0;
                @endphp
                @foreach($items as $index => $item)
                    @php
                        $balance += ($item['penerimaan'] - $item['pengeluaran']);
                        $totalPenerimaan += $item['penerimaan'];
                        $totalPengeluaran += $item['pengeluaran'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item['tgl'] }}</td>
                        <td>{{ $item['uraian'] }}</td>
                        <td class="text-right">
                            {{ $item['penerimaan'] > 0 ? 'Rp' . number_format($item['penerimaan'], 0, ',', '.') : '' }}
                        </td>
                        <td class="text-right">
                            {{ $item['pengeluaran'] > 0 ? 'Rp' . number_format($item['pengeluaran'], 0, ',', '.') : '' }}
                        </td>
                        <td class="text-right">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="footer-summary">
                    <td colspan="3" class="text-center">JUMLAH</td>
                    <td class="text-right">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <table class="signature-table">
            <thead>
                <tr>
                    <th class="signature-label"></th>
                    <th>NAMA / NIK</th>
                    <th>JABATAN</th>
                    <th>TANGGAL</th>
                    <th>TANDA TANGAN</th>
                </tr>
            </thead>
            <tbody>
                @if($rab->creator)
                <tr>
                    <td class="signature-label">Dibuat oleh</td>
                    <td class="signature-name">{{ $rab->creator->name }} / {{ $rab->creator->nip ?? '-' }}</td>
                    <td class="signature-jabatan">{{ $rab->creator->position ?? '-' }}</td>
                    <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                    <td class="signature-box"></td>
                </tr>
                @endif
                @if($rab->checker)
                <tr>
                    <td class="signature-label">Diperiksa oleh</td>
                    <td class="signature-name">{{ $rab->checker->name }} / {{ $rab->checker->nip ?? '-' }}</td>
                    <td class="signature-jabatan">{{ $rab->checker->position ?? '-' }}</td>
                    <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                    <td class="signature-box"></td>
                </tr>
                @endif
                @if($rab->approver)
                <tr>
                    <td class="signature-label">Diperiksa & Disetujui oleh</td>
                    <td class="signature-name">{{ $rab->approver->name }} / {{ $rab->approver->nip ?? '-' }}</td>
                    <td class="signature-jabatan">{{ $rab->approver->position ?? '-' }}</td>
                    <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                    <td class="signature-box"></td>
                </tr>
                @endif
                @if($rab->headmaster)
                <tr>
                    <td class="signature-label">Diperiksa & Disetujui Realisasi</td>
                    <td class="signature-name">{{ $rab->headmaster->name }} / {{ $rab->headmaster->nip ?? '-' }}</td>
                    <td class="signature-jabatan">Kepala Sekolah</td>
                    <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                    <td class="signature-box"></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>
