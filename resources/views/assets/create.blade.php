<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('assets.index') }}" class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 transition-all shadow-sm group">
                <svg class="w-6 h-6 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h2 class="font-black text-2xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('Tambah Aset Baru') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-bold">Input Data Inventaris Manual</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-24">
            <form action="{{ route('assets.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- Left Side: Main Form --}}
                    <div class="lg:col-span-2 space-y-8">
                        {{-- Section 1: Informasi Dasar --}}
                        <div class="bg-white dark:bg-gray-950 p-10 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                            <h3 class="text-lg font-black text-gray-800 dark:text-white flex items-center gap-3">
                                <span class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center text-sm italic">01</span>
                                Informasi Dasar & Kategori
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Barang / Aset</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        placeholder="Contoh: Laptop MacBook Pro M2"
                                        class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 transition-all">
                                </div>
                                
                                <div>
                                    <label for="institution_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Lembaga</label>
                                    <select name="institution_id" id="institution_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih Lembaga</option>
                                        @foreach ($institutions as $item)
                                            <option value="{{ $item->id }}" @selected(old('institution_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="category_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kategori Barang</label>
                                    <select name="category_id" id="category_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}" @selected(old('category_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Penempatan & PIC --}}
                        <div class="bg-white dark:bg-gray-950 p-10 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                            <h3 class="text-lg font-black text-gray-800 dark:text-white flex items-center gap-3">
                                <span class="w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center text-sm italic">02</span>
                                Penempatan & Penanggung Jawab
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label for="building_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Gedung</label>
                                    <select name="building_id" id="building_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih Gedung</option>
                                        @foreach ($buildings as $item)
                                            <option value="{{ $item->id }}" @selected(old('building_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="room_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Ruangan</label>
                                    <select name="room_id" id="room_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih Ruangan</option>
                                        @foreach ($rooms as $item)
                                            <option value="{{ $item->id }}" @selected(old('room_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="faculty_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Fakultas / Direktorat</label>
                                    <select name="faculty_id" id="faculty_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih Fakultas</option>
                                        @foreach ($faculties as $item)
                                            <option value="{{ $item->id }}" @selected(old('faculty_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="department_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Prodi / Unit</label>
                                    <select name="department_id" id="department_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih Unit</option>
                                        @foreach ($departments as $item)
                                            <option value="{{ $item->id }}" @selected(old('department_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="person_in_charge_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Penanggung Jawab (PIC)</label>
                                    <select name="person_in_charge_id" id="person_in_charge_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">Pilih PIC</option>
                                        @foreach ($personsInCharge as $item)
                                            <option value="{{ $item->id }}" @selected(old('person_in_charge_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Financial & Settings --}}
                    <div class="lg:col-span-1 space-y-8">
                        {{-- Section 3: Data Keuangan --}}
                        <div class="bg-gray-900 text-white p-10 rounded-[40px] shadow-2xl space-y-8">
                            <h3 class="text-lg font-black flex items-center gap-3">
                                <span class="w-8 h-8 bg-emerald-500 text-white rounded-xl flex items-center justify-center text-sm italic shadow-lg shadow-emerald-500/20">03</span>
                                Data Keuangan
                            </h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="purchase_year" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tahun Pembelian</label>
                                    <input type="number" name="purchase_year" id="purchase_year" value="{{ old('purchase_year', date('Y')) }}" required
                                        class="w-full px-5 py-4 rounded-[20px] bg-white/5 border-none focus:ring-emerald-500 transition-all text-white placeholder-gray-500">
                                </div>
                                <div>
                                    <label for="purchase_cost" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Harga Beli (Rp)</label>
                                    <input type="number" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost', 0) }}" required min="0"
                                        class="w-full px-5 py-4 rounded-[20px] bg-white/5 border-none focus:ring-emerald-500 transition-all text-white placeholder-gray-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="useful_life" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Manfaat (Th)</label>
                                        <input type="number" name="useful_life" id="useful_life" value="{{ old('useful_life') }}" required min="1"
                                            class="w-full px-5 py-4 rounded-[20px] bg-white/5 border-none focus:ring-emerald-500 transition-all text-white placeholder-gray-500">
                                    </div>
                                    <div>
                                        <label for="salvage_value" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nilai Sisa</label>
                                        <input type="number" name="salvage_value" id="salvage_value" value="{{ old('salvage_value', 0) }}" required min="0"
                                            class="w-full px-5 py-4 rounded-[20px] bg-white/5 border-none focus:ring-emerald-500 transition-all text-white placeholder-gray-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Klasifikasi Lanjutan --}}
                        <div class="bg-white dark:bg-gray-950 p-10 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                            <h3 class="text-lg font-black text-gray-800 dark:text-white flex items-center gap-3">
                                <span class="w-8 h-8 bg-amber-500 text-white rounded-xl flex items-center justify-center text-sm italic">04</span>
                                Klasifikasi Lain
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="asset_function_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Fungsi Barang</label>
                                    <select name="asset_function_id" id="asset_function_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" required>
                                        <option value="">Pilih Fungsi</option>
                                        @foreach ($assetFunctions as $item)
                                            <option value="{{ $item->id }}" @selected(old('asset_function_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="funding_source_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Sumber Dana</label>
                                    <select name="funding_source_id" id="funding_source_id" class="w-full px-5 py-4 rounded-[20px] border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" required>
                                        <option value="">Pilih Sumber Dana</option>
                                        @foreach ($fundingSources as $item)
                                            <option value="{{ $item->id }}" @selected(old('funding_source_id') == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex flex-col gap-4">
                            <button type="submit" class="w-full py-5 bg-red-600 hover:bg-red-700 text-white font-black rounded-[32px] shadow-2xl shadow-red-500/30 transition-all transform hover:-translate-y-1 active:scale-95">
                                Simpan Data Aset
                            </button>
                            <a href="{{ route('assets.index') }}" class="w-full py-5 bg-gray-100 dark:bg-gray-900 text-gray-400 hover:text-gray-600 font-black rounded-[32px] text-center transition-all">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
