<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <a href="{{ route('assets.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Aset
                        </a>
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
                                        <td class="py-4 px-6">
                                            {{ ($assets->currentPage() - 1) * $assets->perPage() + $index + 1 }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $asset->asset_code_ypt }}</td>
                                        <td class="py-4 px-6 font-semibold">{{ $asset->name }}</td>
                                        <td class="py-4 px-6">{{ $asset->purchase_year }}</td>
                                        <td class="py-4 px-6 text-xs">{{ $asset->building->name ?? '' }} /
                                            {{ $asset->room->name ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                                {{ $asset->status }}
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
    </div>
</x-app-layout>

<script>
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
