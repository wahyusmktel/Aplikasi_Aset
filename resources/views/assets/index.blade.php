<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Aset') }}
        </h2>
    </x-slot>

    {{-- === INI PERBAIKAN UTAMA: Panggil pageData() === --}}
    <div x-data="pageData()" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Layout Grid untuk Filter dan Aksi Utama --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                        {{-- Kolom Kiri: Filter --}}
                        <div class="flex flex-wrap gap-4 items-center">
                            {{-- Filter Kategori --}}
                            <div class="flex items-center space-x-2">
                                <label for="category_filter" class="text-sm font-medium">Kategori:</label>
                                {{-- Gunakan directive x-select2 --}}
                                <select id="category_filter" name="category_ids[]" multiple="multiple"
                                    x-ref="categorySelect" class="rounded-md dark:bg-gray-700 text-sm w-full sm:w-64">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Langkah 2: Dropdown KECUALIKAN Kategori -->
                            <div class="flex items-center space-x-2">
                                <label for="exclude_category_filter" class="text-sm font-medium">Kecualikan:</label>
                                <select id="exclude_category_filter" name="exclude_category_ids[]" multiple="multiple"
                                    x-ref="excludeCategorySelect"
                                    class="rounded-md dark:bg-gray-700 text-sm w-full sm:w-64">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Tahun Pengadaan --}}
                            <div class="flex items-center space-x-2">
                                <label for="year_filter" class="text-sm font-medium">Tahun:</label>
                                <select id="year_filter" x-model="selectedYear"
                                    class="rounded-md dark:bg-gray-700 text-sm">
                                    <option value="all">Semua Tahun</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tombol Terapkan --}}
                            <button @click="applyFilters"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded text-sm">
                                Terapkan
                            </button>
                        </div>

                        {{-- Kolom Kanan: Tombol Aksi Utama & Pencarian --}}
                        <div class="flex flex-col md:items-end gap-4">
                            {{-- Grup Tombol Aksi Utama --}}
                            <div class="flex flex-wrap gap-2 justify-end">
                                <a href="{{ route('assets.create') }}"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Tambah Aset
                                </a>
                                <a href="{{ route('assets.batchCreate') }}"
                                    class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Tambah Massal
                                </a>
                                <button @click="showImportBatchModal = true"
                                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Impor Massal
                                </button>
                            </div>
                            {{-- Form Pencarian --}}
                            <form action="{{ route('assets.index') }}" method="GET" class="flex w-full md:w-auto">
                                <!-- Langkah 2: bawa include categories -->
                                <template x-for="categoryId in selectedCategories" :key="'inc-' + categoryId">
                                    <input type="hidden" name="category_ids[]" :value="categoryId">
                                </template>

                                <!-- Langkah 2: bawa EXCLUDE categories -->
                                <template x-for="categoryId in selectedExcludeCategories" :key="'exc-' + categoryId">
                                    <input type="hidden" name="exclude_category_ids[]" :value="categoryId">
                                </template>

                                <input type="hidden" name="purchase_year" :value="selectedYear">
                                <input type="hidden" name="per_page" :value="perPage">

                                <input type="text" name="search" placeholder="Cari nama atau kode aset..."
                                    class="form-input rounded-l-md dark:bg-gray-700 w-full md:w-64"
                                    value="{{ request('search') }}">
                                <button type="submit"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-r-md">
                                    Cari
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Grup Tombol Ekspor & Cetak Label --}}
                    <div class="flex flex-wrap gap-2 mb-6 border-t dark:border-gray-700 pt-4">
                        <button @click="openBulkEditModal" :disabled="selectedIds.length === 0"
                            class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded text-sm disabled:bg-amber-300 disabled:cursor-not-allowed">
                            Bulk Edit (<span x-text="selectedIds.length"></span>)
                        </button>
                        <button @click="printSelected" :disabled="selectedIds.length === 0"
                            class="bg-purple-500 text-white font-bold py-2 px-4 rounded text-sm disabled:bg-purple-300 disabled:cursor-not-allowed">
                            Cetak Label (<span x-text="selectedIds.length"></span>)
                        </button>
                        <a :href="generateExportUrl('{{ route('assets.exportActiveExcel') }}')"
                            class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded text-sm">
                            Export Excel (Filter)
                        </a>
                        <a :href="generateExportUrl('{{ route('assets.downloadActivePDF') }}')" target="_blank"
                            class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded text-sm">
                            Laporan PDF (Filter)
                        </a>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4">
                                        <input type="checkbox" @click="toggleAll($event)"
                                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                    </th>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Kode Aset YPT</th>
                                    <th scope="col" class="py-3 px-6">Nama Barang</th>
                                    <th scope="col" class="py-3 px-6">Tahun</th>
                                    <th scope="col" class="py-3 px-6">Lokasi</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assets as $index => $asset)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="p-4">
                                            <input type="checkbox" :value="{{ $asset->id }}" x-model="selectedIds"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ ($assets->currentPage() - 1) * $assets->perPage() + $index + 1 }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $asset->asset_code_ypt }}</td>
                                        <td class="py-4 px-6 font-semibold">{{ $asset->name }}</td>
                                        <td class="py-4 px-6">{{ $asset->purchase_year }}</td>
                                        <td class="py-4 px-6 text-xs">{{ $asset->building->name ?? '' }} /
                                            {{ $asset->room->name ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            <span @class([
                                                'px-2 py-1 font-semibold leading-tight text-xs rounded-full',
                                                'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' =>
                                                    $asset->current_status == 'Tersedia',
                                                'text-yellow-700 bg-yellow-100 dark:bg-yellow-700 dark:text-yellow-100' =>
                                                    $asset->current_status == 'Dipinjam' ||
                                                    $asset->current_status == 'Digunakan',
                                                'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' =>
                                                    $asset->current_status == 'Rusak',
                                                'text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-100' => !in_array(
                                                    $asset->current_status,
                                                    ['Tersedia', 'Dipinjam', 'Digunakan', 'Rusak']),
                                            ])>
                                                {{ $asset->current_status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex flex-nowrap gap-2">
                                                <a href="{{ route('assets.show', $asset->id) }}"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                    Detail
                                                </a>
                                                <a href="{{ route('assets.edit', $asset->id) }}"
                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                    Edit
                                                </a>
                                                <button onclick="confirmDelete({{ $asset->id }})"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-4 px-6 text-center">Tidak ada data aset.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi & "Per Halaman" --}}
                    <div class="mt-4 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <label for="per_page_select" class="text-gray-700 dark:text-gray-300">Tampilkan:</label>
                            <select id="per_page_select" x-model="perPage" @change="applyFilters"
                                class="rounded-md dark:bg-gray-700 text-sm border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                @foreach ($allowedPerPages as $option)
                                    <option value="{{ $option }}">{{ $option }} per halaman</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow text-right">
                            {{ $assets->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Modal Impor (tidak berubah) --}}
        <div x-show="showImportBatchModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="showImportBatchModal = false">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md"
                @click.away="showImportBatchModal = false">
                <h2 class="text-2xl font-bold mb-6">Impor Aset Massal</h2>
                <form action="{{ route('assets.importBatch') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                            File Excel</label>
                        <input type="file" name="file" id="file"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0"
                            required>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Gunakan template yang sesuai. Pastikan nama-nama data master (gedung, ruangan, dll) sudah
                            benar.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showImportBatchModal = false"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Batal</button>
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Impor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Langkah 4B: Modal Bulk Edit -->
        <div x-show="showBulkEditModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="showBulkEditModal = false">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-2xl"
                @click.away="showBulkEditModal = false">
                <h2 class="text-xl font-bold mb-4">Bulk Edit Aset Terpilih</h2>

                <form method="POST" action="{{ route('assets.bulkUpdateFields') }}">
                    @csrf
                    <!-- kirim ids -->
                    <input type="hidden" name="ids" :value="selectedIds.join(',')">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Barang -->
                        <div class="border dark:border-gray-700 rounded p-3">
                            <label class="flex items-center gap-2 text-sm mb-2">
                                <input type="checkbox" name="apply_name" x-model="apply.name" class="rounded">
                                <span>Ubah Nama Barang</span>
                            </label>
                            <input type="text" name="name" x-bind:disabled="!apply.name"
                                placeholder="Nama barang baru" class="w-full rounded-md dark:bg-gray-700">
                        </div>

                        <!-- Jenis Pendanaan -->
                        <div class="border dark:border-gray-700 rounded p-3">
                            <label class="flex items-center gap-2 text-sm mb-2">
                                <input type="checkbox" name="apply_funding" x-model="apply.funding" class="rounded">
                                <span>Ubah Jenis Pendanaan</span>
                            </label>
                            <select name="funding_source_id" x-bind:disabled="!apply.funding"
                                class="w-full rounded-md dark:bg-gray-700">
                                <option value="">-- Pilih Pendanaan --</option>
                                @foreach ($fundingSources as $fs)
                                    <option value="{{ $fs->id }}">{{ $fs->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ruangan -->
                        <div class="border dark:border-gray-700 rounded p-3">
                            <label class="flex items-center gap-2 text-sm mb-2">
                                <input type="checkbox" name="apply_room" x-model="apply.room" class="rounded">
                                <span>Ubah Ruangan</span>
                            </label>
                            <select name="room_id" x-bind:disabled="!apply.room"
                                class="w-full rounded-md dark:bg-gray-700">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($rooms as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun Pengadaan -->
                        <div class="border dark:border-gray-700 rounded p-3">
                            <label class="flex items-center gap-2 text-sm mb-2">
                                <input type="checkbox" name="apply_year" x-model="apply.year" class="rounded">
                                <span>Ubah Tahun Pengadaan</span>
                            </label>
                            <input type="number" name="purchase_year" x-bind:disabled="!apply.year" min="1900"
                                placeholder="YYYY" class="w-full rounded-md dark:bg-gray-700">
                        </div>

                        <!-- Penanggung Jawab -->
                        <div class="border dark:border-gray-700 rounded p-3 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm mb-2">
                                <input type="checkbox" name="apply_pic" x-model="apply.pic" class="rounded">
                                <span>Ubah Penanggung Jawab</span>
                            </label>
                            <select name="person_in_charge_id" x-bind:disabled="!apply.pic"
                                class="w-full rounded-md dark:bg-gray-700">
                                <option value="">-- Pilih Penanggung Jawab --</option>
                                @foreach ($personsInCharge as $pic)
                                    <option value="{{ $pic->id }}">{{ $pic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 gap-3">
                        <button type="button" @click="showBulkEditModal = false"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                            Terapkan ke (<span x-text="selectedIds.length"></span>) Aset
                        </button>
                    </div>
                </form>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                    Catatan: Jika Anda mengubah Tahun / Ruangan / Penanggung Jawab / Pendanaan, kode aset YPT akan
                    diregenerasi otomatis.
                </p>
            </div>
        </div>

    </div>

    {{-- Script JavaScript (tidak berubah dari sebelumnya, sudah benar) --}}
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pageData', () => ({
                    selectedIds: [],
                    showImportBatchModal: false,

                    showBulkEditModal: false,
                    apply: {
                        name: false,
                        funding: false,
                        room: false,
                        year: false,
                        pic: false
                    },

                    // Langkah 3: state include & exclude dari request
                    selectedCategories: @json(request()->input('category_ids', [])).map(String),
                    selectedExcludeCategories: @json(request()->input('exclude_category_ids', [])).map(String),

                    selectedYear: '{{ request('purchase_year', 'all') }}',
                    perPage: '{{ $perPage }}',

                    init() {
                        // Inisialisasi Select2 (INCLUDE)
                        const $inc = $(this.$refs.categorySelect);
                        $inc.select2({
                            theme: 'classic',
                            placeholder: 'Pilih kategori (TERMASUK)',
                            width: 'resolve'
                        });
                        $inc.val(this.selectedCategories).trigger('change');
                        $inc.on('change', () => {
                            this.selectedCategories = ($inc.val() || []).map(String);
                        });

                        // Langkah 3: Inisialisasi Select2 (EXCLUDE)
                        const $exc = $(this.$refs.excludeCategorySelect);
                        $exc.select2({
                            theme: 'classic',
                            placeholder: 'Pilih kategori (KECUALIKAN)',
                            width: 'resolve'
                        });
                        $exc.val(this.selectedExcludeCategories).trigger('change');
                        $exc.on('change', () => {
                            this.selectedExcludeCategories = ($exc.val() || []).map(String);
                        });
                    },

                    toggleAll(event) {
                        let checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                        this.selectedIds = event.target.checked ?
                            Array.from(checkboxes).map(cb => parseInt(cb.value)) : [];
                    },

                    // Langkah 3: builder URL export yang menyertakan include & exclude
                    generateExportUrl(baseUrl) {
                        const url = new URL(baseUrl);
                        url.searchParams.delete('category_ids[]');
                        (this.selectedCategories || []).forEach(id => {
                            url.searchParams.append('category_ids[]', id);
                        });

                        url.searchParams.delete('exclude_category_ids[]');
                        (this.selectedExcludeCategories || []).forEach(id => {
                            url.searchParams.append('exclude_category_ids[]', id);
                        });

                        url.searchParams.set('purchase_year', this.selectedYear);
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

                    // Langkah 3: applyFilters menyertakan include & exclude
                    applyFilters() {
                        const inc = Array.isArray(this.selectedCategories) ? this.selectedCategories : [];
                        const exc = Array.isArray(this.selectedExcludeCategories) ? this
                            .selectedExcludeCategories : [];

                        const url = new URL('{{ route('assets.index') }}');
                        inc.forEach(id => url.searchParams.append('category_ids[]', id));
                        exc.forEach(id => url.searchParams.append('exclude_category_ids[]', id));

                        url.searchParams.set('purchase_year', this.selectedYear);
                        url.searchParams.set('per_page', this.perPage);
                        url.searchParams.set('page', 1);

                        const currentSearch = '{{ request('search') }}';
                        if (currentSearch) url.searchParams.set('search', currentSearch);

                        window.location.href = url.toString();
                    }
                }));
            });

            // Fungsi confirmDelete (tanpa perubahan)
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data aset ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
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
    @endpush
</x-app-layout>
