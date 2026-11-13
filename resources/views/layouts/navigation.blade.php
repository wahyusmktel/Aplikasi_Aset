<nav class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-xl border-b border-slate-200/80 transition-all duration-300">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left side (Sidebar Toggle & Page Title) -->
            <div class="flex items-center gap-3">
                <!-- Mobile Toggle -->
                <div class="flex items-center md:hidden">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-rose-600 focus:outline-none transition-colors p-1.5 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="ml-3 flex items-center">
                        <x-application-logo class="block h-7 w-auto fill-current text-rose-600" />
                    </a>
                </div>

                <!-- Desktop Sidebar Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen"
                    title="Buka / Tutup Sidebar"
                    class="hidden md:flex items-center justify-center p-2 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-xl transition-all focus:outline-none">
                    <svg x-show="sidebarOpen" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <svg x-show="!sidebarOpen" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="hidden md:flex items-center">
                    <span class="h-4 w-px bg-slate-200 mr-3"></span>
                    <h2 class="text-sm font-semibold text-slate-600 capitalize">
                        {{ str_replace('.', ' / ', request()->route()?->getName() ?? 'Dashboard') }}
                    </h2>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-slate-600 hover:text-rose-600 transition-all duration-200 focus:outline-none bg-slate-100/70 hover:bg-slate-200/60 rounded-full pl-1.5 pr-3 py-1 border border-slate-200/60">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center text-white font-bold mr-2 text-[10px] shadow-2xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:inline text-xs font-semibold">{{ Auth::user()->name }}</span>
                            <svg class="ms-1.5 h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center text-xs font-medium text-slate-700">
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                {{ __('Profile') }}
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="flex items-center text-xs font-medium text-rose-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    {{ __('Log Out') }}
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs md:hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

<!-- Mobile Sidebar -->
<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-2xl transition-transform duration-300 ease-in-out md:hidden flex flex-col border-r border-slate-200">
    <div class="flex items-center justify-between h-16 px-5 border-b border-slate-100">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <x-application-logo class="block h-7 w-auto fill-current text-rose-600" />
            <span class="font-bold text-base tracking-tight text-slate-800">ASET <span class="text-rose-600">APP</span></span>
        </a>
        <button @click="sidebarOpen = false" class="text-slate-400 hover:text-rose-600 focus:outline-none transition-colors p-1.5 rounded-lg hover:bg-slate-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    
    <div class="flex-grow py-3 px-3 overflow-y-auto no-scrollbar">
        @include('layouts.sidebar-content')
    </div>

    <!-- Mobile Footer: User Profile -->
    <div class="p-3 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center text-white font-bold text-xs shadow-xs">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="ml-2.5 min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>
