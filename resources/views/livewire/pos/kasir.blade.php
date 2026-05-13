<div x-data="{
    showCart: false,
    search: '',
    selectedCategory: null,
    products: @js($allProductsJson),
    modalSearch: '',
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    
    get filteredProducts() {
        if (!this.search && !this.selectedCategory) return this.products;
        return this.products.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(this.search.toLowerCase());
            const matchesCategory = !this.selectedCategory || p.category_id == this.selectedCategory;
            return matchesSearch && matchesCategory;
        });
    },

    payment_amount: @entangle('payment_amount'),
    
    get total() {
        // We use $wire.cart to ensure reactivity when items are added/removed
        const cart = this.$wire.cart;
        return Object.values(cart).reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },

    get change() {
        if (this.payment_amount > 0) {
            return this.payment_amount - this.total;
        }
        return 0;
    },

    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number).replace('Rp', 'Rp ');
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
    }
}" x-init="if (darkMode) document.documentElement.classList.add('dark');
else document.documentElement.classList.remove('dark')"
    class="flex flex-col lg:flex-row h-screen w-full bg-[#f8fafc] dark:bg-gray-950 overflow-hidden font-outfit relative">

    <!-- Global Loading Indicator -->
    <div wire:loading.flex
        class="fixed inset-0 z-[9999] bg-gray-950/60 backdrop-blur-[2px] items-center justify-center flex-col gap-6 transition-all animate-in fade-in duration-300">
        <div class="relative">
            <div
                class="w-20 h-20 border-[6px] border-primary-blue/20 rounded-full animate-spin border-t-primary-blue shadow-2xl shadow-blue-500/20">
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-10 h-10 bg-primary-blue rounded-xl animate-pulse"></div>
            </div>
        </div>
        <div class="flex flex-col items-center">
            <p class="text-white font-black italic uppercase tracking-[0.4em] text-sm animate-pulse">Processing</p>
            <p class="text-blue-400 text-[10px] font-black uppercase tracking-widest mt-1 opacity-60">LABANTIK POS
                SYSTEM</p>
        </div>
    </div>
    <style>
        /* Custom Global Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #3b82f6 transparent;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            /* primary-blue */
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #3b82f6;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        [x-cloak] { display: none !important; }
    </style>
    <!-- 1. Main Content Area (Search + Products) -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 dark:bg-gray-950/50 z-10 overflow-hidden">

        <!-- Header Section -->
        <div
            class="px-6 lg:px-10 py-6 lg:py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 z-20 shadow-sm">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 lg:gap-10">
                <div class="flex items-center justify-between w-full md:w-auto gap-6">
                    <div class="flex items-center gap-4 lg:gap-6">
                        <a href="{{ route('dashboard') }}"
                            class="w-12 h-12 lg:w-14 lg:h-14 bg-white rounded-[1.2rem] lg:rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-blue-500/10 hover:scale-110 hover:rotate-3 transition-all p-2 overflow-hidden border border-gray-100 dark:border-gray-800">
                            <img src="{{ asset('favicon.png') }}" class="w-full h-full object-contain">
                        </a>
                        <div class="h-10 lg:h-12 w-[1px] bg-gray-100 dark:bg-gray-800"></div>
                        <div x-data="{
                            currentTime: '',
                            updateTime() {
                                const now = new Date();
                                this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                            }
                        }" x-init="updateTime();
                        setInterval(() => updateTime(), 1000)">
                            <h1
                                class="text-xl lg:text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-tight">
                                LABANTIK POS</h1>
                            <div class="flex items-center gap-2">
                                <p
                                    class="text-[9px] lg:text-[10px] font-black text-primary-blue uppercase tracking-[0.2em]">
                                    {{ now()->translatedFormat('d F Y') }}</p>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                                <p x-text="currentTime"
                                    class="text-[9px] lg:text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Cart Toggle -->
                    <button @click="showCart = true"
                        class="lg:hidden p-4 bg-primary-blue text-white rounded-2xl shadow-xl relative">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="8" cy="21" r="1" />
                            <circle cx="19" cy="21" r="1" />
                            <path
                                d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.56-7.43H5.12" />
                        </svg>
                        @if (count($cart) > 0)
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 bg-primary-red text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white dark:border-gray-900">{{ count($cart) }}</span>
                        @endif
                    </button>
                </div>

                <div class="flex-1 w-full max-w-2xl relative group">
                    <div class="absolute inset-y-0 left-0 pl-7 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary-blue transition-colors"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Cari menu (Instan)..."
                        class="w-full pl-16 pr-8 py-4 lg:py-5 bg-gray-50 dark:bg-gray-800 rounded-[1.5rem] lg:rounded-[2rem] border-none focus:ring-4 focus:ring-primary-blue/10 font-black text-sm lg:text-base text-gray-800 dark:text-white placeholder:text-gray-300 placeholder:italic transition-all">
                </div>

                <div class="flex items-center gap-3 lg:gap-5">
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()"
                        class="p-4 lg:p-5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all active:scale-95 shadow-sm">
                        <svg x-show="!darkMode" class="w-5 h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2" />
                            <path d="M12 20v2" />
                            <path d="m4.93 4.93 1.41 1.41" />
                            <path d="m17.66 17.66 1.41 1.41" />
                            <path d="M2 12h2" />
                            <path d="M20 12h2" />
                            <path d="m6.34 17.66-1.41 1.41" />
                            <path d="m19.07 4.93-1.41 1.41" />
                        </svg>
                    </button>

                    <button wire:click="editOpeningStock"
                        class="p-4 lg:p-5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all active:scale-95 shadow-sm">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m16 6 4 14" />
                            <path d="M12 6v14" />
                            <path d="M8 8v12" />
                            <path d="M4 4v16" />
                        </svg>
                    </button>

                    <div class="h-8 w-[1px] bg-gray-100 dark:bg-gray-800 hidden md:block mx-1"></div>

                    <div class="hidden md:flex items-center gap-3">
                        <button wire:click="finishSession"
                            class="flex items-center gap-4 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-6 lg:px-8 py-4 lg:py-5 rounded-[1.2rem] lg:rounded-[1.5rem] shadow-2xl transition-all active:scale-95 font-black text-[10px] lg:text-[11px] uppercase tracking-[0.2em]">
                            Selesai
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="p-4 lg:p-5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl hover:text-primary-red hover:bg-primary-red/5 transition-all active:scale-95 shadow-sm group">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6 group-hover:translate-x-1 transition-transform"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1 2-2V5a2 2 0 0 1-2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" x2="9" y1="12" y2="12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Navigation -->
        <div
            class="px-6 lg:px-10 py-4 lg:py-6 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3 lg:gap-4 overflow-x-auto scrollbar-hide">
            <button @click="selectedCategory = null"
                :class="selectedCategory === null ? 'bg-primary-blue text-white shadow-xl shadow-blue-500/30 scale-105' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-primary-blue hover:bg-primary-blue/5'"
                class="px-6 lg:px-8 py-3 lg:py-3.5 rounded-xl lg:rounded-2xl whitespace-nowrap text-[10px] lg:text-[11px] font-black uppercase tracking-widest transition-all">
                Semua Menu
            </button>
            @foreach ($this->categories as $cat)
                <button @click="selectedCategory = {{ $cat->id }}"
                    :class="selectedCategory == {{ $cat->id }} ? 'bg-primary-blue text-white shadow-xl shadow-blue-500/30 scale-105' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-primary-blue hover:bg-primary-blue/5'"
                    class="px-6 lg:px-8 py-3 lg:py-3.5 rounded-xl lg:rounded-2xl whitespace-nowrap text-[10px] lg:text-[11px] font-black uppercase tracking-widest transition-all">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid Section -->
        <div class="flex-1 overflow-y-auto px-6 lg:px-8 py-6 lg:py-8 scrollbar-hide bg-gray-50/30 dark:bg-gray-950/30">
            
            <div x-show="filteredProducts.length === 0" x-cloak class="h-full flex flex-col items-center justify-center opacity-30 py-20 lg:py-32">
                <div
                    class="w-32 h-32 lg:w-40 lg:h-40 bg-white dark:bg-gray-900 rounded-[3rem] lg:rounded-[4rem] flex items-center justify-center mb-8 lg:mb-10 shadow-inner">
                    <svg class="w-16 h-16 lg:w-20 lg:h-20 text-gray-200" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                        <line x1="8" y1="11" x2="14" y2="11" />
                    </svg>
                </div>
                <h3
                    class="text-3xl lg:text-4xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                    Kosong</h3>
                <p
                    class="text-gray-400 font-bold text-sm lg:text-base mt-4 uppercase tracking-[0.4em] italic text-center">
                    Menu tidak ditemukan</p>
            </div>

            <div x-show="filteredProducts.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5 gap-4 lg:gap-6">
                
                <template x-for="product in filteredProducts" :key="product.id">
                    <button @click="$wire.addToCart(product.id)"
                        class="group relative bg-white dark:bg-gray-900 rounded-[2rem] lg:rounded-[2.5rem] p-5 lg:p-6 text-left shadow-xl shadow-blue-900/5 border-2 border-transparent hover:border-primary-blue/30 hover:-translate-y-1 lg:hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary-blue/20 transition-all duration-500 flex flex-col h-full overflow-hidden">
                        <div class="relative z-10 flex flex-col h-full">
                            <div
                                class="w-12 h-12 lg:w-14 lg:h-14 bg-gray-50 dark:bg-gray-800 rounded-xl lg:rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-primary-blue group-hover:text-white transition-all duration-500 mb-4 lg:mb-6 shadow-sm">
                                <span x-text="product.initial"
                                    class="text-lg lg:text-xl font-black italic uppercase"></span>
                            </div>
                            <div class="mb-4 flex-1">
                                <h3 x-text="product.name"
                                    class="text-xs lg:text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight leading-tight transition-colors duration-300 line-clamp-2">
                                </h3>
                                <p x-text="product.category_name"
                                    class="text-[8px] lg:text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1.5 group-hover:text-primary-blue transition-colors duration-300">
                                </p>
                            </div>
                            <div class="flex items-center justify-between mt-auto">
                                <span x-text="formatRupiah(product.price)"
                                    class="text-sm lg:text-base font-black text-primary-red italic group-hover:scale-105 transition-transform">
                                </span>
                                <div
                                    class="w-7 h-7 lg:w-8 lg:h-8 bg-gray-50 dark:bg-gray-800 rounded-lg lg:rounded-xl flex items-center justify-center text-gray-300 group-hover:bg-primary-blue group-hover:text-white group-hover:rotate-90 transition-all duration-500">
                                    <svg class="w-3 h-3 lg:w-4 lg:h-4" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M5 12h14" />
                                        <path d="M12 5v14" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div
                            class="absolute top-5 lg:top-6 right-5 lg:right-6 px-2.5 lg:px-3 h-5 lg:h-6 bg-gray-50 dark:bg-gray-900 rounded-full group-hover:bg-primary-blue/10 transition-all flex items-center justify-center">
                            <span
                                class="text-[7px] lg:text-[8px] font-black text-gray-400 group-hover:text-primary-blue uppercase tracking-[0.2em] leading-none">Tersedia</span>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- 2. Enhanced Right Sidebar (Cart & Checkout) -->
    <div x-show="window.innerWidth >= 1024 || showCart" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full" @resize.window="if(window.innerWidth >= 1024) showCart = false"
        class="fixed inset-y-0 right-0 w-full md:w-[500px] bg-white dark:bg-gray-900 border-l border-gray-100 dark:border-gray-800 flex flex-col shadow-2xl z-[100] lg:static lg:flex lg:w-[500px] lg:shadow-none lg:translate-x-0"
        style="display: none;" x-cloak>
        <!-- Mobile Header (Only visible on mobile) -->
        <div
            class="lg:hidden p-6 flex justify-between items-center border-b dark:border-gray-800 bg-white dark:bg-gray-900">
            <h2 class="text-xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Pesanan Saya
            </h2>
            <button @click="showCart = false" class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl text-gray-400">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div x-data="{ tab: 'cart' }" class="flex flex-col h-full overflow-hidden bg-white dark:bg-gray-900">
            <!-- Sidebar Tabs -->
            <div
                class="p-2 mx-6 lg:mx-8 mt-6 lg:mt-8 bg-gray-50 dark:bg-gray-950 rounded-[1.5rem] lg:rounded-[1.8rem] flex border border-gray-100 dark:border-gray-800 shadow-inner flex-shrink-0">
                <button @click="tab = 'cart'"
                    :class="tab === 'cart' ? 'bg-white dark:bg-gray-800 text-primary-blue shadow-lg scale-[1.02]' :
                        'text-gray-400 hover:text-primary-blue'"
                    class="flex-1 py-3 lg:py-3.5 rounded-xl lg:rounded-[1.3rem] text-[9px] lg:text-[10px] font-black uppercase tracking-[0.2em] transition-all">Keranjang</button>
                <button @click="tab = 'history'"
                    :class="tab === 'history' ? 'bg-white dark:bg-gray-800 text-primary-red shadow-lg scale-[1.02]' :
                        'text-gray-400 hover:text-primary-red'"
                    class="flex-1 py-3 lg:py-3.5 rounded-xl lg:rounded-[1.3rem] text-[9px] lg:text-[10px] font-black uppercase tracking-[0.2em] transition-all">Riwayat</button>
            </div>

            <!-- Cart Items List -->
            <div x-show="tab === 'cart'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                class="flex-1 overflow-y-auto p-8 space-y-6 scrollbar-hide min-h-0">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                        Pesanan</h2>
                    <button wire:click="clearCart"
                        class="text-[9px] font-black text-gray-400 hover:text-primary-red uppercase tracking-widest transition-colors">Kosongkan</button>
                </div>

                @forelse($cart as $item)
                    <div
                        class="flex items-center gap-4 group animate-in slide-in-from-right duration-300 bg-gray-50/50 dark:bg-gray-800/30 p-5 rounded-[2rem] border border-transparent hover:border-primary-blue/20 transition-all shadow-sm">
                        <div
                            class="w-12 h-12 bg-white dark:bg-gray-900 rounded-2xl flex items-center justify-center text-primary-blue text-xs font-black italic shadow-md group-hover:scale-110 transition-transform">
                            {{ substr($item['name'], 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">
                                {{ $item['name'] }}</h4>
                            <p class="text-[10px] font-black text-primary-red italic mt-0.5">
                                Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <div
                            class="flex items-center bg-white dark:bg-gray-900 rounded-xl p-1 shadow-md border border-gray-100 dark:border-gray-800">
                            <button wire:click="removeFromCart({{ $item['id'] }})"
                                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-primary-red transition-colors font-black text-base">-</button>
                            <span
                                class="w-10 text-center text-[10px] font-black text-gray-800 dark:text-white">{{ $item['quantity'] }}</span>
                            <button wire:click="addToCart({{ $item['id'] }})"
                                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-primary-blue transition-colors font-black text-base">+</button>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center py-20 opacity-20">
                        <svg class="w-24 h-24 mb-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="8" cy="21" r="1" />
                            <circle cx="19" cy="21" r="1" />
                            <path
                                d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.56-7.43H5.12" />
                        </svg>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] italic">Belum ada pesanan</p>
                    </div>
                @endforelse
            </div>

            <div x-show="tab === 'history'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                class="flex-1 overflow-y-auto p-8 space-y-5 scrollbar-hide min-h-0">
                <h2 class="text-xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-6">
                    History</h2>
                @foreach ($this->recentTransactions as $history)
                    <div
                        class="flex items-center gap-5 p-5 bg-gray-50/50 dark:bg-gray-800/30 rounded-[2rem] border border-transparent hover:border-primary-blue/20 transition-all group">
                        <div
                            class="w-12 h-12 bg-white dark:bg-gray-900 rounded-xl flex items-center justify-center text-primary-blue text-[9px] font-black italic shadow-md">
                            {{ \Carbon\Carbon::parse($history->transacted_at)->format('H:i') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-[11px] font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">
                                {{ $history->reference }}</h4>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span
                                    class="text-[8px] font-black {{ $history->status === 'uang_diterima' ? 'text-green-500' : 'text-primary-red' }} uppercase tracking-[0.2em]">{{ str_replace('_', ' ', $history->status) }}</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase">• {{ $history->total_qty }}
                                    Items</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-black text-gray-800 dark:text-white italic">
                                Rp{{ number_format($history->total_amount, 0, ',', '.') }}</p>
                            <button wire:click="viewDetails('{{ $history->reference }}')"
                                class="mt-1 text-[8px] font-black text-gray-300 hover:text-primary-blue uppercase tracking-widest transition-colors">Detail</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Checkout Section -->
            <div
                class="p-8 bg-white dark:bg-gray-950 border-t-2 border-gray-50 dark:border-gray-800 space-y-6 shadow-[0_-30px_80px_-20px_rgba(0,0,0,0.06)] flex-shrink-0">

                <!-- Summary Stats -->
                <div
                    class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 flex flex-col gap-1">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Total Tagihan</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-base font-black italic text-primary-blue tracking-tighter">Rp</span>
                        <span x-text="formatRupiah(total).replace('Rp', '').trim()"
                            class="text-5xl font-black italic text-primary-blue tracking-tighter leading-none"></span>
                    </div>
                </div>

                <!-- Enhanced Input Group -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-5">Nama
                            Pembeli</label>
                        <input type="text" wire:model="buyer_name" placeholder="Guest"
                            class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-[1.5rem] focus:ring-4 focus:ring-primary-blue/10 font-black text-xs uppercase tracking-tight text-gray-800 dark:text-white">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-5">Nominal
                            Bayar</label>
                        <div class="relative">
                            <span
                                class="absolute left-6 inset-y-0 flex items-center text-[10px] font-black text-gray-300 italic">Rp</span>
                            <input type="number" x-model.number="payment_amount"
                                class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-[1.5rem] focus:ring-4 focus:ring-primary-blue/10 font-black text-xl text-primary-blue dark:text-primary-blue-light italic">
                        </div>
                    </div>
                </div>

                <!-- Payment Status Selector -->
                <div class="flex gap-2">
                    @php
                        $statuses = [
                            'uang_diterima' => [
                                'label' => 'LUNAS',
                                'color' => 'bg-green-500',
                                'icon' => 'M20 6 9 17 4 12',
                            ],
                            'belum_kembalian' => [
                                'label' => 'PENDING',
                                'color' => 'bg-primary-blue',
                                'icon' => 'M12 8v4l3 3',
                            ],
                            'belum_menerima_uang' => [
                                'label' => 'HUTANG',
                                'color' => 'bg-primary-red',
                                'icon' =>
                                    'M12 9v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                            ],
                        ];
                    @endphp
                    @foreach ($statuses as $val => $cfg)
                        <button wire:click="$set('status', '{{ $val }}')"
                            class="flex-1 py-4 rounded-[1.5rem] flex items-center justify-center gap-3 transition-all {{ $status === $val ? $cfg['color'] . ' text-white shadow-2xl scale-[1.02]' : 'bg-gray-50 dark:bg-gray-900 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $cfg['icon'] }}" />
                            </svg>
                            <span class="text-[9px] font-black uppercase tracking-[0.2em]">{{ $cfg['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Change/Due Indicator -->
                <template x-if="payment_amount > 0">
                    <div
                        class="px-6 py-4 rounded-[1.5rem] flex justify-between items-center animate-in slide-in-from-top-4 duration-500 border border-transparent"
                        :class="change < 0 ? 'bg-red-500/10 border-red-500/10' : 'bg-green-500/10 border-green-500/10'">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2 h-2 rounded-full animate-pulse"
                                :class="change < 0 ? 'bg-primary-red' : 'bg-green-500'">
                            </div>
                            <span x-text="change < 0 ? 'Kurang Bayar' : 'Uang Kembalian'"
                                class="text-[9px] font-black uppercase tracking-[0.3em]"
                                :class="change < 0 ? 'text-primary-red' : 'text-green-600'"></span>
                        </div>
                        <span x-text="formatRupiah(Math.abs(change))"
                            class="text-2xl font-black italic"
                            :class="change < 0 ? 'text-primary-red' : 'text-green-600'"></span>
                    </div>
                </template>

                <!-- Final Action -->
                <div class="space-y-4 pt-1">
                    <textarea wire:model="note" rows="2" placeholder="Catatan transaksi..."
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-[1.5rem] focus:ring-4 focus:ring-primary-blue/10 font-bold text-xs text-gray-800 dark:text-white placeholder:text-gray-300"></textarea>
                    <button wire:click="checkout" :disabled="!Object.keys($wire.cart).length || change < 0"
                        class="w-full py-6 bg-primary-blue text-white rounded-[2rem] shadow-[0_30px_80px_-20px_rgba(59,130,246,0.5)] hover:scale-[1.02] active:scale-95 transition-all font-black italic uppercase text-base tracking-[0.3em] flex items-center justify-center gap-5 group disabled:opacity-30 disabled:scale-100 disabled:shadow-none overflow-hidden relative">
                        <span class="relative z-10">Proses Pesanan</span>
                        <svg class="w-5 h-5 group-hover:translate-x-3 transition-transform relative z-10"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                        <div
                            class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Overlay -->
    <div x-data="{ show: false }" x-on:transaction-complete.window="show = true; setTimeout(() => show = false, 1200)"
        x-show="show" x-cloak x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 backdrop-blur-0" x-transition:enter-end="opacity-100 backdrop-blur-md"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 backdrop-blur-md"
        x-transition:leave-end="opacity-0 backdrop-blur-0"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/20">
        <div x-show="show" x-transition:enter="transition ease-out duration-500 delay-100"
            x-transition:enter-start="opacity-0 scale-50 translate-y-20"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
            class="bg-white dark:bg-gray-900 p-20 rounded-[6rem] shadow-[0_50px_150px_-30px_rgba(0,0,0,0.4)] flex flex-col items-center gap-10 border-t-8 border-green-500">
            <div
                class="w-36 h-36 bg-green-500 text-white rounded-full flex items-center justify-center shadow-2xl shadow-green-500/40 animate-bounce">
                <svg class="w-20 h-20" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </div>
            <div class="text-center">
                <h3
                    class="text-5xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">
                    BERHASIL!</h3>
                <p class="text-gray-400 font-bold text-base mt-5 uppercase tracking-[0.4em] italic">Transaksi Telah
                    Dicatat</p>
            </div>
        </div>
    </div>

    <!-- Modals Section -->

    <!-- Opening Stock Modal -->
    <div x-data="{ show: @entangle('showOpeningStockModal') }" x-show="show" x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-gray-900 w-full max-w-5xl max-h-[85vh] overflow-hidden rounded-[4rem] shadow-2xl flex flex-col relative"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <!-- Close Button -->
            <button @click="show = false"
                class="absolute top-10 right-10 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-primary-red hover:bg-primary-red/10 transition-all z-50 group">
                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
            <div
                class="p-12 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-gray-50/30 dark:bg-gray-950/30">
                <div>
                    <h2 class="text-4xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                        Stok Awal Hari Ini</h2>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-3">Silakan verifikasi jumlah
                        stok sebelum mulai berjualan</p>
                </div>
                <div class="flex items-center gap-6 pr-16">
                    <div class="relative">
                        <input type="text" x-model="modalSearch" placeholder="Cari menu..."
                            class="pl-12 pr-6 py-4 bg-white dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 text-xs font-black uppercase tracking-widest shadow-sm">
                        <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-12 scrollbar-hide">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach (\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $p)
                        <div wire:key="opening-stock-{{ $p->id }}" x-show="'{{ strtolower($p->name) }}'.includes(modalSearch.toLowerCase())"
                            class="flex items-center justify-between p-8 bg-gray-50 dark:bg-gray-800/50 rounded-[2.5rem] border-2 border-transparent hover:border-primary-blue/20 transition-all shadow-sm">
                            <div class="min-w-0">
                                <h4
                                    class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">
                                    {{ $p->name }}</h4>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">
                                    {{ $p->category ? $p->category->name : 'No Category' }}</p>
                            </div>
                            <div class="w-36">
                                <div class="relative group">
                                    <input type="number" wire:model.blur="stockItems.{{ $p->id }}"
                                        class="w-full px-6 py-4 bg-white dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-center shadow-inner text-gray-800 dark:text-white transition-all">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="p-12 bg-white dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800">
                <button wire:click="saveOpeningStock"
                    class="w-full py-7 bg-primary-blue text-white rounded-[2rem] shadow-2xl shadow-blue-500/30 font-black italic uppercase tracking-[0.3em] text-lg hover:-translate-y-2 transition-all">SIMPAN
                    & MULAI JUALAN</button>
            </div>
        </div>
    </div>

    <!-- Closing Stock Modal -->
    <div x-data="{ show: @entangle('showClosingStockModal') }" x-show="show" x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-gray-900 w-full max-w-5xl max-h-[85vh] overflow-hidden rounded-[4rem] shadow-2xl flex flex-col relative"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <!-- Close Button -->
            <button @click="show = false"
                class="absolute top-10 right-10 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-primary-red hover:bg-primary-red/10 transition-all z-50 group">
                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
            <div
                class="p-12 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-gray-50/30 dark:bg-gray-950/30">
                <div>
                    <h2 class="text-4xl font-black italic uppercase tracking-tighter text-primary-red">Akhiri Sesi Hari
                        Ini</h2>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-3">Input jumlah stok sisa
                        untuk perhitungan laporan harian</p>
                </div>
                <div class="flex items-center gap-6 pr-16">
                    <div class="relative">
                        <input type="text" x-model="modalSearch" placeholder="Cari menu..."
                            class="pl-12 pr-6 py-4 bg-white dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 text-xs font-black uppercase tracking-widest shadow-sm">
                        <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-12 scrollbar-hide">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach (\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $p)
                        <div wire:key="closing-stock-{{ $p->id }}" x-show="'{{ strtolower($p->name) }}'.includes(modalSearch.toLowerCase())"
                            class="flex items-center justify-between p-8 bg-gray-50 dark:bg-gray-800/50 rounded-[2.5rem] border-2 border-transparent hover:border-primary-red/20 transition-all shadow-sm">
                            <div class="min-w-0">
                                <h4
                                    class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">
                                    {{ $p->name }}</h4>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">
                                    {{ $p->category ? $p->category->name : 'No Category' }}</p>
                            </div>
                            <div class="w-36">
                                <div class="relative group">
                                    <input type="number" wire:model.blur="stockItems.{{ $p->id }}"
                                        class="w-full px-6 py-4 bg-white dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-red/10 font-black text-lg text-center shadow-inner text-gray-800 dark:text-white transition-all">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="p-12 bg-white dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800">
                <button wire:click="saveClosingStock"
                    class="w-full py-7 bg-primary-red text-white rounded-[2rem] shadow-2xl shadow-red-500/30 font-black italic uppercase tracking-[0.3em] text-lg hover:-translate-y-2 transition-all">SIMPAN
                    & TUTUP KASIR</button>
            </div>
        </div>
    </div>


    <script>
        window.addEventListener('transaction-complete', () => {
            setTimeout(() => {
                const searchInput = document.querySelector('input[placeholder*="Cari menu"]');
                if (searchInput) searchInput.focus();
            }, 1000);
        });
    </script>

    <!-- Transaction Detail Modal -->
    <div x-data="{ show: @entangle('showDetailsModal') }" x-show="show" x-cloak
        class="fixed inset-0 z-[400] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-xl rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-8 bg-primary-blue text-white relative">
                <div class="absolute right-8 top-8">
                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <h3 class="text-2xl font-black italic uppercase tracking-tighter mb-1">Rincian Belanja</h3>
                <p class="text-[9px] font-bold uppercase tracking-[0.3em] opacity-60">REF: {{ $detailReference }}</p>
            </div>

            <div class="p-8 max-h-[50vh] overflow-y-auto no-scrollbar">
                <div class="space-y-6">
                    @if($this->detailItems)
                        @foreach ($this->detailItems as $item)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                        {{ $item->product->name }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                        {{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-black text-gray-800 dark:text-white italic">
                                    Rp{{ number_format($item->total_price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div
                class="p-8 bg-gray-50 dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                    <span
                        class="px-3 py-1 rounded-full text-[8px] font-black uppercase {{ ($this->detailItems->first()->status ?? '') === 'uang_diterima' ? 'bg-green-100 text-green-700' : 'bg-primary-red/10 text-primary-red' }}">
                        {{ str_replace('_', ' ', $this->detailItems->first()->status ?? 'Unknown') }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total</p>
                    <p class="text-2xl font-black text-primary-blue italic tracking-tighter leading-none">
                        Rp{{ number_format($this->detailItems->sum('total_price'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
