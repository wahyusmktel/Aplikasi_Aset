<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Proses Penghapusan (Disposal) Aset
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Ringkasan Aset --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Detail Aset yang Akan Dihapus</h3>
                    <dl class="text-sm">
                        <div class="flex">
                            <dt class="w-1/3 text-gray-500">Nama Aset</dt>
                            <dd class="w-2/3 font-semibold">{{ $asset->name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-500">Kode Aset YPT</dt>
                            <dd class="w-2/3 font-mono">{{ $asset->asset_code_ypt }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-500">Kategori</dt>
                            <dd class="w-2/3">{{ $asset->category->name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-500">Tahun Perolehan</dt>
                            <dd class="w-2/3">{{ $asset->purchase_year }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Form Disposal --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ disposalMethod: '' }">
                    <form action="{{ route('disposals.store', $asset->id) }}" method="POST">
                        @csrf
                        <h3 class="text-lg font-bold mb-4">Formulir Penghapusan</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="disposal_date" class="block text-sm font-medium">Tanggal Penghapusan</label>
                                <input type="date" name="disposal_date" id="disposal_date"
                                    value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                    required>
                            </div>
                            <div>
                                <label for="disposal_method" class="block text-sm font-medium">Metode
                                    Penghapusan</label>
                                <select name="disposal_method" id="disposal_method" x-model="disposalMethod"
                                    class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="Dihapusbukukan (Rusak)">Dihapusbukukan (Rusak)</option>
                                    <option value="Hilang">Hilang</option>
                                    <option value="Dijual">Dijual</option>
                                    <option value="Dihibahkan">Dihibahkan</option>
                                </select>
                            </div>
                        </div>

                        {{-- Input Nilai Jual (Hanya muncul jika metode "Dijual") --}}
                        <div x-show="disposalMethod === 'Dijual'" x-transition class="mt-4">
                            <label for="disposal_value" class="block text-sm font-medium">Nilai Jual (Rp)</label>
                            <input type="number" name="disposal_value" id="disposal_value"
                                class="mt-1 block w-full rounded-md dark:bg-gray-700" placeholder="Contoh: 1500000"
                                :required="disposalMethod === 'Dijual'">
                        </div>

                        <div class="mt-4">
                            <label for="disposal_reason" class="block text-sm font-medium">Alasan / Keterangan
                                Penghapusan</label>
                            <textarea name="disposal_reason" id="disposal_reason" rows="3"
                                class="mt-1 block w-full rounded-md dark:bg-gray-700" required></textarea>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('assets.show', $asset->id) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
                                Proses Penghapusan & Unduh BAPh
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
