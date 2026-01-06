<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rencana Anggaran Biaya - {{ $rab->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        .header h1 { margin: 0; font-size: 16px; border-bottom: 2px solid #000; display: inline-block; padding-bottom: 5px; }
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
        
        .notes-section { margin-top: 10px; border: 1px solid #ccc; padding: 10px; min-height: 50px; }
        .notes-section h4 { margin: 0 0 5px; text-transform: uppercase; font-size: 10px; color: #666; }
        
        .signature-container { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .signature-table { width: 100%; table-layout: fixed; }
        .signature-table td { text-align: center; padding-bottom: 20px; }
        .signature-space { height: 60px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
        .signature-role { font-size: 9px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RENCANA ANGGARAN BIAYA (RAB)</h1>
        <p>{{ $rab->name }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">TAHUN ANGGARAN</td>
            <td class="separator">:</td>
            <td>{{ $rab->academicYear->year }}</td>
        </tr>
        <tr>
            <td class="label">KEBUTUHAN WAKTU</td>
            <td class="separator">:</td>
            <td>{{ $rab->kebutuhan_waktu }}</td>
        </tr>
        <tr>
            <td class="label">NOMOR AKUN (MTA)</td>
            <td class="separator">:</td>
            <td>{{ $rab->mta }}</td>
        </tr>
        <tr>
            <td class="label">NAMA AKUN</td>
            <td class="separator">:</td>
            <td>{{ $rab->nama_akun }}</td>
        </tr>
        <tr>
            <td class="label">D R K</td>
            <td class="separator">:</td>
            <td>{{ $rab->drk }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="20">NO</th>
                <th>URAIAN KEGIATAN</th>
                <th>SPESIFIKASI</th>
                <th width="40">VOL</th>
                <th width="50">SATUAN</th>
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
                    <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $detail->unit }}</td>
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



    <style>
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; }
        .signature-table th, .signature-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        .signature-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 9px; text-align: center; }
        .signature-table td { height: 40px; }
        .signature-label { width: 150px; font-weight: bold; }
        .signature-name { width: 200px; text-align: center; }
        .signature-jabatan { width: 150px; text-align: center; }
        .signature-tanggal { width: 80px; text-align: center; }
        .signature-box { width: 100px; }
        .notes-row { height: 60px; vertical-align: top !important; }
    </style>

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
            <tr>
                <td class="signature-label">Dibuat oleh</td>
                <td class="signature-name">{{ $rab->creator->name }} / {{ $rab->creator->nip ?? '-' }}</td>
                <td class="signature-jabatan">{{ $rab->creator->position ?? '-' }}</td>
                <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                <td class="signature-box"></td>
            </tr>
            <tr>
                <td class="signature-label">Diperiksa oleh</td>
                <td class="signature-name">{{ $rab->checker->name }} / {{ $rab->checker->nip ?? '-' }}</td>
                <td class="signature-jabatan">{{ $rab->checker->position ?? '-' }}</td>
                <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                <td class="signature-box"></td>
            </tr>
            <tr>
                <td colspan="5" class="notes-row">
                    <strong>Catatan Anggaran:</strong><br>
                    {{ $rab->notes ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="signature-label">Diperiksa & Disetujui oleh</td>
                <td class="signature-name">{{ $rab->approver->name }} / {{ $rab->approver->nip ?? '-' }}</td>
                <td class="signature-jabatan">{{ $rab->approver->position ?? '-' }}</td>
                <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                <td class="signature-box"></td>
            </tr>
            <tr>
                <td class="signature-label">Diperiksa & Disetujui Realisasi</td>
                <td class="signature-name">{{ $rab->headmaster->name }} / {{ $rab->headmaster->nip ?? '-' }}</td>
                <td class="signature-jabatan">Kepala Sekolah</td>
                <td class="signature-tanggal">{{ $rab->created_at->format('d-M-y') }}</td>
                <td class="signature-box"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
