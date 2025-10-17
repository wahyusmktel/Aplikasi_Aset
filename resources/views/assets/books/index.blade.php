<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Aset Buku') }}
        </h2>
    </x-slot>

    <div x-data="pageData()" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total Buku</h3>
                    <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $totalBooks }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm col-span-2">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Buku per Tahun Pengadaan</h3>
                    <div class="mt-2 text-sm text-gray-900 dark:text-gray-100 grid grid-cols-2 md:grid-cols-4 gap-2">
                        @forelse($booksByYear as $item)
                            <div><span class="font-bold">{{ $item->purchase_year }}:</span> {{ $item->total }} buku
                            </div>
                        @empty
                            <p>Tidak ada data.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Grafik Pengadaan Buku per
                        Tahun</h3>
                    <canvas id="booksChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2">
                            <button @click="downloadSelected('excel')" :disabled="selectedIds.length === 0"
                                class="bg-green-600 text-white font-bold py-2 px-4 rounded disabled:bg-green-400">
                                Export Excel (<span x-text="selectedIds.length"></span>)
                            </button>
                            <button @click="downloadSelected('pdf')" :disabled="selectedIds.length === 0"
                                class="bg-red-600 text-white font-bold py-2 px-4 rounded disabled:bg-red-400">
                                Laporan PDF (<span x-text="selectedIds.length"></span>)
                            </button>
                            <button @click="printSelected" :disabled="selectedIds.length === 0"
                                class="bg-purple-500 text-white font-bold py-2 px-4 rounded disabled:bg-purple-300">
                                Cetak Label (<span x-text="selectedIds.length"></span>)
                            </button>
                        </div>
                        {{-- Form Search can be added here if needed --}}
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            {{-- Table Head --}}
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4"><input type="checkbox" @click="toggleAll"></th>
                                    <th scope="col" class="py-3 px-6">Nama Buku</th>
                                    <th scope="col" class="py-3 px-6">Tahun</th>
                                    <th scope="col" class="py-3 px-6">Lokasi</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            {{-- Table Body --}}
                            <tbody>
                                @forelse ($books as $book)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="p-4"><input type="checkbox" :value="{{ $book->id }}"
                                                x-model="selectedIds"></td>
                                        <td class="py-4 px-6 font-semibold">{{ $book->name }}</td>
                                        <td class="py-4 px-6">{{ $book->purchase_year }}</td>
                                        <td class="py-4 px-6 text-xs">{{ $book->building->name }} /
                                            {{ $book->room->name }}</td>
                                        <td class="py-4 px-6"><a href="{{ route('assets.show', $book->id) }}"
                                                class="text-blue-500 hover:underline">Detail</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Tidak ada data buku.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $books->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // AlpineJS logic
            function pageData() {
                return {
                    selectedIds: [],
                    toggleAll(event) {
                        let checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                        // Jika checkbox master dicentang, isi selectedIds dengan semua value. Jika tidak, kosongkan.
                        this.selectedIds = event.target.checked ? Array.from(checkboxes).map(cb => parseInt(cb.value)) : [];
                    },
                    downloadSelected(format) {
                        const ids = this.selectedIds.join(',');
                        if (ids) {
                            // Tentukan URL berdasarkan format yang diminta
                            let url = format === 'excel' ?
                                '{{ route('books.exportExcel') }}' :
                                '{{ route('books.downloadPDF') }}';

                            // Buka URL di tab yang sama untuk memulai download
                            window.location.href = `${url}?ids=${ids}`;
                        }
                    },
                    printSelected() {
                        const ids = this.selectedIds.join(',');
                        if (ids) {
                            const url = `{{ route('assets.printLabels') }}?ids=${ids}`;
                            // Buka di tab baru untuk halaman cetak
                            window.open(url, '_blank');
                        }
                    }
                };
            }
            // Chart.js rendering
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('booksChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Jumlah Buku Diadakan',
                            data: @json($chartValues),
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
