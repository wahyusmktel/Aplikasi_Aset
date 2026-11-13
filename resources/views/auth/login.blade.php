<x-guest-layout>
    <div class="fixed inset-0 flex flex-col lg:flex-row overflow-hidden">

        {{-- =========================================
             KIRI: Panel Branding (hanya Desktop)
             ========================================= --}}
        <div class="relative hidden lg:flex lg:w-5/12 flex-col justify-between p-14 overflow-hidden bg-stone-50">

            {{-- Tekstur halus background --}}
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: repeating-linear-gradient(45deg, #000 0, #000 1px, transparent 0, transparent 50%); background-size: 20px 20px;">
            </div>

            {{-- Lingkaran dekoratif soft --}}
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-rose-100/60"></div>
            <div class="absolute top-20 -right-16 w-64 h-64 rounded-full bg-orange-50/80"></div>

            {{-- Logo & Nama Aplikasi --}}
            <div class="relative z-10 flex items-center gap-3">
                @php $logo = \App\Models\Setting::get('app_logo'); @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" class="h-10 w-auto object-contain">
                @else
                    <div class="h-10 w-10 rounded-xl bg-rose-600 flex items-center justify-center">
                        <span class="text-white font-black text-sm">A</span>
                    </div>
                @endif
                <div>
                    <span class="font-black text-gray-900 tracking-tight text-lg">ASTELA</span>
                    <p class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">Asset Management</p>
                </div>
            </div>

            {{-- Konten Tengah --}}
            <div class="relative z-10 space-y-8">
                <div class="space-y-4">
                    <div class="inline-block px-3 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-full tracking-widest uppercase">
                        Sistem Inventaris Sekolah
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 leading-tight">
                        Satu tempat<br>
                        untuk semua<br>
                        <span class="text-rose-600">aset sekolah.</span>
                    </h1>
                    <p class="text-gray-500 leading-relaxed text-sm max-w-xs">
                        Pantau, distribusikan, dan rawat inventaris dengan lebih mudah — kapanpun, di mana saja.
                    </p>
                </div>

                {{-- Statistik / Highlight kecil --}}
                <div class="flex gap-8">
                    <div>
                        <p class="text-2xl font-black text-gray-900">7.000+</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Aset Tercatat</p>
                    </div>
                    <div class="w-px bg-gray-200"></div>
                    <div>
                        <p class="text-2xl font-black text-gray-900">100%</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Berbasis Web</p>
                    </div>
                    <div class="w-px bg-gray-200"></div>
                    <div>
                        <p class="text-2xl font-black text-gray-900">24/7</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Dapat Diakses</p>
                    </div>
                </div>
            </div>

            {{-- Footer kiri --}}
            <div class="relative z-10">
                <p class="text-[10px] font-bold tracking-widest text-gray-300 uppercase">
                    &copy; {{ date('Y') }} &middot; Tim IT SMK Telkom Lampung
                </p>
            </div>
        </div>

        {{-- =========================================
             KANAN: Form Login
             ========================================= --}}
        <div class="w-full lg:w-7/12 flex items-center justify-center p-6 lg:p-16 bg-white overflow-y-auto">
            <div class="w-full max-w-md space-y-10">

                {{-- Mobile: logo kecil --}}
                <div class="flex items-center gap-3 lg:hidden">
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" class="h-9 w-auto object-contain">
                    @else
                        <div class="h-9 w-9 rounded-lg bg-rose-600 flex items-center justify-center">
                            <span class="text-white font-black text-xs">A</span>
                        </div>
                    @endif
                    <span class="font-black text-gray-900 tracking-tight">ASTELA</span>
                </div>

                {{-- Judul --}}
                <div class="space-y-1.5">
                    <h2 class="text-3xl font-black text-gray-900 leading-tight">Selamat datang</h2>
                    <p class="text-gray-500 text-sm">Silakan masuk untuk melanjutkan ke dashboard.</p>
                </div>

                {{-- Status sesi --}}
                <x-auth-session-status class="-mb-6" :status="session('status')" />

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">
                            Email Sekolah
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nama@telkom.sch.id"
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-300 text-sm focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 focus:bg-white transition-all duration-200"
                        >
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    {{-- Password dengan Show/Hide --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs font-semibold text-rose-600 hover:text-rose-500 transition-colors">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Ketikkan password Anda"
                                class="w-full px-4 py-3.5 pr-12 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-300 text-sm focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 focus:bg-white transition-all duration-200"
                            >
                            {{-- Tombol Show/Hide: tanpa icon, hanya teks --}}
                            <button
                                type="button"
                                id="toggle-password"
                                onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 hover:text-rose-600 transition-colors select-none focus:outline-none"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <span id="toggle-label">Lihat</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    {{-- Ingat Saya --}}
                    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-rose-600 focus:ring-rose-400 focus:ring-offset-0 cursor-pointer"
                        >
                        <span class="text-sm text-gray-500 group-hover:text-gray-700 transition-colors font-medium">
                            Ingat saya di perangkat ini
                        </span>
                    </label>

                    {{-- Tombol Login Utama --}}
                    <button
                        type="submit"
                        class="w-full py-3.5 px-6 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-sm rounded-xl transition-all duration-150 active:scale-[0.99] shadow-sm shadow-rose-200"
                    >
                        Masuk ke Dashboard
                    </button>

                    {{-- Pemisah --}}
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-3 bg-white text-xs text-gray-400 font-medium">atau masuk dengan</span>
                        </div>
                    </div>

                    {{-- SSO STELLA --}}
                    <a
                        href="{{ route('auth.sso.redirect') }}"
                        class="w-full flex items-center justify-center gap-3 py-3.5 px-6 border border-gray-900 bg-white hover:bg-gray-900 text-gray-900 hover:text-white font-bold text-sm rounded-xl transition-all duration-200 group"
                    >
                        <span>Login SSO STELLA</span>
                    </a>

                    {{-- Google Workspace --}}
                    <a
                        href="{{ route('auth.google.redirect') }}"
                        class="w-full flex items-center justify-center gap-3 py-3.5 px-6 border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm rounded-xl transition-all duration-200"
                    >
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C39.712,35.619,44,29.122,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                        </svg>
                        <span>Google Workspace SMK Telkom</span>
                    </a>
                </form>

                {{-- Daftar --}}
                @if(\App\Models\Setting::get('allow_registration', '1') === '1')
                    <p class="text-center text-sm text-gray-400">
                        Belum punya akses?
                        <a href="{{ route('register') }}" class="font-semibold text-rose-600 hover:text-rose-500 hover:underline">
                            Minta akses sekarang
                        </a>
                    </p>
                @endif

                {{-- Footer mobile --}}
                <p class="text-center text-[10px] font-bold tracking-widest text-gray-300 uppercase lg:hidden">
                    &copy; {{ date('Y') }} &middot; Tim IT SMK Telkom Lampung
                </p>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const label = document.getElementById('toggle-label');
            if (input.type === 'password') {
                input.type = 'text';
                label.textContent = 'Sembunyikan';
            } else {
                input.type = 'password';
                label.textContent = 'Lihat';
            }
        }
    </script>
</x-guest-layout>
