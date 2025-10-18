<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Riwayat Inventaris</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
        }

        /* Ukuran font lebih kecil */
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

        /* Tinggi untuk tanda tangan */
        .footer {
            position: fixed;
            bottom: 0;
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
        <h1>Laporan Rekapitulasi Riwayat Inventaris Aset</h1>
        <p>{{ $assignments->first()->asset->institution->name ?? 'Institusi Aset' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th>Nama Aset</th>
                <th>Kode Aset</th>
                <th>Pegawai</th>
                <th class="no-wrap">Tgl Pinjam</th>
                <th>Kondisi Pinjam</th>
                <th class="no-wrap">Tgl Kembali</th>
                <th>Kondisi Kembali</th>
                <th>No Surat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assignments as $index => $assignment)
                <tr>
                    <td class="no-wrap">{{ $index + 1 }}</td>
                    <td>{{ $assignment->asset->name ?? '-' }}</td>
                    <td>{{ $assignment->asset->asset_code_ypt ?? '-' }}</td>
                    <td>{{ $assignment->employee->name ?? '-' }}</td>
                    <td class="no-wrap">{{ \Carbon\Carbon::parse($assignment->assigned_date)->isoFormat('DD/MM/YY') }}
                    </td>
                    <td>{{ $assignment->condition_on_assign }}</td>
                    <td class="no-wrap">
                        {{ $assignment->returned_date ? \Carbon\Carbon::parse($assignment->returned_date)->isoFormat('DD/MM/YY') : 'Dipinjam' }}
                    </td>
                    <td>{{ $assignment->condition_on_return ?? '-' }}</td>
                    <td>{{ $assignment->return_doc_number ?? ($assignment->checkout_doc_number ?? '-') }}</td>
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
