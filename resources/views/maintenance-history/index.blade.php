<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Perbaikan & Perawatan Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Catatan</h3>
                    <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $totalRecords }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Biaya</h3>
                    <p class="text-4xl font-bold mt-2 text-blue-600 dark:text-blue-400">Rp
                        {{ number_format($totalCost, 0, ',', '.') }}</p>
                </div>
                {{-- Widget lain bisa ditambahkan --}}
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tren Maintenance (12 Bulan
                        Terakhir)</h3>
                    <canvas id="maintenanceChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('maintenance.exportExcel', request()->query()) }}"
                                class="bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('maintenance.downloadPDF', request()->query()) }}" target="_blank"
                                class="bg-red-600 text-white font-bold py-2 px-4 rounded">
                                Laporan PDF
                            </a>
                        </div>
                        <form action="{{ route('maintenance.history') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari Aset/Jenis/Deskripsi..."
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
                                    <th class="py-3 px-6">Jenis</th>
                                    <th class="py-3 px-6">Deskripsi</th>
                                    <th class="py-3 px-6">Biaya</th>
                                    <th class="py-3 px-6">Teknisi</th>
                                    <th class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($maintenances as $maintenance)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold">{{ $maintenance->asset->name ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $maintenance->type }}</td>
                                        <td class="py-4 px-6">{{ Str::limit($maintenance->description, 50) }}</td>
                                        {{-- Batasi panjang deskripsi --}}
                                        <td class="py-4 px-6">
                                            @if ($maintenance->cost)
                                                Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">{{ $maintenance->technician ?? '-' }}</td>
                                        <td class="py-4 px-6">
                                            {{-- Link ke detail aset untuk melihat konteks --}}
                                            <a href="{{ route('assets.show', $maintenance->asset_id) }}"
                                                class="text-blue-500 hover:underline text-xs">Lihat Aset</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada riwayat maintenance.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $maintenances->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('maintenanceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Jumlah Maintenance',
                            data: @json($chartData),
                            borderColor: 'rgb(255, 99, 132)',
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
