<x-app-layout x-data="rkasPage()">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('RKAS') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">Rencana Kegiatan dan Anggaran Sekolah</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showYearSelectionModal = true"
                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-2xl transition-all shadow-xl shadow-red-500/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                    Tambah Data RKAS
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                        class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all shadow-sm">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-950 rounded-[24px] shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden">
                        <div class="p-2">
                            <a href="{{ route('rkas.template') }}" class="flex items-center px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-red-600 rounded-xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                Unduh Template
                            </a>
                            <button @click="showImportModal = true; open = false" class="w-full flex items-center px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-red-600 rounded-xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Impor Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-24">
            
            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-gray-950 p-6 rounded-[32px] border border-gray-100 dark:border-gray-800 shadow-sm">
                <form action="{{ route('rkas.index') }}" method="GET" class="flex flex-col md:flex-row gap-6 items-end">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Tahun Pelajaran</label>
                        <select name="academic_year_id" onchange="this.form.submit()"
                            class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 transition-all shadow-sm font-bold">
                            <option value="">Semua Tahun Pelajaran</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="px-8 py-4 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-black rounded-2xl hover:bg-gray-200 transition-all">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table Wrapper --}}
            <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="p-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kode & Akun</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Rincian Kegiatan</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Volume & Satuan</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tarif & Total</th>
                                <th class="py-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @forelse ($rkas as $item)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors group">
                                    <td class="p-6 px-10">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">{{ $item->mta }}</span>
                                            <span class="text-base font-bold text-gray-800 dark:text-white">{{ $item->nama_akun }}</span>
                                            <div class="flex gap-2 mt-1">
                                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md">{{ $item->kode_lokasi }}</span>
                                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md">{{ $item->academicYear->year }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 line-clamp-2">{{ $item->rincian_kegiatan }}</span>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tight mt-1">{{ $item->nama_drk }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-800 dark:text-white">{{ number_format($item->quantity, 0, ',', '.') }} {{ $item->satuan }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $item->bulan }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-400 font-bold">Rp {{ number_format($item->tarif, 0, ',', '.') }}</span>
                                            <span class="text-sm font-black text-red-600">Rp {{ number_format($item->tarif * $item->quantity, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-10 text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 transition-transform">
                                            <a href="{{ route('rkas.edit', $item->id) }}"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-red-600 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                            <button onclick="confirmDeleteRKAS({{ $item->id }})"
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
                                            <p class="text-xl font-bold text-gray-400">Tidak ada data RKAS ditemukan.</p>
                                            <p class="text-xs text-gray-300 mt-2 uppercase tracking-widest font-black">Pilih tahun pelajaran atau tambahkan data baru</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-10 border-t border-gray-50 dark:divide-gray-900 bg-gray-50/20 dark:bg-gray-900/10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-sm font-bold text-gray-400">
                        Menampilkan <span class="text-gray-800 dark:text-white">{{ $rkas->firstItem() ?? 0 }}</span> - <span class="text-gray-800 dark:text-white">{{ $rkas->lastItem() ?? 0 }}</span> dari <span class="text-gray-800 dark:text-white">{{ $rkas->total() }}</span> Data RKAS
                    </div>
                    <div>
                        {{ $rkas->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Year Selection --}}
        <div x-show="showYearSelectionModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showYearSelectionModal = false"></div>
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <div class="p-10">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Pilih Tahun Pelajaran</h2>
                            <button @click="showYearSelectionModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            @foreach($academicYears as $year)
                                <a href="{{ route('rkas.create', ['academic_year_id' => $year->id]) }}"
                                    class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-transparent hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all group">
                                    <span class="text-lg font-black text-gray-700 dark:text-gray-300 group-hover:text-red-600">{{ $year->year }}</span>
                                    <svg class="w-6 h-6 text-gray-300 group-hover:text-red-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            @endforeach
                            @if($academicYears->isEmpty())
                                <p class="text-center text-gray-400 py-10 font-bold uppercase text-xs tracking-widest">Belum ada data tahun pelajaran.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Import --}}
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showImportModal = false"></div>
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('rkas.import') }}" method="POST" enctype="multipart/form-data" class="p-10">
                        @csrf
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Impor RKAS</h2>
                            <button type="button" @click="showImportModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Tahun Pelajaran</label>
                                <select name="academic_year_id" required
                                    class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                                    <option value="">Pilih Tahun Pelajaran</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="p-8 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl flex flex-col items-center group hover:border-red-500/50 transition-all cursor-pointer relative">
                                <svg class="w-12 h-12 text-gray-300 mb-4 group-hover:scale-110 group-hover:text-red-600 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest text-center">Pilih file Excel</p>
                                <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                        </div>
                        <div class="flex justify-end mt-10 space-x-4">
                            <button type="button" @click="showImportModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                            <button type="submit" class="px-10 py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all">Mulai Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('rkasPage', () => ({
                    showYearSelectionModal: false,
                    showImportModal: false,
                }));
            });

            function confirmDeleteRKAS(id) {
                Swal.fire({
                    title: '<span class="text-xl font-black uppercase tracking-tight">Hapus Data?</span>',
                    html: '<p class="text-sm text-gray-400">Data RKAS akan dihapus secara permanen.</p>',
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
                        form.action = `/rkas/${id}`;
                        form.method = 'POST';
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                })
            }
        </script>
        <style>
            .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            @keyframes slideUp { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        </style>
    @endpush
</x-app-layout>
