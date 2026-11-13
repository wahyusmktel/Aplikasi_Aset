@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        if ($lastPage <= 3) {
            $start = 1;
            $end = $lastPage;
        } elseif ($currentPage <= 3) {
            $start = 1;
            $end = 3;
        } elseif ($currentPage >= $lastPage) {
            $start = max(1, $lastPage - 2);
            $end = $lastPage;
        } else {
            $start = max(1, $currentPage - 1);
            $end = min($lastPage, $currentPage + 1);
        }
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center">
        <div class="inline-flex items-center -space-x-px rounded-xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-2.5 text-slate-300 bg-slate-50/60 cursor-not-allowed text-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-2.5 text-slate-600 bg-white hover:bg-slate-50 hover:text-rose-600 text-xs transition-colors focus:z-10 focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Numbers (Only 3 numbers max: 1, 2, 3...) --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <span aria-current="page" class="relative z-10 inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-bold text-white bg-rose-600 shadow-xs">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 hover:text-rose-600 transition-colors focus:z-10 focus:outline-none">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-2.5 text-slate-600 bg-white hover:bg-slate-50 hover:text-rose-600 text-xs transition-colors focus:z-10 focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-2.5 text-slate-300 bg-slate-50/60 cursor-not-allowed text-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
