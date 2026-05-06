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
            width: 30%;
            padding: 12px 8px;
            vertical-align: top;
        }

        .qr-cell {
            border: 1px dotted #999;
            width: 10%;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
        }

        .sign-img-wrap {
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }
        .sign-img-wrap img { max-height: 50px; max-width: 110px; object-fit: contain; }
        .sign-space { height: 55px; }
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
        use App\Models\UserDigitalSignature;
        use App\Models\DigitalDocument;

        // Ambil tanda tangan kepala sekolah
        $headmasterUser = $headmaster?->user ?? null;
        $headmasterSig  = $headmasterUser
            ? UserDigitalSignature::where('user_id', $headmasterUser->id)->first()
            : null;

        // PersonInCharge tidak punya relasi user langsung — cari via nama di tabel employees
        $picEmployee = \App\Models\Employee::where('name', $asset->personInCharge?->name)->first();
        $picUser     = $picEmployee?->user ?? null;
        $picSig      = $picUser
            ? UserDigitalSignature::where('user_id', $picUser->id)->first()
            : null;

        // Ambil tanda tangan peminjam
        $employeeUser = $employee?->user ?? null;
        $employeeSig  = $employeeUser
            ? UserDigitalSignature::where('user_id', $employeeUser->id)->first()
            : null;

        $docType     = $isReturn ? 'BAST_ASSET_RETURN' : 'BAST_ASSET_CHECKOUT';
        $digitalDoc  = DigitalDocument::where('document_type', $docType)
            ->where('reference_id', $assignment->id)
            ->where('is_valid', true)
            ->first();
    @endphp

    <div class="signatures">
        <table>
            <tr>
                <td>
                    Mengetahui,<br>Kepala Sekolah
                    @if($headmasterSig && $headmasterSig->ttd_image_path)
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $headmasterSig->ttd_image_path) }}"></div>
                        <div class="digital-badge">&#10003; TTD Digital</div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <strong>{{ $headmaster->name ?? '(Nama Kepala Sekolah)' }}</strong>
                </td>
                <td>
                    Menyetujui,<br>Penanggung Jawab Aset
                    @if($picSig && $picSig->ttd_image_path)
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $picSig->ttd_image_path) }}"></div>
                        <div class="digital-badge">&#10003; TTD Digital</div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <strong>{{ $asset->personInCharge->name }}</strong>
                </td>
                <td>
                    Yang Menerima,<br>Peminjam
                    @if($employeeSig && $employeeSig->ttd_image_path)
                        <div class="sign-img-wrap"><img src="{{ public_path('storage/' . $employeeSig->ttd_image_path) }}"></div>
                        <div class="digital-badge">&#10003; TTD Digital</div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <strong>{{ $employee->name }}</strong>
                </td>
                <td class="qr-cell">
                    <img src="{{ $qrCode }}" width="65" height="65">
                    <div style="font-size:7px; color:#666; margin-top:2px;">Scan verifikasi</div>
                    @if($digitalDoc)
                        <div class="digital-badge" style="font-size:6px; margin-top:3px;">TTD Digital Sah</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
