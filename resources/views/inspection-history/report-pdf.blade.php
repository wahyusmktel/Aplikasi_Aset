<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Inspeksi Aset</title>
    <style>
        /* CSS sama seperti report-pdf maintenance */
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signatures table {
            width: 100%;
            border: none;
        }

        .signatures td {
            border: none;
            text-align: center;
            width: 50%;
            padding-top: 60px;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
        }

        .no-wrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Rekapitulasi Pemeriksaan Kondisi Aset</h1>
        <p>{{ $inspections->first()->asset->institution->name ?? 'Institusi Aset' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th class="no-wrap">Tanggal</th>
                <th>Nama Aset</th>
                <th>Kode Aset</th>
                <th>Kondisi</th>
                <th>Catatan</th>
                <th>Diperiksa Oleh</th>
                <th>No Surat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspections as $index => $inspection)
                <tr>
                    <td class="no-wrap">{{ $index + 1 }}</td>
                    <td class="no-wrap">{{ \Carbon\Carbon::parse($inspection->inspection_date)->isoFormat('DD/MM/YY') }}
                    </td>
                    <td>{{ $inspection->asset->name ?? '-' }}</td>
                    <td>{{ $inspection->asset->asset_code_ypt ?? '-' }}</td>
                    <td>{{ $inspection->condition }}</td>
                    <td>{{ $inspection->notes ?? '-' }}</td>
                    <td>{{ $inspection->inspector->name ?? 'Sistem' }}</td>
                    <td>{{ $inspection->inspection_doc_number ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <table>
            <tr>
                <td>{{ $kota ?? 'Kota' }}, {{ date('d F Y') }}<br>Penanggung
                    Jawab,<br><br><br><br><br><strong>{{ $pj->name ?? '(Nama Penanggung Jawab)' }}</strong></td>
                <td><br>Mengetahui,<br>Kepala
                    Sekolah<br><br><br><br><br><strong>{{ $ks->name ?? '(Nama Kepala Sekolah)' }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak dari Sistem Manajemen Aset
    </div>
</body>

</html>
