@props(['active' => false, 'title', 'icon'])

<div x-data="{ open: @js($active) }" class="relative">
    <button @click="open = !open" 
        :class="open ? 'text-primary-600 dark:text-primary-400 bg-gray-50 dark:bg-gray-800' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'"
        class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
        <span class="flex items-center">
            @if($icon == 'collection')
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            @elseif($icon == 'database')
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
            @endif
            <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                {{ $title }}
            </span>
        </span>
        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
    </button>

    <div x-show="open && sidebarOpen" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 -translate-y-2" 
        x-transition:enter-end="opacity-100 translate-y-0"
        class="mt-1 ml-9 space-y-1">
        {{ $slot }}
    </div>
</div>
