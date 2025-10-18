<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Aset Dihapus / Disposal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Dihapus</h3>
                    <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $totalDisposed }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Rusak/Hapus Buku</h3>
                    <p class="text-4xl font-bold mt-2 text-red-600 dark:text-red-400">
                        {{ $disposedByMethod->get('Dihapusbukukan (Rusak)', 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Hilang</h3>
                    <p class="text-4xl font-bold mt-2 text-orange-600 dark:text-orange-400">
                        {{ $disposedByMethod->get('Hilang', 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Dijual/Dihibahkan</h3>
                    <p class="text-4xl font-bold mt-2 text-blue-600 dark:text-blue-400">
                        {{ $disposedByMethod->get('Dijual', 0) + $disposedByMethod->get('Dihibahkan', 0) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tren Penghapusan Aset (12
                        Bulan Terakhir)</h3>
                    <canvas id="disposalChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('disposedAssets.exportExcel', request()->query()) }}"
                                class="bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('disposedAssets.downloadPDF', request()->query()) }}" target="_blank"
                                class="bg-red-600 text-white font-bold py-2 px-4 rounded">
                                Laporan PDF
                            </a>
                        </div>
                        <form action="{{ route('disposedAssets.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari Aset/Metode/Alasan..."
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
                                    <th class="py-3 px-6">Tanggal Hapus</th>
                                    <th class="py-3 px-6">Nama Aset</th>
                                    <th class="py-3 px-6">Kode Aset</th>
                                    <th class="py-3 px-6">Metode</th>
                                    <th class="py-3 px-6">Alasan</th>
                                    <th class="py-3 px-6">No. BAPh</th>
                                    <th class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($disposedAssets as $asset)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($asset->disposal_date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold">{{ $asset->name ?? '-' }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $asset->asset_code_ypt ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6">{{ $asset->disposal_method }}</td>
                                        <td class="py-4 px-6">{{ Str::limit($asset->disposal_reason, 50) }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">
                                            {{ $asset->disposal_doc_number ?? '-' }}</td>
                                        <td class="py-4 px-6">
                                            @if ($asset->disposal_doc_number)
                                                <a href="{{ route('disposals.downloadBaph', $asset->id) }}"
                                                    target="_blank" class="text-blue-500 hover:underline text-xs">Unduh
                                                    BAPh</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada data aset yang dihapus.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $disposedAssets->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('disposalChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Jumlah Aset Dihapus',
                            data: @json($chartData),
                            borderColor: 'rgb(239, 68, 68)', // Warna Merah
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
