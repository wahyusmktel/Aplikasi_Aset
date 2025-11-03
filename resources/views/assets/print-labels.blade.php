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

        /* Ukuran font custom super kecil */
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

    <div class="p-1 grid grid-cols-2 gap-1.5">
        @foreach ($assets as $asset)
            <div class="label-container border border-gray-400 rounded p-1.5 bg-white shadow-sm flex flex-col">

                <div class="text-center border-b border-gray-300 pb-0.5 mb-1">
                    <p class="text-xxs text-gray-600 italic">Property of</p>
                    <p class="font-bold text-xs">{{ $asset->institution->name }}</p>
                </div>

                <div class="flex-grow flex items-center gap-2">
                    {{-- === PERUBAHAN DI SINI: Tambahkan `pl-1` === --}}
                    <div class="flex-shrink-0 pl-1">
                        {!! QrCode::size(45)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                    </div>
                    <div class="text-xxs space-y-0.5 leading-tight">
                        <p class="font-bold text-xs leading-tight">{{ $asset->name }}</p>
                        <p><span class="font-semibold">Tahun Reg:</span> {{ $asset->purchase_year }}</p>
                        <p><span class="font-semibold">Sumber Dana:</span> {{ $asset->fundingSource->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t border-gray-300 text-center">
                    <p class="font-mono text-xxxs tracking-tighter">{{ $asset->asset_code_ypt }}</p>
                    <p class="text-xxxs font-bold text-gray-700 mt-0.5 uppercase">DO NOT REMOVE THIS LABEL</p>
                </div>
            </div>
        @endforeach
    </div>

</body>

</html>
