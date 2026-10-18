<div wire:poll.5s="checkNewTasks" x-data="kasirApp"
    class="flex flex-col lg:flex-row h-screen w-full bg-slate-50 dark:bg-slate-950 overflow-hidden font-outfit relative"
    x-on:stock-saved.window="products = $event.detail.products"
    x-on:keydown.window.escape="search = ''; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })"
    x-on:keydown.window.prevent.slash="if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') { $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); }) }">

    @if ($isLockedForUser)
    <div class="fixed top-0 left-0 right-0 z-[999] bg-rose-600 text-white px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center justify-between shadow-md">
        <div class="flex items-center gap-2">
            <flux:icon.lock-closed class="w-4 h-4 text-white shrink-0" />
            <span>Sesi kasir hari ini telah selesai dan dikunci. Transaksi susulan hanya dapat dilakukan melalui Pengelola.</span>
        </div>
        <span class="bg-rose-950 text-rose-200 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-widest uppercase border border-rose-400">Terkunci</span>
    </div>
    @elseif ($isPostClosingMode)
    <div class="fixed top-0 left-0 right-0 z-[999] bg-amber-500 text-slate-950 px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center justify-between shadow-md">
        <div class="flex items-center gap-2">
            <flux:icon.clock class="w-4 h-4 text-slate-950 shrink-0 animate-pulse" />
            <span>Mode Pasca-Closing (Khusus Pengelola): Sesi hari ini ditutup. Transaksi baru ini otomatis dicatat untuk besok.</span>
        </div>
        <span class="bg-amber-950 text-amber-200 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-widest uppercase border border-amber-400">Rekap Besok</span>
    </div>
    @endif

    <!-- Global Loading Overlay -->
    <div wire:loading.flex.delay
        wire:target="checkout, saveQuickExpense, saveOpeningStock, saveClosingStockAndNext, submitClosingReport"
        style="display: none;"
        class="fixed inset-0 z-[9999] bg-slate-950/60 backdrop-blur-sm items-center justify-center flex-col gap-4">
        <div class="p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl flex flex-col items-center gap-4 max-w-xs text-center">
            <div class="w-12 h-12 rounded-full border-4 border-blue-600/20 border-t-blue-600 animate-spin"></div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Memproses Transaksi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Menyimpan data dan memperbarui stok...</p>
            </div>
        </div>
    </div>

    <!-- 1. Main Left Content Area (Catalog & Controls) -->
    <div class="flex-1 flex flex-col min-w-0 z-10 overflow-hidden lg:border-r border-slate-200 dark:border-slate-800">

        <!-- Header Section -->
        <header class="px-4 lg:px-6 py-3 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 relative z-20 shrink-0">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Branding & Date/Time -->
                <div class="flex items-center justify-between md:justify-start gap-3 shrink-0">
                    <div class="flex items-center gap-2.5 min-w-0">
                        @php
                            $activeJurusanId = session('active_jurusan_id');
                            $themeSettings = null;
                            if ($activeJurusanId) {
                                $jurusanModel = \App\Models\Jurusan::find($activeJurusanId);
                                if ($jurusanModel && $jurusanModel->theme_settings) {
                                    $themeSettings = $jurusanModel->theme_settings;
                                }
                            }
                            $tefaName = $themeSettings['tefa_name'] ?? 'LABANTIK POS';
                            $tefaLogo = $themeSettings['tefa_logo'] ?? '';
                        @endphp
                        <a href="{{ route('dashboard') }}"
                            class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-slate-800 border border-blue-100 dark:border-slate-700 p-1.5 flex items-center justify-center shrink-0 hover:border-blue-500 transition-colors"
                            title="Kembali ke Dashboard">
                            <img src="{{ $tefaLogo ? asset('storage/' . $tefaLogo) : asset('favicon.png') }}"
                                class="w-full h-full object-contain" alt="Logo">
                        </a>
                        <div class="min-w-0">
                            <h1 class="text-sm font-bold uppercase tracking-tight text-slate-900 dark:text-white truncate max-w-[160px] sm:max-w-[220px]">
                                {{ $tefaName }}
                            </h1>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md whitespace-nowrap">
                                    {{ now()->translatedFormat('d M Y') }}
                                </span>
                                <span x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour12: false }), 1000)" x-text="time"
                                    class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-md whitespace-nowrap border border-blue-200/50 dark:border-blue-900/50">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Cart Trigger Button -->
                    <button @click="showCart = true" type="button"
                        class="lg:hidden px-3.5 py-2 bg-blue-600 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm active:scale-95 transition-transform shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Keranjang</span>
                        <span x-show="cart.length > 0" x-text="cart.reduce((sum, i) => sum + i.quantity, 0)"
                            class="px-2 py-0.5 text-[10px] bg-white text-blue-700 rounded-full font-black leading-none"></span>
                    </button>
                </div>

                <!-- Instant Search & Quick Action Controls -->
                <div class="flex flex-col sm:flex-row flex-1 items-stretch sm:items-center gap-2 justify-end min-w-0">
                    <div class="relative flex-1 sm:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="pos-search-input" x-ref="searchInput" x-model="search"
                            placeholder="Cari menu produk (tekan /)..."
                            class="w-full pl-9 pr-8 py-2 text-xs bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                        <button x-show="search" @click="search = ''; $nextTick(() => $refs.searchInput.focus())" type="button"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0 flex-wrap sm:flex-nowrap">
                        <!-- Notifications & TEFA Switcher -->
                        @livewire('note-notifications')
                        @livewire('layout.tefa-switcher')

                        <button @click="toggleTheme()" type="button"
                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-colors shrink-0"
                            title="Toggle Mode Gelap/Terang">
                            <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 17.95l.707.707M7.05 7.05l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                        </button>

                        @if (session('active_role_name') !== 'kasir')
                        <a href="{{ route('inventory-report') }}" wire:navigate
                            class="px-2.5 py-1.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-xs font-bold rounded-xl border border-amber-200 dark:border-amber-800/50 hover:bg-amber-100 transition-colors whitespace-nowrap shrink-0">
                            Selisih
                        </a>
                        @endif

                        <button wire:click="editOpeningStock" type="button"
                            class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors whitespace-nowrap shrink-0">
                            Stok
                        </button>

                        <button wire:click="finishSession" {{ $isSessionFinished ? 'disabled' : '' }} type="button"
                            class="px-3 py-1.5 {{ $isSessionFinished ? 'bg-slate-200 text-slate-400 dark:bg-slate-800 dark:text-slate-500 cursor-not-allowed' : 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 hover:bg-blue-600 dark:hover:bg-blue-400' }} text-xs font-bold rounded-xl transition-colors whitespace-nowrap shrink-0">
                            {{ $isSessionFinished ? 'Shift Selesai' : 'Selesai Shift' }}
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="inline-block shrink-0">
                            @csrf
                            <button type="submit"
                                class="p-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors"
                                title="Keluar / Logout">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Category Horizontal Pills -->
        <nav aria-label="Kategori Produk" class="px-4 lg:px-6 py-2.5 bg-white dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0">
            <button @click="selectedCategory = null" type="button"
                :class="selectedCategory === null 
                    ? 'bg-blue-600 text-white shadow-sm ring-1 ring-blue-600' 
                    : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                class="text-xs font-semibold py-1.5 px-3.5 rounded-xl whitespace-nowrap transition-all">
                Semua Menu
            </button>
            @foreach ($this->categories as $cat)
                <button type="button"
                    @click="selectedCategory = (selectedCategory == '{{ $cat->id }}' ? null : '{{ $cat->id }}')"
                    :class="selectedCategory == '{{ $cat->id }}' 
                        ? 'bg-blue-600 text-white shadow-sm ring-1 ring-blue-600' 
                        : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="text-xs font-semibold py-1.5 px-3.5 rounded-xl whitespace-nowrap transition-all">
                    {{ $cat->name }}
                </button>
            @endforeach
        </nav>

        <!-- Today's Documentation Schedule Alert Banner -->
        @if(isset($todayDocSchedule) && $todayDocSchedule)
            <div class="mx-4 lg:mx-6 mt-3 p-3 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-2xl border border-blue-700/40 shadow-sm flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="p-2 bg-blue-500/20 rounded-xl text-blue-300 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[9px] font-bold uppercase bg-amber-400 text-amber-950 px-2 py-0.5 rounded-md">Tugas Dokumentasi Hari Ini</span>
                            <span class="text-xs font-bold text-blue-100 truncate">{{ $todayDocSchedule->activity->title ?? 'Dokumentasi' }}</span>
                        </div>
                        @if($todayDocSchedule->notes)
                            <p class="text-xs text-blue-200/80 truncate mt-0.5">{{ $todayDocSchedule->notes }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('documentation-schedules') }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-all shrink-0">
                    Buka &rarr;
                </a>
            </div>
        @endif

        <!-- Product Grid Catalog -->
        <main class="flex-1 overflow-y-auto px-4 lg:px-6 py-4 no-scrollbar bg-slate-50 dark:bg-slate-950" data-product-grid>
            <!-- Empty State -->
            <div x-show="filteredProducts.length === 0" x-cloak class="h-full flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Tidak ada produk ditemukan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm">Coba ubah kata kunci pencarian atau pilih kategori lain di atas.</p>
                <button x-show="search || selectedCategory" @click="search = ''; selectedCategory = null" type="button"
                    class="mt-4 px-4 py-2 bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-xl border border-blue-200 dark:border-slate-700 hover:bg-blue-100 transition-colors">
                    Reset Filter
                </button>
            </div>

            <!-- Grid Items -->
            <div x-show="filteredProducts.length > 0"
                class="grid gap-3 sm:gap-4"
                :style="'grid-template-columns: repeat(auto-fill, minmax(180px, 1fr))'">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div
                        @click="addToCart(product)"
                        :class="product.available_stock <= 0 ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md active:scale-[0.99]'"
                        class="group p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col justify-between transition-all duration-150 select-none">
                        
                        <div>
                            <!-- Header badge: Category + Stock status -->
                            <div class="flex items-center justify-between gap-1.5 mb-2.5">
                                <span :class="getCategoryColor(product.category_name)"
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md truncate max-w-[90px] border leading-none"
                                    :title="product.category_name"
                                    x-text="product.category_name"></span>
                                
                                <span x-show="product.available_stock > 0"
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/60 dark:border-emerald-800/50 whitespace-nowrap leading-none"
                                    x-text="'Sisa: ' + product.available_stock"></span>
                                <span x-show="product.available_stock <= 0"
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/50 border border-rose-200/60 dark:border-rose-800/50 whitespace-nowrap leading-none">
                                    Habis
                                </span>
                            </div>

                            <!-- Product Name -->
                            <h3 x-text="product.name"
                                class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            </h3>

                            <!-- Has modifiers pill -->
                            <template x-if="product.modifier_groups && product.modifier_groups.length > 0">
                                <span class="inline-flex items-center gap-1 text-[9px] font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-900/40 mt-1">
                                    + Opsi Topping
                                </span>
                            </template>
                        </div>

                        <!-- Price & Add Action -->
                        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase block leading-none">Harga</span>
                                <span x-text="formatRupiah(product.price)"
                                    class="text-sm font-black text-slate-900 dark:text-white mt-0.5 inline-block"></span>
                            </div>

                            <button type="button"
                                :disabled="product.available_stock <= 0 || {{ $isLockedForUser ? 'true' : 'false' }}"
                                class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 group-hover:bg-blue-600 group-hover:text-white dark:group-hover:bg-blue-600 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </main>
    </div>

    <!-- Desktop Resizer Handle -->
    <div 
        @mousedown="startResize($event)"
        class="hidden lg:block w-1.5 hover:w-2 bg-slate-200 hover:bg-blue-500 dark:bg-slate-800 dark:hover:bg-blue-500 cursor-col-resize transition-all z-30 h-screen select-none relative"
        :class="isResizing ? 'bg-blue-600 w-2' : ''"
        title="Tarik untuk mengubah lebar panel belanja">
        <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[1px] bg-slate-300 dark:bg-slate-700"></div>
    </div>

    <!-- 2. Right Sidebar / Mobile Bottom Sheet (Cart & Checkout) -->
    <aside x-show="screenWidth >= 1024 || showCart" x-cloak
        :style="screenWidth >= 1024 ? 'width: ' + sidebarWidth + 'px; min-width: ' + sidebarWidth + 'px; max-width: ' + sidebarWidth + 'px;' : ''"
        class="fixed inset-y-0 right-0 w-full sm:w-[420px] bg-white dark:bg-slate-900 lg:static lg:flex flex flex-col z-[100] border-l border-slate-200 dark:border-slate-800 shadow-2xl lg:shadow-none">

        <!-- Sidebar Header -->
        <div class="px-5 py-3.5 bg-slate-900 text-white flex justify-between items-center gap-2 shrink-0">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Pesanan Kasir</h2>
                <span x-show="cart.length > 0"
                    class="px-2 py-0.5 text-[10px] font-black bg-blue-600 text-white rounded-full"
                    x-text="cart.reduce((sum, i) => sum + i.quantity, 0)"></span>
            </div>

            <div class="flex items-center gap-1.5">
                <button wire:click="editOpeningStock" type="button"
                    class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] font-bold rounded-lg border border-slate-700 transition-colors"
                    title="Kelola Stok Awal">Stok</button>
                <button @click="showCart = false" type="button"
                    class="lg:hidden p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-data="{ tab: 'cart' }" class="flex flex-col h-full min-h-0 overflow-hidden bg-white dark:bg-slate-900">
            <!-- Sidebar Navigation Tabs -->
            <div class="p-2.5 flex gap-1.5 bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <button @click="tab = 'cart'" type="button"
                    :class="tab === 'cart' 
                        ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm font-bold' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium'"
                    class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center">
                    Keranjang (<span x-text="cart.length"></span>)
                </button>
                <button @click="tab = 'tasks'" type="button"
                    :class="tab === 'tasks' 
                        ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm font-bold' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium'"
                    class="flex-1 py-1.5 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                    <span>Tugas</span>
                    @php
                        $pendingTasksCount = collect($dailyTasks)->where('approval_status', '!=', 'approved')->count();
                    @endphp
                    @if ($pendingTasksCount > 0)
                        <span class="px-1.5 py-0.2 text-[9px] font-bold bg-rose-600 text-white rounded-full leading-none">
                            {{ $pendingTasksCount }}
                        </span>
                    @endif
                </button>
                <button @click="tab = 'history'" type="button"
                    :class="tab === 'history' 
                        ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm font-bold' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-medium'"
                    class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center">
                    Riwayat
                </button>
                <button x-show="tab === 'cart' && cart.length > 0" @click="clearCart()" type="button"
                    class="px-2 py-1 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors"
                    title="Kosongkan Keranjang">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>

            <!-- Content Area Container -->
            <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
                <!-- Cart Content -->
                <div x-show="tab === 'cart'" class="flex-1 min-h-[160px] overflow-y-auto p-3 sm:p-4 space-y-2.5 no-scrollbar bg-slate-50 dark:bg-slate-950">
                    <template x-for="item in cart" :key="item.cartItemId">
                        <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs flex flex-col gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200/50 dark:border-blue-900/50 flex items-center justify-center font-bold text-xs shrink-0"
                                    x-text="item.name.substring(0, 2).toUpperCase()"></div>
                                <div class="flex-1 min-w-0">
                                    <h4 x-text="item.name" class="text-xs font-bold text-slate-900 dark:text-white truncate"></h4>
                                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400 mt-0.5">
                                        <span x-text="formatRupiah(item.price + (item.selected_modifiers ? item.selected_modifiers.reduce((sum, m) => sum + m.price, 0) : 0))"></span>
                                        <template x-if="item.selected_modifiers && item.selected_modifiers.length > 0">
                                            <span class="text-slate-400 font-normal text-[10px]">
                                                (+Topping)
                                            </span>
                                        </template>
                                    </p>
                                </div>
                                <!-- Stepper -->
                                <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-lg p-0.5 border border-slate-200 dark:border-slate-700 shrink-0">
                                    <button @click="removeFromCart(item.cartItemId)" type="button"
                                        class="w-7 h-7 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 rounded-md transition-colors">-</button>
                                    <span x-text="item.quantity" class="px-2 text-xs font-bold text-slate-900 dark:text-white min-w-[20px] text-center"></span>
                                    <button @click="addQuantityFromCart(item)" type="button"
                                        class="w-7 h-7 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 rounded-md transition-colors">+</button>
                                </div>
                            </div>

                            <!-- Toppings details if any -->
                            <template x-if="item.selected_modifiers && item.selected_modifiers.length > 0">
                                <div class="flex flex-wrap gap-1 pl-12 border-t border-slate-100 dark:border-slate-800 pt-1.5">
                                    <template x-for="mod in item.selected_modifiers" :key="mod.id">
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[9px] font-medium rounded border border-slate-200 dark:border-slate-700">
                                            + <span x-text="mod.name"></span> (+<span x-text="formatRupiah(mod.price)"></span>)
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-center py-12 text-slate-400">
                        <svg class="w-10 h-10 mb-2 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-xs font-medium">Keranjang Belum Ada Pesanan</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Pilih produk di samping untuk menambah</p>
                    </div>
                </div>

                <!-- Tasks Content -->
                <div x-show="tab === 'tasks'" class="flex-1 overflow-y-auto p-4 space-y-2.5 no-scrollbar bg-slate-50 dark:bg-slate-950">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tugas Harian Anda Hari Ini</div>
                    @forelse($dailyTasks as $task)
                        <a href="{{ route('my-tasks') }}" target="_blank"
                            class="p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl block hover:border-blue-400 dark:hover:border-blue-600 transition-colors">
                            <div class="flex justify-between items-start gap-2">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white {{ $task->latestSubmission?->approval_status === 'approved' ? 'line-through text-slate-400' : '' }}">
                                    {{ $task->taskDefinition->task_name }}
                                </h4>
                                @if ($task->latestSubmission?->approval_status === 'approved')
                                    <span class="text-[9px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full shrink-0">Disetujui</span>
                                @elseif($task->latestSubmission?->approval_status === 'rejected')
                                    <span class="text-[9px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 px-2 py-0.5 rounded-full shrink-0">Perlu Revisi</span>
                                @elseif($task->latestSubmission?->approval_status === 'pending')
                                    <span class="text-[9px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 px-2 py-0.5 rounded-full shrink-0">Menunggu ACC</span>
                                @else
                                    <span class="text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-2 py-0.5 rounded-full shrink-0">Belum Selesai</span>
                                @endif
                            </div>
                            @if ($task->taskDefinition->description)
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $task->taskDefinition->description }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[10px] text-slate-400">
                                <span>{{ $task->taskDefinition->date->translatedFormat('d M Y') }}</span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold ml-auto flex items-center gap-1">
                                    Buka Tugas &rarr;
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-10 text-xs text-slate-400">Tidak ada tugas harian khusus untuk shift ini.</div>
                    @endforelse
                </div>

                <!-- History Content -->
                <div x-show="tab === 'history'" class="flex-1 overflow-y-auto p-4 space-y-2.5 no-scrollbar bg-slate-50 dark:bg-slate-950">
                    @foreach ($this->recentTransactions as $history)
                        <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl">
                            <div class="flex justify-between items-start mb-1.5">
                                <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                    {{ \Carbon\Carbon::parse($history->transacted_at)->format('H:i') }}
                                </span>
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ str_replace('_', ' ', $history->status) }}
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                {{ $history->reference }}
                            </h4>
                            <div class="flex justify-between items-end mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] text-slate-400">{{ $history->total_qty }} Item</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black text-slate-900 dark:text-white">Rp{{ number_format($history->total_amount, 0, ',', '.') }}</span>
                                    <button wire:click="viewDetails('{{ $history->reference }}')" type="button"
                                        class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 text-[10px] font-bold rounded hover:bg-blue-100 transition-colors">
                                        Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Checkout Panel (Fixed Bottom on Tab Cart) -->
                <div x-show="tab === 'cart'"
                    class="shrink-0 p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 space-y-3 max-h-[60vh] overflow-y-auto no-scrollbar">

                    <!-- Susulan Mode Alert -->
                    <div x-show="$wire.transactionDate < '{{ now()->toDateString() }}'" x-cloak
                        class="p-2 bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-800 rounded-xl flex items-center gap-2 text-amber-800 dark:text-amber-200 text-xs font-semibold">
                        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Mode Pencatatan Transaksi Susulan</span>
                    </div>

                    <!-- Total Bill Display Card -->
                    <div class="p-3.5 bg-slate-900 dark:bg-slate-950 text-white rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Total Tagihan</span>
                            <span class="text-xs text-blue-400 font-semibold" x-text="cart.length + ' jenis menu'"></span>
                        </div>
                        <div class="text-right">
                            <span x-text="formatRupiah(total)" class="text-2xl font-black text-white tracking-tight"></span>
                        </div>
                    </div>

                    <!-- Inputs: Date & Buyer -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">Tanggal</label>
                            <input type="date" wire:model.live="transactionDate" max="{{ now()->toDateString() }}"
                                class="w-full px-2.5 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">Pelanggan</label>
                            <input type="text" x-model="buyer_name" placeholder="Guest / Umum"
                                class="w-full px-2.5 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Payment Method Segmented Controls -->
                    <div>
                        <label class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                            <button type="button" @click="payment_method = 'cash'"
                                :class="payment_method === 'cash' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                                class="py-1.5 text-xs rounded-lg transition-all text-center">
                                Tunai
                            </button>
                            <button type="button" @click="payment_method = 'transfer'; status = 'uang_diterima'; payment_amount = total"
                                :class="payment_method === 'transfer' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                                class="py-1.5 text-xs rounded-lg transition-all text-center">
                                Transfer
                            </button>
                            <button type="button" @click="payment_method = 'qris'; status = 'uang_diterima'; payment_amount = total"
                                :class="payment_method === 'qris' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                                class="py-1.5 text-xs rounded-lg transition-all text-center">
                                QRIS
                            </button>
                        </div>
                    </div>

                    <!-- Non-cash virtual destination badge -->
                    <div x-show="payment_method !== 'cash'" x-cloak
                        class="p-2.5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 rounded-xl flex items-center gap-2 text-xs text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <span>Otomatis masuk ke <strong x-text="payment_method === 'transfer' ? 'Kas Virtual Transfer' : 'Kas Virtual QRIS'"></strong></span>
                    </div>

                    <!-- Cash Input & Quick Chips (Only visible when cash) -->
                    <div x-show="payment_method === 'cash'" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">Nominal Tunai (Rp)</label>
                            <button type="button" @click="setUangPas()"
                                class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                Pakai Uang Pas
                            </button>
                        </div>
                        
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" x-model.number="payment_amount"
                                @keydown.enter="handleCheckoutKeydown($event)"
                                placeholder="0"
                                class="w-full pl-9 pr-3 py-2 text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Quick Cash Chips -->
                        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                            <button type="button" @click="setUangPas()"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                Pas
                            </button>
                            <button type="button" @click="setExactCash(10000)"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                10k
                            </button>
                            <button type="button" @click="setExactCash(20000)"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                20k
                            </button>
                            <button type="button" @click="setExactCash(50000)"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                50k
                            </button>
                            <button type="button" @click="setExactCash(100000)"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                100k
                            </button>
                        </div>

                        <!-- Cash Status Toggle -->
                        <div class="grid grid-cols-3 gap-1.5 pt-1">
                            <button type="button" @click="status = 'uang_diterima'"
                                :class="status === 'uang_diterima' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium'"
                                class="py-1.5 text-xs rounded-lg transition-all text-center">
                                Lunas
                            </button>
                            <button type="button" @click="status = 'belum_kembalian'"
                                :class="status === 'belum_kembalian' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium'"
                                class="py-1.5 text-xs rounded-lg transition-all text-center">
                                Pending
                            </button>
                            <button type="button" @click="status = 'belum_menerima_uang'"
                                :class="status === 'belum_menerima_uang' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium'"
                                class="py-1.5 text-xs rounded-lg transition-all text-center">
                                Hutang
                            </button>
                        </div>

                        <!-- Change indicator -->
                        <template x-if="payment_amount > 0">
                            <div class="p-2.5 rounded-xl flex items-center justify-between text-xs font-bold"
                                :class="change < 0 ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50'">
                                <span x-text="change < 0 ? 'Kurang Bayar' : 'Kembalian'"></span>
                                <span x-text="formatRupiah(Math.abs(change))" class="text-sm"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Checkout Submit Button -->
                    <button @click="checkout()"
                        :disabled="{{ $isLockedForUser ? 'true' : 'false' }} || cart.length === 0 || (payment_method === 'cash' && payment_amount < total && status === 'uang_diterima') || loading"
                        @keydown.enter="handleCheckoutKeydown($event)"
                        class="checkout-trigger w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white shadow-md active:scale-[0.99] transition-all flex items-center justify-center gap-2 {{ $isPostClosingMode ? 'bg-amber-600 hover:bg-amber-500' : 'bg-blue-600 hover:bg-blue-500' }} disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed">
                        <span x-show="!loading" class="flex items-center justify-center gap-2">
                            <span>{{ $isPostClosingMode ? 'Bayar (Rekap Besok)' : 'Bayar Sekarang' }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                        <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Floating Bottom Bar when Cart has items and Drawer closed -->
    <div x-show="cart.length > 0 && !showCart && screenWidth < 1024" x-cloak
        class="fixed bottom-4 left-4 right-4 z-40 bg-slate-900 dark:bg-blue-600 text-white rounded-2xl p-3 px-4 shadow-2xl flex items-center justify-between border border-slate-700/50">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-500 text-white text-[11px] font-black flex items-center justify-center"
                    x-text="cart.reduce((sum, i) => sum + i.quantity, 0)"></span>
                <span class="text-xs font-semibold text-slate-300">Total Belanja</span>
            </div>
            <div class="text-base font-black text-white mt-0.5" x-text="formatRupiah(total)"></div>
        </div>
        <button @click="showCart = true" type="button"
            class="px-4 py-2 bg-white text-slate-900 dark:bg-white dark:text-blue-700 font-bold text-xs rounded-xl shadow-sm flex items-center gap-1.5 active:scale-95 transition-transform">
            <span>Buka Keranjang</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- 3. MODALS -->

    <!-- Unfinished Session / Recovery Modal -->
    <div x-data="{ show: @entangle('showRecoveryModal') }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[500] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-6 text-center shadow-2xl">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-200 dark:border-amber-900/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sesi Belum Ditutup</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Sesi pada tanggal <strong class="text-blue-600">{{ $unfinishedSessionDate ? \Carbon\Carbon::parse($unfinishedSessionDate)->translatedFormat('d F Y') : '-' }}</strong> belum diselesaikan.
            </p>

            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl my-4 text-left border border-slate-200 dark:border-slate-700">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pemulihan Otomatis</p>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Sistem akan menghitung sisa stok dan menggunakannya untuk pembukaan sesi hari ini.</p>
            </div>

            <div class="space-y-2">
                <button wire:click="fixUnfinishedSession" type="button"
                    class="w-full py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-500 transition-colors">
                    Pulihkan & Lanjutkan
                </button>
                <a href="{{ route('dashboard') }}"
                    class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold block hover:bg-slate-200 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Opening Stock Modal -->
    <div x-data="{ show: @entangle('showOpeningStockModal'), modalSearch: '' }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[400] flex items-center justify-center p-3 sm:p-6 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 bg-slate-900 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-white">Stok Awal Toko</h2>
                        <span class="text-[10px] bg-blue-600 text-white px-2 py-0.5 rounded-md font-semibold">
                            {{ \Carbon\Carbon::parse($transactionDate)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Verifikasi jumlah barang fisik yang tersedia di etalase</p>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button wire:click="syncOpeningStockWithLast" type="button"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Sinkronkan Stok Terakhir</span>
                    </button>
                    <input type="text" x-model="modalSearch" placeholder="Cari nama barang..."
                        class="px-3 py-1.5 text-xs bg-slate-800 text-white placeholder:text-slate-400 border border-slate-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 flex-1 sm:w-44">
                    <button @click="show = false" type="button" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50 dark:bg-slate-950 no-scrollbar">
                @php
                    $activeJurusanId = session('active_jurusan_id');
                    $openingProducts = \App\Models\Product::where('is_active', true)
                        ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                            return $q->where('jurusan_id', $activeJurusanId);
                        })
                        ->orderBy('name')
                        ->get();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($openingProducts as $p)
                        <div x-show="'{{ strtolower($p->name) }}'.includes(modalSearch.toLowerCase())"
                            wire:key="opening-stock-{{ $p->id }}"
                            class="p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $p->name }}</h4>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[9px] font-semibold text-slate-500 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                        {{ $p->category->name ?? '' }}
                                    </span>
                                    @if (isset($lastClosingDetails[$p->id]))
                                        <span class="text-[9px] text-blue-600 dark:text-blue-400 font-semibold bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded">
                                            Terakhir: {{ $lastClosingDetails[$p->id]['stock'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[10px] text-slate-400 font-semibold hidden sm:inline">Stok:</span>
                                <input type="number" wire:model="stockItems.{{ $p->id }}"
                                    class="w-20 text-center font-bold text-sm p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button @click="show = false" type="button"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    Tutup
                </button>
                <button wire:click="saveOpeningStock" type="button"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                    Simpan & Mulai Jualan
                </button>
            </div>
        </div>
    </div>

    <!-- Closing Stock Modal -->
    <div x-data="{ show: @entangle('showClosingStockModal'), modalSearch: '' }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[400] flex items-center justify-center p-3 sm:p-6 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 bg-slate-900 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-white">Rekap Harian Selesai Shift</h2>
                        <span class="text-[10px] bg-rose-600 text-white px-2 py-0.5 rounded-md font-semibold">
                            {{ \Carbon\Carbon::parse($transactionDate)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Hitung sisa barang fisik di toko sebelum tutup sesi</p>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="text" x-model="modalSearch" placeholder="Cari barang..."
                        class="px-3 py-1.5 text-xs bg-slate-800 text-white placeholder:text-slate-400 border border-slate-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 flex-1 sm:w-48">
                    <button @click="show = false" type="button" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50 dark:bg-slate-950 no-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($this->stockComparison as $item)
                        <div x-show="'{{ strtolower($item['name']) }}'.includes(modalSearch.toLowerCase())"
                            wire:key="closing-stock-{{ $item['id'] }}"
                            class="p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs space-y-2.5">
                            <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $item['name'] }}</h4>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="bg-slate-100 dark:bg-slate-800 p-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <span class="text-[9px] font-semibold text-slate-400 uppercase block">Awal</span>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $item['opening'] }}</span>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-950/40 p-1.5 rounded-lg border border-blue-200 dark:border-blue-900/40">
                                    <span class="text-[9px] font-semibold text-blue-600 dark:text-blue-400 uppercase block">Laku</span>
                                    <span class="text-xs font-bold text-blue-700 dark:text-blue-300">{{ $item['sold'] }}</span>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-950/40 p-1.5 rounded-lg border border-emerald-200 dark:border-emerald-900/40">
                                    <span class="text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase block">Harus Sisa</span>
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">{{ $item['expected'] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <label class="text-[10px] font-semibold text-slate-500">Sisa Fisik Toko:</label>
                                <input type="number" wire:model="stockItems.{{ $item['id'] }}"
                                    class="w-24 text-center font-bold text-sm p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button wire:click="saveClosingStockAndNext" type="button"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                    Simpan Sisa Barang & Lanjut
                </button>
            </div>
        </div>
    </div>

    <!-- Closing Report & Audit Modal -->
    <div x-data="{ show: @entangle('showClosingReportModal') }" x-show="show" x-cloak
        class="fixed inset-0 z-[500] flex items-center justify-center p-3 sm:p-6 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="text-center mb-4">
                <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 px-2.5 py-0.5 rounded-full border border-rose-200 dark:border-rose-900/50 uppercase">
                    Laporan Akhir Shift
                </span>
                <h2 class="text-base font-bold text-slate-900 dark:text-white mt-2">Closing Report</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verifikasi audit kas dan sampaikan catatan aktivitas Anda</p>
            </div>

            <div class="space-y-3.5 mb-5 text-left">
                <!-- Virtual Cash Audit Section -->
                @if($todayRevenueTransfer > 0 || $todayRevenueQris > 0)
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Audit Kas Virtual Hari Ini</p>
                    
                    @if($todayRevenueTransfer > 0)
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500">Transfer Seharusnya:</span>
                            <span class="font-bold text-slate-900 dark:text-white">Rp{{ number_format($todayRevenueTransfer, 0, ',', '.') }}</span>
                        </div>
                        <input type="number" wire:model.live="actualTransfer" placeholder="Nominal real transfer..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>
                    @endif

                    @if($todayRevenueQris > 0)
                    <div class="space-y-1 pt-2 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500">QRIS Seharusnya:</span>
                            <span class="font-bold text-slate-900 dark:text-white">Rp{{ number_format($todayRevenueQris, 0, ',', '.') }}</span>
                        </div>
                        <input type="number" wire:model.live="actualQris" placeholder="Nominal real QRIS..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>
                    @endif
                </div>
                @endif

                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                        Catatan Aktivitas Selama Shift
                    </label>
                    <textarea wire:model="closingReportText" placeholder="Tuliskan ringkasan tugas dan kendala hari ini..."
                        rows="3"
                        class="w-full p-3 text-xs bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-2xl focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    @error('closingReportText')
                        <span class="text-xs text-rose-500 font-medium mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button wire:click="submitClosingReport" type="button"
                class="w-full py-3 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-md transition-colors uppercase tracking-wider">
                Kirim Laporan & Selesaikan Sesi
            </button>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div x-data="{ show: @entangle('showDetailsModal') }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[600] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="show = false"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md shadow-2xl flex flex-col overflow-hidden">
            <div class="p-4 bg-slate-900 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-white">Detail Transaksi</h3>
                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $detailReference }}</p>
                </div>
                <button @click="show = false" type="button" class="p-1 text-slate-400 hover:text-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 max-h-[50vh] overflow-y-auto no-scrollbar space-y-2.5">
                @if ($this->detailItems)
                    @foreach ($this->detailItems as $item)
                        <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800 last:border-0 text-xs">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $item->product->name }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-bold text-slate-900 dark:text-white">Rp{{ number_format($item->total_price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <span class="text-xs text-slate-500 font-semibold">Total Transaksi</span>
                <span class="text-base font-black text-blue-600 dark:text-blue-400">
                    Rp{{ number_format($this->detailItems ? $this->detailItems->sum('total_price') : 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Stock Alert Dialog -->
    <div x-show="stockAlert !== null" x-cloak @keydown.window.escape="stockAlert = null"
        class="fixed inset-0 z-[600] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-sm p-6 text-center shadow-2xl">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-200 dark:border-rose-900/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" x-text="stockAlert?.title"></h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-5" x-text="stockAlert?.message"></p>
            <button @click="stockAlert = null" type="button"
                class="w-full py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Mengerti
            </button>
        </div>
    </div>

    <!-- Change Due Modal -->
    <div x-show="showChangeModal" x-cloak
        x-on:keydown.window.escape="showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })"
        x-on:keydown.window.enter="if (showChangeModal) { showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); }); }"
        class="fixed inset-0 z-[600] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-sm p-6 text-center shadow-2xl">
            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-900/50 uppercase">
                Transaksi Berhasil
            </span>
            <h2 class="text-base font-bold text-slate-900 dark:text-white mt-2">Uang Kembalian</h2>

            <div class="my-5 p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500">Total Tagihan:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="'Rp' + formatRupiah(lastChangeData.total).replace('Rp', '').trim()"></span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500">Uang Diterima:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="'Rp' + formatRupiah(lastChangeData.payment).replace('Rp', '').trim()"></span>
                </div>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Kembalian:</span>
                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp' + formatRupiah(lastChangeData.change).replace('Rp', '').trim()"></span>
                </div>
            </div>

            <button @click="showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })" type="button"
                class="w-full py-2.5 bg-slate-900 dark:bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-slate-800 dark:hover:bg-blue-500 transition-colors">
                Tutup (Tekan Enter)
            </button>
        </div>
    </div>

    <!-- Modifier / Topping Options Modal -->
    <div x-show="showModifierModal" x-cloak class="fixed inset-0 z-[600] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-5 shadow-2xl">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 rounded-md uppercase border border-amber-200 dark:border-amber-900/50">
                        Opsi Tambahan
                    </span>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white mt-1.5" x-text="activeModifierProduct ? activeModifierProduct.name : ''"></h2>
                </div>
                <button @click="showModifierModal = false; activeModifierProduct = null;" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4 max-h-[300px] overflow-y-auto no-scrollbar" x-show="activeModifierProduct">
                <template x-for="group in (activeModifierProduct ? activeModifierProduct.modifier_groups : [])" :key="group.id">
                    <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="group.name"></span>
                            <span class="text-[9px] text-slate-400 font-medium">
                                Min <span x-text="group.min"></span> / Max <span x-text="group.max"></span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-1.5">
                            <template x-for="opt in group.options" :key="opt.id">
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 cursor-pointer hover:border-blue-400 transition-colors">
                                    <template x-if="group.max > 1">
                                        <input type="checkbox" :value="opt.id"
                                            @change="if ($el.checked) {
                                                if (!selectedModifiersMap[group.id].includes(opt.id)) selectedModifiersMap[group.id].push(opt.id);
                                            } else {
                                                selectedModifiersMap[group.id] = selectedModifiersMap[group.id].filter(id => id !== opt.id);
                                            }"
                                            :checked="(selectedModifiersMap[group.id] || []).includes(opt.id)"
                                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </template>
                                    <template x-if="group.max === 1">
                                        <input type="radio" :name="'group-' + group.id" :value="opt.id"
                                            @change="selectedModifiersMap[group.id] = [opt.id]"
                                            :checked="(selectedModifiersMap[group.id] || []).includes(opt.id)"
                                            class="border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </template>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs font-semibold text-slate-900 dark:text-white block truncate" x-text="opt.name"></span>
                                        <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold" x-text="'+' + formatRupiah(opt.price)"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex gap-2 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button @click="skipModifiers()" type="button"
                    class="flex-1 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    Tanpa Topping
                </button>
                <button @click="confirmModifiers()" type="button"
                    class="flex-1 py-2 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-500 transition-colors">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts Section -->
    <script>
        window.addEventListener('transaction-complete', () => {
            setTimeout(() => {
                const searchInput = document.getElementById('pos-search-input');
                if (searchInput) searchInput.focus();
            }, 100);
        });

        const initKasirApp = () => {
            if (window.Alpine && window.Alpine.data) {
                Alpine.data('kasirApp', () => ({
                    showCart: false,
                    search: '',
                    selectedCategory: null,
                    products: @json($allProductsJson),
                    cart: [],
                    loading: false,
                    modalSearch: '',
                    stockAlert: null,
                    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

                    payment_amount: 0,
                    buyer_name: '',
                    status: 'uang_diterima',
                    payment_method: 'cash',
                    note: '',

                    showChangeModal: false,
                    lastChangeData: { total: 0, payment: 0, change: 0 },

                    sidebarWidth: Math.max(380, localStorage.getItem('cashier_sidebar_width') ? parseInt(localStorage.getItem('cashier_sidebar_width')) : 420),
                    isResizing: false,
                    screenWidth: window.innerWidth,

                    showModifierModal: false,
                    activeModifierProduct: null,
                    selectedModifiersMap: {},
                    isAddingToCart: false,

                    init() {
                        if (this.darkMode) document.documentElement.classList.add('dark');
                        else document.documentElement.classList.remove('dark');

                        /* Real-time product & stock sync with Livewire */
                        this.$watch('$wire.products', (newProds) => {
                            if (newProds && Array.isArray(newProds)) {
                                this.products = JSON.parse(JSON.stringify(newProds));
                            }
                        });

                        window.addEventListener('products-updated', (e) => {
                            if (e.detail) {
                                const newProds = Array.isArray(e.detail) ? e.detail : (e.detail.products || []);
                                if (newProds && newProds.length > 0) {
                                    this.products = JSON.parse(JSON.stringify(newProds));
                                }
                            }
                        });

                        window.addEventListener('resize', () => {
                            this.screenWidth = window.innerWidth;
                        });

                        /* Autofocus search on load */
                        this.$nextTick(() => {
                            const searchInput = document.getElementById('pos-search-input');
                            if (searchInput) searchInput.focus();
                        });

                        /* Setup keydown listener for checkout */
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter' && !e.ctrlKey && !e.shiftKey && !e.altKey) {
                                const activeElement = document.activeElement;
                                if (activeElement && (activeElement.name === 'payment_amount' || activeElement.classList.contains('checkout-trigger'))) {
                                    this.handleCheckoutKeydown(e);
                                }
                            }
                        });
                    },

                    startResize(e) {
                        this.isResizing = true;
                        document.body.style.cursor = 'col-resize';
                        document.body.style.userSelect = 'none';
                        
                        const doResize = (event) => {
                            if (!this.isResizing) return;
                            const newWidth = window.innerWidth - event.clientX;
                            const maxWidth = Math.max(380, window.innerWidth - 350);
                            if (newWidth >= 380 && newWidth <= maxWidth) {
                                this.sidebarWidth = newWidth;
                                localStorage.setItem('cashier_sidebar_width', newWidth);
                            }
                        };
                        
                        const stopResize = () => {
                            this.isResizing = false;
                            document.body.style.cursor = '';
                            document.body.style.userSelect = '';
                            window.removeEventListener('mousemove', doResize);
                            window.removeEventListener('mouseup', stopResize);
                        };
                        
                        window.addEventListener('mousemove', doResize);
                        window.addEventListener('mouseup', stopResize);
                    },

                    get filteredProducts() {
                        return this.products.filter(p => {
                            const matchesSearch = !this.search || p.name.toLowerCase().includes(this.search.toLowerCase());
                            const matchesCategory = !this.selectedCategory || String(p.category_id) === String(this.selectedCategory);
                            return matchesSearch && matchesCategory;
                        });
                    },

                    get total() {
                        return this.cart.reduce((sum, item) => {
                            const modifiersPrice = (item.selected_modifiers || []).reduce((mSum, m) => mSum + m.price, 0);
                            return sum + ((item.price + modifiersPrice) * item.quantity);
                        }, 0);
                    },

                    get change() {
                        if (this.payment_amount > 0) {
                            return this.payment_amount - this.total;
                        }
                        return 0;
                    },

                    setUangPas() {
                        this.payment_amount = this.total;
                    },

                    setExactCash(val) {
                        this.payment_amount = val;
                    },

                    addToCart(product, force) {
                        if (this.isAddingToCart) return;
                        this.isAddingToCart = true;
                        setTimeout(() => { this.isAddingToCart = false; }, 200);

                        const shouldForce = (force === true);

                        if (!shouldForce && product.modifier_groups && product.modifier_groups.length > 0) {
                            this.activeModifierProduct = product;
                            this.selectedModifiersMap = {};
                            product.modifier_groups.forEach(g => {
                                this.selectedModifiersMap[g.id] = [];
                            });
                            this.showModifierModal = true;
                            return;
                        }

                        const selectedMods = product.selected_modifiers || [];
                        const cartItemId = product.id + '_' + selectedMods.map(m => m.id).sort().join('_');

                        const existingItem = this.cart.find(item => item.cartItemId === cartItemId);
                        if (existingItem) {
                            if (existingItem.quantity < product.available_stock) {
                                existingItem.quantity++;
                            } else {
                                this.stockAlert = { title: 'Stok Terbatas', message: 'Stok ' + product.name + ' tidak mencukupi untuk ditambah lagi.' };
                            }
                        } else {
                            if (product.available_stock > 0) {
                                this.cart = [...this.cart, {
                                    ...product,
                                    cartItemId: cartItemId,
                                    selected_modifiers: selectedMods,
                                    quantity: 1
                                }];
                            } else {
                                this.stockAlert = { title: 'Stok Habis', message: 'Maaf, persediaan ' + product.name + ' telah habis untuk shift ini.' };
                            }
                        }
                    },

                    skipModifiers() {
                        if (!this.activeModifierProduct) return;
                        const productWithoutMods = {
                            ...this.activeModifierProduct,
                            selected_modifiers: []
                        };
                        this.addToCart(productWithoutMods, true);
                        this.showModifierModal = false;
                        this.activeModifierProduct = null;
                        this.selectedModifiersMap = {};
                    },

                    confirmModifiers() {
                        if (!this.activeModifierProduct) return;

                        let isValid = true;
                        this.activeModifierProduct.modifier_groups.forEach(g => {
                            const selectedCount = (this.selectedModifiersMap[g.id] || []).length;
                            if (selectedCount < g.min || selectedCount > g.max) {
                                isValid = false;
                                this.stockAlert = { 
                                    title: 'Pilihan Wajib', 
                                    message: 'Harap sesuaikan opsi untuk kelompok ' + g.name + ' (Min: ' + g.min + ', Max: ' + g.max + ')' 
                                };
                            }
                        });

                        if (!isValid) return;

                        const selectedModsData = [];
                        this.activeModifierProduct.modifier_groups.forEach(g => {
                            const selectedIds = this.selectedModifiersMap[g.id] || [];
                            g.options.forEach(opt => {
                                if (selectedIds.includes(opt.id)) {
                                    selectedModsData.push(opt);
                                }
                            });
                        });

                        const productWithMods = {
                            ...this.activeModifierProduct,
                            selected_modifiers: selectedModsData
                        };

                        this.addToCart(productWithMods, true);
                        this.showModifierModal = false;
                        this.activeModifierProduct = null;
                        this.selectedModifiersMap = {};
                    },

                    removeFromCart(cartItemId) {
                        const index = this.cart.findIndex(item => item.cartItemId === cartItemId);
                        if (index !== -1) {
                            if (this.cart[index].quantity > 1) {
                                this.cart[index].quantity--;
                            } else {
                                this.cart.splice(index, 1);
                            }
                        }
                    },

                    addQuantityFromCart(item) {
                        if (item.quantity < item.available_stock) {
                            item.quantity++;
                        } else {
                            this.stockAlert = { title: 'Stok Terbatas', message: 'Sisa stok tinggal ' + item.available_stock + ' item.' };
                        }
                    },

                    clearCart() {
                        this.cart = [];
                        this.payment_amount = 0;
                        this.buyer_name = '';
                        this.status = 'uang_diterima';
                        this.payment_method = 'cash';
                        this.note = '';
                    },

                    formatRupiah(number) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(number || 0).replace('Rp', 'Rp ');
                    },

                    toggleTheme() {
                        this.darkMode = !this.darkMode;
                        if (this.darkMode) {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('theme', 'light');
                        }
                    },

                    getCategoryColor(name) {
                        if (!name) return 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-900/50';
                        const upper = name.toUpperCase();
                        if (upper.includes('KERIPIK') || upper.includes('SNACK')) {
                            return 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-900/50';
                        }
                        if (upper.includes('KERUPUK') || upper.includes('MAKANAN')) {
                            return 'bg-rose-50 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-900/50';
                        }
                        if (upper.includes('MINUMAN') || upper.includes('KOPI')) {
                            return 'bg-blue-50 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-900/50';
                        }
                        return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700';
                    },

                    checkout() {
                        if (this.loading) return;
                        this.loading = true;

                        const totalVal = this.total;
                        const paymentVal = this.payment_amount > 0 ? this.payment_amount : this.total;
                        const changeVal = this.change;

                        this.$wire.checkout(this.cart, this.total, this.change, this.buyer_name, this.status, this.note, this.$wire.transactionDate, this.payment_method).then(() => {
                            this.clearCart();
                            this.loading = false;
                            this.lastChangeData = { total: totalVal, payment: paymentVal, change: changeVal };
                            this.showChangeModal = true;
                        }).catch(() => {
                            this.loading = false;
                        });
                    },

                    handleCheckoutKeydown(e) {
                        if (e.key === 'Enter' && !e.ctrlKey && !e.shiftKey && !e.altKey) {
                            if (this.cart.length > 0 && !(this.payment_amount < this.total && this.status === 'uang_diterima') && !this.loading) {
                                e.preventDefault();
                                this.checkout();
                            }
                        }
                    }
                }));
            }
        };

        if (window.Alpine && window.Alpine.data) {
            initKasirApp();
        } else {
            document.addEventListener('alpine:init', initKasirApp);
        }
    </script>
</div>
