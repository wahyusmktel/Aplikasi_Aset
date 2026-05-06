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
        .sign-img-wrap { height: 48px; display: flex; align-items: center; justify-content: center; margin: 2px auto; }
        .sign-img-wrap img { max-height: 45px; max-width: 110px; object-fit: contain; }
        .sign-qr { margin: 3px auto 2px; text-align: center; }
        .sign-qr img { width: 52px; height: 52px; }
        .sign-space { height: 70px; }
        .sign-name { font-weight: bold; text-decoration: underline; font-size: 10px; margin-top: 4px; }
        .sign-role { font-size: 8px; color: #666; margin-top: 1px; }
        .sign-footer { margin-top: 10px; font-size: 8px; color: #555; border-top: 1px solid #e0e0e0; padding-top: 6px; }
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
            @php \Carbon\Carbon::setLocale('id'); @endphp
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
                    @if($isCheckin)
                        <strong>Yang Mengembalikan (Pengguna):</strong><br>{{ $log->borrower_name }}
                    @else
                        <strong>Yang Menyerahkan ({{ $approverTitle }}):</strong><br>{{ $approver->name ?? 'Petugas Aset' }}
                    @endif
                </td>
                <td>
                    @if($isCheckin)
                        <strong>Yang Menerima ({{ $approverTitle }}):</strong><br>{{ $approver->name ?? 'Petugas Aset' }}
                    @else
                        <strong>Yang Menerima (Pengguna):</strong><br>{{ $log->borrower_name }}
                    @endif
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
                <td>{{ $asset->spesifikasi['nomor_polisi'] ?? '-' }}</td>
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
                <td><strong>Status Pengemudi</strong></td>
                <td>{{ $log->driver_type == 'school_driver' ? 'Driver Sekolah (' . ($log->driverEmployee->name ?? '-') . ')' : 'Menyetir Sendiri' }}</td>
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
                <td><strong>Kondisi BBM {{ $isCheckin ? 'Akhir' : 'Awal' }}</strong></td>
                <td>{{ $isCheckin ? $log->fuel_level_end : $log->fuel_level_start }}</td>
            </tr>
            <tr>
                <td><strong>Kondisi Fisik {{ $isCheckin ? 'Akhir' : 'Awal' }}</strong></td>
                <td>{{ $isCheckin ? $log->condition_on_checkin : $log->condition_on_checkout }}</td>
            </tr>
            @if (!$isCheckin && $log->start_latitude)
                <tr>
                    <td><strong>Koordinat Awal</strong></td>
                    <td>Lat: {{ $log->start_latitude }}, Lng: {{ $log->start_longitude }}</td>
                </tr>
            @endif
            @if ($isCheckin && $log->notes)
                <tr>
                    <td><strong>Catatan Tambahan</strong></td>
                    <td>{{ $log->notes }}</td>
                </tr>
            @endif
        </table>
    </div>

    @php
        use App\Models\DigitalDocument;

        $docType      = $isCheckin ? 'BAST_VEHICLE_CHECKIN' : 'BAST_VEHICLE_CHECKOUT';
        $docNum       = $isCheckin ? $log->checkin_doc_number : $log->checkout_doc_number;
        $docTitle     = ($isCheckin ? 'BAST Pengembalian' : 'BAST Penggunaan') . ' Kendaraan #' . ($docNum ?? $log->id);
        $refId        = (string) $log->id . '_' . ($isCheckin ? 'in' : 'out');
        $hashBase     = [$docType, $refId, $docNum ?? ''];

        $kepsekData   = DigitalDocument::bastSignerData(
            $headmaster, $docType . '_KEPSEK', $docTitle, $refId,
            array_merge($hashBase, [$headmaster?->name ?? '', 'KEPSEK'])
        );
        $approverData = DigitalDocument::bastSignerData(
            $approver, $docType . '_APPROVER', $docTitle, $refId,
            array_merge($hashBase, [$approver?->name ?? '', 'APPROVER'])
        );

        $hasAnyDigital = $kepsekData['doc'] || $approverData['doc'];
    @endphp

    <div class="signatures">
        <table>
            <tr>
                <td>
                    <div class="sign-label">Mengetahui,<br>Kepala Sekolah</div>
                    @if($kepsekData['sig'])
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $kepsekData['sig']->ttd_image_path) }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    @if($kepsekData['qr'])
                        <div class="sign-qr"><img src="{{ $kepsekData['qr'] }}"></div>
                    @endif
                    <div class="sign-name">{{ $headmaster->name ?? '(Nama Kepala Sekolah)' }}</div>
                    <div class="sign-role">Kepala Sekolah</div>
                    @if($headmaster && $headmaster->nip)
                        <div class="sign-role">NIP. {{ $headmaster->nip }}</div>
                    @endif
                </td>
                <td>
                    <div class="sign-label">Menyetujui,<br>{{ $approverTitle }}</div>
                    @if($approverData['sig'])
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $approverData['sig']->ttd_image_path) }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    @if($approverData['qr'])
                        <div class="sign-qr"><img src="{{ $approverData['qr'] }}"></div>
                    @endif
                    <div class="sign-name">{{ $approver->name ?? '(Nama ' . $approverTitle . ')' }}</div>
                    <div class="sign-role">{{ $approverTitle }}</div>
                    @if($approver && $approver->nip)
                        <div class="sign-role">NIP. {{ $approver->nip }}</div>
                    @endif
                </td>
                <td>
                    <div class="sign-label">Pengguna,</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $log->borrower_name }}</div>
                    <div class="sign-role">Pengguna Kendaraan</div>
                    @if($log->borrower_nip)
                        <div class="sign-role">NIP. {{ $log->borrower_nip }}</div>
                    @endif
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
