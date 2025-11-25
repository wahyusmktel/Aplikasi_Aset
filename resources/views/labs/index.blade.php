<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Penggunaan Lab') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'usage' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 flex space-x-4 border-b border-gray-200 dark:border-gray-700">
                <button @click="activeTab = 'usage'"
                    :class="activeTab === 'usage' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-4 border-b-2 font-medium text-sm">
                    Jurnal Penggunaan (Hari Ini)
                </button>
                <button @click="activeTab = 'schedule'"
                    :class="activeTab === 'schedule' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-4 border-b-2 font-medium text-sm">
                    Kelola Jadwal Tetap
                </button>
            </div>

            <div x-show="activeTab === 'usage'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Catat Penggunaan
                                Baru</h3>
                            <form action="{{ route('labs.log.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ruangan
                                            Lab</label>
                                        <select name="room_id" class="select2 w-full mt-1" required>
                                            <option value="">Pilih Ruangan</option>
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Guru/PJ</label>
                                        <select name="teacher_id" class="select2 w-full mt-1" required>
                                            <option value="">Pilih Guru</option>
                                            @foreach (App\Models\Employee::orderBy('name')->get() as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kelas</label>
                                        <input type="text" name="class_group"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required
                                            placeholder="Contoh: XII RPL 1">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Materi/Kegiatan</label>
                                        <textarea name="activity_description" rows="2" class="mt-1 block w-full rounded-md dark:bg-gray-700" required></textarea>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi
                                            Awal</label>
                                        <input type="text" name="condition_before" value="Baik"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Check-In (Mulai)
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Aktivitas Lab Hari
                                Ini</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="py-3 px-4">Jam</th>
                                            <th class="py-3 px-4">Ruang</th>
                                            <th class="py-3 px-4">Guru & Kelas</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todaysLogs as $log)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="py-3 px-4">
                                                    {{ $log->check_in_time->format('H:i') }} -
                                                    {{ $log->check_out_time ? $log->check_out_time->format('H:i') : '...' }}
                                                </td>
                                                <td class="py-3 px-4 font-bold">{{ $log->room->name }}</td>
                                                <td class="py-3 px-4">
                                                    {{ $log->teacher->name }}<br>
                                                    <span class="text-xs text-gray-500">{{ $log->class_group }}</span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if ($log->check_out_time)
                                                        <span
                                                            class="px-2 py-1 bg-gray-200 text-gray-800 rounded text-xs">Selesai</span>
                                                    @else
                                                        <span
                                                            class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs animate-pulse">Sedang
                                                            Dipakai</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if (!$log->check_out_time)
                                                        <div x-data="{ open: false }">
                                                            <button @click="open = !open"
                                                                class="text-red-500 hover:underline text-xs font-bold">Check-Out</button>
                                                            <div x-show="open"
                                                                class="mt-2 p-2 border rounded bg-gray-50 dark:bg-gray-700 absolute z-10 w-64 shadow-lg">
                                                                <form
                                                                    action="{{ route('labs.log.checkout', $log->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <label class="text-xs block">Kondisi Akhir:</label>
                                                                    <input type="text" name="condition_after"
                                                                        value="Baik"
                                                                        class="w-full text-xs rounded mb-2 dark:bg-gray-600">
                                                                    <label class="text-xs block">Catatan:</label>
                                                                    <input type="text" name="notes"
                                                                        class="w-full text-xs rounded mb-2 dark:bg-gray-600">
                                                                    <button type="submit"
                                                                        class="w-full bg-red-500 text-white text-xs py-1 rounded">Selesai</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        {{-- Tombol Download BA Masuk --}}
                                                        @if ($log->checkin_doc_number)
                                                            <a href="{{ route('labs.log.downloadBast', ['log' => $log->id, 'type' => 'in']) }}"
                                                                target="_blank"
                                                                class="flex items-center text-blue-600 hover:underline text-xs font-bold mt-2">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                    </path>
                                                                </svg>
                                                                BA Masuk
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="text-green-500 text-xs block mb-1">&#10003;
                                                            Selesai</span>

                                                        {{-- Tombol Download BA Lengkap (Masuk & Keluar) --}}
                                                        <div class="flex flex-col gap-1">
                                                            @if ($log->checkin_doc_number)
                                                                <a href="{{ route('labs.log.downloadBast', ['log' => $log->id, 'type' => 'in']) }}"
                                                                    target="_blank"
                                                                    class="flex items-center text-blue-500 hover:text-blue-700 text-xs">
                                                                    BA Masuk
                                                                </a>
                                                            @endif
                                                            @if ($log->checkout_doc_number)
                                                                <a href="{{ route('labs.log.downloadBast', ['log' => $log->id, 'type' => 'out']) }}"
                                                                    target="_blank"
                                                                    class="flex items-center text-green-500 hover:text-green-700 text-xs">
                                                                    BA Keluar
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Belum ada aktivitas hari
                                                    ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'schedule'" x-transition style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Tambah Jadwal Tetap
                            </h3>
                            <form action="{{ route('labs.schedule.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hari</label>
                                        <select name="day_of_week"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700">
                                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                                <option value="{{ $day }}">{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mulai</label>
                                            <input type="time" name="start_time"
                                                class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selesai</label>
                                            <input type="time" name="end_time"
                                                class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ruangan</label>
                                        <select name="room_id" class="select2 w-full mt-1" required>
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mata
                                            Pelajaran</label>
                                        <input type="text" name="subject"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kelas</label>
                                        <input type="text" name="class_group"
                                            class="mt-1 block w-full rounded-md dark:bg-gray-700" required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Guru</label>
                                        <select name="teacher_id" class="select2 w-full mt-1" required>
                                            @foreach (App\Models\Employee::orderBy('name')->get() as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Simpan Jadwal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Jadwal Hari Ini
                                ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }})</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="py-3 px-4">Waktu</th>
                                            <th class="py-3 px-4">Ruang</th>
                                            <th class="py-3 px-4">Mapel & Kelas</th>
                                            <th class="py-3 px-4">Guru</th>
                                            <th class="py-3 px-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todaysSchedules as $sch)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="py-3 px-4">
                                                    {{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}</td>
                                                <td class="py-3 px-4 font-bold">{{ $sch->room->name }}</td>
                                                <td class="py-3 px-4">{{ $sch->subject }} <br> <span
                                                        class="text-xs text-gray-500">{{ $sch->class_group }}</span>
                                                </td>
                                                <td class="py-3 px-4">{{ $sch->teacher->name }}</td>
                                                <td class="py-3 px-4">
                                                    <form action="{{ route('labs.schedule.destroy', $sch->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus jadwal ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-500 hover:underline">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Tidak ada jadwal hari ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-gray-500 mt-4">* Untuk melihat jadwal hari lain, kita bisa buat
                                filter/halaman khusus nanti.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    theme: "tailwind", // Atau "classic" jika tema tailwind bermasalah
                    width: 'resolve'
                });
            });
        </script>
    @endpush
</x-app-layout>
