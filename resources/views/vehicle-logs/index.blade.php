<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Log Penggunaan Kendaraan Dinas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Penggunaan</h3>
                    <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $totalLogs }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Sedang Digunakan</h3>
                    <p class="text-4xl font-bold mt-2 text-yellow-600 dark:text-yellow-400">{{ $activeLogs }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Kendaraan Tersedia</h3>
                    {{-- Ganti jika perlu cara hitung yang lebih akurat --}}
                    <p class="text-4xl font-bold mt-2 text-green-600 dark:text-green-400">
                        {{ App\Models\Asset::whereHas('category', fn($q) => $q->where('name', 'KENDARAAN BERMOTOR DINAS / KBM DINAS'))->where('current_status', 'Tersedia')->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tren Penggunaan Kendaraan
                        (12 Bulan Terakhir)</h3>
                    <canvas id="vehicleLogChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Tombol Export & Search (Mirip Maintenance History) --}}
                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('vehicleLogs.exportExcel', request()->query()) }}"
                                class="bg-green-600 text-white font-bold py-2 px-4 rounded">Export Excel</a>
                            <a href="{{ route('vehicleLogs.downloadPDF', request()->query()) }}" target="_blank"
                                class="bg-red-600 text-white font-bold py-2 px-4 rounded">Laporan PDF</a>
                        </div>
                        <form action="{{ route('vehicleLogs.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari Kendaraan/Pegawai/Tujuan..."
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
                                    <th class="py-3 px-6">Kendaraan</th>
                                    <th class="py-3 px-6">Pegawai</th>
                                    <th class="py-3 px-6">Tujuan</th>
                                    <th class="py-3 px-6">Waktu</th>
                                    <th class="py-3 px-6">KM</th>
                                    <th class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6 font-semibold">{{ $log->asset->name ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $log->employee->name ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $log->destination }}</td>
                                        <td class="py-4 px-6 text-xs">
                                            {{ $log->departure_time->isoFormat('D MMM YY, HH:mm') }} - <br>
                                            {{ $log->return_time ? $log->return_time->isoFormat('D MMM YY, HH:mm') : '...' }}
                                        </td>
                                        <td class="py-4 px-6 text-xs">{{ $log->start_odometer }} -
                                            {{ $log->end_odometer ?? '...' }}</td>
                                        <td class="py-4 px-6 space-y-1">
                                            @if ($log->checkout_doc_number)
                                                <a href="{{ route('vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkout']) }}"
                                                    target="_blank"
                                                    class="flex items-center text-blue-500 hover:text-blue-700 text-xs">BAST
                                                    Ambil</a>
                                            @endif
                                            @if ($log->checkin_doc_number)
                                                <a href="{{ route('vehicleLogs.downloadBast', ['log' => $log->id, 'type' => 'checkin']) }}"
                                                    target="_blank"
                                                    class="flex items-center text-green-500 hover:text-green-700 text-xs">BAP
                                                    Kembali</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Tidak ada riwayat penggunaan
                                            kendaraan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $logs->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('vehicleLogChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line', // Tipe grafik garis
                    data: {
                        labels: @json($chartLabels), // Label sumbu X (bulan) dari controller
                        datasets: [{
                            label: 'Jumlah Penggunaan Kendaraan',
                            data: @json($chartData), // Data sumbu Y (jumlah) dari controller
                            borderColor: 'rgb(54, 162, 235)', // Warna garis biru
                            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Warna area di bawah garis
                            fill: true, // Isi area di bawah garis
                            tension: 0.1 // Sedikit melengkungkan garis
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true, // Mulai sumbu Y dari 0
                                ticks: {
                                    stepSize: 1, // Pastikan kelipatan angka bulat
                                    color: document.body.classList.contains('dark') ? '#cbd5e1' :
                                        '#6b7280' // Warna teks sumbu Y (sesuai dark mode)
                                },
                                grid: {
                                    color: document.body.classList.contains('dark') ?
                                        'rgba(255, 255, 255, 0.1)' :
                                        'rgba(0, 0, 0, 0.1)' // Warna garis grid sumbu Y
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.body.classList.contains('dark') ? '#cbd5e1' :
                                        '#6b7280' // Warna teks sumbu X
                                },
                                grid: {
                                    color: document.body.classList.contains('dark') ?
                                        'rgba(255, 255, 255, 0.1)' :
                                        'rgba(0, 0, 0, 0.1)' // Warna garis grid sumbu X
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // Sembunyikan legenda karena hanya satu dataset
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
