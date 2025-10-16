<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Gedung') }}
        </h2>
    </x-slot>

    <div x-data="crud">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="flex justify-between items-center mb-6">
                            <button @click="showModal = true; isEditMode = false; form.reset()"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Gedung
                            </button>
                            <form action="{{ route('buildings.index') }}" method="GET">
                                <div class="flex items-center">
                                    <input type="text" name="search" placeholder="Cari gedung..."
                                        class="form-input rounded-l-md dark:bg-gray-700 dark:text-gray-300"
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
                                        <th scope="col" class="py-3 px-6">Nama Gedung</th>
                                        <th scope="col" class="py-3 px-6">Kode</th>
                                        <th scope="col" class="py-3 px-6">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($buildings as $index => $building)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="py-4 px-6">
                                                {{ ($buildings->currentPage() - 1) * $buildings->perPage() + $index + 1 }}
                                            </td>
                                            <td class="py-4 px-6">{{ $building->name }}</td>
                                            <td class="py-4 px-6">{{ $building->code }}</td>
                                            <td class="py-4 px-6 flex space-x-2">
                                                <button
                                                    @click="showModal = true; isEditMode = true; form.name = '{{ $building->name }}'; form.id = {{ $building->id }}"
                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">
                                                    Edit
                                                </button>
                                                <button @click="confirmDelete({{ $building->id }})"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 px-6 text-center">Tidak ada data gedung.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $buildings->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div x-show="showModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="showModal = false">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md"
                @click.away="showModal = false">
                <h2 class="text-2xl font-bold mb-6" x-text="isEditMode ? 'Edit Gedung' : 'Tambah Gedung Baru'"></h2>
                <form :action="isEditMode ? '/buildings/' + form.id : '{{ route('buildings.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEditMode">
                        @method('PUT')
                    </template>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Gedung</label>
                        <input type="text" name="name" id="name" x-model="form.name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showModal = false"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('crud', () => ({
            showModal: false,
            isEditMode: false,
            form: {
                id: null,
                name: '',
                reset() {
                    this.id = null;
                    this.name = '';
                }
            },
        }));
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Anda yakin?',
            text: "Data ini tidak akan bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = `/buildings/${id}`;
                form.method = 'POST';
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        })
    }
</script>
