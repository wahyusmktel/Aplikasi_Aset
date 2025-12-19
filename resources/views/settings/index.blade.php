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
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-12">
                            {{-- Section: Registrasi --}}
                            <div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 bg-red-600/10 rounded-2xl flex items-center justify-center border border-red-500/20">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-800 dark:text-white">Registrasi Akun</h3>
                                        <p class="text-sm text-gray-400">Kontrol akses pendaftaran pengguna baru di platform ini.</p>
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

                            {{-- Placeholder for future settings --}}
                            <div class="pt-8 border-t border-gray-100 dark:border-gray-800 opacity-50">
                                <p class="text-xs text-center text-gray-400 font-bold uppercase tracking-[0.2em]">Lebih banyak pengaturan akan segera hadir</p>
                            </div>
                        </div>

                        <div class="flex justify-end mt-12">
                            <button type="submit" 
                                class="px-10 py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1">
                                Simpan Semua Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
