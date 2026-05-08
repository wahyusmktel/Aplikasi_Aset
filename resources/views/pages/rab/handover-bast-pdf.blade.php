<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            line-height: 1.5;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .container { padding: 10px; }

        .kop-surat { width: 100%; margin: 0 0 20px 0; }
        .kop-surat img { width: 100%; display: block; }

        /* ISO Header fallback */
        .iso-header { width: 100%; border: 1.5px solid #000; border-collapse: collapse; margin-bottom: 20px; }
        .iso-header td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .iso-logo-cell { width: 15%; text-align: center; }
        .iso-title-cell { width: 50%; text-align: center; font-weight: bold; }
        .iso-meta-label { width: 15%; font-size: 9px; background-color: #fafafa; }
        .iso-meta-value { width: 20%; font-size: 9px; }

        .doc-title { text-align: center; margin-bottom: 20px; }
        .doc-title h2 { margin: 0 0 4px 0; font-size: 13px; text-transform: uppercase; }
        .doc-title .doc-num { font-size: 10px; color: #444; }

        .content p { text-align: justify; margin: 8px 0; }

        .parties-table { width: 100%; border: none; border-collapse: collapse; margin: 10px 0 10px 10px; }
        .parties-table td { padding: 2px 4px; vertical-align: top; }
        .parties-table td.roman { width: 25px; font-weight: bold; }
        .parties-table td.label { width: 70px; }
        .parties-table td.sep { width: 15px; text-align: center; }

        table.item-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.item-table th,
        table.item-table td { border: 1px solid #000; padding: 6px 8px; }
        table.item-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 9px; }
        table.item-table .center { text-align: center; }

        .sign-container { margin-top: 30px; }
        .sign-table { width: 100%; border-collapse: collapse; border: 1px dotted #999; }
        .sign-table td {
            border: 1px dotted #999;
            padding: 15px;
            width: 33.33%;
            text-align: center;
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
<div class="container">

    {{-- Kop Surat --}}
    @if($kopSurat)
        <div class="kop-surat">
            <img src="{{ public_path('storage/' . $kopSurat) }}" alt="Kop Surat">
        </div>
    @else
        <table class="iso-header">
            <tr>
                <td rowspan="3" class="iso-logo-cell">
                    @php $logo = \App\Models\Setting::get('app_logo'); @endphp
                    @if($logo)
                        <img src="{{ public_path('storage/' . $logo) }}" style="width:60px;">
                    @else
                        <div style="font-weight:bold;font-size:10px;">LOGO</div>
                    @endif
                </td>
                <td rowspan="2" class="iso-title-cell">
                    @php $appName = \App\Models\Setting::get('app_name') ?? config('app.name'); @endphp
                    <div style="font-size:13px;margin-bottom:2px;">{{ $appName }}</div>
                </td>
                <td class="iso-meta-label">No. Dokumen</td>
                <td class="iso-meta-value">{{ $handover->document_number }}</td>
            </tr>
            <tr>
                <td class="iso-meta-label">No. Revisi</td>
                <td class="iso-meta-value">00</td>
            </tr>
            <tr>
                <td class="iso-title-cell" style="font-size:9px;padding:2px;">
                    FORM BERITA ACARA SERAH TERIMA BARANG ( BAST )
                </td>
                <td class="iso-meta-label">Tanggal Berlaku</td>
                <td class="iso-meta-value">{{ $handover->handover_date->format('d F Y') }}</td>
            </tr>
        </table>
    @endif

    {{-- Judul --}}
    <div class="doc-title">
        <h2>Berita Acara Serah Terima Barang (BAST)</h2>
        <div class="doc-num">Nomor: {{ $handover->document_number }}</div>
    </div>

    {{-- Isi --}}
    <div class="content">
        <p>
            Pada hari ini <strong>{{ $handover->handover_date->locale('id')->isoFormat('dddd') }}</strong>,
            tanggal <strong>{{ $handover->handover_date->locale('id')->isoFormat('D MMMM Y') }}</strong>,
            kami yang bertanda tangan di bawah ini:
        </p>

        <table class="parties-table">
            <tr>
                <td class="roman">I.</td>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td><strong>{{ $handover->handed_by }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Jabatan</td>
                <td class="sep">:</td>
                <td>{{ $handover->handed_by_jabatan ?: 'Pengelola Sarana Prasarana' }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</td>
            </tr>
            <tr><td colspan="4" style="height:12px;"></td></tr>
            <tr>
                <td class="roman">II.</td>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td><strong>{{ $handover->received_by ?: '.................................................' }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Jabatan</td>
                <td class="sep">:</td>
                <td>{{ $handover->received_by_jabatan ?: '.................................................' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Unit / Bagian</td>
                <td class="sep">:</td>
                <td>{{ $handover->department->name ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3">Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</td>
            </tr>
        </table>

        <p>
            PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima
            dari PIHAK PERTAMA, barang-barang dalam rangka pelaksanaan RAB
            <strong>{{ $handover->rab->name }}</strong>
            Tahun Anggaran <strong>{{ $handover->rab->academicYear->year ?? '-' }}</strong>
            dengan rincian sebagai berikut:
        </p>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:25px;">No</th>
                    <th>Nama / Uraian Barang</th>
                    <th style="width:60px;">Qty</th>
                    <th>Spesifikasi</th>
                    <th style="width:100px;">Kondisi Barang</th>
                </tr>
            </thead>
            <tbody>
                @foreach($handover->items as $i => $item)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $item->uraian }}</td>
                        <td class="center">
                            {{ $item->qty ? (fmod($item->qty, 1) == 0 ? (int)$item->qty : $item->qty) : '-' }}
                        </td>
                        <td>{{ $item->spesifikasi ?: '-' }}</td>
                        <td style="font-size:9px;">
                            <div style="margin-bottom:2px;">[ &nbsp; ] Baik</div>
                            <div>[ &nbsp; ] Tidak Baik</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>
            Demikian Berita Acara Serah Terima ini dibuat dalam rangkap 2 (dua) dan ditandatangani
            oleh kedua belah pihak untuk dipergunakan sebagaimana mestinya.
        </p>
    </div>

    {{-- Tanda Tangan --}}
    @php
        use App\Models\DigitalDocument;
        use App\Models\Employee;

        $docType    = 'BAST_RAB_HANDOVER';
        $docTitle   = 'BAST Serah Terima RAB #' . $handover->document_number;
        $refId      = (string) $handover->id;
        $hashBase   = [$docType, $refId, $handover->document_number];

        $kepsekData = DigitalDocument::bastSignerData(
            $headmaster, $docType . '_KEPSEK', $docTitle, $refId,
            array_merge($hashBase, [$headmaster?->name ?? '', 'KEPSEK'])
        );

        $p1Employee = Employee::where('name', $handover->handed_by)->first();
        $p1Data     = DigitalDocument::bastSignerData(
            $p1Employee, $docType . '_P1', $docTitle, $refId,
            array_merge($hashBase, [$handover->handed_by ?? '', 'P1'])
        );

        $p2Employee = Employee::where('name', $handover->received_by)->first();
        $p2Data     = DigitalDocument::bastSignerData(
            $p2Employee, $docType . '_P2', $docTitle, $refId,
            array_merge($hashBase, [$handover->received_by ?? '', 'P2'])
        );

        $hasAnyDigital = $kepsekData['doc'] !== null || $p1Data['doc'] !== null || $p2Data['doc'] !== null;
    @endphp
    <div class="sign-container">
        <table class="sign-table">
            <tr>
                <td>
                    <div class="sign-label">PIHAK PERTAMA,</div>
                    @if($p1Data['qr'])
                        <div class="sign-qr"><img src="{{ $p1Data['qr'] }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="sign-name">{{ $handover->handed_by }}</div>
                    <div class="sign-role">{{ $handover->handed_by_jabatan ?: 'Pengelola Sarana Prasarana' }}</div>
                </td>
                <td>
                    <div class="sign-label">PIHAK KEDUA,</div>
                    @if($p2Data['qr'])
                        <div class="sign-qr"><img src="{{ $p2Data['qr'] }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="sign-name">{{ $handover->received_by ?: '......................................................' }}</div>
                    <div class="sign-role">{{ $handover->received_by_jabatan ?: ($handover->department->name ?? 'Unit Penerima') }}</div>
                </td>
                <td>
                    <div class="sign-label">MENGETAHUI,</div>
                    @if($kepsekData['qr'])
                        <div class="sign-qr"><img src="{{ $kepsekData['qr'] }}"></div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="sign-name">{{ $headmaster ? $headmaster->name : '......................................................' }}</div>
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
