<x-app-layout>
    <div class="space-y-8 pb-12">
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-rose-500 to-orange-600 p-8 shadow-2xl animate-fadeIn">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-white mb-2">{{ __('Buat Laporan Kerusakan') }}</h2>
                <p class="text-rose-100 opacity-90">Lengkapi detail kerusakan aset di bawah ini agar tim teknis dapat segera menindaklanjuti.</p>
            </div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto">
            <form action="{{ route('asset-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 animate-slideUp">
                @csrf
                
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Asset Selection -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Aset yang Bermasalah</label>
                                @if($asset)
                                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-400">{{ $asset->name }}</p>
                                            <p class="text-[10px] text-emerald-600 font-mono">{{ $asset->asset_code_ypt }}</p>
                                        </div>
                                        <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                        <a href="{{ route('asset-reports.create') }}" class="text-xs text-rose-500 font-bold hover:underline">Ganti Aset</a>
                                    </div>
                                @else
                                    <select name="asset_id" id="asset_id" class="select2 w-full" required>
                                        <option value="">Pilih Aset...</option>
                                        @foreach($assets as $a)
                                            <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->asset_code_ypt }})</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-gray-400 mt-2 italic">* Gunakan fitur cari atau scan QR untuk lebih cepat.</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kondisi yang Terlihat</label>
                                <select name="reported_condition" required class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-rose-500 transition-all dark:text-gray-200">
                                    <option value="Rusak Ringan">Rusak Ringan (Masih bisa digunakan sebagian)</option>
                                    <option value="Rusak Berat">Rusak Berat (Tidak bisa digunakan)</option>
                                    <option value="Bermasalah">Bermasalah (Kendala Teknis/Software)</option>
                                    <option value="Hilang/Pecah">Hilang / Pecah Komponen</option>
                                </select>
                            </div>
                        </div>

                        <!-- Damage Details -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Kerusakan</label>
                                <textarea name="description" rows="4" required placeholder="Jelaskan secara detail bagian mana yang rusak atau bagaimana kronologinya..."
                                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-rose-500 transition-all dark:text-gray-200"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Foto Bukti (Opsional)</label>
                                <div class="relative group">
                                    <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="p-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl text-center group-hover:border-rose-400 transition-colors">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p class="text-xs text-gray-500 font-medium">Klik atau drop gambar ke sini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <a href="{{ route('asset-reports.index') }}" class="flex-1 py-4 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-2xl text-center hover:bg-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="flex-[2] py-4 bg-gradient-to-r from-rose-600 to-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 hover:shadow-rose-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Kirim Laporan
                        </button>
                    </div>
                </div>
            </form>
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
    </script>
    <style>
        .select2-container--tailwind .select2-selection--single {
            @apply !bg-gray-50 !dark:bg-gray-800 !border-none !rounded-2xl !h-12 !flex !items-center !px-2;
        }
    </style>
    @endpush
</x-app-layout>
