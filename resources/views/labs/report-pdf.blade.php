<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penggunaan Lab</title>
    <style>
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
            font-size: 14px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
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
            width: 33%;
            padding-top: 50px;
        }

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Log Penggunaan Laboratorium</h1>
        <p>Ruangan: {{ $labName }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} s/d
            {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px">No</th>
                <th style="width: 60px">Tanggal</th>
                <th>Guru / PJ</th>
                <th>Kelas</th>
                <th>Kegiatan / Materi</th>
                <th style="width: 60px">Waktu</th>
                <th style="width: 60px">Kondisi Awal</th>
                <th style="width: 60px">Kondisi Akhir</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->usage_date)->isoFormat('DD/MM/YY') }}</td>
                    <td>{{ $log->teacher->name }}</td>
                    <td>{{ $log->class_group }}</td>
                    <td>{{ $log->activity_description }}</td>
                    <td>{{ $log->check_in_time->format('H:i') }} -
                        {{ $log->check_out_time ? $log->check_out_time->format('H:i') : '...' }}</td>
                    <td>{{ $log->condition_before }}</td>
                    <td>{{ $log->condition_after ?? '-' }}</td>
                    <td>{{ $log->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <table>
            <tr>
                <td><br>Mengetahui,<br>Kepala
                    Sekolah<br><br><br><br><br><strong>{{ $ks->name ?? '.........................' }}</strong></td>
                <td><br>Kaur Sarana
                    Prasarana<br><br><br><br><br><strong>{{ $pj->name ?? '.........................' }}</strong></td>
                <td>Bandar Lampung, {{ date('d F Y') }}<br>Kepala
                    Lab/Teknisi<br><br><br><br><br><strong>.........................</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak dari Sistem Manajemen Aset SMK Telkom Lampung
    </div>
</body>

</html>
