<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Rekanan / Vendor') }}
        </h2>
    </x-slot>

    <div x-data="crudComponent()" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Header Action & Search -->
            <div class="bg-white dark:bg-gray-950 overflow-hidden shadow-sm rounded-3xl border border-gray-100 dark:border-gray-800">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                        <div>
                            <button @click="openModal()" 
                                class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary-500/30 group">
                                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                {{ __('Tambah Rekanan') }}
                            </button>
                        </div>
                        <form action="{{ route('vendors.index') }}" method="GET" class="w-full md:w-auto">
                            <div class="relative group">
                                <input type="text" name="search" placeholder="Cari rekanan..."
                                    class="w-full md:w-80 pl-12 pr-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all shadow-sm"
                                    value="{{ request('search') }}">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-950 overflow-hidden shadow-sm rounded-3xl border border-gray-100 dark:border-gray-800 animate-fadeIn">
                <div class="overflow-x-auto text-gray-900 dark:text-gray-100">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-50 dark:border-gray-800">
                            <tr>
                                <th class="py-5 px-6 font-bold uppercase tracking-wider">No</th>
                                <th class="py-5 px-6 font-bold uppercase tracking-wider">Nama Rekanan</th>
                                <th class="py-5 px-6 font-bold uppercase tracking-wider">Kontak</th>
                                <th class="py-5 px-6 font-bold uppercase tracking-wider">Telepon/Email</th>
                                <th class="py-5 px-6 font-bold uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse ($vendors as $index => $vendor)
                                <tr class="hover:bg-primary-50/10 dark:hover:bg-primary-900/5 transition-colors group">
                                    <td class="py-4 px-6 text-gray-400 font-medium">
                                        {{ ($vendors->currentPage() - 1) * $vendors->perPage() + $index + 1 }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-black text-sm mr-3">
                                                {{ substr($vendor->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 dark:text-white">{{ $vendor->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $vendor->code ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 font-medium">{{ $vendor->contact_person ?? '-' }}</td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm font-medium">{{ $vendor->phone ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $vendor->email ?? '-' }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="openModal({{ json_encode($vendor) }})"
                                                class="p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                            <button @click="confirmDelete({{ $vendor->id }})"
                                                class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <p class="text-lg font-medium">{{ __('Belum ada data rekanan.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($vendors->hasPages())
                    <div class="p-6 border-t border-gray-50 dark:border-gray-800">
                        {{ $vendors->links() }}
                    </div>
                @endif
            </div>

    <!-- Modal Form -->
    <div x-show="showModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
            
            <div class="relative bg-white dark:bg-gray-950 rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden transform transition-all border border-gray-100 dark:border-gray-800 animate-slideUp">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white" x-text="isEditMode ? 'Edit Rekanan' : 'Tambah Rekanan Baru'"></h2>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form :action="isEditMode ? '/vendors/' + form.id : '{{ route('vendors.store') }}'" method="POST" class="space-y-6">
                        @csrf
                        <template x-if="isEditMode">
                            @method('PUT')
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Rekanan</label>
                                <input type="text" name="name" x-model="form.name" required
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Kode Vendor</label>
                                <input type="text" name="code" x-model="form.code"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Kontak (PIC)</label>
                                <input type="text" name="contact_person" x-model="form.contact_person"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" name="email" x-model="form.email"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Telepon</label>
                                <input type="text" name="phone" x-model="form.phone"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Alamat</label>
                                <textarea name="address" x-model="form.address" rows="3"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 space-x-4">
                            <button type="button" @click="showModal = false"
                                class="px-6 py-3 border border-gray-100 dark:border-gray-800 text-gray-500 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary-500/30">
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function crudComponent() {
            return {
                showModal: false,
                isEditMode: false,
                form: {
                    id: null,
                    name: '',
                    code: '',
                    contact_person: '',
                    email: '',
                    phone: '',
                    address: '',
                },
                openModal(data = null) {
                    if (data && data.id) {
                        this.isEditMode = true;
                        this.form = {
                            id: data.id,
                            name: data.name || '',
                            code: data.code || '',
                            contact_person: data.contact_person || '',
                            email: data.email || '',
                            phone: data.phone || '',
                            address: data.address || '',
                        };
                    } else {
                        this.isEditMode = false;
                        this.form = {
                            id: null,
                            name: '',
                            code: '',
                            contact_person: '',
                            email: '',
                            phone: '',
                            address: '',
                        };
                    }
                    this.showModal = true;
                }
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Menghapus rekanan dapat mempengaruhi data riwayat pengadaan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl border-none',
                    confirmButton: 'rounded-2xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-2xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/vendors/${id}`;
                    form.method = 'POST';
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
