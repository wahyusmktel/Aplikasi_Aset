<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 dark:text-white leading-tight uppercase tracking-widest">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-950 overflow-hidden shadow-2xl rounded-[32px] border border-gray-100 dark:border-gray-800">
                <div class="p-10">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="space-y-12">
                            {{-- Section: Identitas Aplikasi --}}
                            <div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center border border-blue-500/20">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-800 dark:text-white">Identitas Visual</h3>
                                        <p class="text-sm text-gray-400">Atur logo dan kop surat resmi aplikasi.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {{-- Logo Aplikasi --}}
                                    <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-3xl border border-gray-100 dark:border-gray-800">
                                        <label class="block text-base font-bold text-gray-700 dark:text-gray-300 mb-4">Logo Aplikasi</label>
                                        <div class="flex flex-col items-center gap-6">
                                            <div class="w-32 h-32 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden group relative">
                                                @if($settings['app_logo'])
                                                    <img src="{{ asset('storage/' . $settings['app_logo']) }}" id="logo-preview" class="w-full h-full object-contain p-2">
                                                @else
                                                    <div id="logo-placeholder" class="text-gray-300 dark:text-gray-600">
                                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    </div>
                                                    <img id="logo-preview" class="hidden w-full h-full object-contain p-2">
                                                @endif
                                            </div>
                                            <div class="w-full">
                                                <input type="file" name="app_logo" id="app_logo" class="hidden" accept="image/*" onchange="previewImage(this, 'logo-preview', 'logo-placeholder')">
                                                <label for="app_logo" class="w-full flex justify-center items-center px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-400 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                                    Pilih Logo
                                                </label>
                                                <p class="text-[10px] text-gray-400 mt-2 text-center uppercase tracking-wider font-bold">Rekomendasi: PNG Transparan, Max 2MB</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kop Surat --}}
                                    <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-3xl border border-gray-100 dark:border-gray-800">
                                        <label class="block text-base font-bold text-gray-700 dark:text-gray-300 mb-4">Kop Surat (Header)</label>
                                        <div class="flex flex-col items-center gap-6">
                                            <div class="w-full h-32 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden group relative">
                                                @if($settings['app_kop_surat'])
                                                    <img src="{{ asset('storage/' . $settings['app_kop_surat']) }}" id="kop-preview" class="w-full h-full object-contain p-2">
                                                @else
                                                    <div id="kop-placeholder" class="text-gray-300 dark:text-gray-600">
                                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    </div>
                                                    <img id="kop-preview" class="hidden w-full h-full object-contain p-2">
                                                @endif
                                            </div>
                                            <div class="w-full">
                                                <input type="file" name="app_kop_surat" id="app_kop_surat" class="hidden" accept="image/*" onchange="previewImage(this, 'kop-preview', 'kop-placeholder')">
                                                <label for="app_kop_surat" class="w-full flex justify-center items-center px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-400 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                                    Pilih Kop Surat
                                                </label>
                                                <p class="text-[10px] text-gray-400 mt-2 text-center uppercase tracking-wider font-bold">Rekomendasi: Lebar (Landscape), Max 2MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Registrasi --}}
                            <div class="pt-8 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 bg-red-600/10 rounded-2xl flex items-center justify-center border border-red-500/20">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-800 dark:text-white">Akses Sistem</h3>
                                        <p class="text-sm text-gray-400">Kontrol akses pendaftaran pengguna baru.</p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-3xl border border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="allow_registration" class="text-base font-bold text-gray-700 dark:text-gray-300">Izinkan Registrasi Baru</label>
                                            <p class="text-xs text-gray-400 mt-1">Jika dinonaktifkan, halaman pendaftaran tidak akan bisa diakses oleh publik.</p>
                                        </div>
                                        
                                        <div class="relative inline-flex items-center cursor-pointer" x-data="{ enabled: {{ $settings['allow_registration'] == '1' ? 'true' : 'false' }} }">
                                            <input type="hidden" name="allow_registration" :value="enabled ? '1' : '0'">
                                            <button type="button" 
                                                @click="enabled = !enabled"
                                                :class="enabled ? 'bg-red-600 shadow-red-500/30' : 'bg-gray-200 dark:bg-gray-700'"
                                                class="relative inline-flex h-8 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 shadow-lg">
                                                <span 
                                                    :class="enabled ? 'translate-x-6' : 'translate-x-0'"
                                                    class="pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-12 gap-4">
                            <button type="submit" 
                                class="px-10 py-4 bg-gray-900 dark:bg-white dark:text-gray-900 text-white font-black rounded-2xl shadow-xl transition-all transform hover:-translate-y-1">
                                Simpan Semua Perubahan
                            </button>
                        </div>
                    </form>

                    <script>
                        function previewImage(input, previewId, placeholderId) {
                            const preview = document.getElementById(previewId);
                            const placeholder = document.getElementById(placeholderId);
                            
                            if (input.files && input.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    preview.src = e.target.result;
                                    preview.classList.remove('hidden');
                                    if(placeholder) placeholder.classList.add('hidden');
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
