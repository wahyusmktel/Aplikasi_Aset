<x-app-layout x-data="rabPage()">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('RAB') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">Rencana Anggaran Biaya</p>
            </div>
            <div>
                <a href="{{ route('rab.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-2xl transition-all shadow-xl shadow-red-500/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                    Buat RAB Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-24">
            
            {{-- Table Wrapper --}}
            <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="p-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nama RAB & Tahun</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">MTA & Akun</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Waktu</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Anggaran</th>
                                <th class="py-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @forelse ($rabs as $rab)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors group">
                                    <td class="p-6 px-10">
                                        <div class="flex flex-col">
                                            <span class="text-base font-bold text-gray-800 dark:text-white">{{ $rab->name }}</span>
                                            <span class="text-[10px] font-black text-red-600 uppercase tracking-widest mt-1">{{ $rab->academicYear->year }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tight mb-1">{{ $rab->mta }}</span>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $rab->nama_akun }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <span class="text-xs font-bold text-gray-500 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">{{ $rab->kebutuhan_waktu }}</span>
                                    </td>
                                    <td class="py-6">
                                        <span class="text-base font-black text-red-600">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-6 px-10 text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 transition-transform">
                                            <a href="{{ route('rab.exportPdf', $rab->id) }}"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-red-600 rounded-xl transition-all shadow-sm" title="Cetak PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H12m-3 4h1.5m1.5 0H12m-3 4h1.5m1.5 0H12" /></svg>
                                            </a>
                                            <button onclick="confirmDeleteRAB({{ $rab->id }})"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-white hover:bg-red-600 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-6">
                                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                                            </div>
                                            <p class="text-xl font-bold text-gray-400">Tidak ada data RAB ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-10 border-t border-gray-50 dark:divide-gray-900 bg-gray-50/20 dark:bg-gray-900/10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        {{ $rabs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDeleteRAB(id) {
                Swal.fire({
                    title: '<span class="text-xl font-black uppercase tracking-tight">Hapus Data?</span>',
                    html: '<p class="text-sm text-gray-400">Data RAB akan dihapus secara permanen.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    padding: '2rem',
                    background: document.documentElement.classList.contains('dark') ? '#0a0a0a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    borderRadius: '2rem'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.action = `/rab/${id}`;
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
