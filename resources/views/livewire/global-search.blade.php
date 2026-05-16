<div id="global-search-wrapper" x-data="{
    open: false,
    close() {
        this.open = false;
    }
}"
    @open-global-search.window="open = true; $nextTick(() => $refs.searchInput.focus())"
    @keydown.window.ctrl.k.prevent="open = true; $nextTick(() => $refs.searchInput.focus())"
    @keydown.window.escape="close()" class="relative">

    <style>
        @keyframes highlight-breath {
            0% {
                border-color: #fbbf24;
                box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.5);
            }

            50% {
                border-color: #f59e0b;
                box-shadow: 0 0 0 12px rgba(251, 191, 36, 0);
            }

            100% {
                border-color: #fbbf24;
                box-shadow: 0 0 0 0 rgba(251, 191, 36, 0);
            }
        }

        .animate-highlight-breath {
            animation: highlight-breath 1s ease-in-out infinite;
        }
    </style>

    <div x-show="open" x-cloak class="fixed inset-0 z-[2000] flex items-start justify-center pt-24 px-4 sm:px-6 lg:px-8">

        <!-- Backdrop -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.stop="close()"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

        <!-- Search Modal -->
        <div x-show="open" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop=""
            class="relative w-full max-w-2xl nb-card overflow-hidden">

            <!-- Search Input -->
            <div class="p-6 border-b-[var(--nb-border)] border-black dark:border-slate-700 bg-gray-50 dark:bg-slate-900 flex items-center gap-4">
                <svg class="w-6 h-6 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                <input x-ref="searchInput" wire:model.live.debounce.300ms="search" type="text"
                    placeholder="CARI MENU ATAU PRODUK..."
                    class="flex-1 bg-transparent border-none focus:ring-0 text-xl font-black uppercase tracking-tight text-slate-800 dark:text-white placeholder:text-slate-400">
                <div class="flex items-center gap-2">
                    <button @click.stop="close()"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors">
                        <svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="max-h-[60vh] overflow-y-auto no-scrollbar p-4">
                @if (empty($results))
                    @if (strlen($search) < 2)
                        <div class="py-12 px-6 text-center">
                            <div
                                class="w-16 h-16 bg-primary-blue/10 rounded-3xl flex items-center justify-center mx-auto mb-4 animate-bounce">
                                <svg class="w-8 h-8 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21 21-4.3-4.3" />
                                    <circle cx="11" cy="11" r="8" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">
                                Global Search</h3>
                            <p class="text-xs text-slate-500 mt-2">Cari produk, transaksi, atau menu navigasi...</p>

                            <!-- Quick Links -->
                            <div class="grid grid-cols-2 gap-4 mt-10 max-w-lg mx-auto">
                                <a href="{{ route('dashboard') }}"
                                    class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-gray-800/50 rounded-2xl hover:bg-primary-blue/5 hover:scale-105 transition-all group border border-transparent hover:border-primary-blue/20">
                                    <div
                                        class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center shadow-sm group-hover:text-primary-blue">
                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                            <polyline points="9 22 9 12 15 12 15 22" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <span
                                            class="block text-xs font-black text-slate-800 dark:text-white uppercase">Dashboard</span>
                                        <span
                                            class="block text-[9px] text-slate-400 uppercase font-bold tracking-widest mt-1">Overview</span>
                                    </div>
                                </a>
                                <a href="{{ route('kasir') }}"
                                    class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-gray-800/50 rounded-2xl hover:bg-primary-red/5 hover:scale-105 transition-all group border border-transparent hover:border-primary-red/20">
                                    <div
                                        class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center shadow-sm group-hover:text-primary-red">
                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="8" cy="21" r="1" />
                                            <circle cx="19" cy="21" r="1" />
                                            <path
                                                d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <span
                                            class="block text-xs font-black text-slate-800 dark:text-white uppercase">Kasir</span>
                                        <span
                                            class="block text-[9px] text-slate-400 uppercase font-bold tracking-widest mt-1">Buka
                                            POS</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Tidak ada hasil
                                untuk "{{ $search }}"</p>
                        </div>
                    @endif
                @else
                    <div class="space-y-1">
                        @foreach ($results as $result)
                            <a href="{{ $result['url'] }}"
                                class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-all group">
                                <div
                                    class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-800 group-hover:scale-110 group-hover:border-primary-blue/30 transition-all">
                                    @switch($result['icon'])
                                        @case('layout-dashboard')
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-blue"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                <polyline points="9 22 9 12 15 12 15 22" />
                                            </svg>
                                        @break

                                        @case('shopping-cart')
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-red"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="8" cy="21" r="1" />
                                                <circle cx="19" cy="21" r="1" />
                                                <path
                                                    d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                            </svg>
                                        @break

                                        @case('package-search')
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-blue"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m7.5 4.27 9 5.15" />
                                                <path
                                                    d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                                <path d="m3.3 7 8.7 5 8.7-5" />
                                                <path d="M12 22V12" />
                                            </svg>
                                        @break

                                        @case('receipt')
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-amber-500"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                                                <path d="M12 17.5V18.5" />
                                                <path d="M12 7V8" />
                                            </svg>
                                        @break

                                        @case('truck')
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-blue"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10 17h4V5H2v12h3m15 0h2v-3.34a4 4 0 0 0-1.17-2.83L19 13h-5v4h3" />
                                                <circle cx="7" cy="17" r="2" />
                                                <circle cx="17" cy="17" r="2" />
                                            </svg>
                                        @break

                                        @default
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-blue"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14" />
                                                <path d="m12 5 7 7-7 7" />
                                            </svg>
                                    @endswitch
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4
                                            class="text-sm font-bold text-slate-800 dark:text-white uppercase group-hover:text-primary-blue transition-colors">
                                            {{ $result['name'] }}</h4>
                                        <span
                                            class="text-[9px] font-black bg-slate-100 dark:bg-gray-800 text-slate-500 px-2 py-0.5 rounded-md uppercase tracking-widest">{{ $result['type'] }}</span>
                                    </div>
                                    @if (isset($result['price']))
                                        <p class="text-xs font-black text-primary-red mt-1">{{ $result['price'] }}</p>
                                    @elseif(isset($result['date']))
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">{{ $result['date'] }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div
                class="p-4 bg-slate-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Search
                    Active</span>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 text-[9px] font-black text-slate-400">
                        <kbd
                            class="px-1.5 py-0.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded shadow-sm">ESC</kbd>
                        <span>BATAL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
