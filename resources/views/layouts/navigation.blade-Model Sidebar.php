{{-- Sidebar Navigation (Fixed) + Mobile Drawer --}}
<nav x-data="{
    open: false,
    invOpen: {{ request()->routeIs([
        'assignedAssets.index',
        'inventory.history',
        'maintenance.history',
        'inspection.history',
        'vehicleLogs.index',
        'books.index',
        'asset-mapping.index',
    ])
        ? 'true'
        : 'false' }},
    refOpen: {{ request()->routeIs([
        'institutions.index',
        'employees.index',
        'buildings.index',
        'rooms.index',
        'categories.index',
        'faculties.index',
        'departments.index',
        'persons-in-charge.index',
        'asset-functions.index',
        'funding-sources.index',
    ])
        ? 'true'
        : 'false' }},
}" class="relative z-40">
    {{-- Top bar kecil hanya untuk tombol hamburger (mobile) + user menu (kanan) --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 md:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button @click="open = true"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
                </a>
            </div>

            {{-- User Dropdown (mobile topbar) --}}
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900">
                        <div>{{ Auth::user()->name }}</div>
                        <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.207l3.71-3.976a.75.75 0 111.08 1.04l-4.243 4.544a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    {{-- Overlay (mobile) --}}
    <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 bg-black/40 md:hidden" aria-hidden="true">
    </div>

    {{-- SIDEBAR --}}
    <aside
        class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700
               transform transition-transform duration-200 ease-out md:translate-x-0
               md:static md:inset-auto z-50"
        :class="{ '-translate-x-full': !open, 'translate-x-0': open }" @keydown.escape="open=false">
        {{-- Header Sidebar --}}
        <div class="h-16 flex items-center px-4 border-b border-gray-100 dark:border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">SMK Telkom Lampung</span>
            </a>
            <button class="ms-auto md:hidden p-2 text-gray-500 hover:text-gray-700" @click="open=false">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body Sidebar --}}
        <div class="h-[calc(100vh-4rem)] overflow-y-auto p-3 space-y-2">
            {{-- PRIMARY --}}
            <a href="{{ route('dashboard') }}" @class([
                'flex items-center gap-2 px-3 py-2 rounded-md text-sm transition',
                request()->routeIs('dashboard')
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900',
            ])>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('assets.index') }}" @class([
                'flex items-center gap-2 px-3 py-2 rounded-md text-sm transition',
                request()->routeIs('assets.*')
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900',
            ])>
                <span>Aset</span>
            </a>

            <a href="{{ route('disposedAssets.index') }}" @class([
                'flex items-center gap-2 px-3 py-2 rounded-md text-sm transition',
                request()->routeIs('disposedAssets.index')
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900',
            ])>
                <span>Riwayat Disposal</span>
            </a>

            <a href="{{ route('assets.summary') }}" @class([
                'flex items-center gap-2 px-3 py-2 rounded-md text-sm transition',
                request()->routeIs('assets.summary*')
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900',
            ])>
                <span>Ringkasan Aset</span>
            </a>

            {{-- INVENTARIS (Accordion) --}}
            <div class="pt-2">
                <button @click="invOpen = !invOpen" @class([
                    'w-full text-left flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium',
                    $isInventarisActive ?? false
                        ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'
                        : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900',
                ])>
                    <span>Inventaris</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': invOpen }" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.207l3.71-3.976a.75.75 0 111.08 1.04l-4.243 4.544a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="invOpen" x-collapse class="mt-1 ps-2 space-y-1">
                    <a href="{{ route('assignedAssets.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('assignedAssets.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Inventaris Pegawai</a>
                    <a href="{{ route('inventory.history') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('inventory.history')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Riwayat Inventaris</a>
                    <a href="{{ route('vehicleLogs.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('vehicleLogs.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Log Kendaraan</a>
                    <a href="{{ route('maintenance.history') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('maintenance.history')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Riwayat Maintenance</a>
                    <a href="{{ route('inspection.history') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('inspection.history')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Riwayat Inspeksi</a>
                    <a href="{{ route('books.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('books.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Aset Buku</a>
                    <a href="{{ route('asset-mapping.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('asset-mapping.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Mapping Aset (AI)</a>
                </div>
            </div>

            {{-- DATA REFERENSI (Accordion) --}}
            <div class="pt-2">
                <button @click="refOpen = !refOpen" @class([
                    'w-full text-left flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium',
                    $isDataReferensiActive ?? false
                        ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'
                        : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900',
                ])>
                    <span>Data Referensi</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': refOpen }" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.207l3.71-3.976a.75.75 0 111.08 1.04l-4.243 4.544a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="refOpen" x-collapse class="mt-1 ps-2 grid grid-cols-1 gap-1">
                    <a href="{{ route('institutions.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('institutions.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Lembaga</a>
                    <a href="{{ route('employees.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('employees.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Pegawai</a>
                    <a href="{{ route('buildings.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('buildings.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Gedung</a>
                    <a href="{{ route('rooms.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('rooms.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Ruangan</a>
                    <a href="{{ route('categories.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('categories.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Kategori</a>
                    <a href="{{ route('faculties.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('faculties.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Fakultas</a>
                    <a href="{{ route('departments.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('departments.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Prodi/Unit</a>
                    <a href="{{ route('persons-in-charge.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('persons-in-charge.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Penanggung Jawab</a>
                    <a href="{{ route('asset-functions.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('asset-functions.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Fungsi Barang</a>
                    <a href="{{ route('funding-sources.index') }}" @class([
                        'block px-3 py-1.5 rounded-md text-sm',
                        request()->routeIs('funding-sources.index')
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900',
                    ])>Jenis Pendanaan</a>
                </div>
            </div>

            {{-- User (Desktop shortcut) --}}
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-3">
                <div class="px-3 text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2">Akun</div>
                <a href="{{ route('profile.edit') }}"
                    class="block px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="px-3 mt-1">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </aside>
</nav>
