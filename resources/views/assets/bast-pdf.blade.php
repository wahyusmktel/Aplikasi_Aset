<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
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
            font-size: 18px;
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
        <h1>{{ $title }}</h1>
        <p>Nomor: {{ $assignment->checkout_doc_number ?? $assignment->return_doc_number }}</p>
    </div>

    <div class="content">
        <div class="info">
            Pada hari ini, {{ \Carbon\Carbon::parse($assignment->assigned_date)->isoFormat('dddd, D MMMM YYYY') }},
            telah dilakukan serah terima aset dari {{ $isReturn ? 'Peminjam' : 'Penanggung Jawab Aset' }} kepada
            {{ $isReturn ? 'Penanggung Jawab Aset' : 'Peminjam' }} dengan rincian sebagai berikut:
        </div>

        <table>
            <tr>
                <th colspan="2">Pihak yang Terlibat</th>
            </tr>
            <tr>
                <td width="50%">
                    <strong>{{ $isReturn ? 'Yang Mengembalikan (Peminjam)' : 'Yang Menyerahkan (Penanggung Jawab)' }}:</strong><br>{{ $asset->personInCharge->name }}
                </td>
                <td><strong>{{ $isReturn ? 'Yang Menerima (Penanggung Jawab)' : 'Yang Menerima (Peminjam)' }}:</strong><br>{{ $employee->name }}
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <th colspan="2">Detail Aset</th>
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
                <td><strong>Kondisi</strong></td>
                <td>{{ $isReturn ? $assignment->condition_on_return : $assignment->condition_on_assign }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <table>
            <tr>
                <td>Mengetahui,<br>Kepala
                    Sekolah<br><br><br><br><br><strong>{{ $headmaster->name ?? '(Nama Kepala Sekolah)' }}</strong></td>
                <td>Menyetujui,<br>Penanggung Jawab
                    Aset<br><br><br><br><br><strong>{{ $asset->personInCharge->name }}</strong></td>
                <td>Yang Menerima,<br>Peminjam<br><br><br><br><br><strong>{{ $employee->name }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="qr-code">
        {{-- $qrCode sudah berisi string "data:image/png;base64,..." --}}
        <img src="{{ $qrCode }}" width="80px" height="80px">
    </div>
</body>

</html>
