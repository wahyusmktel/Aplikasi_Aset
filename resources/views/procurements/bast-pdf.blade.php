<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #000; line-height: 1.5; font-size: 11px; margin: 0; padding: 0; }
        .container { padding: 10px; }
        
        /* ISO Header Style - Narrow Margins */
        .iso-header {
            width: 100%;
            border: 1.5px solid #000;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .iso-header td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        .iso-logo-cell {
            width: 15%;
            text-align: center;
        }
        .iso-title-cell {
            width: 50%;
            text-align: center;
            font-weight: bold;
        }
        .iso-meta-label {
            width: 15%;
            font-size: 9px;
            background-color: #fafafa;
        }
        .iso-meta-value {
            width: 20%;
            font-size: 9px;
        }

        .content { margin-top: 10px; }
        .content p { text-align: justify; }

        table.item-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.item-table th, table.item-table td { border: 1px solid #000; padding: 6px 8px; }
        table.item-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }

        /* Signature Table with Dotted Borders */
        .sign-container {
            margin-top: 30px;
            width: 100%;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px dotted #999;
        }
        .sign-table td {
            border: 1px dotted #999;
            padding: 15px;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }
        .sign-img-wrap {
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }
        .sign-img-wrap img {
            max-height: 50px;
            max-width: 120px;
            object-fit: contain;
        }
        .sign-space { height: 55px; }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sign-role {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
        .digital-badge {
            font-size: 7px;
            color: #4f46e5;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 4px;
            padding: 1px 4px;
            margin-top: 3px;
            display: inline-block;
        }
        .qr-cell {
            width: 80px;
            text-align: center;
            vertical-align: middle;
            border: 1px dotted #999;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ISO Standard Header -->
        <table class="iso-header">
            <tr>
                <td rowspan="3" class="iso-logo-cell">
                    @php $logo = \App\Models\Setting::get('app_logo'); @endphp
                    @if($logo)
                        <img src="{{ public_path('storage/' . $logo) }}" style="width: 60px;">
                    @else
                        <div style="font-weight: bold; font-size: 10px;">LOGO</div>
                    @endif
                </td>
                <td rowspan="2" class="iso-title-cell">
                    <div style="font-size: 13px; margin-bottom: 2px;">SMK TELKOM LAMPUNG</div>
                    <div style="font-size: 8px; font-weight: normal; line-height: 1.2;">
                        Jl. Raya Gadingrejo, RT001/RW002 Gadingrejo Timur, <br>
                        Kec. Gadingrejo, Kab. Pringsewu, Lampung 35374
                    </div>
                </td>
                <td class="iso-meta-label">No. Dokumen</td>
                <td class="iso-meta-value">SAR-FR-06-2025</td>
            </tr>
            <tr>
                <td class="iso-meta-label">No. Revisi</td>
                <td class="iso-meta-value">00</td>
            </tr>
            <tr>
                <td class="iso-title-cell" style="font-size: 9px; padding: 2px;">
                    FORM BERITA ACARA SERAH TERIMA BARANG ( BAST )
                </td>
                <td class="iso-meta-label">Tanggal Berlaku</td>
                <td class="iso-meta-value">27 November 2025</td>
            </tr>
        </table>

        <!-- Specific Document Info -->
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-weight: bold; font-size: 12px;">BERITA ACARA SERAH TERIMA (BAST)</div>
            <!-- <div style="font-size: 10px;">Nomor: {{ $handover->document_number }}</div> -->
        </div>

        <div class="content">
            <p>Pada hari ini <strong>{{ $handover->handover_date->locale('id')->isoFormat('dddd') }}</strong>, tanggal <strong>{{ $handover->handover_date->locale('id')->isoFormat('D MMMM Y') }}</strong>, kami yang bertanda tangan di bawah ini :</p>
            
            <div style="margin-top: 10px;">
                @php
                    $schoolRepName = $wakaSarpra ? $wakaSarpra->name : Auth::user()->name;
                    $schoolRepPosition = $wakaSarpra ? 'Waka Bid. Sarpra IT dan Lab' : 'Waka Sarana Prasarana';

                    if($type == 'vendor_to_school') {
                        $p1_name = $handover->from_name;
                        $p1_jabatan = "Perwakilan Rekanan (" . $procurement->vendor->name . ")";
                        $p2_name = $schoolRepName;
                        $p2_jabatan = $schoolRepPosition;
                    } else {
                        $p1_name = $schoolRepName;
                        $p1_jabatan = $schoolRepPosition;
                        $p2_name = $handover->to_name;
                        $p2_jabatan = '' . ($handover->toPersonInCharge->name ?? $handover->toDepartment->name ?? 'Terlampir');
                    }
                @endphp

                <table style="width: 100%; border: none; border-collapse: collapse; margin-left: 10px;">
                    <!-- PIHAK PERTAMA -->
                    <tr>
                        <td style="width: 25px; vertical-align: top; font-weight: bold;">I.</td>
                        <td style="width: 60px; vertical-align: top;">Nama</td>
                        <td style="width: 15px; vertical-align: top; text-align: center;">:</td>
                        <td style="vertical-align: top;"><strong>{{ $p1_name }}</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="vertical-align: top;">Jabatan</td>
                        <td style="vertical-align: top; text-align: center;">:</td>
                        <td style="vertical-align: top;">{{ $p1_jabatan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2"></td>
                        <td style="vertical-align: top;">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</td>
                    </tr>

                    <!-- Spacer -->
                    <tr><td colspan="4" style="height: 15px;"></td></tr>

                    <!-- PIHAK KEDUA -->
                    <tr>
                        <td style="vertical-align: top; font-weight: bold;">II.</td>
                        <td style="vertical-align: top;">Nama</td>
                        <td style="vertical-align: top; text-align: center;">:</td>
                        <td style="vertical-align: top;"><strong>{{ $p2_name }}</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="vertical-align: top;">Jabatan</td>
                        <td style="vertical-align: top; text-align: center;">:</td>
                        <td style="vertical-align: top;">{{ $p2_jabatan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2"></td>
                        <td style="vertical-align: top;">Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</td>
                    </tr>
                </table>
            </div>

            <p>PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima dari PIHAK PERTAMA barang-barang dengan rincian sebagai berikut:</p>

            <table class="item-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>Nama Barang / Deskripsi</th>
                        <th style="width: 60px;">Jumlah</th>
                        <th>Spesifikasi</th>
                        <th style="width: 100px;">Kondisi Barang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($procurement->items as $index => $item)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td style="text-align: center;">{{ $item->quantity }} Unit</td>
                            <td>{{ $item->specs ?? '-' }}</td>
                            <td style="font-size: 8px;">
                                <div style="margin-bottom: 2px;">[ &nbsp; ] Baik</div>
                                <div>[ &nbsp; ] Tidak Baik</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Demikian Berita Acara ini dibuat dalam rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Signature Table with Digital Signatures -->
        @php
            use App\Models\UserDigitalSignature;
            use App\Models\DigitalDocument;

            // Cari penandatangan (Waka Sarpra / user saat ini) berdasarkan $wakaSarpra
            $schoolSignerUser = $wakaSarpra?->user ?? null;
            $schoolSig = $schoolSignerUser
                ? UserDigitalSignature::where('user_id', $schoolSignerUser->id)->first()
                : null;

            // Cari tanda tangan kepala sekolah
            $headmasterUser = $headmaster?->user ?? null;
            $headmasterSig = $headmasterUser
                ? UserDigitalSignature::where('user_id', $headmasterUser->id)->first()
                : null;

            // Cari digital document record untuk BAST ini
            $bastType = 'BAST_PROCUREMENT_' . strtoupper($type);
            $digitalDoc = DigitalDocument::where('document_type', $bastType)
                ->where('reference_id', $handover->id)
                ->where('is_valid', true)
                ->first();
        @endphp

        <div class="sign-container">
            <table class="sign-table">
                <tr>
                    {{-- PIHAK PERTAMA --}}
                    <td>
                        <div>PIHAK PERTAMA,</div>
                        @if($type == 'vendor_to_school')
                            <div class="sign-space"></div>
                        @else
                            @if($schoolSig && $schoolSig->ttd_image_path)
                                <div class="sign-img-wrap">
                                    <img src="{{ public_path('storage/' . $schoolSig->ttd_image_path) }}">
                                </div>
                                <div class="digital-badge">&#10003; Tanda Tangan Digital</div>
                            @else
                                <div class="sign-space"></div>
                            @endif
                        @endif
                        <div class="sign-name">{{ $type == 'vendor_to_school' ? $handover->from_name : $schoolRepName }}</div>
                        <div class="sign-role">{{ $type == 'vendor_to_school' ? 'Pihak Rekanan' : $schoolRepPosition }}</div>
                    </td>

                    {{-- PIHAK KEDUA --}}
                    <td>
                        <div>PIHAK KEDUA,</div>
                        @if($type == 'vendor_to_school')
                            @if($schoolSig && $schoolSig->ttd_image_path)
                                <div class="sign-img-wrap">
                                    <img src="{{ public_path('storage/' . $schoolSig->ttd_image_path) }}">
                                </div>
                                <div class="digital-badge">&#10003; Tanda Tangan Digital</div>
                            @else
                                <div class="sign-space"></div>
                            @endif
                        @else
                            <div class="sign-space"></div>
                        @endif
                        <div class="sign-name">{{ $type == 'vendor_to_school' ? $schoolRepName : $handover->to_name }}</div>
                        <div class="sign-role">{{ $type == 'vendor_to_school' ? $schoolRepPosition : $p2_jabatan }}</div>
                    </td>

                    {{-- MENGETAHUI --}}
                    <td>
                        <div>MENGETAHUI,</div>
                        @if($headmasterSig && $headmasterSig->ttd_image_path)
                            <div class="sign-img-wrap">
                                <img src="{{ public_path('storage/' . $headmasterSig->ttd_image_path) }}">
                            </div>
                            <div class="digital-badge">&#10003; Tanda Tangan Digital</div>
                        @else
                            <div class="sign-space"></div>
                        @endif
                        <div class="sign-name">{{ $headmaster ? $headmaster->name : '.........................................' }}</div>
                        <div class="sign-role">Kepala Sekolah</div>
                    </td>
                </tr>
            </table>

            @if($digitalDoc)
            <table style="width:100%; margin-top:10px; border-collapse:collapse;">
                <tr>
                    <td style="font-size:8px; color:#555; vertical-align:middle; padding-right:8px;">
                        <strong style="color:#4f46e5;">&#10003; Dokumen ini telah ditandatangani secara digital</strong><br>
                        Penandatangan: {{ $digitalDoc->signer_name }}
                        {{ $digitalDoc->signer_nip ? ' · NIP: ' . $digitalDoc->signer_nip : '' }}<br>
                        Waktu: {{ \Carbon\Carbon::parse($digitalDoc->signed_at)->format('d/m/Y H:i') }} WIB<br>
                        Token: <span style="font-family:monospace; font-size:7px;">{{ $digitalDoc->token }}</span><br>
                        Verifikasi: <span style="font-family:monospace; font-size:7px;">{{ url('/verify/signature/' . $digitalDoc->token) }}</span>
                    </td>
                    @if(isset($qrCode))
                    <td class="qr-cell">
                        <img src="{{ $qrCode }}" width="65" height="65">
                        <div style="font-size:7px; color:#666; margin-top:2px;">Scan untuk verifikasi</div>
                    </td>
                    @endif
                </tr>
            </table>
            @endif
        </div>
    </div>
</body>
</html>
