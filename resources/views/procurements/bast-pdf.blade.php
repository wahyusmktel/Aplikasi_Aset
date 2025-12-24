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
        .sign-name {
            margin-top: 50px;
            font-weight: bold;
            text-decoration: underline;
        }
        .sign-role {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
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
            <div style="font-size: 10px;">Nomor: {{ $handover->document_number }}</div>
        </div>

        <div class="content">
            <p>Pada hari ini <strong>{{ $handover->handover_date->isoFormat('dddd') }}</strong>, tanggal <strong>{{ $handover->handover_date->isoFormat('D MMMM Y') }}</strong>, kami yang bertanda tangan di bawah ini:</p>
            
            <div style="margin-left: 20px;">
                @php
                    $schoolRepName = $wakaSarpra ? $wakaSarpra->name : Auth::user()->name;
                    $schoolRepPosition = $wakaSarpra ? 'Waka Bid. Sarpra IT dan Lab' : 'Waka Sarana Prasarana';
                @endphp

                @if($type == 'vendor_to_school')
                    <p><strong>I. Nama: {{ $handover->from_name }}</strong><br>
                    Jabatan: Perwakilan Rekanan ({{ $procurement->vendor->name }})<br>
                    Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

                    <p><strong>II. Nama: {{ $schoolRepName }}</strong><br>
                    Jabatan: {{ $schoolRepPosition }}<br>
                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
                @else
                    <p><strong>I. Nama: {{ $schoolRepName }}</strong><br>
                    Jabatan: {{ $schoolRepPosition }}<br>
                    Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

                    <p><strong>II. Nama: {{ $handover->to_name }}</strong><br>
                    Jabatan:  {{ $handover->toDepartment->name ?? 'Unit Terkait' }}<br>
                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
                @endif
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

        <!-- Clean Dotted Signature Table -->
        <div class="sign-container">
            <table class="sign-table">
                <tr>
                    <td>
                        <div>PIHAK PERTAMA,</div>
                        <div class="sign-name">{{ $type == 'vendor_to_school' ? $handover->from_name : $schoolRepName }}</div>
                        <div class="sign-role">{{ $type == 'vendor_to_school' ? 'Pihak Rekanan' : $schoolRepPosition }}</div>
                    </td>
                    <td>
                        <div>PIHAK KEDUA,</div>
                        <div class="sign-name">{{ $type == 'vendor_to_school' ? $schoolRepName : $handover->to_name }}</div>
                        <div class="sign-role">{{ $type == 'vendor_to_school' ? $schoolRepPosition : ' ' . ($handover->toDepartment->name ?? 'KAUR LAB') }}</div>
                    </td>
                    <td>
                        <div>MENGETAHUI,</div>
                        <div class="sign-name">{{ $headmaster ? $headmaster->name : '.........................................' }}</div>
                        <div class="sign-role">Kepala Sekolah</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
