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
                                            <span class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $rab->name }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ $rab->mta }}</span>
                                                @if($rab->realization)
                                                    <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-[8px] font-black rounded-lg uppercase tracking-widest border border-green-200 dark:border-green-800">Terealisasi</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">{{ $rab->academicYear->year }}</span>
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
                                            <button @click="openRealizationModal({{ $rab->id }}, '{{ $rab->name }}', {{ $rab->total_amount }}, {{ $rab->details->map(fn($d) => ['uraian' => $d->alias_name, 'penerimaan' => 0, 'pengeluaran' => $d->amount, 'keterangan' => 'Transaksi tgl ' . $d->created_at->format('d/m/Y')]) }}, {{ $rab->realization ? $rab->realization->details->map(fn($d) => ['tgl' => $d->tgl, 'uraian' => $d->uraian, 'penerimaan' => $d->penerimaan, 'pengeluaran' => $d->pengeluaran, 'keterangan' => $d->keterangan]) : 'null' }})"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-green-600 rounded-xl transition-all shadow-sm" title="Realisasi Anggaran">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </button>
                                            <a href="{{ route('rab.edit', $rab->id) }}"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-blue-600 rounded-xl transition-all shadow-sm" title="Edit RAB">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
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

        {{-- Modal Realisasi --}}
        <div x-show="showRealizationModal" 
            class="fixed inset-0 z-[60] overflow-y-auto" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showRealizationModal = false"></div>
                
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-5xl overflow-hidden transform transition-all border border-gray-100 dark:border-gray-800 animate-fadeIn"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    
                    <div class="p-8 border-b border-gray-50 dark:border-gray-900 flex justify-between items-center bg-gray-50/30 dark:bg-gray-900/20">
                        <div>
                            <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight" x-text="'Penyelesaian Realisasi: ' + selectedRab.name"></h3>
                            <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-black">Sesuaikan komponen realisasi sebelum mencetak PDF</p>
                        </div>
                        <button @click="showRealizationModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full text-gray-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form :action="`/rab/${selectedRab.id}/realization-pdf`" method="POST" class="p-8 overflow-y-auto max-h-[70vh]">
                        @csrf
                        <div class="overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-900/50 uppercase">
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest text-center w-12">No</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-32">TGL</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest">Uraian Kegiatan</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-32">Penerimaan</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-32">Pengeluaran</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-40">Keterangan</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest text-center w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                                    <template x-for="(item, index) in realizationItems" :key="index">
                                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-900/30">
                                            <td class="p-4 text-center text-xs font-black text-gray-400" x-text="index + 1"></td>
                                            <td class="p-4">
                                                <input type="text" name="tgl[]" x-model="item.tgl" 
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-center">
                                            </td>
                                            <td class="p-4">
                                                <input type="text" name="uraian[]" x-model="item.uraian" 
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500">
                                            </td>
                                            <td class="p-4">
                                                <input type="number" name="penerimaan[]" x-model="item.penerimaan" 
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-right">
                                            </td>
                                            <td class="p-4">
                                                <input type="number" name="pengeluaran[]" x-model="item.pengeluaran" 
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-right">
                                            </td>
                                            <td class="p-4">
                                                <input type="text" name="keterangan[]" x-model="item.keterangan" 
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500">
                                            </td>
                                            <td class="p-4 text-center">
                                                <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700 p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" @click="addRow()" class="px-6 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl transition-all shadow-sm flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                Tambah Baris
                            </button>
                            
                            <div class="flex gap-4">
                                <button type="button" @click="showRealizationModal = false" class="px-8 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 font-black rounded-2xl transition-all uppercase tracking-widest text-xs">
                                    Batal
                                </button>
                                <button type="submit" class="px-10 py-3 bg-green-600 hover:bg-green-700 text-white font-black rounded-2xl shadow-xl shadow-green-500/30 transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    Cetak Realisasi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function rabPage() {
                return {
                    showRealizationModal: false,
                    selectedRab: {
                        id: null,
                        name: '',
                        total_amount: 0,
                    },
                    realizationItems: [],

                    openRealizationModal(id, name, totalAmount, details, existingRealization = null) {
                        this.selectedRab = { id, name, total_amount: totalAmount };
                        
                        if (existingRealization && existingRealization.length > 0) {
                            this.realizationItems = existingRealization.map(item => ({
                                tgl: item.tgl,
                                uraian: item.uraian,
                                penerimaan: item.penerimaan,
                                pengeluaran: item.pengeluaran,
                                keterangan: item.keterangan || ''
                            }));
                        } else {
                            // Default first row: Realisasi Dana
                            this.realizationItems = [
                                {
                                    tgl: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' }).replace(/ /g, '-'),
                                    uraian: 'Realisasi Dana',
                                    penerimaan: totalAmount,
                                    pengeluaran: 0,
                                    keterangan: ''
                                }
                            ];

                            // Add expenditure rows from RAB details
                            details.forEach(detail => {
                                this.realizationItems.push({
                                    tgl: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' }).replace(/ /g, '-'),
                                    uraian: detail.uraian,
                                    penerimaan: 0,
                                    pengeluaran: detail.pengeluaran,
                                    keterangan: detail.keterangan
                                });
                            });
                        }

                        this.showRealizationModal = true;
                    },

                    addRow() {
                        this.realizationItems.push({
                            tgl: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' }).replace(/ /g, '-'),
                            uraian: '',
                            penerimaan: 0,
                            pengeluaran: 0,
                            keterangan: ''
                        });
                    },

                    removeRow(index) {
                        this.realizationItems.splice(index, 1);
                    },

                    formatNumber(num) {
                        return new Intl.NumberFormat('id-ID').format(num);
                    }
                }
            }

            function confirmDeleteRAB(id) {
                // ... (existing code remains same)
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
