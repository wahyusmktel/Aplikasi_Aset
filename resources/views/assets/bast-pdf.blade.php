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
        @php
            $kop = \App\Models\Setting::get('app_kop_surat');
        @endphp
        @if($kop)
            <img src="{{ public_path('storage/' . $kop) }}" style="width: 100%; margin-bottom: 10px;">
        @else
            <h1>{{ $title }}</h1>
        @endif
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

    @php
        use App\Models\DigitalDocument;

        $picEmployee  = \App\Models\Employee::where('name', $asset->personInCharge?->name)->first();
        $docType      = $isReturn ? 'BAST_ASSET_RETURN' : 'BAST_ASSET_CHECKOUT';
        $docTitle     = ($isReturn ? 'BAST Return' : 'BAST Checkout') . ' Aset #' . ($assignment->checkout_doc_number ?? $assignment->return_doc_number ?? $assignment->id);
        $refId        = (string) $assignment->id;
        $hashBase     = [$docType, $refId, $assignment->checkout_doc_number ?? $assignment->return_doc_number ?? ''];

        $kepsekData   = DigitalDocument::bastSignerData(
            $headmaster, $docType . '_KEPSEK', $docTitle, $refId,
            array_merge($hashBase, [$headmaster?->name ?? '', 'KEPSEK'])
        );
        $picData      = DigitalDocument::bastSignerData(
            $picEmployee, $docType . '_PIC', $docTitle, $refId,
            array_merge($hashBase, [$picEmployee?->name ?? '', 'PIC'])
        );
        $peminjamData = DigitalDocument::bastSignerData(
            $employee, $docType . '_PEMINJAM', $docTitle, $refId,
            array_merge($hashBase, [$employee?->name ?? '', 'PEMINJAM'])
        );

        $hasAnyDigital = $kepsekData['doc'] || $picData['doc'] || $peminjamData['doc'];
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
                </td>
                <td>
                    <div class="sign-label">Menyetujui,<br>Penanggung Jawab Aset</div>
                    @if($picData['sig'])
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $picData['sig']->ttd_image_path) }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    @if($picData['qr'])
                        <div class="sign-qr"><img src="{{ $picData['qr'] }}"></div>
                    @endif
                    <div class="sign-name">{{ $asset->personInCharge->name }}</div>
                    <div class="sign-role">Penanggung Jawab Aset</div>
                </td>
                <td>
                    <div class="sign-label">Yang Menerima,<br>Peminjam</div>
                    @if($peminjamData['sig'])
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $peminjamData['sig']->ttd_image_path) }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    @if($peminjamData['qr'])
                        <div class="sign-qr"><img src="{{ $peminjamData['qr'] }}"></div>
                    @endif
                    <div class="sign-name">{{ $employee->name }}</div>
                    <div class="sign-role">Peminjam</div>
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
