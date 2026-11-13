<nav class="space-y-1">
    <!-- Dashboard -->
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
        {{ __('Dashboard') }}
    </x-sidebar-link>

    @php
        $user = Auth::user();
        $isApprover = $user->employee?->is_sarpra_it_lab || $user->employee?->is_kaur_it || $user->employee?->is_headmaster;
        $isAdmin = $user->isAdmin();
        $isRegularUser = !$isAdmin && !$isApprover;
    @endphp

    @if($isApprover && !$isAdmin)
    <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Persetujuan') }}
    </div>

    <x-sidebar-link :href="route('vehicleLogs.index')" :active="request()->routeIs('vehicleLogs.*')" icon="check-circle">
        {{ __('Approval Kendaraan') }}
    </x-sidebar-link>

    @if(auth()->user()->employee)
    <x-sidebar-link :href="route('user.dapodik.index')" :active="request()->routeIs('user.dapodik.*')" icon="identification">
        {{ __('Data Dapodik Saya') }}
    </x-sidebar-link>
    @endif
    @endif

    @if($isRegularUser)
    <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Layanan Pengguna') }}
    </div>

    <!-- Peminjaman Barang (User) -->
    <x-sidebar-link :href="route('user.peminjaman.index')" :active="request()->routeIs('user.peminjaman.*')" icon="inbox">
        {{ __('Peminjaman Barang') }}
    </x-sidebar-link>

    <!-- Peminjaman Kendaraan Dinas (User) -->
    <x-sidebar-link :href="route('user.kendaraan.index')" :active="request()->routeIs('user.kendaraan.*')" icon="truck">
        {{ __('Peminjaman Kendaraan') }}
    </x-sidebar-link>

    <!-- Data Dapodik Saya (Regular User) -->
    @if(auth()->user()->employee)
    <x-sidebar-link :href="route('user.dapodik.index')" :active="request()->routeIs('user.dapodik.*')" icon="identification">
        {{ __('Data Dapodik Saya') }}
    </x-sidebar-link>
    @endif
    @endif

    @if($isAdmin)
    <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Manajemen Sarpras') }}
    </div>

    <!-- Treeview: Manajemen Aset -->
    @php
        $isAsetActive = request()->routeIs(['assets.*', 'assignedAssets.*', 'inventory.*', 'disposedAssets.*', 'books.*', 'asset-mapping.*']);
    @endphp
    <x-sidebar-dropdown :active="$isAsetActive" title="Manajemen Aset" icon="cube">
        <x-sidebar-dropdown-link :href="route('assets.index')" :active="request()->routeIs('assets.index', 'assets.create', 'assets.edit', 'assets.show')">
            {{ __('Daftar Aset') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('assets.summary')" :active="request()->routeIs('assets.summary')">
            {{ __('Ringkasan Aset') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('assignedAssets.index')" :active="request()->routeIs('assignedAssets.*')">
            {{ __('Inventaris Pegawai') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('inventory.history')" :active="request()->routeIs('inventory.history')">
            {{ __('Riwayat Mutasi Aset') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('disposedAssets.index')" :active="request()->routeIs('disposedAssets.*')">
            {{ __('Riwayat Disposal') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('asset-mapping.index')" :active="request()->routeIs('asset-mapping.*')">
            {{ __('Mapping Aset (AI)') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('books.index')" :active="request()->routeIs('books.*')">
            {{ __('Aset Buku') }}
        </x-sidebar-dropdown-link>
    </x-sidebar-dropdown>

    <!-- Treeview: Operasional & Layanan -->
    @php
        $pendingBorrows = \App\Models\AssetBorrowRequest::pending()->count();
        $isOperasionalActive = request()->routeIs(['borrow-requests.*', 'maintenance-schedules.*', 'maintenance.history', 'inspection.history', 'vehicleLogs.*', 'asset-reports.*', 'admin.asset-reports.*']);
    @endphp
    <x-sidebar-dropdown :active="$isOperasionalActive" title="Operasional & Layanan" icon="wrench">
        @if($pendingBorrows > 0)
            <x-slot:badge>
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-bold shadow-2xs">
                    {{ $pendingBorrows > 99 ? '99+' : $pendingBorrows }}
                </span>
            </x-slot:badge>
        @endif
        <x-sidebar-dropdown-link :href="route('borrow-requests.index')" :active="request()->routeIs('borrow-requests.*')">
            <span class="flex items-center justify-between w-full">
                <span>{{ __('Peminjaman Aset') }}</span>
                @if($pendingBorrows > 0)
                <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-bold">
                    {{ $pendingBorrows > 99 ? '99+' : $pendingBorrows }}
                </span>
                @endif
            </span>
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('maintenance-schedules.index')" :active="request()->routeIs('maintenance-schedules.*')">
            {{ __('Jadwal Pemeliharaan') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('maintenance.history')" :active="request()->routeIs('maintenance.history')">
            {{ __('Riwayat Maintenance') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('inspection.history')" :active="request()->routeIs('inspection.history')">
            {{ __('Riwayat Inspeksi') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('vehicleLogs.index')" :active="request()->routeIs('vehicleLogs.*')">
            {{ __('Log Kendaraan') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('asset-reports.index')" :active="request()->routeIs('asset-reports.index', 'asset-reports.create', 'asset-reports.show')">
            {{ __('Lapor Kerusakan') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('admin.asset-reports.index')" :active="request()->routeIs('admin.asset-reports.*')">
            {{ __('Kelola Kerusakan') }}
        </x-sidebar-dropdown-link>
    </x-sidebar-dropdown>

    <!-- Treeview: Laboratorium -->
    @php
        $isLabActive = request()->routeIs(['labs.*']);
    @endphp
    <x-sidebar-dropdown :active="$isLabActive" title="Laboratorium" icon="academic-cap">
        <x-sidebar-dropdown-link :href="route('labs.index')" :active="request()->routeIs('labs.index', 'labs.create', 'labs.edit', 'labs.show')">
            {{ __('Daftar Ruang Lab') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('labs.inventory')" :active="request()->routeIs('labs.inventory')">
            {{ __('Inventaris Aset Lab') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('labs.history')" :active="request()->routeIs('labs.history')">
            {{ __('Riwayat Penggunaan Lab') }}
        </x-sidebar-dropdown-link>
    </x-sidebar-dropdown>

    <!-- Treeview: Pengadaan & Anggaran -->
    @php
        $isPengadaanActive = request()->routeIs(['procurements.*', 'rkas.*', 'rab.*', 'vendors.*']);
    @endphp
    <x-sidebar-dropdown :active="$isPengadaanActive" title="Pengadaan & Anggaran" icon="shopping-cart">
        <x-sidebar-dropdown-link :href="route('procurements.index')" :active="request()->routeIs('procurements.*')">
            {{ __('Pengadaan Aset') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('rkas.index')" :active="request()->routeIs('rkas.*')">
            {{ __('RKAS') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('rab.index')" :active="request()->routeIs('rab.*')">
            {{ __('RAB') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*')">
            {{ __('Rekanan / Vendor') }}
        </x-sidebar-dropdown-link>
    </x-sidebar-dropdown>

    <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Pengaturan & Master') }}
    </div>

    <!-- Treeview: Data Master -->
    @php
        $isDataMasterActive = request()->routeIs([
            'institutions.*', 'employees.*', 'buildings.*', 'rooms.*',
            'categories.*', 'faculties.*', 'departments.*',
            'persons-in-charge.*', 'asset-functions.*', 'funding-sources.*'
        ]);
    @endphp
    <x-sidebar-dropdown :active="$isDataMasterActive" title="Data Master" icon="database">
        <x-sidebar-dropdown-link :href="route('institutions.index')" :active="request()->routeIs('institutions.*')">
            {{ __('Lembaga') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
            {{ __('Pegawai') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('buildings.index')" :active="request()->routeIs('buildings.*')">
            {{ __('Gedung') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
            {{ __('Ruangan') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
            {{ __('Kategori') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('faculties.index')" :active="request()->routeIs('faculties.*')">
            {{ __('Fakultas') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
            {{ __('Prodi / Unit') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('persons-in-charge.index')" :active="request()->routeIs('persons-in-charge.*')">
            {{ __('Penanggung Jawab') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('asset-functions.index')" :active="request()->routeIs('asset-functions.*')">
            {{ __('Fungsi Barang') }}
        </x-sidebar-dropdown-link>
        <x-sidebar-dropdown-link :href="route('funding-sources.index')" :active="request()->routeIs('funding-sources.*')">
            {{ __('Sumber Pendanaan') }}
        </x-sidebar-dropdown-link>
    </x-sidebar-dropdown>

    <!-- Tanda Tangan Digital -->
    <x-sidebar-link :href="route('tanda-tangan.index')" :active="request()->routeIs('tanda-tangan.*')" icon="signature">
        {{ __('Tanda Tangan Digital') }}
    </x-sidebar-link>

    <!-- Pengaturan Sistem -->
    <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.*')" icon="cog">
        {{ __('Pengaturan Sistem') }}
    </x-sidebar-link>
    @endif

    {{-- Menu Tanda Tangan untuk non-admin yang login --}}
    @if(!$isAdmin)
    <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">
        {{ __('Akun & Tanda Tangan') }}
    </div>
    <x-sidebar-link :href="route('tanda-tangan.index')" :active="request()->routeIs('tanda-tangan.*')" icon="signature">
        {{ __('Tanda Tangan Digital') }}
    </x-sidebar-link>
    @endif
</nav>
