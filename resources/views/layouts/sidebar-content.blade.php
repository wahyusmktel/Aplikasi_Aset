<nav class="px-3 space-y-1">
    <!-- Dashboard -->
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
        {{ __('Dashboard') }}
    </x-sidebar-link>

    <div class="my-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Aset & Inventaris') }}
    </div>

    <!-- Aset -->
    <x-sidebar-link :href="route('assets.index')" :active="request()->routeIs('assets.*')" icon="cube">
        {{ __('Aset') }}
    </x-sidebar-link>

    <!-- Jadwal Pemeliharaan -->
    <x-sidebar-link :href="route('maintenance-schedules.index')" :active="request()->routeIs('maintenance-schedules.*')" icon="calendar">
        {{ __('Jadwal Pemeliharaan') }}
    </x-sidebar-link>

    <!-- Riwayat Disposal -->
    <x-sidebar-link :href="route('disposedAssets.index')" :active="request()->routeIs('disposedAssets.index')" icon="trash">
        {{ __('Riwayat Disposal') }}
    </x-sidebar-link>

    <!-- Ringkasan Aset -->
    <x-sidebar-link :href="route('assets.summary')" :active="request()->routeIs('assets.summary')" icon="chart-bar">
        {{ __('Ringkasan Aset') }}
    </x-sidebar-link>

    <!-- Lab -->
    <x-sidebar-link :href="route('labs.index')" :active="request()->routeIs('labs.*')" icon="academic-cap">
        {{ __('Lab') }}
    </x-sidebar-link>

    <!-- Inventaris Dropdown -->
    @php
        $isInventarisActive = request()->routeIs(['assignedAssets.index', 'inventory.history', 'maintenance.history', 'inspection.history', 'vehicleLogs.index', 'books.index', 'asset-mapping.index', 'labs.history']);
    @endphp
    <x-sidebar-dropdown :active="$isInventarisActive" title="Inventaris" icon="collection">
        <x-sidebar-dropdown-link :href="route('assignedAssets.index')" :active="request()->routeIs('assignedAssets.index')">{{ __('Inventaris Pegawai') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('inventory.history')" :active="request()->routeIs('inventory.history')">{{ __('Riwayat Inventaris') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('vehicleLogs.index')" :active="request()->routeIs('vehicleLogs.index')">{{ __('Log Kendaraan') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('maintenance.history')" :active="request()->routeIs('maintenance.history')">{{ __('Riwayat Maintenance') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('inspection.history')" :active="request()->routeIs('inspection.history')">{{ __('Riwayat Inspeksi') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('books.index')" :active="request()->routeIs('books.index')">{{ __('Aset Buku') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('asset-mapping.index')" :active="request()->routeIs('asset-mapping.index')">{{ __('Mapping Aset (AI)') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('labs.history')" :active="request()->routeIs('labs.history')">{{ __('Riwayat Penggunaan Lab') }}</x-sidebar-dropdown-link>
    </x-sidebar-dropdown>

    <div class="my-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Pengadaan') }}
    </div>

    <!-- Pengadaan -->
    <x-sidebar-link :href="route('procurements.index')" :active="request()->routeIs('procurements.*')" icon="shopping-cart">
        {{ __('Pengadaan Aset') }}
    </x-sidebar-link>

    <!-- Rekanan -->
    <x-sidebar-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*')" icon="user-group">
        {{ __('Rekanan/Vendor') }}
    </x-sidebar-link>

    <div class="my-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Data Master') }}
    </div>

    <!-- Data Referensi Dropdown -->
    @php
        $isDataReferensiActive = request()->routeIs(['institutions.index', 'employees.index', 'buildings.index', 'rooms.index', 'categories.index', 'faculties.index', 'departments.index', 'persons-in-charge.index', 'asset-functions.index', 'funding-sources.index']);
    @endphp
    <x-sidebar-dropdown :active="$isDataReferensiActive" title="Data Referensi" icon="database">
        <x-sidebar-dropdown-link :href="route('institutions.index')" :active="request()->routeIs('institutions.index')">{{ __('Lembaga') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('employees.index')" :active="request()->routeIs('employees.index')">{{ __('Pegawai') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('buildings.index')" :active="request()->routeIs('buildings.index')">{{ __('Gedung') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('rooms.index')" :active="request()->routeIs('rooms.index')">{{ __('Ruangan') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">{{ __('Kategori') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('faculties.index')" :active="request()->routeIs('faculties.index')">{{ __('Fakultas') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('departments.index')" :active="request()->routeIs('departments.index')">{{ __('Prodi/Unit') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('persons-in-charge.index')" :active="request()->routeIs('persons-in-charge.index')">{{ __('Penanggung Jawab') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('asset-functions.index')" :active="request()->routeIs('asset-functions.index')">{{ __('Fungsi Barang') }}</x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('funding-sources.index')" :active="request()->routeIs('funding-sources.index')">{{ __('Jenis Pendanaan') }}</x-sidebar-dropdown-link>
    </x-sidebar-dropdown>
</nav>
