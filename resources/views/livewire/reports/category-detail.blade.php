<div class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ $this->backUrl }}" wire:navigate class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-xl transition-all text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">Detail Kategori</h1>
                    <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Analisis Penjualan Berdasarkan Produk</p>
                </div>
            </div>
        </div>

        <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-4 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
            <svg class="w-5 h-5 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <div class="font-black text-sm text-gray-800 dark:text-white uppercase tracking-wider">
                {{ $this->formattedPeriod }}
            </div>
        </div>
    </div>

    <!-- Category Performance Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 mb-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-5">
            <div>
                <span class="px-4 py-2 bg-primary-blue/10 text-primary-blue dark:text-primary-blue-light text-[10px] font-black uppercase tracking-widest rounded-xl">Kategori Terpilih</span>
                <h2 class="text-3xl font-black italic uppercase tracking-tight text-gray-800 dark:text-white mt-3">{{ $categoryName }}</h2>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 lg:gap-12 flex-1 max-w-4xl">
                <!-- Vol -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Volume Terjual</p>
                    <p class="text-xl font-black text-gray-800 dark:text-white mt-1">{{ number_format($summary->total_qty ?? 0, 0, ',', '.') }} <span class="text-[10px] text-gray-400 font-normal">Unit</span></p>
                </div>
                <!-- HPP -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total Modal (HPP)</p>
                    <p class="text-xl font-black text-gray-600 dark:text-gray-300 mt-1">Rp{{ number_format($summary->total_modal ?? 0, 0, ',', '.') }}</p>
                </div>
                <!-- Untung -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-l-4 border-primary-red">
                    <p class="text-[9px] font-bold text-primary-red uppercase tracking-widest">Keuntungan</p>
                    <p class="text-xl font-black text-primary-red mt-1">Rp{{ number_format($summary->total_profit ?? 0, 0, ',', '.') }}</p>
                </div>
                <!-- Omzet -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-l-4 border-primary-blue">
                    <p class="text-[9px] font-bold text-primary-blue uppercase tracking-widest">Omzet</p>
                    <p class="text-xl font-black text-primary-blue mt-1">Rp{{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Daftar Produk Terjual</h2>
            
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <!-- Search -->
                <div class="relative group w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Cari nama produk..." class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300 shadow-inner">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                        <th class="py-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-primary-blue transition-colors" wire:click="sort('product_name')">
                            Produk
                            @if($sortBy === 'product_name')
                                <span>{!! $sortDirection === 'asc' ? '▲' : '▼' !!}</span>
                            @endif
                        </th>
                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center cursor-pointer hover:text-primary-blue transition-colors" wire:click="sort('total_qty')">
                            Volume
                            @if($sortBy === 'total_qty')
                                <span>{!! $sortDirection === 'asc' ? '▲' : '▼' !!}</span>
                            @endif
                        </th>
                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                            Harga Satuan
                        </th>
                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right cursor-pointer hover:text-primary-blue transition-colors" wire:click="sort('total_modal')">
                            Total Modal (HPP)
                            @if($sortBy === 'total_modal')
                                <span>{!! $sortDirection === 'asc' ? '▲' : '▼' !!}</span>
                            @endif
                        </th>
                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right cursor-pointer hover:text-primary-blue transition-colors" wire:click="sort('total_profit')">
                            Keuntungan
                            @if($sortBy === 'total_profit')
                                <span>{!! $sortDirection === 'asc' ? '▲' : '▼' !!}</span>
                            @endif
                        </th>
                        <th class="py-6 px-10 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right cursor-pointer hover:text-primary-blue transition-colors" wire:click="sort('total_revenue')">
                            Omzet
                            @if($sortBy === 'total_revenue')
                                <span>{!! $sortDirection === 'asc' ? '▲' : '▼' !!}</span>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($products as $product)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all">
                            <td class="py-6 px-10">
                                <div>
                                    <p class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $product->product_name }}</p>
                                    @if($product->supplier_name)
                                        <span class="text-[9px] px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded font-bold text-gray-500 uppercase mt-1 inline-block">Supplier: {{ $product->supplier_name }}</span>
                                    @else
                                        <span class="text-[9px] px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 rounded font-bold text-primary-blue uppercase mt-1 inline-block">Internal</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-6 text-center">
                                <span class="text-sm font-black text-gray-800 dark:text-white">{{ $product->total_qty }} <span class="text-[9px] text-gray-400 uppercase ml-1">Unit</span></span>
                            </td>
                            <td class="py-6 text-right">
                                <div class="text-xs">
                                    <p class="text-gray-800 dark:text-white font-bold">Rp{{ number_format($product->unit_price, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-gray-400">Margin: Rp{{ number_format($product->unit_profit, 0, ',', '.') }}</p>
                                </div>
                            </td>
                            <td class="py-6 text-right">
                                <span class="text-sm font-bold text-gray-400">Rp{{ number_format($product->total_modal, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-6 text-right">
                                <span class="text-base font-black text-primary-red tracking-tighter">Rp{{ number_format($product->total_profit, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-6 px-10 text-right">
                                <span class="text-base font-black text-primary-blue tracking-tighter">Rp{{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-sm font-black uppercase tracking-wider">Tidak ada produk terjual</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="p-10 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
