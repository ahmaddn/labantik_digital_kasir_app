<div class="py-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-white">
                Hutang & Kembalian
            </h2>
            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mt-1">
                Monitoring Piutang Pembeli & Hutang Kembalian
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-white dark:bg-gray-800 p-1 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex">
                <button 
                    wire:click="setTab('debt')"
                    class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'debt' ? 'bg-primary-blue text-white shadow-lg shadow-blue-900/20' : 'text-gray-400 hover:text-primary-blue' }}"
                >
                    Hutang Pembeli
                </button>
                <button 
                    wire:click="setTab('change')"
                    class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'change' ? 'bg-primary-red text-white shadow-lg shadow-red-900/20' : 'text-gray-400 hover:text-primary-red' }}"
                >
                    Belum Kembalian
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-20 h-20 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-2">Total Piutang (Hutang Pembeli)</p>
            <h3 class="text-4xl font-black italic text-primary-blue dark:text-white tracking-tighter">
                Rp{{ number_format($summary['total_debt'], 0, ',', '.') }}
            </h3>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-20 h-20 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-2">Total Belum Kembalian</p>
            <h3 class="text-4xl font-black italic text-primary-red dark:text-white tracking-tighter">
                Rp{{ number_format($summary['total_change'], 0, ',', '.') }}
            </h3>
            <p class="text-[9px] text-gray-400 mt-2 italic font-medium">*Berdasarkan total harga transaksi yang berstatus belum kembalian</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-2">Cari Nama Pembeli/Jurusan</label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live="search"
                        placeholder="Ketik nama untuk memfilter..."
                        class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl py-4 pl-12 pr-6 text-sm font-bold focus:ring-2 focus:ring-primary-blue transition-all"
                    >
                    <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-2">Mulai Dari</label>
                <input 
                    type="date" 
                    wire:model.live="startDate"
                    class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl py-4 px-6 text-sm font-bold focus:ring-2 focus:ring-primary-blue transition-all"
                >
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-2">Sampai Dengan</label>
                <input 
                    type="date" 
                    wire:model.live="endDate"
                    class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl py-4 px-6 text-sm font-bold focus:ring-2 focus:ring-primary-blue transition-all"
                >
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-gray-800">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Tanggal & Waktu</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Pembeli / Jurusan</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Produk</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Total</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Catatan</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $trx->transacted_at->format('d/m/Y') }}</span>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $trx->transacted_at->format('H:i') }} WIB</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-primary-blue font-black text-xs mr-4">
                                        {{ substr($trx->buyer_name ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-black italic uppercase tracking-tighter text-gray-700 dark:text-gray-300">
                                        {{ $trx->buyer_name ?? 'Anonim' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $trx->product->name }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $trx->quantity }} Unit × Rp{{ number_format($trx->unit_price, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-base font-black italic {{ $activeTab === 'change' ? 'text-primary-red' : 'text-primary-blue' }} dark:text-white tracking-tighter">
                                    Rp{{ number_format($activeTab === 'change' ? $trx->change_due : $trx->debt_amount, 0, ',', '.') }}
                                </span>
                                @if($activeTab === 'change')
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total Belanja: Rp{{ number_format($trx->total_price, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <div class="max-w-xs">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 italic">
                                        {{ $trx->note ?: '-' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <button 
                                    wire:click="settle({{ $trx->id }})"
                                    wire:confirm="Selesaikan transaksi ini?"
                                    class="flex items-center px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95"
                                >
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    Selesaikan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-300 mb-6">
                                        <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                                    </div>
                                    <h4 class="text-lg font-black italic uppercase tracking-tighter text-gray-400">Tidak Ada Data</h4>
                                    <p class="text-xs font-bold text-gray-300 uppercase tracking-widest mt-1">Semua transaksi telah diselesaikan!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 dark:border-gray-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
