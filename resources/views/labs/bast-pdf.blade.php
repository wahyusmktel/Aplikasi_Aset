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
            border-collapse: collapse;
            border: 1px dotted #999;
        }

        .signatures td {
            border: 1px dotted #999;
            text-align: center;
            width: 30%;
            padding: 10px 8px;
            vertical-align: top;
        }

        .qr-cell { border: 1px dotted #999; width: 10%; text-align: center; vertical-align: middle; padding: 8px; }
        .sign-img-wrap { height: 52px; display: flex; align-items: center; justify-content: center; margin: 4px auto; }
        .sign-img-wrap img { max-height: 48px; max-width: 110px; object-fit: contain; }
        .sign-space { height: 52px; }
        .digital-badge { font-size: 7px; color: #4f46e5; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 4px; padding: 1px 4px; margin-top: 2px; display: inline-block; }

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

    @php
        use App\Models\UserDigitalSignature;
        $headmasterUser = $headmaster?->user ?? null;
        $headmasterSig  = $headmasterUser ? UserDigitalSignature::where('user_id', $headmasterUser->id)->first() : null;
        $kaurUser       = $kaurLab?->user ?? null;
        $kaurSig        = $kaurUser ? UserDigitalSignature::where('user_id', $kaurUser->id)->first() : null;
        $teacherUser    = $log->teacher?->user ?? null;
        $teacherSig     = $teacherUser ? UserDigitalSignature::where('user_id', $teacherUser->id)->first() : null;
    @endphp

    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                @if($headmasterSig && $headmasterSig->ttd_image_path)
                    <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $headmasterSig->ttd_image_path) }}"></div>
                    <div class="digital-badge">&#10003; TTD Digital</div>
                @else
                    <div class="sign-space"></div>
                @endif
                <strong>{{ $headmaster->name ?? '(................)' }}</strong>
            </td>
            <td>
                Menyetujui,<br>Ka. Lab / Sarpras
                @if($kaurSig && $kaurSig->ttd_image_path)
                    <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $kaurSig->ttd_image_path) }}"></div>
                    <div class="digital-badge">&#10003; TTD Digital</div>
                @else
                    <div class="sign-space"></div>
                @endif
                <strong>{{ $kaurLab->name ?? '(................)' }}</strong>
            </td>
            <td>
                Yang Menggunakan,<br>Guru Mata Pelajaran
                @if($teacherSig && $teacherSig->ttd_image_path)
                    <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $teacherSig->ttd_image_path) }}"></div>
                    <div class="digital-badge">&#10003; TTD Digital</div>
                @else
                    <div class="sign-space"></div>
                @endif
                <strong>{{ $log->teacher->name }}</strong>
            </td>
            <td class="qr-cell">
                <img src="{{ $qrCode }}" width="65" height="65">
                <div style="font-size:7px; color:#666; margin-top:2px;">Scan verifikasi</div>
            </td>
        </tr>
    </table>
</body>

</html>
