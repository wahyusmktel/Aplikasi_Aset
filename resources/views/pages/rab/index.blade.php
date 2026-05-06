<x-app-layout x-data="rabPage()" x-init="initDeptMap(@json($departments->pluck('name', 'id')))"  >
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
                                            <button @click="openRealizationModal({{ $rab->id }}, '{{ $rab->name }}', {{ $rab->total_amount }}, {{ $rab->details->map(fn($d) => ['uraian' => $d->alias_name, 'penerimaan' => 0, 'pengeluaran' => $d->amount, 'keterangan' => 'Transaksi tgl ' . $d->created_at->format('d/m/Y')]) }}, {{ $rab->realization ? $rab->realization->details->map(fn($d) => ['tgl' => $d->tgl, 'uraian' => $d->uraian, 'qty' => $d->qty ?? '', 'spesifikasi' => $d->spesifikasi ?? '', 'penerimaan' => $d->penerimaan, 'pengeluaran' => $d->pengeluaran, 'keterangan' => $d->keterangan]) : 'null' }})"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-green-600 rounded-xl transition-all shadow-sm" title="Realisasi Anggaran">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </button>
                                            <button @click="openConversionModal({{ $rab->id }}, '{{ addslashes($rab->name) }}', {{ $rab->details->map(fn($d) => ['id' => $d->id, 'uraian' => $d->alias_name, 'quantity' => (int)$d->quantity, 'unit' => $d->unit, 'price' => $d->price]) }})"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-purple-600 rounded-xl transition-all shadow-sm" title="Konversi ke Daftar Aset">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0V9m0-2L14 5m4 4l-2 2M6 17H2m0 0v2m0-2l2-2m-2 2l2 2m16-6h-4m0 0v2m0-2l2-2m-2 2l2 2" /></svg>
                                            </button>
                                            @php
                                                $handoverItems = $rab->realization
                                                    ? $rab->realization->details->where('qty', '>', 0)->values()
                                                        ->map(fn($d) => ['uraian' => $d->uraian, 'qty' => $d->qty, 'spesifikasi' => $d->spesifikasi ?? ''])
                                                    : collect([]);
                                                $existingHandovers = $rab->handovers->map(fn($h) => [
                                                    'id'     => $h->id,
                                                    'docNum' => $h->document_number,
                                                    'dept'   => $h->department->name ?? '-',
                                                    'date'   => $h->handover_date->format('d/m/Y'),
                                                    'count'  => $h->items->count(),
                                                ]);
                                            @endphp
                                            @if($rab->realization && $handoverItems->count() > 0)
                                                <button @click="openHandoverModal({{ $rab->id }}, '{{ addslashes($rab->name) }}', {{ $handoverItems }})"
                                                    class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-orange-600 rounded-xl transition-all shadow-sm" title="Serah Terima Barang">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                                </button>
                                            @endif
                                            @if($rab->handovers->count() > 0)
                                                <button @click="openBastListModal('{{ addslashes($rab->name) }}', {{ $existingHandovers }}, {{ $rab->id }})"
                                                    class="relative p-2.5 bg-orange-100 dark:bg-orange-900/30 text-orange-600 hover:bg-orange-200 rounded-xl transition-all shadow-sm" title="Lihat BAST">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 text-white text-[8px] font-black rounded-full flex items-center justify-center">{{ $rab->handovers->count() }}</span>
                                                </button>
                                            @endif
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
        <template x-teleport="body">
            <div x-show="showRealizationModal"
                class="fixed inset-0 z-[9999] overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4" :class="{'p-0': isMaximized}">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                <div class="relative bg-white dark:bg-gray-950 shadow-2xl w-full overflow-hidden transform transition-all border border-gray-100 dark:border-gray-800 animate-fadeIn"
                    :class="isMaximized ? 'max-w-full h-screen rounded-none flex flex-col' : 'max-w-5xl rounded-[40px]'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                    <div class="p-8 border-b border-gray-50 dark:border-gray-900 flex justify-between items-center bg-gray-50/30 dark:bg-gray-900/20">
                        <div>
                            <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight" x-text="'Penyelesaian Realisasi: ' + selectedRab.name"></h3>
                            <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-black">Sesuaikan komponen realisasi sebelum mencetak PDF</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="isMaximized = !isMaximized" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full text-gray-400 transition-colors" title="Perbesar/Perkecil">
                                <svg x-show="!isMaximized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                <svg x-show="isMaximized" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h6m0 0v6m0-6l-7 7m17-11h-6m0 0V4m0 6l7-7M4 10h6m0 0V4m0 6l-7-7m17 11h-6m0 0v6m0-6l7 7" /></svg>
                            </button>
                            <button type="button" @click="showRealizationModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full text-gray-400 transition-colors" title="Tutup">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>

                    <form :action="`/rab/${selectedRab.id}/realization-pdf`" method="POST" class="p-8 overflow-y-auto" :class="isMaximized ? 'flex-1 max-h-none' : 'max-h-[70vh]'">
                        @csrf
                        <div class="overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-900/50 uppercase">
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest text-center w-12">No</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-32">TGL</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest">Uraian Kegiatan</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-20">Kuantitas</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-40">Spesifikasi</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-36">Penerimaan</th>
                                        <th class="p-4 text-[10px] font-black text-gray-400 tracking-widest w-36">Pengeluaran</th>
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
                                                <input type="number" name="qty[]" x-model="item.qty"
                                                    step="1" min="0"
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-center" placeholder="Opsional">
                                            </td>
                                            <td class="p-4">
                                                <input type="text" name="spesifikasi[]" x-model="item.spesifikasi"
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500" placeholder="Opsional">
                                            </td>
                                            <td class="p-4">
                                                <input type="text" name="penerimaan[]"
                                                    :value="formatRupiah(item.penerimaan)"
                                                    @blur="item.penerimaan = parseRupiah($event.target.value); $event.target.value = formatRupiah(item.penerimaan)"
                                                    class="w-full px-3 py-2 text-xs rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-right">
                                            </td>
                                            <td class="p-4">
                                                <input type="text" name="pengeluaran[]"
                                                    :value="formatRupiah(item.pengeluaran)"
                                                    @blur="item.pengeluaran = parseRupiah($event.target.value); $event.target.value = formatRupiah(item.pengeluaran)"
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
        </template>

        {{-- Modal Konversi ke Daftar Aset --}}
        <template x-teleport="body">
            <div x-show="showConversionModal"
                class="fixed inset-0 z-[9999] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

                    <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-6xl overflow-hidden border border-gray-100 dark:border-gray-800"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <form :action="`/rab/${conversionRab.id}/convert-to-assets`" method="POST">
                            @csrf
                            <div class="flex flex-col h-[88vh]">

                                {{-- Header --}}
                                <div class="p-8 border-b border-gray-50 dark:border-gray-900 flex items-center justify-between shrink-0 bg-gray-50/30 dark:bg-gray-900/20">
                                    <div>
                                        <h3 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">Konversi ke Daftar Aset</h3>
                                        <p class="text-sm text-gray-400 mt-1" x-text="'RAB: ' + conversionRab.name"></p>
                                    </div>
                                    <button type="button" @click="showConversionModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full text-gray-400 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div class="flex flex-1 overflow-hidden">
                                    {{-- Sidebar: Daftar Item RAB --}}
                                    <div class="w-72 bg-gray-50/50 dark:bg-gray-900/30 border-r border-gray-50 dark:border-gray-900 overflow-y-auto p-5 space-y-2 shrink-0">
                                        <div class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-3 px-1">Pilih Item yang Menjadi Aset</div>
                                        <template x-for="(item, idx) in conversionItems" :key="item.id">
                                            <div class="rounded-2xl transition-all border"
                                                :class="activeConversionItem === item.id
                                                    ? 'bg-purple-600 border-purple-600 shadow-lg shadow-purple-500/30'
                                                    : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-800 hover:border-purple-200'"
                                                @click="activeConversionItem = item.id">
                                                <div class="p-4 cursor-pointer">
                                                    <div class="flex items-start gap-3">
                                                        <input type="checkbox"
                                                            :id="'chk_' + item.id"
                                                            :name="'items[' + item.id + '][convert]'"
                                                            value="1"
                                                            x-model="conversionSelected[item.id]"
                                                            @click.stop
                                                            class="mt-0.5 w-4 h-4 rounded text-purple-600 border-gray-300 focus:ring-purple-500 shrink-0">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-black truncate"
                                                                :class="activeConversionItem === item.id ? 'text-white' : 'text-gray-700 dark:text-gray-200'"
                                                                x-text="item.uraian"></p>
                                                            <p class="text-[10px] mt-1"
                                                                :class="activeConversionItem === item.id ? 'text-purple-200' : 'text-gray-400'"
                                                                x-text="item.quantity + ' ' + item.unit"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Form Detail Aset --}}
                                    <div class="flex-1 overflow-y-auto p-8">
                                        <template x-for="(item, idx) in conversionItems" :key="'form_' + item.id">
                                            <div x-show="activeConversionItem === item.id"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 translate-y-3"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                class="space-y-6">

                                                <div class="flex items-center justify-between pb-6 border-b border-gray-100 dark:border-gray-800">
                                                    <div>
                                                        <h4 class="text-xl font-black text-gray-800 dark:text-white" x-text="item.uraian"></h4>
                                                        <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">Detail Penempatan Aset</p>
                                                    </div>
                                                    <div x-show="!conversionSelected[item.id]" class="flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800">
                                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Centang untuk dikonversi</span>
                                                    </div>
                                                </div>

                                                <div :class="!conversionSelected[item.id] ? 'opacity-40 pointer-events-none' : ''">
                                                    {{-- Hidden fields --}}
                                                    <input type="hidden" :name="'items[' + item.id + '][purchase_cost]'" :value="item.price">

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                        <div>
                                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Aset</label>
                                                            <input type="text" :name="'items[' + item.id + '][name]'" :value="item.uraian"
                                                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm font-bold">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Jumlah Aset Dibuat</label>
                                                            <input type="number" :name="'items[' + item.id + '][quantity]'" :value="item.quantity"
                                                                min="1" :max="item.quantity" step="1"
                                                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm font-bold">
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                        <div class="space-y-5">
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kategori Aset</label>
                                                                <select :name="'items[' + item.id + '][category_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach($categories as $cat)
                                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Institusi / Lembaga</label>
                                                                <select :name="'items[' + item.id + '][institution_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Institusi</option>
                                                                    @foreach($institutions as $inst)
                                                                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Lokasi Gedung</label>
                                                                <select :name="'items[' + item.id + '][building_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Gedung</option>
                                                                    @foreach($buildings as $b)
                                                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Ruangan / Lab</label>
                                                                <select :name="'items[' + item.id + '][room_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Ruangan</option>
                                                                    @foreach($rooms as $r)
                                                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Fakultas / Direktorat</label>
                                                                <select :name="'items[' + item.id + '][faculty_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Fakultas</option>
                                                                    @foreach($faculties as $f)
                                                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-5">
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Prodi / Unit Kerja</label>
                                                                <select :name="'items[' + item.id + '][department_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Unit</option>
                                                                    @foreach($departments as $d)
                                                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Penanggung Jawab (PIC)</label>
                                                                <select :name="'items[' + item.id + '][person_in_charge_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih PIC</option>
                                                                    @foreach($personsInCharge as $pic)
                                                                        <option value="{{ $pic->id }}">{{ $pic->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Fungsi Barang</label>
                                                                <select :name="'items[' + item.id + '][asset_function_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Fungsi</option>
                                                                    @foreach($assetFunctions as $af)
                                                                        <option value="{{ $af->id }}">{{ $af->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Sumber Pendanaan</label>
                                                                <select :name="'items[' + item.id + '][funding_source_id]'"
                                                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                                    <option value="">Pilih Sumber Dana</option>
                                                                    @foreach($fundingSources as $fs)
                                                                        <option value="{{ $fs->id }}">{{ $fs->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div x-show="conversionItems.length === 0" class="flex flex-col items-center justify-center h-full text-gray-300">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" /></svg>
                                            <p class="text-sm font-bold">Tidak ada item pada RAB ini</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="p-8 border-t border-gray-50 dark:border-gray-900 bg-gray-50/50 dark:bg-gray-950/50 flex items-center justify-between shrink-0">
                                    <div class="flex items-center text-amber-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Hanya item yang dicentang yang akan dikonversi menjadi aset</span>
                                    </div>
                                    <div class="flex gap-4">
                                        <button type="button" @click="showConversionModal = false" class="px-8 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 font-black rounded-2xl transition-all uppercase tracking-widest text-xs">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-10 py-3 bg-purple-600 hover:bg-purple-700 text-white font-black rounded-2xl shadow-xl shadow-purple-500/30 transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            Konversi & Buat Aset
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Serah Terima Barang --}}
        <template x-teleport="body">
            <div x-show="showHandoverModal"
                class="fixed inset-0 z-[9999] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display:none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

                    <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-5xl overflow-hidden border border-gray-100 dark:border-gray-800"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <form :action="`/rab/${handoverRab.id}/handover`" method="POST">
                            @csrf
                            <div class="flex flex-col" style="max-height:90vh;">

                                {{-- Header --}}
                                <div class="p-8 border-b border-gray-50 dark:border-gray-900 flex items-center justify-between shrink-0 bg-gray-50/30 dark:bg-gray-900/20">
                                    <div>
                                        <h3 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">Serah Terima Barang</h3>
                                        <p class="text-sm text-gray-400 mt-1" x-text="'RAB: ' + handoverRab.name"></p>
                                    </div>
                                    <button type="button" @click="showHandoverModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full text-gray-400 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div class="overflow-y-auto p-8 space-y-8">
                                    {{-- Info Global --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Tanggal Serah Terima</label>
                                            <input type="date" name="handover_date" x-model="handoverDate" required
                                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Penyerah (Pihak Pertama)</label>
                                            <input type="text" name="handed_by" x-model="handoverHandedBy" required placeholder="Nama pengelola/penyerah barang"
                                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm font-bold">
                                        </div>
                                    </div>

                                    {{-- Tabel Barang --}}
                                    <div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Pilih Barang & Tentukan Unit Tujuan</div>
                                        <div class="overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800">
                                            <table class="w-full text-left">
                                                <thead>
                                                    <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-10">
                                                            <input type="checkbox" @change="toggleAllHandover($event.target.checked)"
                                                                class="w-4 h-4 rounded text-orange-500 border-gray-300 focus:ring-orange-400">
                                                        </th>
                                                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Uraian Barang</th>
                                                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-20 text-center">Qty</th>
                                                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-44">Spesifikasi</th>
                                                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-52">Unit / Bagian Tujuan</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                                                    <template x-for="(item, idx) in handoverItems" :key="idx">
                                                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-900/30 transition-colors">
                                                            <td class="p-4">
                                                                <input type="checkbox" x-model="item.include"
                                                                    class="w-4 h-4 rounded text-orange-500 border-gray-300 focus:ring-orange-400">
                                                                {{-- Hidden fields submitted only when checked --}}
                                                                <template x-if="item.include">
                                                                    <span>
                                                                        <input type="hidden" :name="'items[' + idx + '][include]'" value="1">
                                                                        <input type="hidden" :name="'items[' + idx + '][uraian]'" :value="item.uraian">
                                                                        <input type="hidden" :name="'items[' + idx + '][qty]'" :value="item.qty">
                                                                        <input type="hidden" :name="'items[' + idx + '][spesifikasi]'" :value="item.spesifikasi">
                                                                        <input type="hidden" :name="'items[' + idx + '][dept_id]'" :value="item.deptId">
                                                                    </span>
                                                                </template>
                                                            </td>
                                                            <td class="p-4 text-sm font-bold text-gray-700 dark:text-gray-200" x-text="item.uraian"></td>
                                                            <td class="p-4 text-center text-sm font-black text-gray-600" x-text="item.qty ? Number(item.qty) : '-'"></td>
                                                            <td class="p-4 text-xs text-gray-500" x-text="item.spesifikasi || '-'"></td>
                                                            <td class="p-4">
                                                                <select x-model="item.deptId"
                                                                    :disabled="!item.include"
                                                                    class="w-full px-3 py-2 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 text-xs font-bold focus:border-orange-500 focus:ring-orange-400 disabled:opacity-40">
                                                                    <option value="">— Pilih Unit —</option>
                                                                    @foreach($departments as $dept)
                                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Nama Penerima per Unit --}}
                                    <div x-show="getUniqueDepts().length > 0">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Nama Penerima per Unit (Pihak Kedua)</div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <template x-for="deptId in getUniqueDepts()" :key="deptId">
                                                <div class="p-4 bg-orange-50 dark:bg-orange-900/10 rounded-2xl border border-orange-100 dark:border-orange-800/30">
                                                    <label class="block text-[10px] font-black text-orange-600 uppercase tracking-widest mb-2"
                                                        x-text="departmentMap[deptId] || ('Unit #' + deptId)"></label>
                                                    <input type="text"
                                                        :name="'received_by[' + deptId + ']'"
                                                        x-model="handoverReceivedBy[deptId]"
                                                        placeholder="Nama penerima dari unit ini"
                                                        class="w-full px-3 py-2 rounded-xl border-orange-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 text-sm font-bold focus:border-orange-500 focus:ring-orange-400">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="p-8 border-t border-gray-50 dark:border-gray-900 bg-gray-50/50 dark:bg-gray-950/50 flex items-center justify-between shrink-0">
                                    <div class="flex items-center text-orange-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Satu RAB dapat menghasilkan beberapa BAST sesuai unit tujuan</span>
                                    </div>
                                    <div class="flex gap-4">
                                        <button type="button" @click="showHandoverModal = false" class="px-8 py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 font-black rounded-2xl transition-all uppercase tracking-widest text-xs">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-10 py-3 bg-orange-500 hover:bg-orange-600 text-white font-black rounded-2xl shadow-xl shadow-orange-500/30 transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4" /></svg>
                                            Simpan & Buat BAST
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Daftar BAST --}}
        <template x-teleport="body">
            <div x-show="showBastListModal"
                class="fixed inset-0 z-[9999] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display:none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showBastListModal = false"></div>

                    <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-xl overflow-hidden border border-gray-100 dark:border-gray-800"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <div class="p-8 border-b border-gray-50 dark:border-gray-900 flex items-center justify-between bg-gray-50/30 dark:bg-gray-900/20">
                            <div>
                                <h3 class="text-xl font-black text-gray-800 dark:text-white tracking-tight">Daftar BAST</h3>
                                <p class="text-sm text-gray-400 mt-1" x-text="bastListRabName"></p>
                            </div>
                            <button type="button" @click="showBastListModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full text-gray-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="p-8 space-y-3 max-h-[60vh] overflow-y-auto">
                            <template x-for="(bast, idx) in bastList" :key="bast.id">
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-orange-200 dark:hover:border-orange-800 transition-colors">
                                    <div>
                                        <p class="text-sm font-black text-gray-800 dark:text-white" x-text="bast.docNum"></p>
                                        <p class="text-[10px] text-gray-400 mt-0.5" x-text="bast.dept + ' · ' + bast.count + ' barang · ' + bast.date"></p>
                                    </div>
                                    <a :href="`/rab/${bastListRabId}/handover/${bast.id}/pdf`"
                                        class="p-2.5 bg-orange-100 dark:bg-orange-900/30 text-orange-600 hover:bg-orange-200 rounded-xl transition-all" title="Unduh BAST PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </a>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function rabPage() {
                return {
                    showRealizationModal: false,
                    isMaximized: false,
                    selectedRab: { id: null, name: '', total_amount: 0 },
                    realizationItems: [],

                    showConversionModal: false,
                    conversionRab: { id: null, name: '' },
                    conversionItems: [],
                    conversionSelected: {},
                    activeConversionItem: null,

                    formatRupiah(val) {
                        const num = parseInt(String(val).replace(/\D/g, ''), 10) || 0;
                        return num.toLocaleString('id-ID');
                    },

                    parseRupiah(str) {
                        return parseInt(String(str).replace(/\./g, '').replace(/,/g, '').replace(/\D/g, ''), 10) || 0;
                    },

                    openRealizationModal(id, name, totalAmount, details, existingRealization = null) {
                        this.selectedRab = { id, name, total_amount: totalAmount };
                        this.isMaximized = false;

                        if (existingRealization && existingRealization.length > 0) {
                            this.realizationItems = existingRealization.map(item => ({
                                tgl: item.tgl,
                                uraian: item.uraian,
                                qty: item.qty ? Number(item.qty) : '',
                                spesifikasi: item.spesifikasi || '',
                                penerimaan: Number(item.penerimaan) || 0,
                                pengeluaran: Number(item.pengeluaran) || 0,
                                keterangan: item.keterangan || ''
                            }));
                        } else {
                            this.realizationItems = [
                                {
                                    tgl: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' }).replace(/ /g, '-'),
                                    uraian: 'Realisasi Dana',
                                    qty: '',
                                    spesifikasi: '',
                                    penerimaan: totalAmount,
                                    pengeluaran: 0,
                                    keterangan: ''
                                }
                            ];

                            details.forEach(detail => {
                                this.realizationItems.push({
                                    tgl: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' }).replace(/ /g, '-'),
                                    uraian: detail.uraian,
                                    qty: '',
                                    spesifikasi: '',
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
                            qty: '',
                            spesifikasi: '',
                            penerimaan: 0,
                            pengeluaran: 0,
                            keterangan: ''
                        });
                    },

                    removeRow(index) {
                        this.realizationItems.splice(index, 1);
                    },

                    openConversionModal(id, name, details) {
                        this.conversionRab = { id, name };
                        this.conversionItems = details;
                        this.conversionSelected = {};
                        details.forEach(d => { this.conversionSelected[d.id] = false; });
                        this.activeConversionItem = details.length > 0 ? details[0].id : null;
                        this.showConversionModal = true;
                    },

                    // ── Serah Terima ────────────────────────────────────────────
                    showHandoverModal: false,
                    handoverRab: { id: null, name: '' },
                    handoverItems: [],
                    handoverDate: new Date().toISOString().split('T')[0],
                    handoverHandedBy: '',
                    handoverReceivedBy: {},
                    departmentMap: {},

                    initDeptMap(map) {
                        this.departmentMap = map;
                    },

                    openHandoverModal(id, name, items) {
                        this.handoverRab = { id, name };
                        this.handoverDate = new Date().toISOString().split('T')[0];
                        this.handoverHandedBy = '';
                        this.handoverReceivedBy = {};
                        this.handoverItems = items.map(i => ({
                            uraian: i.uraian,
                            qty: i.qty ? Number(i.qty) : null,
                            spesifikasi: i.spesifikasi || '',
                            include: false,
                            deptId: '',
                        }));
                        this.showHandoverModal = true;
                    },

                    toggleAllHandover(checked) {
                        this.handoverItems.forEach(i => { i.include = checked; });
                    },

                    getUniqueDepts() {
                        const ids = [...new Set(
                            this.handoverItems
                                .filter(i => i.include && i.deptId)
                                .map(i => String(i.deptId))
                        )];
                        return ids;
                    },

                    // ── Daftar BAST ─────────────────────────────────────────────
                    showBastListModal: false,
                    bastListRabName: '',
                    bastListRabId: null,
                    bastList: [],

                    openBastListModal(rabName, handovers, rabId) {
                        this.bastListRabName = rabName;
                        this.bastListRabId = rabId;
                        this.bastList = handovers;
                        this.showBastListModal = true;
                    },
                }
            }

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
