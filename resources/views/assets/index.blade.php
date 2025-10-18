<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Aset') }}
        </h2>
    </x-slot>

    <div x-data="{ selectedIds: [], showImportBatchModal: false }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('assets.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Aset
                            </a>
                            <a href="{{ route('assets.batchCreate') }}"
                                class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Massal (Batch)
                            </a>
                            <button @click="showImportBatchModal = true"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Impor Massal
                            </button>
                            {{-- Tombol Cetak Terpilih --}}
                            <button @click="printSelected" :disabled="selectedIds.length === 0"
                                class="bg-purple-500 text-white font-bold py-2 px-4 rounded disabled:bg-purple-300 disabled:cursor-not-allowed">
                                Cetak Terpilih (<span x-text="selectedIds.length"></span>)
                            </button>
                            {{-- Tombol Cetak Semua --}}
                            <a href="{{ route('assets.printLabels') }}" target="_blank"
                                class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded">
                                Cetak Semua
                            </a>
                        </div>
                        <form action="{{ route('assets.index') }}" method="GET">
                            <div class="flex items-center">
                                <input type="text" name="search" placeholder="Cari nama atau kode aset..."
                                    class="form-input rounded-l-md dark:bg-gray-700 dark:text-gray-300 w-64"
                                    value="{{ request('search') }}">
                                <button type="submit"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-r-md">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4"></th>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Kode Aset YPT</th>
                                    <th scope="col" class="py-3 px-6">Nama Barang</th>
                                    <th scope="col" class="py-3 px-6">Tahun</th>
                                    <th scope="col" class="py-3 px-6">Lokasi</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assets as $index => $asset)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="p-4">
                                            {{-- Checkbox untuk setiap baris --}}
                                            <input type="checkbox" :value="{{ $asset->id }}" x-model="selectedIds"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ ($assets->currentPage() - 1) * $assets->perPage() + $index + 1 }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $asset->asset_code_ypt }}</td>
                                        <td class="py-4 px-6 font-semibold">{{ $asset->name }}</td>
                                        <td class="py-4 px-6">{{ $asset->purchase_year }}</td>
                                        <td class="py-4 px-6 text-xs">{{ $asset->building->name ?? '' }} /
                                            {{ $asset->room->name ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            <span @class([
                                                'px-2 py-1 font-semibold leading-tight text-xs rounded-full',
                                                'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' =>
                                                    $asset->current_status == 'Tersedia',
                                                'text-yellow-700 bg-yellow-100 dark:bg-yellow-700 dark:text-yellow-100' =>
                                                    $asset->current_status == 'Dipinjam' ||
                                                    $asset->current_status == 'Digunakan',
                                                'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' =>
                                                    $asset->current_status == 'Rusak',
                                            ])>
                                                {{ $asset->current_status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 flex flex-wrap gap-2">
                                            <a href="{{ route('assets.show', $asset->id) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                Detail
                                            </a>
                                            <a href="{{ route('assets.edit', $asset->id) }}"
                                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">
                                                Edit
                                            </a>
                                            <button onclick="confirmDelete({{ $asset->id }})"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 px-6 text-center">Belum ada data aset.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $assets->appends(['search' => request('search')])->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div x-show="showImportBatchModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="showImportBatchModal = false">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md"
                @click.away="showImportBatchModal = false">
                <h2 class="text-2xl font-bold mb-6">Impor Aset Massal</h2>
                <form action="{{ route('assets.importBatch') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                            File Excel</label>
                        <input type="file" name="file" id="file"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0"
                            required>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Gunakan template yang sesuai. Pastikan nama-nama data master (gedung, ruangan, dll) sudah
                            benar.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showImportBatchModal = false"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Batal</button>
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Fungsi untuk membuka tab baru dengan ID yang dipilih
    function printSelected() {
        const ids = this.selectedIds.join(',');
        if (ids) {
            const url = `{{ route('assets.printLabels') }}?ids=${ids}`;
            window.open(url, '_blank');
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Anda yakin?',
            text: "Data aset ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = `/assets/${id}`;
                form.method = 'POST';
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        })
    }
</script>
