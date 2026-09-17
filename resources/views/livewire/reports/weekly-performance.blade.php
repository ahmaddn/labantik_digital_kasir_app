<div class="p-6">
    {{-- Header & Date Range Selection matching Daily & Monthly Recaps --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">Performa Mingguan</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Evaluasi Penjualan & Kinerja Kasir Digital</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            {{-- Quick Presets --}}
            <div class="flex items-center bg-white dark:bg-gray-800 p-1.5 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                <button wire:click="setPresetRange('this_week')" class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $startDate === now()->startOfWeek()->toDateString() && $endDate === now()->endOfWeek()->toDateString() ? 'bg-primary-blue text-white shadow-md' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                    Minggu Ini
                </button>
                <button wire:click="setPresetRange('last_week')" class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $startDate === now()->subWeek()->startOfWeek()->toDateString() && $endDate === now()->subWeek()->endOfWeek()->toDateString() ? 'bg-primary-blue text-white shadow-md' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                    Minggu Lalu
                </button>
                <button wire:click="setPresetRange('this_month')" class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $startDate === now()->startOfMonth()->toDateString() && $endDate === now()->endOfMonth()->toDateString() ? 'bg-primary-blue text-white shadow-md' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                    Bulan Ini
                </button>
            </div>

            {{-- Date Range Inputs with Theme Styled Container --}}
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <input type="date" wire:model.live="startDate" class="border-none p-0 focus:ring-0 font-black text-xs bg-transparent dark:text-white cursor-pointer w-28">
                <span class="text-xs font-black text-gray-400 mx-2 uppercase tracking-widest">s/d</span>
                <input type="date" wire:model.live="endDate" class="border-none p-0 focus:ring-0 font-black text-xs bg-transparent dark:text-white cursor-pointer w-28">
            </div>
        </div>
    </div>

    {{-- INTERACTIVE FORM WIZARD STEPPER BAR (MATCHING THE 3.5rem / 2rem THEME DESIGN) --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-4 mb-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Step 1 Tab --}}
            <button wire:click="setStep(1)" 
                    class="flex items-center gap-4 p-4 rounded-2xl transition-all text-left {{ $currentStep === 1 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 1 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    01
                </div>
                <div class="min-w-0">
                    <span class="block text-[9px] font-black uppercase tracking-[0.2em] {{ $currentStep === 1 ? 'text-white/70' : 'text-gray-400' }}">Tahap 1</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Omset & Tren Harian</span>
                </div>
            </button>

            {{-- Step 2 Tab --}}
            <button wire:click="setStep(2)" 
                    class="flex items-center gap-4 p-4 rounded-2xl transition-all text-left {{ $currentStep === 2 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 2 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    02
                </div>
                <div class="min-w-0">
                    <span class="block text-[9px] font-black uppercase tracking-[0.2em] {{ $currentStep === 2 ? 'text-white/70' : 'text-gray-400' }}">Tahap 2</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Kinerja & Kasir</span>
                </div>
            </button>

            {{-- Step 3 Tab --}}
            <button wire:click="setStep(3)" 
                    class="flex items-center gap-4 p-4 rounded-2xl transition-all text-left {{ $currentStep === 3 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 3 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    03
                </div>
                <div class="min-w-0">
                    <span class="block text-[9px] font-black uppercase tracking-[0.2em] {{ $currentStep === 3 ? 'text-white/70' : 'text-gray-400' }}">Tahap 3</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Produk & Stok</span>
                </div>
            </button>

            {{-- Step 4 Tab --}}
            <button wire:click="setStep(4)" 
                    class="flex items-center gap-4 p-4 rounded-2xl transition-all text-left {{ $currentStep === 4 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $currentStep === 4 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    04
                </div>
                <div class="min-w-0">
                    <span class="block text-[9px] font-black uppercase tracking-[0.2em] {{ $currentStep === 4 ? 'text-white/70' : 'text-gray-400' }}">Tahap 4</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Audit Piket & Catatan</span>
                </div>
            </button>
        </div>
    </div>

    {{-- WIZARD STEP CONTENTS --}}
    <div class="space-y-12">

        {{-- ==================== STEP 1: RINGKASAN OMSET & TREN HARIAN ==================== --}}
        @if($currentStep === 1)
            {{-- KPI Executive Cards matching Daily & Monthly Recaps rounded-[3rem] --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-12">
                {{-- Card 1: Total Omset (Signature Primary Blue Card) --}}
                <div class="bg-primary-blue rounded-[3rem] p-10 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Total Omset Toko</h3>
                    <p class="text-4xl font-black text-white tracking-tight" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <div class="mt-8 pt-8 border-t border-white/10 flex justify-between items-center text-xs font-bold">
                        <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                            {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }} Pertumbuhan
                        </span>
                        <span class="opacity-60 text-[10px] uppercase tracking-wider">Lalu: Rp{{ number_format($prevRevenue / 1000, 0) }}k</span>
                    </div>
                </div>

                {{-- Card 2: Keuntungan Bersih (Signature Profit Card) --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Keuntungan Bersih</h3>
                    <p class="text-4xl font-black text-primary-red tracking-tight" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($totalProfit, 0, ',', '.') }}</p>
                    <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span class="{{ $profitGrowth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-primary-red' }}">
                            {{ $profitGrowth >= 0 ? '+'.$profitGrowth.'%' : $profitGrowth.'%' }} Pertumbuhan
                        </span>
                        <span class="text-gray-400 text-[10px] uppercase tracking-wider">Lalu: Rp{{ number_format($prevProfit / 1000, 0) }}k</span>
                    </div>
                </div>

                {{-- Card 3: Total Transaksi & Basket --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Volume Transaksi</h3>
                    <p class="text-4xl font-black text-gray-800 dark:text-white tracking-tight">{{ number_format($totalTransactions) }} <span class="text-xs uppercase font-bold text-gray-400 tracking-widest">Struk</span></p>
                    <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span class="text-primary-blue">{{ $totalItemsSold }} Pcs Terjual</span>
                        <span class="text-gray-400 text-[10px] uppercase tracking-wider">Rata: Rp{{ number_format($avgBasketSize) }}</span>
                    </div>
                </div>

                {{-- Card 4: Kepatuhan Piket --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-amber-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Kepatuhan Jadwal</h3>
                    <p class="text-4xl font-black text-amber-500 tracking-tight">{{ $shiftFulfillmentRate }}%</p>
                    <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span class="text-emerald-600 dark:text-emerald-400">{{ $taskApprovedRate }}% Tugas Disetujui</span>
                        <span class="text-gray-400 text-[10px] uppercase tracking-wider">{{ $totalAttendedShifts }}/{{ $totalScheduledShifts }} Shift</span>
                    </div>
                </div>
            </div>

            {{-- Daily Sales Bar Chart matching rounded-[3.5rem] in monthly recap --}}
            <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
                    <div>
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Tren Penjualan Harian</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Visualisasi Omset & Transaksi Tiap Hari Sepanjang Periode</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-5 py-2.5 bg-primary-blue/10 text-primary-blue border border-primary-blue/20 rounded-2xl text-xs font-black uppercase tracking-wider">
                            Puncak: {{ $peakDay['day'] }} (Rp{{ number_format($peakDay['revenue'], 0, ',', '.') }})
                        </div>
                    </div>
                </div>

                {{-- Authentic Bar Chart Track Container --}}
                <div class="pt-6 pb-2">
                    <div class="relative h-64 flex items-end">
                        {{-- Y-Axis Grid Lines --}}
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-12">
                            <div class="w-full border-b border-dashed border-gray-200 dark:border-gray-700/60 flex items-center justify-between">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-2">Rp{{ number_format($maxDailyRevenue / 1000, 0) }}k</span>
                            </div>
                            <div class="w-full border-b border-dashed border-gray-200 dark:border-gray-700/60 flex items-center justify-between">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-2">Rp{{ number_format(($maxDailyRevenue * 0.5) / 1000, 0) }}k</span>
                            </div>
                            <div class="w-full border-b border-gray-300 dark:border-gray-600 flex items-center justify-between">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-2">Rp0</span>
                            </div>
                        </div>

                        {{-- Bar Columns --}}
                        <div class="relative z-10 w-full flex items-end justify-around pl-14 pr-4 h-full pb-10">
                            @foreach($dailySales as $day)
                                @php 
                                    $barPct = ($maxDailyRevenue > 0 && $day['revenue'] > 0) ? max(6, min(100, round(($day['revenue'] / $maxDailyRevenue) * 100))) : 0;
                                    $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0 && $day['revenue'] == $peakDay['revenue']);
                                @endphp
                                <div class="flex-1 flex flex-col items-center justify-end group max-w-[80px] relative">
                                    {{-- Value Label Above Column --}}
                                    <div class="mb-2 text-center transition-transform group-hover:-translate-y-1">
                                        <span class="text-[11px] font-black block whitespace-nowrap {{ $isPeak ? 'text-primary-blue' : ($day['revenue'] > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400') }}">
                                            {{ $day['revenue'] > 0 ? 'Rp' . number_format($day['revenue'] / 1000, 0) . 'k' : 'Rp0' }}
                                        </span>
                                    </div>

                                    {{-- The Actual Vertical Bar Column Track --}}
                                    <div class="w-8 sm:w-12 h-44 flex items-end justify-center">
                                        @if($barPct > 0)
                                            <div class="w-full rounded-t-xl transition-all duration-500 {{ $isPeak ? 'bg-primary-blue shadow-lg shadow-blue-500/50 ring-2 ring-blue-300' : 'bg-primary-blue/80 hover:bg-primary-blue shadow-sm' }}" 
                                                 style="height: {{ $barPct }}%; min-height: 8px;"
                                                 title="{{ $day['day_name'] }} ({{ $day['date'] }}): Rp{{ number_format($day['revenue']) }} ({{ $day['transactions'] }} Tx)">
                                            </div>
                                        @else
                                            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-t-sm" title="Tidak ada penjualan"></div>
                                        @endif
                                    </div>

                                    {{-- X-Axis Day & Transaction --}}
                                    <div class="mt-3 text-center">
                                        <span class="text-xs font-black uppercase tracking-tight block {{ $isPeak ? 'text-primary-blue' : 'text-gray-800 dark:text-white' }}">
                                            {{ $day['day_name'] }}
                                        </span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">
                                            {{ $day['transactions'] }} Tx
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        {{-- ==================== STEP 2: KINERJA KASIR & EVALUASI ==================== --}}
        @elseif($currentStep === 2)
            <div class="space-y-12">
                {{-- Top 3 and Bottom Performers --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Top 3 Kasir Berkinerja Terbaik --}}
                    <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                        <div>
                            <h2 class="text-2xl font-bold uppercase tracking-tight text-emerald-600 dark:text-emerald-400 leading-none">Bintang Piket (Top 3)</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Kedisiplinan Piket, Kepatuhan Tugas & Omset Tertinggi</p>
                        </div>
                        <div class="space-y-4">
                            @forelse($topPerformers as $idx => $item)
                                <div class="flex items-center justify-between p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center gap-4">
                                        <span class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-base flex items-center justify-center border border-emerald-500/20">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <h4 class="font-black text-gray-800 dark:text-white text-sm uppercase tracking-tight">{{ $item->user->name }}</h4>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ $item->total_tx }} Tx • Omset Rp{{ number_format($item->total_sales) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3.5 py-1.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-black text-xs rounded-xl">
                                            {{ $item->overall_score }} Pts
                                        </span>
                                        <button wire:click="viewCashierDetail({{ $item->user->id }})" class="block text-[10px] font-black text-primary-blue uppercase tracking-widest hover:underline mt-1.5">
                                            Log Detail
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 py-6 text-center italic">Belum ada data kinerja kasir.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Kasir Perlu Evaluasi --}}
                    <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                        <div>
                            <h2 class="text-2xl font-bold uppercase tracking-tight text-primary-red leading-none">Perlu Evaluasi</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Kasir dengan Tugas Tertunda atau Kehadiran Kurang</p>
                        </div>
                        <div class="space-y-4">
                            @forelse($bottomPerformers as $item)
                                <div class="p-5 bg-rose-50/50 dark:bg-rose-950/20 rounded-2xl border border-rose-100 dark:border-rose-900/40 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-black text-gray-800 dark:text-white text-sm uppercase tracking-tight">{{ $item->user->name }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 bg-primary-red text-white text-[10px] font-black uppercase tracking-wider rounded-xl">
                                                Skor: {{ $item->overall_score }}
                                            </span>
                                            <button wire:click="viewCashierDetail({{ $item->user->id }})" class="text-[10px] font-black text-primary-blue uppercase tracking-widest hover:underline">
                                                Detail
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-rose-800 dark:text-rose-300 font-semibold leading-relaxed">{{ $item->evaluation_notes }}</p>
                                    <div class="text-[10px] font-bold text-gray-400 pt-2 flex justify-between border-t border-rose-100 dark:border-rose-900/40 uppercase tracking-wider">
                                        <span>Omset: Rp{{ number_format($item->total_sales) }}</span>
                                        <span>{{ $item->total_tx }} Transaksi</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 text-emerald-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-wider">Semua kasir berkinerja disiplin tanpa catatan evaluasi!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Tabel Seluruh Kinerja Kasir matching category-recap-table styling --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Daftar Audit Kinerja Seluruh Kasir</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Rekap Absensi, Kepatuhan Tugas, dan Total Omset Tiap Kasir</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Kasir</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Shift Terjadwal</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kehadiran</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tugas Selesai</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Omset Kasir</th>
                                    <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Skor</th>
                                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($cashierPerformanceList as $c)
                                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all">
                                        <td class="px-10 py-8">
                                            <span class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $c->user->name }}</span>
                                        </td>
                                        <td class="px-6 py-8 text-center text-sm font-bold text-gray-500 dark:text-gray-300">
                                            {{ $c->scheduled_shifts }} Shift
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <span class="px-3 py-1 rounded-xl text-xs font-black {{ $c->attendance_rate >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                                {{ $c->attended_shifts }} ({{ $c->attendance_rate }}%)
                                            </span>
                                        </td>
                                        <td class="px-6 py-8 text-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                            {{ $c->approved_tasks }} / {{ $c->total_assigned_tasks }}
                                        </td>
                                        <td class="px-6 py-8 text-right">
                                            <span class="text-base font-black text-primary-blue tracking-tight">Rp{{ number_format($c->total_sales, 0, ',', '.') }}</span>
                                            <span class="block text-[9px] font-bold text-gray-400 uppercase">{{ $c->total_tx }} Transaksi</span>
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <span class="px-3 py-1 rounded-xl font-black text-xs {{ $c->overall_score >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($c->overall_score >= 60 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}">
                                                {{ $c->overall_score }} Pts
                                            </span>
                                        </td>
                                        <td class="px-10 py-8 text-right">
                                            <button wire:click="viewCashierDetail({{ $c->user->id }})" class="px-4 py-2 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-xs">
                                                Audit Log
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-10 py-8 text-center text-gray-400 italic">Tidak ada kasir terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        {{-- ==================== STEP 3: ANALISIS PRODUK & STOK ==================== --}}
        @elseif($currentStep === 3)
            <div class="space-y-12">
                {{-- Product Comparison Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Grafik Perbandingan Produk Terlaris</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Volume Penjualan Periode Ini vs Periode Lalu (Internal TEFA vs Supplier)</p>
                    </div>
                    <div class="space-y-4 pt-2">
                        @foreach($topSellingProducts as $p)
                            @php 
                                $maxQty = max(1, max($p->qty_sold, $p->prev_qty));
                                $currWidth = round(($p->qty_sold / $maxQty) * 100);
                                $prevWidth = round(($p->prev_qty / $maxQty) * 100);
                            @endphp
                            <div class="space-y-2 p-5 bg-gray-50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="font-black text-gray-800 dark:text-white text-sm uppercase tracking-tight">{{ $p->product->name }}</span>
                                        <span class="px-2.5 py-0.5 {{ $p->is_tefa_internal ? 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800' : 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950 dark:text-purple-300 dark:border-purple-800' }} border rounded-lg text-[9px] font-black uppercase tracking-wider">
                                            {{ $p->is_tefa_internal ? 'TEFA Internal' : 'Supplier' }}
                                        </span>
                                    </div>
                                    <span class="font-black {{ $p->qty_growth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-primary-red' }} text-sm">
                                        {{ $p->qty_sold }} pcs ({{ $p->qty_growth >= 0 ? '+'.$p->qty_growth.'%' : $p->qty_growth.'%' }} vs {{ $p->prev_qty }} pcs lalu)
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-4 text-xs">
                                        <span class="w-28 text-gray-500 dark:text-gray-300 font-bold shrink-0 text-[10px] uppercase tracking-wider">Periode Ini: {{ $p->qty_sold }}</span>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden p-0.5">
                                            <div class="bg-primary-blue h-full rounded-full transition-all" style="width: {{ max(6, $currWidth) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs">
                                        <span class="w-28 text-gray-400 font-bold shrink-0 text-[10px] uppercase tracking-wider">Periode Lalu: {{ $p->prev_qty }}</span>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700/60 rounded-full h-2.5 overflow-hidden p-0.5">
                                            <div class="bg-gray-400 dark:bg-gray-500 h-full rounded-full transition-all" style="width: {{ max(4, $prevWidth) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 10 Slow-Moving Products matching grid --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-amber-600 dark:text-amber-400 leading-none">10 Produk Stagnan / Slow-Moving</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Perlu Atensi Promosi Bundling atau Rotasi Stok</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                        @foreach($leastSellingProducts as $p)
                            <div class="p-5 bg-gray-50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-800 text-xs flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="font-black text-gray-800 dark:text-white block truncate text-sm uppercase tracking-tight" title="{{ $p->product->name }}">{{ $p->product->name }}</span>
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-black uppercase tracking-wider block mt-1">Sisa Stok: {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</span>
                                </div>
                                <span class="text-primary-red font-black block bg-rose-50 dark:bg-rose-950/40 p-2 rounded-xl text-center border border-rose-100 dark:border-rose-900/30 text-xs">
                                    {{ $p->qty_sold }} Terjual
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- ==================== STEP 4: AUDIT PIKET HARIAN & CATATAN ==================== --}}
        @elseif($currentStep === 4)
            <div class="space-y-12">
                {{-- Kesimpulan Eksekutif --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Kesimpulan & Rekomendasi Evaluasi</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Poin Penting Hasil Evaluasi Operasional Mingguan</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 bg-blue-50/50 dark:bg-blue-950/20 rounded-3xl border border-blue-100 dark:border-blue-900/40 space-y-3">
                            <h4 class="font-black text-primary-blue text-sm uppercase tracking-wider">1. Analisis Penjualan</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed font-medium">
                                Puncak omset terjadi pada hari <strong class="text-gray-900 dark:text-white font-bold">{{ $peakDay['day'] }}</strong> sebesar Rp{{ number_format($peakDay['revenue']) }}. Dianjurkan menambah program promo khusus pada hari-hari dengan omset lebih rendah.
                            </p>
                        </div>
                        <div class="p-6 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-3xl border border-emerald-100 dark:border-emerald-900/40 space-y-3">
                            <h4 class="font-black text-emerald-600 dark:text-emerald-400 text-sm uppercase tracking-wider">2. Disiplin & Tugas Kasir</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed font-medium">
                                Kehadiran kasir piket tercapai <strong class="text-gray-900 dark:text-white font-bold">{{ $shiftFulfillmentRate }}%</strong> dan tingkat penyelesaian tugas piket yang disetujui sebesar <strong class="text-gray-900 dark:text-white font-bold">{{ $taskApprovedRate }}%</strong>.
                            </p>
                        </div>
                        <div class="p-6 bg-amber-50/50 dark:bg-amber-950/20 rounded-3xl border border-amber-100 dark:border-amber-900/40 space-y-3">
                            <h4 class="font-black text-amber-600 dark:text-amber-400 text-sm uppercase tracking-wider">3. Rotasi Stok Barang</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed font-medium">
                                Buat penawaran diskon atau bundling paket untuk 10 barang lambat terjual guna menghindari risiko barang rusak atau kedaluwarsa.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Audit Piket & Tugas per Hari --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Audit Piket Kasir & Laporan Tugas Harian</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Daftar Kehadiran dan Realisasi Tugas Piket Per Hari</p>
                    </div>
                    <div class="space-y-5">
                        @forelse($dailyShiftAudits as $audit)
                            <div class="border border-gray-100 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs">
                                <div class="bg-gray-50 dark:bg-gray-900/60 px-6 py-4 font-black text-xs text-gray-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 uppercase tracking-wider">
                                    <span>{{ $audit['day_name'] }} ({{ $audit['date'] }})</span>
                                    <span class="text-primary-blue font-bold">{{ count($audit['cashiers']) }} Kasir Bertugas</span>
                                </div>
                                <div class="p-6 divide-y divide-gray-100 dark:divide-gray-800 space-y-4">
                                    @foreach($audit['cashiers'] as $c)
                                        <div class="pt-4 first:pt-0 flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs">
                                            <div>
                                                <div class="flex items-center gap-3">
                                                    <span class="font-black text-gray-800 dark:text-white text-sm uppercase tracking-tight">{{ $c['name'] }}</span>
                                                    <span class="px-2.5 py-0.5 rounded-lg font-black text-[10px] uppercase {{ $c['is_present'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                                        {{ $c['is_present'] ? 'Hadir' : 'Tidak Hadir' }}
                                                    </span>
                                                </div>
                                                <div class="mt-1.5 text-gray-400 flex items-center gap-4 text-[11px] font-bold">
                                                    <span>Omset: <strong class="text-gray-800 dark:text-white">Rp{{ number_format($c['sales_revenue']) }}</strong></span>
                                                    <span>Transaksi: <strong class="text-gray-800 dark:text-white">{{ $c['sales_tx'] }} Tx</strong></span>
                                                    <span>Tugas: <strong class="text-gray-800 dark:text-white">{{ $c['completed_task_count'] }} / {{ $c['assigned_task_count'] }}</strong></span>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap gap-2 max-w-md">
                                                @foreach($c['task_details'] as $t)
                                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold {{ $t['status'] === 'Disetujui' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                                        {{ $t['task_name'] }} ({{ $t['status'] }})
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-6 text-center italic">Belum ada data audit jadwal untuk rentang tanggal ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- BOTTOM WIZARD NAVIGATION BAR MATCHING 2.5rem CORNERS --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4 mt-12">
        {{-- Tombol Sebelumnya --}}
        <div>
            @if($currentStep > 1)
                <button wire:click="prevStep" 
                        class="flex items-center gap-3 px-6 py-3.5 bg-gray-100 dark:bg-gray-700/60 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Sebelumnya</span>
                </button>
            @else
                <span class="text-xs font-black uppercase tracking-widest text-gray-400 pl-2">Tahap Awal</span>
            @endif
        </div>

        {{-- Step Indicator Dots --}}
        <div class="flex items-center gap-2.5">
            @for($st = 1; $st <= 4; $st++)
                <button wire:click="setStep({{ $st }})" 
                        class="h-2.5 rounded-full transition-all {{ $currentStep === $st ? 'bg-primary-blue w-10' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 w-2.5' }}"
                        title="Ke Tahap {{ $st }}">
                </button>
            @endfor
        </div>

        {{-- Tombol Selanjutnya --}}
        <div>
            @if($currentStep < 4)
                <button wire:click="nextStep" 
                        class="flex items-center gap-3 px-8 py-3.5 bg-primary-blue hover:bg-blue-600 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-xl shadow-blue-500/20">
                    <span>Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button wire:click="setStep(1)" 
                        class="flex items-center gap-3 px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-xl shadow-emerald-600/20">
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
            <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50">
                    <div>
                        <h3 class="font-black text-gray-800 dark:text-white text-lg uppercase tracking-tight">Detail Log Harian: {{ $modalCashierData['user']->name }}</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-0.5">Rekap Kehadiran, Tugas, & Catatan Operasional</p>
                    </div>
                    <button wire:click="closeCashierDetailModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-8 max-h-[70vh] overflow-y-auto space-y-4 text-xs">
                    @foreach($modalCashierData['daily_breakdown'] as $dayLog)
                        <div class="p-5 bg-gray-50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-3">
                            <div class="flex items-center justify-between font-black">
                                <span class="text-gray-800 dark:text-white uppercase tracking-tight">{{ $dayLog['day_name'] }} ({{ $dayLog['date'] }})</span>
                                <span class="px-3 py-1 rounded-xl text-[10px] uppercase tracking-wider {{ $dayLog['attendance'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    {{ $dayLog['attendance'] ? 'Absen Masuk' : 'Tidak Ada Absensi' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-gray-500 dark:text-gray-400 text-xs font-bold">
                                <div>Omset: <strong class="text-gray-800 dark:text-white">Rp{{ number_format($dayLog['sales_omset']) }}</strong> ({{ $dayLog['sales_count'] }} Tx)</div>
                                <div>Tugas: <strong class="text-gray-800 dark:text-white">{{ count($dayLog['tasks']) }} Tugas</strong></div>
                            </div>
                            @if(count($dayLog['tasks']) > 0)
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-800 space-y-1.5">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Daftar Tugas:</span>
                                    @foreach($dayLog['tasks'] as $t)
                                        <div class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                            <span>• {{ $t->taskDefinition->task_name ?? 'Tugas' }}</span>
                                            <span class="font-bold text-[10px] {{ ($t->latestSubmission && $t->latestSubmission->approval_status === 'approved') ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                {{ $t->latestSubmission ? ucfirst($t->latestSubmission->approval_status) : 'Belum Submit' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 text-right">
                    <button wire:click="closeCashierDetailModal" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 font-black rounded-2xl text-xs uppercase tracking-wider">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
