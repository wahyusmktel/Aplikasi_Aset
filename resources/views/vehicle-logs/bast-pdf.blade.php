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

        .sign-img-wrap {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px auto;
        }
        .sign-img-wrap img { max-height: 48px; max-width: 110px; object-fit: contain; }
        .sign-space { height: 50px; }
        .digital-badge {
            font-size: 7px;
            color: #4f46e5;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 4px;
            padding: 1px 4px;
            margin-top: 2px;
            display: inline-block;
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
        use App\Models\UserDigitalSignature;
        $headmasterUser  = $headmaster?->user ?? null;
        $headmasterSig   = $headmasterUser ? UserDigitalSignature::where('user_id', $headmasterUser->id)->first() : null;
        $approverUser    = $approver?->user ?? null;
        $approverSig     = $approverUser ? UserDigitalSignature::where('user_id', $approverUser->id)->first() : null;
    @endphp

    <div class="signatures">
        <table>
            <tr>
                <td>
                    Mengetahui,<br>Kepala Sekolah
                    @if($headmasterSig && $headmasterSig->ttd_image_path)
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $headmasterSig->ttd_image_path) }}"></div>
                        <div class="digital-badge">&#10003; TTD Digital</div>
                    @elseif(isset($kepsekQrCode))
                        <br><img src="{{ $kepsekQrCode }}" width="55px" height="55px"><br>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <strong>{{ $headmaster->name ?? '(Nama Kepala Sekolah)' }}</strong><br>
                    NIP. {{ $headmaster->nip ?? '-' }}
                </td>
                <td>
                    Menyetujui,<br>{{ $approverTitle }}
                    @if($approverSig && $approverSig->ttd_image_path)
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $approverSig->ttd_image_path) }}"></div>
                        <div class="digital-badge">&#10003; TTD Digital</div>
                    @elseif(isset($wakaQrCode))
                        <br><img src="{{ $wakaQrCode }}" width="55px" height="55px"><br>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <strong>{{ $approver->name ?? '(Nama ' . $approverTitle . ')' }}</strong><br>
                    NIP. {{ $approver->nip ?? '-' }}
                </td>
                <td>
                    Pengguna,
                    @if(isset($userQrCode))
                        <br><img src="{{ $userQrCode }}" width="55px" height="55px"><br>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <strong>{{ $log->borrower_name }}</strong><br>
                    NIP. {{ $log->borrower_nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="qr-code">
        <img src="{{ $qrCode }}" width="75px" height="75px">
        <p style="font-size:9px; text-align:center; margin-top:2px; color:#555;">Scan untuk verifikasi dokumen</p>
    </div>
</body>

</html>
