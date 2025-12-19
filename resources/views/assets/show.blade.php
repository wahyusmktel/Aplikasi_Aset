<x-app-layout x-data="pageData()">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('assets.index') }}" class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 transition-all shadow-sm group">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <h2 class="font-black text-2xl text-gray-800 dark:text-white leading-tight tracking-tight">
                        Detail Aset
                    </h2>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-black">{{ $asset->asset_code_ypt }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if (!$asset->disposal_date)
                    <a href="{{ route('assets.edit', $asset->id) }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-black rounded-2xl hover:bg-gray-200 transition-all">
                        Edit Data
                    </a>
                    <a href="{{ route('disposals.create', $asset->id) }}"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1">
                        Proses Disposal
                    </a>
                @else
                    <div class="px-6 py-3 bg-purple-100 text-purple-600 font-black rounded-2xl flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        Sudah di-Dispose
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-24">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left: QR & Main Info --}}
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white dark:bg-gray-950 p-10 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col items-center text-center">
                        <div class="p-6 bg-white rounded-3xl border-2 border-gray-50 shadow-inner mb-6">
                            {!! QrCode::size(180)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                        </div>
                        <h3 class="text-xl font-black text-gray-800 dark:text-white leading-tight mb-2">{{ $asset->name }}</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">{{ $asset->category->name ?? '-' }}</p>
                        
                        @php
                            $statusColors = [
                                'Tersedia' => 'bg-emerald-100 text-emerald-600',
                                'Dipinjam' => 'bg-blue-100 text-blue-600',
                                'Rusak' => 'bg-red-100 text-red-600',
                            ];
                            $statCls = $statusColors[$asset->current_status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <div class="px-6 py-2 rounded-full font-black text-[10px] uppercase tracking-widest {{ $statCls }} shadow-sm">
                            Status: {{ $asset->current_status }}
                        </div>

                        <div class="mt-8 w-full space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl border border-gray-50 dark:border-gray-900">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun Beli</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $asset->purchase_year }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl border border-gray-50 dark:border-gray-900">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga Beli</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    <div class="bg-gray-800 text-white p-8 rounded-[40px] shadow-2xl relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-red-500/20 transition-all duration-700"></div>
                        <h4 class="text-lg font-black mb-6 relative">Tindakan Cepat</h4>
                        <div class="grid grid-cols-1 gap-3 relative">
                            <button @click="window.print()" class="w-full p-4 bg-white/10 hover:bg-white text-white hover:text-gray-900 rounded-2xl text-sm font-black transition-all flex items-center justify-between">
                                Cetak Detail Aset
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            </button>
                            @if($asset->disposal_date)
                                <a href="{{ route('disposals.downloadBaph', $asset->id) }}" target="_blank" class="w-full p-4 bg-white/10 hover:bg-purple-600 rounded-2xl text-sm font-black transition-all flex items-center justify-between">
                                    Unduh Dokumen BAPh
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Tabs & Details --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Nav Tabs --}}
                    <div class="flex bg-gray-100 dark:bg-gray-900 p-2 rounded-[28px] gap-2">
                        <button @click="activeTab = 'detail'" :class="activeTab === 'detail' ? 'bg-white dark:bg-gray-800 text-red-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 text-xs font-black uppercase tracking-widest rounded-[22px] transition-all">Detail Info</button>
                        <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-white dark:bg-gray-800 text-red-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 text-xs font-black uppercase tracking-widest rounded-[22px] transition-all">Riwayat</button>
                        <button @click="activeTab = 'maintenance'" :class="activeTab === 'maintenance' ? 'bg-white dark:bg-gray-800 text-red-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 text-xs font-black uppercase tracking-widest rounded-[22px] transition-all">Maintenance</button>
                        @if($asset->category->name == 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
                            <button @click="activeTab = 'vehicle'" :class="activeTab === 'vehicle' ? 'bg-white dark:bg-gray-800 text-red-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 text-xs font-black uppercase tracking-widest rounded-[22px] transition-all">Log KBM</button>
                        @endif
                    </div>

                    {{-- Tab Contents --}}
                    <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden animate-fadeIn">
                        {{-- Detail Tab --}}
                        <div x-show="activeTab === 'detail'" class="p-10 space-y-12">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                <div class="space-y-6">
                                    <h4 class="text-xs font-black text-red-600 uppercase tracking-[0.2em] pb-3 border-b border-gray-100 dark:border-gray-900">Lokasi & Penempatan</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Gedung / Ruangan</p>
                                            <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->building->name }} / {{ $asset->room->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Lembaga</p>
                                            <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->institution->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Fakultas & Unit</p>
                                            <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->faculty->name }} - {{ $asset->department->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <h4 class="text-xs font-black text-red-600 uppercase tracking-[0.2em] pb-3 border-b border-gray-100 dark:border-gray-900">Finansial & Status</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Nilai Buku Saat Ini</p>
                                            <p class="text-lg font-black text-red-600">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Masa Manfaat</p>
                                                <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->useful_life ?? '-' }} Tahun</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Nilai Sisa</p>
                                                <p class="text-sm font-black text-gray-800 dark:text-white">Rp {{ number_format($asset->salvage_value, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8 bg-gray-50/50 dark:bg-gray-900/50 rounded-3xl border border-gray-50 dark:border-gray-900 grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">PIC (Awal)</p>
                                    <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->personInCharge->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Fungsi Barang</p>
                                    <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->assetFunction->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Sumber Dana</p>
                                    <p class="text-sm font-black text-gray-800 dark:text-white">{{ $asset->fundingSource->name }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- History Tab --}}
                        <div x-show="activeTab === 'history'" class="p-0">
                            {{-- Current Assignment Status --}}
                            <div class="p-10 border-b border-gray-50 dark:border-gray-900">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Status Penugasan</h3>
                                    @if($asset->current_status == 'Tersedia' && $asset->category->name != 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
                                        <button @click="showAssignModal = true" class="px-5 py-2.5 bg-red-600 text-white text-xs font-black rounded-xl shadow-lg shadow-red-500/20 active:scale-95 transition-all">
                                            Serah Terima Aset
                                        </button>
                                    @endif
                                </div>

                                @if($asset->currentAssignment)
                                    <div class="p-6 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-800/50 rounded-3xl flex items-center justify-between">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-xl">
                                                {{ substr($asset->currentAssignment->employee->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-blue-400 uppercase tracking-widest">Dipegang oleh</p>
                                                <p class="text-base font-black text-blue-900 dark:text-blue-300">{{ $asset->currentAssignment->employee->name }}</p>
                                                <p class="text-[10px] text-blue-400 mt-1 italic">Dipinjam sejak {{ \Carbon\Carbon::parse($asset->currentAssignment->assigned_date)->isoFormat('D MMMM YYYY') }}</p>
                                            </div>
                                        </div>
                                        <button @click="showReturnModal = true" class="px-4 py-2 bg-white dark:bg-gray-800 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-xl border border-blue-100 shadow-sm hover:translate-x-1 transition-all">
                                            Kembalikan
                                        </button>
                                    </div>
                                @else
                                    <div class="p-10 text-center bg-gray-50/50 dark:bg-gray-900/50 rounded-[32px] border border-dashed border-gray-100 dark:border-gray-800">
                                        <p class="text-sm font-bold text-gray-400">Aset dalam kondisi Tersedia (Gudang)</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Assignments History Table --}}
                            <div class="p-10 pt-0 mt-8">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 px-1">Riwayat Peminjaman</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                                <th class="p-4 px-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest rounded-l-2xl">Nama Pegawai</th>
                                                <th class="p-4 px-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Tgl Pinjam / Kembali</th>
                                                <th class="p-4 px-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Kondisi</th>
                                                <th class="p-4 px-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right rounded-r-2xl">BAST</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                                            @forelse ($asset->assignments()->latest()->get() as $assignment)
                                                <tr class="group transition-colors hover:bg-gray-50/30 dark:hover:bg-gray-900/30">
                                                    <td class="p-5 px-6 font-bold text-gray-700 dark:text-gray-300 text-sm italic">{{ $assignment->employee->name }}</td>
                                                    <td class="p-5 px-6 text-center">
                                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($assignment->assigned_date)->isoFormat('D MMM YY') }}</span>
                                                        <span class="mx-2 text-gray-300">→</span>
                                                        @if ($assignment->returned_date)
                                                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ \Carbon\Carbon::parse($assignment->returned_date)->isoFormat('D MMM YY') }}</span>
                                                        @else
                                                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Aktif</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-5 px-6 text-center">
                                                        <span class="text-[10px] bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-lg text-gray-500 font-bold uppercase">{{ $assignment->condition_on_assign }}</span>
                                                    </td>
                                                    <td class="p-5 px-6 text-right">
                                                        <div class="flex justify-end gap-2">
                                                            @if ($assignment->checkout_doc_number)
                                                                <a href="{{ route('assignments.downloadBast', ['assignment' => $assignment->id, 'type' => 'checkout']) }}" target="_blank" class="p-2 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-red-600 rounded-lg border border-gray-100 transition-all">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="p-10 text-center text-xs font-bold text-gray-300">Belum ada riwayat.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Maintenance Tab --}}
                        <div x-show="activeTab === 'maintenance'" class="p-10 space-y-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Kesehatan Aset</h3>
                                <button @click="showMaintenanceModal = true" class="px-5 py-2.5 bg-gray-800 dark:bg-white dark:text-gray-950 text-white text-xs font-black rounded-xl">
                                    Catat Pekerjaan
                                </button>
                            </div>

                            {{-- Maintenance & Inspection Cards --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- Maintenance Sub-tab --}}
                                <div class="space-y-6">
                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Riwayat Perbaikan</h5>
                                    <div class="space-y-4">
                                        @forelse($asset->maintenances()->latest()->take(5)->get() as $m)
                                            <div class="p-6 bg-gray-50/50 dark:bg-gray-900/50 rounded-3xl border border-gray-50 dark:border-gray-900 relative group overflow-hidden">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <span class="text-[9px] font-black text-red-600 uppercase tracking-widest leading-none">{{ $m->type }}</span>
                                                        <p class="text-sm font-black text-gray-800 dark:text-white mt-1 leading-snug">{{ $m->description }}</p>
                                                        <div class="flex items-center mt-3 text-[10px] text-gray-400 font-bold uppercase group-hover:text-gray-600 transition-colors">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                            {{ \Carbon\Carbon::parse($m->maintenance_date)->isoFormat('D MMM YYYY') }}
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-xs font-black text-gray-800 dark:text-white">Rp {{ number_format($m->cost, 0, ',', '.') }}</p>
                                                        <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">{{ $m->technician ?? 'Staf Internal' }}</p>
                                                    </div>
                                                </div>
                                                <div class="absolute right-0 bottom-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    @if($m->doc_number)
                                                        <a href="{{ route('maintenance.downloadReport', $m->id) }}" target="_blank" class="p-2 bg-white rounded-lg shadow-sm block translate-y-2 group-hover:translate-y-0 transition-transform">
                                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <p class="p-10 text-center text-[10px] font-black text-gray-300 uppercase leading-relaxed">Belum ada riwayat perbaikan.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Inspections Sub-tab --}}
                                <div class="space-y-6">
                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Pemeriksaan Kondisi</h5>
                                    <div class="space-y-4">
                                        @forelse($asset->inspections()->latest()->take(5)->get() as $i)
                                            <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-3xl flex items-center justify-between group">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-2 h-10 @if($i->condition == 'Baik') bg-emerald-500 @elseif($m->condition == 'Rusak Berat') bg-red-500 @else bg-amber-500 @endif rounded-full"></div>
                                                    <div>
                                                        <p class="text-sm font-black text-gray-800 dark:text-white tracking-tight">{{ $i->condition }}</p>
                                                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5 tracking-wider">{{ \Carbon\Carbon::parse($i->inspection_date)->isoFormat('D MMM YYYY') }}</p>
                                                    </div>
                                                </div>
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                    @if($i->inspection_doc_number)
                                                        <a href="{{ route('inspections.downloadBast', $i->id) }}" target="_blank" class="p-2 border border-blue-50 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <p class="p-10 text-center text-[10px] font-black text-gray-300 uppercase leading-relaxed font-bold italic">Belum pernah diinspeksi.</p>
                                        @endforelse
                                    </div>
                                    <button @click="showInspectionModal = true" class="w-full py-4 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-red-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-3xl border border-dashed border-gray-100 dark:border-gray-800 transition-all">
                                        + Tambah Catatan Inspeksi
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Vehicle Log Tab --}}
                        @if($asset->category->name == 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
                            <div x-show="activeTab === 'vehicle'" class="p-10 space-y-10">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight tracking-tight italic">Logbook KBM</h3>
                                    @if($asset->current_status == 'Tersedia')
                                        <button @click="showVehicleModal = true" class="px-6 py-3 bg-red-600 text-white text-xs font-black rounded-2xl shadow-xl shadow-red-500/20 active:translate-y-1 transition-all">
                                            Catat Perjalanan
                                        </button>
                                    @endif
                                </div>

                                @if($asset->currentVehicleLog)
                                    <div class="p-8 bg-amber-50/50 border border-amber-100 rounded-[32px] flex flex-col md:flex-row items-center justify-between gap-6">
                                        <div class="flex items-center gap-6">
                                            <div class="animate-pulse w-4 h-4 bg-amber-500 rounded-full shadow-lg shadow-amber-500/40"></div>
                                            <div>
                                                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Sedang Digunakan</p>
                                                <p class="text-base font-black text-gray-800 mt-1">{{ $asset->currentVehicleLog->employee->name }}</p>
                                                <p class="text-xs text-gray-500 font-bold mt-1">Ke: {{ $asset->currentVehicleLog->destination }}</p>
                                            </div>
                                        </div>
                                        <button @click="showVehicleReturnModal = true" class="px-8 py-4 bg-gray-800 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl hover:-translate-y-1">
                                            Selesai Perjalanan
                                        </button>
                                    </div>
                                @endif

                                <div class="overflow-x-auto rounded-[32px] border border-gray-50 dark:border-gray-900">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                                <th class="p-5 px-8 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pengemudi</th>
                                                <th class="p-5 px-8 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Waktu & KM</th>
                                                <th class="p-5 px-8 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                                            @foreach($asset->vehicleLogs()->latest()->take(10)->get() as $log)
                                                <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-900/30 transition-colors">
                                                    <td class="p-6 px-8">
                                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300 italic">{{ $log->employee->name }}</p>
                                                        <p class="text-[10px] text-gray-400 font-bold mt-1">{{ $log->destination }}</p>
                                                    </td>
                                                    <td class="p-6 px-8 text-center">
                                                        <div class="flex flex-col items-center">
                                                            <span class="text-[10px] font-black text-gray-800 dark:text-white uppercase tracking-widest">{{ \Carbon\Carbon::parse($log->departure_time)->isoFormat('D MMM HH:mm') }}</span>
                                                            <div class="w-10 h-[1px] bg-gray-100 my-1"></div>
                                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ number_format($log->start_odometer) }} KM → {{ $log->end_odometer ? number_format($log->end_odometer) : '...' }} KM</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-6 px-8 text-right">
                                                        @if($log->checkout_doc_number)
                                                            <a href="{{ route('vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkout']) }}" target="_blank" class="px-4 py-2 bg-gray-50 dark:bg-gray-900 text-[10px] font-black uppercase text-gray-400 hover:text-red-600 rounded-xl transition-all border border-gray-100">BAST</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modals Section --}}
        
        <!-- Modal: Assign Asset -->
        <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAssignModal = false"></div>
            <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                <form action="{{ route('assets.assign', $asset->id) }}" method="POST" class="p-10">
                    @csrf
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-8">Serah Terima Aset</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Pegawai Penerima</label>
                            <select name="employee_id" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                                @foreach (App\Models\Employee::orderBy('name')->get() as $e) 
                                    <option value="{{ $e->id }}">{{ $e->name }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kondisi Barang</label>
                            <input type="text" name="condition_on_assign" value="Baik" class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                        </div>
                    </div>
                    <div class="flex justify-end mt-10 gap-4">
                        <button type="button" @click="showAssignModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                        <button type="submit" class="px-8 py-4 bg-red-600 text-white font-black rounded-2xl shadow-xl shadow-red-500/20">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Return Asset -->
        @if($asset->currentAssignment)
        <div x-show="showReturnModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showReturnModal = false"></div>
            <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                <form action="{{ route('assets.return', $asset->currentAssignment->id) }}" method="POST" class="p-10">
                    @csrf
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-8">Pengembalian Aset</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kondisi Saat Kembali</label>
                            <select name="condition_on_return" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                                <option value="Baik">Baik</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end mt-10 gap-4">
                        <button type="button" @click="showReturnModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                        <button type="submit" class="px-8 py-4 bg-gray-800 text-white font-black rounded-2xl shadow-xl">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Modal: Maintenance Log -->
        <div x-show="showMaintenanceModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showMaintenanceModal = false"></div>
            <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                <form action="{{ route('maintenance.store', $asset->id) }}" method="POST" class="p-10">
                    @csrf
                    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-8">Catat Pekerjaan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Deskripsi Pekerjaan</label>
                            <input type="text" name="description" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Biaya (Rp)</label>
                            <input type="number" name="cost" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tanggal</label>
                            <input type="date" name="maintenance_date" value="{{ date('Y-m-d') }}" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none font-bold">
                        </div>
                    </div>
                    <div class="flex justify-end mt-10 gap-4">
                        <button type="button" @click="showMaintenanceModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                        <button type="submit" class="px-8 py-4 bg-gray-800 text-white font-black rounded-2xl shadow-xl">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Inspection Log -->
        <div x-show="showInspectionModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showInspectionModal = false"></div>
            <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                <form action="{{ route('inspections.store', $asset->id) }}" method="POST" class="p-10">
                    @csrf
                    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-8">Tambah Inspeksi</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kondisi Saat Ini</label>
                            <select name="condition" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none font-black text-sm">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Catatan Inspeksi</label>
                            <textarea name="notes" class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none min-h-[100px] font-bold text-sm"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-10 gap-4">
                        <button type="button" @click="showInspectionModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                        <button type="submit" class="px-8 py-4 bg-red-600 text-white font-black rounded-2xl shadow-xl shadow-red-500/20">Simpan Inspeksi</button>
                    </div>
                </form>
            </div>
        </div>

        @if($asset->category->name == 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
            <!-- Modal: Vehicle Checkout -->
            <div x-show="showVehicleModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showVehicleModal = false"></div>
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('vehicles.checkout', $asset->id) }}" method="POST" class="p-10">
                        @csrf
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-8 italic italic">Catat Perjalanan</h2>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Pengemudi</label>
                                <select name="employee_id" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                                    @foreach (App\Models\Employee::orderBy('name')->get() as $e) <option value="{{ $e->id }}">{{ $e->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tujuan</label>
                                <input type="text" name="destination" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">KM Awal</label>
                                    <input type="number" name="start_odometer" value="{{ $asset->vehicleLogs()->latest()->first()?->end_odometer ?? 0 }}" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Bensin (L)</label>
                                    <input type="number" step="0.1" name="fuel_liters" value="0" class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-10 gap-4">
                            <button type="button" @click="showVehicleModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                            <button type="submit" class="px-8 py-4 bg-gray-800 text-white font-black rounded-2xl shadow-xl">Mulai</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal: Vehicle Return -->
            <div x-show="showVehicleReturnModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showVehicleReturnModal = false"></div>
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('vehicleLogs.checkin', $asset->currentVehicleLog->id ?? 0) }}" method="POST" class="p-10">
                        @csrf
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-8">Selesai Perjalanan</h2>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">KM Akhir</label>
                                <input type="number" name="end_odometer" required class="w-full px-5 py-4 rounded-[20px] bg-gray-50 dark:bg-gray-900 border-none">
                            </div>
                        </div>
                        <div class="flex justify-end mt-10 gap-4">
                            <button type="button" @click="showVehicleReturnModal = false" class="px-6 py-3 font-bold text-gray-400">Batal</button>
                            <button type="submit" class="px-8 py-4 bg-red-600 text-white font-black rounded-2xl shadow-xl shadow-red-500/20">Data Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pageData', () => ({
                activeTab: 'detail',
                showAssignModal: false,
                showReturnModal: false,
                showMaintenanceModal: false,
                showInspectionModal: false,
                showVehicleModal: false,
                showVehicleReturnModal: false,
            }));
        });
    </script>

    <style>
        .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    </style>
</x-app-layout>
