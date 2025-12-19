<div :class="sidebarOpen ? 'w-64' : 'w-20'" class="relative h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out hidden md:flex flex-col z-50">
    <!-- Header/Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-application-logo class="block h-8 w-auto fill-current text-primary-600 dark:text-primary-400" />
                <span class="ml-3 font-bold text-xl tracking-tight text-gray-800 dark:text-white">ASET <span class="text-primary-600">APP</span></span>
            </a>
        </div>
        <div class="flex items-center" x-show="!sidebarOpen">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-8 w-auto fill-current text-primary-600 dark:text-primary-400" />
            </a>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary-600 transition-colors focus:outline-none">
            <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
            <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <div class="flex-grow py-6 overflow-y-auto no-scrollbar">
        @include('layouts.sidebar-content')
    </div>

    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        <div class="flex items-center" x-show="sidebarOpen">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <div class="ml-3 truncate">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div x-show="!sidebarOpen" class="flex justify-center">
            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
    </div>
</div>
