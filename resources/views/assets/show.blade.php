<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Aset: {{ $asset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 flex flex-col items-center">
                            <h3 class="text-lg font-bold mb-2">QR Code</h3>
                            <div class="p-4 border dark:border-gray-600 rounded-lg inline-block">
                                {{-- Ini adalah magic-nya! Generate QR code dengan URL halaman ini --}}
                                {!! QrCode::size(200)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Scan untuk membuka halaman ini</p>
                            <p class="font-mono text-center bg-gray-100 dark:bg-gray-700 p-2 rounded-md mt-4 text-xs">
                                {{ $asset->asset_code_ypt }}</p>
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <h4 class="font-bold">Informasi Dasar</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Nama Barang:</strong> {{ $asset->name }}</li>
                                    <li><strong>Tahun Pembelian:</strong> {{ $asset->purchase_year }}</li>
                                    <li><strong>No Urut:</strong> {{ $asset->sequence_number }}</li>
                                    <li><strong>Status:</strong> {{ $asset->status }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold">Lokasi & Kepemilikan</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Lembaga:</strong> {{ $asset->institution->name }}</li>
                                    <li><strong>Gedung:</strong> {{ $asset->building->name }}</li>
                                    <li><strong>Ruangan:</strong> {{ $asset->room->name }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold">Klasifikasi & Penanggung Jawab</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Kategori:</strong> {{ $asset->category->name }}</li>
                                    <li><strong>Fakultas/Direktorat:</strong> {{ $asset->faculty->name }}</li>
                                    <li><strong>Prodi/Unit:</strong> {{ $asset->department->name }}</li>
                                    <li><strong>Penanggung Jawab:</strong> {{ $asset->personInCharge->name }}</li>
                                    <li><strong>Fungsi Barang:</strong> {{ $asset->assetFunction->name }}</li>
                                    <li><strong>Sumber Dana:</strong> {{ $asset->fundingSource->name }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('assets.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Kembali
                        </a>
                        <a href="{{ route('assets.edit', $asset->id) }}"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        {{-- Nanti di sini bisa tambah tombol "Cetak Label" --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
