<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-red-600 rounded-3xl flex items-center justify-center shadow-lg shadow-red-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight">
                    {{ __('Pengaturan Sistem') }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">Kelola pengaturan aplikasi</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 pb-24">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="bg-white dark:bg-gray-950 rounded-[40px] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-fadeIn p-10 space-y-10">
                    
                    {{-- Kop Surat Section --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-50 dark:border-gray-900">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">01</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Kop Surat PDF</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Upload Kop Surat (Header PDF)</label>
                                <input type="file" name="kop_surat" accept="image/png,image/jpeg,image/jpg"
                                    class="w-full px-4 py-3 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 dark:bg-gray-900 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                <p class="text-xs text-gray-400 px-1">Format: PNG, JPG, JPEG. Maksimal 4MB. Rekomendasi: Lebar penuh A4 (sekitar 2480px).</p>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Preview Kop Surat</label>
                                @if($settings['kop_surat'])
                                    <div class="relative group">
                                        <img src="{{ Storage::url($settings['kop_surat']) }}" alt="Kop Surat" class="w-full rounded-2xl border border-gray-100 dark:border-gray-800 shadow-lg">
                                        <div class="absolute top-2 right-2">
                                            <button type="button" 
                                                onclick="if(confirm('Hapus Kop Surat ini?')) document.getElementById('delete-kop-form').submit();"
                                                class="p-2 bg-red-600 text-white rounded-xl opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full h-32 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 flex items-center justify-center">
                                        <span class="text-gray-300 dark:text-gray-700 text-sm">Belum ada Kop Surat</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- App Logo Section --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-50 dark:border-gray-900">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">02</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Logo Aplikasi</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Upload Logo Baru</label>
                                <input type="file" name="app_logo" accept="image/*"
                                    class="w-full px-4 py-3 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 dark:bg-gray-900 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                <p class="text-xs text-gray-400 px-1">Format: PNG, JPG, SVG. Maksimal 2MB.</p>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-1">Preview Logo</label>
                                @if($settings['app_logo'])
                                    <img src="{{ Storage::url($settings['app_logo']) }}" alt="Logo" class="h-20 rounded-2xl border border-gray-100 dark:border-gray-800">
                                @else
                                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 flex items-center justify-center">
                                        <span class="text-gray-300 dark:text-gray-700 text-xs">No Logo</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Registration Setting --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-50 dark:border-gray-900">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center font-black text-xs">03</div>
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-[0.2em]">Keamanan</h3>
                        </div>

                        <div class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl">
                            <div>
                                <span class="block text-sm font-bold text-gray-800 dark:text-white">Izinkan Registrasi Pengguna Baru</span>
                                <span class="text-xs text-gray-400">Pengguna dapat mendaftarkan akun baru melalui halaman registrasi.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="allow_registration" value="0">
                                <input type="checkbox" name="allow_registration" value="1" {{ $settings['allow_registration'] == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-8 border-t border-gray-50 dark:border-gray-900">
                        <button type="submit" 
                            class="px-12 py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form id="delete-kop-form" action="{{ route('settings.deleteKopSurat') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-app-layout>
