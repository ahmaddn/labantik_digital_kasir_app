<div class="p-6">
    <x-hub-tabs hub="laporan" title="Rekap Tahunan" subtitle="Performansi keuangan dan laba TEFA akumulasi per tahun." />

    <div class="flex flex-col md:flex-row md:items-center justify-end mb-8 gap-6">
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <select wire:model.live="selectedYear" class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            @if($recap)
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="px-8 py-4 bg-primary-red text-white rounded-2xl shadow-xl shadow-red-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span wire:loading.remove wire:target="exportExcel">Export XLSX</span>
                <span wire:loading wire:target="exportExcel">Mengekspor...</span>
            </button>
            @endif
        </div>
    </div>

    @if($recap)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-12">
        <div class="bg-primary-blue rounded-2xl p-12 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-64 h-64 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="text-xs font-black uppercase tracking-[0.4em] opacity-60 mb-4">Total Omzet Tahunan</h3>
            <p class="text-6xl font-black tracking-tighter" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_real, 0, ',', '.') }}</p>
            <div class="mt-12 space-y-4 border-t border-white/10 pt-8">
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40">Murni Jurusan</p>
                    <p class="text-2xl font-black" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_internal_revenue, 0, ',', '.') }}</p>
                </div>
                <div class="flex justify-between items-center opacity-40">
                    <p class="text-[10px] font-black uppercase tracking-widest">Gross Omzet</p>
                    <p class="text-xl font-black" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_all, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-64 h-64 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
            </div>
            <h3 class="text-xs font-black uppercase tracking-[0.4em] text-gray-400 mb-4">Total Keuntungan Bersih</h3>
            <p class="text-6xl font-black tracking-tighter text-primary-red" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_profit, 0, ',', '.') }}</p>
            <div class="mt-12 flex gap-10">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Modal Berjalan</p>
                    <p class="text-xl font-black text-gray-800 dark:text-white" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_modal, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Avg Profit/Mo</p>
                    <p class="text-xl font-black text-gray-800 dark:text-white" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_profit / max(1, $recap->months_count), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-10 mb-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Performa Per Kategori ({{ $selectedYear }})</h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Rekap Tahunan Berdasarkan Jenis Produk</p>
            </div>
            <div class="p-4 bg-primary-red/5 rounded-2xl text-primary-red">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
        </div>

        <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Volume</th>
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Total Modal</th>
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Keuntungan</th>
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($categoryRecap as $stats)
                    <tr class="group hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="py-4">
                            <div class="flex items-center gap-2.5">
                                <span class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $stats->name }}</span>
                                <a href="{{ route('category-detail', ['categoryId' => $stats->id, 'type' => 'yearly', 'year' => $selectedYear]) }}" wire:navigate class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white text-[10px] font-bold uppercase rounded-lg transition-all flex items-center gap-1 border border-blue-200 dark:border-blue-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                            </div>
                        </td>
                        <td class="py-4 text-center whitespace-nowrap">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $stats->qty }} <span class="text-[10px] text-gray-400 uppercase font-normal">Unit</span></span>
                        </td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 font-mono">Rp{{ number_format($stats->modal, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">Rp{{ number_format($stats->profit, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 font-mono">Rp{{ number_format($stats->revenue, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Cards View (< md) -->
        <div class="block md:hidden space-y-2.5">
            @foreach($categoryRecap as $stats)
            <div class="p-3 bg-white dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col gap-2">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $stats->name }}</span>
                    <a href="{{ route('category-detail', ['categoryId' => $stats->id, 'type' => 'yearly', 'year' => $selectedYear]) }}" wire:navigate class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase rounded-lg border border-blue-200 dark:border-blue-800 flex items-center gap-1">
                        <span>Detail</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-2 py-2 px-2.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-center">
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Volume</span>
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 mt-0.5">{{ $stats->qty }} Unit</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Keuntungan</span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">Rp{{ number_format($stats->profit, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Omzet</span>
                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">Rp{{ number_format($stats->revenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Stats Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Trend Bulanan ({{ $selectedYear }})</h2>
        </div>
        <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bulan</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Volume</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Pendapatan</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Kas Fisik</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Selisih</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Keuntungan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($monthlyBreakdown as $month)
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $month->month_name }}</span>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $month->total_volume }} unit</span>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap text-xs font-bold text-blue-600 dark:text-blue-400 font-mono">
                            Rp{{ number_format($month->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap text-xs font-semibold text-gray-800 dark:text-white font-mono">
                            @if($month->actual_cash_total > 0)
                                Rp{{ number_format($month->actual_cash_total, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400 font-normal text-[10px]">Belum Audit</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap text-xs font-bold font-mono">
                            @if($month->actual_cash_total > 0)
                                @php
                                    $diff = $month->actual_cash_total - $month->total_revenue;
                                @endphp
                                @if($diff == 0)
                                    <span class="text-emerald-600 dark:text-emerald-400 uppercase text-[10px]">Cocok</span>
                                @elseif($diff < 0)
                                    <span class="text-rose-600 dark:text-rose-400">-Rp{{ number_format(abs($diff), 0, ',', '.') }}</span>
                                @else
                                    <span class="text-amber-600 dark:text-amber-400">+Rp{{ number_format($diff, 0, ',', '.') }}</span>
                                @endif
                            @else
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                            Rp{{ number_format($month->total_profit, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('monthly-recap', ['month' => $month->month_num, 'year' => $selectedYear]) }}" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-lg transition-all inline-block">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Cards View (< md) -->
        <div class="block md:hidden p-3.5 space-y-2.5">
            @foreach($monthlyBreakdown as $month)
            <div class="p-3 bg-white dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col gap-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $month->month_name }}</span>
                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $month->total_volume }} Unit terjual</div>
                    </div>
                    <a href="{{ route('monthly-recap', ['month' => $month->month_num, 'year' => $selectedYear]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800 shrink-0">
                        Detail
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-2 py-2 px-2.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-center text-xs">
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Omzet</span>
                        <span class="font-bold font-mono text-blue-600 dark:text-blue-400 mt-0.5">Rp{{ number_format($month->total_revenue, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Profit</span>
                        <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-0.5">Rp{{ number_format($month->total_profit, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Selisih</span>
                        @if($month->actual_cash_total > 0)
                            @php
                                $diff = $month->actual_cash_total - $month->total_revenue;
                            @endphp
                            @if($diff == 0)
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold text-[10px] mt-0.5 block">Cocok</span>
                            @elseif($diff < 0)
                                <span class="text-rose-600 dark:text-rose-400 font-bold font-mono text-[10px] mt-0.5 block">-Rp{{ number_format(abs($diff), 0, ',', '.') }}</span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400 font-bold font-mono text-[10px] mt-0.5 block">+Rp{{ number_format($diff, 0, ',', '.') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-[10px] mt-0.5 block">Belum Audit</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @else
    <div class="bg-white dark:bg-gray-800 rounded-[4rem] p-32 border border-gray-100 dark:border-gray-700 text-center flex flex-col items-center shadow-xl shadow-blue-900/5">
        <div class="w-32 h-32 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center mb-10 text-gray-200 dark:text-gray-700 shadow-inner">
            <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/><path d="m9.05 9 5.64 5.64"/><circle cx="12" cy="12" r="2"/></svg>
        </div>
        <h3 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Data Belum Tersedia</h3>
        <p class="text-gray-400 font-bold text-sm mt-4 uppercase tracking-[0.3em] italic">Tidak ada rekaman transaksi untuk tahun {{ $selectedYear }}</p>
    </div>
    @endif
</div>
