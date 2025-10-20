<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Aset Aktif</title>
    <style>
        /* CSS mirip report-pdf maintenance/disposal */
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
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

        .currency {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Daftar Aset Aktif</h1>
        <p>{{ $activeAssets->first()->institution->name ?? 'Institusi Aset' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th>Kode Aset</th>
                <th>Nama Barang</th>
                <th class="no-wrap">Tahun</th>
                <th class="currency">Harga Beli</th>
                <th class="currency">Nilai Buku</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>PJ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activeAssets as $index => $asset)
                <tr>
                    <td class="no-wrap">{{ $index + 1 }}</td>
                    <td>{{ $asset->asset_code_ypt ?? '-' }}</td>
                    <td>{{ $asset->name ?? '-' }}</td>
                    <td class="no-wrap">{{ $asset->purchase_year ?? '-' }}</td>
                    <td class="currency">{{ number_format($asset->purchase_cost ?? 0, 0, ',', '.') }}</td>
                    <td class="currency">{{ number_format($asset->book_value ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $asset->category->name ?? '-' }}</td>
                    <td>{{ $asset->building->name ?? '' }} / {{ $asset->room->name ?? '' }}</td>
                    <td>{{ $asset->current_status ?? '-' }}</td>
                    <td>{{ $asset->personInCharge->name ?? '-' }}</td>
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
