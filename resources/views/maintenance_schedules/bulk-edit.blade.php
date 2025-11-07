<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Update Progress Massal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('maintenance-schedules.bulkUpdate') }}">
                        @csrf

                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                                role="alert">
                                <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div
                            class="border p-4 rounded-lg dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 dark:bg-gray-700">
                            <div>
                                <x-input-label for="status" :value="__('Set Status Menjadi *')" />
                                <select id="status" name="status"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">-- Pilih Status Baru --</option>
                                    <option value="in_progress">In Progress (Dikerjakan)</option>
                                    <option value="completed">Completed (Selesai)</option>
                                    <option value="cancelled">Cancelled (Dibatalkan)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Tambah Catatan (Opsional)')" />
                                <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes"
                                    :value="old('notes')" placeholder="Misal: Selesai dikerjakan sesuai SOP." />
                                <p class="mt-1 text-xs text-gray-500">Catatan ini akan menimpa catatan sebelumnya pada
                                    jadwal yang dipilih.</p>
                            </div>
                        </div>
                        @if ($schedules->isNotEmpty())
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg border dark:border-gray-700">
                                {{-- Beri id="schedules-table" untuk DataTables --}}
                                <table class="w-full text-sm text-left" id="schedules-table">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="py-3 px-4 w-12">
                                                {{-- Beri id="select-all-schedules" untuk JS --}}
                                                <input type="checkbox" id="select-all-schedules"
                                                    class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                            </th>
                                            <th scope="col" class="py-3 px-6">Aset</th>
                                            <th scope="col" class="py-3 px-6">Judul Pekerjaan</th>
                                            <th scope="col" class="py-3 px-6">Tgl. Jadwal</th>
                                            <th scope="col" class="py-3 px-6">Status Saat Ini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedules as $schedule)
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <td class="py-4 px-4">
                                                    <input type="checkbox" name="schedule_ids[]"
                                                        value="{{ $schedule->id }}"
                                                        class="schedule-checkbox rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                                </td>
                                                <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                                                    {{ $schedule->asset->name ?? 'N/A' }}
                                                </td>
                                                <td class="py-4 px-6">{{ $schedule->title }}</td>
                                                <td class="py-4 px-6">
                                                    {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                                                </td>
                                                <td class="py-4 px-6">{{ ucfirst($schedule->status) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button class="ms-4">
                                    Update Status Massal Terpilih
                                </x-primary-button>
                            </div>
                        @else
                            <div class="text-center py-10 px-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Semua Beres!</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal pemeliharaan
                                    yang perlu ditindaklanjuti saat ini.</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk DataTables & Select All --}}
    @if ($schedules->isNotEmpty())
        @push('scripts')
            <script>
                $(document).ready(function() {
                    // 1. Inisialisasi DataTables
                    let table = $('#schedules-table').DataTable({
                        renderer: 'tailwindcss', // (Opsional: jika Anda pakai plugin tailwind)
                        language: {
                            search: "Cari jadwal:",
                            lengthMenu: "Tampilkan _MENU_ jadwal",
                            zeroRecords: "Tidak ditemukan",
                            info: "Halaman _PAGE_ dari _PAGES_",
                            infoEmpty: "Tidak ada jadwal",
                            infoFiltered: "(difilter dari _MAX_ total jadwal)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        }
                    });

                    // 2. Logika "Select All"
                    //    (Kompatibel dengan DataTables: hanya memilih yang terfilter)
                    $('#select-all-schedules').on('click', function() {
                        let rows = table.rows({
                            'search': 'applied'
                        }).nodes();
                        $('input[type="checkbox"]', rows).prop('checked', this.checked);
                    });

                    // 3. Logika untuk membatalkan centang "Select All"
                    //    jika salah satu checkbox di-uncheck
                    $('#schedules-table tbody').on('change', 'input[type="checkbox"]', function() {
                        if (!this.checked) {
                            $('#select-all-schedules').prop('checked', false);
                        }
                    });
                });
            </script>
        @endpush
    @endif
</x-app-layout>
