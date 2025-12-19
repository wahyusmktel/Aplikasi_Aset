<x-app-layout x-data="pageData()">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('Manajemen Aset') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">Kelola dan pantau seluruh inventaris aset institusi Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('assets.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-2xl transition-all shadow-xl shadow-red-500/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                    Tambah Aset
                </a>
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
                            <a href="{{ route('assets.batchCreate') }}" class="flex items-center px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-red-600 rounded-xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" /></svg>
                                Tambah Massal
                            </a>
                            <button @click="showImportBatchModal = true; open = false" class="w-full flex items-center px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-red-600 rounded-xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                Impor Massal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-24">
            
            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $stats = [
                        ['label' => 'Total Aset', 'icon' => 'cube', 'color' => 'red', 'count' => \App\Models\Asset::whereNull('disposal_date')->count()],
                        ['label' => 'Tersedia', 'icon' => 'check-circle', 'color' => 'emerald', 'count' => \App\Models\Asset::whereNull('disposal_date')->where('current_status', 'Tersedia')->count()],
                        ['label' => 'Dipinjam', 'icon' => 'user', 'color' => 'blue', 'count' => \App\Models\Asset::whereNull('disposal_date')->whereIn('current_status', ['Dipinjam', 'Digunakan'])->count()],
                        ['label' => 'Rusak', 'icon' => 'exclamation-triangle', 'color' => 'amber', 'count' => \App\Models\Asset::whereNull('disposal_date')->where('current_status', 'Rusak')->count()],
                    ];
                @endphp

                @foreach($stats as $stat)
                    <div class="bg-white dark:bg-gray-950 p-6 rounded-[32px] border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-5 group hover:shadow-xl hover:translate-x-1 transition-all">
                        <div class="w-14 h-14 bg-{{ $stat['color'] }}-600/10 rounded-2xl flex items-center justify-center border border-{{ $stat['color'] }}-500/20 group-hover:scale-110 transition-transform">
                            @if($stat['icon'] == 'cube')
                                <svg class="w-7 h-7 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            @elseif($stat['icon'] == 'check-circle')
                                <svg class="w-7 h-7 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($stat['icon'] == 'user')
                                <svg class="w-7 h-7 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            @else
                                <svg class="w-7 h-7 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                            <h3 class="text-2xl font-black text-gray-800 dark:text-white">{{ $stat['count'] }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-gray-950 p-6 rounded-[32px] border border-gray-100 dark:border-gray-800 shadow-sm" x-data="{ expanded: false }">
                <div class="flex flex-col md:flex-row gap-6 items-end">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                        {{-- Search --}}
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Cari Aset</label>
                            <div class="relative group">
                                <input type="text" x-model="searchQuery" @keyup.enter="applyFilters"
                                    placeholder="Nama atau Kode Aset..."
                                    class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 transition-all shadow-sm">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori (Custom Tailwind Multi-select) --}}
                        <div class="md:col-span-1" x-data="{ open: false, search: '' }">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Pilih Kategori</label>
                            <div class="relative" @click.away="open = false">
                                <div @click="open = !open"
                                    class="min-h-[56px] w-full px-4 py-2 rounded-2xl border border-gray-100 dark:border-gray-800 dark:bg-gray-900 flex flex-wrap gap-2 items-center cursor-pointer transition-all focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-500/10 shadow-sm">
                                    <template x-if="selectedCategories.length === 0">
                                        <span class="text-sm text-gray-400 font-bold px-2">Pilih Kategori...</span>
                                    </template>
                                    <template x-for="id in selectedCategories" :key="id">
                                        <div class="flex items-center gap-1.5 px-3 py-1 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-500/20 group">
                                            <span x-text="allCategories.find(c => c.id == id)?.name"></span>
                                            <button @click.stop="selectedCategories = selectedCategories.filter(i => i != id)" class="hover:text-red-200 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <div class="ml-auto">
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>

                                {{-- Dropdown --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute z-[100] mt-3 w-full animate-slideUp bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                                    <div class="p-3 border-b border-gray-50 dark:border-gray-900">
                                        <input type="text" x-model="search" placeholder="Cari kategori..." 
                                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-0 font-bold text-gray-600 dark:text-gray-300">
                                    </div>
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar p-2">
                                        <template x-for="cat in allCategories.filter(c => c.name.toLowerCase().includes(search.toLowerCase()))" :key="cat.id">
                                            <div @click="selectedCategories.includes(cat.id.toString()) ? selectedCategories = selectedCategories.filter(i => i != cat.id.toString()) : selectedCategories.push(cat.id.toString())"
                                                class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all"
                                                :class="selectedCategories.includes(cat.id.toString()) ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'hover:bg-gray-50 dark:hover:bg-gray-900 text-gray-700 dark:text-gray-400 font-bold'">
                                                <span class="text-sm font-black uppercase tracking-tight" x-text="cat.name"></span>
                                                <div x-show="selectedCategories.includes(cat.id.toString())" class="w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tahun (Custom Selection) --}}
                        <div class="md:col-span-1" x-data="{ open: false }">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Tahun Pengadaan</label>
                            <div class="relative" @click.away="open = false">
                                <div @click="open = !open"
                                    class="h-[56px] w-full px-6 py-4 rounded-2xl border border-gray-100 dark:border-gray-800 dark:bg-gray-900 flex items-center justify-between cursor-pointer group shadow-sm">
                                    <span class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest" x-text="selectedYear === 'all' ? 'Semua Tahun' : selectedYear"></span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 transition-all" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                                <div x-show="open" x-transition class="absolute z-[100] mt-3 w-full bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl p-2">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        <div @click="selectedYear = 'all'; open = false" 
                                            class="px-4 py-3 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 text-sm font-black uppercase tracking-tight"
                                            :class="selectedYear === 'all' ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-gray-600 dark:text-gray-400'">Semua Tahun</div>
                                        @foreach ($years as $year)
                                            <div @click="selectedYear = '{{ $year }}'; open = false" 
                                                class="px-4 py-3 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 text-sm font-black uppercase tracking-tight"
                                                :class="selectedYear === '{{ $year }}' ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-gray-600 dark:text-gray-400'">{{ $year }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <button @click="expanded = !expanded" 
                            class="px-5 py-3.5 bg-gray-50 dark:bg-gray-900 text-gray-500 font-bold rounded-2xl border border-gray-100 dark:border-gray-800 hover:bg-gray-100 transition-all flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                            Lanjutan
                        </button>
                        <button @click="applyFilters"
                            class="px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl transition-all shadow-xl shadow-red-500/20 hover:-translate-y-1">
                            Terapkan
                        </button>
                    </div>
                </div>

                {{-- Expanded Filters --}}
                <div x-show="expanded" x-collapse>
                    <div class="pt-8 mt-6 border-t border-gray-50 dark:border-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            {{-- Kecualikan Kategori (Custom Tailwind Multi-select) --}}
                            <div class="md:col-span-2" x-data="{ open: false, search: '' }">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Kecualikan Kategori</label>
                                <div class="relative" @click.away="open = false">
                                    <div @click="open = !open"
                                        class="min-h-[56px] w-full px-4 py-2 rounded-2xl border border-gray-100 dark:border-gray-800 dark:bg-gray-900 flex flex-wrap gap-2 items-center cursor-pointer transition-all focus-within:border-red-500 shadow-sm">
                                        <template x-if="selectedExcludeCategories.length === 0">
                                            <span class="text-sm text-gray-400 font-bold px-2">Pilih yang dikecualikan...</span>
                                        </template>
                                        <template x-for="id in selectedExcludeCategories" :key="id">
                                            <div class="flex items-center gap-1.5 px-3 py-1 bg-gray-800 text-white rounded-xl text-[10px] font-black uppercase tracking-widest group">
                                                <span x-text="allCategories.find(c => c.id == id)?.name"></span>
                                                <button @click.stop="selectedExcludeCategories = selectedExcludeCategories.filter(i => i != id)" class="hover:text-red-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <div x-show="open" x-transition class="absolute z-[100] mt-3 w-full bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden p-2">
                                        <div class="p-2">
                                            <input type="text" x-model="search" placeholder="Cari..." 
                                                class="w-full px-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-0 font-bold">
                                        </div>
                                        <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="cat in allCategories.filter(c => c.name.toLowerCase().includes(search.toLowerCase()))" :key="cat.id">
                                                <div @click="selectedExcludeCategories.includes(cat.id.toString()) ? selectedExcludeCategories = selectedExcludeCategories.filter(i => i != cat.id.toString()) : selectedExcludeCategories.push(cat.id.toString())"
                                                    class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-900"
                                                    :class="selectedExcludeCategories.includes(cat.id.toString()) ? 'text-red-500 font-black' : 'text-gray-600 dark:text-gray-400 font-bold'">
                                                    <span class="text-[11px] uppercase tracking-widest" x-text="cat.name"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2" x-data="{ open: false }">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Jumlah Per Halaman</label>
                                <div class="relative" @click.away="open = false">
                                    <div @click="open = !open"
                                        class="h-[56px] w-full px-6 py-4 rounded-2xl border border-gray-100 dark:border-gray-800 dark:bg-gray-900 flex items-center justify-between cursor-pointer group shadow-sm">
                                        <span class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase underline decoration-red-500/30 underline-offset-4" x-text="perPage + ' ITEM PER HALAMAN'"></span>
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 transition-all" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                    <div x-show="open" x-transition class="absolute z-[100] mt-3 w-full bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl p-2">
                                        @foreach ($allowedPerPages as $option)
                                            <div @click="perPage = '{{ $option }}'; open = false" 
                                                class="px-4 py-3 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 text-[10px] font-black uppercase tracking-widest transition-all"
                                                :class="perPage == '{{ $option }}' ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-gray-500'">{{ $option }} ITEM PER HALAMAN</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Wrapper --}}
            <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn">
                {{-- Bulk Action Bar --}}
                <div x-show="selectedIds.length > 0" x-transition 
                    class="bg-red-600 p-4 px-10 flex items-center justify-between text-white">
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center border border-white/30 text-xs font-black" x-text="selectedIds.length"></div>
                        </div>
                        <span class="text-sm font-black uppercase tracking-widest">Item Terpilih</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="openBulkEditModal" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-black transition-all">
                            Bulk Edit
                        </button>
                        <button @click="printSelected" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-black transition-all">
                            Cetak Label
                        </button>
                    </div>
                </div>

                {{-- Export Actions --}}
                <div class="p-4 px-10 border-b border-gray-50 dark:border-gray-900 flex justify-end gap-3 bg-gray-50/30 dark:bg-gray-900/10">
                    <a :href="generateExportUrl('{{ route('assets.exportActiveExcel') }}')"
                        class="px-4 py-2 bg-emerald-600/10 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-black transition-all flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export Excel
                    </a>
                    <a :href="generateExportUrl('{{ route('assets.downloadActivePDF') }}')" target="_blank"
                        class="px-4 py-2 bg-blue-600/10 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-black transition-all flex items-center text-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Laporan PDF
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="p-6 px-10 w-20">
                                    <input type="checkbox" @click="toggleAll($event)"
                                        class="rounded-lg bg-gray-100 dark:bg-gray-800 border-none text-red-600 focus:ring-red-600 p-2.5 transition-all">
                                </th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kode Aset & Nama</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hidden md:table-cell">Detail & Lokasi</th>
                                <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status</th>
                                <th class="py-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @forelse ($assets as $asset)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors group">
                                    <td class="p-6 px-10">
                                        <input type="checkbox" :value="{{ $asset->id }}" x-model="selectedIds"
                                            class="rounded-lg bg-gray-100 dark:bg-gray-800 border-none text-red-600 focus:ring-red-600 p-2.5 transition-all">
                                    </td>
                                    <td class="py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">{{ $asset->asset_code_ypt }}</span>
                                            <span class="text-base font-bold text-gray-800 dark:text-white group-hover:text-red-600 transition-colors">{{ $asset->name }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 mt-1">{{ $asset->category->name ?? 'Tanpa Kategori' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-6 hidden md:table-cell">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center text-xs text-gray-500 font-bold">
                                                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                                {{ $asset->building->name ?? '-' }}
                                            </div>
                                            <div class="flex items-center text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                                {{ $asset->room->name ?? '-' }}
                                            </div>
                                            <div class="flex items-center text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">
                                                {{ $asset->purchase_year }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 text-center">
                                        @php
                                            $statusColors = [
                                                'Tersedia' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                'Dipinjam' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                                'Digunakan' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                                'Rusak' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                            ];
                                            $cls = $statusColors[$asset->current_status] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $cls }}">
                                            {{ $asset->current_status }}
                                        </span>
                                    </td>
                                    <td class="py-6 px-10 text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-2 group-hover:translate-x-0 transition-transform">
                                            <a href="{{ route('assets.show', $asset->id) }}"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-red-600 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            <a href="{{ route('assets.edit', $asset->id) }}"
                                                class="p-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-red-600 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                            <button onclick="confirmDelete({{ $asset->id }})"
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
                                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                            </div>
                                            <p class="text-xl font-bold text-gray-400">Tidak ada data aset ditemukan.</p>
                                            <p class="text-xs text-gray-300 mt-2 uppercase tracking-widest font-black">Coba sesuaikan filter atau tambahkan aset baru</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-10 border-t border-gray-50 dark:border-gray-900 bg-gray-50/20 dark:bg-gray-900/10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-sm font-bold text-gray-400">
                        Menampilkan <span class="text-gray-800 dark:text-white">{{ $assets->firstItem() ?? 0 }}</span> - <span class="text-gray-800 dark:text-white">{{ $assets->lastItem() ?? 0 }}</span> dari <span class="text-gray-800 dark:text-white">{{ $assets->total() }}</span> Aset
                    </div>
                    <div>
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Impor Massal --}}
        <div x-show="showImportBatchModal" x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showImportBatchModal = false"></div>
                
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('assets.importBatch') }}" method="POST" enctype="multipart/form-data" class="p-10">
                        @csrf
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Impor Massal</h2>
                            <button type="button" @click="showImportBatchModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div class="p-8 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl flex flex-col items-center group hover:border-red-500/50 transition-all cursor-pointer relative overflow-hidden">
                                <svg class="w-12 h-12 text-gray-300 mb-4 group-hover:scale-110 group-hover:text-red-600 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest text-center">Klik untuk memilih file excel</p>
                                <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <p class="text-[10px] text-gray-500 italic leading-relaxed">
                                    <span class="font-black text-red-600">Catatan:</span> Pastikan data master (gedung, ruangan, dll) sudah terdaftar di sistem agar impor berjalan lancar.
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end mt-10 space-x-4">
                            <button type="button" @click="showImportBatchModal = false" class="px-6 py-3 font-bold text-gray-400 hover:text-gray-600">Batal</button>
                            <button type="submit" class="px-10 py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1">Mulai Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Bulk Edit --}}
        <div x-show="showBulkEditModal" x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBulkEditModal = false"></div>
                
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-2xl overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form method="POST" action="{{ route('assets.bulkUpdateFields') }}" class="p-10">
                        @csrf
                        <input type="hidden" name="ids" :value="selectedIds.join(',')">
                        
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Bulk Edit Aset</h2>
                                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold"><span x-text="selectedIds.length"></span> Item Terpilih</p>
                            </div>
                            <button type="button" @click="showBulkEditModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[50vh] overflow-y-auto px-1 custom-scrollbar">
                            {{-- Field Edit Items (Cards) --}}
                            @php
                                $editFields = [
                                    ['name' => 'apply_name', 'model' => 'apply.name', 'label' => 'Nama Barang', 'input_name' => 'name', 'type' => 'text', 'placeholder' => 'Nama barang baru'],
                                    ['name' => 'apply_funding', 'model' => 'apply.funding', 'label' => 'Jenis Pendanaan', 'input_name' => 'funding_source_id', 'type' => 'select', 'options' => $fundingSources],
                                    ['name' => 'apply_room', 'model' => 'apply.room', 'label' => 'Lokasi Ruangan', 'input_name' => 'room_id', 'type' => 'select', 'options' => $rooms],
                                    ['name' => 'apply_year', 'model' => 'apply.year', 'label' => 'Tahun Pengadaan', 'input_name' => 'purchase_year', 'type' => 'number', 'placeholder' => 'YYYY'],
                                    ['name' => 'apply_pic', 'model' => 'apply.pic', 'label' => 'Penanggung Jawab', 'input_name' => 'person_in_charge_id', 'type' => 'select', 'options' => $personsInCharge, 'full' => true],
                                ];
                            @endphp

                            @foreach($editFields as $field)
                                <div class="bg-gray-50/50 dark:bg-gray-900/30 p-5 rounded-3xl border border-gray-100 dark:border-gray-800 @if($field['full'] ?? false) md:col-span-2 @endif">
                                    <label class="flex items-center gap-3 cursor-pointer mb-4">
                                        <input type="checkbox" name="{{ $field['name'] }}" x-model="{{ $field['model'] }}" 
                                            class="rounded-lg bg-white dark:bg-gray-800 border-none text-red-600 focus:ring-red-600 p-2.5 transition-all">
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">{{ $field['label'] }}</span>
                                    </label>
                                    
                                    @if($field['type'] == 'select')
                                        <select name="{{ $field['input_name'] }}" x-bind:disabled="!{{ $field['model'] }}"
                                            class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 disabled:opacity-30 disabled:grayscale transition-all">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($field['options'] as $opt)
                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $field['type'] }}" name="{{ $field['input_name'] }}" x-bind:disabled="!{{ $field['model'] }}"
                                            placeholder="{{ $field['placeholder'] }}"
                                            class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 disabled:opacity-30 transition-all">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end mt-10 space-x-4">
                            <button type="button" @click="showBulkEditModal = false" class="px-6 py-3 font-bold text-gray-400 hover:text-gray-600">Batal</button>
                            <button type="submit" 
                                x-bind:disabled="Object.values(apply).every(v => v === false)"
                                class="px-10 py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed">
                                Simpan Perubahan Massal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pageData', () => ({
                    selectedIds: [],
                    showImportBatchModal: false,
                    showBulkEditModal: false,
                    searchQuery: '{{ request('search') }}',
                    expanded: {{ (request('exclude_category_ids') || $perPage != 12) ? 'true' : 'false' }},
                    apply: {
                        name: false,
                        funding: false,
                        room: false,
                        year: false,
                        pic: false
                    },

                    allCategories: @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),
                    selectedCategories: @json(request()->input('category_ids', [])).map(String),
                    selectedExcludeCategories: @json(request()->input('exclude_category_ids', [])).map(String),
                    selectedYear: '{{ request('purchase_year', 'all') }}',
                    perPage: '{{ $perPage }}',

                    init() {
                        // No logic needed for custom Tailwind selects
                    },

                    toggleAll(event) {
                        let checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                        this.selectedIds = event.target.checked ? Array.from(checkboxes).map(cb => parseInt(cb.value)) : [];
                    },

                    generateExportUrl(baseUrl) {
                        const url = new URL(baseUrl);
                        (this.selectedCategories || []).forEach(id => url.searchParams.append('category_ids[]', id));
                        (this.selectedExcludeCategories || []).forEach(id => url.searchParams.append('exclude_category_ids[]', id));
                        url.searchParams.set('purchase_year', this.selectedYear);
                        if(this.searchQuery) url.searchParams.set('search', this.searchQuery);
                        return url.toString();
                    },

                    openBulkEditModal() {
                        if (this.selectedIds.length === 0) return;
                        this.showBulkEditModal = true;
                    },

                    printSelected() {
                        const ids = this.selectedIds.join(',');
                        if (ids) {
                            const url = `{{ route('assets.printLabels') }}?ids=${ids}`;
                            window.open(url, '_blank');
                        }
                    },

                    applyFilters() {
                        const url = new URL('{{ route('assets.index') }}');
                        this.selectedCategories.forEach(id => {
                            if(id) url.searchParams.append('category_ids[]', id);
                        });
                        this.selectedExcludeCategories.forEach(id => {
                            if(id) url.searchParams.append('exclude_category_ids[]', id);
                        });
                        url.searchParams.set('purchase_year', this.selectedYear);
                        url.searchParams.set('per_page', this.perPage);
                        if (this.searchQuery) url.searchParams.set('search', this.searchQuery);
                        window.location.href = url.toString();
                    }
                }));
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: '<span class="text-xl font-black uppercase tracking-tight">Hapus Aset?</span>',
                    html: '<p class="text-sm text-gray-400">Tindakan ini tidak dapat dibatalkan dan data akan hilang permanen.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus Sekarang',
                    cancelButtonText: 'Batal',
                    padding: '2rem',
                    background: document.documentElement.classList.contains('dark') ? '#0a0a0a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    borderRadius: '2rem'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.action = `/assets/${id}`;
                        form.method = 'POST';
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                })
            }
        </script>

        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #e5e7eb;
                border-radius: 10px;
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #374151;
            }
            .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            @keyframes slideUp { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        </style>
    @endpush
</x-app-layout>
