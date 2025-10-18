<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Pemeriksaan Kondisi Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Inspeksi</h3>
                    <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $totalInspections }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Kondisi Baik</h3>
                    <p class="text-4xl font-bold mt-2 text-green-600 dark:text-green-400">
                        {{ $conditionsCount->get('Baik', 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Perlu Perbaikan</h3>
                    <p class="text-4xl font-bold mt-2 text-yellow-600 dark:text-yellow-400">
                        {{ $conditionsCount->get('Perlu Perbaikan', 0) + $conditionsCount->get('Rusak Ringan', 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Rusak Berat</h3>
                    <p class="text-4xl font-bold mt-2 text-red-600 dark:text-red-400">
                        {{ $conditionsCount->get('Rusak Berat', 0) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tren Inspeksi Aset (12 Bulan
                        Terakhir)</h3>
                    <canvas id="inspectionChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('inspection.exportExcel', request()->query()) }}"
                                class="bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('inspection.downloadPDF', request()->query()) }}" target="_blank"
                                class="bg-red-600 text-white font-bold py-2 px-4 rounded">
                                Laporan PDF
                            </a>
                        </div>
                        <form action="{{ route('inspection.history') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari Aset/Kondisi/Pemeriksa..."
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
                                    <th class="py-3 px-6">Tanggal</th>
                                    <th class="py-3 px-6">Nama Aset</th>
                                    <th class="py-3 px-6">Kondisi</th>
                                    <th class="py-3 px-6">Catatan</th>
                                    <th class="py-3 px-6">Diperiksa Oleh</th>
                                    <th class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inspections as $inspection)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($inspection->inspection_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold">{{ $inspection->asset->name ?? '-' }}</td>
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
                                        <td class="py-4 px-6">{{ Str::limit($inspection->notes, 50) }}</td>
                                        <td class="py-4 px-6">{{ $inspection->inspector->name ?? 'Sistem' }}</td>
                                        <td class="py-4 px-6 space-y-1">
                                            <a href="{{ route('assets.show', $inspection->asset_id) }}"
                                                class="text-blue-500 hover:underline text-xs">Lihat Aset</a>
                                            @if ($inspection->inspection_doc_number)
                                                <a href="{{ route('inspections.downloadBast', $inspection->id) }}"
                                                    target="_blank"
                                                    class="flex items-center text-green-500 hover:text-green-700 text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                        </path>
                                                    </svg>
                                                    BAPK
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada riwayat inspeksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $inspections->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('inspectionChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Jumlah Inspeksi',
                            data: @json($chartData),
                            borderColor: 'rgb(153, 102, 255)', // Warna ungu
                            tension: 0.1
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
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
