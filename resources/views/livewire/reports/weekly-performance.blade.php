<div class="p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen text-gray-900 dark:text-gray-100">
    {{-- Header & Week Selection --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700/60">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-primary-blue/10 text-primary-blue dark:text-blue-400 rounded-xl sm:rounded-2xl shrink-0 border border-primary-blue/20">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-gray-900 dark:text-white">Performa Penjualan & Kasir Mingguan</h1>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">Laporan evaluasi omset toko, audit tugas piket harian, & stok barang</p>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-3">
            {{-- Quick Presets --}}
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/60 p-1 rounded-xl border border-gray-200 dark:border-gray-600 shrink-0">
                <button wire:click="setPresetRange('this_week')" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-600 shadow-xs">
                    Minggu Ini
                </button>
                <button wire:click="setPresetRange('last_week')" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-600 shadow-xs">
                    Minggu Lalu
                </button>
                <button wire:click="setPresetRange('this_month')" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-600 shadow-xs">
                    Bulan Ini
                </button>
            </div>

            {{-- Date Range Picker Inputs --}}
            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-1.5 shadow-xs">
                <input type="date" wire:model.live="startDate" class="bg-transparent text-xs font-bold text-gray-900 dark:text-white focus:outline-none cursor-pointer">
                <span class="text-xs font-semibold text-gray-400">s/d</span>
                <input type="date" wire:model.live="endDate" class="bg-transparent text-xs font-bold text-gray-900 dark:text-white focus:outline-none cursor-pointer">
            </div>

            {{-- Navigate to Presentation Page Button --}}
            <a href="{{ route('weekly-performance.presentation', ['startDate' => $startDate, 'endDate' => $endDate]) }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-blue hover:bg-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/15 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12H4z" />
                </svg>
                <span class="whitespace-nowrap">Mode Presentasi (Halaman Penuh)</span>
            </a>
        </div>
    </div>

    {{-- REGULAR DASHBOARD VIEW (DARK & LIGHT MODE STYLED) --}}

    {{-- SECTION 1: EXECUTIVE KEY METRICS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Omset --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Omset</span>
                <div class="p-2 bg-primary-blue/10 text-primary-blue dark:text-blue-400 rounded-lg border border-primary-blue/20">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <div class="mt-2 text-xs font-semibold flex items-center justify-between gap-1">
                <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }}
                </span>
                <span class="text-gray-500 dark:text-gray-400 font-normal">vs Periode Lalu (Rp {{ number_format($prevRevenue) }})</span>
            </div>
        </div>

        {{-- Card 2: Net Profit --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Net Profit Toko</span>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-200 dark:border-emerald-900/50">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            <div class="mt-2 text-xs font-semibold flex items-center justify-between gap-1">
                <span class="{{ $profitGrowth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $profitGrowth >= 0 ? '+'.$profitGrowth.'%' : $profitGrowth.'%' }}
                </span>
                <span class="text-gray-500 dark:text-gray-400 font-normal">vs Periode Lalu (Rp {{ number_format($prevProfit) }})</span>
            </div>
        </div>

        {{-- Card 3: Transaksi & Basket --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Transaksi</span>
                <div class="p-2 bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 rounded-lg border border-purple-200 dark:border-purple-900/50">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalTransactions) }} <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Struk</span></h3>
            <div class="mt-2 text-xs font-semibold flex items-center justify-between gap-1">
                <span class="{{ $txGrowth >= 0 ? 'text-purple-600 dark:text-purple-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $txGrowth >= 0 ? '+'.$txGrowth.'%' : $txGrowth.'%' }}
                </span>
                <span class="text-gray-500 dark:text-gray-400 font-normal">vs Periode Lalu ({{ number_format($prevTxCount) }} Tx)</span>
            </div>
        </div>

        {{-- Card 4: Kehadiran Piket & Kepatuhan --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kepatuhan Piket</span>
                <div class="p-2 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-900/50">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $shiftFulfillmentRate }}%</h3>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                Tugas: <strong class="text-emerald-600 dark:text-emerald-400">{{ $taskApprovedRate }}%</strong> Disetujui
            </div>
        </div>
    </div>

    {{-- SECTION 2: HIGH CONTRAST VISIBLE CHART HARIAN --}}
    <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white">Tren Penjualan Harian (Senin - Minggu)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian omset penjualan dan jumlah transaksi harian sepanjang minggu</p>
            </div>
            <div class="self-start sm:self-auto px-4 py-2 bg-primary-blue/10 text-primary-blue dark:text-blue-400 border border-primary-blue/20 rounded-xl text-xs font-bold">
                Puncak Omset: {{ $peakDay['day'] }} (Rp {{ number_format($peakDay['revenue']) }})
            </div>
        </div>

        {{-- AUTHENTIC PROFESSIONAL VERTICAL BAR CHART --}}
        <div class="pt-6 pb-2 border-b border-gray-100 dark:border-gray-700/60">
            <div class="relative h-64 flex items-end">
                {{-- Y-Axis Background Grid Lines --}}
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-12">
                    <div class="w-full border-b border-dashed border-gray-200 dark:border-gray-700/60 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-1">Rp {{ number_format($maxDailyRevenue / 1000, 0) }}k</span>
                    </div>
                    <div class="w-full border-b border-dashed border-gray-200 dark:border-gray-700/60 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-1">Rp {{ number_format(($maxDailyRevenue * 0.5) / 1000, 0) }}k</span>
                    </div>
                    <div class="w-full border-b border-gray-300 dark:border-gray-600 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-1">Rp 0</span>
                    </div>
                </div>

                {{-- Chart Columns --}}
                <div class="relative z-10 w-full flex items-end justify-around pl-14 pr-4 h-full pb-12">
                    @foreach($dailySales as $day)
                        @php 
                            $barPct = ($maxDailyRevenue > 0 && $day['revenue'] > 0) ? max(4, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 0;
                            $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0);
                        @endphp
                        <div class="flex-1 flex flex-col items-center justify-end h-full group max-w-[70px]">
                            {{-- Value Label Above Column --}}
                            <div class="mb-1 text-center transition-transform group-hover:-translate-y-1">
                                <span class="text-[11px] font-black block whitespace-nowrap {{ $isPeak ? 'text-blue-600 dark:text-blue-400' : ($day['revenue'] > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600') }}">
                                    {{ $day['revenue'] > 0 ? 'Rp ' . number_format($day['revenue'] / 1000, 0) . 'k' : 'Rp 0' }}
                                </span>
                            </div>

                            {{-- The Actual Vertical Bar Column --}}
                            <div class="w-8 sm:w-11 flex items-end justify-center h-full">
                                @if($barPct > 0)
                                    <div class="w-full rounded-t-md transition-all duration-500 {{ $isPeak ? 'bg-primary-blue dark:bg-blue-500 shadow-md shadow-blue-500/30 ring-2 ring-blue-400/30' : 'bg-slate-400 dark:bg-slate-600 hover:bg-primary-blue dark:hover:bg-blue-400' }}" 
                                         style="height: {{ $barPct }}%;"
                                         title="{{ $day['day_name'] }}: Rp {{ number_format($day['revenue']) }} ({{ $day['transactions'] }} Tx)">
                                    </div>
                                @else
                                    <div class="w-full h-1 bg-gray-200 dark:bg-gray-700 rounded-t-sm" title="Tidak ada penjualan"></div>
                                @endif
                            </div>

                            {{-- X-Axis Day & Transaction --}}
                            <div class="absolute -bottom-11 text-center">
                                <span class="text-xs font-black block {{ $isPeak ? 'text-blue-600 dark:text-blue-400' : 'text-gray-800 dark:text-gray-200' }}">
                                    {{ $day['day_name'] }}
                                </span>
                                <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 block">
                                    {{ $day['transactions'] }} Tx
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="h-4"></div>
        </div>
    </div>
</div>
