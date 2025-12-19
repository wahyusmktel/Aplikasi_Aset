<nav class="sticky top-0 z-40 w-full bg-white/60 dark:bg-gray-950/60 backdrop-blur-xl border-b border-gray-100 dark:border-gray-800 transition-all duration-300">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left side (Mobile Toggle) -->
            <div class="flex items-center md:hidden">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary-600 focus:outline-none transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <a href="{{ route('dashboard') }}" class="ml-4 flex items-center">
                    <x-application-logo class="block h-7 w-auto fill-current text-primary-600" />
                </a>
            </div>

            <!-- Page Title (Optional) -->
            <div class="hidden md:block">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">
                    {{ str_replace('.', ' / ', request()->route()->getName()) }}
                </h2>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                {{-- Notification / Search could go here --}}
                
                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200 focus:outline-none bg-gray-50 dark:bg-gray-900 rounded-full pl-1 pr-3 py-1 border border-gray-100 dark:border-gray-800">
                            <div class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold mr-2 text-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                            <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                {{ __('Profile') }}
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="flex items-center text-red-600">
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
<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

<!-- Mobile Sidebar -->
<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 shadow-2xl transition-transform duration-300 ease-in-out md:hidden flex flex-col">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100 dark:border-gray-800">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <x-application-logo class="block h-8 w-auto fill-current text-primary-600" />
            <span class="ml-3 font-bold text-xl tracking-tight text-gray-800 dark:text-white uppercase">ASET <span class="text-primary-600">APP</span></span>
        </a>
        <button @click="sidebarOpen = false" class="text-gray-500 hover:text-primary-600 focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    
    <div class="flex-grow py-6 overflow-y-auto no-scrollbar">
        {{-- Re-use sidebar content if needed, for simplicity I'll just suggest moving sidebar navigation to a component --}}
        @include('layouts.sidebar-content')
    </div>
</div>
