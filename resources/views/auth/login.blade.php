<x-guest-layout>
    <div class="fixed inset-0 flex flex-col lg:flex-row overflow-hidden bg-white dark:bg-gray-950">
        <!-- Left Panel: Interactive Visual & Branding -->
        <div class="relative hidden lg:flex lg:w-1/2 items-center justify-center p-12 overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900">
            <!-- Animated Background Elements -->
            <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] opacity-20 pointer-events-none">
                <svg class="w-full h-full text-white/5" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative z-10 w-full max-w-lg text-center lg:text-left space-y-8 animate-fadeIn">
                <div class="flex items-center gap-4 mb-2 justify-center lg:justify-start">
                    @php
                        $logo = \App\Models\Setting::get('app_logo');
                    @endphp
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" class="h-16 w-auto object-contain filter drop-shadow-2xl">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 text-white shadow-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                        </div>
                    @endif
                    <div class="text-white">
                        <h2 class="text-4xl font-black tracking-tighter leading-none">ASTELA</h2>
                        <p class="text-sm font-bold tracking-[0.3em] uppercase opacity-70">Asset Management</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h1 class="text-5xl lg:text-6xl font-black text-white leading-tight">
                        Kelola Aset di Satu <span class="text-primary-300">Genggaman.</span>
                    </h1>
                    <p class="text-lg text-primary-100 leading-relaxed opacity-80">
                        Sistem manajemen aset modern yang membantu Anda memantau, mendistribusikan, dan memelihara inventaris sekolah dengan lebih cerdas dan efisien.
                    </p>
                </div>

                <div class="pt-8 flex flex-wrap gap-6 justify-center lg:justify-start">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/10">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div class="text-white text-sm font-semibold">Aman & Terenkripsi</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/10">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div class="text-white text-sm font-semibold">Performa Tinggi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 relative overflow-y-auto">
            <!-- Mobile Header (hidden on Large screens) -->
            <div class="lg:hidden absolute top-0 left-0 right-0 p-8 flex items-center justify-between">
                 <div class="flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-white shadow-lg shadow-primary-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <span class="font-black tracking-tighter text-gray-900 dark:text-white">ASTELA</span>
                </div>
            </div>

            <div class="w-full max-w-sm space-y-8 animate-slideUp">
                <div class="space-y-2">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white leading-none">Selamat Datang!</h2>
                    <p class="text-gray-500 dark:text-gray-400">Silakan masuk dengan kredensial untuk melanjutkan.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2 group">
                        <label for="email" class="text-xs font-black uppercase tracking-widest text-gray-400 group-focus-within:text-primary-600 transition-colors ml-1">Email Sekolah</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="nama@telkom.sch.id"
                                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl focus:bg-white dark:focus:bg-gray-950 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-gray-800 dark:text-gray-200 transition-all duration-200 placeholder-gray-400" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2 group">
                        <div class="flex items-center justify-between ml-1">
                            <label for="password" class="text-xs font-black uppercase tracking-widest text-gray-400 group-focus-within:text-primary-600 transition-colors">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-bold text-primary-600 hover:text-primary-500 transition-colors" href="{{ route('password.request') }}">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••"
                                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl focus:bg-white dark:focus:bg-gray-950 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-gray-800 dark:text-gray-200 transition-all duration-200 placeholder-gray-400" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-gray-200 dark:border-gray-700 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 transition-all cursor-pointer bg-gray-50 dark:bg-gray-900">
                            <span class="ms-3 text-sm font-semibold text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">{{ __('Ingat saya di perangkat ini') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-4 px-6 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-black rounded-2xl shadow-xl shadow-primary-500/25 transition-all active:scale-[0.98] group relative overflow-hidden">
                        <span class="relative z-10">{{ __('MASUK KE DASHBOARD') }}</span>
                        <svg class="w-5 h-5 relative z-10 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    </button>
                </form>

                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-100 dark:border-gray-800"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-4 bg-white dark:bg-gray-950 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Atau Lanjutkan Dengan</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <a href="{{ route('auth.google.redirect') }}" class="flex items-center justify-center w-full px-6 py-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:border-primary-200 dark:hover:border-primary-900/50 transition-all group">
                        <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" />
                            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C39.712,35.619,44,29.122,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
                        </svg>
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Google Workspace SMK Telkom</span>
                    </a>

                    @if(\App\Models\Setting::get('allow_registration', '1') === '1')
                        <p class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400">
                            Belum memiliki akses sistem? 
                            <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-500 hover:underline">
                                Minta Akses Sekarang
                            </a>
                        </p>
                    @endif
                </div>
                
                <div class="pt-12 text-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300 dark:text-gray-700">Powered by Tim IT SMK Telkom Purwokerto</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out forwards;
        }
        .animate-slideUp {
            animation: slideUp 0.8s ease-out forwards;
        }
        body {
            overflow: hidden;
        }
    </style>
</x-guest-layout>
