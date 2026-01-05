<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ringkasan Aset') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-8">
        <!-- Welcoming Header with Gradient -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold text-white mb-2">{{ __('Ringkasan & Grup Aset') }} 👋</h1>
                <p class="text-primary-100 text-lg opacity-90">Kelola dan pantau pengelompokan aset Anda dengan lebih mudah.</p>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Filter & Preset Section -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 animate-slideUp" style="animation-delay: 100ms">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Preset Section -->
                <div class="lg:w-1/3 space-y-4 pr-0 lg:pr-6 lg:border-r border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Preset Filter') }}</h3>
                    
                    <div class="space-y-3">
                        {{-- Load Preset --}}
                        <form method="GET" action="{{ route('assets.summary') }}" class="flex items-center gap-2">
                            <div class="relative flex-grow">
                                <select name="preset_id" class="select2 w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                                    <option value="">— Pilih Preset —</option>
                                    @foreach ($presets ?? [] as $p)
                                        <option value="{{ $p->id }}" @selected(request('preset_id') == $p->id)>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="p-2.5 rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition-all duration-200 shadow-md shadow-primary-500/20 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </button>
                        </form>

                        {{-- Save Preset --}}
                        <form method="POST" action="{{ route('assets.summary.preset.save') }}" class="space-y-2">
                            @csrf
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            @foreach ((array) request('status', []) as $st)
                                <input type="hidden" name="status[]" value="{{ $st }}">
                            @endforeach

                            <div class="flex gap-2">
                                <input type="text" name="name" 
                                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                                    placeholder="Nama preset baru..." required>
                                <button class="p-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all duration-200 shadow-md shadow-emerald-500/20 active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                </button>
                            </div>
                        </form>

                        {{-- Delete Preset --}}
                        <form id="delPresetForm" method="POST" action="" onsubmit="return confirm('Hapus preset ini?')">
                            @csrf
                            @method('DELETE')
                            <button id="delPresetBtn" disabled
                                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Hapus Preset Aktif
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Custom Filter Section -->
                <div class="lg:w-2/3 space-y-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Pencarian & Filter Kustom') }}</h3>
                    
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative md:col-span-2">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                                placeholder="Cari nama, kode, atau deskripsi aset..." />
                        </div>

                        <select name="category_id" class="select2 w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm" data-placeholder="Semua Kategori">
                            <option value="">Semua Kategori</option>
                            @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <select name="year" class="select2 w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm" data-placeholder="Semua Tahun">
                            <option value="">Semua Tahun</option>
                            @foreach ($years ?? [] as $y)
                                <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                            @endforeach
                        </select>

                        <div class="md:col-span-2">
                            <select name="status[]" class="select2 w-full rounded-xl" multiple data-placeholder="Filter Status (Bisa Pilih Banyak)">
                                @foreach ($allStatuses ?? [] as $st)
                                    <option value="{{ $st }}" @selected(collect(request('status', []))->contains($st))>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2 flex gap-3">
                            <button class="flex-grow flex items-center justify-center gap-2 px-6 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/25 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                                Terapkan Filter
                            </button>
                            <a href="{{ route('assets.summary') }}" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-all duration-200 active:scale-95">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export & Content Section -->
        <div class="space-y-4 animate-fadeIn" style="animation-delay: 200ms">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-2">
                <div class="flex items-center gap-3">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Daftar Grup Aset</h3>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('assets.summary.export-excel', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-sm font-bold rounded-xl hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Excel
                    </a>
                    <a href="{{ route('assets.summary.export-pdf', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 text-sm font-bold rounded-xl hover:bg-rose-200 dark:hover:bg-rose-900/50 transition-all duration-200 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        PDF
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rentang Kode</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai Total</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">QTY</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @php
                                $fmtRange = function ($min, $max) {
                                    if (!$min && !$max) return '-';
                                    if (!$max || $min === $max) return $min;
                                    
                                    $len = min(strlen($min), strlen($max));
                                    $i = 0;
                                    for (; $i < $len && $min[$i] === $max[$i]; $i++);
                                    
                                    return substr($min, 0, $i) . '[' . substr($min, $i) . ' – ' . substr($max, $i) . ']';
                                };
                                $idr = fn($n) => number_format((float) $n, 0, ',', '.');
                            @endphp

                            @forelse($groups as $i => $g)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-400">{{ $groups->firstItem() + $i }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-lg text-gray-700 dark:text-gray-300">
                                            {{ $fmtRange($g->min_code, $g->max_code) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $g->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-500">{{ $g->yr }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeClasses = match ($g->status_label) {
                                                'Aktif' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                'Dipinjam' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                'Maintenance' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                'Rusak' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
                                                'Disposed' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                                default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2.5 py-1 text-xs font-black uppercase rounded-lg {{ $badgeClasses }}">
                                            {{ $g->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-black text-gray-800 dark:text-white tabular-nums">Rp {{ $idr($g->total_cost) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-3 py-1 rounded-full">{{ $g->qty }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('assets.summary.show', $g->group_key) }}"
                                            class="inline-flex items-center gap-2 text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors uppercase tracking-widest">
                                            Detail
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                            <span class="text-gray-500 font-medium italic">Tidak ada data aset yang ditemukan.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($groups->hasPages())
                    <div class="px-6 py-4 border-t border-gray-50 dark:border-gray-800">
                        {{ $groups->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration for Tom Select
            const tsConfig = {
                plugins: ['remove_button'],
                create: false,
                maxOptions: null,
                allowEmptyOption: true,
                dropdownParent: 'body', // This ensures dropdown is not cut off by parent overflow
                onInitialize: function() {
                    const self = this;
                    // Apply dark mode adjustments if needed
                    if (document.documentElement.classList.contains('dark')) {
                        self.wrapper.classList.add('ts-dark');
                    }
                }
            };

            // Initialize Tom Select for all select elements with 'select2' class (we'll keep the class for now or change it)
            document.querySelectorAll('.select2').forEach(el => {
                new TomSelect(el, tsConfig);
            });

            // Preset delete button logic
            const selectEl = document.querySelector('select[name="preset_id"]');
            const form = document.getElementById('delPresetForm');
            const btn = document.getElementById('delPresetBtn');

            const updatePresetBtn = () => {
                const val = selectEl.value;
                if (val) {
                    form.action = "{{ route('assets.summary.preset.delete', ['preset' => '___ID___']) }}".replace('___ID___', val);
                    btn.disabled = false;
                    btn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'dark:bg-gray-800', 'dark:text-gray-500');
                    btn.classList.add('bg-rose-50', 'text-rose-600', 'hover:bg-rose-100', 'dark:bg-rose-900/20', 'dark:text-rose-400');
                } else {
                    form.action = '';
                    btn.disabled = true;
                    btn.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'dark:bg-gray-800', 'dark:text-gray-500');
                    btn.classList.remove('bg-rose-50', 'text-rose-600', 'hover:bg-rose-100', 'dark:bg-rose-900/20', 'dark:text-rose-400');
                }
            };

            if (selectEl) {
                // For Tom Select, we listen to 'change' on the original element
                selectEl.addEventListener('change', updatePresetBtn);
                // Initial check
                setTimeout(updatePresetBtn, 100); 
            }
        });
    </script>
    <style>
        /* Tom Select Overrides - Fixed Styling */
        .ts-wrapper.single .ts-control, 
        .ts-wrapper.multi .ts-control {
            border-radius: 0.75rem !important; /* rounded-xl */
            border-width: 1px !important;
            border-color: #e5e7eb !important; /* border-gray-200 */
            background-color: #ffffff !important; /* bg-white */
            font-size: 0.875rem !important; /* text-sm */
            padding-top: 0.625rem !important; /* py-2.5 */
            padding-bottom: 0.625rem !important;
            transition-property: all !important;
            transition-duration: 200ms !important;
            box-shadow: none !important;
        }

        .dark .ts-wrapper.single .ts-control, 
        .dark .ts-wrapper.multi .ts-control {
            border-color: #374151 !important; /* dark:border-gray-700 */
            background-color: #1f2937 !important; /* dark:bg-gray-800 */
            color: #f3f4f6 !important; /* text-gray-100 */
        }

        .ts-wrapper.focus .ts-control {
            border-color: #e11d48 !important; /* border-primary-600 */
            outline: 2px solid transparent !important;
            ring-width: 2px !important;
            ring-color: rgba(225, 29, 72, 0.2) !important;
        }

        .ts-dropdown {
            border-radius: 0.75rem !important; /* rounded-xl */
            border-color: #f3f4f6 !important; /* border-gray-100 */
            background-color: #ffffff !important; /* bg-white */
            shadow-xl !important;
            margin-top: 0.5rem !important;
            z-index: 9999 !important; /* Extremely high z-index */
        }

        .dark .ts-dropdown {
            background-color: #1f2937 !important; /* dark:bg-gray-800 */
            border-color: #374151 !important; /* dark:border-gray-700 */
        }

        .ts-dropdown .active {
            background-color: #fff1f2 !important; /* bg-primary-50 */
            color: #be123c !important; /* text-primary-700 */
        }

        .dark .ts-dropdown .active {
            background-color: rgba(225, 29, 72, 0.3) !important; /* dark:bg-primary-900/30 */
            color: #fda4af !important; /* dark:text-primary-400 */
        }

        .ts-dropdown .option {
            padding: 0.5rem 1rem !important;
        }

        .ts-control .item {
            background-color: #ffe4e6 !important; /* bg-primary-100 */
            color: #be123c !important; /* text-primary-700 */
            border-radius: 0.5rem !important;
            padding: 0.125rem 0.5rem !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
        }

        .dark .ts-control .item {
            background-color: rgba(225, 29, 72, 0.4) !important;
            color: #fda4af !important;
        }
    </style>
    @endpush

</x-app-layout>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.js-select2').select2({
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true
        });
    });
</script> --}}
