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
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">Laporan evaluasi omset toko, audit kinerja kasir, rotasi produk & catatan piket</p>
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
        </div>
    </div>

    {{-- INTERACTIVE FORM WIZARD STEPPER BAR --}}
    <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
            {{-- Step 1 Tab --}}
            <button wire:click="setStep(1)" 
                    class="flex items-center gap-3 p-3 rounded-xl transition-all text-left {{ $currentStep === 1 ? 'bg-blue-50 dark:bg-blue-950/70 border-2 border-blue-500 shadow-xs' : 'bg-gray-50 dark:bg-gray-700/40 border border-transparent hover:border-gray-200 dark:hover:border-gray-600' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 1 ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    1
                </div>
                <div class="min-w-0">
                    <span class="block text-xs font-black uppercase tracking-wider {{ $currentStep === 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">Tahap 1</span>
                    <span class="block text-xs font-bold text-gray-900 dark:text-white truncate">Omset & Tren Harian</span>
                </div>
            </button>

            {{-- Step 2 Tab --}}
            <button wire:click="setStep(2)" 
                    class="flex items-center gap-3 p-3 rounded-xl transition-all text-left {{ $currentStep === 2 ? 'bg-blue-50 dark:bg-blue-950/70 border-2 border-blue-500 shadow-xs' : 'bg-gray-50 dark:bg-gray-700/40 border border-transparent hover:border-gray-200 dark:hover:border-gray-600' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 2 ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    2
                </div>
                <div class="min-w-0">
                    <span class="block text-xs font-black uppercase tracking-wider {{ $currentStep === 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">Tahap 2</span>
                    <span class="block text-xs font-bold text-gray-900 dark:text-white truncate">Kinerja & Kasir</span>
                </div>
            </button>

            {{-- Step 3 Tab --}}
            <button wire:click="setStep(3)" 
                    class="flex items-center gap-3 p-3 rounded-xl transition-all text-left {{ $currentStep === 3 ? 'bg-blue-50 dark:bg-blue-950/70 border-2 border-blue-500 shadow-xs' : 'bg-gray-50 dark:bg-gray-700/40 border border-transparent hover:border-gray-200 dark:hover:border-gray-600' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 3 ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    3
                </div>
                <div class="min-w-0">
                    <span class="block text-xs font-black uppercase tracking-wider {{ $currentStep === 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">Tahap 3</span>
                    <span class="block text-xs font-bold text-gray-900 dark:text-white truncate">Produk & Stok</span>
                </div>
            </button>

            {{-- Step 4 Tab --}}
            <button wire:click="setStep(4)" 
                    class="flex items-center gap-3 p-3 rounded-xl transition-all text-left {{ $currentStep === 4 ? 'bg-blue-50 dark:bg-blue-950/70 border-2 border-blue-500 shadow-xs' : 'bg-gray-50 dark:bg-gray-700/40 border border-transparent hover:border-gray-200 dark:hover:border-gray-600' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 4 ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    4
                </div>
                <div class="min-w-0">
                    <span class="block text-xs font-black uppercase tracking-wider {{ $currentStep === 4 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">Tahap 4</span>
                    <span class="block text-xs font-bold text-gray-900 dark:text-white truncate">Audit Piket & Catatan</span>
                </div>
            </button>
        </div>
    </div>

    {{-- WIZARD CONTENT CONTAINER --}}
    <div class="space-y-6">

        {{-- ==================== STEP 1: RINGKASAN OMSET & TREN HARIAN ==================== --}}
        @if($currentStep === 1)
            {{-- KPI Executive Cards --}}
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

                {{-- Card 2: Keuntungan Bersih --}}
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

            {{-- Daily Sales Bar Chart --}}
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

                {{-- Authentic Vertical Bar Chart --}}
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
                        <div class="relative z-10 w-full flex items-end justify-around pl-14 pr-4 h-full pb-10">
                            @foreach($dailySales as $day)
                                @php 
                                    $barPct = ($maxDailyRevenue > 0 && $day['revenue'] > 0) ? max(6, min(100, round(($day['revenue'] / $maxDailyRevenue) * 100))) : 0;
                                    $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0 && $day['revenue'] == $peakDay['revenue']);
                                @endphp
                                <div class="flex-1 flex flex-col items-center justify-end group max-w-[75px] relative">
                                    {{-- Value Label Above Column --}}
                                    <div class="mb-1 text-center transition-transform group-hover:-translate-y-1">
                                        <span class="text-[11px] font-black block whitespace-nowrap {{ $isPeak ? 'text-blue-600 dark:text-blue-400' : ($day['revenue'] > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600') }}">
                                            {{ $day['revenue'] > 0 ? 'Rp ' . number_format($day['revenue'] / 1000, 0) . 'k' : 'Rp 0' }}
                                        </span>
                                    </div>

                                    {{-- The Actual Vertical Bar Column Track (Fixed explicit height h-44 for reliable CSS percentage computation) --}}
                                    <div class="w-8 sm:w-11 h-44 flex items-end justify-center">
                                        @if($barPct > 0)
                                            <div class="w-full rounded-t-md transition-all duration-500 {{ $isPeak ? 'bg-blue-600 dark:bg-blue-500 shadow-lg shadow-blue-500/50 ring-2 ring-blue-400' : 'bg-blue-500 dark:bg-blue-600 hover:bg-blue-600 hover:dark:bg-blue-500 shadow-sm' }}" 
                                                 style="height: {{ $barPct }}%; min-height: 8px;"
                                                 title="{{ $day['day_name'] }} ({{ $day['date'] }}): Rp {{ number_format($day['revenue']) }} ({{ $day['transactions'] }} Tx)">
                                            </div>
                                        @else
                                            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-t-sm" title="Tidak ada penjualan"></div>
                                        @endif
                                    </div>

                                    {{-- X-Axis Day & Transaction --}}
                                    <div class="mt-2 text-center">
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
                    <div class="h-2"></div>
                </div>
            </div>

        {{-- ==================== STEP 2: KINERJA KASIR & EVALUASI ==================== --}}
        @elseif($currentStep === 2)
            <div class="space-y-6">
                {{-- Top 3 and Bottom Performers Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Top 3 Kasir Berkinerja Terbaik --}}
                    <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                            <div>
                                <h3 class="text-base font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                    <span>Top 3 Kasir Terbaik (Bintang Piket)</span>
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Penilaian berdasarkan kedisiplinan jadwal, kepatuhan tugas, dan omset POS</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @forelse($topPerformers as $idx => $item)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600/60">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-black text-sm flex items-center justify-center border border-emerald-300 dark:border-emerald-800">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->user->name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item->total_tx }} Transaksi | Omset Rp {{ number_format($item->total_sales) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-black rounded-lg">
                                            {{ $item->overall_score }} Pts
                                        </span>
                                        <button wire:click="viewCashierDetail({{ $item->user->id }})" class="block text-[11px] text-blue-600 dark:text-blue-400 font-semibold hover:underline mt-1">
                                            Lihat Log
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 dark:text-gray-400 py-6 text-center">Belum ada data kinerja kasir.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Kasir Perlu Evaluasi & Catatan Tugas --}}
                    <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                            <div>
                                <h3 class="text-base font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider">
                                    Kasir Perlu Evaluasi & Catatan Tugas
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Memiliki absensi terlewat, tugas belum selesai, atau omset minim</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @forelse($bottomPerformers as $item)
                                <div class="p-4 bg-rose-50/50 dark:bg-rose-950/20 rounded-xl border border-rose-100 dark:border-rose-900/40 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->user->name }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold rounded-lg">
                                                Skor: {{ $item->overall_score }}
                                            </span>
                                            <button wire:click="viewCashierDetail({{ $item->user->id }})" class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                                                Detail
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-rose-700 dark:text-rose-300 font-medium leading-relaxed">{{ $item->evaluation_notes }}</p>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 pt-1.5 flex justify-between border-t border-rose-100 dark:border-rose-900/40">
                                        <span>Omset POS: Rp {{ number_format($item->total_sales) }}</span>
                                        <span>{{ $item->total_tx }} Transaksi</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">Luar biasa! Semua kasir berkinerja baik & disiplin tanpa catatan evaluasi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Tabel Lengkap Semua Kinerja Kasir --}}
                <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                    <h3 class="text-base font-black text-gray-900 dark:text-white">Daftar Lengkap Audit Kinerja Seluruh Kasir</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase font-black border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="p-3">Nama Kasir</th>
                                    <th class="p-3 text-center">Jadwal Shift</th>
                                    <th class="p-3 text-center">Kehadiran</th>
                                    <th class="p-3 text-center">Tugas Selesai</th>
                                    <th class="p-3 text-right">Omset Kasir</th>
                                    <th class="p-3 text-center">Transaksi</th>
                                    <th class="p-3 text-center">Skor</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                @forelse($cashierPerformanceList as $c)
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-all">
                                        <td class="p-3 font-bold text-gray-900 dark:text-white">{{ $c->user->name }}</td>
                                        <td class="p-3 text-center">{{ $c->scheduled_shifts }} Kali</td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 rounded font-bold {{ $c->attendance_rate >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                                {{ $c->attended_shifts }} ({{ $c->attendance_rate }}%)
                                            </span>
                                        </td>
                                        <td class="p-3 text-center font-semibold">
                                            {{ $c->approved_tasks }} / {{ $c->total_assigned_tasks }}
                                        </td>
                                        <td class="p-3 text-right font-black text-gray-900 dark:text-white">Rp {{ number_format($c->total_sales) }}</td>
                                        <td class="p-3 text-center font-bold">{{ $c->total_tx }} Tx</td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 rounded font-black {{ $c->overall_score >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($c->overall_score >= 60 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}">
                                                {{ $c->overall_score }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <button wire:click="viewCashierDetail({{ $c->user->id }})" class="px-3 py-1 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white rounded-lg font-bold transition-all">
                                                Audit Log
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-6 text-center text-gray-500 dark:text-gray-400">Tidak ada kasir terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        {{-- ==================== STEP 3: ANALISIS PRODUK & STOK ==================== --}}
        @elseif($currentStep === 3)
            <div class="space-y-6">
                {{-- Product Comparison Chart --}}
                <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <div>
                            <h3 class="text-base font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Grafik Perbandingan Produk Terlaris (Periode Ini vs Periode Lalu)</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Menganalisis pergerakan volume penjualan item internal TEFA & barang supplier reguler</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-2">
                        @foreach($topSellingProducts as $p)
                            @php 
                                $maxQty = max(1, max($p->qty_sold, $p->prev_qty));
                                $currWidth = round(($p->qty_sold / $maxQty) * 100);
                                $prevWidth = round(($p->prev_qty / $maxQty) * 100);
                            @endphp
                            <div class="space-y-2 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600/60">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $p->product->name }}</span>
                                        <span class="px-2 py-0.5 {{ $p->is_tefa_internal ? 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800' : 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950 dark:text-purple-300 dark:border-purple-800' }} border rounded text-[10px] font-bold">
                                            {{ $p->is_tefa_internal ? 'TEFA Internal' : 'Supplier' }}
                                        </span>
                                    </div>
                                    <span class="font-bold {{ $p->qty_growth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} text-sm">
                                        {{ $p->qty_sold }} pcs ({{ $p->qty_growth >= 0 ? '+'.$p->qty_growth.'%' : $p->qty_growth.'%' }} vs {{ $p->prev_qty }} pcs lalu)
                                    </span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="w-24 text-gray-600 dark:text-gray-300 font-semibold shrink-0">Ini: {{ $p->qty_sold }} pcs</span>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden p-0.5">
                                            <div class="bg-blue-500 h-full rounded-full transition-all" style="width: {{ max(6, $currWidth) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="w-24 text-gray-400 dark:text-gray-400 shrink-0">Lalu: {{ $p->prev_qty }} pcs</span>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700/60 rounded-full h-2.5 overflow-hidden p-0.5">
                                            <div class="bg-gray-400 dark:bg-gray-500 h-full rounded-full transition-all" style="width: {{ max(4, $prevWidth) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 10 Slow-Moving / Stagnant Products --}}
                <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <div>
                            <h3 class="text-base font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider">10 Produk Stagnan / Slow-Moving</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perlu perhatian rotasi stok atau program bundling promo agar tidak menumpuk</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($leastSellingProducts as $p)
                            <div class="p-3.5 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600/60 text-xs flex flex-col justify-between space-y-2">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white block truncate" title="{{ $p->product->name }}">{{ $p->product->name }}</span>
                                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold">Sisa: {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</span>
                                </div>
                                <span class="text-rose-600 dark:text-rose-400 font-bold block bg-rose-50 dark:bg-rose-950/40 p-1.5 rounded text-center border border-rose-100 dark:border-rose-900/30">
                                    {{ $p->qty_sold }} Terjual
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- ==================== STEP 4: AUDIT PIKET HARIAN & CATATAN ==================== --}}
        @elseif($currentStep === 4)
            <div class="space-y-6">
                {{-- Kesimpulan Eksekutif Evaluasi --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        Kesimpulan & Rekomendasi Evaluasi Mingguan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-5 bg-blue-50/50 dark:bg-blue-950/20 rounded-xl border border-blue-100 dark:border-blue-900/40 space-y-2">
                            <h4 class="font-black text-blue-600 dark:text-blue-400 text-sm uppercase tracking-wider">1. Analisis Penjualan</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                Puncak omset terjadi pada hari <strong class="text-gray-900 dark:text-white font-bold">{{ $peakDay['day'] }}</strong> sebesar Rp {{ number_format($peakDay['revenue']) }}. Disarankan untuk meningkatkan variasi jajanan dan promosi pada hari dengan omset lebih rendah.
                            </p>
                        </div>
                        <div class="p-5 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-xl border border-emerald-100 dark:border-emerald-900/40 space-y-2">
                            <h4 class="font-black text-emerald-600 dark:text-emerald-400 text-sm uppercase tracking-wider">2. Disiplin & Tugas Kasir</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                Kehadiran kasir piket tercapai <strong class="text-gray-900 dark:text-white font-bold">{{ $shiftFulfillmentRate }}%</strong> dan tingkat penyelesaian tugas yang disetujui mencapai <strong class="text-gray-900 dark:text-white font-bold">{{ $taskApprovedRate }}%</strong>.
                            </p>
                        </div>
                        <div class="p-5 bg-amber-50/50 dark:bg-amber-950/20 rounded-xl border border-amber-100 dark:border-amber-900/40 space-y-2">
                            <h4 class="font-black text-amber-600 dark:text-amber-400 text-sm uppercase tracking-wider">3. Rotasi Stok Barang</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                Segera buat penawaran khusus atau bundling untuk 10 produk dengan perputaran lambat guna mencegah risiko barang kedaluwarsa.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Audit Piket & Tugas per Hari --}}
                <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                    <h3 class="text-base font-black text-gray-900 dark:text-white">Audit Piket Kasir & Laporan Tugas Harian</h3>
                    <div class="space-y-4">
                        @forelse($dailyShiftAudits as $audit)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                                <div class="bg-gray-50 dark:bg-gray-700/60 px-4 py-3 font-black text-xs text-gray-800 dark:text-gray-200 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                                    <span>{{ $audit['day_name'] }} ({{ $audit['date'] }})</span>
                                    <span class="text-primary-blue dark:text-blue-400 font-bold">{{ count($audit['cashiers']) }} Kasir Bertugas</span>
                                </div>
                                <div class="p-4 divide-y divide-gray-100 dark:divide-gray-700/60 space-y-3">
                                    @foreach($audit['cashiers'] as $c)
                                        <div class="pt-3 first:pt-0 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $c['name'] }}</span>
                                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $c['is_present'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                                        {{ $c['is_present'] ? 'Hadir' : 'Tidak Hadir' }}
                                                    </span>
                                                </div>
                                                <div class="mt-1 text-gray-500 dark:text-gray-400 flex items-center gap-3">
                                                    <span>Omset: <strong>Rp {{ number_format($c['sales_revenue']) }}</strong></span>
                                                    <span>Transaksi: <strong>{{ $c['sales_tx'] }} Tx</strong></span>
                                                    <span>Tugas: <strong>{{ $c['completed_task_count'] }} / {{ $c['assigned_task_count'] }} Selesai</strong></span>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5 max-w-md">
                                                @foreach($c['task_details'] as $t)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $t['status'] === 'Disetujui' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                                        {{ $t['task_name'] }} ({{ $t['status'] }})
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 dark:text-gray-400 py-6 text-center">Belum ada data audit jadwal untuk rentang tanggal ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- BOTTOM WIZARD NAVIGATION BAR (SEBELUMNYA / SELANJUTNYA) --}}
    <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
        {{-- Tombol Sebelumnya --}}
        <div>
            @if($currentStep > 1)
                <button wire:click="prevStep" 
                        class="flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold text-xs rounded-xl transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Sebelumnya</span>
                </button>
            @else
                <div class="text-xs font-semibold text-gray-400">
                    Awal Laporan
                </div>
            @endif
        </div>

        {{-- Step Indicator Dots --}}
        <div class="flex items-center gap-2">
            @for($st = 1; $st <= 4; $st++)
                <button wire:click="setStep({{ $st }})" 
                        class="h-2 rounded-full transition-all {{ $currentStep === $st ? 'bg-primary-blue w-8' : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 w-2' }}"
                        title="Ke Tahap {{ $st }}">
                </button>
            @endfor
        </div>

        {{-- Tombol Selanjutnya --}}
        <div>
            @if($currentStep < 4)
                <button wire:click="nextStep" 
                        class="flex items-center gap-2 px-5 py-2.5 bg-primary-blue hover:bg-blue-600 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-blue-500/20">
                    <span>Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button wire:click="setStep(1)" 
                        class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-emerald-600/20">
                    <span>Kembali ke Tahap 1</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- MODAL AUDIT KASIR DETAIL --}}
    @if($showCashierDetailModal && $modalCashierData)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50">
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white text-base">Detail Log Harian: {{ $modalCashierData['user']->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Rekap kehadiran, penyelesaian tugas, dan catatan operasional</p>
                    </div>
                    <button wire:click="closeCashierDetailModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 max-h-[70vh] overflow-y-auto space-y-4 text-xs">
                    @foreach($modalCashierData['daily_breakdown'] as $dayLog)
                        <div class="p-3.5 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600/60 space-y-2">
                            <div class="flex items-center justify-between font-bold">
                                <span class="text-gray-900 dark:text-white">{{ $dayLog['day_name'] }} ({{ $dayLog['date'] }})</span>
                                <span class="px-2 py-0.5 rounded text-[10px] {{ $dayLog['attendance'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
                                    {{ $dayLog['attendance'] ? 'Absen Masuk' : 'Tidak Ada Absensi' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-gray-600 dark:text-gray-300 text-[11px]">
                                <div>Omset: <strong>Rp {{ number_format($dayLog['sales_omset']) }}</strong> ({{ $dayLog['sales_count'] }} Tx)</div>
                                <div>Tugas Terdaftar: <strong>{{ count($dayLog['tasks']) }} Tugas</strong></div>
                            </div>
                            @if(count($dayLog['tasks']) > 0)
                                <div class="pt-1.5 border-t border-gray-200 dark:border-gray-600/60 space-y-1">
                                    <span class="text-[10px] font-bold text-gray-400 block">Daftar Tugas:</span>
                                    @foreach($dayLog['tasks'] as $t)
                                        <div class="text-[11px] text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                            <span>• {{ $t->taskDefinition->task_name ?? 'Tugas' }}</span>
                                            <span class="font-semibold text-[10px] {{ ($t->latestSubmission && $t->latestSubmission->approval_status === 'approved') ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                {{ $t->latestSubmission ? ucfirst($t->latestSubmission->approval_status) : 'Belum Submit' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 text-right">
                    <button wire:click="closeCashierDetailModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 font-bold rounded-xl text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
