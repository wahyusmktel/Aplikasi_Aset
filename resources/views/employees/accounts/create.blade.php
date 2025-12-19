<x-app-layout>
    <div class="max-w-xl mx-auto space-y-8 animate-fadeIn">
        <!-- Header Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 to-primary-800 p-8 shadow-2xl">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-2">Buat Akun Login</h2>
                <p class="text-primary-100 opacity-90 text-sm italic">Pegawai: {{ $employee->name }}</p>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Form Card -->
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 animate-slideUp">
            <form action="{{ route('employee.accounts.store', $employee->id) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Akun Login</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200"
                        placeholder="Nama tampilan di sistem...">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200"
                        placeholder="user@example.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                        <input type="password" name="password" id="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Konfirmasi</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-200">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800 mt-8">
                    <a href="{{ route('employees.index') }}"
                        class="text-sm font-bold text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Buat Akun Akses
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="bg-primary-50 dark:bg-primary-900/20 p-6 rounded-3xl border border-primary-100 dark:border-primary-900/30">
            <div class="flex">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400 mr-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="text-sm text-primary-800 dark:text-primary-200 font-medium italic leading-relaxed">
                    Setelah akun dibuat, pegawai dapat login menggunakan email dan password yang telah Anda tentukan. Pastikan data akun dijaga kerahasiaannya.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
