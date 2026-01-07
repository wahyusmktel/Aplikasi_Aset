<x-app-layout x-data="rabForm()">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('rab.index') }}" class="p-3 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all flex items-center justify-center text-gray-400 hover:text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('Buat RAB Baru') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest font-black">Tahun Pelajaran Aktif: <span class="text-red-600">{{ $activeYear->year }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-24">
            <form action="{{ route('rab.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                <input type="hidden" name="nama_akun_hidden" x-model="namaAkun">
                <input type="hidden" name="drk_hidden" x-model="drk">

                <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Identitas RAB --}}
                        <div class="md:col-span-2 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">01</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Informasi Umum</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Nama RAB</label>
                            <input type="text" name="name" required placeholder="Contoh: Panitia PPDB 2025"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Kebutuhan Waktu</label>
                            <input type="text" name="kebutuhan_waktu" required placeholder="Contoh: Januari 2025"
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                        </div>

                        {{-- Sumber Data RKAS --}}
                        <div class="md:col-span-2 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3 mt-4">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">02</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Sumber Data (MTA)</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Pilih Nomor Akun (MTA)</label>
                            <select name="mta" x-model="selectedMta" @change="fetchMtaDetails()" required
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold">
                                <option value="">Pilih MTA</option>
                                @foreach($mtaList as $mta)
                                    <option value="{{ $mta->mta }}">{{ $mta->mta }} - {{ $mta->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Nama Akun</label>
                                <div class="px-6 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 font-bold text-gray-500 dark:text-gray-400" x-text="namaAkun || '-'"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">DRK</label>
                                <div class="px-6 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 font-bold text-gray-500 dark:text-gray-400" x-text="drk || '-'"></div>
                            </div>
                        </div>

                        {{-- Rincian Kegiatan --}}
                        <div class="md:col-span-2 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3 mt-4">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">03</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Rincian Kegiatan (Uraian)</h3>
                        </div>

                        <div class="md:col-span-2">
                            <template x-if="items.length > 0">
                                <div class="overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-16">Pilih</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rincian Asli (RKAS)</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Alias (Uraian RAB)</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Spesifikasi</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Vol & Harga (RKAS)</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-24">Custom Vol</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-32">Custom Harga</th>
                                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                                                <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-900/30" :class="{'bg-red-50/50 dark:bg-red-900/10': item.customAmount > (item.tarif * item.quantity)}">
                                                    <td class="p-4 text-center">
                                                        <input type="checkbox" name="selected_rkas[]" :value="item.id" x-model="selectedItems" @change="calculateTotal()"
                                                            class="w-5 h-5 rounded-lg border-gray-300 text-red-600 focus:ring-red-600">
                                                    </td>
                                                    <td class="p-4">
                                                        <span class="text-xs font-medium text-gray-400" x-text="item.rincian_kegiatan"></span>
                                                    </td>
                                                    <td class="p-4">
                                                        <input type="text" :name="'alias[' + item.id + ']'" :value="item.rincian_kegiatan"
                                                            class="w-full px-4 py-2 text-sm rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500">
                                                    </td>
                                                    <td class="p-4">
                                                        <input type="text" :name="'specification[' + item.id + ']'" placeholder="Spesifikasi..."
                                                            class="w-full px-4 py-2 text-sm rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500">
                                                    </td>
                                                    <td class="p-4 text-right">
                                                        <div class="flex flex-col">
                                                            <span class="text-[10px] font-bold text-gray-400" x-text="item.quantity + ' ' + item.satuan"></span>
                                                            <span class="text-[10px] font-bold text-gray-400" x-text="'@ Rp ' + formatNumber(item.tarif)"></span>
                                                            <span class="text-[10px] font-black text-gray-400" x-text="'Limit: Rp ' + formatNumber(item.tarif * item.quantity)"></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-4">
                                                        <input type="number" step="any" :name="'custom_vol[' + item.id + ']'" x-model="item.customVol" @input="updateItemAmount(item)"
                                                            class="w-full px-3 py-2 text-sm rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-center"
                                                            :class="{'border-red-500 focus:ring-red-500': item.customAmount > (item.tarif * item.quantity)}">
                                                    </td>
                                                    <td class="p-4">
                                                        <input type="number" step="any" :name="'custom_price[' + item.id + ']'" x-model="item.customPrice" @input="updateItemAmount(item)"
                                                            class="w-full px-3 py-2 text-sm rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 font-bold focus:border-red-500 text-center"
                                                            :class="{'border-red-500 focus:ring-red-500': item.customAmount > (item.tarif * item.quantity)}">
                                                    </td>
                                                    <td class="p-4 text-right">
                                                        <div class="flex flex-col">
                                                            <span class="text-sm font-black" :class="item.customAmount > (item.tarif * item.quantity) ? 'text-red-600' : 'text-gray-800 dark:text-white'" x-text="'Rp ' + formatNumber(item.customAmount)"></span>
                                                            <template x-if="item.customAmount > (item.tarif * item.quantity)">
                                                                <span class="text-[8px] font-black text-red-600 uppercase tracking-tighter">Melebihi Limit!</span>
                                                            </template>
                                                        </div>
                                                    </td>
                                                </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                                <td colspan="5" class="p-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Total Anggaran Terpilih:</td>
                                                <td class="p-6 text-right">
                                                    <span class="text-xl font-black text-red-600" x-text="'Rp ' + formatNumber(totalAmount)"></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </template>
                            <template x-if="items.length === 0 && selectedMta">
                                <div class="p-10 text-center bg-gray-50 dark:bg-gray-900 rounded-3xl">
                                    <p class="text-sm font-bold text-gray-400">Memuat rincian kegiatan...</p>
                                </div>
                            </template>
                            <template x-if="!selectedMta">
                                <div class="p-10 text-center bg-gray-50 dark:bg-gray-900 rounded-3xl border-2 border-dashed border-gray-100 dark:border-gray-800">
                                    <p class="text-xs font-black text-gray-300 uppercase tracking-widest">Silakan pilih MTA terlebih dahulu</p>
                                </div>
                            </template>
                        </div>

                        {{-- Tanda Tangan --}}
                        <div class="md:col-span-2 pb-4 border-b border-gray-50 dark:border-gray-900 flex items-center gap-3 mt-4">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">04</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Verifikasi & Tanda Tangan</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Dibuat Oleh</label>
                            <select name="created_by_id" class="form-select-premium">
                                <option value="">Pilih Pegawai</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('created_by_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Diperiksa Oleh</label>
                            <select name="checked_by_id" class="form-select-premium">
                                <option value="">Pilih Pegawai</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('checked_by_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Diperiksa & Disetujui oleh</label>
                            <select name="approved_by_id" class="form-select-premium">
                                <option value="">Pilih Pegawai</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('approved_by_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Diperiksa & Disetujui Realisasi</label>
                            <select name="headmaster_id" class="form-select-premium">
                                <option value="">Pilih Pegawai</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ ($headmaster && $headmaster->id == $emp->id) ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Catatan Tambahan (Akan muncul di PDF)</label>
                            <textarea name="notes" rows="3" placeholder="Berikan catatan jika diperlukan..."
                                class="w-full px-6 py-4 rounded-3xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 border font-bold focus:border-red-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-12 pt-8 border-t border-gray-50 dark:border-gray-900 gap-4">
                        <button type="submit" :disabled="hasInvalidItems"
                            class="px-12 py-4 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                            Simpan & Cetak Preview
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function rabForm() {
                return {
                    selectedMta: '',
                    namaAkun: '',
                    drk: '',
                    items: [],
                    selectedItems: [],
                    totalAmount: 0,

                    fetchMtaDetails() {
                        if (!this.selectedMta) {
                            this.namaAkun = '';
                            this.drk = '';
                            this.items = [];
                            return;
                        }

                        fetch(`{{ route('rab.getMtaDetails') }}?mta=${this.selectedMta}`)
                            .then(res => res.json())
                            .then(data => {
                                this.namaAkun = data.nama_akun;
                                this.drk = data.drk;
                                this.items = data.items.map(item => ({
                                    ...item,
                                    customVol: item.quantity,
                                    customPrice: item.tarif,
                                    customAmount: item.quantity * item.tarif
                                }));
                                this.selectedItems = [];
                                this.calculateTotal();
                            });
                    },

                    updateItemAmount(item) {
                        item.customAmount = (parseFloat(item.customVol) || 0) * (parseFloat(item.customPrice) || 0);
                        this.calculateTotal();
                    },

                    get hasInvalidItems() {
                        return this.items.some(item => 
                            this.selectedItems.includes(item.id.toString()) && 
                            item.customAmount > (item.tarif * item.quantity)
                        );
                    },

                    calculateTotal() {
                        this.totalAmount = this.items
                            .filter(item => this.selectedItems.includes(item.id.toString()) || this.selectedItems.includes(item.id))
                            .reduce((sum, item) => sum + item.customAmount, 0);
                    },

                    formatNumber(num) {
                        return new Intl.NumberFormat('id-ID').format(num);
                    }
                }
            }
        </script>
        <style>
            .form-select-premium {
                @apply w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 shadow-sm font-bold;
            }
        </style>
    @endpush
</x-app-layout>
