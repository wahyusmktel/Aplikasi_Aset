<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset</title>
    @vite(['resources/css/app.css'])
    <style>
        /* CSS Khusus untuk Halaman Cetak */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                /* Memastikan background color tercetak di Chrome */
                print-color-adjust: exact;
                /* Standar */
            }

            .no-print {
                display: none;
                /* Sembunyikan tombol saat mencetak */
            }

            .label-container {
                page-break-inside: avoid;
                /* Mencegah label terpotong antar halaman */
            }
        }

        @page {
            size: A4;
            margin: 1cm;
            /* Atur margin kertas */
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Tombol Aksi (Tidak ikut tercetak) -->
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Cetak Halaman Ini
        </button>
        <a href="{{ route('assets.index') }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Kembali
        </a>
    </div>

    <!-- Container untuk semua label -->
    <div class="p-4 grid grid-cols-2 gap-4">
        @foreach ($assets as $asset)
            <div class="label-container border border-gray-400 rounded-lg p-3 bg-white shadow-sm flex flex-col">

                {{-- === PERUBAHAN 1: Header Label === --}}
                <div class="text-center border-b border-gray-300 pb-2 mb-2">
                    <p class="text-xs text-gray-600 italic">Property of</p>
                    <p class="font-bold text-sm">{{ $asset->institution->name }}</p>
                </div>

                {{-- Body Label (QR + Details) --}}
                <div class="flex-grow flex items-center gap-3">
                    <div class="flex-shrink-0">
                        {!! QrCode::size(80)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                    </div>
                    <div class="text-xs space-y-1">
                        <p class="font-bold text-sm leading-tight">{{ $asset->name }}</p>
                        <p><span class="font-semibold">Lokasi:</span> {{ $asset->building->name }} /
                            {{ $asset->room->name }}</p>
                        {{-- === PERUBAHAN 2: Tanggal Registrasi === --}}
                        <p><span class="font-semibold">Tahun Reg:</span> {{ $asset->purchase_year }}</p>
                        <p><span class="font-semibold">Sumber Dana:</span> {{ $asset->fundingSource->name ?? '-' }}</p>
                    </div>
                </div>

                {{-- Footer Label (Kode Aset + Warning) --}}
                <div class="mt-2 pt-2 border-t border-gray-300 text-center">
                    <p class="font-mono text-xs tracking-tight">{{ $asset->asset_code_ypt }}</p>
                    {{-- === PERUBAHAN 3: Label Peringatan === --}}
                    <p class="text-xs font-bold text-gray-700 mt-1 uppercase">DO NOT REMOVE THIS LABEL</p>
                </div>
            </div>
        @endforeach
    </div>

</body>

</html>
