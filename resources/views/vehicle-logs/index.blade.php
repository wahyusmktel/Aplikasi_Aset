<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Log Penggunaan Kendaraan Dinas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Panel Konfigurasi Auto-Approval ─────────────────────────────── --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->employee?->is_sarpra_it_lab || auth()->user()->employee?->is_kaur_it || auth()->user()->employee?->is_headmaster)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white">Konfigurasi Auto-Approval Kendaraan</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Jika diaktifkan, pengajuan peminjaman kendaraan akan disetujui otomatis tanpa perlu tindakan manual dari pejabat yang bersangkutan.</p>
                        </div>
                    </div>

                    <form action="{{ route('vehicleLogs.saveSettings') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Toggle: Waka / Kaur IT --}}
                            <label class="flex items-start gap-4 p-4 rounded-2xl border cursor-pointer transition-colors
                                {{ $autoApproveWaka ? 'border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/20' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/40' }}">
                                <div class="relative mt-0.5 shrink-0">
                                    <input type="checkbox" name="auto_approve_waka" value="1"
                                        {{ $autoApproveWaka ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-emerald-500 dark:peer-checked:bg-emerald-600"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        Auto-Approval Waka / Kaur IT
                                        @if($autoApproveWaka)
                                            <span class="text-[10px] font-black px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-800/40 dark:text-emerald-300 rounded-full uppercase tracking-widest">Aktif</span>
                                        @else
                                            <span class="text-[10px] font-black px-2 py-0.5 bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 rounded-full uppercase tracking-widest">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Persetujuan tahap pertama oleh <strong>Waka Bid. Sarpra IT &amp; Lab</strong> dan <strong>Kaur IT</strong> dilewati secara otomatis. Pengajuan langsung masuk ke tahap menunggu Kepala Sekolah.
                                    </p>
                                </div>
                            </label>

                            {{-- Toggle: Kepala Sekolah --}}
                            <label class="flex items-start gap-4 p-4 rounded-2xl border cursor-pointer transition-colors
                                {{ $autoApproveKepsek ? 'border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/20' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/40' }}">
                                <div class="relative mt-0.5 shrink-0">
                                    <input type="checkbox" name="auto_approve_kepsek" value="1"
                                        {{ $autoApproveKepsek ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-emerald-500 dark:peer-checked:bg-emerald-600"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        Auto-Approval Kepala Sekolah
                                        @if($autoApproveKepsek)
                                            <span class="text-[10px] font-black px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-800/40 dark:text-emerald-300 rounded-full uppercase tracking-widest">Aktif</span>
                                        @else
                                            <span class="text-[10px] font-black px-2 py-0.5 bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 rounded-full uppercase tracking-widest">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Persetujuan tahap akhir oleh <strong>Kepala Sekolah</strong> dilewati secara otomatis. Berlaku hanya jika Auto-Approval Waka/Kaur juga aktif — pengajuan langsung berstatus <em>Disetujui</em>.
                                    </p>
                                </div>
                            </label>

                        </div>

                        @if($autoApproveWaka || $autoApproveKepsek)
                        <div class="mt-4 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-xs text-amber-700 dark:text-amber-300">
                                <strong>Perhatian:</strong> Auto-approval aktif. Pengajuan peminjaman kendaraan baru akan
                                {{ $autoApproveWaka && $autoApproveKepsek ? 'langsung disetujui penuh tanpa perlu tindakan manual apapun' : 'melewati persetujuan Waka/Kaur dan langsung menunggu Kepala Sekolah' }}.
                                Nonaktifkan jika ingin kembali ke alur persetujuan manual.
                            </p>
                        </div>
                        @endif

                        <div class="mt-4 flex justify-end">
                            <button type="submit"
                                class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-xl shadow-md shadow-orange-500/20 transition-all">
                                Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            {{-- ──────────────────────────────────────────────────────────────────── --}}

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
                                    <th class="py-3 px-6">Status</th>
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
                                        <td class="py-4 px-6">
                                            @if($log->status === 'pengajuan')
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Menunggu Waka/Kaur</span>
                                            @elseif($log->status === 'menunggu_kepsek')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Menunggu Kepsek</span>
                                            @elseif($log->status === 'disetujui')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Sedang Jalan</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ ucfirst($log->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 space-y-2">
                                            @if ($log->status === 'pengajuan' && (auth()->user()->employee?->is_sarpra_it_lab || auth()->user()->employee?->is_kaur_it || auth()->user()->role === 'admin'))
                                                <form action="{{ route('vehicleLogs.approveWaka', $log->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-2 py-1 rounded">Setujui Waka</button>
                                                </form>
                                            @endif
                                            @if ($log->status === 'menunggu_kepsek' && (auth()->user()->employee?->is_headmaster || auth()->user()->role === 'admin'))
                                                <form action="{{ route('vehicleLogs.approveKepsek', $log->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-2 py-1 rounded">Setujui Kepsek</button>
                                                </form>
                                            @endif

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
