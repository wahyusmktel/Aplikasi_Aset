<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset (16 per Halaman)</title>
    @vite(['resources/css/app.css'])
    <style>
        /* CSS Khusus untuk Halaman Cetak */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            .label-container {
                page-break-inside: avoid !important;
                /* Mencegah label terpotong */
                border: 1px solid #AAA !important;
                /* Pastikan border tipis tercetak */
            }
        }

        @page {
            size: A4;
            margin: 0.8cm;
            /* Margin kertas diperkecil sedikit */
        }

        /* Ukuran font custom super kecil yang tidak ada di Tailwind standar */
        .text-xxs {
            font-size: 0.6rem;
            /* Sekitar 9.6px */
            line-height: 0.8rem;
        }

        .text-xxxs {
            font-size: 0.5rem;
            /* Sekitar 8px */
            line-height: 0.7rem;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Cetak Halaman Ini
        </button>
        <a href="{{ route('assets.index') }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Kembali
        </a>
    </div>

    {{-- === PERUBAHAN: p-4 ke p-1, gap-4 ke gap-1.5 === --}}
    <div class="p-1 grid grid-cols-2 gap-1.5">
        @foreach ($assets as $asset)
            {{-- === PERUBAHAN: p-3 ke p-1.5 (padding label lebih kecil) === --}}
            <div class="label-container border border-gray-400 rounded p-1.5 bg-white shadow-sm flex flex-col">

                {{-- === PERUBAHAN: pb-1 mb-1 (spacing lebih rapat) === --}}
                <div class="text-center border-b border-gray-300 pb-0.5 mb-1">
                    {{-- === PERUBAHAN: text-xs ke text-xxs === --}}
                    <p class="text-xxs text-gray-600 italic">Property of</p>
                    {{-- === PERUBAHAN: text-sm ke text-xs === --}}
                    <p class="font-bold text-xs">{{ $asset->institution->name }}</p>
                </div>

                {{-- === PERUBAHAN: gap-3 ke gap-2 === --}}
                <div class="flex-grow flex items-center gap-2">
                    <div class="flex-shrink-0">
                        {{-- === PERUBAHAN: size(80) ke size(45) (QR jauh lebih kecil) === --}}
                        {!! QrCode::size(45)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                    </div>
                    {{-- === PERUBAHAN: text-xs ke text-xxs, space-y-1 ke space-y-0.5 === --}}
                    <div class="text-xxs space-y-0.5 leading-tight">
                        {{-- === PERUBAHAN: text-sm ke text-xs (nama barang) === --}}
                        <p class="font-bold text-xs leading-tight">{{ $asset->name }}</p>
                        <p><span class="font-semibold">Tahun Reg:</span> {{ $asset->purchase_year }}</p>
                        <p><span class="font-semibold">Sumber Dana:</span> {{ $asset->fundingSource->name ?? '-' }}</p>
                    </div>
                </div>

                {{-- === PERUBAHAN: mt-2 pt-2 ke mt-1 pt-1 === --}}
                <div class="mt-1 pt-1 border-t border-gray-300 text-center">
                    {{-- === PERUBAHAN: text-xs ke text-xxxs, tracking-tighter === --}}
                    <p class="font-mono text-xxxs tracking-tighter">{{ $asset->asset_code_ypt }}</p>
                    {{-- === PERUBAHAN: text-xs ke text-xxxs === --}}
                    <p class="text-xxxs font-bold text-gray-700 mt-0.5 uppercase">DO NOT REMOVE THIS LABEL</p>
                </div>
            </div>
        @endforeach
    </div>

</body>

</html>
