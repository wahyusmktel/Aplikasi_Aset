<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Aset: {{ $asset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Detail Aset & QR Code --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Kolom QR Code --}}
                        <div class="md:col-span-1 flex flex-col items-center">
                            <h3 class="text-lg font-bold mb-2">QR Code</h3>
                            <div class="p-4 border dark:border-gray-600 rounded-lg inline-block bg-white">
                                {!! QrCode::size(200)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Scan untuk membuka halaman publik</p>
                            <p
                                class="font-mono text-center bg-gray-100 dark:bg-gray-700 p-2 rounded-md mt-4 text-xs break-all">
                                {{ $asset->asset_code_ypt }}
                            </p>
                        </div>
                        {{-- Kolom Detail Aset --}}
                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <h4 class="font-bold">Informasi Dasar</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Nama Barang:</strong> {{ $asset->name }}</li>
                                    <li><strong>Tahun Pembelian:</strong> {{ $asset->purchase_year }}</li>
                                    <li><strong>No Urut:</strong> {{ $asset->sequence_number }}</li>
                                    <li><strong>Status Awal:</strong> {{ $asset->status }}</li> {{-- Status awal saat input --}}
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold">Lokasi & Kepemilikan</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Lembaga:</strong> {{ $asset->institution->name }}</li>
                                    <li><strong>Gedung:</strong> {{ $asset->building->name }}</li>
                                    <li><strong>Ruangan:</strong> {{ $asset->room->name }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold">Klasifikasi & Penanggung Jawab</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Kategori:</strong> {{ $asset->category->name }}</li>
                                    <li><strong>Fakultas/Direktorat:</strong> {{ $asset->faculty->name }}</li>
                                    <li><strong>Prodi/Unit:</strong> {{ $asset->department->name }}</li>
                                    <li><strong>Penanggung Jawab:</strong> {{ $asset->personInCharge->name }}</li>
                                    <li><strong>Fungsi Barang:</strong> {{ $asset->assetFunction->name }}</li>
                                    <li><strong>Sumber Dana:</strong> {{ $asset->fundingSource->name }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    {{-- Tombol Aksi Kembali & Edit --}}
                    <div class="flex justify-end mt-6 pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('assets.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Kembali
                        </a>
                        <a href="{{ route('assets.edit', $asset->id) }}"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tampilkan Bagian Inventaris jika BUKAN Kendaraan --}}
            @if ($asset->category->name != 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ showAssignForm: false, showReturnForm: false }">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2 dark:border-gray-700">Status & Riwayat
                            Inventaris</h3>

                        <div
                            class="mb-6 p-4 rounded-lg {{ $asset->current_status == 'Tersedia' ? 'bg-green-100 dark:bg-green-800/50' : 'bg-yellow-100 dark:bg-yellow-800/50' }}">
                            <p class="font-semibold">Status Saat Ini:
                                <span
                                    class="font-bold {{ $asset->current_status == 'Tersedia' ? 'text-green-700 dark:text-green-300' : 'text-yellow-700 dark:text-yellow-300' }}">
                                    {{ $asset->current_status }}
                                </span>
                            </p>
                            @if ($asset->currentAssignment)
                                <p class="text-sm mt-1">
                                    Dipegang oleh: <span
                                        class="font-semibold">{{ $asset->currentAssignment->employee->name }}</span>
                                    sejak
                                    {{ \Carbon\Carbon::parse($asset->currentAssignment->assigned_date)->isoFormat('D MMMM YYYY') }}
                                </p>
                            @endif
                        </div>

                        {{-- Form Aksi (Serah Terima / Pengembalian) Aset Biasa --}}
                        @if ($asset->current_status == 'Tersedia')
                            <div>
                                <button @click="showAssignForm = !showAssignForm"
                                    class="bg-blue-500 text-white font-bold py-2 px-4 rounded">
                                    <span x-show="!showAssignForm">&#x21A9; Serah Terima Aset</span>
                                    <span x-show="showAssignForm">Tutup Form</span>
                                </button>
                                <div x-show="showAssignForm" x-transition
                                    class="mt-4 border-t pt-4 dark:border-gray-700">
                                    <form action="{{ route('assets.assign', $asset->id) }}" method="POST">
                                        @csrf
                                        <h4 class="font-semibold mb-2">Form Serah Terima Aset</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="employee_id" class="block text-sm font-medium">Serahkan
                                                    Kepada</label>
                                                <select name="employee_id" id="employee_id"
                                                    class="select2 mt-1 block w-full" required>
                                                    <option value="">Pilih Pegawai</option>
                                                    @foreach (App\Models\Employee::orderBy('name')->get() as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label for="assigned_date" class="block text-sm font-medium">Tanggal
                                                    Serah Terima</label>
                                                <input type="date" name="assigned_date" id="assigned_date"
                                                    value="{{ date('Y-m-d') }}"
                                                    class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label for="condition_on_assign" class="block text-sm font-medium">Kondisi
                                                Aset Saat Diserahkan</label>
                                            <input type="text" name="condition_on_assign" id="condition_on_assign"
                                                value="Baik" class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                                required>
                                        </div>
                                        <div class="mt-4">
                                            <button type="submit"
                                                class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan
                                                Penyerahan & Unduh BAST</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- Jika status BUKAN Tersedia --}}
                            @if ($asset->currentAssignment)
                                <div>
                                    <button @click="showReturnForm = !showReturnForm"
                                        class="bg-orange-500 text-white font-bold py-2 px-4 rounded transition-all">
                                        <span x-show="!showReturnForm">&#x21AA; Proses Pengembalian Aset</span>
                                        <span x-show="showReturnForm">Tutup Form</span>
                                    </button>
                                    <div x-show="showReturnForm" x-transition
                                        class="mt-4 border-t pt-4 dark:border-gray-700">
                                        <form action="{{ route('assets.return', $asset->currentAssignment->id) }}"
                                            method="POST">
                                            @csrf
                                            <h4 class="font-semibold mb-2">Form Pengembalian Aset</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="returned_date" class="block text-sm font-medium">Tanggal
                                                        Pengembalian</label>
                                                    <input type="date" name="returned_date" id="returned_date"
                                                        value="{{ date('Y-m-d') }}"
                                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                                </div>
                                                <div>
                                                    <label for="condition_on_return"
                                                        class="block text-sm font-medium">Kondisi Saat
                                                        Dikembalikan</label>
                                                    <input type="text" name="condition_on_return"
                                                        id="condition_on_return" value="Baik"
                                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="notes" class="block text-sm font-medium">Catatan
                                                    (Opsional)</label>
                                                <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md dark:bg-gray-700"></textarea>
                                            </div>
                                            <div class="mt-4">
                                                <button type="submit"
                                                    class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan
                                                    Pengembalian & Unduh BAP</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-red-500">Status aset tidak 'Tersedia' namun tidak ditemukan data
                                    penggunaan aktif.</p>
                            @endif
                        @endif

                        {{-- Riwayat Peminjaman Aset Biasa --}}
                        <div class="mt-8">
                            <h4 class="font-semibold mb-2 text-lg">Riwayat Penggunaan Aset</h4>
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="py-3 px-6">Nama Pegawai</th>
                                            <th scope="col" class="py-3 px-6">Tgl. Pinjam</th>
                                            <th scope="col" class="py-3 px-6">Kondisi Pinjam</th>
                                            <th scope="col" class="py-3 px-6">Tgl. Kembali</th>
                                            <th scope="col" class="py-3 px-6">Kondisi Kembali</th>
                                            <th scope="col" class="py-3 px-6">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asset->assignments()->latest()->get() as $assignment)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="py-4 px-6 font-semibold">{{ $assignment->employee->name }}
                                                </td>
                                                <td class="py-4 px-6">
                                                    {{ \Carbon\Carbon::parse($assignment->assigned_date)->isoFormat('D MMM YYYY') }}
                                                </td>
                                                <td class="py-4 px-6">{{ $assignment->condition_on_assign }}</td>
                                                <td class="py-4 px-6">
                                                    @if ($assignment->returned_date)
                                                        {{ \Carbon\Carbon::parse($assignment->returned_date)->isoFormat('D MMM YYYY') }}
                                                    @else
                                                        <span class="text-yellow-500">Masih Dipinjam</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-6">{{ $assignment->condition_on_return ?? '-' }}
                                                </td>
                                                <td class="py-4 px-6 space-y-1">
                                                    @if ($assignment->checkout_doc_number)
                                                        <a href="{{ route('assignments.downloadBast', ['assignment' => $assignment->id, 'type' => 'checkout']) }}"
                                                            target="_blank"
                                                            class="flex items-center text-blue-500 hover:text-blue-700 font-semibold text-xs">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                </path>
                                                            </svg>
                                                            BAST Pinjam
                                                        </a>
                                                    @endif
                                                    @if ($assignment->return_doc_number)
                                                        <a href="{{ route('assignments.downloadBast', ['assignment' => $assignment->id, 'type' => 'return']) }}"
                                                            target="_blank"
                                                            class="flex items-center text-green-500 hover:text-green-700 font-semibold text-xs">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                </path>
                                                            </svg>
                                                            BAST Kembali
                                                        </a>
                                                    @endif
                                                    @if (!$assignment->checkout_doc_number && !$assignment->return_doc_number)
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">Belum ada riwayat
                                                    penggunaan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Riwayat Maintenance (Tampil untuk SEMUA jenis aset) --}}
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ showMaintenanceForm: false }">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2 dark:border-gray-700">Riwayat Perbaikan & Perawatan
                    </h3>
                    {{-- Tombol & Form Tambah Catatan Maintenance --}}
                    <div class="mb-6">
                        <button @click="showMaintenanceForm = !showMaintenanceForm"
                            class="bg-blue-500 text-white font-bold py-2 px-4 rounded transition-all">
                            <span x-show="!showMaintenanceForm">&#x271A; Tambah Catatan Maintenance</span>
                            <span x-show="showMaintenanceForm">Tutup Form</span>
                        </button>
                        <div x-show="showMaintenanceForm" x-transition
                            class="mt-4 border-t pt-4 dark:border-gray-700">
                            <form action="{{ route('maintenance.store', $asset->id) }}" method="POST">
                                @csrf
                                <h4 class="font-semibold mb-2">Form Catatan Maintenance</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="maintenance_date"
                                            class="block text-sm font-medium">Tanggal</label>
                                        <input type="date" name="maintenance_date" id="maintenance_date"
                                            value="{{ date('Y-m-d') }}"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                    </div>
                                    <div>
                                        <label for="type" class="block text-sm font-medium">Jenis
                                            Pekerjaan</label>
                                        <select name="type" id="type"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                            <option value="Perawatan Rutin">Perawatan Rutin</option>
                                            <option value="Perbaikan">Perbaikan</option>
                                            <option value="Upgrade">Upgrade</option>
                                            <option value="Inspeksi">Inspeksi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="cost" class="block text-sm font-medium">Biaya (Rp)
                                            (Opsional)</label>
                                        <input type="number" name="cost" id="cost" step="0.01"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                            placeholder="Contoh: 500000">
                                    </div>
                                    <div>
                                        <label for="technician" class="block text-sm font-medium">Teknisi/Vendor
                                            (Opsional)</label>
                                        <input type="text" name="technician" id="technician"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium">Deskripsi
                                        Pekerjaan</label>
                                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                        required></textarea>
                                </div>
                                <div>
                                    <button type="submit"
                                        class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan
                                        Catatan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- Tabel Riwayat Maintenance --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Tanggal</th>
                                    <th scope="col" class="py-3 px-6">Jenis</th>
                                    <th scope="col" class="py-3 px-6">Deskripsi</th>
                                    <th scope="col" class="py-3 px-6">Biaya</th>
                                    <th scope="col" class="py-3 px-6">Teknisi</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($asset->maintenances as $maintenance)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6">{{ $maintenance->type }}</td>
                                        <td class="py-4 px-6">{{ $maintenance->description }}</td>
                                        <td class="py-4 px-6">
                                            @if ($maintenance->cost)
                                                Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">{{ $maintenance->technician ?? '-' }}</td>
                                        <td class="py-4 px-6 space-x-2">
                                            {{-- Tombol Download BA Baru --}}
                                            @if ($maintenance->doc_number)
                                                <a href="{{ route('maintenance.downloadReport', $maintenance->id) }}"
                                                    target="_blank"
                                                    class="text-blue-500 hover:text-blue-700 text-xs font-semibold">
                                                    Unduh BA
                                                </a>
                                            @endif
                                            <button onclick="confirmMaintenanceDelete({{ $maintenance->id }})"
                                                class="text-red-500 hover:text-red-700 text-xs font-semibold">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada riwayat maintenance.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Riwayat Inspeksi (Tampil untuk SEMUA jenis aset) --}}
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ showInspectionForm: false }">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2 dark:border-gray-700">Riwayat Pemeriksaan Kondisi
                    </h3>
                    {{-- Tombol & Form Tambah Catatan Inspeksi --}}
                    <div class="mb-6">
                        <button @click="showInspectionForm = !showInspectionForm"
                            class="bg-blue-500 text-white font-bold py-2 px-4 rounded transition-all">
                            <span x-show="!showInspectionForm">&#x271A; Tambah Catatan Inspeksi</span>
                            <span x-show="showInspectionForm">Tutup Form</span>
                        </button>
                        <div x-show="showInspectionForm" x-transition class="mt-4 border-t pt-4 dark:border-gray-700">
                            <form action="{{ route('inspections.store', $asset->id) }}" method="POST">
                                @csrf
                                <h4 class="font-semibold mb-2">Form Catatan Inspeksi</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="inspection_date" class="block text-sm font-medium">Tanggal
                                            Inspeksi</label>
                                        <input type="date" name="inspection_date" id="inspection_date"
                                            value="{{ date('Y-m-d') }}"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                    </div>
                                    <div>
                                        <label for="condition" class="block text-sm font-medium">Kondisi Aset</label>
                                        <select name="condition" id="condition"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                            <option value="Baik">Baik</option>
                                            <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="notes_inspection" class="block text-sm font-medium">Catatan /
                                        Keterangan (Opsional)</label>
                                    <textarea name="notes" id="notes_inspection" rows="3" class="mt-1 block w-full rounded-md dark:bg-gray-700"></textarea>
                                </div>
                                <div>
                                    <button type="submit"
                                        class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan Catatan
                                        Inspeksi & Unduh BAPK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- Tabel Riwayat Inspeksi --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Tanggal</th>
                                    <th scope="col" class="py-3 px-6">Kondisi</th>
                                    <th scope="col" class="py-3 px-6">Catatan</th>
                                    <th scope="col" class="py-3 px-6">Diperiksa Oleh</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($asset->inspections as $inspection)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($inspection->inspection_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <span @class([
                                                'px-2 py-1 font-semibold leading-tight rounded-full text-xs',
                                                'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' =>
                                                    $inspection->condition == 'Baik',
                                                'text-yellow-700 bg-yellow-100 dark:bg-yellow-700 dark:text-yellow-100' =>
                                                    $inspection->condition == 'Perlu Perbaikan' ||
                                                    $inspection->condition == 'Rusak Ringan',
                                                'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' =>
                                                    $inspection->condition == 'Rusak Berat',
                                            ])>
                                                {{ $inspection->condition }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">{{ $inspection->notes ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $inspection->inspector->name ?? 'Sistem' }}</td>
                                        <td class="py-4 px-6">
                                            @if ($inspection->inspection_doc_number)
                                                <a href="{{ route('inspections.downloadBast', $inspection->id) }}"
                                                    target="_blank"
                                                    class="text-blue-500 hover:text-blue-700 font-semibold text-xs">
                                                    Cetak BAPK
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Belum ada riwayat inspeksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Log Kendaraan (Hanya muncul jika aset ADALAH Kendaraan) --}}
            @if ($asset->category->name == 'KENDARAAN BERMOTOR DINAS / KBM DINAS')
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ showCheckoutForm: false, showCheckinForm: false }">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2 dark:border-gray-700">Log Penggunaan Kendaraan
                        </h3>
                        {{-- Status Kendaraan --}}
                        <div
                            class="mb-6 p-4 rounded-lg {{ $asset->current_status == 'Tersedia' ? 'bg-green-100 dark:bg-green-800/50' : 'bg-yellow-100 dark:bg-yellow-800/50' }}">
                            <p class="font-semibold">Status Kendaraan:
                                <span
                                    class="font-bold {{ $asset->current_status == 'Tersedia' ? 'text-green-700 dark:text-green-300' : 'text-yellow-700 dark:text-yellow-300' }}">
                                    {{ $asset->current_status }}
                                </span>
                            </p>
                            @if ($asset->currentVehicleLog)
                                <p class="text-sm mt-1">
                                    Digunakan oleh: <span
                                        class="font-semibold">{{ $asset->currentVehicleLog->employee->name }}</span>
                                    sejak
                                    {{ $asset->currentVehicleLog->departure_time->isoFormat('D MMM YYYY, HH:mm') }}
                                </p>
                            @endif
                        </div>

                        {{-- Form Checkout / Checkin Kendaraan --}}
                        @if ($asset->current_status == 'Tersedia')
                            <div>
                                <button @click="showCheckoutForm = !showCheckoutForm"
                                    class="bg-blue-500 text-white font-bold py-2 px-4 rounded">
                                    <span x-show="!showCheckoutForm">&#x1F697; Catat Penggunaan Baru</span>
                                    <span x-show="showCheckoutForm">Tutup Form</span>
                                </button>
                                <div x-show="showCheckoutForm" x-transition
                                    class="mt-4 border-t pt-4 dark:border-gray-700">
                                    <form action="{{ route('vehicles.checkout', $asset->id) }}" method="POST">
                                        @csrf
                                        <h4 class="font-semibold mb-2">Form Penggunaan Kendaraan</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label for="employee_id_vehicle"
                                                    class="block text-sm font-medium">Digunakan Oleh</label>
                                                <select name="employee_id" id="employee_id_vehicle"
                                                    class="select2 mt-1 block w-full" required>
                                                    <option value="">Pilih Pegawai</option>
                                                    @foreach (App\Models\Employee::orderBy('name')->get() as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label for="departure_time" class="block text-sm font-medium">Waktu
                                                    Berangkat</label>
                                                <input type="datetime-local" name="departure_time"
                                                    id="departure_time" value="{{ now()->format('Y-m-d\TH:i') }}"
                                                    class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                            </div>
                                            <div>
                                                <label for="destination"
                                                    class="block text-sm font-medium">Tujuan</label>
                                                <input type="text" name="destination" id="destination"
                                                    class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                            </div>
                                            <div>
                                                <label for="start_odometer" class="block text-sm font-medium">KM
                                                    Awal</label>
                                                <input type="number" name="start_odometer" id="start_odometer"
                                                    class="mt-1 block w-full rounded-md dark:bg-gray-700" required
                                                    min="0">
                                            </div>
                                            <div>
                                                <label for="condition_on_checkout"
                                                    class="block text-sm font-medium">Kondisi Awal</label>
                                                <input type="text" name="condition_on_checkout"
                                                    id="condition_on_checkout" value="Baik"
                                                    class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="purpose" class="block text-sm font-medium">Keperluan</label>
                                            <textarea name="purpose" id="purpose" rows="2" class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                                required></textarea>
                                        </div>
                                        <button type="submit"
                                            class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan & Cetak
                                            BAST</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- Jika status BUKAN Tersedia --}}
                            @if ($asset->currentVehicleLog)
                                <div>
                                    <button @click="showCheckinForm = !showCheckinForm"
                                        class="bg-orange-500 text-white font-bold py-2 px-4 rounded">
                                        <span x-show="!showCheckinForm">&#x1F519; Catat Pengembalian</span>
                                        <span x-show="showCheckinForm">Tutup Form</span>
                                    </button>
                                    <div x-show="showCheckinForm" x-transition
                                        class="mt-4 border-t pt-4 dark:border-gray-700">
                                        <form
                                            action="{{ route('vehicleLogs.checkin', $asset->currentVehicleLog->id) }}"
                                            method="POST">
                                            @csrf
                                            <h4 class="font-semibold mb-2">Form Pengembalian Kendaraan</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <label for="return_time" class="block text-sm font-medium">Waktu
                                                        Kembali</label>
                                                    <input type="datetime-local" name="return_time" id="return_time"
                                                        value="{{ now()->format('Y-m-d\TH:i') }}"
                                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                                </div>
                                                <div>
                                                    <label for="end_odometer" class="block text-sm font-medium">KM
                                                        Akhir</label>
                                                    <input type="number" name="end_odometer" id="end_odometer"
                                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required
                                                        min="{{ $asset->currentVehicleLog->start_odometer }}">
                                                </div>
                                                <div>
                                                    <label for="condition_on_checkin"
                                                        class="block text-sm font-medium">Kondisi Akhir</label>
                                                    <input type="text" name="condition_on_checkin"
                                                        id="condition_on_checkin" value="Baik"
                                                        class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <label for="notes_vehicle" class="block text-sm font-medium">Catatan
                                                    (Opsional)</label>
                                                <textarea name="notes" id="notes_vehicle" rows="2" class="mt-1 block w-full rounded-md dark:bg-gray-700"></textarea>
                                            </div>
                                            <button type="submit"
                                                class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan &
                                                Cetak BAP</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-red-500">Status aset tidak 'Tersedia' namun tidak ditemukan data
                                    penggunaan aktif.</p>
                            @endif
                        @endif

                        {{-- Tabel Riwayat Penggunaan Kendaraan Ini --}}
                        <div class="mt-8">
                            <h4 class="font-semibold mb-2 text-lg">Riwayat Penggunaan Kendaraan Ini</h4>
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="py-3 px-6">Pegawai</th>
                                            <th scope="col" class="py-3 px-6">Tujuan</th>
                                            <th scope="col" class="py-3 px-6">Waktu Berangkat</th>
                                            <th scope="col" class="py-3 px-6">Waktu Kembali</th>
                                            <th scope="col" class="py-3 px-6">KM (Awal - Akhir)</th>
                                            <th scope="col" class="py-3 px-6">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asset->vehicleLogs as $log)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="py-4 px-6 font-semibold">{{ $log->employee->name ?? '-' }}
                                                </td>
                                                <td class="py-4 px-6">{{ $log->destination }}</td>
                                                <td class="py-4 px-6">
                                                    {{ $log->departure_time->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                                <td class="py-4 px-6">
                                                    @if ($log->return_time)
                                                        {{ $log->return_time->isoFormat('D MMM YYYY, HH:mm') }}
                                                    @else
                                                        <span class="text-yellow-500 font-semibold">Digunakan</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-6">{{ number_format($log->start_odometer) }} -
                                                    {{ $log->end_odometer ? number_format($log->end_odometer) : '...' }}
                                                </td>
                                                <td class="py-4 px-6 space-y-1">
                                                    @if ($log->checkout_doc_number)
                                                        <a href="{{ route('vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkout']) }}"
                                                            target="_blank"
                                                            class="flex items-center text-blue-500 hover:text-blue-700 text-xs">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                </path>
                                                            </svg>
                                                            BAST Ambil
                                                        </a>
                                                    @endif
                                                    @if ($log->checkin_doc_number)
                                                        <a href="{{ route('vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkin']) }}"
                                                            target="_blank"
                                                            class="flex items-center text-green-500 hover:text-green-700 text-xs">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                </path>
                                                            </svg>
                                                            BAP Kembali
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">Belum ada riwayat
                                                    penggunaan untuk kendaraan ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
    {{-- Scripts --}}
    @push('scripts')
        <script>
            function confirmMaintenanceDelete(id) {
                Swal.fire({
                    title: 'Hapus Catatan Ini?',
                    text: "Tindakan ini tidak bisa dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.action = `/maintenance/${id}`;
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
