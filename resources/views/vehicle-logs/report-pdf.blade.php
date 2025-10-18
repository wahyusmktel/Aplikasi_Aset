<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Log Kendaraan</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            /* Ukuran font lebih kecil */
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
            /* Memastikan teks panjang tidak keluar tabel */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
            /* Mencegah tanda tangan terpotong */
        }

        .signatures table {
            width: 100%;
            border: none;
            /* Hapus border untuk tabel tanda tangan */
        }

        .signatures td {
            border: none;
            /* Hapus border untuk sel tanda tangan */
            text-align: center;
            width: 50%;
            /* Bagi dua kolom */
            padding-top: 60px;
            /* Jarak untuk tanda tangan */
            vertical-align: bottom;
            /* Teks rata bawah */
        }

        .footer {
            position: fixed;
            bottom: -20px;
            /* Sesuaikan jika perlu */
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #777;
        }

        .no-wrap {
            white-space: nowrap;
            /* Mencegah teks terpotong */
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Rekapitulasi Penggunaan Kendaraan Dinas</h1>
        {{-- Mengambil nama institusi dari data pertama --}}
        <p>{{ $logs->first()->asset->institution->name ?? 'Institusi Aset' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th>Kendaraan</th>
                <th>Pegawai</th>
                <th>Tujuan</th>
                <th>Keperluan</th>
                <th class="no-wrap">Berangkat</th>
                <th class="no-wrap">Kembali</th>
                <th class="text-right">KM Awal</th>
                <th class="text-right">KM Akhir</th>
                <th class="text-right">Jarak (KM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $index => $log)
                <tr>
                    <td class="no-wrap">{{ $index + 1 }}</td>
                    <td>{{ $log->asset->name ?? '-' }}</td>
                    <td>{{ $log->employee->name ?? '-' }}</td>
                    <td>{{ $log->destination }}</td>
                    <td>{{ $log->purpose }}</td>
                    <td class="no-wrap">
                        {{ $log->departure_time ? \Carbon\Carbon::parse($log->departure_time)->isoFormat('DD/MM/YY HH:mm') : '-' }}
                    </td>
                    <td class="no-wrap">
                        {{ $log->return_time ? \Carbon\Carbon::parse($log->return_time)->isoFormat('DD/MM/YY HH:mm') : '-' }}
                    </td>
                    <td class="text-right">{{ number_format($log->start_odometer, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $log->end_odometer ? number_format($log->end_odometer, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">
                        {{ $log->end_odometer ? number_format($log->end_odometer - $log->start_odometer, 0, ',', '.') : '-' }}
                    </td>
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
