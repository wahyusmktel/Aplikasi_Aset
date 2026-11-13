<div :class="sidebarOpen ? 'w-64' : 'w-20'" class="relative h-screen bg-white border-r border-slate-200/80 transition-all duration-300 ease-in-out hidden md:flex flex-col z-50 shadow-xs">
    <!-- Header / Logo -->
    <div class="flex items-center h-16 border-b border-slate-100 transition-all duration-300 shrink-0"
         :class="sidebarOpen ? 'px-5 justify-between' : 'justify-center px-0'">
        <div class="flex items-center min-w-0" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 group">
                <x-application-logo class="block h-8 w-auto fill-current text-rose-600 transition-transform group-hover:scale-105" />
                <div class="flex flex-col">
                    <span class="font-extrabold text-base tracking-tight text-slate-900 whitespace-nowrap leading-none">ASET <span class="text-rose-600">APP</span></span>
                    <span class="text-[9px] font-semibold text-slate-600 tracking-wider uppercase mt-0.5">SMK Telkom</span>
                </div>
            </a>
        </div>
        <div class="flex items-center justify-center" x-show="!sidebarOpen">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center p-2 rounded-xl hover:bg-slate-100 transition-colors">
                <x-application-logo class="block h-7 w-auto fill-current text-rose-600" />
            </a>
        </div>
    </div>

    <!-- Navigation Links Scroll Area -->
    <div class="flex-grow py-3 px-3 overflow-y-auto no-scrollbar">
        @include('layouts.sidebar-content')
    </div>

    <!-- Footer Sidebar: User Profile -->
    <div class="p-3 border-t border-slate-100 bg-slate-50/50 shrink-0">
        <div class="flex items-center" x-show="sidebarOpen">
            <div class="flex-shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center text-white font-bold text-xs shadow-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="ml-2.5 min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-800 truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-slate-500 truncate mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div x-show="!sidebarOpen" class="flex justify-center">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center text-white font-bold text-xs shadow-xs" title="{{ Auth::user()->name }}">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>
    </div>
</div>
