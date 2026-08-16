<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('app_name', config('app.name', 'StellaLog\'s')) }} - Sistem Manajemen Aset & Sarpras</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800,900|plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    {{-- Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine JS CDN fallback to guarantee smooth execution --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Custom Styles for Ultra-Modern Visuals & Full Hero Slider --}}
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --primary: #e11d48;
            --primary-glow: rgba(225, 29, 72, 0.35);
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            background-color: #070709;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-text-red {
            background: linear-gradient(135deg, #ffffff 20%, #fca5a5 60%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-text-accent {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 50%, #991b1b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .bg-glass {
            background: rgba(15, 17, 23, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .bg-glass-card {
            background: linear-gradient(145deg, rgba(26, 29, 39, 0.6) 0%, rgba(13, 15, 22, 0.8) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .bg-glass-card:hover {
            border-color: rgba(239, 68, 68, 0.3);
            box-shadow: 0 10px 30px -10px rgba(225, 29, 72, 0.2);
        }

        .bg-red-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 60%, #450a0a 100%);
        }

        .radial-mesh {
            background-image: 
                radial-gradient(at 10% 20%, rgba(220, 38, 38, 0.18) 0px, transparent 50%),
                radial-gradient(at 90% 10%, rgba(239, 68, 68, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(153, 27, 27, 0.15) 0px, transparent 60%);
        }

        .hero-progress-bar {
            transition: width 0.1s linear;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.08); }
        }

        .animate-glow {
            animation: pulseGlow 6s ease-in-out infinite;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-14px) rotate(1deg); }
        }

        .animate-float-slow {
            animation: floatSlow 7s ease-in-out infinite;
        }

        .grid-pattern {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
    </style>
</head>

<body x-data="{ 
        scrolled: false, 
        mobileMenu: false,
        activeTab: 'inventaris',
        heroSlide: 0,
        totalSlides: 4,
        slideProgress: 0,
        slideDuration: 6500,
        timer: null,
        progressTimer: null,
        
        initSlider() {
            this.startSlider();
        },
        startSlider() {
            this.slideProgress = 0;
            if (this.timer) clearInterval(this.timer);
            if (this.progressTimer) clearInterval(this.progressTimer);
            
            const stepTime = 50;
            const stepIncrement = (stepTime / this.slideDuration) * 100;
            
            this.progressTimer = setInterval(() => {
                this.slideProgress += stepIncrement;
                if (this.slideProgress >= 100) {
                    this.slideProgress = 0;
                }
            }, stepTime);
            
            this.timer = setInterval(() => {
                this.nextSlide();
            }, this.slideDuration);
        },
        goToSlide(index) {
            this.heroSlide = index;
            this.startSlider();
        },
        nextSlide() {
            this.heroSlide = (this.heroSlide + 1) % this.totalSlides;
            this.startSlider();
        },
        prevSlide() {
            this.heroSlide = (this.heroSlide - 1 + this.totalSlides) % this.totalSlides;
            this.startSlider();
        }
    }" 
    x-init="initSlider()"
    @scroll.window="scrolled = (window.pageYOffset > 25)"
    class="antialiased selection:bg-red-600 selection:text-white">

    @php
        // Mengambil identitas sekolah & data sistem secara aman
        $appName = \App\Models\Setting::get('app_name', 'StellaLog\'s');
        $schoolName = \App\Models\Setting::get('school_name', 'SMK Telkom Lampung');
        $logo = \App\Models\Setting::get('app_logo');
        
        // Menghitung statistik aktual jika ada di database
        $totalAssets = 0;
        $totalRooms = 0;
        $totalCategories = 0;
        $totalEmployees = 0;
        try {
            $totalAssets = \App\Models\Asset::count();
            $totalRooms = \App\Models\Room::count();
            $totalCategories = \App\Models\Category::count();
            $totalEmployees = \App\Models\Employee::count();
        } catch (\Throwable $e) {
            // fallback
        }
        if ($totalAssets == 0) $totalAssets = '4.200+';
        if ($totalRooms == 0) $totalRooms = '48';
        if ($totalCategories == 0) $totalCategories = '24';
        if ($totalEmployees == 0) $totalEmployees = '120+';
    @endphp

    {{-- Ambient Lighting & Grid Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-[20%] left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-red-600/15 rounded-full blur-[140px] animate-glow"></div>
        <div class="absolute top-[40%] -left-[10%] w-[600px] h-[600px] bg-rose-600/10 rounded-full blur-[130px]"></div>
        <div class="absolute bottom-[10%] -right-[10%] w-[700px] h-[700px] bg-red-900/15 rounded-full blur-[150px]"></div>
        <div class="absolute inset-0 grid-pattern opacity-60"></div>
    </div>

    {{-- Modern Navbar --}}
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-black/70 backdrop-blur-xl border-b border-white/10 py-3.5 shadow-2xl' : 'bg-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                
                {{-- Brand Identity --}}
                <a href="#" class="flex items-center gap-3.5 group">
                    <div class="relative flex items-center justify-center">
                        <div class="absolute -inset-1 bg-red-600/50 rounded-xl blur-sm group-hover:bg-red-500 transition-all opacity-70"></div>
                        <div class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-red-700 text-white shadow-lg shadow-red-900/40">
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" class="h-7 w-auto object-contain brightness-0 invert" alt="{{ $appName }}">
                            @else
                                <svg class="h-6 w-6 transform group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="heading-font font-black text-xl tracking-tight text-white group-hover:text-red-400 transition-colors">
                                {{ $appName }}
                            </span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">PRO</span>
                        </div>
                        <span class="text-[11px] font-medium text-gray-400 -mt-0.5 tracking-wide">{{ $schoolName }}</span>
                    </div>
                </a>

                {{-- Desktop Navigation Links --}}
                <nav class="hidden lg:flex items-center gap-1 bg-white/[0.04] p-1.5 rounded-full border border-white/10 backdrop-blur-md">
                    <a href="#hero" class="px-4 py-2 text-xs font-bold text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition-all">Beranda</a>
                    <a href="#features" class="px-4 py-2 text-xs font-bold text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition-all">Fitur Utama</a>
                    <a href="#modules" class="px-4 py-2 text-xs font-bold text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition-all">Modul Sistem</a>
                    <a href="#workflow" class="px-4 py-2 text-xs font-bold text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition-all">Alur Kerja</a>
                    <a href="#verify-hub" class="px-4 py-2 text-xs font-bold text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-full transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verifikasi Publik
                    </a>
                </nav>

                {{-- Action / Auth Buttons --}}
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('public.verify-document') }}" 
                       class="hidden xl:inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-all">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Cek Dokumen
                    </a>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="relative inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/30 transform hover:-translate-y-0.5 transition-all">
                                <span>Buka Dashboard</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="px-4 py-2 text-xs font-bold text-gray-300 hover:text-white transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/30 transform hover:-translate-y-0.5 transition-all">
                                <span>Akses Sistem</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endauth
                    @endif
                </div>

                {{-- Mobile Menu Trigger --}}
                <div class="flex lg:hidden items-center gap-2">
                    <button @click="mobileMenu = !mobileMenu" 
                            class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-300 hover:text-white focus:outline-none">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile Drawer Menu --}}
        <div x-show="mobileMenu" x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="lg:hidden px-4 pt-3 pb-6 bg-gray-950/95 border-b border-white/10 backdrop-blur-2xl">
            <div class="flex flex-col gap-2">
                <a href="#hero" @click="mobileMenu = false" class="px-4 py-2.5 text-sm font-semibold text-gray-200 hover:bg-white/5 rounded-xl">Beranda</a>
                <a href="#features" @click="mobileMenu = false" class="px-4 py-2.5 text-sm font-semibold text-gray-200 hover:bg-white/5 rounded-xl">Fitur Utama</a>
                <a href="#modules" @click="mobileMenu = false" class="px-4 py-2.5 text-sm font-semibold text-gray-200 hover:bg-white/5 rounded-xl">Modul Sistem</a>
                <a href="#workflow" @click="mobileMenu = false" class="px-4 py-2.5 text-sm font-semibold text-gray-200 hover:bg-white/5 rounded-xl">Alur Kerja</a>
                <a href="{{ route('public.verify-document') }}" class="px-4 py-2.5 text-sm font-semibold text-red-400 bg-red-500/10 rounded-xl flex items-center justify-between">
                    <span>Verifikasi Dokumen PDF</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full text-center py-3 bg-red-600 text-white font-bold text-sm rounded-xl">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold text-sm rounded-xl">Masuk ke Sistem</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="relative z-10">

        {{-- ========================================================================= --}}
        {{-- FULL PAGE HERO SLIDER (100vh / Full Viewport)                             --}}
        {{-- ========================================================================= --}}
        <section id="hero" class="relative w-full min-h-screen flex flex-col justify-between pt-24 pb-10 px-4 sm:px-6 lg:px-8 overflow-hidden bg-radial-mesh">
            
            {{-- Background decorative grid light --}}
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/40 to-[#070709] pointer-events-none"></div>

            {{-- Slider Main Content Wrapper --}}
            <div class="max-w-7xl mx-auto w-full my-auto py-8">
                
                {{-- Slide 0: Smart Asset Inventory & QR Management --}}
                <div x-show="heroSlide === 0" 
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-8"
                     class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    {{-- Text Left Column --}}
                    <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-red-600/10 border border-red-500/30 backdrop-blur-md">
                            <span class="flex h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-red-400">Generasi Baru Tata Kelola Sarpras</span>
                        </div>

                        <h1 class="heading-font text-4xl sm:text-6xl xl:text-7xl font-black leading-[1.08] tracking-tight text-white">
                            Transformasi Digital <br>
                            <span class="gradient-text-red">Manajemen Aset</span> Sekolah
                        </h1>

                        <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                            Sentralisasi inventarisasi sekolah berbasis QR Code cerdas. Pantau pergerakan ribuan aset, lokasi ruangan, kondisi terkini, dan riwayat pertanggungjawaban secara akurat dan transparan.
                        </p>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                   class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-red-900/40 hover:from-red-500 hover:to-rose-500 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                    <span>Buka Dashboard Utama</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-red-900/40 hover:from-red-500 hover:to-rose-500 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                    <span>Mulai Sekarang</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            @endauth
                            <a href="#features" 
                               class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-sm rounded-2xl backdrop-blur-md transition-all text-center">
                                Jelajahi Fitur
                            </a>
                        </div>

                        {{-- Micro Highlight Badges --}}
                        <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Labeling QR Otomatis</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Multi-Gedung & Ruangan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Batch Entry & Import Excel</span>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Right Column: Dashboard Mockup & Live Card --}}
                    <div class="lg:col-span-5 relative hidden lg:block">
                        <div class="relative animate-float-slow">
                            <div class="bg-glass-card rounded-[32px] p-6 shadow-2xl border border-white/10 relative overflow-hidden">
                                <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                        <span class="text-xs text-gray-400 font-mono ml-2">live-monitoring.app</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full bg-green-500/10 text-green-400 text-[10px] font-bold border border-green-500/20">Online 99.9%</span>
                                </div>

                                {{-- Live Asset Cards Preview --}}
                                <div class="space-y-3.5">
                                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-between hover:bg-white/[0.06] transition-all">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-11 h-11 rounded-xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-white">Workstation Lab AI #04</div>
                                                <div class="text-[11px] text-gray-400 font-mono">AST/YPT/2026/0882</div>
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">Siap Pakai</span>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-between hover:bg-white/[0.06] transition-all">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-11 h-11 rounded-xl bg-amber-600/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-white">Proyektor Laser Ruang 204</div>
                                                <div class="text-[11px] text-gray-400 font-mono">AST/YPT/2026/0315</div>
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-semibold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/20">Maintenance</span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 pt-2">
                                        <div class="p-4 rounded-2xl bg-gradient-to-br from-red-600/30 to-red-900/30 border border-red-500/30">
                                            <div class="text-[11px] font-bold text-red-300 uppercase tracking-wider">Aset Terdata</div>
                                            <div class="text-2xl font-black text-white mt-1">{{ $totalAssets }}</div>
                                        </div>
                                        <div class="p-4 rounded-2xl bg-white/[0.04] border border-white/10">
                                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ruang & Lab</div>
                                            <div class="text-2xl font-black text-white mt-1">{{ $totalRooms }} Unit</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 1: Log Kendaraan & Peminjaman Aset --}}
                <div x-show="heroSlide === 1" x-cloak
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-8"
                     class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    {{-- Text Left Column --}}
                    <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-rose-600/10 border border-rose-500/30 backdrop-blur-md">
                            <span class="flex h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-rose-400">Efisiensi Mobilitas & Peminjaman</span>
                        </div>

                        <h1 class="heading-font text-4xl sm:text-6xl xl:text-7xl font-black leading-[1.08] tracking-tight text-white">
                            Log Kendaraan Dinas & <br>
                            <span class="gradient-text-red">Peminjaman Aset</span> Cepat
                        </h1>

                        <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                            Permudah permohonan pinjam aset dan kendaraan dinas operasional sekolah. Dilengkapi persetujuan Waka/Kepsek berjenjang, pencatatan odometer (KM), dan generate BAST serah-terima otomatis.
                        </p>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-red-900/40 hover:from-red-500 hover:to-rose-500 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <span>Ajukan Peminjaman</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#modules" 
                               class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-sm rounded-2xl backdrop-blur-md transition-all text-center">
                                Pelajari Alur Pinjam
                            </a>
                        </div>

                        <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Approval Multi-Level Digital</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Log BBM & Odometer (KM)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>BAST Checkout/Checkin Instan</span>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Right Column: Vehicle & Booking Flow --}}
                    <div class="lg:col-span-5 relative hidden lg:block">
                        <div class="relative animate-float-slow">
                            <div class="bg-glass-card rounded-[32px] p-6 shadow-2xl border border-white/10">
                                <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                                    <div class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        Log Operasional Kendaraan
                                    </div>
                                    <span class="text-[10px] font-mono text-gray-400">BE 1234 XY</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="p-4 rounded-2xl bg-white/[0.04] border border-white/5 space-y-2">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-sm font-black text-white">Toyota HiAce Operasional</div>
                                                <div class="text-xs text-gray-400">Tujuan: Dinas Pendidikan Prov. Lampung</div>
                                            </div>
                                            <span class="px-2 py-1 rounded bg-blue-500/20 text-blue-400 text-[10px] font-bold border border-blue-500/30">Disetujui Kepsek</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-white/5 font-mono">
                                            <span>KM Berangkat: 42.150</span>
                                            <span class="text-emerald-400">BAST #BAST-KND-09</span>
                                        </div>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white/[0.04] border border-white/5 space-y-2">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-sm font-black text-white">Honda Vario 160 (Kurir)</div>
                                                <div class="text-xs text-gray-400">Peminjam: Bagian Tata Usaha</div>
                                            </div>
                                            <span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">Tersedia</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2: Pemeliharaan & Laboratorium Sarpras --}}
                <div x-show="heroSlide === 2" x-cloak
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-8"
                     class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    {{-- Text Left Column --}}
                    <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-amber-600/10 border border-amber-500/30 backdrop-blur-md">
                            <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-amber-400">Pemeliharaan Terjadwal & Lab Praktik</span>
                        </div>

                        <h1 class="heading-font text-4xl sm:text-6xl xl:text-7xl font-black leading-[1.08] tracking-tight text-white">
                            Jaga Performa Aset, <br>
                            <span class="gradient-text-red">Kelola Lab Terintegrasi</span>
                        </h1>

                        <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                            Cegah kerusakan mendadak dengan jadwal pemeliharaan berkala, inspeksi visual, pelaporan kerusakan cepat via QR, dan manajemen penjadwalan laboratorium praktik kejuruan.
                        </p>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <a href="{{ route('asset-reports.scan') }}" 
                               class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-red-900/40 hover:from-red-500 hover:to-rose-500 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Scan QR Lapor Kerusakan</span>
                            </a>
                            <a href="#workflow" 
                               class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-sm rounded-2xl backdrop-blur-md transition-all text-center">
                                Lihat Siklus Perawatan
                            </a>
                        </div>

                        <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Kalender Maintenance Rutin</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Log Praktik & Penggunaan Lab</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Laporan BAPH & Disposal</span>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Right Column --}}
                    <div class="lg:col-span-5 relative hidden lg:block">
                        <div class="relative animate-float-slow">
                            <div class="bg-glass-card rounded-[32px] p-6 shadow-2xl border border-white/10">
                                <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                                    <div class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Jadwal Pemeliharaan Mendatang
                                    </div>
                                    <span class="text-[10px] text-amber-400 font-bold bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">Bulan Ini</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/5 flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-white">Servis Berkala AC Gedung A</div>
                                            <div class="text-[11px] text-gray-400">12 Unit Terjadwal - Lab Multimedia</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-bold text-amber-400">18 Ags 2026</div>
                                            <span class="text-[10px] text-gray-400">Vendor Terverifikasi</span>
                                        </div>
                                    </div>
                                    <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/5 flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-white">Kalibrasi Router & Switch Core</div>
                                            <div class="text-[11px] text-gray-400">Ruang Server Sentral</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-bold text-emerald-400">Selesai</div>
                                            <span class="text-[10px] text-gray-400">BAST Tersedia</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 3: Keamanan Dokumen & Tanda Tangan Digital --}}
                <div x-show="heroSlide === 3" x-cloak
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-8"
                     class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    {{-- Text Left Column --}}
                    <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-emerald-600/10 border border-emerald-500/30 backdrop-blur-md">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-400">Paperless & Anti-Pemalsuan</span>
                        </div>

                        <h1 class="heading-font text-4xl sm:text-6xl xl:text-7xl font-black leading-[1.08] tracking-tight text-white">
                            Tanda Tangan Digital & <br>
                            <span class="gradient-text-red">Verifikasi Dokumen</span> Cepat
                        </h1>

                        <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                            Sistem otentikasi Berita Acara (BAST) dan Surat Keputusan dengan enkripsi QR token. Dokumen resmi sekolah dapat diverifikasi keasliannya oleh siapapun secara instan melalui portal publik.
                        </p>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <a href="{{ route('public.verify-document') }}" 
                               class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-emerald-900/40 hover:from-emerald-500 hover:to-teal-500 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Verifikasi Dokumen Sekarang</span>
                            </a>
                            <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-sm rounded-2xl backdrop-blur-md transition-all text-center">
                                Masuk Portal TTD
                            </a>
                        </div>

                        <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Enkripsi Token SHA-256</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Otomatisasi Kop Surat & Stempel</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Audit Trail Siap Akreditasi</span>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Right Column --}}
                    <div class="lg:col-span-5 relative hidden lg:block">
                        <div class="relative animate-float-slow">
                            <div class="bg-glass-card rounded-[32px] p-6 shadow-2xl border border-white/10">
                                <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                                    <div class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Otentikasi Dokumen Digital
                                    </div>
                                    <span class="text-[10px] text-emerald-400 font-mono font-bold">VERIFIED</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/20 text-center space-y-3">
                                    <div class="w-16 h-16 mx-auto bg-emerald-600/20 rounded-2xl border border-emerald-500/40 flex items-center justify-center text-emerald-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-white">BAST Pengadaan Perangkat Lab TI</div>
                                        <div class="text-[11px] font-mono text-gray-400">Token: 9f8a2c1b-e4d3-4a11-b02c-78a01f</div>
                                    </div>
                                    <div class="text-[11px] text-emerald-400 bg-emerald-500/10 py-1.5 px-3 rounded-lg">
                                        Ditandatangani secara elektronik oleh Kepala Sekolah
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Slider Controls & Progress Indicator Bar --}}
            <div class="max-w-7xl mx-auto w-full pt-6">
                
                {{-- Slide Tabs & Countdown Bar --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    
                    {{-- Tab 0 --}}
                    <button @click="goToSlide(0)" 
                            class="text-left p-3.5 rounded-2xl transition-all duration-300 relative overflow-hidden border"
                            :class="heroSlide === 0 ? 'bg-white/10 border-red-500/50 shadow-lg shadow-red-950/50' : 'bg-white/[0.02] border-white/5 hover:bg-white/[0.05]'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-black font-mono" :class="heroSlide === 0 ? 'text-red-400' : 'text-gray-500'">01</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider" :class="heroSlide === 0 ? 'text-white' : 'text-gray-400'">Inventaris</span>
                        </div>
                        <div class="text-xs font-bold truncate" :class="heroSlide === 0 ? 'text-white' : 'text-gray-400'">Smart QR Tagging</div>
                        
                        {{-- Active Progress Indicator --}}
                        <div x-show="heroSlide === 0" class="absolute bottom-0 left-0 right-0 h-1 bg-red-950">
                            <div class="h-full bg-gradient-to-r from-red-600 to-rose-500 hero-progress-bar" :style="`width: ${slideProgress}%`"></div>
                        </div>
                    </button>

                    {{-- Tab 1 --}}
                    <button @click="goToSlide(1)" 
                            class="text-left p-3.5 rounded-2xl transition-all duration-300 relative overflow-hidden border"
                            :class="heroSlide === 1 ? 'bg-white/10 border-red-500/50 shadow-lg shadow-red-950/50' : 'bg-white/[0.02] border-white/5 hover:bg-white/[0.05]'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-black font-mono" :class="heroSlide === 1 ? 'text-red-400' : 'text-gray-500'">02</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider" :class="heroSlide === 1 ? 'text-white' : 'text-gray-400'">Kendaraan</span>
                        </div>
                        <div class="text-xs font-bold truncate" :class="heroSlide === 1 ? 'text-white' : 'text-gray-400'">Peminjaman & Log</div>

                        <div x-show="heroSlide === 1" class="absolute bottom-0 left-0 right-0 h-1 bg-red-950">
                            <div class="h-full bg-gradient-to-r from-red-600 to-rose-500 hero-progress-bar" :style="`width: ${slideProgress}%`"></div>
                        </div>
                    </button>

                    {{-- Tab 2 --}}
                    <button @click="goToSlide(2)" 
                            class="text-left p-3.5 rounded-2xl transition-all duration-300 relative overflow-hidden border"
                            :class="heroSlide === 2 ? 'bg-white/10 border-red-500/50 shadow-lg shadow-red-950/50' : 'bg-white/[0.02] border-white/5 hover:bg-white/[0.05]'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-black font-mono" :class="heroSlide === 2 ? 'text-red-400' : 'text-gray-500'">03</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider" :class="heroSlide === 2 ? 'text-white' : 'text-gray-400'">Perawatan</span>
                        </div>
                        <div class="text-xs font-bold truncate" :class="heroSlide === 2 ? 'text-white' : 'text-gray-400'">Maintenance & Lab</div>

                        <div x-show="heroSlide === 2" class="absolute bottom-0 left-0 right-0 h-1 bg-red-950">
                            <div class="h-full bg-gradient-to-r from-red-600 to-rose-500 hero-progress-bar" :style="`width: ${slideProgress}%`"></div>
                        </div>
                    </button>

                    {{-- Tab 3 --}}
                    <button @click="goToSlide(3)" 
                            class="text-left p-3.5 rounded-2xl transition-all duration-300 relative overflow-hidden border"
                            :class="heroSlide === 3 ? 'bg-white/10 border-red-500/50 shadow-lg shadow-red-950/50' : 'bg-white/[0.02] border-white/5 hover:bg-white/[0.05]'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-black font-mono" :class="heroSlide === 3 ? 'text-red-400' : 'text-gray-500'">04</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider" :class="heroSlide === 3 ? 'text-white' : 'text-gray-400'">Keamanan</span>
                        </div>
                        <div class="text-xs font-bold truncate" :class="heroSlide === 3 ? 'text-white' : 'text-gray-400'">TTD & Verifikasi</div>

                        <div x-show="heroSlide === 3" class="absolute bottom-0 left-0 right-0 h-1 bg-red-950">
                            <div class="h-full bg-gradient-to-r from-red-600 to-rose-500 hero-progress-bar" :style="`width: ${slideProgress}%`"></div>
                        </div>
                    </button>

                </div>

                {{-- Partner & Nav Buttons Footer in Hero --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-white/10">
                    <div class="flex items-center gap-6 opacity-60 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('logo.png') }}" class="h-6 sm:h-8 object-contain brightness-0 invert" alt="SMK Telkom Lampung">
                        <img src="{{ asset('ypt logo-putih.png') }}" class="h-6 sm:h-8 object-contain brightness-0 invert" alt="Yayasan Pendidikan Telkom">
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="prevSlide()" 
                                aria-label="Previous Slide"
                                class="p-2.5 rounded-xl bg-white/5 hover:bg-white/15 border border-white/10 text-gray-300 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="nextSlide()" 
                                aria-label="Next Slide"
                                class="p-2.5 rounded-xl bg-white/5 hover:bg-white/15 border border-white/10 text-gray-300 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

            </div>

        </section>

        {{-- ========================================================================= --}}
        {{-- QUICK PUBLIC ACCESS & VERIFICATION HUB                                     --}}
        {{-- ========================================================================= --}}
        <section id="verify-hub" class="relative py-16 px-4 sm:px-6 lg:px-8 border-y border-white/5 bg-black/40">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Quick Action 1: Verifikasi BAST PDF --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 hover:border-red-500/40 transition-all flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-red-600/10 border border-red-500/20 flex items-center justify-center text-red-500 group-hover:bg-red-600 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-lg font-black text-white">Verifikasi Dokumen & BAST</h3>
                            <p class="text-xs text-gray-400 leading-relaxed">Cek keabsahan nomor dokumen berita acara serah terima atau surat pengantar pemeliharaan resmi.</p>
                        </div>
                        <div class="pt-6">
                            <a href="{{ route('public.verify-document') }}" 
                               class="inline-flex items-center gap-2 text-xs font-bold text-red-400 group-hover:text-red-300">
                                <span>Buka Verifikator Dokumen</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Quick Action 2: Lapor Kerusakan Aset --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 hover:border-red-500/40 transition-all flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-600/10 border border-amber-500/20 flex items-center justify-center text-amber-500 group-hover:bg-amber-600 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h3 class="text-lg font-black text-white">Lapor Kerusakan Aset</h3>
                            <p class="text-xs text-gray-400 leading-relaxed">Temukan kerusakan fasilitas atau peralatan praktik? Pindai barcode pada barang untuk mengirim laporan cepat ke tim Sarpras.</p>
                        </div>
                        <div class="pt-6">
                            <a href="{{ route('asset-reports.scan') }}" 
                               class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 group-hover:text-amber-300">
                                <span>Scan QR & Kirim Laporan</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Quick Action 3: Peminjaman & Informasi Lab --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 hover:border-red-500/40 transition-all flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-rose-600/10 border border-rose-500/20 flex items-center justify-center text-rose-500 group-hover:bg-rose-600 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-lg font-black text-white">Portal Sarpras Pegawai</h3>
                            <p class="text-xs text-gray-400 leading-relaxed">Akses cepat untuk guru & staf dalam pengajuan peminjaman barang, kendaraan operasional, dan logbook lab.</p>
                        </div>
                        <div class="pt-6">
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-2 text-xs font-bold text-rose-400 group-hover:text-rose-300">
                                <span>Masuk ke Akun Pegawai</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- BENTO GRID FEATURES SHOWCASE                                               --}}
        {{-- ========================================================================= --}}
        <section id="features" class="py-28 px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-7xl mx-auto">
                
                {{-- Section Title --}}
                <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-600/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase tracking-widest">
                        Solusi Komprehensif
                    </div>
                    <h2 class="heading-font text-3xl sm:text-5xl font-black text-white leading-tight">
                        Fitur Unggulan untuk <br>
                        <span class="gradient-text-red">Tata Kelola Profesional</span>
                    </h2>
                    <p class="text-gray-400 text-sm sm:text-base">
                        Dirancang khusus untuk mendukung standar mutu pengelolaan sarana prasarana sekolah kejuruan modern.
                    </p>
                </div>

                {{-- Bento Grid Layout --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    
                    {{-- Bento 1: Large Featured Card (8 cols) --}}
                    <div class="md:col-span-12 lg:col-span-8 bg-glass-card rounded-[32px] p-8 md:p-10 border border-white/10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-80 h-80 bg-red-600/10 rounded-full blur-3xl pointer-events-none group-hover:bg-red-600/20 transition-all"></div>
                        <div class="relative z-10 space-y-6">
                            <div class="w-14 h-14 rounded-2xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-500">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <div class="space-y-3">
                                <h3 class="text-2xl sm:text-3xl font-black text-white">Smart QR Code & Labeling Massal</h3>
                                <p class="text-gray-400 text-sm sm:text-base max-w-xl leading-relaxed">
                                    Cetak ribuan label barcode dan QR Code dengan tata letak rapi, logo institusi, dan nomor serial YPT unik. Mendukung pencarian instan dan audit fisik hanya dengan sekali scan kamera smartphone.
                                </p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-white/10">
                                <div class="p-3 bg-white/[0.02] rounded-xl border border-white/5">
                                    <div class="text-white font-bold text-sm">Batch Printing</div>
                                    <div class="text-[11px] text-gray-400">PDF Label siap tempel</div>
                                </div>
                                <div class="p-3 bg-white/[0.02] rounded-xl border border-white/5">
                                    <div class="text-white font-bold text-sm">QR Code Publik</div>
                                    <div class="text-[11px] text-gray-400">Cek profil aset instan</div>
                                </div>
                                <div class="p-3 bg-white/[0.02] rounded-xl border border-white/5">
                                    <div class="text-white font-bold text-sm">AI Asset Mapping</div>
                                    <div class="text-[11px] text-gray-400">Klasifikasi otomatis</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bento 2: BAST Digital & TTD (4 cols) --}}
                    <div class="md:col-span-6 lg:col-span-4 bg-glass-card rounded-[32px] p-8 border border-white/10 flex flex-col justify-between group">
                        <div class="space-y-5">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white mb-2">BAST Digital & Tanda Tangan</h3>
                                <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                                    Lupakan tumpukan kertas. Seluruh serah terima barang, peminjaman, dan perawatan menghasilkan dokumen BAST PDF dengan tanda tangan digital tersertifikasi.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6">
                            <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span>Verifikasi QR Token 100% Sah</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bento 3: Fleet & Kendaraan (4 cols) --}}
                    <div class="md:col-span-6 lg:col-span-4 bg-glass-card rounded-[32px] p-8 border border-white/10 flex flex-col justify-between group">
                        <div class="space-y-5">
                            <div class="w-14 h-14 rounded-2xl bg-rose-600/20 border border-rose-500/30 flex items-center justify-center text-rose-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white mb-2">Log Operasional Kendaraan</h3>
                                <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                                    Kontrol menyeluruh penggunaan armada sekolah. Pantau jadwal keberangkatan, persetujuan Waka/Kepsek, catatan BBM, dan odometer secara berkala.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6">
                            <div class="text-xs font-mono text-gray-400 bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                Terhubung dengan BAST Otomatis
                            </div>
                        </div>
                    </div>

                    {{-- Bento 4: RKAS & RAB Terintegrasi (4 cols) --}}
                    <div class="md:col-span-6 lg:col-span-4 bg-glass-card rounded-[32px] p-8 border border-white/10 flex flex-col justify-between group">
                        <div class="space-y-5">
                            <div class="w-14 h-14 rounded-2xl bg-amber-600/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white mb-2">Pengadaan, RKAS & RAB</h3>
                                <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                                    Jembatani perencanaan anggaran (RAB) dengan pengadaan riil. Konversi barang hasil belanja vendor langsung menjadi aset aktif tanpa input ulang.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6">
                            <div class="text-xs font-mono text-gray-400 bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                Realisasi Anggaran Akurat
                            </div>
                        </div>
                    </div>

                    {{-- Bento 5: Manajemen Laboratorium (4 cols) --}}
                    <div class="md:col-span-6 lg:col-span-4 bg-glass-card rounded-[32px] p-8 border border-white/10 flex flex-col justify-between group">
                        <div class="space-y-5">
                            <div class="w-14 h-14 rounded-2xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white mb-2">Manajemen Lab & Praktik</h3>
                                <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                                    Kelola inventaris lab kejuruan, log penggunaan ruang praktik, dan peminjaman alat praktik siswa dengan pencatatan tertib.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6">
                            <div class="text-xs font-mono text-gray-400 bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                Logbook Praktik Siap Audit
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- INTERACTIVE SYSTEM MODULES EXPLORER                                        --}}
        {{-- ========================================================================= --}}
        <section id="modules" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-white/5 bg-gradient-to-b from-black/40 to-transparent">
            <div class="max-w-7xl mx-auto">
                
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-600/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase tracking-widest">
                        Eksplorasi Modul
                    </div>
                    <h2 class="heading-font text-3xl sm:text-5xl font-black text-white leading-tight">
                        Dirancang untuk Setiap Kebutuhan <br>
                        <span class="gradient-text-red">Sarana & Prasarana</span>
                    </h2>
                </div>

                {{-- Interactive Tabs --}}
                <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-4 mb-12">
                    <button @click="activeTab = 'inventaris'"
                            :class="activeTab === 'inventaris' ? 'bg-red-600 text-white shadow-lg shadow-red-900/30' : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Inventaris & Labeling</span>
                    </button>

                    <button @click="activeTab = 'perawatan'"
                            :class="activeTab === 'perawatan' ? 'bg-red-600 text-white shadow-lg shadow-red-900/30' : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                        <span>Pemeliharaan & Laporan</span>
                    </button>

                    <button @click="activeTab = 'kendaraan'"
                            :class="activeTab === 'kendaraan' ? 'bg-red-600 text-white shadow-lg shadow-red-900/30' : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Peminjaman & Kendaraan</span>
                    </button>

                    <button @click="activeTab = 'ttd'"
                            :class="activeTab === 'ttd' ? 'bg-red-600 text-white shadow-lg shadow-red-900/30' : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>E-Signature & Verifikasi</span>
                    </button>
                </div>

                {{-- Tab Content Display --}}
                <div class="bg-glass-card rounded-[36px] p-8 sm:p-12 border border-white/10">
                    
                    {{-- Tab 1 Content --}}
                    <div x-show="activeTab === 'inventaris'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div class="space-y-6">
                            <h3 class="text-2xl sm:text-3xl font-black text-white">Pendataan Presisi, Audit Cepat & Labeling QR</h3>
                            <p class="text-gray-300 text-sm sm:text-base leading-relaxed">
                                Setiap aset dikelompokkan berdasarkan kategori, gedung, ruangan, fungsi barang, sumber dana, hingga penanggung jawab. Fitur mutasi massal memudahkan pemindahan lokasi aset dalam hitungan detik.
                            </p>
                            <ul class="space-y-3 text-xs sm:text-sm text-gray-300">
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Ekspor & Impor massal format Excel dan ringkasan PDF resmi.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Riwayat mutasi dan penempatan aset tercatat dalam log audit.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Katalog buku perpustakaan & aset khusus kejuruan.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-6 rounded-2xl bg-black/40 border border-white/10 space-y-4">
                            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                <span class="text-xs font-bold text-white uppercase">Preview Label Aset</span>
                                <span class="text-[10px] font-mono text-red-400">YPT Standard</span>
                            </div>
                            <div class="p-4 rounded-xl bg-white text-black flex items-center gap-4 shadow-lg">
                                <div class="w-16 h-16 bg-gray-900 rounded-lg flex items-center justify-center text-white font-mono text-[10px] text-center p-1">
                                    [QR CODE]
                                </div>
                                <div class="flex-1 text-left">
                                    <div class="text-[11px] font-extrabold uppercase tracking-tight text-red-600">{{ $schoolName }}</div>
                                    <div class="text-xs font-bold text-gray-900">PC Server Lab Komputer 01</div>
                                    <div class="text-[10px] font-mono text-gray-600">KODE: AST-2026-00412</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 2 Content --}}
                    <div x-show="activeTab === 'perawatan'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div class="space-y-6">
                            <h3 class="text-2xl sm:text-3xl font-black text-white">Preventive Maintenance & Tiket Kerusakan</h3>
                            <p class="text-gray-300 text-sm sm:text-base leading-relaxed">
                                Kelola jadwal servis berkala agar fasilitas selalu dalam kondisi optimal. Siapapun dapat melaporkan kerusakan aset hanya dengan memindai kode QR yang menempel pada barang.
                            </p>
                            <ul class="space-y-3 text-xs sm:text-sm text-gray-300">
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Notifikasi jadwal servis & kalibrasi berkala.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Unggah foto bukti kerusakan dan tracking status perbaikan.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Laporan BAPH (Berita Acara Penghapusan/Disposal) otomatis.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-6 rounded-2xl bg-black/40 border border-white/10 space-y-3">
                            <div class="flex justify-between items-center text-xs font-bold text-gray-300 border-b border-white/10 pb-3">
                                <span>Tiket Pelaporan Terkini</span>
                                <span class="text-red-400 font-mono">Real-Time</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5 flex justify-between items-center">
                                <div>
                                    <div class="text-xs font-bold text-white">AC Ruang Guru Mati Total</div>
                                    <div class="text-[10px] text-gray-400">Dilaporkan oleh: Bpk. Ahmad (Guru)</div>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-1 bg-amber-500/20 text-amber-400 rounded-lg">Proses Pengecekan</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5 flex justify-between items-center">
                                <div>
                                    <div class="text-xs font-bold text-white">Ganti Lampu Proyektor Lab 2</div>
                                    <div class="text-[10px] text-gray-400">Teknisi: Tim Sarpras Internal</div>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg">Selesai Diperbaiki</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 3 Content --}}
                    <div x-show="activeTab === 'kendaraan'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div class="space-y-6">
                            <h3 class="text-2xl sm:text-3xl font-black text-white">Sirkulasi Peminjaman Barang & Armada</h3>
                            <p class="text-gray-300 text-sm sm:text-base leading-relaxed">
                                Tata tertib peminjaman aset untuk kegiatan sekolah, ekstrakurikuler, dan dinas luar. Alur persetujuan terstruktur dari Wakasek hingga Kepala Sekolah.
                            </p>
                            <ul class="space-y-3 text-xs sm:text-sm text-gray-300">
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Pencatatan serah terima (Checkout & Checkin) dengan BAST digital.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Monitoring ketersediaan unit kendaraan dan jadwal pemakaian.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Validasi pengembalian barang tepat waktu.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-6 rounded-2xl bg-black/40 border border-white/10 space-y-4">
                            <div class="text-xs font-bold text-gray-300 border-b border-white/10 pb-3 flex justify-between items-center">
                                <span>Status Armada Operasional</span>
                                <span class="text-green-400 font-bold text-[10px]">Ready</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.04]">
                                    <div class="text-xs font-bold text-white">Mobil Dinas Avanza (BE 1012 XY)</div>
                                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">Tersedia</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.04]">
                                    <div class="text-xs font-bold text-white">Mikrobus Elf Sekolah (BE 7001 YP)</div>
                                    <span class="text-[10px] font-bold text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded">Sedang Dinas</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 4 Content --}}
                    <div x-show="activeTab === 'ttd'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div class="space-y-6">
                            <h3 class="text-2xl sm:text-3xl font-black text-white">Integritas Dokumen & Tanda Tangan Elektronik</h3>
                            <p class="text-gray-300 text-sm sm:text-base leading-relaxed">
                                Keamanan dokumen tingkat tinggi. Setiap dokumen resmi yang diterbitkan sistem memiliki hash kriptografi dan barcode verifikasi publik untuk mencegah pemalsuan tanda tangan.
                            </p>
                            <ul class="space-y-3 text-xs sm:text-sm text-gray-300">
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Tanda tangan digital terhubung langsung dengan akun pejabat berwenang.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Halaman verifikasi publik untuk mengecek keaslian PDF tanpa perlu login.</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-xs">✓</span>
                                    <span>Otomatisasi pencantuman Kop Surat resmi dan nomor registrasi.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-6 rounded-2xl bg-black/40 border border-white/10 space-y-4 text-center">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div class="text-sm font-bold text-white">Sertifikat Digital Otentik</div>
                            <p class="text-xs text-gray-400">Dokumen dijamin aman dari manipulasi isi dan pemalsuan paraf.</p>
                            <a href="{{ route('public.verify-document') }}" class="inline-block text-xs font-bold text-red-400 hover:text-red-300 underline">
                                Coba Verifikasi Dokumen Publik &rarr;
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- WORKFLOW / ALUR KERJA SISTEM                                               --}}
        {{-- ========================================================================= --}}
        <section id="workflow" class="py-28 px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-7xl mx-auto">
                
                <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-600/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase tracking-widest">
                        Alur Kerja Efisien
                    </div>
                    <h2 class="heading-font text-3xl sm:text-5xl font-black text-white leading-tight">
                        4 Langkah Mudah <br>
                        <span class="gradient-text-red">Siklus Hidup Aset</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                    
                    {{-- Step 1 --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 space-y-4 relative group">
                        <div class="w-12 h-12 rounded-2xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-400 font-mono font-black text-lg">
                            01
                        </div>
                        <h3 class="text-lg font-black text-white">Input & Pengadaan</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Integrasi anggaran dari RAB/RKAS, penerimaan barang dari rekanan vendor, dan pendaftaran spesifikasi detail.
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 space-y-4 relative group">
                        <div class="w-12 h-12 rounded-2xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-400 font-mono font-black text-lg">
                            02
                        </div>
                        <h3 class="text-lg font-black text-white">Labeling & QR Code</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Generate nomor identifikasi unik otomatis dan cetak label barcode/QR code tahan lama untuk ditempel pada fisik barang.
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 space-y-4 relative group">
                        <div class="w-12 h-12 rounded-2xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-400 font-mono font-black text-lg">
                            03
                        </div>
                        <h3 class="text-lg font-black text-white">Distribusi & BAST</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Serah terima ke ruangan atau pegawai penanggung jawab dengan bukti dokumen BAST digital yang ditandatangani sah.
                        </p>
                    </div>

                    {{-- Step 4 --}}
                    <div class="bg-glass-card p-6 rounded-3xl border border-white/10 space-y-4 relative group">
                        <div class="w-12 h-12 rounded-2xl bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-400 font-mono font-black text-lg">
                            04
                        </div>
                        <h3 class="text-lg font-black text-white">Audit & Perawatan</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Monitoring kondisi riil, pemeliharaan rutin terjadwal, pelaporan kerusakan, hingga penghapusan (disposal) aset.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- CALL TO ACTION (CTA)                                                      --}}
        {{-- ========================================================================= --}}
        <section class="py-24 px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-7xl mx-auto">
                <div class="relative rounded-[40px] overflow-hidden p-10 sm:p-16 md:p-20 text-center border border-red-500/30 shadow-2xl bg-red-gradient">
                    <div class="absolute inset-0 grid-pattern opacity-20"></div>
                    <div class="relative z-10 max-w-3xl mx-auto space-y-8">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-black uppercase tracking-widest">
                            Siap Memulai Transformasi?
                        </div>
                        <h2 class="heading-font text-3xl sm:text-5xl md:text-6xl font-black text-white leading-tight">
                            Kelola Seluruh Aset Sekolah <br> Lebih Cepat, Rapi & Transparan
                        </h2>
                        <p class="text-white/80 text-sm sm:text-base font-normal leading-relaxed">
                            Akses sistem sekarang untuk memulai pendataan inventaris, monitoring sarana prasarana, dan pengajuan layanan peminjaman.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                   class="w-full sm:w-auto px-10 py-4 bg-white text-red-600 font-black text-sm rounded-2xl shadow-2xl hover:bg-gray-100 transform hover:-translate-y-1 transition-all">
                                    Buka Dashboard Sistem
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="w-full sm:w-auto px-10 py-4 bg-white text-red-600 font-black text-sm rounded-2xl shadow-2xl hover:bg-gray-100 transform hover:-translate-y-1 transition-all">
                                    Masuk ke Aplikasi
                                </a>
                                <a href="{{ route('public.verify-document') }}" 
                                   class="w-full sm:w-auto px-10 py-4 bg-red-950/40 hover:bg-red-950/60 border border-white/30 text-white font-bold text-sm rounded-2xl transition-all">
                                    Verifikasi Dokumen PDF
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- Modern Footer --}}
    <footer class="border-t border-white/10 bg-black/60 pt-16 pb-12 px-4 sm:px-6 lg:px-8 text-xs text-gray-400">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                
                {{-- Col 1: Brand & Bio --}}
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-600 text-white font-bold">
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" class="h-6 w-auto object-contain brightness-0 invert" alt="{{ $appName }}">
                            @else
                                S
                            @endif
                        </div>
                        <span class="heading-font font-black text-lg text-white">{{ $appName }}</span>
                    </div>
                    <p class="text-gray-400 max-w-sm leading-relaxed">
                        Sistem Informasi Manajemen Aset & Sarana Prasarana Terintegrasi {{ $schoolName }}. Mendukung efisiensi, akuntabilitas, dan keamanan inventaris sekolah.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Sistem Berjalan Normal & Terproteksi</span>
                    </div>
                </div>

                {{-- Col 2: Nav Links --}}
                <div class="md:col-span-3 space-y-3">
                    <div class="text-white font-bold uppercase tracking-wider text-xs">Navigasi Cepat</div>
                    <ul class="space-y-2">
                        <li><a href="#hero" class="hover:text-red-400 transition-colors">Beranda Utama</a></li>
                        <li><a href="#features" class="hover:text-red-400 transition-colors">Fitur Unggulan</a></li>
                        <li><a href="#modules" class="hover:text-red-400 transition-colors">Modul Sistem</a></li>
                        <li><a href="#workflow" class="hover:text-red-400 transition-colors">Alur Pengelolaan</a></li>
                        <li><a href="{{ route('public.verify-document') }}" class="hover:text-red-400 transition-colors">Verifikasi Dokumen Publik</a></li>
                    </ul>
                </div>

                {{-- Col 3: Layanan Publik --}}
                <div class="md:col-span-4 space-y-3">
                    <div class="text-white font-bold uppercase tracking-wider text-xs">Layanan & Bantuan</div>
                    <p class="text-gray-400 leading-relaxed">
                        Butuh bantuan seputar pendataan inventaris atau pelaporan kerusakan aset? Hubungi tim Sarpras sekolah.
                    </p>
                    <div class="pt-2 flex flex-col gap-1.5 text-gray-300">
                        <div class="flex items-center gap-2">
                            <span class="text-red-500">📍</span>
                            <span>{{ $schoolName }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-red-500">🔒</span>
                            <span>Yayasan Pendidikan Telkom (YPT)</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="pt-8 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div>
                    &copy; {{ date('Y') }} {{ $schoolName }}. Hak Cipta Dilindungi Undang-Undang.
                </div>
                <div class="text-gray-400 font-mono text-[11px]">
                    Powered by <span class="text-white font-semibold">Laravel</span> & <span class="text-red-500 font-semibold">{{ $appName }} v2.0</span>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
