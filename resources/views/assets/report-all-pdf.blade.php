<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Aset Aktif {{ $categoryName ? '- Kategori: ' . $categoryName : '' }}</title>
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
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Ukuran font judul disesuaikan */
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        /* Ukuran font subjudul disesuaikan */
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
            margin-top: 30px;
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
            padding-top: 50px;
        }

        /* Jarak tanda tangan dikurangi */
        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
        }

        /* Posisi footer disesuaikan */
        .no-wrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Daftar Aset Aktif</h1>
        {{-- Tampilkan nama kategori jika difilter --}}
        @if ($categoryName)
            <p>Kategori: <strong>{{ $categoryName }}</strong></p>
        @endif
        <p>{{ $activeAssets->first()->institution->name ?? 'Institusi Aset' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th>Kode Aset YPT</th>
                <th>Nama Barang</th>
                <th class="no-wrap">Thn</th> {{-- Singkat --}}
                <th>Gedung</th>
                <th>Ruangan</th>
                <th>Unit</th>
                <th>PJ</th> {{-- Singkat --}}
                <th>Fungsi</th>
                <th>Dana</th> {{-- Singkat --}}
                <th class="no-wrap">No Urut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activeAssets as $index => $asset)
                <tr>
                    <td class="no-wrap">{{ $index + 1 }}</td>
                    <td>{{ $asset->asset_code_ypt ?? '-' }}</td>
                    <td>{{ $asset->name ?? '-' }}</td>
                    <td class="no-wrap">{{ $asset->purchase_year ?? '-' }}</td>
                    <td>{{ $asset->building->name ?? '-' }}</td>
                    <td>{{ $asset->room->name ?? '-' }}</td>
                    <td>{{ $asset->department->name ?? '-' }}</td>
                    <td>{{ $asset->personInCharge->name ?? '-' }}</td>
                    <td>{{ $asset->assetFunction->name ?? '-' }}</td>
                    <td>{{ $asset->fundingSource->name ?? '-' }}</td>
                    <td class="no-wrap">{{ $asset->sequence_number ?? '-' }}</td>
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
