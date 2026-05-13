<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
            <div class="inline-flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-300 flex items-center border-r border-gray-100 dark:border-gray-700 cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 15l-3-3m0 0l3-3m-3 3h8M12 21a9 9 0 110-18 9 9 0 010 18z"/></svg>
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-primary-blue hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center border-r border-gray-100 dark:border-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 15l-3-3m0 0l3-3m-3 3h8M12 21a9 9 0 110-18 9 9 0 010 18z"/></svg>
                        Previous
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-4 py-3 text-[10px] font-black text-gray-300 border-r border-gray-100 dark:border-gray-700">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-5 py-3 bg-primary-blue/5 dark:bg-primary-blue/10 text-primary-blue text-[10px] font-black border-r border-gray-100 dark:border-gray-700">{{ $page }}</span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="px-5 py-3 text-[10px] font-black text-gray-400 hover:text-primary-blue hover:bg-gray-50 dark:hover:bg-gray-900 border-r border-gray-100 dark:border-gray-700 transition-all">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-primary-blue hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center">
                        Next
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                @else
                    <span class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-gray-300 flex items-center cursor-not-allowed">
                        Next
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
