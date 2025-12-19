<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'StellaLog\'s - Manajemen Aset') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800,900" rel="stylesheet" />

    {{-- Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom Styles for Animations & Gradients --}}
    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0c0000;
        }

        .gradient-text {
            background: linear-gradient(135deg, #ff4d4d 0%, #ff0a0a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .bg-red-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #7f1d1d 100%);
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .glass:hover {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-delayed {
            animation: float 8s ease-in-out infinite;
            animation-delay: 2s;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(60px);
        }

        .mouse-glow {
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.05) 0%, transparent 70%);
            position: fixed;
            pointer-events: none;
            z-index: 10;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            display: none;
        }
    </style>
</head>

<body x-data="{ scrolled: false, mouseX: 0, mouseY: 0 }" 
      @scroll.window="scrolled = (window.pageYOffset > 20)"
      @mousemove="mouseX = $event.clientX; mouseY = $event.clientY; $refs.glow.style.left = mouseX + 'px'; $refs.glow.style.top = mouseY + 'px'; $refs.glow.style.display = 'block';"
      class="antialiased text-gray-200 selection:bg-red-500 selection:text-white overflow-x-hidden">

    {{-- Mouse Glow Effect --}}
    <div x-ref="glow" class="mouse-glow"></div>

    {{-- Background Decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blob top-[-10%] right-[-10%]"></div>
        <div class="blob bottom-[-10%] left-[-10%]"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03]"></div>
    </div>

    {{-- Navbar --}}
    <nav :class="scrolled ? 'glass py-3 border-b' : 'py-6'" 
         class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-red-600 blur-lg opacity-40 group-hover:opacity-60 transition-opacity"></div>
                    <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white shadow-xl shadow-red-900/20 transform group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>
                <span class="font-black text-2xl tracking-tighter text-white">Stella<span class="text-red-600">Log's</span></span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="#features" class="text-sm font-bold text-gray-400 hover:text-white transition-colors">Fitur</a>
                <a href="#about" class="text-sm font-bold text-gray-400 hover:text-white transition-colors">Tentang</a>
                <a href="#stats" class="text-sm font-bold text-gray-400 hover:text-white transition-colors">Statistik</a>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="relative group px-6 py-2.5 bg-red-600 text-white text-sm font-black rounded-xl overflow-hidden shadow-xl shadow-red-900/20">
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-400 hover:text-white mr-4">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="hidden sm:block glass px-6 py-2.5 text-sm font-black rounded-xl hover:bg-white hover:text-black transition-all">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <main>
        {{-- Hero Section --}}
        <section class="relative min-h-screen flex items-center pt-20 px-6 overflow-hidden">
            <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="relative z-10 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 border border-red-500/20">
                        <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-xs font-black uppercase tracking-widest text-red-400">Integrated Asset Management v2.0</span>
                    </div>
                    
                    <h1 class="text-5xl md:text-7xl font-black leading-[1.1] mb-8 text-white">
                        Kuasai Aset Anda <br>
                        <span class="gradient-text">Tanpa Batas</span>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-gray-400 mb-12 max-w-xl mx-auto lg:mx-0 leading-relaxed font-light">
                        Manajemen aset modern untuk <span class="text-white font-bold tracking-tight">SMK Telkom Lampung</span>. 
                        Presisi dalam pendataan, efisien dalam pengawasan, dan transparan dalam pelaporan.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="w-full sm:w-auto px-10 py-5 bg-red-600 text-white font-black rounded-2xl shadow-2xl shadow-red-900/40 hover:bg-red-500 transform hover:-translate-y-1 transition-all">
                                Buka Dashboard Utama
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto px-10 py-5 bg-red-600 text-white font-black rounded-2xl shadow-2xl shadow-red-900/40 hover:bg-red-500 transform hover:-translate-y-1 transition-all">
                                Mulai Sekarang
                            </a>
                            <a href="#features" class="w-full sm:w-auto glass px-10 py-5 font-black rounded-2xl hover:bg-white/5 transition-all text-center">
                                Lihat Demo
                            </a>
                        @endauth
                    </div>
                    
                    <div class="mt-16 flex items-center justify-center lg:justify-start gap-8 opacity-40">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Logo_Telkom_University.svg/1200px-Logo_Telkom_University.svg.png" 
                             class="h-8 md:h-12 object-contain filter grayscale invert" alt="Partner">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/23/Logo_YPT.png" 
                             class="h-8 md:h-12 object-contain filter grayscale invert px-4" alt="Partner">
                    </div>
                </div>

                <div class="relative hidden lg:block">
                    <div class="absolute -inset-10 bg-red-600/20 blur-[100px] rounded-full"></div>
                    <div class="relative animate-float">
                        <div class="glass p-8 rounded-[40px] border-white/10 shadow-3xl">
                            <div class="p-6 bg-gray-950 rounded-3xl border border-white/5 overflow-hidden">
                                {{-- Mockup Content --}}
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex gap-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500/30"></div>
                                        <div class="w-3 h-3 rounded-full bg-red-500/30"></div>
                                        <div class="w-3 h-3 rounded-full bg-red-500/30"></div>
                                    </div>
                                    <div class="bg-red-600/10 px-4 py-1.5 rounded-full border border-red-500/20 text-[10px] font-black uppercase text-red-500">Live Status</div>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex items-center gap-4 p-4 glass rounded-2xl">
                                        <div class="w-12 h-12 rounded-xl bg-red-600/20 flex items-center justify-center border border-red-500/30">
                                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="h-2 w-24 bg-white/10 rounded mb-2"></div>
                                            <div class="h-2 w-16 bg-white/5 rounded"></div>
                                        </div>
                                        <div class="text-xs font-bold text-green-500">98% Active</div>
                                    </div>
                                    <div class="flex items-center gap-4 p-4 glass rounded-2xl opacity-50 translate-x-4">
                                        <div class="w-12 h-12 rounded-xl bg-red-600/20 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="h-2 w-20 bg-white/10 rounded mb-2"></div>
                                            <div class="h-2 w-12 bg-white/5 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-4 bg-red-600 rounded-2xl shadow-xl shadow-red-900/20">
                                            <div class="text-xs font-bold text-white/60 uppercase mb-2">Total Items</div>
                                            <div class="text-3xl font-black text-white">4,281</div>
                                        </div>
                                        <div class="p-4 glass rounded-2xl">
                                            <div class="text-xs font-bold text-white/40 uppercase mb-2">Maintenance</div>
                                            <div class="text-3xl font-black text-white">12</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Floating Elements --}}
                        <div class="absolute -top-10 -right-10 animate-float-delayed">
                             <div class="glass p-4 rounded-2xl rotate-12 shadow-2xl">
                                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Features Section --}}
        <section id="features" class="py-32 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-sm font-black text-red-500 uppercase tracking-[0.3em] mb-4">Fitur Utama</h2>
                    <p class="text-4xl md:text-5xl font-black text-white">Semua Kendali di <span class="gradient-text">Genggaman</span></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group glass p-10 rounded-[40px] transition-all hover:-translate-y-2">
                        <div class="w-16 h-16 bg-red-600/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-red-600 transition-all">
                            <svg class="w-8 h-8 text-red-500 group-hover:text-white transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black mb-4 text-white">Inventaris Cerdas</h3>
                        <p class="text-gray-400 leading-relaxed">Sistem label otomatis dan detail spesifikasi barang yang mendalam memudahkan identifikasi aset sekolah dalam hitungan detik.</p>
                    </div>

                    <div class="group glass p-10 rounded-[40px] transition-all hover:-translate-y-2 border-red-500/10">
                        <div class="w-16 h-16 bg-red-600/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-red-600 transition-all">
                            <svg class="w-8 h-8 text-red-500 group-hover:text-white transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black mb-4 text-white">Laporan Real-time</h3>
                        <p class="text-gray-400 leading-relaxed">Dapatkan visualisasi data kondisi aset saat ini secara langsung. Pengambilan keputusan jadi lebih cepat dan berbasis fakta.</p>
                    </div>

                    <div class="group glass p-10 rounded-[40px] transition-all hover:-translate-y-2">
                        <div class="w-16 h-16 bg-red-600/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-red-600 transition-all">
                            <svg class="w-8 h-8 text-red-500 group-hover:text-white transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black mb-4 text-white">Tracking BAST</h3>
                        <p class="text-gray-400 leading-relaxed">Log serah terima dari vendor hingga ke tangan unit terjaga dengan aman. Digitalisasi Berita Acara yang meminimalisir kehilangan.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-32 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="relative overflow-hidden bg-red-gradient rounded-[50px] p-12 md:p-24 text-center">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/asfalt-dark.png')] opacity-10"></div>
                    <div class="relative z-10">
                        <h2 class="text-4xl md:text-6xl font-black text-white mb-8">Siap Mengelola Aset <br> Lebih Profesional?</h2>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                             <a href="{{ route('login') }}" 
                               class="w-full sm:w-auto px-12 py-6 bg-white text-red-600 font-black rounded-2xl shadow-2xl hover:bg-gray-100 transform hover:-translate-y-1 transition-all">
                                Login Ke Sistem
                            </a>
                            <a href="{{ route('register') }}" 
                               class="w-full sm:w-auto px-12 py-6 border-2 border-white/30 text-white font-black rounded-2xl hover:bg-white/10 transition-all">
                                Hubungi Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-20 px-6 border-t border-white/5">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-12">
            <div>
                 <a href="#" class="flex items-center gap-3 mb-6">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-white shadow-xl shadow-red-900/20 leading-none font-bold">S</div>
                    <span class="font-black text-xl tracking-tighter text-white">Stella<span class="text-red-600">Log's</span></span>
                </a>
                <p class="text-sm text-gray-500 max-w-xs leading-relaxed">The next generation of asset management solutions for vocational high schools.</p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-x-12 gap-y-6 text-sm font-bold text-gray-400">
                <a href="#" class="hover:text-red-500 transition-colors">Kebijakan Privasi</a>
                <a href="#" class="hover:text-red-500 transition-colors">Term of Service</a>
                <a href="#" class="hover:text-red-500 transition-colors">Bantuan</a>
            </div>

            <div class="text-sm text-gray-500 font-medium">
                &copy; {{ date('Y') }} SMK Telkom Lampung. Proudly powered by <span class="text-white">Laravel</span>
            </div>
        </div>
    </footer>

    {{-- Script for Alpine.js & Custom Logic --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
