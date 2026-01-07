<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rencana Anggaran Biaya - {{ $rab->name }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
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
            padding: 20px 40px;
        }
        .header { text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        .header h1 { margin: 0; font-size: 14px; display: inline-block; padding-bottom: 5px; }
        .header p { margin: 5px 0 0; font-size: 12px; font-weight: bold; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; padding: 2px 0; }
        .info-table td.label { width: 140px; font-weight: bold; }
        .info-table td.separator { width: 10px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 10px; text-align: center; }
        table.data-table .text-right { text-align: right; }
        table.data-table .text-center { text-align: center; }
        
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; font-size: 10px; }
        .signature-table th, .signature-table td { border: 1px solid #000; padding: 5px 6px; vertical-align: middle; }
        .signature-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 9px; text-align: center; }
        .signature-table td { height: 35px; }
        .signature-label { font-weight: bold; font-size: 9px; }
        .signature-name { text-align: center; }
        .signature-jabatan { text-align: center; }
        .signature-tanggal { text-align: center; }
        .signature-box { }
        
        .notes-section { 
            margin-top: 15px; 
            border: 1px dotted #000; 
            padding: 10px; 
            min-height: 50px; 
        }
        .notes-section strong { 
            text-transform: uppercase; 
            font-size: 10px; 
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
            <h1>RENCANA ANGGARAN BIAYA (RAB)</h1>
            <br>
            <h1>{{ $rab->name }}</h1>
            <br>
            <h1>TAHUN ANGGARAN {{ $rab->academicYear->year }}</h1>
        </div>

        <table class="info-table">
            <!-- <tr>
                <td class="label">TAHUN ANGGARAN</td>
                <td class="separator">:</td>
                <td>{{ $rab->academicYear->year }}</td>
            </tr> -->
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
                    <th>URAIAN KEGIATAN</th>
                    <th>SPESIFIKASI</th>                    
                    <th width="50">SATUAN</th>
                    <th width="40">VOL</th>
                    <th width="80">HARGA</th>
                    <th width="90">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rab->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->alias_name }}</td>
                        <td>{{ $detail->specification ?? '-' }}</td>                        
                        <td class="text-center">{{ $detail->unit }}</td>
                        <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="6" style="text-align: right;">TOTAL ANGGARAN</td>
                    <td class="text-right">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</td>
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
                <tr>
                    <td colspan="5" style="text-align: left; padding: 8px; height: 50px; vertical-align: top;">
                        <strong style="text-transform: uppercase; font-size: 9px;">Catatan Anggaran:</strong><br>
                        {{ $rab->notes ?? '-' }}
                    </td>
                </tr>
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

        <div class="notes-section">
            <strong>CATATAN:</strong><br>
            -
        </div>
    </div>
</body>
</html>
