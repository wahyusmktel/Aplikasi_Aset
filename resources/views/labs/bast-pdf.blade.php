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
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .data-table td {
            border-bottom: 1px solid #ddd;
        }

        .signatures {
            margin-top: 50px;
            width: 100%;
        }

        .signatures td {
            text-align: center;
            width: 33%;
            padding-top: 60px;
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
        <p>Nomor: {{ $docNumber }}</p>
    </div>

    <p>Pada hari ini,
        <strong>{{ \Carbon\Carbon::parse($isCheckout ? $log->usage_date : $log->usage_date)->isoFormat('dddd, D MMMM YYYY') }}</strong>,
        telah dilakukan pencatatan penggunaan fasilitas laboratorium SMK Telkom Lampung dengan rincian sebagai berikut:
    </p>

    <table class="data-table">
        <tr>
            <td width="30%"><strong>Ruangan Lab</strong></td>
            <td>: {{ $log->room->name }}</td>
        </tr>
        <tr>
            <td><strong>Guru / Penanggung Jawab</strong></td>
            <td>: {{ $log->teacher->name }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ $log->class_group }}</td>
        </tr>
        <tr>
            <td><strong>Kegiatan / Materi</strong></td>
            <td>: {{ $log->activity_description }}</td>
        </tr>

        @if (!$isCheckout)
            {{-- Data Masuk --}}
            <tr>
                <td><strong>Waktu Masuk</strong></td>
                <td>: {{ $log->check_in_time->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Kondisi Awal</strong></td>
                <td>: {{ $log->condition_before }}</td>
            </tr>
        @else
            {{-- Data Keluar --}}
            <tr>
                <td><strong>Waktu Masuk</strong></td>
                <td>: {{ $log->check_in_time->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Waktu Selesai</strong></td>
                <td>: {{ $log->check_out_time->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Kondisi Akhir</strong></td>
                <td>: {{ $log->condition_after }}</td>
            </tr>
            <tr>
                <td><strong>Catatan Kejadian</strong></td>
                <td>: {{ $log->notes ?? '-' }}</td>
            </tr>
        @endif
    </table>

    <p style="margin-top: 20px;">Demikian berita acara ini dibuat untuk dipergunakan sebagaimana mestinya sebagai bukti
        akuntabilitas penggunaan aset sekolah.</p>

    <table class="signatures">
        <tr>
            <td>Mengetahui,<br>Kepala
                Sekolah<br><br><br><br><strong>{{ $headmaster->name ?? '(................)' }}</strong></td>
            <td>Menyetujui,<br>Ka. Lab /
                Sarpras<br><br><br><br><strong>{{ $kaurLab->name ?? '(................)' }}</strong></td>
            <td>Yang Menggunakan,<br>Guru Mata Pelajaran<br><br><br><br><strong>{{ $log->teacher->name }}</strong></td>
        </tr>
    </table>

    <div class="qr-code">
        <img src="{{ $qrCode }}" width="80px" height="80px">
    </div>
</body>

</html>
