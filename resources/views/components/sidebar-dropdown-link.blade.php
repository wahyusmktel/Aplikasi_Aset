@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'group relative flex items-center px-2.5 py-1.5 text-xs font-bold rounded-lg text-rose-700 bg-rose-50/90 shadow-2xs transition-all duration-150'
            : 'group relative flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100/70 transition-all duration-150';

$dotClasses = ($active ?? false)
            ? 'w-1.5 h-1.5 rounded-full mr-2 shrink-0 bg-rose-600 ring-2 ring-rose-200 transition-all'
            : 'w-1.5 h-1.5 rounded-full mr-2 shrink-0 bg-slate-300 group-hover:bg-slate-400 group-hover:scale-125 transition-all';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="{{ $dotClasses }}"></span>
    <span class="truncate flex-1 tracking-tight">{{ $slot }}</span>
</a>
