<div :class="sidebarOpen ? 'w-64' : 'w-20'" class="relative h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out hidden md:flex flex-col z-50">
    <!-- Header/Logo -->
    <div class="flex items-center h-16 border-b border-gray-100 dark:border-gray-800 transition-all duration-300"
         :class="sidebarOpen ? 'px-6 justify-start' : 'justify-center px-0'">
        <div class="flex items-center" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-application-logo class="block h-8 w-auto fill-current text-primary-600 dark:text-primary-400" />
                <span class="ml-3 font-bold text-xl tracking-tight text-gray-800 dark:text-white whitespace-nowrap">ASET <span class="text-primary-600">APP</span></span>
            </a>
        </div>
        <div class="flex items-center justify-center" x-show="!sidebarOpen">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
                <x-application-logo class="block h-8 w-auto fill-current text-primary-600 dark:text-primary-400" />
            </a>
        </div>
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
