<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $title }} — {{ $yr }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-8">
        <!-- Welcoming Header with Gradient -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-2">
                    <a href="{{ route('assets.summary') }}" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7-7m7-7H3" /></svg>
                    </a>
                    <h1 class="text-3xl font-bold text-white uppercase">{{ $title }}</h1>
                </div>
                <p class="text-primary-100 text-lg opacity-90 ml-14">Detail daftar aset untuk tahun pengadaan {{ $yr }}.</p>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl"></div>
        </div>

        @if (session('success'))
            <div class="mx-2 px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-900/30 dark:text-emerald-400 animate-fadeIn flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-2 px-4 py-3 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 dark:bg-rose-900/20 dark:border-rose-900/30 dark:text-rose-400 animate-fadeIn flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Bulk Action Section -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden animate-slideUp" style="animation-delay: 100ms">
            <div class="p-6 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">{{ __('Aksi Massal (Bulk Action)') }}</h3>
            </div>
            <div class="p-6 grid grid-cols-1 xl:grid-cols-2 gap-8">
                {{-- Form Bulk Move --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-primary-600 dark:text-primary-400 font-bold mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        <span>Pindahkan / Alokasikan Aset</span>
                    </div>
                    <form id="bulk-move-form" method="POST" action="{{ route('assets.bulk-move') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @csrf
                        <input type="hidden" name="ids" id="bulk-move-ids">

                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Gedung</label>
                            <select name="building_id" class="select-ts w-full">
                                <option value="">— Tidak Diubah —</option>
                                @foreach (\App\Models\Building::orderBy('name')->get() as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Ruangan</label>
                            <select name="room_id" class="select-ts w-full">
                                <option value="">— Tidak Diubah —</option>
                                @foreach (\App\Models\Room::orderBy('name')->get() as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">PIC</label>
                            <select name="person_in_charge_id" class="select-ts w-full">
                                <option value="">— Tidak Diubah —</option>
                                @foreach (\App\Models\PersonInCharge::orderBy('name')->get() as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="md:col-span-3 flex items-center justify-center gap-2 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-lg shadow-emerald-500/20 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            Eksekusi Pemindahan
                        </button>
                    </form>
                </div>

                {{-- Form Bulk Status --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Pembaruan Status Aset</span>
                    </div>
                    <form id="bulk-status-form" method="POST" action="{{ route('assets.bulk-status') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        @csrf
                        <input type="hidden" name="ids" id="bulk-status-ids">

                        <div class="md:col-span-3 space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase ml-1">Status Baru</label>
                            <select name="status" class="select-ts w-full" required>
                                <option value="">— Pilih Status Baru —</option>
                                @foreach (['Aktif', 'Dipinjam', 'Maintenance', 'Rusak', 'Disposed'] as $st)
                                    <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="flex items-center justify-center gap-2 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-500/20 active:scale-95 self-end">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Asset Table Section -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 animate-slideUp" style="animation-delay: 200ms">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            <th class="px-6 py-4 w-12 text-center">
                                <input type="checkbox" id="select-all" class="rounded-lg border-gray-300 text-primary-600 focus:ring-primary-500 transition-all duration-200">
                            </th>
                            <th class="px-2 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Aset</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Tahun</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi / Ruangan</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">P.I.C</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach ($items as $i => $a)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors group">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="row-check rounded-lg border-gray-300 text-primary-600 focus:ring-primary-500 transition-all duration-200" value="{{ $a->id }}">
                                </td>
                                <td class="px-2 py-4 text-sm font-medium text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-lg text-gray-700 dark:text-gray-300 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20 transition-colors duration-200">
                                        {{ $a->asset_code_ypt ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $a->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-semibold text-gray-500">{{ $a->purchase_year ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-400 group-hover:text-primary-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ optional($a->room)->name ?? (optional($a->building)->name ?? '-') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[10px] font-black uppercase">
                                            {{ substr(optional($a->personInCharge)->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ optional($a->personInCharge)->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $badgeClasses = match ($a->status) {
                                            'Aktif' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'Dipinjam' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'Maintenance' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            'Rusak' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
                                            'Disposed' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                            default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-black uppercase rounded-lg {{ $badgeClasses }}">
                                        {{ $a->status ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 animate-fadeIn" style="animation-delay: 300ms">
            <a href="{{ route('assets.summary') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-all duration-200 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7-7m7-7H3" /></svg>
                Kembali ke Ringkasan
            </a>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Tom Select
                const tsConfig = {
                    plugins: ['remove_button'],
                    create: false,
                    dropdownParent: 'body',
                    onInitialize: function() {
                        if (document.documentElement.classList.contains('dark')) {
                            this.wrapper.classList.add('ts-dark');
                        }
                    }
                };
                
                document.querySelectorAll('.select-ts').forEach(el => {
                    new TomSelect(el, tsConfig);
                });

                // Selection logic
                const selectAll = document.getElementById('select-all');
                const checks = Array.from(document.querySelectorAll('.row-check'));
                const moveForm = document.getElementById('bulk-move-form');
                const statusForm = document.getElementById('bulk-status-form');
                const moveIds = document.getElementById('bulk-move-ids');
                const statusIds = document.getElementById('bulk-status-ids');

                if (selectAll) {
                    selectAll.addEventListener('change', () => {
                        checks.forEach(c => {
                            c.checked = selectAll.checked;
                            toggleRowHighlight(c);
                        });
                    });
                }

                checks.forEach(c => {
                    c.addEventListener('change', () => {
                        toggleRowHighlight(c);
                        updateSelectAllState();
                    });
                });

                function toggleRowHighlight(checkbox) {
                    const row = checkbox.closest('tr');
                    if (checkbox.checked) {
                        row.classList.add('bg-primary-50/50', 'dark:bg-primary-900/10');
                    } else {
                        row.classList.remove('bg-primary-50/50', 'dark:bg-primary-900/10');
                    }
                }

                function updateSelectAllState() {
                    const allChecked = checks.every(c => c.checked);
                    const someChecked = checks.some(c => c.checked);
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = someChecked && !allChecked;
                }

                function collectIds() {
                    return checks.filter(c => c.checked).map(c => c.value).join(',');
                }

                if (moveForm) {
                    moveForm.addEventListener('submit', (e) => {
                        const ids = collectIds();
                        if (!ids) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: 'Pilih minimal satu aset terlebih dahulu.',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl px-6 py-2.5 bg-primary-600 text-white font-bold'
                                }
                            });
                            return;
                        }
                        moveIds.value = ids;
                    });
                }

                if (statusForm) {
                    statusForm.addEventListener('submit', (e) => {
                        const ids = collectIds();
                        if (!ids) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: 'Pilih minimal satu aset terlebih dahulu.',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl px-6 py-2.5 bg-primary-600 text-white font-bold'
                                }
                            });
                            return;
                        }
                        statusIds.value = ids;
                    });
                }
            });
        </script>
        
        <style>
            /* Tom Select Overrides */
            .ts-wrapper .ts-control {
                border-radius: 0.75rem !important;
                border-color: #e5e7eb !important;
                background-color: #ffffff !important;
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
                font-size: 0.875rem !important;
                transition: all 0.2s !important;
            }
            .dark .ts-wrapper .ts-control {
                border-color: #374151 !important;
                background-color: #1f2937 !important;
                color: #f3f4f6 !important;
            }
            .ts-wrapper.focus .ts-control {
                border-color: #e11d48 !important;
                ring: 2px rgba(225, 29, 72, 0.2) !important;
            }
            .ts-dropdown {
                border-radius: 1rem !important;
                border: 1px solid #f3f4f6 !important;
                background-color: #ffffff !important;
                box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1) !important;
                z-index: 9999 !important;
            }
            .dark .ts-dropdown {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
            }
            .ts-dropdown .active {
                background-color: #fff1f2 !important;
                color: #be123c !important;
            }
            .dark .ts-dropdown .active {
                background-color: rgba(225, 29, 72, 0.2) !important;
                color: #fda4af !important;
            }
        </style>
    @endpush
</x-app-layout>
