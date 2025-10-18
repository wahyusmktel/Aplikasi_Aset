<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Inventaris Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Riwayat</h3>
                    <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $totalAssignments }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Aset Sedang Dipinjam</h3>
                    <p class="text-4xl font-bold mt-2 text-yellow-600 dark:text-yellow-400">{{ $activeAssignments }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Aset Tersedia</h3>
                    {{-- Ganti App\Models\Asset::where('current_status', 'Tersedia')->count() jika ingin hitung dari tabel Aset --}}
                    <p class="text-4xl font-bold mt-2 text-green-600 dark:text-green-400">
                        {{ App\Models\Asset::count() - $activeAssignments }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tren Peminjaman Aset (12
                        Bulan Terakhir)</h3>
                    <canvas id="assignmentChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('inventory.exportExcel', request()->query()) }}"
                                class="bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('inventory.downloadPDF', request()->query()) }}" target="_blank"
                                class="bg-red-600 text-white font-bold py-2 px-4 rounded">
                                Laporan PDF
                            </a>
                        </div>
                        <form action="{{ route('inventory.history') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari Aset/Pegawai/No Surat..."
                                class="form-input rounded-md dark:bg-gray-700 w-64" value="{{ request('search') }}">
                            <button type="submit"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md ml-2">Cari</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-6">Nama Aset</th>
                                    <th class="py-3 px-6">Pegawai</th>
                                    <th class="py-3 px-6">Tgl Pinjam</th>
                                    <th class="py-3 px-6">Kondisi Pinjam</th>
                                    <th class="py-3 px-6">Tgl Kembali</th>
                                    <th class="py-3 px-6">Kondisi Kembali</th>
                                    <th class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignments as $assignment)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6 font-semibold">{{ $assignment->asset->name ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $assignment->employee->name ?? '-' }}</td>
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($assignment->assigned_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6">{{ $assignment->condition_on_assign }}</td>
                                        <td class="py-4 px-6">
                                            @if ($assignment->returned_date)
                                                {{ \Carbon\Carbon::parse($assignment->returned_date)->isoFormat('D MMM YYYY') }}
                                            @else
                                                <span class="text-yellow-500 font-semibold">Dipinjam</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">{{ $assignment->condition_on_return ?? '-' }}</td>
                                        <td class="py-4 px-6 space-y-1">
                                            @if ($assignment->checkout_doc_number)
                                                <a href="{{ route('assignments.downloadBast', ['assignment' => $assignment->id, 'type' => 'checkout']) }}"
                                                    target="_blank"
                                                    class="flex items-center text-blue-500 hover:text-blue-700 text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
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
                                                    class="flex items-center text-green-500 hover:text-green-700 text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                        </path>
                                                    </svg>
                                                    BAST Kembali
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada riwayat inventaris.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $assignments->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('assignmentChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: @json($chartData),
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        } // Paksa Y axis mulai dari 0 dan kelipatan 1
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
