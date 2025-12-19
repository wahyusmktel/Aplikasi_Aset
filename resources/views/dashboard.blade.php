<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-8">
        <!-- Welcoming Header with Gradient -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold text-white mb-2">Halo, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-primary-100 text-lg opacity-90">Selamat datang kembali di sistem manajemen aset Anda.</p>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Assets -->
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 p-6 group hover:shadow-xl transition-all duration-300 animate-slideUp" style="animation-delay: 100ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <span class="text-xs font-bold text-primary-600 bg-primary-50 dark:bg-primary-900/20 px-2.5 py-1 rounded-full">+12%</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Aset') }}</h3>
                <p class="text-3xl font-black mt-1 text-gray-800 dark:text-white tabular-nums">{{ number_format($totalAssets) }}</p>
            </div>

            <!-- Total Pegawai -->
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 p-6 group hover:shadow-xl transition-all duration-300 animate-slideUp" style="animation-delay: 200ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/20 px-2.5 py-1 rounded-full">Pegawai</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Pegawai') }}</h3>
                <p class="text-3xl font-black mt-1 text-gray-800 dark:text-white tabular-nums">{{ number_format($totalEmployees) }}</p>
            </div>

            <!-- Total Ruangan -->
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 p-6 group hover:shadow-xl transition-all duration-300 animate-slideUp" style="animation-delay: 300ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-50 dark:bg-orange-900/20 px-2.5 py-1 rounded-full">Ruangan</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Ruangan') }}</h3>
                <p class="text-3xl font-black mt-1 text-gray-800 dark:text-white tabular-nums">{{ number_format($totalRooms) }}</p>
            </div>

            <!-- Pending Maintenance -->
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 p-6 group hover:shadow-xl transition-all duration-300 animate-slideUp" style="animation-delay: 400ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <span class="text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/20 px-2.5 py-1 rounded-full">Urgent</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Jadwal Maintenance') }}</h3>
                <p class="text-3xl font-black mt-1 text-gray-800 dark:text-white tabular-nums">{{ number_format($pendingMaintenance) }}</p>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left Column: Charts -->
            <div class="xl:col-span-2 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Chart Category -->
                    <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 p-6 animate-fadeIn" style="animation-delay: 500ms">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('Aset per Kategori') }}</h3>
                            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                            </div>
                        </div>
                        <div class="h-64 relative">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>

                    <!-- Chart Year -->
                    <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 p-6 animate-fadeIn" style="animation-delay: 600ms">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('Tren Pengadaan') }}</h3>
                            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            </div>
                        </div>
                        <div class="h-64 relative">
                            <canvas id="yearChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Assets Table -->
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-800 animate-fadeIn" style="animation-delay: 700ms">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('Aset Terbaru') }}</h3>
                        <a href="{{ route('assets.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">Lihat Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nama Aset') }}</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Kategori') }}</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Lokasi') }}</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">{{ __('Waktu') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @foreach($recentAssets as $asset)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-xs mr-3">
                                                {{ substr($asset->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $asset->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 italic">
                                            {{ $asset->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $asset->room->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs text-gray-400">{{ $asset->created_at->diffForHumans() }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Quick Links & Summary -->
            <div class="space-y-8 animate-fadeIn" style="animation-delay: 800ms">
                <!-- Activity Summary Bar -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Ringkasan Sistem</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1.5 font-medium">
                                <span class="text-gray-500">Pemanfaatan Kapasitas</span>
                                <span class="text-primary-600 font-bold">78%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-primary-400 to-primary-600 rounded-full" style="width: 78%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1.5 font-medium">
                                <span class="text-gray-500">Anggaran Pemeliharaan</span>
                                <span class="text-blue-600 font-bold">45%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-6 text-white shadow-xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-4">Aksi Cepat</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('assets.create') }}" class="bg-white/10 hover:bg-white/20 p-4 rounded-xl text-center transition-colors">
                                <svg class="w-6 h-6 mx-auto mb-2 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                <span class="text-xs font-semibold">Aset Baru</span>
                            </a>
                            <a href="{{ route('inventory.history') }}" class="bg-white/10 hover:bg-white/20 p-4 rounded-xl text-center transition-colors">
                                <svg class="w-6 h-6 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                <span class="text-xs font-semibold">Laporan</span>
                            </a>
                        </div>
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary-600/20 rounded-full blur-3xl group-hover:bg-primary-600/30 transition-colors"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const isDark = document.body.classList.contains('dark') || document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#94a3b8' : '#64748b';
                const gridColor = isDark ? '#1e293b' : '#f1f5f9';

                // Data dari Controller
                const categoryLabels = @json($categoryLabels);
                const categoryData = @json($categoryData);
                const yearLabels = @json($yearLabels);
                const yearData = @json($yearData);

                // Inisialisasi Chart Aset per Kategori
                const ctxCategory = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctxCategory, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryData,
                            backgroundColor: [
                                '#f43f5e', // primary-500
                                '#fb7185', // primary-400
                                '#3b82f6', // blue-500
                                '#6366f1', // indigo-500
                                '#f59e0b', // amber-500
                                '#10b981', // emerald-500
                            ],
                            borderWidth: 0,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    color: textColor,
                                    font: { family: 'Outfit', size: 11, weight: '500' }
                                }
                            }
                        },
                        cutout: '75%'
                    }
                });

                // Inisialisasi Chart Aset per Tahun
                const ctxYear = document.getElementById('yearChart').getContext('2d');
                const gradient = ctxYear.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, '#f43f5e');
                gradient.addColorStop(1, '#be123c');

                new Chart(ctxYear, {
                    type: 'bar',
                    data: {
                        labels: yearLabels,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: yearData,
                            backgroundColor: gradient,
                            borderRadius: 12,
                            barThickness: 24,
                            maxBarThickness: 32,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor, drawBorder: false, borderDash: [5, 5] },
                                ticks: { color: textColor, font: { family: 'Outfit' }, stepSize: 1 }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor, font: { family: 'Outfit', weight: '500' } }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: isDark ? '#0f172a' : '#fff',
                                titleColor: isDark ? '#fff' : '#0f172a',
                                bodyColor: isDark ? '#94a3b8' : '#64748b',
                                borderColor: isDark ? '#1e293b' : '#e2e8f0',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 12,
                                displayColors: false
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
