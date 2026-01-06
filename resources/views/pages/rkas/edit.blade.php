<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('rkas.index', ['academic_year_id' => $rka->academic_year_id]) }}" class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all flex items-center justify-center text-gray-400 hover:text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('Edit Data RKAS') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-black">Tahun Pelajaran: <span class="text-red-600">{{ $rka->academicYear->year }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-24">
            <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn">
                <form action="{{ route('rkas.update', $rka->id) }}" method="POST" class="p-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Section 1: Identitas --}}
                        <div class="md:col-span-3 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">01</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Identitas & Organisasi</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Kode Lokasi</label>
                            <input type="text" name="kode_lokasi" placeholder="Contoh: 01.01.01" value="{{ old('kode_lokasi', $rka->kode_lokasi) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Struktur PP</label>
                            <input type="text" name="struktur_pp" value="{{ old('struktur_pp', $rka->struktur_pp) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Program (Nama PP)</label>
                            <div class="flex gap-2">
                                <input type="text" name="kode_pp" placeholder="Kode" value="{{ old('kode_pp', $rka->kode_pp) }}" class="w-24 px-4 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                                <input type="text" name="nama_pp" placeholder="Nama Program" value="{{ old('nama_pp', $rka->nama_pp) }}" class="flex-1 px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                            </div>
                        </div>

                        {{-- Section 2: Kegiatan --}}
                        <div class="md:col-span-3 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3 mt-4">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">02</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Kegiatan & DRK</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Kode RKM</label>
                            <input type="text" name="kode_rkm" value="{{ old('kode_rkm', $rka->kode_rkm) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Detail Rencana Kegiatan (DRK)</label>
                            <div class="flex gap-2">
                                <input type="text" name="kode_drk" placeholder="Kode" value="{{ old('kode_drk', $rka->kode_drk) }}" class="w-24 px-4 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                                <input type="text" name="nama_drk" placeholder="Nama DRK" value="{{ old('nama_drk', $rka->nama_drk) }}" class="flex-1 px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                            </div>
                        </div>

                        {{-- Section 3: Mata Anggaran --}}
                        <div class="md:col-span-3 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3 mt-4">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">03</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Mata Anggaran (MTA)</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">MTA (Kode Akun)</label>
                            <input type="text" name="mta" placeholder="Contoh: 5.1.01" value="{{ old('mta', $rka->mta) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Nama Akun</label>
                            <input type="text" name="nama_akun" value="{{ old('nama_akun', $rka->nama_akun) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Sumber Anggaran</label>
                            <input type="text" name="sumber_anggaran" placeholder="Contoh: BOS / Yayasan" value="{{ old('sumber_anggaran', $rka->sumber_anggaran) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>
                        <div class="md:col-span-3 space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Rincian Kegiatan</label>
                            <textarea name="rincian_kegiatan" rows="3"
                                class="w-full px-6 py-4 rounded-3xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">{{ old('rincian_kegiatan', $rka->rincian_kegiatan) }}</textarea>
                        </div>

                        {{-- Section 4: Budget --}}
                        <div class="md:col-span-3 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3 mt-4">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">04</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Budget & Rincian</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Quantity</label>
                            <input type="number" step="any" name="quantity" value="{{ old('quantity', $rka->quantity) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold text-center">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Satuan</label>
                            <input type="text" name="satuan" placeholder="Contoh: Orang / Hari" value="{{ old('satuan', $rka->satuan) }}"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold text-center">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Tarif (Satuan)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 font-black text-gray-400">Rp</span>
                                <input type="number" name="tarif" value="{{ old('tarif', $rka->tarif) }}"
                                    class="w-full pl-14 pr-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Bulan Pelaksanaan</label>
                            <select name="bulan" 
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                    <option value="{{ $bulan }}" {{ old('bulan', $rka->bulan) == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-12 pt-8 border-t border-gray-50 dark:border-gray-900 gap-4">
                        <a href="{{ route('rkas.index', ['academic_year_id' => $rka->academic_year_id]) }}" 
                            class="px-8 py-4 font-black text-gray-400 hover:text-gray-600 transition-all uppercase tracking-widest text-xs">Batal</a>
                        <button type="submit" 
                            class="px-12 py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                            Perbarui Data RKAS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
