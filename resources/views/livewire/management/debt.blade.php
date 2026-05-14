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
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Nota / Referensi</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Pembeli / Jurusan</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Ringkasan</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Total Tagihan</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-primary-blue uppercase italic tracking-tighter">{{ $trx->reference }}</span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase mt-1">{{ $trx->transacted_at->format('d/m/Y H:i') }}</span>
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
                                    <span class="text-xs font-bold text-gray-800 dark:text-white">{{ $trx->items_count }} Jenis Produk</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Klik Detail untuk rincian</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-base font-black italic {{ $activeTab === 'change' ? 'text-primary-red' : 'text-primary-blue' }} dark:text-white tracking-tighter">
                                    Rp{{ number_format($activeTab === 'change' ? $trx->change_due : $trx->debt_amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-[9px] font-black uppercase text-gray-400 rounded-full tracking-widest border border-gray-200 dark:border-gray-700">
                                    {{ str_replace('_', ' ', $trx->status) }}
                                </span>
                            </td>
                             <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <button 
                                        wire:click="viewDetails('{{ $trx->reference }}')"
                                        wire:loading.attr="disabled"
                                        class="flex items-center px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95 disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="viewDetails('{{ $trx->reference }}')">Detail</span>
                                        <span wire:loading wire:target="viewDetails('{{ $trx->reference }}')" class="animate-spin">
                                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                        </span>
                                    </button>
                                    <button 
                                        wire:click="openSettleModal('{{ $trx->reference }}')"
                                        class="flex items-center px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95"
                                    >
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                        Lunaskan
                                    </button>
                                </div>
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

    <!-- Settle Modal -->
    <div 
        x-data="{ 
            show: @entangle('showSettleModal'),
            settleMethod: @entangle('settleMethod')
        }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col relative max-h-[90vh]"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        >
            <!-- Close Button -->
            <button @click="show = false" class="absolute top-10 right-10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors z-20">
                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>

            <div class="p-12 overflow-y-auto custom-scrollbar">
                <div class="mb-10">
                    <h3 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-tight">
                        Pelunasan Nota
                    </h3>
                    <div class="flex items-center gap-2 mt-2">
                        <p class="text-[11px] font-black text-primary-blue uppercase tracking-widest bg-blue-500/5 px-4 py-1.5 rounded-full inline-block">{{ $selectedReference }}</p>
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 dark:bg-gray-800 px-4 py-1.5 rounded-full inline-block">Pembeli: {{ $currentBuyerName ?? 'Anonim' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <!-- Left Column: Info & Amount -->
                    <div class="space-y-8">
                        <!-- Summary Info -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Total Sisa Tagihan</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-xs font-black text-gray-400 italic">Rp</span>
                                <span class="text-5xl font-black italic text-gray-800 dark:text-white tracking-tighter">
                                    {{ number_format($maxAmount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Input Amount -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Nominal Pelunasan</label>
                            <div class="relative group">
                                <span class="absolute left-8 inset-y-0 flex items-center text-sm font-black text-gray-300 italic group-focus-within:text-primary-blue transition-colors">Rp</span>
                                <input 
                                    type="number" 
                                    wire:model.live="settleAmount"
                                    max="{{ $maxAmount }}"
                                    class="w-full pl-16 pr-8 py-6 bg-gray-50 dark:bg-gray-800 border-none rounded-[2rem] focus:ring-8 focus:ring-primary-blue/5 font-black text-2xl text-primary-blue italic transition-all shadow-inner"
                                >
                            </div>
                            @error('settleAmount') <span class="text-[9px] text-primary-red font-bold uppercase ml-4 tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <!-- Method Selection -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Metode Pelunasan</label>
                            <div class="grid grid-cols-1 gap-4">
                                <button 
                                    @click="settleMethod = 'dibayarkan'"
                                    :class="settleMethod === 'dibayarkan' ? 'bg-primary-blue text-white shadow-2xl shadow-blue-500/20 scale-[1.02]' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="flex items-center justify-between p-6 rounded-[2rem] transition-all group"
                                >
                                    <div class="flex items-center gap-5">
                                        <div :class="settleMethod === 'dibayarkan' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-900'" class="w-12 h-12 rounded-2xl flex items-center justify-center">
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[11px] font-black uppercase tracking-widest">Dibayarkan</p>
                                            <p class="text-[9px] font-bold opacity-60 uppercase">Penyelesaian secara tunai</p>
                                        </div>
                                    </div>
                                    <div x-show="settleMethod === 'dibayarkan'" class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-primary-blue shadow-sm animate-in zoom-in">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    </div>
                                </button>

                                <button 
                                    @click="settleMethod = 'dicicil'"
                                    :class="settleMethod === 'dicicil' ? 'bg-amber-500 text-white shadow-2xl shadow-amber-500/20 scale-[1.02]' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="flex items-center justify-between p-6 rounded-[2rem] transition-all group"
                                >
                                    <div class="flex items-center gap-5">
                                        <div :class="settleMethod === 'dicicil' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-900'" class="w-12 h-12 rounded-2xl flex items-center justify-center">
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/></svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[11px] font-black uppercase tracking-widest">Dicicil</p>
                                            <p class="text-[9px] font-bold opacity-60 uppercase">Pembayaran sebagian</p>
                                        </div>
                                    </div>
                                    <div x-show="settleMethod === 'dicicil'" class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-amber-500 shadow-sm animate-in zoom-in">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Dijajankan Details -->
                    <div class="space-y-6">
                        @if($activeTab === 'change')
                            <div class="space-y-4">
                                <button 
                                    @click="settleMethod = 'dijajankan'"
                                    :class="settleMethod === 'dijajankan' ? 'bg-primary-red text-white shadow-2xl shadow-red-500/20 scale-[1.02]' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="flex items-center justify-between p-6 rounded-[2rem] transition-all group w-full"
                                >
                                    <div class="flex items-center gap-5">
                                        <div :class="settleMethod === 'dijajankan' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-900'" class="w-12 h-12 rounded-2xl flex items-center justify-center">
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.56-7.43H5.12"/></svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[11px] font-black uppercase tracking-widest">Dijajankan</p>
                                            <p class="text-[9px] font-bold opacity-60 uppercase">Ditukar dengan produk lain</p>
                                        </div>
                                    </div>
                                    <div x-show="settleMethod === 'dijajankan'" class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-primary-red shadow-sm animate-in zoom-in">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    </div>
                                </button>

                                <!-- Spent Items Selection -->
                                <div x-show="settleMethod === 'dijajankan'" class="p-8 bg-gray-50 dark:bg-gray-800/50 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 animate-in slide-in-from-top-6 duration-500">
                                    <div class="space-y-6">
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                wire:model.live="productSearch"
                                                placeholder="Cari produk..."
                                                class="w-full pl-14 pr-6 py-5 bg-white dark:bg-gray-900 border-none rounded-[1.5rem] text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-primary-red/10 shadow-sm"
                                            >
                                            <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                            
                                            <!-- Search Results -->
                                            @if(count($this->searchResults) > 0)
                                                <div class="absolute left-0 right-0 top-full mt-4 bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 z-[60] overflow-hidden">
                                                    @foreach($this->searchResults as $res)
                                                        <button 
                                                            wire:click="addSpentItem({{ $res->id }})"
                                                            class="w-full p-5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-50 dark:border-gray-800 last:border-none"
                                                        >
                                                            <div class="text-left">
                                                                <p class="text-[11px] font-black uppercase tracking-tight">{{ $res->name }}</p>
                                                                <p class="text-[10px] font-bold text-primary-red italic mt-1">Rp{{ number_format($res->price, 0, ',', '.') }}</p>
                                                            </div>
                                                            <div class="w-8 h-8 rounded-full bg-red-50 text-primary-red flex items-center justify-center">
                                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Cart List -->
                                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                            @forelse($spentItems as $index => $item)
                                                <div class="flex items-center justify-between p-5 bg-white dark:bg-gray-900 rounded-2xl border border-gray-50 dark:border-gray-800 group shadow-sm">
                                                    <div class="flex-1">
                                                        <p class="text-[10px] font-black uppercase tracking-tight text-gray-700 dark:text-white">{{ $item['name'] }}</p>
                                                        <div class="flex items-center gap-3 mt-2">
                                                            <input 
                                                                type="number" 
                                                                wire:change="updateSpentItemQty({{ $index }}, $event.target.value)"
                                                                value="{{ $item['quantity'] }}"
                                                                class="w-14 p-1.5 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-center text-[10px] font-black focus:ring-4 focus:ring-primary-red/5"
                                                            >
                                                            <span class="text-[9px] font-bold text-gray-400">x Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-4">
                                                        <span class="text-[11px] font-black italic text-primary-red">Rp{{ number_format($item['total'], 0, ',', '.') }}</span>
                                                        <button wire:click="removeSpentItem({{ $index }})" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 shadow-sm">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="py-10 text-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl">
                                                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Belum ada produk</p>
                                                </div>
                                            @endforelse
                                        </div>
                                        
                                        <div class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-800">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Dijajankan</span>
                                            <span class="text-xl font-black italic text-primary-red tracking-tight">Rp{{ number_format($totalSpent, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="h-full flex flex-col items-center justify-center p-12 bg-gray-50 dark:bg-gray-800/50 rounded-[3rem] border border-dashed border-gray-200 dark:border-gray-700 opacity-60">
                                <svg class="w-16 h-16 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Pilih metode pelunasan di kolom kiri untuk melanjutkan</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-50 dark:border-gray-800">
                    <button 
                        wire:click="settle"
                        class="w-full py-6 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-[2.5rem] shadow-2xl font-black italic uppercase tracking-[0.4em] text-sm hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group"
                    >
                        <span>Proses Pelunasan</span>
                        <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                    @error('settleMethod') <p class="text-center text-[9px] text-primary-red font-bold uppercase mt-4 tracking-widest">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div 
        x-data="{ show: @entangle('showDetailsModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[500] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col relative max-h-[85vh]"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        >
            <button @click="show = false" class="absolute top-8 right-8 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors z-10">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>

            <div class="p-10 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-tight">
                    Rincian Transaksi
                </h3>
                <p class="text-[10px] font-black text-primary-blue uppercase tracking-widest mt-1">{{ $detailReference }}</p>
            </div>

            <div class="flex-1 overflow-y-auto p-10 space-y-4 scrollbar-hide">
                @if($this->detailItems)
                    @foreach($this->detailItems as $item)
                        <div class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white dark:bg-gray-900 rounded-xl flex items-center justify-center text-primary-blue font-black text-xs shadow-sm">
                                    {{ $item->quantity }}x
                                </div>
                                <div>
                                    <h4 class="text-sm font-black uppercase tracking-tight text-gray-800 dark:text-white">{{ $item->product->name }}</h4>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                        Rp{{ number_format($item->unit_price, 0, ',', '.') }} / Unit
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black italic text-gray-800 dark:text-white">
                                    Rp{{ number_format($item->total_price, 0, ',', '.') }}
                                </p>
                                @if($item->debt_amount > 0)
                                    <span class="text-[8px] font-black text-primary-red uppercase tracking-widest bg-red-500/5 px-2 py-1 rounded-lg">Hutang: Rp{{ number_format($item->debt_amount, 0, ',', '.') }}</span>
                                @endif
                                @if($item->change_due > 0)
                                    <span class="text-[8px] font-black text-primary-blue uppercase tracking-widest bg-blue-500/5 px-2 py-1 rounded-lg">Kembalian: Rp{{ number_format($item->change_due, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="p-10 bg-gray-50 dark:bg-gray-800/30 border-t border-gray-50 dark:border-gray-800 flex justify-between items-center">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Transaksi</span>
                    <span class="text-xl font-black italic text-gray-800 dark:text-white">Rp{{ number_format($this->detailItems->sum('total_price'), 0, ',', '.') }}</span>
                </div>
                <button @click="show = false" class="px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all hover:scale-105 active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
