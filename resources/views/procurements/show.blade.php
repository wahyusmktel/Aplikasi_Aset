<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('procurements.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Pengadaan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        showBastReceived: false, 
        showBastUnit: false, 
        showAssetConversion: false,
        activeAssetItem: {{ $procurement->items->where('is_converted_to_asset', false)->first()->id ?? 0 }}
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 pb-20">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Side: Main Detail -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Status & Basic Info -->
                    <div class="bg-white dark:bg-gray-950 rounded-3xl p-8 border border-gray-100 dark:border-gray-800 relative overflow-hidden">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-100 text-amber-600',
                                'received' => 'bg-blue-100 text-blue-600',
                                'unit_delivered' => 'bg-green-100 text-green-600',
                            ];
                        @endphp
                        <div class="inline-flex px-4 py-2 rounded-xl text-xs font-bold {{ $statusClasses[$procurement->status] ?? 'bg-gray-100' }} mb-6 uppercase tracking-widest">
                            Status: {{ $procurement->status }}
                        </div>
                        
                        <h1 class="text-4xl font-black text-gray-800 dark:text-white mb-2">{{ $procurement->vendor->name }}</h1>
                        <p class="text-gray-400 font-bold mb-8 uppercase tracking-tighter">{{ $procurement->reference_number }}</p>

                        <div class="grid grid-cols-2 gap-8 pt-8 border-t border-gray-50 dark:border-gray-800">
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Pengadaan</span>
                                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">{{ $procurement->procurement_date->format('d F Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Investasi</span>
                                <span class="text-xl font-black text-primary-600">Rp {{ number_format($procurement->total_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="bg-white dark:bg-gray-950 rounded-3xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
                        <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-wider">Item Barang</h3>
                            @if($procurement->status == 'received' || $procurement->status == 'unit_delivered')
                                @if($procurement->items->where('is_converted_to_asset', false)->count() > 0)
                                    <button @click="showAssetConversion = true" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-primary-500/30">
                                        Konversi ke Daftar Aset
                                    </button>
                                @else
                                    <span class="text-xs font-bold text-green-500 bg-green-50 px-4 py-2 rounded-xl">Sudah dikonversi</span>
                                @endif
                            @endif
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50/50 dark:bg-gray-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <tr>
                                        <th class="py-4 px-8">Nama Barang</th>
                                        <th class="py-4 px-6 text-center">Jumlah</th>
                                        <th class="py-4 px-6">Satuan (Rp)</th>
                                        <th class="py-4 px-8 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    @foreach($procurement->items as $item)
                                        <tr class="hover:bg-gray-50/20 dark:hover:bg-gray-800/10">
                                            <td class="py-5 px-8">
                                                <div class="font-bold text-gray-800 dark:text-gray-200">{{ $item->name }}</div>
                                                <div class="text-xs text-gray-400 font-medium">
                                                    {{ $item->category->name ?? 'Tanpa Kategori' }} • {{ $item->institution->name ?? 'Tanpa Pemilik' }}
                                                </div>
                                                <div class="text-[10px] text-primary-500 italic mt-1">{{ $item->specs ?? '' }}</div>
                                            </td>
                                            <td class="py-5 px-6 text-center font-black text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                                            <td class="py-5 px-6 font-bold text-gray-400">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="py-5 px-8 text-right font-black text-gray-800 dark:text-white">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Handovers & BAST -->
                <div class="space-y-8">
                    <!-- BAST History -->
                    <div class="bg-white dark:bg-gray-950 rounded-3xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-lg font-black text-gray-800 dark:text-white mb-8 uppercase tracking-wider">Dokumen BAST</h3>
                            
                            <div class="space-y-6">
                                <!-- Step 1: Vendor to School -->
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $procurement->status != 'pending' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400' }}">1</div>
                                        <div class="flex-1 w-0.5 bg-gray-100 dark:bg-gray-800 my-1"></div>
                                    </div>
                                    <div class="pb-6">
                                        <h4 class="font-bold text-sm mb-1 text-gray-800 dark:text-white">Terima dari Vendor</h4>
                                        @php
                                            $bastVendor = $procurement->handovers->where('type', 'vendor_to_school')->first();
                                        @endphp

                                        @if($bastVendor)
                                            <p class="text-[10px] text-gray-400 mb-2 uppercase tracking-tighter">{{ $bastVendor->document_number }}</p>
                                            <a href="{{ route('procurements.downloadBast', [$procurement, 'vendor_to_school']) }}" class="inline-flex items-center text-xs font-bold text-primary-600 hover:text-primary-700">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Unduh PDF
                                            </a>
                                        @else
                                            <button @click="showBastReceived = true" class="text-xs font-bold text-gray-400 hover:text-primary-600 transition-colors italic italicunderline decoration-dotted underline-offset-4 pointer-events-auto cursor-pointer">Belum Dibuat &rarr;</button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Step 2: School to Unit -->
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $procurement->status == 'unit_delivered' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400' }}">2</div>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm mb-1 text-gray-800 dark:text-white">Serah Terima ke Unit</h4>
                                        @php
                                            $bastUnit = $procurement->handovers->where('type', 'school_to_unit')->first();
                                        @endphp

                                        @if($bastUnit)
                                            <p class="text-[10px] text-gray-400 mb-2 uppercase tracking-tighter">{{ $bastUnit->document_number }}</p>
                                            <a href="{{ route('procurements.downloadBast', [$procurement, 'school_to_unit']) }}" class="inline-flex items-center text-xs font-bold text-primary-600 hover:text-primary-700">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Unduh PDF
                                            </a>
                                        @elseif($bastVendor)
                                            <button @click="showBastUnit = true" class="text-xs font-bold text-gray-400 hover:text-primary-600 transition-colors italic underline decoration-dotted underline-offset-4">Buat BAST Unit &rarr;</button>
                                        @else
                                            <span class="text-xs font-medium text-gray-300 italic">Menunggu Penerimaan...</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vendor Contact Box -->
                    <div class="bg-gradient-to-br from-gray-900 to-black rounded-3xl p-8 text-white shadow-xl">
                        <h3 class="text-xs font-bold text-primary-400 uppercase tracking-widest mb-4">Informasi Vendor</h3>
                        <div class="font-black text-xl mb-4">{{ $procurement->vendor->name }}</div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span class="text-sm font-medium text-gray-400">{{ $procurement->vendor->address ?? 'Alamat tidak tersedia' }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span class="text-sm font-medium text-gray-400">{{ $procurement->vendor->phone ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Convert to Assets -->
        <div x-show="showAssetConversion" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showAssetConversion = false"></div>
                
                <div class="relative bg-white dark:bg-gray-950 rounded-[40px] shadow-2xl w-full max-w-6xl overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('procurements.convertToAssets', $procurement) }}" method="POST">
                        @csrf
                        
                        <div class="flex flex-col h-[85vh]">
                            <!-- Header -->
                            <div class="p-10 border-b border-gray-50 dark:border-gray-900 flex items-center justify-between shrink-0 bg-white/50 dark:bg-gray-950/50 backdrop-blur-md">
                                <div>
                                    <h2 class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">Konversi ke Daftar Aset</h2>
                                    <p class="text-sm text-gray-400 mt-1">Lengkapi detail penempatan dan PIC untuk setiap jenis barang.</p>
                                </div>
                                <button type="button" @click="showAssetConversion = false" class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-400 hover:text-gray-600 rounded-2xl transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <div class="flex flex-1 overflow-hidden bg-white dark:bg-gray-950">
                                <!-- Sidebar Tabs -->
                                <div class="w-80 bg-gray-50/50 dark:bg-gray-900/30 border-r border-gray-50 dark:border-gray-900 overflow-y-auto p-6 space-y-3 shrink-0">
                                    <div class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-4 px-2">Daftar Barang</div>
                                    @foreach($procurement->items->where('is_converted_to_asset', false) as $item)
                                        <button type="button" 
                                            @click="activeAssetItem = {{ $item->id }}"
                                            :class="activeAssetItem === {{ $item->id }} ? 'bg-primary-600 text-white shadow-2xl shadow-primary-500/40 translate-x-1' : 'text-gray-500 hover:bg-white dark:hover:bg-gray-800 hover:text-gray-700'"
                                            class="w-full flex flex-col items-start p-5 rounded-[24px] transition-all text-left group">
                                            <span class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1 group-hover:opacity-100 transition-opacity">Item {{ $loop->iteration }}</span>
                                            <span class="font-bold text-sm truncate w-full">{{ $item->name }}</span>
                                            <div class="mt-3 flex items-center gap-2">
                                                <span class="text-[9px] font-black bg-black/10 dark:bg-white/10 px-2.5 py-1 rounded-full">{{ $item->quantity }} Unit</span>
                                                <span class="text-[9px] font-bold opacity-60 italic">{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Tab Content -->
                                <div class="flex-1 overflow-y-auto p-12 custom-scrollbar">
                                    @foreach($procurement->items->where('is_converted_to_asset', false) as $item)
                                        <div x-show="activeAssetItem === {{ $item->id }}" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="space-y-10">
                                            
                                            <div class="flex items-center justify-between pb-8 border-b border-gray-100 dark:border-gray-800">
                                                <div>
                                                    <h3 class="text-2xl font-black text-gray-800 dark:text-white">{{ $item->name }}</h3>
                                                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">Pengaturan Spesifik Aset</p>
                                                </div>
                                                
                                                <button type="button" 
                                                    class="text-[10px] font-black text-primary-600 hover:text-white hover:bg-primary-600 flex items-center bg-primary-50 dark:bg-primary-900/20 px-5 py-3 rounded-2xl transition-all border border-primary-100 dark:border-primary-800 group"
                                                    onclick="copySettingsToAll({{ $item->id }})">
                                                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0V9m0-2L14 5m4 4l-2 2M10 17H6m0 0v-2m0 2l2 2m-2-2l2-2" /></svg>
                                                    Terapkan ke Semua Item Lain
                                                </button>
                                            </div>

                                            <div class="bg-amber-50 dark:bg-amber-900/20 p-6 rounded-2xl mb-8 border border-amber-100 dark:border-amber-800/50">
                                                <div class="flex items-start">
                                                    <svg class="w-6 h-6 text-amber-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <div>
                                                        <p class="text-sm font-bold text-amber-800 dark:text-amber-400 mb-1">Penting!</p>
                                                        <p class="text-xs text-amber-700 dark:text-amber-500">Tentukan lokasi dan kepemilikan aset di bawah ini dengan benar.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                                <div class="space-y-8">
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Lokasi Gedung</label>
                                                        <select name="items[{{ $item->id }}][building_id]" required id="building_{{ $item->id }}" class="setting-building w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih Gedung</option>
                                                            @foreach($buildings as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Ruangan / Lab</label>
                                                        <select name="items[{{ $item->id }}][room_id]" required id="room_{{ $item->id }}" class="setting-room w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih Ruangan</option>
                                                            @foreach($rooms as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Fakultas / Direktorat</label>
                                                        <select name="items[{{ $item->id }}][faculty_id]" required id="faculty_{{ $item->id }}" class="setting-faculty w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih Fakultas</option>
                                                            @foreach($faculties as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Prodi / Unit Kerja</label>
                                                        <select name="items[{{ $item->id }}][department_id]" required id="department_{{ $item->id }}" class="setting-department w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih Unit</option>
                                                            @foreach($departments as $opt)
                                                                <option value="{{ $opt->id }}" @selected(isset($bastUnit) && $bastUnit->to_department_id == $opt->id)>{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="space-y-8">
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Penanggung Jawab (PIC)</label>
                                                        <select name="items[{{ $item->id }}][person_in_charge_id]" required id="pic_{{ $item->id }}" class="setting-pic w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih PIC</option>
                                                            @foreach($personsInCharge as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Fungsi Barang</label>
                                                        <select name="items[{{ $item->id }}][asset_function_id]" required id="function_{{ $item->id }}" class="setting-function w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih Fungsi</option>
                                                            @foreach($assetFunctions as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Sumber Pendanaan</label>
                                                        <select name="items[{{ $item->id }}][funding_source_id]" required id="funding_{{ $item->id }}" class="setting-funding w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                                                            <option value="">Pilih Sumber Dana</option>
                                                            @foreach($fundingSources as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="p-10 border-t border-gray-50 dark:border-gray-900 bg-gray-50/50 dark:bg-gray-950/50 backdrop-blur-md flex items-center justify-between shrink-0">
                                <div class="flex items-center text-amber-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Pastikan data setiap item sudah benar</span>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" @click="showAssetConversion = false" class="px-8 py-4 font-bold text-gray-400 hover:text-gray-600 transition-colors">Batal</button>
                                    <button type="submit" class="px-12 py-5 bg-primary-600 hover:bg-primary-700 text-white font-black rounded-3xl shadow-2xl shadow-primary-500/40 transition-all transform hover:-translate-y-1 active:scale-95">
                                        Konfirmasi & Buat Aset Massal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function copySettingsToAll(sourceId) {
                if(!confirm('Terapkan pengaturan item ini ke semua item lainnya?')) return;
                
                const building = document.getElementById('building_' + sourceId).value;
                const room = document.getElementById('room_' + sourceId).value;
                const faculty = document.getElementById('faculty_' + sourceId).value;
                const department = document.getElementById('department_' + sourceId).value;
                const pic = document.getElementById('pic_' + sourceId).value;
                const func = document.getElementById('function_' + sourceId).value;
                const funding = document.getElementById('funding_' + sourceId).value;

                document.querySelectorAll('.setting-building').forEach(el => el.value = building);
                document.querySelectorAll('.setting-room').forEach(el => el.value = room);
                document.querySelectorAll('.setting-faculty').forEach(el => el.value = faculty);
                document.querySelectorAll('.setting-department').forEach(el => el.value = department);
                document.querySelectorAll('.setting-pic').forEach(el => el.value = pic);
                document.querySelectorAll('.setting-function').forEach(el => el.value = func);
                document.querySelectorAll('.setting-funding').forEach(el => el.value = funding);
            }
        </script>

        <!-- Modal: BAST Received from Vendor -->
        <div x-show="showBastReceived" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBastReceived = false"></div>
                
                <div class="relative bg-white dark:bg-gray-950 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('procurements.received', $procurement) }}" method="POST" class="p-10">
                        @csrf
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-6">Penerimaan Barang</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">No. BAST Vendor</label>
                                <input type="text" name="document_number" required placeholder="Contoh: BAST/{{ date('Y') }}/001"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Terima</label>
                                <input type="date" name="handover_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Penyerah (Vendor)</label>
                                <input type="text" name="from_name" required x-bind:value="'{{ $procurement->vendor->contact_person }}'"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="flex justify-end pt-8 space-x-4">
                            <button type="button" @click="showBastReceived = false" class="px-6 py-3 font-bold text-gray-500 hover:text-gray-700">Batal</button>
                            <button type="submit" class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 transition-all">
                                Proses Penerimaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: BAST Handover to Unit -->
        <div x-show="showBastUnit" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBastUnit = false"></div>
                
                <div class="relative bg-white dark:bg-gray-950 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                    <form action="{{ route('procurements.handoverUnit', $procurement) }}" method="POST" class="p-10">
                        @csrf
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-6">Penyerahan ke Unit</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">No. BAST Unit</label>
                                <input type="text" name="document_number" required placeholder="Contoh: BAST-UNIT/{{ date('Y') }}/001"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Serah</label>
                                <input type="date" name="handover_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Penanggung Jawab Penerima</label>
                                <select name="to_person_in_charge_id" required class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                                    <option value="">Pilih Penanggung Jawab</option>
                                    @foreach($personsInCharge as $pic)
                                        <option value="{{ $pic->id }}">{{ $pic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Penerima (Unit)</label>
                                <input type="text" name="to_name" required
                                    class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="flex justify-end pt-8 space-x-4">
                            <button type="button" @click="showBastUnit = false" class="px-6 py-3 font-bold text-gray-500 hover:text-gray-700">Batal</button>
                            <button type="submit" class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 transition-all">
                                Proses Penyerahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
