<div class="p-6" x-data="{ showProductDetail: false, detailProduct: {} }">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Katalog Produk</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Manajemen Inventaris Digital</p>
        </div>
        
        @if(session('active_role_name') !== 'superadmin')
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-2 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                <button wire:click="$set('tab', 'products'); $set('activeTab', 'products');" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ ($tab === 'products' && $activeTab === 'products') ? 'bg-primary-blue text-white shadow-lg shadow-blue-900/20' : 'text-gray-400 hover:text-gray-600' }}">Daftar Produk</button>
                <button wire:click="$set('tab', 'products'); $set('activeTab', 'grouping');" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ ($tab === 'products' && $activeTab === 'grouping') ? 'bg-primary-blue text-white shadow-lg shadow-blue-900/20' : 'text-gray-400 hover:text-gray-600' }}">Pengelompokan Kas</button>
                <button wire:click="$set('tab', 'stock')" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'stock' ? 'bg-primary-blue text-white shadow-lg shadow-blue-900/20' : 'text-gray-400 hover:text-gray-600' }}">Input Stok Awal</button>
            </div>
        </div>
        @endif
    </div>

    @if($tab === 'products')
    <div class="w-full">
        @if($activeTab === 'products')
        <!-- List Section -->
        <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-6">
                <!-- Left Filters & Add Button -->
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4 w-full lg:w-auto">
                    <!-- Tambah Produk Button -->
                    <button wire:click="openCreateModal" class="px-6 py-3.5 bg-primary-blue text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-md flex items-center justify-center shrink-0 w-full lg:w-auto">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Produk
                    </button>
                    
                    <!-- Grid wrapper to align Search & Dropdowns side-by-side on mobile, flex on desktop -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-center gap-4 w-full lg:w-auto">
                        <!-- Search Input -->
                        <div class="relative group w-full lg:w-64">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                            <input type="text" wire:model.live="search" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300">
                        </div>
                        
                        <!-- Category Select -->
                        <div class="flex items-center bg-gray-50 dark:bg-gray-900 px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-800 w-full lg:w-auto">
                            <svg class="w-3.5 h-3.5 text-gray-400 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                            <select wire:model.live="filterCategory" class="bg-transparent border-none p-0 focus:ring-0 text-[10px] font-black uppercase tracking-widest text-gray-500 w-full">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Jurusan Select -->
                        @if(!session('active_jurusan_id'))
                        <div class="flex items-center bg-gray-50 dark:bg-gray-900 px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-800 w-full lg:w-auto">
                            <svg class="w-3.5 h-3.5 text-gray-400 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                            <select wire:model.live="filterJurusan" class="bg-transparent border-none p-0 focus:ring-0 text-[10px] font-black uppercase tracking-widest text-gray-500 w-full">
                                <option value="">Semua Jurusan</option>
                                @foreach($jurusans as $jur)
                                    <option value="{{ $jur->id }}">{{ $jur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Bulk Actions & Count -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full lg:w-auto shrink-0 justify-between">
                    @if(count($selectedProducts) > 0)
                        <div class="flex items-center justify-between sm:justify-start bg-primary-blue/5 px-6 py-2.5 rounded-2xl border border-primary-blue/10 animate-fade-in gap-4 w-full sm:w-auto">
                            <span class="text-[10px] font-black text-primary-blue uppercase tracking-widest shrink-0">{{ count($selectedProducts) }} Dipilih</span>
                            <div class="flex gap-2">
                                <button wire:click="bulkToggleStatus" class="px-3.5 py-2 bg-white dark:bg-gray-900 text-primary-blue rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm hover:bg-primary-blue hover:text-white transition-all border border-primary-blue/20">Ganti Status</button>
                                <button wire:click="confirmBulkDelete" class="px-3.5 py-2 bg-primary-red text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-red-500/20 hover:scale-105 transition-all">Hapus Semua</button>
                            </div>
                        </div>
                    @endif
                    <span class="text-[10px] font-black text-gray-450 uppercase tracking-widest text-center sm:text-right w-full sm:w-auto">
                        {{ $products->total() }} Produk Ditemukan
                    </span>
                </div>
            </div>
                <div class="px-8 py-4">
                    <!-- Header Row (Grid 12) -->
                    <div class="hidden lg:grid grid-cols-12 items-center px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-900/50 rounded-2xl mb-4">
                        <div class="col-span-1 flex justify-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-gray-300 text-primary-blue focus:ring-primary-blue dark:bg-gray-900 dark:border-gray-700">
                        </div>
                        <div class="col-span-3 px-4">Produk</div>
                        <div class="col-span-2 px-4 text-center">Kategori</div>
                        <div class="col-span-1 px-4 text-center">Supplier</div>
                        <div class="col-span-1 px-2 text-center">Harga</div>
                        <div class="col-span-1 px-2 text-center">Modal</div>
                        <div class="col-span-1 px-2 text-center">Profit</div>
                        <div class="col-span-1 px-2 text-center">Status</div>
                        <div class="col-span-1 px-4 text-right">Opsi</div>
                    </div>

                    <!-- Data Rows -->
                    <div class="space-y-4">
                        @forelse($products as $product)
                        <div class="group transition-all duration-300">
                            <!-- Desktop view -->
                            <div class="hidden lg:grid grid-cols-12 items-center p-4 rounded-[2.5rem] border-2 transition-all duration-500 {{ $highlight == $product->id ? 'bg-amber-400/10 border-amber-400 animate-highlight-breath z-10 relative' : 'bg-white dark:bg-gray-800/50 border-transparent group-hover:border-primary-blue/20' }} {{ in_array($product->id, $selectedProducts) ? 'border-primary-blue/30 bg-primary-blue/5' : '' }}">
                                <!-- Checkbox -->
                                <div class="col-span-1 flex justify-center">
                                    <input type="checkbox" wire:model.live="selectedProducts" value="{{ $product->id }}" class="w-5 h-5 rounded-lg border-gray-200 text-primary-blue focus:ring-primary-blue dark:bg-gray-900 dark:border-gray-700">
                                </div>
 
                                <!-- Product Info -->
                                <div class="col-span-3 px-4 min-w-0">
                                    <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic truncate">{{ $product->name }}</div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">ID: #{{ substr($product->id, 0, 8) }}</span>
                                        @if($product->jurusan)
                                            <span class="px-1.5 py-0.5 text-[8px] font-black rounded uppercase tracking-wider bg-primary-red/10 text-primary-red">
                                                TEFA {{ $product->jurusan->name }}
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 text-[8px] font-black rounded uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                                GLOBAL
                                            </span>
                                        @endif
                                    </div>
                                </div>
 
                                <!-- Category -->
                                <div class="col-span-2 px-4">
                                    @php
                                        $catName = strtolower($product->category->name);
                                        $catColor = match(true) {
                                            str_contains($catName, 'snack') => 'bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-500 border-amber-200/50 dark:border-amber-500/20',
                                            str_contains($catName, 'minuman') => 'bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-500 border-blue-200/50 dark:border-blue-500/20',
                                            str_contains($catName, 'makanan') => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-500 border-emerald-200/50 dark:border-emerald-500/20',
                                            str_contains($catName, 'atk') || str_contains($catName, 'tulis') => 'bg-purple-100 text-purple-600 dark:bg-purple-500/10 dark:text-purple-500 border-purple-200/50 dark:border-purple-500/20',
                                            default => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 border-gray-200 dark:border-gray-700'
                                        };
                                    @endphp
                                    <span class="w-fit mx-auto px-3 py-1.5 {{ $catColor }} rounded-xl text-[9px] font-black uppercase tracking-widest block text-center truncate border shadow-sm">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
 
                                <!-- Supplier -->
                                <div class="col-span-1 px-4 text-center">
                                    @if($product->supplier_id)
                                    <span class="w-fit mx-auto px-3 py-1.5 bg-primary-blue/5 rounded-xl text-[9px] font-black text-primary-blue uppercase tracking-widest italic border border-primary-blue/10 block truncate">
                                        {{ $product->supplier->name }}
                                    </span>
                                    @else
                                    <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest italic">Internal</span>
                                    @endif
                                </div>
 
                                <!-- Keuangan -->
                                <div class="col-span-1 px-2 text-center truncate">
                                    <div class="text-sm font-black text-primary-red italic truncate">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-span-1 px-2 text-center truncate">
                                    <div class="text-xs font-black text-gray-500 dark:text-gray-400 truncate">Rp{{ number_format($product->modal_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-span-1 px-2 text-center truncate">
                                    <div class="text-xs font-black text-green-500 truncate">Rp{{ number_format($product->price - $product->modal_price, 0, ',', '.') }}</div>
                                </div>
 
                                <!-- Status -->
                                <div class="col-span-1 px-2">
                                    @if($product->is_active)
                                        <span class="w-fit mx-auto flex items-center justify-center text-[9px] font-black text-green-500 uppercase tracking-widest bg-green-50 dark:bg-green-500/10 py-1.5 px-2 rounded-xl border border-green-100 dark:border-green-500/20">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="w-fit mx-auto flex items-center justify-center text-[9px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 py-1.5 px-2 rounded-xl border border-gray-100 dark:border-gray-800">
                                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
 
                                <!-- Options -->
                                <div class="col-span-1 px-4 flex justify-end gap-2">
                                    <button wire:click="editProduct('{{ $product->id }}')" class="p-2 bg-white dark:bg-gray-700 text-primary-blue rounded-lg shadow-sm hover:scale-110 transition-transform border border-gray-100 dark:border-gray-600">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $product->id }}')" class="p-2 bg-white dark:bg-gray-700 text-primary-red rounded-lg shadow-sm hover:scale-110 transition-transform border border-gray-100 dark:border-gray-600">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="block lg:hidden p-5 rounded-[2rem] border bg-white dark:bg-gray-900/40 transition-all duration-300 {{ $highlight == $product->id ? 'border-amber-400 bg-amber-400/5 animate-highlight-breath' : 'border-gray-100 dark:border-gray-800' }} {{ in_array($product->id, $selectedProducts) ? 'border-primary-blue bg-primary-blue/5' : '' }} mb-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <!-- Checkbox -->
                                        <input type="checkbox" wire:model.live="selectedProducts" value="{{ $product->id }}" class="mt-1.5 w-5 h-5 rounded-lg border-gray-200 text-primary-blue focus:ring-primary-blue dark:bg-gray-900 dark:border-gray-700">
                                        
                                        <div>
                                            <!-- Product Title -->
                                            <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic leading-snug">{{ $product->name }}</h3>
                                            <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/20 text-primary-blue border border-blue-100 dark:border-blue-900/30 rounded text-[8px] font-black uppercase tracking-wider inline-block mt-1">
                                                {{ $product->category->name }}
                                            </span>
                                            <span class="block text-[11px] font-black text-primary-red mt-1">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Aksi -->
                                    <div class="flex gap-1.5">
                                        <!-- Detail Eye Button -->
                                        <button @click="showProductDetail = true; detailProduct = { name: '{{ addslashes($product->name) }}', category: '{{ addslashes($product->category->name) }}', price: 'Rp{{ number_format($product->price, 0, ',', '.') }}', modal: 'Rp{{ number_format($product->modal_price, 0, ',', '.') }}', profit: 'Rp{{ number_format($product->price - $product->modal_price, 0, ',', '.') }}', supplier: '{{ $product->supplier_id ? addslashes($product->supplier->name) : 'Internal' }}', status: '{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}' }" class="p-2.5 bg-white dark:bg-gray-700 text-primary-blue rounded-xl shadow-sm border border-gray-150 dark:border-gray-650">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button wire:click="editProduct('{{ $product->id }}')" class="p-2.5 bg-white dark:bg-gray-700 text-primary-blue rounded-xl shadow-sm border border-gray-150 dark:border-gray-600">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <button wire:click="confirmDelete('{{ $product->id }}')" class="p-2.5 bg-white dark:bg-gray-700 text-primary-red rounded-xl shadow-sm border border-gray-150 dark:border-gray-600">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-32 text-center opacity-20">
                            <p class="text-xs font-black uppercase tracking-widest italic">Belum ada produk</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
                    {{ $products->links('livewire.partials.custom-pagination') }}
                </div>
            </div>
        @else
        <!-- Search bar for Grouping -->
        <div class="mb-8 max-w-md">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-4 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                <svg class="w-4 h-4 text-gray-400 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" wire:model.live="search" placeholder="Cari nama produk / supplier..." class="border-none p-0 focus:ring-0 font-black text-xs bg-transparent dark:text-white uppercase tracking-widest w-full">
            </div>
        </div>

        <!-- Grouped Cash Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($cashGroupedProducts as $categoryName => $prods)
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-2xl border border-gray-150 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                        <svg class="w-32 h-32 text-primary-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00 2 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-base font-black uppercase tracking-wider text-gray-800 dark:text-white mb-6 border-b-2 border-dashed border-gray-100 dark:border-gray-700 pb-3 flex justify-between items-center">
                        <span>{{ $categoryName }}</span>
                        <span class="text-[10px] bg-primary-blue/10 text-primary-blue dark:text-primary-blue-light px-2.5 py-1 rounded-lg font-black tracking-normal normal-case">{{ count($prods) }} Produk</span>
                    </h3>
                    
                    <!-- Paginated Product List (Alpine.js) -->
                    <div x-data="{ 
                        page: 1, 
                        perPage: 5, 
                        items: {{ json_encode($prods) }},
                        get totalPages() { return Math.ceil(this.items.length / this.perPage) || 1 },
                        get paginatedItems() {
                            let start = (this.page - 1) * this.perPage;
                            return this.items.slice(start, start + this.perPage);
                        }
                    }" x-init="$watch('items', () => page = 1)" class="flex flex-col justify-between h-[310px]">
                        <ul class="space-y-3.5 relative z-10">
                            <template x-for="prod in paginatedItems" :key="prod.id">
                                <li class="flex items-center justify-between text-xs py-2.5 border-b border-gray-100 dark:border-gray-700/50">
                                    <span class="font-bold text-gray-750 dark:text-gray-300 uppercase tracking-tight" x-text="prod.name"></span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[9px] font-black bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded uppercase" x-text="'M: Rp' + Number(prod.modal_price).toLocaleString('id-ID')"></span>
                                        <span class="text-[9px] font-black bg-green-500/10 text-green-500 px-2 py-0.5 rounded uppercase" x-text="'J: Rp' + Number(prod.price).toLocaleString('id-ID')"></span>
                                    </div>
                                </li>
                            </template>
                        </ul>

                        <!-- Pagination Controls inside Card -->
                        <div x-show="totalPages > 1" class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 text-xs font-black uppercase tracking-wider text-gray-400 relative z-20">
                            <button type="button" @click="if (page > 1) page--" :disabled="page === 1" class="px-3.5 py-2.5 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-primary-blue hover:text-white disabled:opacity-20 disabled:hover:bg-gray-100 disabled:hover:text-gray-450 transition-all flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                Prev
                            </button>
                            <span class="text-gray-550 font-black" x-text="page + ' / ' + totalPages"></span>
                            <button type="button" @click="if (page < totalPages) page++" :disabled="page === totalPages" class="px-3.5 py-2.5 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-primary-blue hover:text-white disabled:opacity-20 disabled:hover:bg-gray-100 disabled:hover:text-gray-450 transition-all flex items-center gap-1 cursor-pointer">
                                Next
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-[3rem] p-12 text-center border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest italic">Belum ada pemetaan kategori kas.</p>
                </div>
            @endforelse
        </div>
        @endif
    </div>
    @endif

    @if($tab === 'stock')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-12 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-10">
                <div class="w-14 h-14 bg-primary-blue rounded-2xl flex items-center justify-center text-white mr-6 shadow-lg shadow-blue-900/20">
                    <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.27 6.96 8.73 5.04 8.73-5.04"/><path d="M12 22.08V12"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Input Stok Awal Harian</h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Tentukan stok produk saat membuka toko</p>
                </div>
            </div>

            <form wire:submit.prevent="saveStock" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Tanggal Stok</label>
                        <input type="date" wire:model="stock_date" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                        @error('stock_date') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Pilih Produk</label>
                        <select wire:model="stock_product_id" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                            <option value="">Pilih Produk</option>
                            @foreach(\App\Models\Product::orderBy('name')->get() as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('stock_product_id') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Jumlah Stok Awal</label>
                    <input type="number" wire:model="opening_stock" class="w-full px-8 py-6 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-3xl text-primary-blue text-center tracking-tighter">
                    @error('opening_stock') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full py-6 bg-primary-blue text-white rounded-[2.5rem] shadow-2xl shadow-blue-900/30 font-black italic uppercase tracking-widest transform hover:-translate-y-1 transition-all text-lg">
                    Simpan Stok Awal
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Product Form Modal -->
    <div 
        x-data="{ show: @entangle('showFormModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center sm:p-6 p-3 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            x-show="show"
            @click.away="show = false"
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3rem] sm:p-10 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto no-scrollbar shadow-2xl border border-gray-100 dark:border-gray-700 relative"
            x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-300 transform"
            x-transition:enter-start="opacity-0 scale-75 translate-y-20"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-75 translate-y-20"
        >
            <!-- Close Button -->
            <button type="button" wire:click="cancelEdit" class="absolute top-8 right-8 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex items-center mb-8">
                <div class="w-14 h-14 bg-primary-red rounded-2xl flex items-center justify-center text-white mr-4 shadow-lg shadow-red-900/20">
                    <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.27 6.96 8.73 5.04 8.73-5.04"/><path d="M12 22.08V12"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                        {{ $editingId ? 'Edit Informasi Produk' : 'Tambah Produk Baru' }}
                    </h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Lengkapi data katalog produk di bawah ini</p>
                </div>
            </div>

            <form wire:submit.prevent="saveProduct" class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Nama Produk</label>
                    <input type="text" wire:model.blur="name" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white uppercase tracking-tight">
                    @error('name') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Kategori</label>
                        <select wire:model="category_id" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Status Keaktifan</label>
                        <div class="flex items-center h-[52px] bg-gray-50 dark:bg-gray-900 px-6 rounded-2xl">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-blue"></div>
                                <span class="ml-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produk Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Supplier / Penitip (Opsional)</label>
                        <select wire:model="supplier_id" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(!session('active_jurusan_id'))
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Jurusan / Unit TEFA</label>
                        <select wire:model="jurusan_id" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white">
                            <option value="">Pilih Jurusan / Global</option>
                            @foreach($jurusans as $jur)
                                <option value="{{ $jur->id }}">{{ $jur->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <!-- Price fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-6 inset-y-0 flex items-center text-[10px] font-black text-gray-400">Rp</span>
                            <input type="number" wire:model.live="price" class="w-full pl-12 pr-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                        </div>
                        @error('price') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Profit/Unit</label>
                        <div class="relative">
                            <span class="absolute left-6 inset-y-0 flex items-center text-[10px] font-black text-gray-400">Rp</span>
                            <input type="number" wire:model.live="profit_per_unit" class="w-full pl-12 pr-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                        </div>
                        @error('profit_per_unit') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2 italic">Estimasi Harga Modal</label>
                    <div class="relative">
                        <span class="absolute left-6 inset-y-0 flex items-center text-[10px] font-black text-gray-400">Rp</span>
                        <input type="number" wire:model.live="modal_price" class="w-full pl-12 pr-6 py-4 bg-white dark:bg-gray-800 border-none rounded-xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                    </div>
                </div>

                @if($editingId)
                <div class="p-6 bg-amber-500/5 dark:bg-amber-500/5 rounded-2xl border border-dashed border-amber-500/30 space-y-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="update_history" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500"></div>
                        <span class="ml-3 text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">Perbarui Harga di Riwayat Transaksi</span>
                    </label>
                    <p class="text-[9px] font-bold text-gray-400 leading-relaxed uppercase pl-14">Centang ini jika ingin mengubah harga produk ini di seluruh riwayat transaksi sebelumnya agar mengikuti harga baru (misal karena salah input harga).</p>
                </div>
                @endif

                <!-- Restock Assistant -->
                <div class="p-6 bg-blue-500/5 dark:bg-primary-blue/5 rounded-3xl border-2 border-dashed border-primary-blue/30 space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-primary-blue dark:text-primary-blue-light">Asisten Restock & Modal (Opsional)</h3>
                    <p class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase">Gunakan ini jika ingin menginput stok baru sekaligus menghitung harga modal unit secara otomatis berdasarkan total belanja modal.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-2 ml-2 italic">Jumlah Restock (Pcs)</label>
                            <input type="number" wire:model.live="restockQty" placeholder="Contoh: 100" class="w-full px-5 py-3.5 bg-white dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/30 font-black text-sm text-gray-800 dark:text-white">
                            @error('restockQty') <span class="text-[9px] font-bold text-primary-red mt-1.5 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-2 ml-2 italic">Total Modal Restock (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-5 inset-y-0 flex items-center text-[9px] font-black text-gray-400">Rp</span>
                                <input type="number" wire:model.live="totalModalCost" placeholder="Contoh: 100000" class="w-full pl-10 pr-5 py-3.5 bg-white dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/30 font-black text-sm text-gray-800 dark:text-white">
                            </div>
                            @error('totalModalCost') <span class="text-[9px] font-bold text-primary-red mt-1.5 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="flex-1 py-5 bg-primary-red text-white rounded-[2rem] shadow-2xl shadow-red-500/20 font-black italic uppercase tracking-wider transform hover:-translate-y-1 transition-all">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Produk' }}
                    </button>
                    <button type="button" wire:click="cancelEdit" class="px-8 py-5 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-[2rem] font-black uppercase text-[10px] tracking-widest hover:text-gray-600 dark:hover:text-white transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div 
        x-data="{ show: @entangle('showDeleteModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-800 rounded-[3rem] p-12 max-w-md w-full shadow-2xl text-center"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        >
            <div class="w-20 h-20 bg-primary-red/10 text-primary-red rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </div>
            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-4">Hapus Produk?</h3>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-10 leading-relaxed">Tindakan ini tidak dapat dibatalkan. Seluruh data terkait produk ini akan dihapus permanen.</p>
            <div class="flex gap-4">
                <button wire:click="cancelDelete" class="flex-1 py-4 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:text-gray-600 transition-all">Batal</button>
                <button wire:click="deleteProduct" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-500/30 hover:scale-105 transition-all">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div 
        x-data="{ show: @entangle('showBulkDeleteModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-800 rounded-[3rem] p-12 max-w-md w-full shadow-2xl text-center"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        >
            <div class="w-20 h-20 bg-primary-red/10 text-primary-red rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </div>
            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-4">Hapus Banyak Produk?</h3>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-10 leading-relaxed">Tindakan ini tidak dapat dibatalkan. Seluruh produk yang dipilih ({{ count($selectedProducts) }} produk) akan dihapus permanen.</p>
            <div class="flex gap-4">
                <button wire:click="cancelBulkDelete" class="flex-1 py-4 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:text-gray-600 transition-all">Batal</button>
                <button wire:click="bulkDelete" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-500/30 hover:scale-105 transition-all">Ya, Hapus Semua</button>
            </div>
        </div>
    </div>

    <!-- Product Detail Modal (Alpine.js) -->
    <div x-show="showProductDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showProductDetail = false" class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 max-w-md w-full shadow-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-black italic uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Detail Produk</h3>
                <button @click="showProductDetail = false" class="text-gray-400 hover:text-gray-650 dark:hover:text-white text-2xl font-black">&times;</button>
            </div>
            
            <div class="py-6 space-y-4 text-left">
                <div>
                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Nama Produk</span>
                    <span class="text-base font-black text-gray-800 dark:text-white uppercase italic tracking-tight" x-text="detailProduct.name"></span>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Kategori</span>
                        <span class="text-xs font-bold text-gray-650 dark:text-gray-300" x-text="detailProduct.category"></span>
                    </div>
                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Status</span>
                        <span class="text-xs font-bold text-gray-650 dark:text-gray-300" x-text="detailProduct.status"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Harga Jual</span>
                        <span class="text-sm font-black text-primary-red" x-text="detailProduct.price"></span>
                    </div>
                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Harga Modal</span>
                        <span class="text-sm font-black text-gray-500 dark:text-gray-405" x-text="detailProduct.modal"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Profit</span>
                        <span class="text-sm font-black text-green-500" x-text="detailProduct.profit"></span>
                    </div>
                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Supplier</span>
                        <span class="text-xs font-bold text-gray-650 dark:text-gray-300" x-text="detailProduct.supplier"></span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button @click="showProductDetail = false" class="px-6 py-3 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-250 transition-all">Tutup</button>
            </div>
        </div>
    </div>
</div>
