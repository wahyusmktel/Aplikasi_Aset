<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pemeriksaan Kondisi Aset</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0 0 0;
            font-size: 14px;
        }

        .content {
            margin-top: 30px;
        }

        .content .info {
            margin-bottom: 20px;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .content th,
        .content td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .content th {
            background-color: #f2f2f2;
        }

        .signatures {
            margin-top: 50px;
            width: 100%;
        }

        .signatures table {
            width: 100%;
            border: none;
        }

        .signatures td {
            border: none;
            text-align: center;
            width: 33.33%;
            padding-top: 70px;
        }

        /* Space for signature */
        .qr-code {
            position: absolute;
            bottom: 20px;
            left: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Berita Acara Pemeriksaan Kondisi Aset</h1>
        <p>Nomor: {{ $inspection->inspection_doc_number }}</p>
    </div>

    <div class="content">
        <div class="info">
            Pada hari ini, {{ \Carbon\Carbon::parse($inspection->inspection_date)->isoFormat('dddd, D MMMM YYYY') }},
            telah dilakukan pemeriksaan kondisi aset dengan rincian sebagai berikut:
        </div>

        <table>
            <tr>
                <th colspan="2">Detail Aset yang Diperiksa</th>
            </tr>
            <tr>
                <td width="30%"><strong>Nama Aset</strong></td>
                <td>{{ $asset->name }}</td>
            </tr>
            <tr>
                <td><strong>Kode Aset YPT</strong></td>
                <td>{{ $asset->asset_code_ypt }}</td>
            </tr>
            <tr>
                <td><strong>Lokasi</strong></td>
                <td>{{ $asset->building->name ?? '' }} / {{ $asset->room->name ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Penanggung Jawab Aset</strong></td>
                <td>{{ $asset->personInCharge->name ?? '-' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th colspan="2">Hasil Pemeriksaan</th>
            </tr>
            <tr>
                <td width="30%"><strong>Tanggal Pemeriksaan</strong></td>
                <td>{{ \Carbon\Carbon::parse($inspection->inspection_date)->isoFormat('D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <td><strong>Kondisi Ditemukan</strong></td>
                <td><strong>{{ $inspection->condition }}</strong></td>
            </tr>
            <tr>
                <td><strong>Catatan / Keterangan</strong></td>
                <td>{{ $inspection->notes ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Diperiksa Oleh</strong></td>
                <td>{{ $inspector->name ?? 'Sistem' }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <table>
            <tr>
                <td>Mengetahui,<br>Kepala
                    Sekolah<br><br><br><br><br><br><strong>{{ $headmaster->name ?? '(Nama Kepala Sekolah)' }}</strong>
                </td>
                <td>Menyetujui,<br>Penanggung Jawab
                    Aset<br><br><br><br><br><br><strong>{{ $asset->personInCharge->name ?? '(Nama PJ Aset)' }}</strong>
                </td>
                <td>Yang Melakukan
                    Pemeriksaan,<br>Pemeriksa<br><br><br><br><br><br><strong>{{ $inspector->name ?? 'Sistem' }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="qr-code">
        <img src="{{ $qrCode }}" width="80px" height="80px">
    </div>
</body>

</html>
