<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Pengadaan Aset Baru') }}
        </h2>
    </x-slot>

    <div x-data="procurementFormComponent()" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('procurements.store') }}" method="POST" class="space-y-8 pb-20">
                @csrf
                
                <!-- Main Info -->
                <div class="bg-white dark:bg-gray-950 rounded-3xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <h3 class="text-lg font-black text-gray-800 dark:text-white mb-6 uppercase tracking-wider flex items-center">
                        <span class="w-2 h-8 bg-primary-600 rounded-full mr-3"></span>
                        Informasi Umum
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Rekanan / Vendor</label>
                            <select name="vendor_id" required class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all select2">
                                <option value="">Pilih Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Nomor Referensi (PO/Kontrak)</label>
                            <input type="text" name="reference_number" required placeholder="Contoh: PO/2025/001"
                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tanggal Pengadaan</label>
                            <input type="date" name="procurement_date" required value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Catatan Tambahan</label>
                            <textarea name="notes" rows="2"
                                class="w-full px-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-all"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white dark:bg-gray-950 rounded-3xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-wider flex items-center">
                            <span class="w-2 h-8 bg-blue-600 rounded-full mr-3"></span>
                            Daftar Barang Belanja
                        </h3>
                        <button type="button" @click="addItem()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold rounded-xl transition-all">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah Baris
                        </button>
                    </div>

                    <div class="space-y-6">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-6 rounded-3xl border border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/10 space-y-6 relative animate-fadeIn">
                                <button type="button" @click="removeItem(index)" 
                                    class="absolute -top-3 -right-3 w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 hover:bg-red-200 rounded-full transition-colors shadow-sm"
                                    x-show="items.length > 1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Barang</label>
                                        <input type="text" :name="'items['+index+'][name]'" required
                                            class="w-full px-4 py-2.5 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kategori</label>
                                        <select :name="'items['+index+'][category_id]'" class="w-full px-4 py-2.5 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all">
                                            <option value="">- Pilih Kategori -</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Lembaga Pemilik</label>
                                        <select :name="'items['+index+'][institution_id]'" class="w-full px-4 py-2.5 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all">
                                            <option value="">- Unit Pemilik -</option>
                                            @foreach($institutions as $inst)
                                                <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah (Pcs/Unit)</label>
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" required min="1"
                                            class="w-full px-4 py-2.5 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Harga Satuan (Rp)</label>
                                        <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="item.unit_price" required min="0"
                                            class="w-full px-4 py-2.5 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all font-bold">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Spesifikasi Singkat</label>
                                        <input type="text" :name="'items['+index+'][specs]'" placeholder="Warna, Model, No. Seri, dll"
                                            class="w-full px-4 py-2.5 rounded-xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 transition-all">
                                    </div>
                                </div>
                                
                                <div class="text-right text-xs font-bold text-gray-400">
                                    Subtotal: <span class="text-gray-800 dark:text-white" x-text="formatCurrency(item.quantity * item.unit_price)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total Bottom -->
                    <div class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-800 flex justify-end">
                        <div class="bg-primary-50 dark:bg-primary-900/20 px-8 py-6 rounded-3xl text-right">
                            <span class="block text-xs font-bold text-primary-600 uppercase tracking-widest mb-2">Estimasi Total Investasi</span>
                            <span class="text-3xl font-black text-primary-700 dark:text-primary-400" x-text="formatCurrency(calculateTotal())"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Nav -->
                <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/80 dark:bg-gray-950/80 backdrop-blur-xl border-t border-gray-100 dark:border-gray-800 p-4">
                    <div class="max-w-7xl mx-auto flex justify-between items-center px-4">
                        <a href="{{ route('procurements.index') }}" class="text-gray-500 font-bold hover:text-gray-700 transition-colors">Batal</a>
                        <button type="submit" 
                            class="px-12 py-4 bg-primary-600 hover:bg-primary-700 text-white font-black rounded-2xl transition-all shadow-xl shadow-primary-500/30">
                            Simpan & Proses Pengadaan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function procurementFormComponent() {
            return {
                items: [
                    { name: '', quantity: 1, unit_price: 0, category_id: '', institution_id: '', specs: '' }
                ],
                addItem() {
                    this.items.push({ name: '', quantity: 1, unit_price: 0, category_id: '', institution_id: '', specs: '' });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                calculateTotal() {
                    return this.items.reduce((acc, item) => acc + (item.quantity * item.unit_price), 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
