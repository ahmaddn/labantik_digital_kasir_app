<div wire:poll.5s="checkNewTasks" x-data="{
    showCart: false,
    search: '',
    selectedCategory: null,
    products: @entangle('products'),
    cart: [],
    loading: false,
    modalSearch: '',
    stockAlert: null,
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

    payment_amount: 0,
    buyer_name: '',
    status: 'uang_diterima',
    note: '',

    showChangeModal: false,
    lastChangeData: { total: 0, payment: 0, change: 0 },
    showQuickExpenseModal: false,
    quickExpenseAmount: '',
    quickExpenseCategoryId: '',
    quickExpenseDescription: '',
    quickExpenseLoading: false,

    get filteredProducts() {
        return this.products.filter(p => {
            const matchesSearch = !this.search || p.name.toLowerCase().includes(this.search.toLowerCase());
            const matchesCategory = this.selectedCategory === null || String(p.category_id) === String(this.selectedCategory);
            return matchesSearch && matchesCategory;
        });
    },

    get total() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },

    get change() {
        if (this.payment_amount > 0) {
            return this.payment_amount - this.total;
        }
        return 0;
    },

    addToCart(product) {
        const index = this.cart.findIndex(item => item.id === product.id);
        if (index !== -1) {
            if (this.cart[index].quantity < product.available_stock) {
                this.cart[index].quantity = this.cart[index].quantity + 1;
            } else {
                this.stockAlert = { title: 'STOK TERBATAS', message: 'Sisa stok ' + product.name + ' tinggal ' + product.available_stock + ' item.' };
            }
        } else {
            if (product.available_stock > 0) {
                this.cart = [...this.cart, {
                    ...product,
                    quantity: 1
                }];
            } else {
                this.stockAlert = { title: 'STOK HABIS', message: 'Maaf, stok ' + product.name + ' sudah habis hari ini.' };
            }
        }
    },

    handleProductClick(e) {
        if (e.target.closest('button[data-add-to-cart]')) {
            const productId = e.target.closest('button').dataset.productId;
            const product = this.products.find(p => p.id === productId);
            if (product) {
                this.addToCart(product);
            }
        }
    },

    removeFromCart(productId) {
        const index = this.cart.findIndex(item => item.id === productId);
        if (index !== -1) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
            } else {
                this.cart.splice(index, 1);
            }
        }
    },

    clearCart() {
        this.cart = [];
        this.payment_amount = 0;
        this.buyer_name = '';
        this.status = 'uang_diterima';
        this.note = '';
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
    },

    getCategoryColor(name) {
        const colors = {
            'SNACK': 'bg-primary-yellow text-black border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
            'MINUMAN': 'bg-primary-blue text-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
            'MAKANAN': 'bg-primary-red text-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
            'ESKRIM': 'bg-purple-500 text-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
            'DEFAULT': 'bg-white text-black border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]'
        };
        return colors[name.toUpperCase()] || colors['DEFAULT'];
    },

    getCategoryBorderColor(name) {
        const borders = {
            'SNACK': 'border-t-4 border-t-primary-yellow',
            'MINUMAN': 'border-t-4 border-t-primary-blue',
            'MAKANAN': 'border-t-4 border-t-primary-red',
            'ESKRIM': 'border-t-4 border-t-purple-500',
            'DEFAULT': 'border-t-4 border-t-gray-400'
        };
        return borders[name.toUpperCase()] || borders['DEFAULT'];
    },

    checkout() {
        if (this.loading) return;
        this.loading = true;

        const totalVal = this.total;
        const paymentVal = this.payment_amount > 0 ? this.payment_amount : this.total;
        const changeVal = this.change;

        this.$wire.checkout(this.cart, this.total, this.change, this.buyer_name, this.status, this.note, this.$wire.transactionDate).then(() => {
            this.clearCart();
            this.loading = false;

            // Show Change Due Modal
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
}" x-init="if (darkMode) document.documentElement.classList.add('dark');
else document.documentElement.classList.remove('dark');

// Autofocus search on load
$nextTick(() => {
    const searchInput = document.getElementById('pos-search-input');
    if (searchInput) searchInput.focus();
});

// Setup event listeners that persist even after idle
const gridContainer = document.querySelector('[data-product-grid]');
if (gridContainer) {
    gridContainer.addEventListener('click', (e) => $dispatch('product-click', { event: e }));
}

// Setup keydown listener for checkout
document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.ctrlKey && !e.shiftKey && !e.altKey) {
        const activeElement = document.activeElement;
        if (activeElement.name === 'payment_amount' || activeElement.classList.contains('checkout-trigger')) {
            this.handleCheckoutKeydown(e);
        }
    }
});"
    class="flex flex-col lg:flex-row h-screen w-full bg-slate-50 dark:bg-dark-bg overflow-hidden font-outfit relative"
    x-on:stock-saved.window="products = $event.detail.products"
    x-on:product-click="handleProductClick($event.detail.event)"
    x-on:keydown.window.escape="search = ''; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })"
    x-on:keydown.window.prevent.slash="if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') { $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); }) }">

    <!-- Global Loading Indicator (only for key actions, exclude short polling) -->
    <div wire:loading.flex.delay
        wire:target="checkout, saveQuickExpense, saveOpeningStock, saveClosingStockAndNext, submitClosingReport"
        style="display: none;"
        class="fixed inset-0 z-[9999] bg-white/40 dark:bg-black/60 backdrop-blur-md items-center justify-center flex-col gap-6">
        <div class="nb-card p-12 bg-white dark:bg-dark-soft flex flex-col items-center gap-8 animate-brutal-bounce">
            <div class="relative w-24 h-24">
                <div class="absolute inset-0 border-4 border-black dark:border-white opacity-20"></div>
                <div
                    class="absolute inset-0 border-4 border-black dark:border-white border-t-primary-blue animate-brutal-spin shadow-[4px_4px_0_0_rgba(0,0,0,1)] dark:shadow-[4px_4px_0_0_rgba(255,255,255,0.2)]">
                </div>
                <div class="absolute -top-2 -right-2 w-6 h-6 bg-primary-red border-2 border-black animate-pulse"></div>
            </div>
            <div class="text-center">
                <h3
                    class="text-2xl font-black italic uppercase tracking-tighter text-black dark:text-white leading-none">
                    MEMPROSES...</h3>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.6em] mt-3">SEDANG MENCATAT TRANSAKSI
                </p>
            </div>
        </div>
    </div>

    <!-- 1. Main Content Area -->
    <div
        class="flex-1 flex flex-col min-w-0 z-10 overflow-hidden lg:border-r-[var(--nb-border)] border-black dark:border-slate-800">

        <!-- Header Section -->
        <div
            class="px-6 lg:px-10 py-5 bg-primary-blue dark:bg-slate-900 border-b-[var(--nb-border)] border-black dark:border-slate-800 shadow-[0_4px_0_0_rgba(0,0,0,1)] dark:shadow-[0_4px_0_0_rgba(0,0,0,0.5)]">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
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
                        class="theme-no-card nb-card-flat p-2 w-14 h-14 bg-white flex items-center justify-center hover:scale-110 transition-transform">
                        <img src="{{ $tefaLogo ? asset('storage/' . $tefaLogo) : asset('favicon.png') }}"
                            class="w-full h-full object-contain">
                    </a>
                    <div>
                        <h1 class="text-xl lg:text-2xl font-black uppercase tracking-tighter text-white leading-none">
                            {{ $tefaName }}</h1>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span
                                class="text-[9px] font-black bg-black text-white px-2 py-0.5 uppercase tracking-widest border border-white">{{ now()->translatedFormat('d F Y') }}</span>
                            <span x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour12: false }), 1000)" x-text="time"
                                class="text-[9px] font-black bg-slate-100 dark:bg-black text-black dark:text-white px-2 py-0.5 uppercase tracking-widest border border-black dark:border-white"></span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 w-full max-w-lg">
                    <input type="text" id="pos-search-input" x-ref="searchInput" x-model="search"
                        placeholder="CARI MENU (INSTAN)..."
                        class="nb-input w-full p-3 text-sm uppercase placeholder:text-gray-400 bg-white dark:bg-slate-800 border-white shadow-none focus:ring-0">
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Global Notifications Bell -->
                    @livewire('note-notifications')
                    <button @click="showQuickExpenseModal = true"
                        class="nb-btn px-4 py-3 bg-primary-red text-white shadow-none border-2 border-black flex items-center gap-2 hover:scale-105 transition-transform text-xs font-black uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Catat Pengeluaran
                    </button>
                    <button @click="toggleTheme()" class="nb-btn p-3 bg-white dark:bg-dark-soft shadow-none border-2">
                        <svg x-show="!darkMode" class="w-5 h-5 text-black" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 17.95l.707.707M7.05 7.05l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>
                    <a href="{{ route('inventory-report') }}" wire:navigate
                        class="nb-btn py-3 px-4 bg-primary-yellow text-black text-xs shadow-none border-2 hidden md:block">SELISIH</a>
                    <button wire:click="editOpeningStock"
                        class="nb-btn py-3 px-4 bg-white text-black text-xs shadow-none border-2">STOK</button>
                    <button wire:click="finishSession" {{ $isSessionFinished ? 'disabled' : '' }}
                        class="nb-btn py-3 px-4 {{ $isSessionFinished ? 'bg-gray-400' : 'bg-black text-white' }} text-xs shadow-none border-2">
                        {{ $isSessionFinished ? 'OFF' : 'SELESAI' }}
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nb-btn p-3 bg-primary-red text-white shadow-none border-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category Navigation -->
        <div
            class="theme-no-card px-6 lg:px-10 py-4 bg-white dark:bg-slate-950 border-b-[var(--nb-border)] border-black dark:border-slate-800 flex items-center gap-3 overflow-x-auto no-scrollbar">
            <button @click="selectedCategory = null"
                :class="selectedCategory === null ? 'bg-primary-blue text-white' :
                    'bg-gray-100 text-black dark:bg-dark-soft dark:text-white'"
                class="nb-btn text-[10px] py-1.5 px-4 shadow-none border-2">SEMUA</button>
            @foreach ($this->categories as $cat)
                <button type="button"
                    @click="selectedCategory = (selectedCategory == '{{ $cat->id }}' ? null : '{{ $cat->id }}')"
                    :class="selectedCategory == '{{ $cat->id }}' ? 'bg-primary-blue text-white' :
                        'bg-gray-100 text-black dark:bg-dark-soft dark:text-white'"
                    class="nb-btn text-[10px] py-1.5 px-4 whitespace-nowrap shadow-none border-2">{{ $cat->name }}</button>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto px-6 lg:px-10 py-8 no-scrollbar bg-slate-100 dark:bg-dark-bg"
            data-product-grid>
            <div x-show="filteredProducts.length === 0" x-cloak
                class="h-full flex flex-col items-center justify-center p-6">
                <div
                    class="nb-card p-12 bg-transparent dark:bg-transparent text-center border-dashed border-4 border-gray-300 dark:border-slate-700 shadow-none max-w-lg w-full flex flex-col items-center">
                    <svg class="w-20 h-20 mb-6 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-3xl font-black uppercase italic text-gray-400 dark:text-slate-500 mb-3">PRODUK
                        KOSONG</h3>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.2em] text-gray-400 dark:text-slate-500 leading-relaxed">
                        Produk tidak ditemukan pada pencarian atau kategori ini.</p>
                </div>
            </div>

            <div x-show="filteredProducts.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                <template x-for="product in filteredProducts" :key="product.id">
                    <button type="button" data-add-to-cart :data-product-id="product.id"
                        :disabled="{{ $isSessionFinished ? 'true' : 'false' }}"
                        :class="getCategoryBorderColor(product.category_name)"
                        class="nb-card nb-card-hover group p-0 text-left overflow-hidden flex flex-col h-full bg-white dark:bg-slate-900">
                        <div
                            class="p-3 bg-gray-50 dark:bg-slate-800 border-b-[var(--nb-border)] border-black dark:border-slate-800 flex items-center justify-between">
                            <span :class="getCategoryColor(product.category_name)"
                                class="text-[9px] font-black px-2 py-0.5 uppercase tracking-widest border-2"
                                x-text="product.category_name"></span>
                            <span x-show="product.available_stock > 0"
                                class="text-[8px] font-black border-2 border-black dark:border-slate-700 px-2 py-0.5 uppercase tracking-widest text-emerald-600 dark:text-emerald-400"
                                x-text="'STOK: ' + product.available_stock"></span>
                            <span x-show="product.available_stock <= 0"
                                class="text-[8px] font-black border-2 border-black dark:border-slate-700 px-2 py-0.5 uppercase tracking-widest text-primary-red dark:text-rose-400">HABIS</span>
                        </div>
                        <div class="p-5 flex-1">
                            <h3 x-text="product.name"
                                class="text-lg font-black uppercase leading-tight mb-3 dark:text-white line-clamp-2">
                            </h3>
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-[9px] font-black italic text-primary-red dark:text-rose-400 uppercase">IDR</span>
                                <span x-text="formatRupiah(product.price).replace('Rp', '').trim()"
                                    class="text-xl font-black italic text-primary-red dark:text-rose-400 leading-none"></span>
                            </div>
                        </div>
                        <div
                            class="p-3 bg-black dark:bg-slate-800 text-white group-hover:bg-primary-blue transition-colors flex items-center justify-center gap-3 border-t-[var(--nb-border)] border-black dark:border-slate-800">
                            <span class="font-black text-[10px] uppercase tracking-[0.2em]">TAMBAH KE CART</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- 2. Right Sidebar (Cart) -->
    <div x-show="window.innerWidth >= 1024 || showCart"
        class="fixed inset-y-0 right-0 w-full md:w-[420px] bg-white dark:bg-slate-900 lg:static lg:flex lg:w-[420px] flex flex-col z-[100] border-l-[var(--nb-border)] border-black dark:border-slate-800">

        <div
            class="p-5 bg-primary-red text-white border-b-[var(--nb-border)] border-black flex justify-between items-center shadow-[inset_0_-4px_0_0_rgba(0,0,0,0.2)]">
            <h2 class="text-xl font-black uppercase italic tracking-tighter">ORDER CART</h2>
            <button @click="showCart = false" class="lg:hidden nb-btn bg-white text-black p-2 shadow-none border-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div x-data="{ tab: 'cart' }" class="flex flex-col h-full overflow-hidden bg-white dark:bg-slate-950">
            <!-- Sidebar Tabs -->
            <div
                class="p-3 flex gap-3 bg-gray-50 dark:bg-dark-neutral border-b-[var(--nb-border)] border-black dark:border-slate-800">
                <button @click="tab = 'cart'"
                    :class="tab === 'cart' ? 'bg-primary-blue text-white' :
                        'bg-white text-black dark:bg-dark-soft dark:text-white'"
                    class="nb-btn flex-1 py-1.5 text-xs shadow-none border-2">CART</button>
                <button @click="tab = 'tasks'"
                    :class="tab === 'tasks' ? 'bg-amber-500 text-white' :
                        'bg-white text-black dark:bg-dark-soft dark:text-white'"
                    class="nb-btn flex-1 py-1.5 text-xs shadow-none border-2 flex items-center justify-center gap-1.5">
                    <span>TASKS</span>
                    @php
                        $pendingTasksCount = collect($dailyTasks)->where('approval_status', '!=', 'approved')->count();
                    @endphp
                    @if ($pendingTasksCount > 0)
                        <span
                            class="px-1.5 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full border border-white dark:border-gray-800 leading-none min-w-[16px] text-center">
                            {{ $pendingTasksCount }}
                        </span>
                    @endif
                </button>
                <button @click="tab = 'history'"
                    :class="tab === 'history' ? 'bg-primary-red text-white' :
                        'bg-white text-black dark:bg-dark-soft dark:text-white'"
                    class="nb-btn flex-1 py-1.5 text-xs shadow-none border-2">HISTORY</button>
                <button x-show="tab === 'cart' && cart.length > 0" @click="clearCart()"
                    class="nb-btn py-1.5 px-3 text-xs bg-white text-primary-red dark:bg-dark-soft shadow-none border-2 hover:bg-red-50"
                    title="Clear Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>

            <!-- Cart Content -->
            <div x-show="tab === 'cart'" class="flex-1 overflow-y-auto p-5 space-y-3 no-scrollbar">
                <template x-for="item in cart" :key="item.id">
                    <div
                        class="nb-card p-3 flex items-center gap-3 bg-white dark:bg-dark-soft hover:shadow-none transition-shadow border-2">
                        <div class="w-9 h-9 bg-black text-white flex items-center justify-center font-black text-[10px] italic border-2 border-white"
                            x-text="item.name.substring(0, 2).toUpperCase()"></div>
                        <div class="flex-1">
                            <h4 x-text="item.name"
                                class="text-[10px] font-black uppercase dark:text-white line-clamp-1"></h4>
                            <p class="text-[10px] font-black text-primary-red" x-text="formatRupiah(item.price)"></p>
                        </div>
                        <div class="flex items-center border-2 border-black dark:border-white bg-white dark:bg-black">
                            <button @click="removeFromCart(item.id)"
                                class="px-2 py-0.5 font-black hover:bg-gray-100 dark:hover:bg-gray-800 border-r-2 border-black dark:border-white text-black dark:text-white">-</button>
                            <span x-text="item.quantity"
                                class="px-2 text-[10px] font-black text-black dark:text-white"></span>
                            <button @click="addToCart(item)"
                                class="px-2 py-0.5 font-black hover:bg-gray-100 dark:hover:bg-gray-800 border-l-2 border-black dark:border-white text-black dark:text-white">+</button>
                        </div>
                    </div>
                </template>
                <div x-show="cart.length === 0"
                    class="h-full flex flex-col items-center justify-center opacity-20 italic font-black uppercase tracking-widest text-center text-[10px] py-20">
                    <p>BELUM ADA PESANAN</p>
                </div>
            </div>

            <!-- Tasks Content -->
            <div x-show="tab === 'tasks'" class="flex-1 overflow-y-auto p-5 space-y-3 no-scrollbar">
                <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tugas Harian Anda Hari Ini
                </div>
                @forelse($dailyTasks as $task)
                    <a href="{{ route('my-tasks') }}" target="_blank"
                        class="nb-card p-4 flex flex-col gap-3 bg-white dark:bg-dark-soft border-2 shadow-sm hover:shadow-none transition-shadow cursor-pointer">
                        <div class="flex justify-between items-start">
                            <h4
                                class="text-xs font-bold uppercase tracking-tight {{ $task->latestSubmission?->approval_status === 'approved' ? 'line-through text-gray-400' : 'text-slate-800 dark:text-white' }}">
                                {{ $task->taskDefinition->task_name }}</h4>
                            @if ($task->latestSubmission?->approval_status === 'approved')
                                <span
                                    class="text-[8px] font-black uppercase bg-green-500 text-white px-2 py-0.5 rounded border border-white">DISETUJUI</span>
                            @elseif($task->latestSubmission?->approval_status === 'rejected')
                                <span
                                    class="text-[8px] font-black uppercase bg-red-500 text-white px-2 py-0.5 rounded border border-white">DITOLAK
                                    - REVISI</span>
                            @elseif($task->latestSubmission?->approval_status === 'pending')
                                <span
                                    class="text-[8px] font-black uppercase bg-primary-blue text-white px-2 py-0.5 rounded border border-white">MENUNGGU
                                    ACC</span>
                            @else
                                <span
                                    class="text-[8px] font-black uppercase bg-amber-500 text-white px-2 py-0.5 rounded border border-white">BELUM
                                    SELESAI</span>
                            @endif
                        </div>
                        @if ($task->taskDefinition->description)
                            <p class="text-[10px] text-gray-400">{{ $task->taskDefinition->description }}</p>
                        @endif
                        <div class="flex flex-wrap gap-1.5 items-center mt-1">
                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ $task->taskDefinition->date->translatedFormat('d M Y') }}</span>
                            @if ($task->taskDefinition->deadline_at)
                                <span class="text-[8px] font-black uppercase tracking-widest bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">
                                    Deadline: {{ $task->taskDefinition->deadline_at->translatedFormat('d M H:i') }}
                                </span>
                            @elseif($task->taskDefinition->is_routine && isset($task->taskDefinition->computed_deadline) && $task->taskDefinition->computed_deadline)
                                <span class="text-[8px] font-black uppercase tracking-widest bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">
                                    Deadline: {{ \Carbon\Carbon::parse($task->taskDefinition->computed_deadline)->translatedFormat('d M H:i') }}
                                </span>
                            @endif
                        </div>
                        <span
                            class="text-[9px] font-black uppercase tracking-widest text-primary-blue flex items-center gap-1.5 mt-1">
                            Kerjakan di Halaman Tugas Saya
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </span>
                    </a>
                @empty
                    <div class="text-center py-10 text-xs text-gray-400 italic font-semibold">Tidak ada tugas khusus
                        hari ini</div>
                @endforelse
            </div>

            <div x-show="tab === 'history'" class="flex-1 overflow-y-auto p-5 space-y-3 no-scrollbar">
                @foreach ($this->recentTransactions as $history)
                    <div class="nb-card p-3 bg-white dark:bg-dark-soft border-2 hover:shadow-none transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <span
                                class="text-[8px] font-black bg-black text-white px-2 py-0.5 uppercase tracking-widest border border-white">{{ \Carbon\Carbon::parse($history->transacted_at)->format('H:i') }}</span>
                            <span
                                class="text-[8px] font-black border-2 border-black dark:border-white px-2 py-0.5 uppercase tracking-widest dark:text-white">{{ str_replace('_', ' ', $history->status) }}</span>
                        </div>
                        <h4 class="text-[10px] font-black uppercase dark:text-white tracking-tight line-clamp-1">
                            {{ $history->reference }}</h4>
                        <div class="flex justify-between items-end mt-3">
                            <span
                                class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ $history->total_qty }}
                                ITEMS</span>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xs font-black italic text-primary-red">Rp{{ number_format($history->total_amount, 0, ',', '.') }}</span>
                                <button wire:click="viewDetails('{{ $history->reference }}')"
                                    class="nb-btn text-[8px] py-1 px-2 bg-primary-blue text-white shadow-none border-2">DETAIL</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- Checkout Section -->
            <div
                class="p-6 bg-white dark:bg-dark-neutral border-t-[var(--nb-border)] border-black dark:border-slate-800 space-y-4">
                <div x-show="$wire.transactionDate < '{{ now()->toDateString() }}'" x-cloak
                    class="nb-card p-3 bg-primary-yellow border-2 shadow-none flex items-center justify-center gap-2 animate-pulse">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-widest text-black">Mode Susulan Aktif</span>
                </div>

                <div
                    class="nb-card-flat bg-gray-50 dark:bg-dark-soft p-4 relative overflow-hidden border-2 shadow-[4px_4px_0_0_rgba(0,0,0,1)] dark:shadow-[4px_4px_0_0_rgba(255,255,255,1)]">
                    <span class="text-[9px] font-black uppercase tracking-[0.3em] mb-1 block dark:text-gray-400">TOTAL
                        BILL</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-lg font-black italic dark:text-white">RP</span>
                        <span x-text="formatRupiah(total).replace('Rp', '').trim()"
                            class="text-4xl font-black italic tracking-tighter leading-none dark:text-white"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[8px] font-black uppercase tracking-widest ml-1 dark:text-gray-400">TANGGAL
                            TRANSAKSI</label>
                        <input type="date" wire:model.live="transactionDate" max="{{ now()->toDateString() }}"
                            class="nb-input w-full p-2.5 text-[10px] uppercase shadow-none border-2 bg-white dark:bg-black font-bold">
                    </div>
                    <div class="space-y-1">
                        <label
                            class="text-[8px] font-black uppercase tracking-widest ml-1 dark:text-gray-400">BUYER</label>
                        <input type="text" x-model="buyer_name" placeholder="GUEST"
                            class="nb-input w-full p-2.5 text-[10px] uppercase shadow-none border-2 bg-white dark:bg-black">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[8px] font-black uppercase tracking-widest ml-1 dark:text-gray-400">CASH
                        (RP)</label>
                    <input type="number" x-model.number="payment_amount"
                        @keydown.enter="handleCheckoutKeydown($event)"
                        class="nb-input w-full p-2.5 text-xs text-primary-blue italic shadow-none border-2 font-black bg-white dark:bg-black">
                </div>

                <div class="flex gap-2">
                    <button @click="status = 'uang_diterima'"
                        :class="status === 'uang_diterima' ? 'bg-green-500 text-white' :
                            'bg-white dark:bg-dark-soft dark:text-white'"
                        class="nb-btn flex-1 py-2 text-[9px] shadow-none border-2 font-black">LUNAS</button>
                    <button @click="status = 'belum_kembalian'"
                        :class="status === 'belum_kembalian' ? 'bg-primary-blue text-white' :
                            'bg-white dark:bg-dark-soft dark:text-white'"
                        class="nb-btn flex-1 py-2 text-[9px] shadow-none border-2 font-black">PENDING</button>
                    <button @click="status = 'belum_menerima_uang'"
                        :class="status === 'belum_menerima_uang' ? 'bg-primary-red text-white' :
                            'bg-white dark:bg-dark-soft dark:text-white'"
                        class="nb-btn flex-1 py-2 text-[9px] shadow-none border-2 font-black">HUTANG</button>
                </div>

                <template x-if="payment_amount > 0">
                    <div class="nb-card p-3 flex justify-between items-center shadow-none border-2"
                        :class="change < 0 ? 'bg-primary-red text-white' : 'bg-green-500 text-white'">
                        <span x-text="change < 0 ? 'CURANG' : 'CHANGE'"
                            class="text-[9px] font-black uppercase tracking-widest"></span>
                        <span x-text="formatRupiah(Math.abs(change))" class="text-lg font-black italic"></span>
                    </div>
                </template>

                <button @click="checkout()"
                    :disabled="{{ $isSessionFinished ? 'true' : 'false' }} || cart.length === 0 || (payment_amount < total &&
                        status === 'uang_diterima') || loading"
                    @keydown.enter="handleCheckoutKeydown($event)"
                    class="nb-btn checkout-trigger w-full py-5 text-lg bg-black text-white hover:bg-primary-blue disabled:bg-gray-400 disabled:opacity-50 group shadow-[4px_4px_0_0_rgba(37,99,235,0.4)] dark:shadow-[4px_4px_0_0_rgba(255,255,255,1)]">
                    <span x-show="!loading" class="flex items-center justify-center gap-4">
                        PROCESS NOW
                        <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span x-show="loading" x-cloak class="flex items-center justify-center gap-3">
                        <div class="w-5 h-5 border-2 border-white border-t-transparent animate-brutal-spin"></div>
                        PROCESSING...
                    </span>
                </button>
            </div>
        </div>
    </div>
    <!-- Recovery Modal -->
    <div x-data="{ show: @entangle('showRecoveryModal') }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[500] flex items-center justify-center p-6 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        <div
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-xl p-10 text-center animate-in zoom-in-95 duration-300">
            <div
                class="w-20 h-20 bg-primary-yellow border-2 border-black flex items-center justify-center mx-auto mb-6 shadow-[var(--nb-shadow-sm)]">
                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-2xl font-black uppercase italic mb-3 dark:text-white">UNFINISHED SESSION</h2>
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500 mb-6 leading-relaxed">Session on
                <span
                    class="text-primary-blue font-black">{{ $unfinishedSessionDate ? \Carbon\Carbon::parse($unfinishedSessionDate)->translatedFormat('d F Y') : '-' }}</span>
                was not closed.
            </p>

            <div class="nb-card-flat bg-gray-50 dark:bg-black p-5 mb-8 text-left border-2 shadow-none">
                <p class="text-[9px] font-black uppercase mb-1 dark:text-gray-400">AUTO-FIX:</p>
                <p class="text-[10px] font-bold italic leading-tight text-gray-600 dark:text-gray-400">System will
                    calculate remaining stock and use it for today's opening.</p>
            </div>

            <div class="space-y-3">
                <button wire:click="fixUnfinishedSession"
                    class="nb-btn w-full bg-primary-blue text-white text-base py-4">RECOVER & CONTINUE</button>
                <a href="{{ route('dashboard') }}" class="nb-btn w-full bg-white text-black py-3 block text-xs">BACK
                    TO DASHBOARD</a>
            </div>
        </div>
    </div>

    <!-- Opening Stock Modal -->
    <div x-data="{ show: @entangle('showOpeningStockModal'), modalSearch: '' }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[400] flex items-center justify-center p-6 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        <div
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden animate-in slide-in-from-bottom-10 duration-500 border-4">
            <div
                class="p-6 bg-primary-blue text-white border-b-4 border-black flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black uppercase italic leading-none flex items-center gap-3">
                        STOK AWAL
                        <span
                            class="text-[10px] bg-white text-primary-blue px-3 py-1 rounded-full not-italic tracking-widest">{{ \Carbon\Carbon::parse($transactionDate)->translatedFormat('d M Y') }}</span>
                    </h2>
                    <p class="text-[10px] font-black uppercase tracking-widest mt-1.5 opacity-80 italic">Verifikasi
                        barang fisik di toko</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="text" x-model="modalSearch" placeholder="CARI BARANG..."
                        class="nb-input bg-white text-black p-2 text-[10px] flex-1 md:w-48 shadow-none border-2">
                    <button @click="show = false" class="nb-btn bg-white text-black p-2 shadow-none border-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6 no-scrollbar bg-gray-50 dark:bg-black">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $activeJurusanId = session('active_jurusan_id');
                        $openingProducts = \App\Models\Product::where('is_active', true)
                            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                                return $q->where('jurusan_id', $activeJurusanId);
                            })
                            ->orderBy('name')
                            ->get();
                        $awalNames = $openingProducts->pluck('name')->toArray();
                    @endphp
                    @foreach ($openingProducts as $p)
                        <div x-show="'{{ strtolower($p->name) }}'.includes(modalSearch.toLowerCase())"
                            wire:key="opening-stock-{{ $p->id }}"
                            class="nb-card p-5 flex items-center justify-between bg-white dark:bg-dark-soft shadow-none border-2">
                            <div class="flex-1">
                                <h4 class="font-black uppercase text-sm dark:text-white">{{ $p->name }}</h4>
                                <div class="flex items-center gap-2 mt-2">
                                    <span
                                        class="text-[9px] font-black bg-black text-white px-2 py-0.5 uppercase tracking-widest border border-white">{{ $p->category->name ?? '' }}</span>
                                    @if (isset($lastClosingStocks[$p->id]))
                                        <span
                                            class="text-[9px] font-black border-2 border-primary-blue dark:border-primary-blue-light px-2 py-0.5 uppercase tracking-widest text-primary-blue dark:text-primary-blue-light">KEMARIN:
                                            {{ $lastClosingStocks[$p->id] }}</span>
                                    @endif
                                </div>
                            </div>
                            <input type="number" wire:model="stockItems.{{ $p->id }}"
                                class="nb-input w-24 text-center text-lg p-2 shadow-none border-2 bg-white dark:bg-black">
                        </div>
                    @endforeach

                    <div x-show="{{ json_encode($awalNames) }}.filter(name => name.toLowerCase().includes(modalSearch.toLowerCase())).length === 0"
                        x-cloak
                        class="col-span-full py-16 flex flex-col items-center justify-center bg-gray-50 dark:bg-black/50 border-2 border-dashed border-gray-200 dark:border-gray-800">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-gray-400 font-bold text-xs uppercase tracking-widest italic mb-1">PRODUK TIDAK
                            DITEMUKAN</div>
                        <div class="text-gray-300 dark:text-gray-600 font-black text-2xl uppercase tracking-tighter">
                            "<span x-text="modalSearch"></span>"</div>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-white dark:bg-dark-soft border-t-4 border-black">
                <button wire:click="saveOpeningStock"
                    class="nb-btn w-full bg-primary-blue text-white text-lg py-5">SIMPAN & MULAI JUALAN</button>
            </div>
        </div>
    </div>

    <!-- Closing Stock Modal -->
    <div x-data="{ show: @entangle('showClosingStockModal'), modalSearch: '' }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[400] flex items-center justify-center p-6 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        <div
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden border-4">
            <div
                class="p-6 bg-primary-red text-white border-b-4 border-black flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black uppercase italic leading-none text-white flex items-center gap-3">
                        REKAP HARIAN
                        <span
                            class="text-[10px] bg-white text-primary-red px-3 py-1 rounded-full not-italic tracking-widest">{{ \Carbon\Carbon::parse($transactionDate)->translatedFormat('d M Y') }}</span>
                    </h2>
                    <p class="text-[10px] font-black uppercase tracking-widest mt-1.5 opacity-80 italic">Input sisa
                        barang di toko hari ini</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="text" x-model="modalSearch" placeholder="CARI BARANG..."
                        class="nb-input bg-white text-black p-2 text-[10px] flex-1 md:w-48 shadow-none border-2">
                    <button @click="show = false" class="nb-btn bg-white text-black p-2 shadow-none border-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6 no-scrollbar bg-gray-50 dark:bg-black">
                @php
                    $hasHigherRole = auth()
                        ->user()
                        ->roles()
                        ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
                        ->exists();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $rekapNames = collect($this->stockComparison)->pluck('name')->toArray();
                    @endphp
                    @foreach ($this->stockComparison as $item)
                        <div x-show="'{{ strtolower($item['name']) }}'.includes(modalSearch.toLowerCase())"
                            wire:key="closing-stock-{{ $item['id'] }}"
                            class="nb-card p-5 flex flex-col bg-white dark:bg-dark-soft shadow-none border-2">
                            <h4 class="font-black uppercase text-sm dark:text-white mb-4">{{ $item['name'] }}</h4>
                            <div class="flex flex-wrap items-center gap-3 mb-5">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black uppercase text-gray-400 mb-1">AWAL</span>
                                    <span
                                        class="text-xs font-black border-2 border-black dark:border-white px-3 py-1 uppercase tracking-widest dark:text-white bg-gray-100 dark:bg-slate-800">{{ $item['opening'] }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[9px] font-black uppercase text-primary-blue mb-1">LAKU</span>
                                    <span
                                        class="text-xs font-black bg-primary-blue text-white px-3 py-1 uppercase tracking-widest">{{ $item['sold'] }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black uppercase text-green-600 mb-1">SISA</span>
                                    <span
                                        class="text-xs font-black bg-green-500 text-white px-3 py-1 uppercase tracking-widest">{{ $item['expected'] }}</span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest dark:text-gray-400">STOK
                                    FISIK SEKARANG</label>
                                <input type="number" wire:model="stockItems.{{ $item['id'] }}"
                                    class="nb-input w-full text-center text-2xl p-3 shadow-none border-2 bg-white dark:bg-black">
                            </div>
                        </div>
                    @endforeach

                    <div x-show="{{ json_encode($rekapNames) }}.filter(name => name.toLowerCase().includes(modalSearch.toLowerCase())).length === 0"
                        x-cloak
                        class="col-span-full py-16 flex flex-col items-center justify-center bg-gray-50 dark:bg-black/50 border-2 border-dashed border-gray-200 dark:border-gray-800">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-gray-400 font-bold text-xs uppercase tracking-widest italic mb-1">PRODUK
                            TIDAK
                            DITEMUKAN</div>
                        <div
                            class="text-gray-300 dark:text-gray-600 font-black text-2xl uppercase tracking-tighter">
                            "<span x-text="modalSearch"></span>"</div>
                    </div>
                </div>

            </div>
            <div class="p-6 bg-white dark:bg-dark-soft border-t-4 border-black">
                <button wire:click="saveClosingStockAndNext"
                    class="nb-btn w-full bg-primary-blue text-white text-lg py-5">SIMPAN SISA BARANG & LANJUT</button>
            </div>
        </div>
    </div>

    <!-- Closing Report Modal -->
    <div x-data="{ show: @entangle('showClosingReportModal') }" x-show="show" x-cloak
        class="fixed inset-0 z-[500] flex items-center justify-center p-6 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        <div class="nb-card bg-white dark:bg-dark-soft w-full max-w-md p-10 border-4 border-black text-center">
            <div class="mb-6">
                <span
                    class="text-[9px] font-black bg-primary-red text-white px-3 py-1 uppercase tracking-widest border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">LAPORAN
                    AKHIR SHIFT</span>
                <h2 class="text-2xl font-black uppercase italic mt-4 dark:text-white">CLOSING REPORT</h2>
                <p class="text-xs text-gray-500 font-semibold mt-1">Tulis laporan aktivitas Anda sebelum menyelesaikan
                    sesi</p>
            </div>

            <div class="space-y-4 mb-6 text-left">
                @php
                    $hasHigherRole = auth()
                        ->user()
                        ->roles()
                        ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
                        ->exists();
                @endphp
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">
                        Laporan Aktivitas Selama Shift {!! $hasHigherRole ? '<span class="text-amber-500 font-black">(OPSIONAL)</span>' : '' !!}
                    </label>
                    <textarea wire:model="closingReportText" placeholder="Jelaskan apa saja yang Anda lakukan selama shift ini..."
                        rows="4" class="nb-input w-full p-4 text-sm font-bold bg-white dark:bg-slate-800 border-2 border-black"></textarea>
                    @error('closingReportText')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button wire:click="submitClosingReport"
                class="nb-btn w-full bg-primary-red text-white text-base py-4 font-black uppercase tracking-widest">KIRIM
                LAPORAN & CLOCK OUT</button>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div x-data="{ show: @entangle('showDetailsModal') }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[600] flex items-center justify-center p-6 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        <div @click.away="show = false"
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-lg flex flex-col overflow-hidden animate-in zoom-in-95 duration-300 border-4">
            <div class="p-6 bg-primary-blue text-white border-b-4 border-black relative">
                <button @click="show = false"
                    class="absolute right-6 top-6 nb-btn bg-white text-black p-2 shadow-none border-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-xl font-black uppercase italic tracking-tighter">ORDER DETAILS</h3>
                <p class="text-[8px] font-black uppercase tracking-[0.3em] mt-1.5 opacity-60">REF:
                    {{ $detailReference }}</p>
            </div>

            <div class="p-6 max-h-[50vh] overflow-y-auto no-scrollbar bg-white dark:bg-black">
                <div class="space-y-4">
                    @if ($this->detailItems)
                        @foreach ($this->detailItems as $item)
                            <div
                                class="flex justify-between items-center border-b-2 border-black/5 dark:border-white/5 pb-3 last:border-0">
                                <div>
                                    <p class="text-[11px] font-black uppercase dark:text-white">
                                        {{ $item->product->name }}</p>
                                    <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5 text-gray-400">
                                        {{ $item->quantity }} X Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="text-xs font-black italic dark:text-white">
                                    Rp{{ number_format($item->total_price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div
                class="p-6 bg-gray-50 dark:bg-dark-soft text-black border-t-4 border-black flex justify-between items-center">
                <div>
                    <span
                        class="text-[9px] font-black bg-black text-white px-2 py-1 uppercase tracking-widest border border-white">{{ $this->detailItems->first()->status ?? '' }}</span>
                </div>
                <div class="text-right">
                    <p class="text-[8px] font-black uppercase tracking-widest mb-1 opacity-60 dark:text-gray-400">GRAND
                        TOTAL</p>
                    <p
                        class="text-2xl font-black italic tracking-tighter text-primary-blue leading-none dark:text-primary-blue-light">
                        Rp{{ number_format($this->detailItems->sum('total_price'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Stock Alert Modal -->
    <div x-show="stockAlert !== null" x-cloak @keydown.window.escape="stockAlert = null"
        class="fixed inset-0 z-[600] flex items-center justify-center p-6">

        <!-- Backdrop -->
        <div x-show="stockAlert !== null" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="absolute inset-0 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        </div>

        <!-- Modal Box -->
        <div x-show="stockAlert !== null" @click.away="stockAlert = null"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-sm p-10 text-center relative z-10">
            <div
                class="w-20 h-20 bg-primary-yellow border-2 border-black flex items-center justify-center mx-auto mb-6 shadow-[var(--nb-shadow-sm)]">
                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-2xl font-black uppercase italic mb-3 dark:text-white" x-text="stockAlert?.title"></h2>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-8 leading-relaxed"
                x-text="stockAlert?.message"></p>

            <button @click="stockAlert = null"
                class="nb-btn w-full bg-black text-white text-base py-4">MENGERTI</button>
        </div>
    </div>


    <!-- Change Due Modal -->
    <div x-show="showChangeModal" x-cloak
        x-on:keydown.window.escape="showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })"
        x-on:keydown.window.enter="if (showChangeModal) { showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); }); }"
        class="fixed inset-0 z-[600] flex items-center justify-center p-6">

        <!-- Backdrop -->
        <div x-show="showChangeModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="absolute inset-0 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        </div>

        <!-- Modal Box -->
        <div x-show="showChangeModal"
            @click.away="showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-md p-10 relative z-10 border-t-8 border-t-primary-blue">

            <div class="text-center mb-8">
                <span
                    class="text-[9px] font-black bg-green-500 text-white px-3 py-1 uppercase tracking-widest border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">TRANSAKSI
                    SUKSES</span>
                <h2 class="text-2xl font-black uppercase italic mt-4 dark:text-white">UANG KEMBALIAN</h2>
            </div>

            <div class="space-y-4 mb-10">
                <div
                    class="flex justify-between items-center py-3 border-b-2 border-dashed border-black dark:border-slate-800">
                    <span class="text-xs font-black uppercase tracking-widest text-slate-400">Total Belanja</span>
                    <span class="text-lg font-black dark:text-white"
                        x-text="'Rp' + formatRupiah(lastChangeData.total).replace('Rp', '').trim()"></span>
                </div>
                <div
                    class="flex justify-between items-center py-3 border-b-2 border-dashed border-black dark:border-slate-800">
                    <span class="text-xs font-black uppercase tracking-widest text-slate-400">Uang Diterima</span>
                    <span class="text-lg font-black dark:text-white"
                        x-text="'Rp' + formatRupiah(lastChangeData.payment).replace('Rp', '').trim()"></span>
                </div>
                <div class="flex justify-between items-center py-5">
                    <span class="text-sm font-black uppercase tracking-widest text-slate-500">Kembalian</span>
                    <span class="text-3xl font-black italic text-green-600 dark:text-emerald-400"
                        x-text="'Rp' + formatRupiah(lastChangeData.change).replace('Rp', '').trim()"></span>
                </div>
            </div>

            <button
                @click="showChangeModal = false; $nextTick(() => { const el = document.getElementById('pos-search-input'); if (el) el.focus(); })"
                class="nb-btn w-full bg-black text-white text-base py-4 font-black uppercase tracking-widest">TUTUP
                (ENTER)</button>
        </div>
    </div>


    <!-- Quick Expense Modal -->
    <div x-show="showQuickExpenseModal" x-cloak @keydown.window.escape="showQuickExpenseModal = false"
        class="fixed inset-0 z-[600] flex items-center justify-center p-6">

        <!-- Backdrop -->
        <div x-show="showQuickExpenseModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="absolute inset-0 bg-white/20 dark:bg-black/40 backdrop-blur-md">
        </div>

        <!-- Modal Box -->
        <div x-show="showQuickExpenseModal" @click.away="showQuickExpenseModal = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="nb-card bg-white dark:bg-dark-soft w-full max-w-md p-10 relative z-10 border-t-8 border-t-primary-red">

            <div class="text-center mb-8">
                <span
                    class="text-[9px] font-black bg-primary-red text-white px-3 py-1 uppercase tracking-widest border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">OPERASIONAL</span>
                <h2 class="text-2xl font-black uppercase italic mt-4 dark:text-white">CATAT PENGELUARAN CEPAT</h2>
            </div>

            <div class="space-y-5 mb-8">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Nominal
                        Pengeluaran (Rp)</label>
                    <input type="number" x-model="quickExpenseAmount" placeholder="Contoh: 10000"
                        class="nb-input w-full p-4 text-base font-bold bg-white dark:bg-slate-800 border-2 border-black">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Kategori
                        Kas</label>
                    <select x-model="quickExpenseCategoryId"
                        class="nb-input w-full p-4 text-sm font-bold bg-white dark:bg-slate-800 border-2 border-black uppercase">
                        <option value="">-- Pilihlah Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Keterangan /
                        Keperluan</label>
                    <textarea x-model="quickExpenseDescription" placeholder="Contoh: Beli Kantong Plastik Besar" rows="3"
                        class="nb-input w-full p-4 text-sm font-bold bg-white dark:bg-slate-800 border-2 border-black uppercase"></textarea>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="button" @click="showQuickExpenseModal = false"
                    class="nb-btn w-1/3 bg-white text-black border-2 border-black hover:bg-slate-100 py-4 font-black uppercase tracking-widest text-xs">BATAL</button>
                <button type="button"
                    @click="
                    if (!quickExpenseAmount || !quickExpenseCategoryId || !quickExpenseDescription) {
                        alert('Harap isi semua kolom!');
                        return;
                    }
                    quickExpenseLoading = true;
                    $wire.saveQuickExpense(quickExpenseAmount, quickExpenseCategoryId, quickExpenseDescription).then(() => {
                        showQuickExpenseModal = false;
                        quickExpenseAmount = '';
                        quickExpenseCategoryId = '';
                        quickExpenseDescription = '';
                        quickExpenseLoading = false;
                    }).catch(() => {
                        quickExpenseLoading = false;
                    });
                "
                    :disabled="quickExpenseLoading"
                    class="nb-btn w-2/3 bg-primary-red text-white py-4 font-black uppercase tracking-widest text-xs flex items-center justify-center">
                    <span x-show="!quickExpenseLoading">SIMPAN</span>
                    <span x-show="quickExpenseLoading">MENYIMPAN...</span>
                </button>
            </div>
        </div>
    </div>


    <!-- Mobile Cart FAB Trigger -->
    <button @click="showCart = true" x-show="!showCart"
        class="lg:hidden fixed bottom-6 right-6 z-40 px-6 py-4 bg-primary-blue text-white rounded-2xl shadow-2xl font-black uppercase tracking-widest flex items-center gap-3 border-2 border-black active:scale-95 transition-transform">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
        <span>Lihat Keranjang</span>
        <span class="bg-primary-red text-white text-[10px] px-2.5 py-1 rounded-full border border-white"
            x-text="cart.length"></span>
    </button>

    <script>
        window.addEventListener('transaction-complete', () => {
            setTimeout(() => {
                const searchInput = document.querySelector('input[placeholder*="CARI"]');
                if (searchInput) searchInput.focus();
            }, 100);
        });

        // Re-setup event listeners when Livewire updates
        document.addEventListener('livewire:updated', () => {
            const gridContainer = document.querySelector('[data-product-grid]');
            if (gridContainer) {
                gridContainer.addEventListener('click', (e) => {
                    const alpine = document.querySelector('[x-data]').__alpine_$1;
                    if (alpine) {
                        alpine.handleProductClick(e);
                    }
                });
            }
        });
    </script>
</div>
