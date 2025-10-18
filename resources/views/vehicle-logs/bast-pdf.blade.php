<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        /* CSS sama seperti bast-pdf sebelumnya */
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
        <h1>{{ $title }}</h1>
        <p>Nomor: {{ $isCheckin ? $log->checkin_doc_number : $log->checkout_doc_number }}</p>
    </div>

    <div class="content">
        <div class="info">
            Pada hari ini,
            {{ ($isCheckin ? $log->return_time : $log->departure_time)->isoFormat('dddd, D MMMM YYYY') }}, telah
            dilakukan {{ $isCheckin ? 'pengembalian' : 'penggunaan' }} kendaraan dinas operasional SMK Telkom Lampung
            dengan rincian sebagai berikut:
        </div>

        <table>
            <tr>
                <th colspan="2">Pihak yang Terlibat</th>
            </tr>
            <tr>
                <td width="50%">
                    <strong>{{ $isCheckin ? 'Yang Mengembalikan (Pengguna)' : 'Yang Menyerahkan (Petugas)' }}:</strong><br>{{ $employee->name }}
                </td>
                <td><strong>{{ $isCheckin ? 'Yang Menerima (Petugas)' : 'Yang Menerima (Pengguna)' }}:</strong><br>{{ $asset->personInCharge->name ?? 'Petugas Aset' }}
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <th colspan="2">Detail Kendaraan</th>
            </tr>
            <tr>
                <td width="30%"><strong>Nama Kendaraan</strong></td>
                <td>{{ $asset->name }}</td>
            </tr>
            <tr>
                <td><strong>Kode Aset YPT</strong></td>
                <td>{{ $asset->asset_code_ypt }}</td>
            </tr>
            <tr>
                <td><strong>Nomor Polisi</strong></td>
                <td>{{-- Tambahkan field nomor polisi jika ada --}}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th colspan="2">Detail Penggunaan</th>
            </tr>
            <tr>
                <td width="30%"><strong>Tujuan</strong></td>
                <td>{{ $log->destination }}</td>
            </tr>
            <tr>
                <td><strong>Keperluan</strong></td>
                <td>{{ $log->purpose }}</td>
            </tr>
            <tr>
                <td><strong>Waktu {{ $isCheckin ? 'Kembali' : 'Berangkat' }}</strong></td>
                <td>{{ ($isCheckin ? $log->return_time : $log->departure_time)->isoFormat('D MMM YYYY, HH:mm') }}</td>
            </tr>
            <tr>
                <td><strong>Kilometer {{ $isCheckin ? 'Akhir' : 'Awal' }}</strong></td>
                <td>{{ number_format($isCheckin ? $log->end_odometer : $log->start_odometer) }} KM</td>
            </tr>
            @if ($isCheckin && $log->end_odometer)
                <tr>
                    <td><strong>Jarak Tempuh</strong></td>
                    <td>{{ number_format($log->end_odometer - $log->start_odometer) }} KM</td>
                </tr>
            @endif
            <tr>
                <td><strong>Kondisi {{ $isCheckin ? 'Akhir' : 'Awal' }}</strong></td>
                <td>{{ $isCheckin ? $log->condition_on_checkin : $log->condition_on_checkout }}</td>
            </tr>
            @if ($isCheckin && $log->notes)
                <tr>
                    <td><strong>Catatan Pengembalian</strong></td>
                    <td>{{ $log->notes }}</td>
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
                <td>Petugas
                    Aset,<br><br><br><br><br><br><strong>{{ $asset->personInCharge->name ?? '(Nama Petugas)' }}</strong>
                </td>
                <td>Pengguna,<br><br><br><br><br><br><strong>{{ $employee->name }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="qr-code">
        <img src="{{ $qrCode }}" width="80px" height="80px">
    </div>
</body>

</html>
