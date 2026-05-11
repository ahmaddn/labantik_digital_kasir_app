<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Rekap Harian</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Pembukuan Transaksi Digital</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <input type="date" wire:model.live="selectedDate" class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white">
            </div>

            <a href="{{ route('inventory-report', ['date' => $selectedDate]) }}" class="px-8 py-4 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                Audit Stok
            </a>

            @if($recap)
            <button wire:click="exportCSV" class="px-8 py-4 bg-primary-red text-white rounded-2xl shadow-xl shadow-red-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Export CSV
            </button>
            @endif

        </div>
    </div>

    @if($recap)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Revenue Card -->
        <div class="bg-primary-blue rounded-[3rem] p-10 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Total Omzet Tunai</h3>
            <p class="text-4xl font-black italic text-white">Rp{{ number_format($recap->total_revenue_real, 0, ',', '.') }}</p>
            <div class="mt-8 pt-8 border-t border-white/10 flex justify-between items-center">
                <span class="text-[10px] font-bold opacity-50 uppercase tracking-widest">Gross Total:</span>
                <span class="text-xs font-black">Rp{{ number_format($recap->total_revenue_all, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Keuntungan Bersih</h3>
            <p class="text-4xl font-black italic text-primary-red">Rp{{ number_format($recap->total_profit, 0, ',', '.') }}</p>
            <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Modal Terpakai:</span>
                <span class="text-xs font-black text-gray-800 dark:text-white">Rp{{ number_format($recap->total_modal, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6">Status Pembayaran</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Lunas</span>
                    </div>
                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $recap->count_received }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-primary-blue mr-3"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Pending</span>
                    </div>
                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $recap->count_unpaid_change }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-primary-red mr-3"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Hutang</span>
                    </div>
                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $recap->count_no_payment }}</span>
                </div>
            </div>
        </div>

        <!-- Date Info Card -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 flex flex-col justify-center text-center">
            <p class="text-[10px] font-black text-primary-blue uppercase tracking-widest mb-1">{{ $recap->month_name }}</p>
            <p class="text-3xl font-black italic text-gray-800 dark:text-white">Minggu ke-{{ $recap->month_week }}</p>
            <div class="mt-6 flex items-center justify-center text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                <svg class="w-3 h-3 mr-2 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Sync: {{ $recap->generated_at->format('H:i:s') }}
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Detail Transaksi Harian</h2>
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <!-- Search -->
                <div class="relative group w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Cari transaksi..." class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300 shadow-inner">
                </div>

                <!-- Filter Status -->
                <div class="relative group w-full md:w-48">
                    <select wire:model.live="filterStatus" class="w-full pl-6 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest appearance-none shadow-inner">
                        <option value="">Semua Status</option>
                        <option value="uang_diterima">Uang Diterima</option>
                        <option value="belum_kembalian">Belum Kembalian</option>
                        <option value="belum_menerima_uang">Belum Bayar</option>
                    </select>
                </div>

                <!-- Filter Kategori -->
                <div class="relative group w-full md:w-48">
                    <select wire:model.live="filterCategory" class="w-full pl-6 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest appearance-none shadow-inner">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jam</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Qty</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-10 py-8">
                            <span class="text-xs font-black text-gray-400 uppercase">{{ $tx->transacted_at->format('H:i') }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $tx->product->name }}</div>
                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $tx->buyer_name ?? 'Guest' }}</div>
                        </td>
                        <td class="px-10 py-8 text-xs font-bold text-gray-500 italic">Rp{{ number_format($tx->unit_price, 0, ',', '.') }}</td>
                        <td class="px-10 py-8 text-xs font-black text-gray-800 dark:text-gray-300">{{ $tx->quantity }}</td>
                        <td class="px-10 py-8">
                            <span class="text-base font-black text-primary-red italic">Rp{{ number_format($tx->total_price, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <span class="text-[9px] font-black uppercase px-4 py-1.5 rounded-full {{ $tx->status === 'uang_diterima' ? 'bg-green-100 text-green-700' : 'bg-primary-red/10 text-primary-red' }}">
                                {{ str_replace('_', ' ', $tx->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-32 text-center opacity-20">
                            <p class="text-xs font-black uppercase tracking-widest italic">Tidak ada transaksi ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
            {{ $transactions->links('livewire.custom-pagination') }}
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-[4rem] p-32 border border-gray-100 dark:border-gray-700 text-center flex flex-col items-center shadow-xl shadow-blue-900/5">
        <div class="w-32 h-32 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center mb-10 text-gray-200 dark:text-gray-700 shadow-inner">
            <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
        </div>
        <h3 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Belum Ada Catatan</h3>
        <p class="text-gray-400 font-bold text-sm mt-4 uppercase tracking-[0.3em] italic">Tidak ada aktivitas transaksi pada {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
    </div>
    @endif
</div>
