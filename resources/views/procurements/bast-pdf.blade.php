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

        /* Tanda Tangan */
        .sign-container { margin-top: 30px; }
        .sign-table { width: 100%; border-collapse: collapse; border: 1px dotted #999; }
        .sign-table td { border: 1px dotted #999; padding: 10px 8px; width: 33.33%; text-align: center; vertical-align: top; }
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

        <!-- Tanda Tangan Digital -->
        @php
            use App\Models\DigitalDocument;

            $bastType  = 'BAST_PROCUREMENT_' . strtoupper($type);
            $bastTitle = 'BAST Pengadaan #' . $handover->document_number;
            $refId     = (string) $handover->id;
            $hashBase  = [$bastType, $refId, $handover->document_number];

            // Waka Sarpra (pihak sekolah)
            $schoolData = DigitalDocument::bastSignerData(
                $wakaSarpra, $bastType . '_WAKA', $bastTitle, $refId,
                array_merge($hashBase, [$schoolRepName, 'WAKA'])
            );

            // Kepala Sekolah
            $kepsekData = DigitalDocument::bastSignerData(
                $headmaster, $bastType . '_KEPSEK', $bastTitle, $refId,
                array_merge($hashBase, [$headmaster?->name ?? '', 'KEPSEK'])
            );

            $hasAnyDigital = $schoolData['doc'] || $kepsekData['doc'];
        @endphp

        <div class="sign-container">
            <table class="sign-table">
                <tr>
                    {{-- PIHAK PERTAMA --}}
                    <td>
                        <div class="sign-label">PIHAK PERTAMA,</div>
                        @if($type == 'vendor_to_school')
                            {{-- Vendor tanda tangan fisik --}}
                            <div class="sign-space"></div>
                        @else
                            {{-- Waka Sarpra sebagai pihak pertama (school_to_unit) --}}
                            @if($schoolData['qr'])
                                <div class="sign-qr"><img src="{{ $schoolData['qr'] }}"></div>
                            @else
                                <div class="sign-space"></div>
                            @endif
                        @endif
                        <div class="sign-name">{{ $type == 'vendor_to_school' ? $handover->from_name : $schoolRepName }}</div>
                        <div class="sign-role">{{ $type == 'vendor_to_school' ? 'Pihak Rekanan' : $schoolRepPosition }}</div>
                    </td>

                    {{-- PIHAK KEDUA --}}
                    <td>
                        <div class="sign-label">PIHAK KEDUA,</div>
                        @if($type == 'vendor_to_school')
                            {{-- Waka Sarpra sebagai pihak kedua --}}
                            @if($schoolData['qr'])
                                <div class="sign-qr"><img src="{{ $schoolData['qr'] }}"></div>
                            @else
                                <div class="sign-space"></div>
                            @endif
                        @else
                            {{-- Penerima unit — tanda tangan fisik --}}
                            <div class="sign-space"></div>
                        @endif
                        <div class="sign-name">{{ $type == 'vendor_to_school' ? $schoolRepName : $handover->to_name }}</div>
                        <div class="sign-role">{{ $type == 'vendor_to_school' ? $schoolRepPosition : $p2_jabatan }}</div>
                    </td>

                    {{-- MENGETAHUI --}}
                    <td>
                        <div class="sign-label">MENGETAHUI,</div>
                        @if($kepsekData['qr'])
                            <div class="sign-qr"><img src="{{ $kepsekData['qr'] }}"></div>
                        @else
                            <div class="sign-space"></div>
                        @endif
                        <div class="sign-name">{{ $headmaster ? $headmaster->name : '.........................................' }}</div>
                        <div class="sign-role">Kepala Sekolah</div>
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
    </div>
</body>
</html>
