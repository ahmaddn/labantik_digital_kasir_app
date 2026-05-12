<div class="flex h-screen w-full bg-[#f8fafc] dark:bg-gray-950 overflow-hidden font-outfit">
    <!-- 1. Nano Sidebar (Navigation) -->
    <div class="w-20 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 flex flex-col items-center py-8 z-30">
        <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-primary-red rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-500/20 hover:scale-110 active:scale-95 transition-all mb-10">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </a>
        
        <div class="flex-1 flex flex-col items-center gap-6">
            <button wire:click="selectCategory(null)" class="group relative p-4 rounded-2xl transition-all {{ is_null($selectedCategory) ? 'bg-primary-blue text-white shadow-xl shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-blue' }}">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="absolute left-full ml-4 px-3 py-1 bg-gray-800 text-white text-[10px] font-black rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 uppercase tracking-widest">Semua Kategori</span>
            </button>

            @foreach($categories as $cat)
            <button wire:click="selectCategory({{ $cat->id }})" class="group relative p-4 rounded-2xl transition-all {{ $selectedCategory == $cat->id ? 'bg-primary-blue text-white shadow-xl shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-blue' }}">
                <div class="w-6 h-6 flex items-center justify-center text-[10px] font-black uppercase tracking-tighter">{{ substr($cat->name, 0, 2) }}</div>
                <span class="absolute left-full ml-4 px-3 py-1 bg-gray-800 text-white text-[10px] font-black rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 uppercase tracking-widest">{{ $cat->name }}</span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- 2. Main Content Area (Search + Products) -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 dark:bg-gray-950/50 z-10">
        <!-- Sticky Header with Search -->
        <div class="p-8 pb-4 bg-gray-50/80 dark:bg-gray-950/80 backdrop-blur-xl z-20">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1 max-w-2xl relative group">
                    <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary-blue transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari menu favorit..." class="w-full pl-16 pr-8 py-5 bg-white dark:bg-gray-900 rounded-[2.5rem] border-none shadow-2xl shadow-blue-900/5 focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-gray-800 dark:text-white placeholder:text-gray-300 placeholder:italic transition-all">
                </div>
                
                <div class="hidden lg:flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 bg-white dark:bg-gray-900 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 hover:bg-gray-50 transition-all active:scale-95 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-blue transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-primary-blue">Dashboard</span>
                    </a>

                    <div class="flex items-center gap-4 bg-white dark:bg-gray-900 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                        <div class="flex flex-col items-end">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs font-black text-primary-blue uppercase tracking-tighter" id="real-time-clock"></p>
                        </div>
                    </div>
                    
                    <button wire:click="editOpeningStock" class="flex items-center gap-3 bg-white dark:bg-gray-900 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 hover:bg-gray-50 transition-all active:scale-95 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-blue transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m16 6 4 14"/><path d="M12 6v14"/><path d="M8 8v12"/><path d="M4 4v16"/></svg>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-primary-blue">Update Stok Awal</span>
                    </button>
                    
                    <button wire:click="finishSession" class="flex items-center gap-3 bg-primary-red hover:bg-red-600 text-white px-6 py-3 rounded-2xl shadow-xl shadow-red-500/20 transition-all active:scale-95 group">
                        <span class="text-[10px] font-black uppercase tracking-widest">Selesaikan Sesi</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                    
                    <script>
                        function updateClock() {
                            const now = new Date();
                            const el = document.getElementById('real-time-clock');
                            if (el) el.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        }
                        setInterval(updateClock, 1000);
                        updateClock();
                    </script>
                </div>

            </div>
        </div>

        <!-- Scrollable Product Grid -->
        <div class="flex-1 overflow-y-auto px-8 pb-12 scrollbar-hide">
            @if($products->isEmpty())
                <div class="h-full flex flex-col items-center justify-center opacity-30 py-32">
                    <div class="w-32 h-32 bg-white dark:bg-gray-900 rounded-[3rem] flex items-center justify-center mb-10 shadow-inner">
                        <svg class="w-16 h-16 text-gray-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    </div>
                    <h3 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Tidak Ada Menu</h3>
                    <p class="text-gray-400 font-bold text-sm mt-4 uppercase tracking-[0.3em] italic">Coba kata kunci atau kategori lain</p>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-8">
                    @foreach($products as $product)
                    <button wire:click="addToCart({{ $product->id }})" class="group relative bg-white dark:bg-gray-900 rounded-[3rem] p-8 text-left shadow-xl shadow-blue-900/5 border border-transparent hover:border-primary-blue hover:-translate-y-2 transition-all duration-300 flex flex-col h-full">
                        <!-- Card Decorative Background -->
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-primary-blue/5 rounded-full group-hover:scale-[10] transition-transform duration-700 pointer-events-none"></div>
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <!-- Product Icon/Placeholder -->
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-[1.5rem] flex items-center justify-center text-gray-400 group-hover:bg-primary-blue group-hover:text-white transition-all duration-300 mb-8 shadow-sm">
                                <span class="text-2xl font-black italic uppercase">{{ substr($product->name, 0, 1) }}</span>
                            </div>

                            <div class="mb-6 flex-1">
                                <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight leading-tight group-hover:text-white transition-colors duration-300 line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2 group-hover:text-white/60 transition-colors duration-300">
                                    {{ $product->category->name }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-auto">
                                <p class="text-xl font-black text-primary-red italic group-hover:text-white transition-colors duration-300">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                
                                @if(isset($cart[$product->id]))
                                <div class="w-10 h-10 bg-primary-red text-white rounded-2xl flex items-center justify-center text-xs font-black shadow-lg shadow-red-500/30 group-hover:bg-white group-hover:text-primary-red transition-all">
                                    {{ $cart[$product->id]['quantity'] }}
                                </div>
                                @else
                                <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-300 group-hover:bg-white/20 group-hover:text-white transition-all">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                </div>
                                @endif
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
                
                <div class="mt-16">
                    {{ $products->links('livewire.custom-pagination') }}
                </div>
            @endif
        </div>
    </div>

    <!-- 3. Right Sidebar (Cart & Checkout) -->
    <div class="w-[450px] bg-white dark:bg-gray-900 border-l border-gray-100 dark:border-gray-800 flex flex-col shadow-[-20px_0_60px_-15px_rgba(0,0,0,0.05)] z-40">
        <!-- Cart Tabs -->
        <div class="p-2 mx-8 mt-8 bg-gray-50 dark:bg-gray-950 rounded-2xl flex border border-gray-100 dark:border-gray-800 shadow-inner">
            <button wire:click="setRightSidebarTab('cart')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $rightSidebarTab === 'cart' ? 'bg-white dark:bg-gray-800 text-primary-blue shadow-sm' : 'text-gray-400 hover:text-primary-blue' }}">Pesanan</button>
            <button wire:click="setRightSidebarTab('history')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $rightSidebarTab === 'history' ? 'bg-white dark:bg-gray-800 text-primary-red shadow-sm' : 'text-gray-400 hover:text-primary-red' }}">History</button>
        </div>

        @if($rightSidebarTab === 'cart')
        <!-- Cart Header -->
        <div class="px-10 py-8 flex justify-between items-center animate-in fade-in duration-300">
            <div>
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Isi Keranjang</h2>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ count($cart) }} Item Dipilih</p>
            </div>
            <button wire:click="clearCart" class="p-3 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-red hover:bg-primary-red/5 transition-all">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </button>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-10 space-y-8 scrollbar-hide">
            @forelse($cart as $item)
            <div class="flex items-center gap-6 group animate-in slide-in-from-right duration-300">
                <div class="w-14 h-14 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-primary-blue text-sm font-black italic shadow-sm group-hover:scale-110 transition-transform">
                    {{ substr($item['name'], 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">{{ $item['name'] }}</h4>
                    <p class="text-xs font-black text-primary-red italic mt-1">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                </div>
                <div class="flex items-center bg-gray-50 dark:bg-gray-800 rounded-2xl p-1.5 shadow-inner">
                    <button wire:click="removeFromCart({{ $item['id'] }})" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-primary-red transition-colors font-black">-</button>
                    <span class="w-10 text-center text-xs font-black text-gray-800 dark:text-white">{{ $item['quantity'] }}</span>
                    <button wire:click="addToCart({{ $item['id'] }})" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-primary-blue transition-colors font-black">+</button>
                </div>
            </div>
            @empty
            <div class="h-full flex flex-col items-center justify-center py-20">
                <div class="w-40 h-40 bg-gray-50 dark:bg-gray-950 rounded-[4rem] flex items-center justify-center mb-8 shadow-inner opacity-20">
                    <svg class="w-20 h-20" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.56-7.43H5.12"/></svg>
                </div>
                <p class="text-xs font-black uppercase tracking-[0.3em] italic text-gray-300">Keranjang Masih Kosong</p>
            </div>
            @endforelse
        </div>
        @else
        <!-- History Section -->
        <div class="flex-1 overflow-y-auto p-10 space-y-6 scrollbar-hide animate-in slide-in-from-right duration-500">
            <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-8">Transaksi Terakhir</h2>
            @foreach($this->recentTransactions as $history)
            <div class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-[2rem] border border-transparent hover:border-primary-blue/20 transition-all group">
                <div class="w-12 h-12 bg-white dark:bg-gray-900 rounded-2xl flex items-center justify-center text-primary-blue text-[10px] font-black italic shadow-sm">
                    {{ $history->transacted_at->format('H:i') }}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">{{ $history->product->name }}</h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[9px] font-black {{ $history->status === 'uang_diterima' ? 'text-green-500' : 'text-primary-red' }} uppercase tracking-widest">{{ str_replace('_', ' ', $history->status) }}</span>
                        <span class="text-[9px] font-bold text-gray-400">× {{ $history->quantity }}</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <p class="text-xs font-black text-gray-800 dark:text-white italic">Rp{{ number_format($history->total_price, 0, ',', '.') }}</p>
                    <button wire:click="editTransaction({{ $history->id }})" class="p-2 bg-white dark:bg-gray-900 text-gray-300 hover:text-primary-blue rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all group-hover:scale-110">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Checkout Section -->
        <div class="p-10 bg-gray-50 dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800 space-y-8 rounded-t-[4rem] shadow-[0_-15px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between items-end">
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Total Pembayaran</span>
                    <span class="text-4xl font-black italic text-primary-blue tracking-tighter">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative group">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-3">Pembeli</label>
                        <input type="text" wire:model="buyer_name" placeholder="Guest Customer" class="w-full px-6 py-4 bg-white dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight placeholder:text-gray-300">
                    </div>
                    <div class="relative group">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-3">Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute left-5 inset-y-0 flex items-center text-[10px] font-black text-gray-300">Rp</span>
                            <input type="number" wire:model.live="payment_amount" class="w-full pl-12 pr-6 py-4 bg-white dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                        </div>
                    </div>
                </div>

                @if($payment_amount > 0)
                <div class="flex justify-between items-center px-8 py-4 {{ $change < 0 ? 'bg-red-500/10' : 'bg-primary-blue/5' }} dark:bg-opacity-20 rounded-2xl animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full {{ $change < 0 ? 'bg-primary-red' : 'bg-primary-blue' }} mr-3 animate-pulse"></div>
                        <span class="text-[10px] font-black {{ $change < 0 ? 'text-primary-red' : 'text-primary-blue' }} uppercase tracking-widest">{{ $change < 0 ? 'Kurang Bayar' : 'Uang Kembalian' }}</span>
                    </div>
                    <span class="text-xl font-black italic {{ $change < 0 ? 'text-primary-red' : 'text-primary-blue' }}">Rp{{ number_format(abs($change), 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="flex gap-3">
                    @php
                        $statuses = [
                            'uang_diterima' => ['label' => 'LUNAS', 'color' => 'bg-green-500', 'icon' => 'M20 6 9 17 4 12'],
                            'belum_kembalian' => ['label' => 'PENDING', 'color' => 'bg-primary-blue', 'icon' => 'M12 8v4l3 3'],
                            'belum_menerima_uang' => ['label' => 'HUTANG', 'color' => 'bg-primary-red', 'icon' => 'M12 9v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ];
                    @endphp
                    @foreach($statuses as $val => $cfg)
                    <button wire:click="$set('status', '{{ $val }}')" class="flex-1 py-4 rounded-2xl flex flex-col items-center gap-2 transition-all {{ $status === $val ? $cfg['color'] . ' text-white shadow-xl shadow-' . explode('-', $cfg['color'])[1] . '-500/30 scale-105' : 'bg-white dark:bg-gray-900 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $cfg['icon'] }}"/></svg>
                        <span class="text-[8px] font-black uppercase tracking-[0.2em]">{{ $cfg['label'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <button wire:click="checkout" @if(empty($cart) || $change < 0) disabled @endif class="w-full py-8 bg-primary-red text-white rounded-[2.5rem] shadow-2xl shadow-red-500/30 font-black italic uppercase tracking-[0.2em] transform hover:-translate-y-2 active:scale-95 transition-all text-xl disabled:opacity-30 disabled:transform-none group relative overflow-hidden">
                <span class="relative z-10">Konfirmasi Pesanan</span>
                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            </button>

        </div>
    </div>

    <!-- 4. Success Overlay (Enhanced) -->
    <div x-data="{ show: false }" 
         x-on:transaction-complete.window="show = true; setTimeout(() => show = false, 1200)"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 backdrop-blur-0"
         x-transition:enter-end="opacity-100 backdrop-blur-md"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 backdrop-blur-md"
         x-transition:leave-end="opacity-0 backdrop-blur-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/20">
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-500 delay-100"
            x-transition:enter-start="opacity-0 scale-50 translate-y-20"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
            class="bg-white dark:bg-gray-900 p-16 rounded-[5rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.3)] flex flex-col items-center gap-8 border-t-8 border-green-500"
        >
            <div class="w-32 h-32 bg-green-500 text-white rounded-full flex items-center justify-center shadow-2xl shadow-green-500/40 animate-bounce">
                <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="text-center">
                <h3 class="text-4xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Selesai!</h3>
                <p class="text-gray-400 font-bold text-sm mt-3 uppercase tracking-[0.3em] italic">Transaksi Berhasil Dicatat</p>
            </div>
        </div>
    </div>

    <!-- 5. Opening Stock Modal -->
    <div 
        x-data="{ show: @entangle('showOpeningStockModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white dark:bg-gray-900 w-full max-w-4xl max-h-[80vh] overflow-hidden rounded-[3rem] shadow-2xl flex flex-col"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        >
            <div class="p-10 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Stok Awal Hari Ini</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Mohon masukkan jumlah stok awal untuk setiap produk</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" wire:model.live="modalSearch" placeholder="Cari..." class="pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 text-[10px] font-black uppercase tracking-widest">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <select wire:model.live="modalCategory" class="pl-4 pr-10 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-[10px] font-black text-gray-400 uppercase tracking-widest rounded-xl hover:text-primary-red transition-all">
                        Kembali
                    </a>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-10 scrollbar-hide">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach(collect($allProducts)->when($modalSearch, fn($c) => $c->filter(fn($p) => str_contains(strtolower($p->name), strtolower($modalSearch))))->when($modalCategory, fn($c) => $c->where('category_id', $modalCategory)) as $p)
                    <div wire:key="opening-stock-{{ $p->id }}" class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-transparent hover:border-primary-blue/20 transition-all">
                        <div class="min-w-0">
                            <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">{{ $p->name }}</h4>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ $p->category ? $p->category->name : 'No Category' }}</p>
                        </div>
                        <div class="w-32">
                            <input type="number" wire:model.blur="stockItems.{{ $p->id }}" class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-none rounded-xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-center">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
 
            <div class="p-10 bg-gray-50 dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800">
                <button wire:click="saveOpeningStock" class="w-full py-6 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase tracking-widest hover:-translate-y-1 transition-all">
                    Simpan Stok Awal & Mulai Jualan
                </button>
            </div>
        </div>
    </div>

    <!-- 6. Closing Stock Modal -->
    <div 
        x-data="{ show: @entangle('showClosingStockModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white dark:bg-gray-900 w-full max-w-4xl max-h-[80vh] overflow-hidden rounded-[3rem] shadow-2xl flex flex-col"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        >
            <div class="p-10 border-b border-gray-50 dark:border-gray-800 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white text-primary-red">Akhiri Sesi</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Masukkan jumlah stok akhir (sisa) hari ini</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" wire:model.live="modalSearch" placeholder="Cari..." class="pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-primary-red/20 text-[10px] font-black uppercase tracking-widest">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <select wire:model.live="modalCategory" class="pl-4 pr-10 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-primary-red/20 text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button wire:click="$set('showClosingStockModal', false)" class="text-[10px] font-black text-gray-400 uppercase hover:text-primary-red transition-colors">Batal</button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-10 scrollbar-hide">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach(collect($allProducts)->when($modalSearch, fn($c) => $c->filter(fn($p) => str_contains(strtolower($p->name), strtolower($modalSearch))))->when($modalCategory, fn($c) => $c->where('category_id', $modalCategory)) as $p)
                    <div wire:key="closing-stock-{{ $p->id }}" class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-transparent hover:border-primary-red/20 transition-all">
                        <div class="min-w-0">
                            <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-1">{{ $p->name }}</h4>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ $p->category ? $p->category->name : 'No Category' }}</p>
                        </div>
                        <div class="w-32">
                            <input type="number" wire:model.blur="stockItems.{{ $p->id }}" class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-none rounded-xl focus:ring-4 focus:ring-primary-red/10 font-black text-sm text-center">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="p-10 bg-gray-50 dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800">
                <button wire:click="saveClosingStock" class="w-full py-6 bg-primary-red text-white rounded-2xl shadow-xl shadow-red-500/20 font-black italic uppercase tracking-widest hover:-translate-y-1 transition-all">
                    Simpan Stok Akhir & Selesaikan Hari
                </button>
            </div>
        </div>
    </div>
    
    <!-- 7. Edit Transaction Modal -->
    <div 
        x-data="{ show: @entangle('showEditModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[3rem] shadow-2xl flex flex-col p-10 gap-8"
        >
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-black italic uppercase tracking-tighter text-primary-blue">Edit Transaksi</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Koreksi kesalahan input data</p>
                </div>
                <button @click="show = false" class="text-gray-300 hover:text-primary-red transition-colors">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">Pembeli</label>
                        <input type="text" wire:model="editBuyer" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl font-black text-xs uppercase tracking-tight focus:ring-4 focus:ring-primary-blue/10">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">Quantity</label>
                        <input type="number" wire:model="editQty" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl font-black text-sm text-center focus:ring-4 focus:ring-primary-blue/10">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">Status Pembayaran</label>
                    <select wire:model="editStatus" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl font-black text-xs uppercase tracking-widest focus:ring-4 focus:ring-primary-blue/10">
                        <option value="uang_diterima">Uang Diterima</option>
                        <option value="belum_kembalian">Belum Kembalian</option>
                        <option value="belum_menerima_uang">Belum Bayar (Hutang)</option>
                        <option value="uang_dipinjam">Uang Dipinjam</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">Catatan</label>
                    <textarea wire:model="editNote" rows="3" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl font-bold text-xs focus:ring-4 focus:ring-primary-blue/10"></textarea>
                </div>
            </div>

            <button wire:click="updateTransaction" class="w-full py-5 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest hover:scale-[1.02] active:scale-95 transition-all">
                Simpan Perubahan
            </button>
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
</div>
