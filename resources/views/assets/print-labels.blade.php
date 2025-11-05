<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset (12 per Halaman)</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Langkah 1: Set ukuran kertas khusus 19cm x 13.4cm, tanpa margin printer */
        @page {
            size: 19cm 13.4cm;
            /* lebar x tinggi */
            margin: 0;
        }

        /* Langkah 2: Print helpers */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .label-container {
                page-break-inside: avoid !important;
            }
        }

        /* Langkah 3: Wrapper lembar (membuat margin fisik & celah antar kolom/baris)
           Perhitungan:
           - Lebar kertas = 19cm
             Margin kiri/kanan total 0.4cm (0.2 + 0.2)
             Sisa lebar 18.6cm = 3 kolom * 6cm + 2 celah kolom * 0.3cm
           - Tinggi kertas = 13.4cm
             Margin atas/bawah total 0.8cm (0.4 + 0.4)
             Sisa tinggi 12.6cm = 4 baris * 3cm + 3 celah baris * 0.2cm
        */
        .sheet {
            width: 19cm;
            height: 13.4cm;
            padding: 0.4cm 0.2cm;
            /* top/bottom 0.4cm, left/right 0.2cm */
            box-sizing: border-box;

            display: grid;
            grid-template-columns: repeat(3, 6cm);
            grid-auto-rows: 3cm;
            /* setiap baris 3cm */
            column-gap: 0.3cm;
            /* 2 celah kolom = 0.6cm */
            row-gap: 0.2cm;
            /* 3 celah baris = 0.6cm */
            background: white;
        }

        /* Langkah 4: Kotak label ukuran pasti 6×3 cm */
        .label-container {
            width: 6cm;
            height: 3cm;
            box-sizing: border-box;
            padding: 0.15cm;
            /* ruang dalam aman */
            border: 0.2pt solid #AAA;
            /* tipis, aman untuk cetak */
            border-radius: 2px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Langkah 5: Skala font kecil biar muat */
        .text-xxs {
            font-size: 0.6rem;
            line-height: 0.8rem;
        }

        .text-xxxs {
            font-size: 0.5rem;
            line-height: 0.7rem;
        }

        /* Layout bagian dalam label */
        .label-header {
            text-align: center;
            border-bottom: 0.2pt solid #CCC;
            padding-bottom: 0mm;
            margin-bottom: 1mm;
        }

        .label-body {
            display: flex;
            align-items: center;
            gap: 2mm;
            flex: 1;
        }

        .label-footer {
            text-align: center;
            border-top: 0.2pt solid #CCC;
            padding-top: 0.8mm;
            margin-top: 0.8mm;
        }

        /* QR + teks seimbang di tinggi 3cm */
        .qr-box {
            flex-shrink: 0;
            /* QR akan disuntikkan sebagai SVG; ukuran di-atur dari helper -> size(90) */
        }

        .body-text {
            line-height: 1.1;
        }

        .body-text p {
            margin: 0;
        }

        .text-xxxxs {
            font-size: 0.44rem;
            /* sekitar 7 px */
            line-height: 0.6rem;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Toolbar non-cetak -->
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Cetak Halaman Ini
        </button>
        <a href="{{ route('assets.index') }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Kembali
        </a>
        <div class="text-sm text-gray-600 mt-2">
            <strong>Tips:</strong> Di dialog Print, gunakan <em>Scale 100%</em> dan <em>Margins: None</em> agar ukuran
            presisi.
        </div>
    </div>

    @foreach ($assets->chunk(12) as $chunk)
        <!-- Langkah 6: Grid 3 kolom x 4 baris (12 label) -->
        <div class="sheet">
            @foreach ($chunk as $asset)
                <div class="label-container bg-white shadow-sm">
                    <div class="label-header">
                        <p class="text-xxs text-gray-600 italic m-0">Property of</p>
                        <p class="font-bold text-xs m-0">{{ $asset->institution->name }}</p>
                    </div>

                    <div class="label-body">
                        <div class="qr-box">
                            @php
                                $publicDomain = 'https://sarpra.smktelkom-lpg.id';
                                $relativePath = route('public.assets.show', $asset->asset_code_ypt, false);
                                $fullPublicUrl = $publicDomain . $relativePath;
                            @endphp
                            {{-- Langkah 7: Perbesar QR agar terbaca (≈ 2.4 cm pada 96dpi) --}}
                            {!! QrCode::size(40)->generate($fullPublicUrl) !!}
                        </div>
                        <div class="body-text text-xxxxs">
                            <p class="font-bold text-xxs leading-tight">{{ $asset->name }}</p>
                            <p><span class="font-semibold">Tahun Reg :</span> {{ $asset->purchase_year }}</p>
                            <p><span class="font-semibold">Sumber Dana :</span> {{ $asset->fundingSource->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="label-footer text-xxxxs">
                        <p class="font-mono tracking-tighter m-0">{{ $asset->asset_code_ypt }}</p>
                        <p class="font-bold text-gray-700 mt-0.5 uppercase m-0">DO NOT REMOVE THIS LABEL</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>

</html>
