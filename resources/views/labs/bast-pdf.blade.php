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

        .signatures table {
            width: 100%;
            border-collapse: collapse;
            border: 1px dotted #999;
        }

        .signatures td {
            border: 1px dotted #999;
            text-align: center;
            width: 33.33%;
            padding: 10px 8px;
            vertical-align: top;
        }

        .sign-label { font-size: 10px; margin-bottom: 4px; }
        .sign-qr { margin: 6px auto 2px; text-align: center; }
        .sign-qr img { width: 72px; height: 72px; }
        .sign-space { height: 70px; }
        .sign-name { font-weight: bold; text-decoration: underline; font-size: 10px; margin-top: 4px; }
        .sign-role { font-size: 8px; color: #666; margin-top: 1px; }
        .sign-footer { margin-top: 10px; font-size: 8px; color: #555; border-top: 1px solid #e0e0e0; padding-top: 6px; }
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
        use App\Models\DigitalDocument;

        $docType     = $isCheckout ? 'BAST_LAB_CHECKOUT' : 'BAST_LAB_CHECKIN';
        $docTitle    = 'BAST Lab #' . $docNumber;
        $refId       = (string) $log->id . '_' . ($isCheckout ? 'out' : 'in');
        $hashBase    = [$docType, $refId, $docNumber];

        $kepsekData  = DigitalDocument::bastSignerData(
            $headmaster, $docType . '_KEPSEK', $docTitle, $refId,
            array_merge($hashBase, [$headmaster?->name ?? '', 'KEPSEK'])
        );
        $kaurData    = DigitalDocument::bastSignerData(
            $kaurLab, $docType . '_KAUR', $docTitle, $refId,
            array_merge($hashBase, [$kaurLab?->name ?? '', 'KAUR'])
        );
        $teacherData = DigitalDocument::bastSignerData(
            $log->teacher, $docType . '_TEACHER', $docTitle, $refId,
            array_merge($hashBase, [$log->teacher?->name ?? '', 'TEACHER'])
        );

        $hasAnyDigital = $kepsekData['doc'] || $kaurData['doc'] || $teacherData['doc'];
    @endphp

    <div class="signatures">
        <table>
            <tr>
                <td>
                    <div class="sign-label">Mengetahui,<br>Kepala Sekolah</div>
                    @if($kepsekData['qr'])
                        <div class="sign-qr"><img src="{{ $kepsekData['qr'] }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="sign-name">{{ $headmaster->name ?? '(................)' }}</div>
                    <div class="sign-role">Kepala Sekolah</div>
                </td>
                <td>
                    <div class="sign-label">Menyetujui,<br>Ka. Lab / Sarpras</div>
                    @if($kaurData['qr'])
                        <div class="sign-qr"><img src="{{ $kaurData['qr'] }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="sign-name">{{ $kaurLab->name ?? '(................)' }}</div>
                    <div class="sign-role">Ka. Lab / Sarpras</div>
                </td>
                <td>
                    <div class="sign-label">Yang Menggunakan,<br>Guru Mata Pelajaran</div>
                    @if($teacherData['qr'])
                        <div class="sign-qr"><img src="{{ $teacherData['qr'] }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="sign-name">{{ $log->teacher->name }}</div>
                    <div class="sign-role">Guru Mata Pelajaran</div>
                </td>
            </tr>
        </table>

        @if($hasAnyDigital)
        <div class="sign-footer">
            <strong>*)</strong> Scan QR Code pada tiap tanda tangan untuk memverifikasi keaslian tanda tangan digital masing-masing penandatangan.
            Verifikasi dapat dilakukan di: <strong>{{ url('/verify/signature') }}/{token}</strong>
        </div>
        @endif
    </div>
</body>

</html>
