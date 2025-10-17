<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Aset: {{ $asset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 flex flex-col items-center">
                            <h3 class="text-lg font-bold mb-2">QR Code</h3>
                            <div class="p-4 border dark:border-gray-600 rounded-lg inline-block">
                                {{-- Ini adalah magic-nya! Generate QR code dengan URL halaman ini --}}
                                {!! QrCode::size(200)->generate(route('public.assets.show', $asset->asset_code_ypt)) !!}
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Scan untuk membuka halaman ini</p>
                            <p class="font-mono text-center bg-gray-100 dark:bg-gray-700 p-2 rounded-md mt-4 text-xs">
                                {{ $asset->asset_code_ypt }}</p>
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <h4 class="font-bold">Informasi Dasar</h4>
                                <ul class="list-disc list-inside text-sm space-y-1 mt-1">
                                    <li><strong>Nama Barang:</strong> {{ $asset->name }}</li>
                                    <li><strong>Tahun Pembelian:</strong> {{ $asset->purchase_year }}</li>
                                    <li><strong>No Urut:</strong> {{ $asset->sequence_number }}</li>
                                    <li><strong>Status:</strong> {{ $asset->status }}</li>
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

                    <div class="flex justify-end mt-6 pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('assets.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Kembali
                        </a>
                        <a href="{{ route('assets.edit', $asset->id) }}"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        {{-- Nanti di sini bisa tambah tombol "Cetak Label" --}}
                    </div>
                </div>
            </div>
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Status & Riwayat Inventaris</h3>

                    <div
                        class="mb-6 p-4 rounded-lg {{ $asset->current_status == 'Tersedia' ? 'bg-green-100 dark:bg-green-900' : 'bg-yellow-100 dark:bg-yellow-900' }}">
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

                    {{-- Form Aksi (Serah Terima / Pengembalian) --}}
                    @if ($asset->current_status == 'Tersedia')
                        {{-- Form untuk Serah Terima (Checkout) --}}
                        <div x-data="{ showForm: false }">
                            <button @click="showForm = !showForm"
                                class="bg-blue-500 text-white font-bold py-2 px-4 rounded">
                                <span x-show="!showForm">Serah Terima Aset</span>
                                <span x-show="showForm">Tutup Form</span>
                            </button>
                            <div x-show="showForm" x-transition class="mt-4 border-t pt-4">
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
                                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="assigned_date" class="block text-sm font-medium">Tanggal Serah
                                                Terima</label>
                                            <input type="date" name="assigned_date" id="assigned_date"
                                                value="{{ date('Y-m-d') }}"
                                                class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="condition_on_assign" class="block text-sm font-medium">Kondisi Aset
                                            Saat Diserahkan</label>
                                        <input type="text" name="condition_on_assign" id="condition_on_assign"
                                            value="Baik" class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                            required>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit"
                                            class="bg-green-500 text-white font-bold py-2 px-4 rounded">Simpan
                                            Penyerahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Form untuk Pengembalian (Check-in) --}}
                        <div>
                            <button @click="showReturnForm = !showReturnForm"
                                class="bg-orange-500 text-white font-bold py-2 px-4 rounded transition-all">
                                &#x21AA; Proses Pengembalian Aset
                            </button>
                            <div x-show="showReturnForm" x-transition class="mt-4 border-t pt-4 dark:border-gray-700">
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
                                            <label for="condition_on_return" class="block text-sm font-medium">Kondisi
                                                Saat Dikembalikan</label>
                                            <input type="text" name="condition_on_return" id="condition_on_return"
                                                value="Baik" class="mt-1 block w-full rounded-md dark:bg-gray-700"
                                                required>
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
                                            Pengembalian</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Riwayat Peminjaman --}}
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($asset->assignments()->latest()->get() as $assignment)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6 font-semibold">{{ $assignment->employee->name }}</td>
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
                                            <td class="py-4 px-6">{{ $assignment->condition_on_return ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">Belum ada riwayat penggunaan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
