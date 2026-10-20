@php
    $pageName = $paginator->getPageName();
    $lastPage = $paginator->lastPage();
    $currentPage = $paginator->currentPage();
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 my-2 w-full">
            {{-- Info Count --}}
            <div class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1 text-center sm:text-left">
                Menampilkan <span class="font-bold text-slate-700 dark:text-slate-200">{{ $paginator->firstItem() ?? 0 }}</span>
                sampai <span class="font-bold text-slate-700 dark:text-slate-200">{{ $paginator->lastItem() ?? 0 }}</span>
                dari <span class="font-bold text-slate-700 dark:text-slate-200">{{ $paginator->total() }}</span> data
            </div>

            {{-- Pagination Controls --}}
            <div class="inline-flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs order-1 sm:order-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 sm:px-4 py-2 sm:py-2.5 text-[11px] font-bold text-slate-300 dark:text-slate-600 flex items-center border-r border-slate-200 dark:border-slate-800 cursor-not-allowed select-none">
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled" rel="prev"
                        class="px-3 sm:px-4 py-2 sm:py-2.5 text-[11px] font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center border-r border-slate-200 dark:border-slate-800">
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </button>
                @endif

                {{-- Page Numbers Logic --}}
                @php
                    $elements = [];
                    $formatRange = fn($range) => array_combine($range, array_fill(0, count($range), '#'));

                    if ($lastPage <= 7) {
                        $elements[] = $formatRange(range(1, $lastPage));
                    } else {
                        if ($currentPage <= 4) {
                            $elements[] = $formatRange(range(1, 5));
                            $elements[] = '...';
                            $elements[] = $formatRange([$lastPage]);
                        } elseif ($currentPage >= $lastPage - 3) {
                            $elements[] = $formatRange([1]);
                            $elements[] = '...';
                            $elements[] = $formatRange(range($lastPage - 4, $lastPage));
                        } else {
                            $elements[] = $formatRange([1]);
                            $elements[] = '...';
                            $elements[] = $formatRange(range($currentPage - 1, $currentPage + 1));
                            $elements[] = '...';
                            $elements[] = $formatRange([$lastPage]);
                        }
                    }
                @endphp

                @foreach ($elements as $element)
                    {{-- Separator --}}
                    @if (is_string($element))
                        <span class="px-3 py-2 sm:py-2.5 text-xs text-slate-400 dark:text-slate-600 border-r border-slate-200 dark:border-slate-800 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Page Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $currentPage)
                                <span wire:key="paginator-{{ $pageName }}-page-{{ $page }}"
                                    class="min-w-[36px] sm:min-w-[40px] px-2.5 py-2 sm:py-2.5 bg-blue-600 text-white text-xs font-bold flex items-center justify-center border-r border-slate-200 dark:border-slate-800 shadow-xs">
                                    {{ $page }}
                                </span>
                            @else
                                <button type="button" wire:key="paginator-{{ $pageName }}-page-{{ $page }}"
                                    wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                    class="min-w-[36px] sm:min-w-[40px] px-2.5 py-2 sm:py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 border-r border-slate-200 dark:border-slate-800 transition-colors flex items-center justify-center">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled" rel="next"
                        class="px-3 sm:px-4 py-2 sm:py-2.5 text-[11px] font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center">
                        <span class="hidden sm:inline">Berikutnya</span>
                        <svg class="w-4 h-4 sm:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @else
                    <span class="px-3 sm:px-4 py-2 sm:py-2.5 text-[11px] font-bold text-slate-300 dark:text-slate-600 flex items-center cursor-not-allowed select-none">
                        <span class="hidden sm:inline">Berikutnya</span>
                        <svg class="w-4 h-4 sm:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
