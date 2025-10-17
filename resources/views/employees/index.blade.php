<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Data Pegawai') }}
        </h2>
    </x-slot>

    <div x-data="crud">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="flex justify-between items-center mb-6">
                            <div class="flex flex-wrap gap-2">
                                <button @click="showModal = true; isEditMode = false; form.reset()"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Tambah Pegawai
                                </button>
                                {{-- Tombol Impor Baru --}}
                                <button @click="showImportModal = true"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Impor Excel
                                </button>
                            </div>
                            <form action="{{ route('employees.index') }}" method="GET">
                                <input type="text" name="search" placeholder="Cari pegawai..."
                                    class="form-input rounded-md dark:bg-gray-700" value="{{ request('search') }}">
                                <button type="submit"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">Cari</button>
                            </form>
                        </div>

                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">No</th>
                                        <th scope="col" class="py-3 px-6">Nama Pegawai</th>
                                        <th scope="col" class="py-3 px-6">NIP</th>
                                        <th scope="col" class="py-3 px-6">Jabatan</th>
                                        <th scope="col" class="py-3 px-6">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $index => $employee)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6">
                                                {{ ($employees->currentPage() - 1) * $employees->perPage() + $index + 1 }}
                                            </td>
                                            <td class="py-4 px-6 font-semibold">{{ $employee->name }}</td>
                                            <td class="py-4 px-6">{{ $employee->nip ?? '-' }}</td>
                                            <td class="py-4 px-6">{{ $employee->position }}</td>
                                            <td class="py-4 px-6 flex space-x-2">
                                                <button
                                                    @click="showModal = true; isEditMode = true; form.id = {{ $employee->id }}; form.name = '{{ $employee->name }}'; form.nip = '{{ $employee->nip }}'; form.position = '{{ $employee->position }}'"
                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Edit</button>
                                                <button @click="confirmDelete({{ $employee->id }})"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Hapus</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">Tidak ada data pegawai.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $employees->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md"
                @click.away="showModal = false">
                <h2 class="text-2xl font-bold mb-6" x-text="isEditMode ? 'Edit Pegawai' : 'Tambah Pegawai Baru'"></h2>
                <form :action="isEditMode ? '/employees/' + form.id : '{{ route('employees.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEditMode">@method('PUT')</template>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium">Nama Pegawai</label>
                        <input type="text" name="name" id="name" x-model="form.name"
                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                    </div>
                    <div class="mb-4">
                        <label for="nip" class="block text-sm font-medium">NIP (Opsional)</label>
                        <input type="text" name="nip" id="nip" x-model="form.nip"
                            class="mt-1 block w-full rounded-md dark:bg-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="position" class="block text-sm font-medium">Jabatan</label>
                        <input type="text" name="position" id="position" x-model="form.position"
                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showModal = false"
                            class="bg-gray-500 text-white font-bold py-2 px-4 rounded">Batal</button>
                        <button type="submit"
                            class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <div x-show="showImportModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md"
                @click.away="showImportModal = false">
                <h2 class="text-2xl font-bold mb-6">Impor Data Pegawai</h2>
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium">Pilih File Excel</label>
                        <input type="file" name="file" id="file"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0"
                            required>
                        <p class="mt-2 text-xs text-gray-500">Pastikan header: <strong>nama_pegawai</strong>,
                            <strong>nip</strong>, <strong>jabatan</strong>.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showImportModal = false"
                            class="bg-gray-500 text-white font-bold py-2 px-4 rounded">Batal</button>
                        <button type="submit"
                            class="bg-green-500 text-white font-bold py-2 px-4 rounded">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function pageData() {
                return {
                    showModal: false,
                    showImportModal: false,
                    isEditMode: false,
                    form: {
                        id: null,
                        name: '',
                        nip: '',
                        position: '',
                        reset() {
                            this.id = null;
                            this.name = '';
                            this.nip = '';
                            this.position = '';
                        }
                    },
                    confirmDelete(id) {
                        Swal.fire({
                            title: 'Anda yakin?',
                            text: "Data ini akan dihapus!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let form = document.createElement('form');
                                form.action = `/employees/${id}`;
                                form.method = 'POST';
                                form.innerHTML = `@csrf @method('DELETE')`;
                                document.body.appendChild(form);
                                form.submit();
                            }
                        })
                    }
                };
            }
            document.addEventListener('alpine:init', () => {
                Alpine.data('crud', pageData);
            });
        </script>
    @endpush
</x-app-layout>
