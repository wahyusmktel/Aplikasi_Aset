<x-app-layout>
    <div x-data="{ 
        activeTab: 'usage', 
        selectedDay: '{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }}' 
    }" class="space-y-8 pb-12">
        
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">{{ __('Manajemen Penggunaan Lab') }}</h2>
                    <p class="text-primary-100 opacity-90">Monitor dan catat aktivitas harian penggunaan laboratorium komputer.</p>
                </div>
                <!-- Navigation Tabs in Header -->
                <div class="flex p-1 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                    <button @click="activeTab = 'usage'"
                        :class="activeTab === 'usage' ? 'bg-white text-primary-700 shadow-lg' : 'text-white hover:bg-white/5'"
                        class="px-6 py-2 rounded-xl text-sm font-bold transition-all duration-300">
                        Jurnal Harian
                    </button>
                    <button @click="activeTab = 'schedule'"
                        :class="activeTab === 'schedule' ? 'bg-white text-primary-700 shadow-lg' : 'text-white hover:bg-white/5'"
                        class="px-6 py-2 rounded-xl text-sm font-bold transition-all duration-300">
                        Jadwal Tetap
                    </button>
                    <a href="{{ route('labs.history') }}" class="px-6 py-2 text-white hover:bg-white/5 rounded-xl text-sm font-bold transition-all duration-300">
                        Riwayat
                    </a>
                </div>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Jurnal Penggunaan Content -->
        <div x-show="activeTab === 'usage'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="animate-slideUp">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Check-in Form -->
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 sticky top-24">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl">
                                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Check-In Baru</h3>
                        </div>

                        <form action="{{ route('labs.log.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ruangan Lab</label>
                                <select name="room_id" class="select2 w-full" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Guru / Penanggung Jawab</label>
                                <select name="teacher_id" class="select2 w-full" required>
                                    <option value="">Pilih Guru</option>
                                    @foreach (App\Models\Employee::orderBy('name')->get() as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kelas</label>
                                <input type="text" name="class_group" required placeholder="Contoh: XII RPL 1"
                                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Materi / Kegiatan</label>
                                <textarea name="activity_description" rows="3" required placeholder="Ketik keterangan kegiatan..."
                                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kondisi Awal</label>
                                <input type="text" name="condition_before" value="Baik"
                                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200">
                            </div>

                            <button type="submit"
                                class="w-full py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14" /></svg>
                                <span>Mulai Penggunaan</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Today's Activity Table -->
                <div class="lg:col-span-8">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Aktivitas Hari Ini</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                            </div>
                            <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jam</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ruang</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Guru & Kelas</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    @forelse($todaysLogs as $log)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-all duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $log->check_in_time->format('H:i') }}</span>
                                                    <span class="text-xs text-gray-400">{{ $log->check_out_time ? $log->check_out_time->format('H:i') : 'Aktif' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-xs font-bold rounded-full">
                                                    {{ $log->room->name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $log->teacher->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $log->class_group }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($log->check_out_time)
                                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase rounded-lg">Selesai</span>
                                                @else
                                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-lg animate-pulse">Aktif</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    @if (!$log->check_out_time)
                                                        <button @click="confirmCheckout({{ $log->id }})"
                                                            class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold transition-all">Check-Out</button>
                                                    @endif
                                                    @if ($log->checkin_doc_number)
                                                         <a href="{{ route('labs.log.downloadBast', ['log' => $log->id, 'type' => 'in']) }}" target="_blank"
                                                            class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-all" title="BA Masuk">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                        </a>
                                                    @endif
                                                    @if ($log->checkout_doc_number)
                                                         <a href="{{ route('labs.log.downloadBast', ['log' => $log->id, 'type' => 'out']) }}" target="_blank"
                                                            class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-all" title="BA Keluar">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada aktivitas hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Tetap Content -->
        <div x-show="activeTab === 'schedule'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Add Schedule Form -->
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 sticky top-24">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-2xl">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Tambah Jadwal</h3>
                        </div>

                        <form action="{{ route('labs.schedule.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Hari</label>
                                <select name="day_of_week" class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-blue-500 transition-all dark:text-gray-200">
                                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mulai</label>
                                    <input type="time" name="start_time" required class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-blue-500 transition-all dark:text-gray-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Selesai</label>
                                    <input type="time" name="end_time" required class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-blue-500 transition-all dark:text-gray-200">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ruangan</label>
                                <select name="room_id" class="select2 w-full" required>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mata Pelajaran</label>
                                <input type="text" name="subject" required placeholder="Nama Mapel"
                                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-blue-500 transition-all dark:text-gray-200">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kelas</label>
                                <input type="text" name="class_group" required placeholder="X RPL 1"
                                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-blue-500 transition-all dark:text-gray-200">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Guru</label>
                                <select name="teacher_id" class="select2 w-full" required>
                                    @foreach (App\Models\Employee::orderBy('name')->get() as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                Simpan Jadwal
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Schedule Table -->
                <div class="lg:col-span-8">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col md:flex-row justify-between items-start md:items-center bg-blue-50/30 dark:bg-blue-900/10 gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Jadwal Tetap</h3>
                                <div class="flex items-center mt-1 space-x-2">
                                    <span class="text-xs text-blue-600 dark:text-blue-400 font-bold uppercase tracking-widest" x-text="selectedDay"></span>
                                    <span class="text-xs text-gray-400">|</span>
                                    <span class="text-xs text-gray-400 italic">Pilih hari untuk filter</span>
                                </div>
                            </div>
                            <div class="flex bg-white dark:bg-gray-800 p-1 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto max-w-full">
                                <template x-for="day in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']">
                                    <button @click="selectedDay = day"
                                        :class="selectedDay === day ? 'bg-blue-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
                                        x-text="day">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                             @php
                                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $allSchedules = [];
                                foreach($days as $day) {
                                    $allSchedules[$day] = App\Models\LabSchedule::with(['room', 'teacher'])
                                        ->where('day_of_week', $day)
                                        ->orderBy('start_time')
                                        ->get();
                                }
                            @endphp

                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ruang</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mapel & Kelas</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allSchedules as $dayName => $schedules)
                                        <template x-if="selectedDay === '{{ $dayName }}'">
                                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                                @forelse($schedules as $sch)
                                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-all duration-200">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="text-sm font-bold text-gray-800 dark:text-white">
                                                                {{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-[10px] font-bold rounded-full uppercase">
                                                                {{ $sch->room->name }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex flex-col">
                                                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $sch->subject }}</span>
                                                                <span class="text-xs text-gray-500">{{ $sch->class_group }} ({{ $sch->teacher->name }})</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <button @click="confirmDeleteSchedule({{ $sch->id }})"
                                                                class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada jadwal untuk hari ini.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </template>
                                    @endforeach
                                </tbody>
                            </table>
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
                theme: "tailwind",
                width: '100%'
            });
        });

        function confirmCheckout(id) {
            Swal.fire({
                title: 'Check-Out Lab',
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-500 font-medium">Konfirmasi kondisi akhir sebelum sesi berakhir.</p>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Kondisi Akhir</label>
                            <input id="condition_after" class="swal2-input !m-0 !w-full !rounded-2xl !bg-gray-50 !border-none !text-sm" value="Baik">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Catatan Tambahan</label>
                            <textarea id="notes" class="swal2-textarea !m-0 !w-full !rounded-2xl !bg-gray-50 !border-none !text-sm" placeholder="Ketik jika ada kerusakan/kejadian..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Selesai & Keluar',
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#0f172a',
                borderRadius: '1.5rem',
                customClass: {
                    confirmButton: 'rounded-2xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-2xl px-6 py-3 font-bold',
                    popup: 'rounded-3xl'
                },
                preConfirm: () => {
                    return {
                        condition_after: document.getElementById('condition_after').value,
                        notes: document.getElementById('notes').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/labs/log/${id}/checkout`;
                    form.method = 'POST';
                    form.innerHTML = `@csrf
                        <input type="hidden" name="condition_after" value="${result.value.condition_after}">
                        <input type="hidden" name="notes" value="${result.value.notes}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }

        function confirmDeleteSchedule(id) {
            Swal.fire({
                title: 'Hapus Jadwal?',
                text: "Data ini akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Ya, Hapus!',
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#0f172a',
                borderRadius: '1.5rem',
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/labs/schedule/${id}`;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
    <style>
        .select2-container--tailwind .select2-selection--single {
            @apply !bg-gray-50 !dark:bg-gray-800 !border-none !rounded-2xl !h-12 !flex !items-center !px-2;
        }
        .select2-container--tailwind .select2-selection__rendered {
            @apply !text-gray-700 !dark:text-gray-200 !text-sm;
        }
    </style>
    @endpush
</x-app-layout>
