<x-app-layout x-data="rabPage()">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('rab.index') }}" class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all flex items-center justify-center text-gray-400 hover:text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                        {{ $rab->name }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-sm text-gray-400 uppercase tracking-widest font-black whitespace-nowrap">RAB Preview • <span class="text-red-600">{{ $rab->academicYear->year }}</span></p>
                        @if($rab->realization)
                            <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-[8px] font-black rounded-lg uppercase tracking-widest border border-green-200 dark:border-green-800">Terealisasi</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('rab.exportPdf', $rab->id) }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-2xl transition-all shadow-xl shadow-red-500/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak PDF
                </a>
                <button @click="openRealizationModal({{ $rab->id }}, '{{ $rab->name }}', {{ $rab->total_amount }}, {{ $rab->details->map(fn($d) => ['uraian' => $d->alias_name, 'penerimaan' => 0, 'pengeluaran' => $d->amount, 'keterangan' => 'Transaksi tgl ' . $d->created_at->format('d/m/Y')]) }}, {{ $rab->realization ? $rab->realization->details->map(fn($d) => ['tgl' => $d->tgl, 'uraian' => $d->uraian, 'penerimaan' => $d->penerimaan, 'pengeluaran' => $d->pengeluaran, 'keterangan' => $d->keterangan]) : 'null' }})"
                    class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-black rounded-2xl transition-all shadow-xl shadow-green-500/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ $rab->realization ? 'Update Realisasi' : 'Realisasi' }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-24">
            {{-- Info Content --}}
            <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mata Anggaran (MTA)</span>
                        <span class="text-lg font-black text-red-600">{{ $rab->mta }}</span>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl lg:col-span-2">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Akun</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $rab->nama_akun }}</span>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Waktu Pelaksanaan</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $rab->kebutuhan_waktu }}</span>
                    </div>
                </div>

                {{-- Table Details --}}
                <div class="overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800 mb-8">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">No</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Uraian Kegiatan</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Spesifikasi</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Vol & Satuan</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Harga Satuan</th>
                                <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @foreach($rab->details as $index => $detail)
                                <tr>
                                    <td class="p-6 text-sm font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="p-6">
                                        <div class="flex flex-col">
                                            <span class="text-base font-bold text-gray-800 dark:text-white">{{ $detail->alias_name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $detail->rkas->rincian_kegiatan }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $detail->specification ?? '-' }}</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span class="text-sm font-bold text-gray-800 dark:text-white">{{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit }}</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span class="text-sm font-bold text-gray-400">Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span class="text-base font-black text-red-600">Rp {{ number_format($detail->amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <td colspan="5" class="p-8 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Total Keseluruhan Anggaran:</td>
                                <td class="p-8 text-right">
                                    <span class="text-2xl font-black text-red-600">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Signature Table --}}
                <div class="mt-12 overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-40"></th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">NAMA / NIK</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">JABATAN</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">TANGGAL</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">TANDA TANGAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                        @if($rab->creator)
                            <tr>
                                <td class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Dibuat oleh</td>
                                <td class="p-4 text-sm font-bold text-gray-800 dark:text-white">{{ $rab->creator->name }} / {{ $rab->creator->nip ?? '-' }}</td>
                                <td class="p-4 text-sm text-gray-500">{{ $rab->creator->position ?? '-' }}</td>
                                <td class="p-4 text-sm text-center text-gray-500">{{ $rab->created_at->format('d-M-y') }}</td>
                                <td class="p-4"></td>
                            </tr>
                        @endif
                        @if($rab->checker)
                            <tr>
                                <td class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Diperiksa oleh</td>
                                <td class="p-4 text-sm font-bold text-gray-800 dark:text-white">{{ $rab->checker->name }} / {{ $rab->checker->nip ?? '-' }}</td>
                                <td class="p-4 text-sm text-gray-500">{{ $rab->checker->position ?? '-' }}</td>
                                <td class="p-4 text-sm text-center text-gray-500">{{ $rab->created_at->format('d-M-y') }}</td>
                                <td class="p-4"></td>
                            </tr>
                        @endif
                        <tr class="bg-gray-50/20">
                            <td colspan="5" class="p-6">
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan Anggaran:</span>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $rab->notes ?? '-' }}</p>
                            </td>
                        </tr>
                        @if($rab->approver)
                            <tr>
                                <td class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Diperiksa & Disetujui oleh</td>
                                <td class="p-4 text-sm font-bold text-gray-800 dark:text-white">{{ $rab->approver->name }} / {{ $rab->approver->nip ?? '-' }}</td>
                                <td class="p-4 text-sm text-gray-500">{{ $rab->approver->position ?? '-' }}</td>
                                <td class="p-4 text-sm text-center text-gray-500">{{ $rab->created_at->format('d-M-y') }}</td>
                                <td class="p-4"></td>
                            </tr>
                        @endif
                        @if($rab->headmaster)
                            <tr>
                                <td class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Diperiksa & Disetujui Realisasi</td>
                                <td class="p-4 text-sm font-bold text-gray-800 dark:text-white">{{ $rab->headmaster->name }} / {{ $rab->headmaster->nip ?? '-' }}</td>
                                <td class="p-4 text-sm text-gray-500">Kepala Sekolah</td>
                                <td class="p-4 text-sm text-center text-gray-500">{{ $rab->created_at->format('d-M-y') }}</td>
                                <td class="p-4"></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
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
                                    keterangan: detail.keterangan || ''
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
        </script>
    @endpush
</x-app-layout>
