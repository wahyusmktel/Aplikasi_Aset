<x-app-layout>
    <div x-data="crud" class="space-y-8">
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">{{ __('Manajemen Fungsi Barang') }}</h2>
                    <p class="text-primary-100 opacity-90">Kelola kategori fungsi penggunaan aset dalam organisasi.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button @click="showModal = true; isEditMode = false; form.reset()"
                        class="inline-flex items-center px-6 py-3 bg-white text-primary-700 hover:bg-primary-50 font-bold rounded-2xl shadow-lg transition-all duration-300 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Fungsi
                    </button>
                    <button @click="showImportModal = true"
                        class="inline-flex items-center px-6 py-3 bg-primary-500/20 border border-white/30 text-white hover:bg-white/10 font-bold rounded-2xl backdrop-blur-sm transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Impor Excel
                    </button>
                </div>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-3xl border border-gray-100 dark:border-gray-800 animate-slideUp" style="animation-delay: 100ms">
            <div class="p-8">
                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    <div class="w-full md:w-96">
                        <form action="{{ route('asset-functions.index') }}" method="GET" class="relative group">
                            <input type="text" name="search" placeholder="Cari fungsi barang..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-2 focus:ring-primary-500 transition-all duration-300 dark:text-gray-200"
                                value="{{ request('search') }}">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <button type="submit" class="hidden">Cari</button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-50 dark:border-gray-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Fungsi</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse ($assetFunctions as $index => $assetFunction)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-all duration-200 group">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ ($assetFunctions->currentPage() - 1) * $assetFunctions->perPage() + $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-sm mr-3 group-hover:scale-110 transition-transform duration-300">
                                                {{ substr($assetFunction->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $assetFunction->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                                            {{ $assetFunction->code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button @click="showModal = true; isEditMode = true; form.name = '{{ $assetFunction->name }}'; form.id = {{ $assetFunction->id }}"
                                                class="p-2 text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-xl transition-colors shadow-sm bg-white dark:bg-gray-800 border border-amber-100 dark:border-amber-900/30">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                            <button @click="confirmDelete({{ $assetFunction->id }})"
                                                class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-colors shadow-sm bg-white dark:bg-gray-800 border border-rose-100 dark:border-rose-900/30">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-4 rounded-full bg-gray-50 dark:bg-gray-800 mb-4">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data fungsi ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $assetFunctions->links() }}
                </div>
            </div>
        </div>

        <!-- Modals -->
        <div x-show="showModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-md"
            x-cloak>
            <div class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl p-8 rounded-3xl shadow-2xl w-full max-w-md border border-white/20"
                @click.away="showModal = false">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="isEditMode ? 'Edit Fungsi' : 'Tambah Fungsi'"></h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form :action="isEditMode ? '/asset-functions/' + form.id : '{{ route('asset-functions.store') }}'" method="POST" class="space-y-6">
                    @csrf
                    <template x-if="isEditMode">
                        @method('PUT')
                    </template>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Fungsi Barang</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200"
                            placeholder="Masukkan nama fungsi barang...">
                    </div>

                    <div class="flex gap-3 mt-8">
                        <button type="button" @click="showModal = false"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showImportModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-md"
            x-cloak>
            <div class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl p-8 rounded-3xl shadow-2xl w-full max-w-md border border-white/20"
                @click.away="showImportModal = false">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Impor Data</h2>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form action="{{ route('asset-functions.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="p-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl text-center hover:border-primary-400 transition-colors group">
                        <input type="file" name="file" id="file" class="hidden" required @change="fileName = $event.target.files[0].name">
                        <label for="file" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4 group-hover:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            <span class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2" x-text="fileName || 'Pilih file Excel'"></span>
                            <span class="text-xs text-gray-400 uppercase tracking-widest font-bold">xlsx, xls</span>
                        </label>
                    </div>
                    
                    <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-2xl text-center">
                        <p class="text-xs text-primary-700 dark:text-primary-300 leading-relaxed font-medium">
                            <span class="font-bold underline uppercase">Format Header:</span><br>
                            nama_fungsi_barang
                        </p>
                    </div>

                    <div class="flex gap-3 mt-8">
                        <button type="button" @click="showImportModal = false"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Upload File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('crud', () => ({
                showModal: false,
                showImportModal: false,
                isEditMode: false,
                fileName: '',
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
                title: 'Hapus Data?',
                text: "Data fungsi barang ini tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#0f172a',
                borderRadius: '1.5rem',
                customClass: {
                    confirmButton: 'rounded-2xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-2xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/asset-functions/${id}`;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
