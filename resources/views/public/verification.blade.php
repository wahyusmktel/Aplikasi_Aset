<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Dokumen - {{ config('app.name', 'Aplikasi Aset') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen relative flex flex-col justify-center py-8 sm:py-12">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute top-40 -left-40 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl"></div>
        </div>

        <div class="relative max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl overflow-hidden shadow-2xl sm:rounded-3xl border border-white/20 dark:border-gray-700/50 transition-all duration-300 hover:shadow-emerald-500/10">
                
                <!-- Header Status -->
                <div class="bg-emerald-500/10 dark:bg-emerald-500/20 px-8 py-10 text-center border-b border-emerald-100 dark:border-emerald-800/50">
                    <div class="inline-flex justify-center items-center w-24 h-24 rounded-full bg-emerald-100 dark:bg-emerald-900/50 mb-6 shadow-inner relative">
                        <div class="absolute inset-0 rounded-full border-4 border-emerald-500 animate-pulse opacity-50"></div>
                        <svg class="w-12 h-12 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight mb-2">Dokumen Valid & Resmi</h2>
                    <p class="text-emerald-700 dark:text-emerald-300 font-medium">Dokumen ini telah terverifikasi secara digital oleh sistem.</p>
                </div>

                <!-- Content Body -->
                <div class="p-8 sm:p-10">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 border-l-4 border-primary-500 pl-4">{{ $documentType }}</h3>
                        <span class="px-4 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-full uppercase tracking-widest whitespace-nowrap">Detail Dokumen</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        {{-- Data Items as Cards --}}
                        <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nomor Surat</p>
                            <p class="text-sm font-mono font-semibold text-gray-800 dark:text-gray-200 break-all">{{ $docNumber }}</p>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                                @if (isset($labLog)) Ruangan @else Nama Aset @endif
                            </p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $assetName }}</p>
                        </div>

                        <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                                @if (isset($inspection)) Pemeriksa
                                @elseif(isset($vehicleLog)) Pengguna
                                @elseif(isset($labLog)) Guru / PJ
                                @elseif(isset($assignment)) Pegawai
                                @elseif(isset($asset)) Penanggung Jawab
                                @else Pihak Terlibat @endif
                            </p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $employeeName }}</p>
                        </div>

                        <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tanggal Transaksi</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($transactionDate)->isoFormat('D MMMM YYYY') }}</p>
                        </div>

                        {{-- DETAIL KHUSUS: LAB --}}
                        @if (isset($labLog))
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kelas</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $labLog->class_group }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kegiatan</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $labLog->activity_description }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Waktu {{ $isReturn ? 'Selesai' : 'Masuk' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $isReturn ? $labLog->check_out_time->format('H:i') : $labLog->check_in_time->format('H:i') }} WIB</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kondisi {{ $isReturn ? 'Akhir' : 'Awal' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $isReturn ? $labLog->condition_after : $labLog->condition_before }}</p>
                            </div>

                        {{-- DETAIL KHUSUS: KENDARAAN --}}
                        @elseif(isset($vehicleLog))
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md md:col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tujuan Perjalanan</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $vehicleLog->destination }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md md:col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Keperluan / Agenda</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $vehicleLog->purpose }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Pengemudi</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $vehicleLog->driver_type == 'school_driver' ? 'Driver Sekolah (' . ($vehicleLog->driverEmployee->name ?? '-') . ')' : 'Menyetir Sendiri' }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Waktu {{ $isReturn ? 'Kembali' : 'Berangkat' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ ($isReturn ? $vehicleLog->return_time : $vehicleLog->departure_time)->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kilometer {{ $isReturn ? 'Akhir' : 'Awal' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ number_format($isReturn ? $vehicleLog->end_odometer : $vehicleLog->start_odometer) }} KM</p>
                            </div>
                            @if ($isReturn && $vehicleLog->end_odometer)
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Jarak Tempuh</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ number_format($vehicleLog->end_odometer - $vehicleLog->start_odometer) }} KM</p>
                            </div>
                            @endif
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">BBM {{ $isReturn ? 'Akhir' : 'Awal' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $isReturn ? $vehicleLog->fuel_level_end : $vehicleLog->fuel_level_start }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kondisi Fisik {{ $isReturn ? 'Akhir' : 'Awal' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $isReturn ? $vehicleLog->condition_on_checkin : $vehicleLog->condition_on_checkout }}</p>
                            </div>
                            @if (!$isReturn && $vehicleLog->start_latitude)
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md md:col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Koordinat Awal Peminjaman</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">Lat: {{ $vehicleLog->start_latitude }}, Lng: {{ $vehicleLog->start_longitude }}</p>
                            </div>
                            @endif
                            @if ($isReturn && $vehicleLog->notes)
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md md:col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Catatan Tambahan</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $vehicleLog->notes }}</p>
                            </div>
                            @endif

                        {{-- DETAIL KHUSUS: INSPEKSI --}}
                        @elseif(isset($inspection))
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md md:col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kondisi Tercatat</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $inspection->condition }}</p>
                            </div>

                        {{-- DETAIL KHUSUS: PEMINJAMAN ASET BIASA --}}
                        @elseif(isset($assignment))
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kondisi {{ $isReturn ? 'Kembali' : 'Pinjam' }}</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $isReturn ? $assignment->condition_on_return : $assignment->condition_on_assign }}</p>
                            </div>

                        {{-- DETAIL KHUSUS: DISPOSAL --}}
                        @elseif(isset($asset))
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Metode Disposal</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $asset->disposal_method }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Alasan Disposal</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $asset->disposal_reason }}</p>
                            </div>
                            @if ($asset->disposal_method == 'Dijual')
                                <div class="bg-white dark:bg-gray-900/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm transition-all hover:shadow-md">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nilai Jual</p>
                                    <p class="text-sm font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($asset->disposal_value ?? 0, 0, ',', '.') }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Custom Credit Footer -->
            <div class="text-center mt-8 pb-8 text-sm text-gray-500 font-medium">
                Dibuat dengan hati <span class="text-rose-500 mx-1">❤</span> untuk pendidikan indonesia oleh <span class="font-bold text-gray-700 dark:text-gray-300">IT Tim Development SMK Telkom Lampung</span>
            </div>
        </div>
    </div>
</body>
</html>
