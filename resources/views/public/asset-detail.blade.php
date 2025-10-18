{{-- Hapus <x-guest-layout> dari sini --}}

{{-- Kita akan gunakan layout manual agar lebih leluasa --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        {{-- INI KUNCINYA: Atur lebar maksimum di sini --}}
        <div
            class="w-full sm:max-w-7xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            @if ($isDisposed)
                {{-- Tampilan Jika Aset Sudah Dihapus --}}
                <div class="p-8 text-gray-900 dark:text-gray-100 text-center">
                    <div class="flex justify-center mb-4">
                        {{-- Icon Warning --}}
                        <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold">Aset Sudah Dihapus</h2>
                    <p class="text-gray-500 mt-1">Aset ini sudah tidak tercatat dalam inventaris aktif.</p>

                    <div class="mt-6 text-left border-t pt-6 dark:border-gray-700">
                        <h3 class="font-bold text-lg mb-2">Informasi Penghapusan</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Nama Aset</dt>
                                <dd class="w-2/3">{{ $asset->name }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Kode Aset</dt>
                                <dd class="w-2/3 font-mono">{{ $asset->asset_code_ypt }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Tanggal Hapus</dt>
                                <dd class="w-2/3">
                                    {{ \Carbon\Carbon::parse($asset->disposal_date)->isoFormat('D MMMM YYYY') }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Metode</dt>
                                <dd class="w-2/3">{{ $asset->disposal_method }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 font-semibold text-gray-500">Alasan</dt>
                                <dd class="w-2/3">{{ $asset->disposal_reason }}</dd>
                            </div>
                            @if ($asset->disposal_doc_number)
                                <div class="flex items-center">
                                    <dt class="w-1/3 font-semibold text-gray-500">No. BAPh</dt>
                                    <dd class="w-2/3 font-mono text-xs">{{ $asset->disposal_doc_number }}
                                        {{-- Link verifikasi BAPh jika perlu --}}
                                        (<a href="{{ route('public.verify', $asset->disposal_doc_number) }}"
                                            target="_blank" class="text-blue-500 hover:underline">Verifikasi</a>)
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @else
                {{-- KODE DETAIL ASET YANG SUDAH KITA BUAT SEBELUMNYA DIMULAI DARI SINI --}}
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                            Detail Aset
                        </h2>
                        <p class="text-md text-gray-500 mt-1">{{ $asset->institution->name }}</p>
                    </div>

                    <div class="flex flex-col md:flex-row items-start gap-8">
                        <div
                            class="w-full md:w-1/4 flex flex-col items-center text-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <div
                                class="p-4 border dark:border-gray-200 dark:border-gray-600 rounded-lg inline-block bg-white">
                                {!! QrCode::size(200)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                            </div>
                            <p
                                class="font-mono break-all bg-gray-200 dark:bg-gray-900/50 p-2 rounded-md mt-4 text-xs w-full">
                                {{ $asset->asset_code_ypt }}
                            </p>
                        </div>

                        <div class="w-full md:w-3/4 grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4
                                        class="text-lg font-bold border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                                        Informasi Dasar</h4>
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex">
                                            <dt class="w-2/5 font-semibold text-gray-500">Nama Barang</dt>
                                            <dd class="w-3/5">{{ $asset->name }}</dd>
                                        </div>
                                        <div class="flex">
                                            <dt class="w-2/5 font-semibold text-gray-500">Tahun Beli</dt>
                                            <dd class="w-3/5">{{ $asset->purchase_year }}</dd>
                                        </div>
                                        <div class="flex">
                                            <dt class="w-2/5 font-semibold text-gray-500">No Urut</dt>
                                            <dd class="w-3/5">{{ $asset->sequence_number }}</dd>
                                        </div>
                                        <div class="flex items-center">
                                            <dt class="w-2/5 font-semibold text-gray-500">Status</dt>
                                            <dd class="w-3/5"><span
                                                    class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">{{ $asset->status }}</span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                                <div>
                                    <h4
                                        class="text-lg font-bold border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                                        Lokasi & Kepemilikan</h4>
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex">
                                            <dt class="w-2/5 font-semibold text-gray-500">Lembaga</dt>
                                            <dd class="w-3/5">{{ $asset->institution->name }}</dd>
                                        </div>
                                        <div class="flex">
                                            <dt class="w-2/5 font-semibold text-gray-500">Gedung</dt>
                                            <dd class="w-3/5">{{ $asset->building->name }}</dd>
                                        </div>
                                        <div class="flex">
                                            <dt class="w-2/5 font-semibold text-gray-500">Ruangan</dt>
                                            <dd class="w-3/5">{{ $asset->room->name }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                                    Klasifikasi & Penanggung Jawab</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex">
                                        <dt class="w-2/5 font-semibold text-gray-500">Kategori</dt>
                                        <dd class="w-3/5">{{ $asset->category->name }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-2/5 font-semibold text-gray-500">Fakultas</dt>
                                        <dd class="w-3/5">{{ $asset->faculty->name }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-2/5 font-semibold text-gray-500">Prodi/Unit</dt>
                                        <dd class="w-3/5">{{ $asset->department->name }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-2/5 font-semibold text-gray-500">Penanggung Jawab</dt>
                                        <dd class="w-3/5">{{ $asset->personInCharge->name }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-2/5 font-semibold text-gray-500">Fungsi Barang</dt>
                                        <dd class="w-3/5">{{ $asset->assetFunction->name }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-2/5 font-semibold text-gray-500">Sumber Dana</dt>
                                        <dd class="w-3/5">{{ $asset->fundingSource->name }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- AKHIR DARI KODE DETAIL ASET --}}
            @endif
        </div>
        <div class="text-center mt-6 text-sm text-gray-500">
            Powered by {{ config('app.name', 'Laravel') }}
        </div>
    </div>
</body>

</html>

{{-- Hapus </x-guest-layout> dari sini --}}
