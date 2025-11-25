<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Penggunaan Lab') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Filter & Export Section --}}
                    <form action="{{ route('labs.history') }}" method="GET"
                        class="mb-8 border-b dark:border-gray-700 pb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            {{-- Filter Ruangan --}}
                            <div>
                                <label class="block text-sm font-medium mb-1">Ruangan Lab</label>
                                <select name="room_id"
                                    class="w-full rounded-md dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                                    <option value="">Semua Ruangan</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ $roomId == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Tanggal --}}
                            <div>
                                <label class="block text-sm font-medium mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ $startDate }}"
                                    class="w-full rounded-md dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ $endDate }}"
                                    class="w-full rounded-md dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                            </div>

                            {{-- Tombol Action --}}
                            <div class="flex space-x-2">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-bold flex-1">
                                    Filter
                                </button>
                                {{-- Tombol Reset ke Bulan Ini --}}
                                <a href="{{ route('labs.history') }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-3 rounded-md text-sm flex items-center justify-center"
                                    title="Reset ke Bulan Ini">
                                    &#x21BB;
                                </a>
                            </div>
                        </div>

                        {{-- Tombol Export (Dinamis ikut filter) --}}
                        <div class="mt-4 flex justify-end space-x-2">
                            <a href="{{ route('labs.exportExcel', request()->all()) }}"
                                class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md text-sm font-bold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Excel
                            </a>
                            <a href="{{ route('labs.downloadPDF', request()->all()) }}" target="_blank"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md text-sm font-bold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                PDF
                            </a>
                        </div>
                    </form>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Ruangan</th>
                                    <th class="py-3 px-4">Guru & Kelas</th>
                                    <th class="py-3 px-4">Kegiatan</th>
                                    <th class="py-3 px-4">Waktu</th>
                                    <th class="py-3 px-4">Durasi</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-3 px-4">
                                            {{ \Carbon\Carbon::parse($log->usage_date)->isoFormat('D MMM YY') }}</td>
                                        <td class="py-3 px-4 font-bold">{{ $log->room->name }}</td>
                                        <td class="py-3 px-4">
                                            <div class="font-semibold">{{ $log->teacher->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->class_group }}</div>
                                        </td>
                                        <td class="py-3 px-4">{{ Str::limit($log->activity_description, 30) }}</td>
                                        <td class="py-3 px-4 text-xs">
                                            In: {{ $log->check_in_time->format('H:i') }}<br>
                                            Out: {{ $log->check_out_time ? $log->check_out_time->format('H:i') : '-' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($log->check_out_time)
                                                @php
                                                    $diff = $log->check_in_time->diffInMinutes($log->check_out_time);
                                                    $hours = floor($diff / 60);
                                                    $minutes = $diff % 60;
                                                @endphp
                                                {{ $hours }}j {{ $minutes }}m
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($log->check_out_time)
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Selesai</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs animate-pulse">Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada data riwayat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    <div class="mt-4">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
