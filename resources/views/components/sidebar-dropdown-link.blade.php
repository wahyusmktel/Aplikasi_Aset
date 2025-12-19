@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block py-2 px-4 text-sm font-medium text-primary-600 dark:text-primary-400 rounded-lg bg-primary-50 dark:bg-primary-900/20 transition-colors'
            : 'block py-2 px-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
