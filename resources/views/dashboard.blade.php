<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Aset</h3>
                        <p class="text-4xl font-bold mt-2">{{ $totalAssets }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aset per Kategori</h3>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pengadaan Aset per Tahun
                        </h3>
                        <canvas id="yearChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data dari Controller
                const categoryLabels = @json($categoryLabels);
                const categoryData = @json($categoryData);
                const yearLabels = @json($yearLabels);
                const yearData = @json($yearData);

                // Inisialisasi Chart Aset per Kategori
                const ctxCategory = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctxCategory, {
                    type: 'pie',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: categoryData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 159, 64, 0.8)'
                            ],
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: document.body.classList.contains('dark') ? '#FFF' : '#333'
                                }
                            }
                        }
                    }
                });

                // Inisialisasi Chart Aset per Tahun
                const ctxYear = document.getElementById('yearChart').getContext('2d');
                new Chart(ctxYear, {
                    type: 'bar',
                    data: {
                        labels: yearLabels,
                        datasets: [{
                            label: 'Jumlah Aset Diadakan',
                            data: yearData,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: document.body.classList.contains('dark') ? '#FFF' : '#333'
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.body.classList.contains('dark') ? '#FFF' : '#333'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
