@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Menampilkan
            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm text-slate-400 dark:border-white/10 dark:text-slate-500" style="min-width:2.25rem">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5" style="min-width:2.25rem" aria-label="Halaman sebelumnya">‹</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center px-1 text-sm text-slate-400" style="min-width:2.25rem">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                aria-current="page"
                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-sm font-semibold"
                                style="min-width:2.25rem;background:#0891b2;color:#ffffff;border:1px solid #0891b2"
                            >{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-white/5" style="min-width:2.25rem" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5" style="min-width:2.25rem" aria-label="Halaman berikutnya">›</a>
            @else
                <span class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm text-slate-400 dark:border-white/10 dark:text-slate-500" style="min-width:2.25rem">›</span>
            @endif
        </div>
    </nav>
@endif
