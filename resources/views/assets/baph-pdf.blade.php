<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penghapusan Aset</title>
    <style>
        /* CSS mirip BAST, sesuaikan jika perlu */
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

        .qr-code {
            position: absolute;
            bottom: 20px;
            left: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Berita Acara Penghapusan Aset</h1>
        <p>Nomor: {{ $asset->disposal_doc_number }}</p>
    </div>

    <div class="content">
        <div class="info">
            Pada hari ini, {{ \Carbon\Carbon::parse($asset->disposal_date)->isoFormat('dddd, D MMMM YYYY') }}, telah
            dilakukan proses penghapusan aset dari daftar inventaris SMK Telkom Lampung dengan rincian sebagai berikut:
        </div>

        <table>
            <tr>
                <th colspan="2">Detail Aset yang Dihapus</th>
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
                <td><strong>Kategori</strong></td>
                <td>{{ $asset->category->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tahun Perolehan</strong></td>
                <td>{{ $asset->purchase_year }}</td>
            </tr>
            <tr>
                <td><strong>Lokasi Terakhir</strong></td>
                <td>{{ $asset->building->name ?? '' }} / {{ $asset->room->name ?? '' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th colspan="2">Detail Penghapusan</th>
            </tr>
            <tr>
                <td width="30%"><strong>Tanggal Penghapusan</strong></td>
                <td>{{ \Carbon\Carbon::parse($asset->disposal_date)->isoFormat('D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <td><strong>Metode Penghapusan</strong></td>
                <td><strong>{{ $asset->disposal_method }}</strong></td>
            </tr>
            <tr>
                <td><strong>Alasan Penghapusan</strong></td>
                <td>{{ $asset->disposal_reason ?? '-' }}</td>
            </tr>
            @if ($asset->disposal_method == 'Dijual')
                <tr>
                    <td><strong>Nilai Jual (Rp)</strong></td>
                    <td>{{ number_format($asset->disposal_value ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endif
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
                <td>Yang
                    Mengajukan,<br>{{-- Bisa diisi nama user yg login atau PJ Aset --}}<br><br><br><br><br><br><strong>{{ $disposer->name ?? '(Nama Pengaju)' }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="qr-code">
        <img src="{{ $qrCode }}" width="80px" height="80px">
    </div>
</body>

</html>
