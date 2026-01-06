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
                <th width="30">NO</th>
                <th>URAIAN KEGIATAN</th>
                <th width="50">VOL</th>
                <th width="60">SATUAN</th>
                <th width="90">HARGA</th>
                <th width="100">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rab->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->alias_name }}</td>
                    <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $detail->unit }}</td>
                    <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTAL ANGGARAN</td>
                <td class="text-right">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="notes-section">
        <h4>CATATAN:</h4>
        <p>{{ $rab->notes ?? '-' }}</p>
    </div>

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td>
                    <p class="signature-role">Dibuat Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rab->creator->name }}</p>
                    <p class="signature-role">Pegawai</p>
                </td>
                <td>
                    <p class="signature-role">Diperiksa Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rab->checker->name }}</p>
                    <p class="signature-role">Verifikator</p>
                </td>
                <td>
                    <p class="signature-role">Disetujui Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rab->approver->name }}</p>
                    <p class="signature-role">Pejabat Berwenang</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <p style="margin-top: 20px;" class="signature-role">Mengetahui,</p>
                    <p class="signature-role">Kepala Sekolah</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rab->headmaster->name }}</p>
                    <p class="signature-role">NIP. {{ $rab->headmaster->nip ?? '-' }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
