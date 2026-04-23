<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">Peminjaman Barang</h2>
            <p class="text-sm text-gray-400 mt-1">Ajukan peminjaman aset dan pantau status permintaan Anda.</p>
        </div>
    </x-slot>

    <div x-data="{ borrowModalOpen: false, scanModalOpen: false, selectedAsset: null, tab: '{{ $tab }}' }">

        {{-- Flash --}}
        @if(session('success'))
        <div x-data="{ s: true }" x-show="s" x-init="setTimeout(() => s = false, 4000)" class="mb-6 flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-800">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
        @endif

        {{-- Status Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @php
                $stats = [
                    ['label' => 'Menunggu', 'key' => 'pending', 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Dipinjam', 'key' => 'approved', 'color' => 'emerald', 'icon' => 'M5 13l4 4L19 7'],
                    ['label' => 'Dikembalikan', 'key' => 'returned', 'color' => 'blue', 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
                    ['label' => 'Ditolak', 'key' => 'rejected', 'color' => 'red', 'icon' => 'M6 18L18 6M6 6l12 12'],
                ];
            @endphp
            @foreach($stats as $s)
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 bg-{{ $s['color'] }}-50 dark:bg-{{ $s['color'] }}-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-{{ $s['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $s['label'] }}</p>
                    <p class="text-2xl font-black text-{{ $s['color'] }}-700">{{ $myCounts[$s['key']] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Tab Navigation --}}
        <div class="flex gap-2 mb-6">
            <a href="{{ route('user.peminjaman.index', ['tab' => 'katalog']) }}"
                class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ $tab == 'katalog' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'bg-white dark:bg-gray-900 text-gray-600 border border-gray-100 dark:border-gray-800 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Katalog Aset
                </span>
            </a>
            <a href="{{ route('user.peminjaman.index', ['tab' => 'riwayat']) }}"
                class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ $tab == 'riwayat' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'bg-white dark:bg-gray-900 text-gray-600 border border-gray-100 dark:border-gray-800 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Saya
                </span>
            </a>
        </div>

        {{-- TAB: Katalog Aset --}}
        @if($tab == 'katalog')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            {{-- Filter --}}
            <div class="p-5 border-b border-gray-50 dark:border-gray-800">
                <form method="GET" action="{{ route('user.peminjaman.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="tab" value="katalog">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari nama atau kode aset..."
                            class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                        <button type="button" @click="scanModalOpen = true; initScanner()" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-lg transition-colors" title="Scan QR Code">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </button>
                    </div>
                    <select name="category" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-2.5 px-4 focus:border-primary-400 focus:ring-2 focus:ring-primary-100">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-md shadow-primary-500/20">Cari</button>
                </form>
            </div>

            {{-- Asset Grid --}}
            <div class="p-5">
                @if(!request('category') && !request('search'))
                    <div class="mb-6">
                        <h3 class="text-lg font-black text-gray-800 dark:text-white mb-1">Kategori Aset</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pilih kategori untuk melihat daftar aset yang tersedia.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($categories as $cat)
                        <a href="{{ route('user.peminjaman.index', ['tab' => 'katalog', 'category' => $cat->id]) }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-primary-300 dark:hover:border-primary-700 transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-black text-xl mr-4 group-hover:scale-110 transition-transform">
                                {{ substr($cat->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $cat->name }}</h4>
                                <p class="text-[10px] font-bold text-primary-600 mt-0.5">Lihat Aset &rarr;</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div>
                            @if(request('search'))
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Hasil Pencarian: "<span class="text-primary-600">{{ request('search') }}</span>"</h3>
                            @elseif(request('category'))
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Kategori: <span class="text-primary-600">{{ $categories->firstWhere('id', request('category'))?->name ?? 'Semua' }}</span></h3>
                            @endif
                        </div>
                        <a href="{{ route('user.peminjaman.index', ['tab' => 'katalog']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Kategori
                        </a>
                    </div>

                    @if($assets->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($assets as $asset)
                        <div class="group bg-gray-50 dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-300">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-black text-lg group-hover:scale-110 transition-transform">
                                    {{ substr($asset->name, 0, 1) }}
                                </div>
                                <span class="inline-flex px-2 py-1 text-[9px] font-black rounded-lg bg-emerald-100 text-emerald-700 uppercase tracking-widest">Tersedia</span>
                            </div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-1 line-clamp-2">{{ $asset->name }}</h4>
                            <p class="text-[10px] font-bold text-primary-600 mb-2">{{ $asset->asset_code_ypt }}</p>
                            <div class="space-y-1 mb-4">
                                <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $asset->category->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $asset->room->name ?? '-' }}
                                </p>
                            </div>
                            @if($asset->category && $asset->category->name == 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
                            <button type="button"
                                @click="vehicleModalOpen = true; selectedVehicle = { id: {{ $asset->id }}, name: '{{ addslashes($asset->name) }}', code: '{{ $asset->asset_code_ypt }}', odometer: {{ $asset->vehicleLogs()->latest()->first()?->end_odometer ?? 0 }} }"
                                class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-red-500/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Catat Perjalanan
                            </button>
                            @else
                            <button type="button"
                                @click="borrowModalOpen = true; selectedAsset = { id: {{ $asset->id }}, name: '{{ addslashes($asset->name) }}', code: '{{ $asset->asset_code_ypt }}', category: '{{ addslashes($asset->category->name ?? '-') }}', location: '{{ addslashes($asset->room->name ?? '-') }}' }"
                                class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-primary-500/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Ajukan Peminjaman
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $assets->links() }}</div>
                    @else
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-500">Tidak ada aset tersedia</p>
                        <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        @endif

        {{-- TAB: Riwayat --}}
        @if($tab == 'riwayat')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/70 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aset</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:table-cell">Tujuan</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right hidden md:table-cell">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($myRequests as $req)
                        @php
                            $sc = match($req->status) {
                                'pending'  => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'Menunggu'],
                                'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Dipinjam'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'Ditolak'],
                                'returned' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => 'Dikembalikan'],
                                default    => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => ucfirst($req->status)],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                @if($req->asset)
                                <p class="text-[10px] font-black text-primary-600">{{ $req->asset->asset_code_ypt }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $req->asset->name }}</p>
                                @else
                                <span class="text-xs text-gray-400">Aset tidak ditemukan</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 max-w-48">{{ $req->purpose }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $req->start_date?->format('d M Y') }}</p>
                                @if($req->end_date)
                                <p class="text-[10px] text-gray-400">s/d {{ $req->end_date->format('d M Y') }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $sc['bg'] }} {{ $sc['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                    {{ $sc['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right hidden md:table-cell">
                                @if($req->status === 'rejected' && $req->rejection_reason)
                                <p class="text-[10px] text-red-500 max-w-32 ml-auto">{{ $req->rejection_reason }}</p>
                                @elseif($req->status === 'approved' && $req->approved_by)
                                <p class="text-[10px] text-gray-400">Disetujui: {{ $req->approved_by }}</p>
                                @elseif($req->status === 'returned' && $req->returned_at)
                                <p class="text-[10px] text-gray-400">{{ $req->returned_at->format('d M Y') }}</p>
                                @else
                                <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-500">Belum ada riwayat peminjaman</p>
                                <p class="text-xs text-gray-400 mt-1">Ajukan peminjaman melalui tab Katalog Aset</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($myRequests->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 dark:border-gray-800">{{ $myRequests->links() }}</div>
            @endif
        </div>
        @endif

        {{-- Modal: Form Peminjaman --}}
        <div x-show="borrowModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-lg p-6" @click.outside="borrowModalOpen = false">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-800 dark:text-white">Ajukan Peminjaman</h3>
                        <p class="text-xs text-gray-400" x-text="selectedAsset?.name"></p>
                    </div>
                </div>

                <form method="POST" action="{{ route('user.peminjaman.store') }}">
                    @csrf
                    <input type="hidden" name="asset_id" :value="selectedAsset?.id">

                    {{-- Info Aset --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4">
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div><span class="text-gray-400">Kode:</span> <span class="font-bold text-primary-600" x-text="selectedAsset?.code"></span></div>
                            <div><span class="text-gray-400">Kategori:</span> <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="selectedAsset?.category"></span></div>
                            <div class="col-span-2"><span class="text-gray-400">Lokasi:</span> <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="selectedAsset?.location"></span></div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-5">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Tujuan Peminjaman <span class="text-red-500">*</span></label>
                            <textarea name="purpose" rows="2" required placeholder="Jelaskan tujuan peminjaman..."
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 p-3 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all resize-none bg-white dark:bg-gray-800"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" required min="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 p-3 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all bg-white dark:bg-gray-800">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Tanggal Kembali</label>
                                <input type="date" name="end_date"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 p-3 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all bg-white dark:bg-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" placeholder="Catatan tambahan (opsional)..."
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 p-3 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all resize-none bg-white dark:bg-gray-800"></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="borrowModalOpen = false"
                            class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-bold rounded-xl hover:bg-gray-200 transition-all">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-md shadow-primary-500/20">Ajukan Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Scan QR --}}
        <div x-show="scanModalOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-lg p-6" @click.outside="if(scanModalOpen) { scanModalOpen = false; closeScanner(); }">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-800 dark:text-white">Scan Label Aset</h3>
                            <p class="text-xs text-gray-400">Arahkan kamera ke QR Code aset</p>
                        </div>
                    </div>
                    <button type="button" @click="scanModalOpen = false; closeScanner()" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div id="reader" class="rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 mb-4"></div>
                
                <div id="scanResult" class="hidden w-full p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-center font-bold text-sm animate-pulse">
                    QR Code Terbaca! Mencari aset...
                </div>
            </div>
        </div>



                <div id="reader" class="rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 mb-4"></div>
                
                <div id="scanResult" class="hidden w-full p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-center font-bold text-sm animate-pulse">
                    QR Code Terbaca! Mencari aset...
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrcodeScanner = null;

        function initScanner() {
            if (html5QrcodeScanner) return;
            
            setTimeout(() => {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
                html5QrcodeScanner.render(onScanSuccess);
            }, 300);
        }

        function closeScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().then(() => {
                    html5QrcodeScanner = null;
                }).catch(err => console.error("Failed to clear scanner", err));
            }
            document.getElementById('scanResult').classList.add('hidden');
        }

        function onScanSuccess(decodedText, decodedResult) {
            let assetCode = decodedText.includes('/aset/') ? decodedText.split('/aset/').pop() : decodedText;
            document.getElementById('scanResult').classList.remove('hidden');
            document.getElementById('searchInput').value = assetCode;
            
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().then(() => {
                    html5QrcodeScanner = null;
                    document.getElementById('searchInput').closest('form').submit();
                });
            } else {
                document.getElementById('searchInput').closest('form').submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
