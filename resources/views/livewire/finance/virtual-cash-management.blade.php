<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">Buku Kas Virtual</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Pencatatan Rekening Transfer & QRIS</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            <button wire:click="openModal" class="px-5 sm:px-5 py-2.5 sm:py-4 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center justify-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Catat Kas Virtual
            </button>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-[2rem] p-5 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-8 flex flex-col xl:flex-row xl:items-end justify-between gap-5">
        <div class="flex flex-wrap items-end gap-4 flex-1">
            <!-- Filter Source Method (Transfer / QRIS) -->
            <div class="flex flex-col flex-1 min-w-[170px]">
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 mb-1.5 ml-1">Metode Non-Cash</span>
                <div class="relative w-full">
                    <select wire:model.live="filterSourceMethod" class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider focus:ring-4 focus:ring-primary-blue/10 appearance-none">
                        <option value="">Semua (Transfer & QRIS)</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter Type Select -->
            <div class="flex flex-col flex-1 min-w-[160px]">
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 mb-1.5 ml-1">Periode Analisis</span>
                <div class="relative w-full">
                    <select wire:model.live="filterType" class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider focus:ring-4 focus:ring-primary-blue/10 appearance-none">
                        <option value="weekly">Mingguan</option>
                        <option value="monthly">Bulanan</option>
                        <option value="cumulative">Kumulatif (Semua)</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Conditional filters based on Filter Type -->
            @if($filterType !== 'cumulative')
                <div class="flex flex-col flex-1 min-w-[150px]">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 mb-1.5 ml-1">Bulan</span>
                    <input type="month" wire:model.live="filterMonth" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-black text-xs text-gray-800 dark:text-white focus:ring-4 focus:ring-primary-blue/10">
                </div>
            @endif

            @if($filterType === 'weekly')
                <div class="flex flex-col flex-1 min-w-[180px]">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 mb-1.5 ml-1">Pilih Minggu</span>
                    <div class="relative w-full">
                        <select wire:model.live="filterWeek" class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider focus:ring-4 focus:ring-primary-blue/10 appearance-none">
                            <option value="this_week">Minggu Ini (Berjalan)</option>
                            <option value="last_week">Minggu Lalu</option>
                            <option value="week_1">Minggu 1 (Tgl 1 - 7)</option>
                            <option value="week_2">Minggu 2 (Tgl 8 - 14)</option>
                            <option value="week_3">Minggu 3 (Tgl 15 - 21)</option>
                            <option value="week_4">Minggu 4 (Tgl 22 - 28)</option>
                            <option value="week_5">Minggu 5 (Tgl 29+)</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($startDate && $endDate)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40 rounded-xl px-4 py-2.5 flex items-center gap-3 shrink-0 self-stretch xl:self-auto min-w-[240px]">
                <div class="p-2 bg-primary-blue text-white rounded-lg shrink-0">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                </div>
                <div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block leading-tight">Rentang Tanggal</span>
                    <span class="text-xs font-black text-primary-blue dark:text-blue-400 uppercase leading-snug">
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-12">
        <!-- Total Saldo Virtual Card -->
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 dark:from-indigo-950 dark:to-slate-950 rounded-[3rem] p-6 md:p-7 text-white shadow-xl shadow-indigo-500/10 relative overflow-hidden group border-b-8 border-indigo-900 dark:border-indigo-500">
            <div class="absolute -right-6 -bottom-6 opacity-15 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-100 dark:text-indigo-300 mb-3">Total Saldo Virtual</h3>
            <p class="text-2xl xl:text-3xl font-black text-white {{ $totalBalance < 0 ? 'text-primary-red' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($totalBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Saldo Transfer Card -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 dark:from-blue-950 dark:to-slate-900 rounded-[3rem] p-6 md:p-7 text-white shadow-xl shadow-blue-500/10 relative overflow-hidden group border-b-8 border-blue-900 dark:border-blue-500">
            <div class="absolute -right-6 -bottom-6 opacity-15 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12"/><path d="M4 14h9"/><path d="M19 6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8l-2-2Z"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-100 dark:text-blue-300 mb-3">Saldo Bank Transfer</h3>
            <p class="text-2xl xl:text-3xl font-black text-white {{ $transferBalance < 0 ? 'text-primary-red' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($transferBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Saldo QRIS Card -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 dark:from-purple-950 dark:to-slate-900 rounded-[3rem] p-6 md:p-7 text-white shadow-xl shadow-purple-500/10 relative overflow-hidden group border-b-8 border-purple-900 dark:border-purple-500">
            <div class="absolute -right-6 -bottom-6 opacity-15 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h3v3h-3z"/><path d="M18 18h3v3h-3z"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-purple-100 dark:text-purple-300 mb-3">Saldo QRIS</h3>
            <p class="text-2xl xl:text-3xl font-black text-white {{ $qrisBalance < 0 ? 'text-purple-300' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($qrisBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 dark:from-gray-900 dark:to-slate-900 rounded-[3rem] p-6 md:p-7 text-white shadow-xl shadow-emerald-500/10 relative overflow-hidden group border-b-8 border-emerald-900 dark:border-green-500">
            <div class="absolute -right-6 -bottom-6 opacity-15 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white dark:text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-100 dark:text-green-400 mb-3">Pemasukan Virtual</h3>
            <p class="text-2xl xl:text-3xl font-black text-white dark:text-green-400" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($displayIncome, 0, ',', '.') }}</p>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-gradient-to-br from-rose-600 to-red-800 dark:from-gray-900 dark:to-slate-900 rounded-[3rem] p-6 md:p-7 text-white shadow-xl shadow-rose-500/10 relative overflow-hidden group border-b-8 border-rose-900 dark:border-primary-red">
            <div class="absolute -right-6 -bottom-6 opacity-15 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white dark:text-primary-red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-rose-100 dark:text-red-400 mb-3">Pengeluaran Virtual</h3>
            <p class="text-2xl xl:text-3xl font-black text-white dark:text-primary-red" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($displayExpense, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Category Stats Cards -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white mb-6">Ringkasan Per Kategori</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($categoryStats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="flex justify-between items-start mb-4 gap-2 relative z-10">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-800 dark:text-white">{{ $stat['name'] }}</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Pemasukan</span>
                        <span class="font-black text-green-500" :class="censorMode ? 'privacy-blur' : ''">+Rp{{ number_format($stat['income'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Pengeluaran</span>
                        <span class="font-black text-primary-red" :class="censorMode ? 'privacy-blur' : ''">-Rp{{ number_format($stat['expense'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 mt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Saldo</span>
                        <span class="text-lg font-black {{ $stat['balance'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-primary-red' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stat['balance'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-3xl p-5 text-center border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-black text-gray-400 uppercase tracking-widest italic">Belum ada transaksi kas virtual.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden mb-12">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Riwayat Transaksi Kas Virtual</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Metode</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori Kas</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Nominal</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black text-gray-800 dark:text-white uppercase">{{ $tx->date ? $tx->date->translatedFormat('d M Y') : '-' }}</span>
                        </td>
                        <td class="px-10 py-8">
                            @if($tx->source_method === 'transfer')
                                <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-500">
                                    Transfer
                                </span>
                            @else
                                <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-purple-500/10 text-purple-500">
                                    QRIS
                                </span>
                            @endif
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $tx->cashCategory->name ?? '-' }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white tracking-tight">{{ $tx->description }}</div>
                        </td>
                        <td class="px-10 py-8">
                            @if($tx->type === 'income')
                                <span class="px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest">Masuk</span>
                            @else
                                <span class="px-4 py-1.5 bg-red-100 text-red-700 rounded-full text-[9px] font-black uppercase tracking-widest">Keluar</span>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-right">
                            <span class="text-lg font-black italic {{ $tx->type === 'income' ? 'text-green-500' : 'text-primary-red' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right flex justify-end gap-2">
                            <button 
                                wire:click="editTransaction('{{ $tx->id }}')"
                                class="p-3 bg-white dark:bg-gray-700 text-primary-blue rounded-xl shadow-sm hover:scale-110 transition-transform border border-gray-100 dark:border-gray-600"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                            </button>
                            <button 
                                wire:click="confirmDelete('{{ $tx->id }}')"
                                class="p-3 bg-red-50 dark:bg-red-900/20 text-red-400 hover:text-red-600 rounded-xl hover:scale-110 transition-transform"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-10 py-32 text-center opacity-20">
                            <p class="text-xs font-black uppercase tracking-widest italic">Tidak ada catatan kas virtual pada periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
            {{ $transactions->links('livewire.partials.custom-pagination') }}
        </div>
    </div>

    <!-- Modal Form -->
    <div 
        x-data="{ show: @entangle('showModal') }" 
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
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[3rem] shadow-2xl flex flex-col max-h-[90vh] overflow-y-auto no-scrollbar animate-in zoom-in-95 duration-300"
        >
            <div class="p-10 bg-primary-blue text-white relative">
                <div class="absolute right-10 top-10">
                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <h3 class="text-2xl font-bold uppercase tracking-tight mb-1">{{ $editingId ? 'Edit Kas Virtual' : 'Catat Kas Virtual Baru' }}</h3>
            </div>

            <form wire:submit.prevent="saveTransaction" class="p-10 space-y-6">
                <!-- Tanggal -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Tanggal Transaksi</label>
                    <input type="date" wire:model="date" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-gray-800 dark:text-white">
                    @error('date') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Metode Pembayaran Virtual -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Metode Non-Cash</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer relative">
                            <input type="radio" wire:model="sourceMethod" value="transfer" class="peer sr-only">
                            <div class="text-center px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-xs tracking-widest peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-500 transition-all">
                                Bank Transfer
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" wire:model="sourceMethod" value="qris" class="peer sr-only">
                            <div class="text-center px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-xs tracking-widest peer-checked:border-purple-500 peer-checked:bg-purple-500/10 peer-checked:text-purple-500 transition-all">
                                QRIS
                            </div>
                        </label>
                    </div>
                    @error('sourceMethod') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Transaksi -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer relative">
                            <input type="radio" wire:model="type" value="income" class="peer sr-only">
                            <div class="text-center px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-xs tracking-widest peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-green-500 transition-all">
                                Pemasukan
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" wire:model="type" value="expense" class="peer sr-only">
                            <div class="text-center px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-xs tracking-widest peer-checked:border-primary-red peer-checked:bg-primary-red/10 peer-checked:text-primary-red transition-all">
                                Pengeluaran
                            </div>
                        </label>
                    </div>
                    @error('type') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori Kas -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Kategori Kas</label>
                    <select wire:model="cashCategoryId" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-gray-800 dark:text-white appearance-none">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('cashCategoryId') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Nominal (Rp)</label>
                    <input type="number" wire:model="amount" placeholder="0" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-gray-800 dark:text-white">
                    @error('amount') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Keterangan</label>
                    <textarea wire:model="description" rows="3" placeholder="Deskripsi transaksi..." class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white"></textarea>
                    @error('description') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="show = false" class="flex-1 py-4 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-2xl font-black uppercase text-xs tracking-widest hover:text-gray-600 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-primary-blue text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-blue-500/30 hover:scale-105 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
        x-data="{ show: @entangle('showDeleteConfirmation') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[350] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
    >
        <div class="bg-white dark:bg-gray-900 w-full max-w-sm rounded-[3rem] shadow-2xl p-8 text-center space-y-6">
            <div class="w-16 h-16 bg-red-500/10 text-primary-red rounded-2xl flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold uppercase text-gray-800 dark:text-white">Konfirmasi Hapus</h3>
                <p class="text-xs text-gray-400 font-bold mt-2">Apakah Anda yakin ingin menghapus catatan kas virtual ini?</p>
            </div>
            <div class="flex gap-4">
                <button wire:click="$set('showDeleteConfirmation', false)" class="flex-1 py-3 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-xl font-black uppercase text-xs tracking-widest">Batal</button>
                <button wire:click="deleteTransaction" class="flex-1 py-3 bg-primary-red text-white rounded-xl font-black uppercase text-xs tracking-widest shadow-lg shadow-red-500/30">Hapus</button>
            </div>
        </div>
    </div>
</div>
