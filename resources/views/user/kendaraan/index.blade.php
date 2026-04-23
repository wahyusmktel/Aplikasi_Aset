<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">Peminjaman Kendaraan Dinas</h2>
            <p class="text-sm text-gray-400 mt-1">Ajukan penggunaan operasional kendaraan sekolah dan pantau riwayat perjalanan Anda.</p>
        </div>
    </x-slot>

    <div x-data="{ 
        checkoutModalOpen: false, 
        returnModalOpen: false, 
        isCheckoutFullscreen: false, 
        isReturnFullscreen: false, 
        selectedVehicle: null, 
        selectedLog: null, 
        driverType: 'self',
        init() {
            this.$watch('checkoutModalOpen', value => {
                if(value) {
                    setTimeout(() => {
                        if (typeof google !== 'undefined') {
                            if (!window.map && document.getElementById('map')) {
                                initMap();
                            } else if (window.map) {
                                google.maps.event.trigger(window.map, 'resize');
                                window.map.setCenter(window.marker.getPosition());
                            }
                        }
                    }, 300);
                }
            });

            @if(request()->has('asset_id'))
                setTimeout(() => {
                    const btn = document.querySelector(`button[data-asset-id="{{ request('asset_id') }}"]`);
                    if(btn) {
                        btn.click();
                    }
                }, 500);
            @endif
        }
    }">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ s: true }" x-show="s" x-init="setTimeout(() => s = false, 5000)" class="mb-6 flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-800">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
        @endif

        {{-- Daftar Kendaraan --}}
        <div class="mb-8">
            <h3 class="text-lg font-black text-gray-800 dark:text-white mb-4">Kendaraan Tersedia</h3>
            @if($vehicles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($vehicles as $vehicle)
                <div class="group bg-white dark:bg-gray-900 rounded-3xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl hover:border-red-200 dark:hover:border-red-900/50 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/10 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                    
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center text-red-600 dark:text-red-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            @if($vehicle->current_status == 'Tersedia')
                                <span class="inline-flex px-3 py-1.5 text-[10px] font-black rounded-xl bg-emerald-100 text-emerald-700 uppercase tracking-widest shadow-sm">Tersedia</span>
                            @else
                                <span class="inline-flex px-3 py-1.5 text-[10px] font-black rounded-xl bg-amber-100 text-amber-700 uppercase tracking-widest shadow-sm">Digunakan</span>
                            @endif
                        </div>
                        
                        <h4 class="text-lg font-black text-gray-800 dark:text-white mb-1">{{ $vehicle->name }}</h4>
                        <p class="text-xs font-bold text-red-500 mb-4">{{ $vehicle->asset_code_ypt }}</p>
                        
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate">{{ $vehicle->room->name ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">KM Terakhir</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ number_format($vehicle->vehicleLogs()->latest()->first()?->end_odometer ?? 0, 0, ',', '.') }} KM</p>
                            </div>
                        </div>

                        @if($vehicle->current_status == 'Tersedia')
                        <button data-asset-id="{{ $vehicle->id }}" @click="checkoutModalOpen = true; selectedVehicle = { id: {{ $vehicle->id }}, name: `{{ addslashes(str_replace('`', '', $vehicle->name)) }}`, odometer: {{ $vehicle->vehicleLogs()->latest()->first()?->end_odometer ?? 0 }} }" 
                            class="w-full py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-xl shadow-lg shadow-red-500/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Mulai Perjalanan
                        </button>
                        @else
                        <button disabled class="w-full py-3 bg-gray-100 dark:bg-gray-800 text-gray-400 text-sm font-black rounded-xl cursor-not-allowed">
                            Sedang Dipakai
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-10 text-center border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Tidak ada kendaraan dinas</h4>
                <p class="text-gray-400 text-sm">Belum ada data kendaraan dinas yang aktif di sistem.</p>
            </div>
            @endif
        </div>

        {{-- Riwayat Penggunaan --}}
        <div>
            <h3 class="text-lg font-black text-gray-800 dark:text-white mb-4">Riwayat Perjalanan Saya</h3>
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                @if($myLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Kendaraan & Tujuan</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Waktu</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Pengemudi</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($myLogs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $log->asset->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $log->destination }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        <span class="text-emerald-600">↑</span> {{ $log->departure_time->format('d M Y, H:i') }}
                                    </p>
                                    @if($log->return_time)
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">
                                        <span class="text-blue-600">↓</span> {{ $log->return_time->format('d M Y, H:i') }}
                                    </p>
                                    @else
                                    <p class="text-[10px] font-bold text-amber-500 mt-1">Est. {{ $log->estimated_return_time?->format('d M, H:i') ?? '-' }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->driver_type == 'school_driver')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-100 text-purple-700 text-[10px] font-black uppercase tracking-wider">
                                            Driver: {{ $log->driverEmployee->name ?? '-' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-black uppercase tracking-wider">
                                            Menyetir Sendiri
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->return_time)
                                        <span class="inline-flex px-3 py-1 text-[10px] font-black rounded-lg bg-emerald-100 text-emerald-700 uppercase tracking-widest">Selesai</span>
                                    @elseif($log->status === 'pengajuan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black rounded-lg bg-yellow-100 text-yellow-700 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-ping"></span>
                                            Menunggu Waka/Kaur
                                        </span>
                                    @elseif($log->status === 'menunggu_kepsek')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black rounded-lg bg-blue-100 text-blue-700 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span>
                                            Menunggu Kepsek
                                        </span>
                                    @elseif($log->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black rounded-lg bg-amber-100 text-amber-700 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            Sedang Jalan
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-[10px] font-black rounded-lg bg-gray-100 text-gray-500 uppercase tracking-widest">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- BAST Keluar hanya bisa di-download jika sudah disetujui semua --}}
                                        @if($log->checkout_doc_number && $log->status === 'disetujui')
                                        <a href="{{ route('user.kendaraan.downloadBast', [$log->id, 'checkout']) }}" title="Download BAST Keluar"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                        @endif
                                        
                                        @if(!$log->return_time)
                                            @if($log->status === 'disetujui')
                                            <button @click="returnModalOpen = true; selectedLog = { id: {{ $log->id }}, vehicle: `{{ addslashes(str_replace('`', '', $log->asset->name ?? '')) }}`, destination: `{{ addslashes(str_replace('`', '', $log->destination)) }}`, odometer: {{ $log->start_odometer }} }"
                                                class="px-4 py-2 bg-gray-900 dark:bg-gray-100 hover:bg-gray-800 dark:hover:bg-white text-white dark:text-gray-900 text-xs font-black rounded-xl shadow-md transition-all">
                                                Kembalikan
                                            </button>
                                            @else
                                            <span class="text-[10px] text-gray-400 font-bold uppercase italic border border-gray-200 px-2 py-1 rounded-lg">Menunggu Approval</span>
                                            @endif
                                        @else
                                            @if($log->checkin_doc_number)
                                            <a href="{{ route('user.kendaraan.downloadBast', [$log->id, 'checkin']) }}" title="Download BAST Kembali"
                                                class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-xl transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $myLogs->links() }}
                </div>
                @else
                <div class="p-10 text-center">
                    <p class="text-gray-400 text-sm">Belum ada riwayat penggunaan kendaraan dinas.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Modal: Checkout Kendaraan --}}
        <template x-teleport="body">
            <div x-show="checkoutModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-8 bg-gray-900/60 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div :class="isCheckoutFullscreen ? 'w-full h-full max-w-none rounded-none' : 'w-full max-w-4xl max-h-[85vh] rounded-[2rem]'" 
                 class="bg-white dark:bg-gray-900 shadow-2xl overflow-y-auto relative transition-all duration-300 flex flex-col">
                 
                <div class="sticky top-0 z-10 flex justify-end p-4 pointer-events-none">
                    <button type="button" @click="isCheckoutFullscreen = !isCheckoutFullscreen" class="pointer-events-auto p-2 text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 bg-gray-100/80 dark:bg-gray-800/80 backdrop-blur-md rounded-xl transition-colors">
                        <svg x-show="!isCheckoutFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <svg x-show="isCheckoutFullscreen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4M4 14l5 5m11-5h-4v4m4-4l-5 5M4 10H8V6M4 10L9 5m11 5h-4V6m4 4l-5-5"/></svg>
                    </button>
                </div>
                <form action="{{ route('user.kendaraan.store') }}" method="POST" class="p-6 sm:p-10 flex-1 -mt-12">
                    @csrf
                    <input type="hidden" name="asset_id" :value="selectedVehicle?.id">
                    
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Form Penggunaan Kendaraan</h2>
                            <p class="text-sm text-gray-500 mt-1" x-text="selectedVehicle?.name"></p>
                        </div>
                        <div class="w-12 h-12 bg-red-50 dark:bg-red-900/30 rounded-2xl flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Data Peminjam (Readonly) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-800/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Peminjam</label>
                                <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none text-gray-500 text-sm font-semibold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">NIP Peminjam</label>
                                <input type="text" value="{{ Auth::user()->nip ?? '-' }}" readonly class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none text-gray-500 text-sm font-semibold cursor-not-allowed">
                            </div>
                        </div>

                        {{-- Perjalanan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Tujuan Perjalanan</label>
                                <input type="text" name="destination" required placeholder="Contoh: Kunjungan Dinas ke Diknas" class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Detail Keperluan</label>
                                <textarea name="purpose" rows="2" required placeholder="Jelaskan detail keperluan meminjam..." class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"></textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Tanggal & Jam Keluar</label>
                                <input type="datetime-local" name="departure_time" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Estimasi Kembali</label>
                                <input type="datetime-local" name="estimated_return_time" required value="{{ now()->addHours(4)->format('Y-m-d\TH:i') }}" class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-bold">
                            </div>
                        </div>

                        {{-- Pengemudi & Teknis --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Status Pengemudi</label>
                                <select name="driver_type" x-model="driverType" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-semibold">
                                    <option value="self">Menyetir Sendiri</option>
                                    <option value="school_driver">Butuh Driver Sekolah</option>
                                </select>
                            </div>
                            <div x-show="driverType === 'school_driver'">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Pilih Driver</label>
                                <select name="driver_employee_id" :required="driverType === 'school_driver'" class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                    <option value="">-- Pilih Driver --</option>
                                    @foreach($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->name }} ({{ $e->position }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="driverType === 'self'" class="hidden md:block"></div> {{-- Spacer --}}

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Kilometer Awal</label>
                                <div class="relative">
                                    <input type="number" name="start_odometer" :value="selectedVehicle?.odometer" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-black text-lg">
                                    <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">KM</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Kondisi BBM Awal</label>
                                <select name="fuel_level_start" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-semibold">
                                    <option value="Full">Full (Penuh)</option>
                                    <option value="3/4">3/4 Tangki</option>
                                    <option value="1/2">1/2 Tangki</option>
                                    <option value="1/4">1/4 Tangki</option>
                                    <option value="Hampir Habis">Hampir Habis</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Kondisi Fisik Awal</label>
                                <select name="condition_on_checkout" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-semibold">
                                    <option value="Baik">Baik / Mulus</option>
                                    <option value="Ada Lecet">Ada Lecet Sebelumnya</option>
                                    <option value="Rusak">Rusak (Catat di tujuan/keperluan)</option>
                                </select>
                            </div>

                            {{-- Google Maps Koordinat Awal --}}
                            <div class="md:col-span-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Lokasi Awal Kendaraan</label>
                                <p class="text-xs text-gray-400 mb-3 px-1">Titik koordinat saat serah terima kunci (otomatis mendeteksi lokasi saat ini, bisa digeser jika kurang akurat).</p>
                                
                                <div id="map" class="w-full h-56 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner z-0 relative overflow-hidden mb-3"></div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[10px] font-black text-gray-400 uppercase">Lat:</span>
                                        <input type="text" name="start_latitude" id="start_latitude" readonly required class="w-full pl-10 pr-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-xs font-mono font-semibold text-gray-600 dark:text-gray-300 focus:ring-0 cursor-not-allowed">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[10px] font-black text-gray-400 uppercase">Lng:</span>
                                        <input type="text" name="start_longitude" id="start_longitude" readonly required class="w-full pl-10 pr-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-xs font-mono font-semibold text-gray-600 dark:text-gray-300 focus:ring-0 cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-10">
                        <button type="button" @click="checkoutModalOpen = false" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">Batal</button>
                        <button type="submit" class="px-8 py-3 bg-red-600 text-white font-black rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-700 transition-all">Simpan & Mulai</button>
                    </div>
                </form>
            </div>
            </div>
        </template>

        {{-- Modal: Return Kendaraan --}}
        <template x-teleport="body">
            <div x-show="returnModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-8 bg-gray-900/60 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div :class="isReturnFullscreen ? 'w-full h-full max-w-none rounded-none' : 'w-full max-w-4xl max-h-[85vh] rounded-[2rem]'" 
                 class="bg-white dark:bg-gray-900 shadow-2xl overflow-y-auto relative transition-all duration-300 flex flex-col">
                 
                <div class="sticky top-0 z-10 flex justify-end p-4 pointer-events-none">
                    <button type="button" @click="isReturnFullscreen = !isReturnFullscreen" class="pointer-events-auto p-2 text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 bg-gray-100/80 dark:bg-gray-800/80 backdrop-blur-md rounded-xl transition-colors">
                        <svg x-show="!isReturnFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <svg x-show="isReturnFullscreen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4M4 14l5 5m11-5h-4v4m4-4l-5 5M4 10H8V6M4 10L9 5m11 5h-4V6m4 4l-5-5"/></svg>
                    </button>
                </div>
                <form :action="selectedLog ? `{{ url('peminjaman-kendaraan') }}/${selectedLog.id}/checkin` : '#'" method="POST" enctype="multipart/form-data" class="p-6 sm:p-10 flex-1 -mt-12">
                    @csrf
                    
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Form Pengembalian</h2>
                            <p class="text-sm text-gray-500 mt-1" x-text="selectedLog?.vehicle"></p>
                        </div>
                        <div class="w-12 h-12 bg-gray-900 dark:bg-white rounded-2xl flex items-center justify-center text-white dark:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Data Info (Readonly) --}}
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-800 text-sm">
                            <div class="grid grid-cols-2 gap-y-3">
                                <div><span class="text-gray-400 font-semibold block text-[10px] uppercase tracking-wider">Nama</span><span class="font-bold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span></div>
                                <div><span class="text-gray-400 font-semibold block text-[10px] uppercase tracking-wider">Tujuan</span><span class="font-bold text-gray-700 dark:text-gray-200" x-text="selectedLog?.destination"></span></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Tanggal & Jam Aktual Kembali</label>
                                <input type="datetime-local" name="return_time" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-all font-bold">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Kilometer Akhir (Min: <span x-text="selectedLog?.odometer"></span>)</label>
                                <div class="relative">
                                    <input type="number" name="end_odometer" :min="selectedLog?.odometer" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-all font-black text-lg">
                                    <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">KM</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Kondisi BBM Akhir</label>
                                <select name="fuel_level_end" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-all font-semibold">
                                    <option value="Full">Full (Penuh)</option>
                                    <option value="3/4">3/4 Tangki</option>
                                    <option value="1/2">1/2 Tangki</option>
                                    <option value="1/4">1/4 Tangki</option>
                                    <option value="Hampir Habis">Hampir Habis</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Kondisi Fisik Kendaraan</label>
                                <select name="condition_on_checkin" required class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-all font-semibold">
                                    <option value="Aman">Aman / Mulus</option>
                                    <option value="Ada Lecet">Ada Lecet / Goresan Baru</option>
                                    <option value="Rusak">Rusak (Jelaskan di catatan)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Catatan Tambahan (Keluhan/Saran)</label>
                                <textarea name="notes" rows="2" placeholder="Contoh: Ban depan kiri kurang angin, atau AC kurang dingin..." class="w-full px-5 py-3.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-all"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 px-1">Bukti Foto Kendaraan</label>
                                <input type="file" name="return_photos[]" multiple accept="image/*" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 transition-all">
                                <p class="text-[10px] text-gray-400 mt-1 pl-1">Bisa upload lebih dari satu foto. Format: JPG/PNG, Maks 4MB/foto.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-10">
                        <button type="button" @click="returnModalOpen = false" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">Batal</button>
                        <button type="submit" class="px-8 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black rounded-xl shadow-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-all">Selesaikan Pengembalian</button>
                    </div>
                </form>
            </div>
            </div>
        </template>

    </div>

    @push('scripts')
    <script>
        window.map = null;
        window.marker = null;

        function initMap() {
            // Default ke Jakarta jika tidak ada lokasi
            const defaultLoc = { lat: -6.2088, lng: 106.8456 };
            
            window.map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: defaultLoc,
                mapTypeControl: false,
                streetViewControl: false,
            });
            
            window.marker = new google.maps.Marker({
                position: defaultLoc,
                map: window.map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });
            
            // Set default hidden values
            document.getElementById('start_latitude').value = defaultLoc.lat;
            document.getElementById('start_longitude').value = defaultLoc.lng;

            // Update input hidden saat marker digeser
            google.maps.event.addListener(window.marker, 'dragend', function (evt) {
                document.getElementById('start_latitude').value = evt.latLng.lat();
                document.getElementById('start_longitude').value = evt.latLng.lng();
            });

            // Coba ambil lokasi saat ini
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        window.map.setCenter(pos);
                        window.marker.setPosition(pos);
                        
                        document.getElementById('start_latitude').value = pos.lat;
                        document.getElementById('start_longitude').value = pos.lng;
                    },
                    () => {
                        console.log("Error: The Geolocation service failed.");
                    }
                );
            }
        } // Closes initMap

        window.mapsReady = function() {
            // Callback kosong, inisialisasi dipanggil saat modal terbuka
        };
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=mapsReady" async defer></script>
    @endpush
</x-app-layout>
