<div class="p-6">
    {{-- Header & Date Range Selection matching Daily & Monthly Recaps --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">
                Performa Mingguan</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Evaluasi Penjualan & Kinerja
                Kasir Digital</p>
        </div>

        <div class="flex flex-wrap items-center gap-4">
            {{-- Quick Presets --}}
            <div
                class="flex items-center bg-white dark:bg-gray-800 p-1.5 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                <button wire:click="setPresetRange('this_week')"
                    class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $startDate === now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString() && $endDate === now()->startOfWeek(\Carbon\Carbon::MONDAY)->addDays(4)->toDateString() ? 'bg-primary-blue text-white shadow-md' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                    Minggu Ini
                </button>
                <button wire:click="setPresetRange('last_week')"
                    class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $startDate === now()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString() && $endDate === now()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY)->addDays(4)->toDateString() ? 'bg-primary-blue text-white shadow-md' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                    Minggu Lalu
                </button>
                <button wire:click="setPresetRange('this_month')"
                    class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $startDate === now()->startOfMonth()->toDateString() && $endDate === now()->endOfMonth()->toDateString() ? 'bg-primary-blue text-white shadow-md' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                    Bulan Ini
                </button>
            </div>

            {{-- Date Range Inputs with Theme Styled Container --}}
            <div
                class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" x2="16" y1="2" y2="6" />
                    <line x1="8" x2="8" y1="2" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                <input type="date" wire:model.live="startDate"
                    class="border-none p-0 focus:ring-0 font-black text-xs bg-transparent dark:text-white cursor-pointer w-28">
                <span class="text-xs font-black text-gray-400 mx-2 uppercase tracking-widest">s/d</span>
                <input type="date" wire:model.live="endDate"
                    class="border-none p-0 focus:ring-0 font-black text-xs bg-transparent dark:text-white cursor-pointer w-28">
            </div>
        </div>
    </div>

    {{-- INTERACTIVE FORM WIZARD STEPPER BAR (MATCHING THE 3.5rem / 2rem THEME DESIGN) --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-4 mb-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            {{-- Step 1 Tab --}}
            <button wire:click="setStep(1)"
                class="flex items-center gap-3 p-3.5 rounded-2xl transition-all text-left cursor-pointer {{ $currentStep === 1 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $currentStep === 1 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    01
                </div>
                <div class="min-w-0">
                    <span
                        class="block text-[8px] font-black uppercase tracking-[0.2em] {{ $currentStep === 1 ? 'text-white/70' : 'text-gray-400' }}">Tahap 1</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Omset & Tren</span>
                </div>
            </button>

            {{-- Step 2 Tab --}}
            <button wire:click="setStep(2)"
                class="flex items-center gap-3 p-3.5 rounded-2xl transition-all text-left cursor-pointer {{ $currentStep === 2 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $currentStep === 2 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    02
                </div>
                <div class="min-w-0">
                    <span
                        class="block text-[8px] font-black uppercase tracking-[0.2em] {{ $currentStep === 2 ? 'text-white/70' : 'text-gray-400' }}">Tahap 2</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Kinerja Kasir</span>
                </div>
            </button>

            {{-- Step 3 Tab --}}
            <button wire:click="setStep(3)"
                class="flex items-center gap-3 p-3.5 rounded-2xl transition-all text-left cursor-pointer {{ $currentStep === 3 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $currentStep === 3 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    03
                </div>
                <div class="min-w-0">
                    <span
                        class="block text-[8px] font-black uppercase tracking-[0.2em] {{ $currentStep === 3 ? 'text-white/70' : 'text-gray-400' }}">Tahap 3</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Produk & Stok</span>
                </div>
            </button>

            {{-- Step 4 Tab --}}
            <button wire:click="setStep(4)"
                class="flex items-center gap-3 p-3.5 rounded-2xl transition-all text-left cursor-pointer {{ $currentStep === 4 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $currentStep === 4 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    04
                </div>
                <div class="min-w-0">
                    <span
                        class="block text-[8px] font-black uppercase tracking-[0.2em] {{ $currentStep === 4 ? 'text-white/70' : 'text-gray-400' }}">Tahap 4</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Audit Piket</span>
                </div>
            </button>

            {{-- Step 5 Tab: Kritik, Saran & Tanya Jawab --}}
            <button wire:click="setStep(5)"
                class="flex items-center gap-3 p-3.5 rounded-2xl transition-all text-left cursor-pointer {{ $currentStep === 5 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $currentStep === 5 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    05
                </div>
                <div class="min-w-0">
                    <span
                        class="block text-[8px] font-black uppercase tracking-[0.2em] {{ $currentStep === 5 ? 'text-white/70' : 'text-gray-400' }}">Tahap 5</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Saran & QnA</span>
                </div>
            </button>

            {{-- Step 6 Tab: Penutup & Evaluasi Langsung --}}
            <button wire:click="setStep(6)"
                class="flex items-center gap-3 p-3.5 rounded-2xl transition-all text-left cursor-pointer {{ $currentStep === 6 ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900/80' }}">
                <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $currentStep === 6 ? 'bg-white/20 text-white' : 'bg-white dark:bg-gray-800 text-primary-blue shadow-xs' }}">
                    06
                </div>
                <div class="min-w-0">
                    <span
                        class="block text-[8px] font-black uppercase tracking-[0.2em] {{ $currentStep === 6 ? 'text-white/70' : 'text-gray-400' }}">Tahap 6</span>
                    <span class="block text-xs font-black uppercase tracking-tight truncate">Sesi Evaluasi</span>
                </div>
            </button>
        </div>
    </div>

    {{-- WIZARD STEP CONTENTS --}}
    <div class="space-y-12">

        {{-- ==================== STEP 1: RINGKASAN OMSET & TREN HARIAN ==================== --}}
        @if ($currentStep === 1)
            {{-- KPI Executive Cards matching Daily & Monthly Recaps rounded-[3rem] --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-12">
                {{-- Card 1: Total Omset (Signature Primary Blue Card) --}}
                <div
                    class="bg-primary-blue rounded-[3rem] p-8 sm:p-10 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Total Omset Toko</h3>
                    <p class="text-3xl sm:text-4xl font-black text-white tracking-tight" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <div class="mt-8 pt-8 border-t border-white/10 flex justify-between items-center text-xs font-bold">
                        <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                            {{ $revenueGrowth >= 0 ? '+' . $revenueGrowth . '%' : $revenueGrowth . '%' }} Pertumbuhan
                        </span>
                        <span class="opacity-60 text-[10px] uppercase tracking-wider">Lalu:
                            Rp{{ number_format($prevRevenue / 1000, 0) }}k</span>
                    </div>
                </div>

                {{-- Card 2: Keuntungan Bersih (Signature Profit Card) --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 7-8.5 8.5-5-5L2 17" />
                            <polyline points="18 7 22 7 22 11" />
                        </svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Keuntungan Bersih
                    </h3>
                    <p class="text-3xl sm:text-4xl font-black text-primary-red tracking-tight"
                        :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($totalProfit, 0, ',', '.') }}</p>
                    <div
                        class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span
                            class="{{ $profitGrowth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-primary-red' }}">
                            {{ $profitGrowth >= 0 ? '+' . $profitGrowth . '%' : $profitGrowth . '%' }} Pertumbuhan
                        </span>
                        <span class="text-gray-400 text-[10px] uppercase tracking-wider">Lalu:
                            Rp{{ number_format($prevProfit / 1000, 0) }}k</span>
                    </div>
                </div>

                {{-- Card 3: Total Pengeluaran Kas (Manual Input dari Kas) --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-rose-500" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="19" x2="12" y2="5" />
                            <polyline points="5 12 12 19 19 12" />
                        </svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Total Pengeluaran Kas
                    </h3>
                    <p class="text-3xl sm:text-4xl font-black text-rose-600 dark:text-rose-400 tracking-tight"
                        :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($totalExpense, 0, ',', '.') }}</p>
                    <div
                        class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span class="text-gray-500 dark:text-gray-400 text-[10px] uppercase tracking-wider">
                            Input Kas Manual
                        </span>
                        <span class="text-gray-400 text-[10px] uppercase tracking-wider">Lalu:
                            Rp{{ number_format($prevExpense / 1000, 0) }}k</span>
                    </div>
                </div>

                {{-- Card 4: Total Transaksi & Basket --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Volume Transaksi
                    </h3>
                    <p class="text-3xl sm:text-4xl font-black text-gray-800 dark:text-white tracking-tight">
                        {{ number_format($totalTransactions) }} <span
                            class="text-xs uppercase font-bold text-gray-400 tracking-widest">Struk</span></p>
                    <div
                        class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span class="text-primary-blue">{{ $totalItemsSold }} Pcs Terjual</span>
                        <span class="text-gray-400 text-[10px] uppercase tracking-wider">Rata:
                            Rp{{ number_format($avgBasketSize) }}</span>
                    </div>
                </div>

                {{-- Card 5: Kepatuhan Piket --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-40 h-40 text-amber-500" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Kepatuhan Jadwal
                    </h3>
                    <p class="text-3xl sm:text-4xl font-black text-amber-500 tracking-tight">{{ $shiftFulfillmentRate }}%</p>
                    <div
                        class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                        <span class="text-emerald-600 dark:text-emerald-400">{{ $taskApprovedRate }}% Tugas
                            Disetujui</span>
                        <span
                            class="text-gray-400 text-[10px] uppercase tracking-wider">{{ $totalAttendedShifts }}/{{ $totalScheduledShifts }}
                            Shift</span>
                    </div>
                </div>
            </div>

            {{-- Daily Sales Bar Chart matching rounded-[3.5rem] in monthly recap --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3.5rem] p-6 sm:p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 sm:mb-10">
                    <div>
                        <h2
                            class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">
                            Tren Penjualan Harian</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Visualisasi
                            Omset & Transaksi Tiap Hari Sepanjang Periode</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="px-4 sm:px-5 py-2 sm:py-2.5 bg-primary-blue/10 text-primary-blue border border-primary-blue/20 rounded-2xl text-xs font-black uppercase tracking-wider">
                            Puncak: {{ $peakDay['day'] }} (Rp{{ number_format($peakDay['revenue'], 0, ',', '.') }})
                        </div>
                    </div>
                </div>

                {{-- Authentic Bar Chart Track Container with mobile overflow support --}}
                <div class="pt-6 pb-2 overflow-x-auto no-scrollbar">
                    <div class="relative h-64 flex items-end min-w-[500px] sm:min-w-full">
                        {{-- Y-Axis Grid Lines --}}
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-12">
                            <div
                                class="w-full border-b border-dashed border-gray-200 dark:border-gray-700/60 flex items-center justify-between">
                                <span
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-2">Rp{{ number_format($maxDailyRevenue / 1000, 0) }}k</span>
                            </div>
                            <div
                                class="w-full border-b border-dashed border-gray-200 dark:border-gray-700/60 flex items-center justify-between">
                                <span
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-2">Rp{{ number_format(($maxDailyRevenue * 0.5) / 1000, 0) }}k</span>
                            </div>
                            <div
                                class="w-full border-b border-gray-300 dark:border-gray-600 flex items-center justify-between">
                                <span
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 -mt-3.5 bg-white dark:bg-gray-800 pr-2">Rp0</span>
                            </div>
                        </div>

                        {{-- Bar Columns --}}
                        <div class="relative z-10 w-full flex items-end justify-around pl-14 pr-4 h-full pb-10">
                            @foreach ($dailySales as $day)
                                @php
                                    $barPct =
                                        $maxDailyRevenue > 0 && $day['revenue'] > 0
                                            ? max(8, min(100, round(($day['revenue'] / $maxDailyRevenue) * 100)))
                                            : 0;
                                    $isPeak =
                                        $day['day_name'] === $peakDay['day'] &&
                                        $day['revenue'] > 0 &&
                                        $day['revenue'] == $peakDay['revenue'];
                                @endphp
                                <div
                                    class="flex-1 flex flex-col items-center justify-end group max-w-[80px] relative px-1">
                                    {{-- Value Label Above Column --}}
                                    <div class="mb-2 text-center transition-transform group-hover:-translate-y-1">
                                        <span
                                            class="text-[10px] sm:text-[11px] font-black block whitespace-nowrap {{ $isPeak ? 'text-primary-blue' : ($day['revenue'] > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400') }}">
                                            {{ $day['revenue'] > 0 ? 'Rp' . number_format($day['revenue'] / 1000, 0) . 'k' : 'Rp0' }}
                                        </span>
                                    </div>

                                    {{-- The Actual Vertical Bar Column Track --}}
                                    <div class="w-8 sm:w-12 h-44 flex items-end justify-center">
                                        @if ($barPct > 0)
                                            <div class="w-full rounded-t-xl transition-all duration-500 {{ $isPeak ? 'bg-blue-600 dark:bg-blue-500 shadow-lg shadow-blue-500/50 ring-2 ring-blue-300' : 'bg-blue-500/80 hover:bg-blue-600 dark:bg-blue-500/70 dark:hover:bg-blue-500 shadow-xs' }}"
                                                style="height: {{ $barPct }}%; min-height: 12px; background-color: {{ $isPeak ? 'var(--color-primary-blue, #2563eb)' : 'var(--color-primary-blue-dark, #3b82f6)' }};"
                                                title="{{ $day['day_name'] }} ({{ $day['date'] }}): Rp{{ number_format($day['revenue']) }} ({{ $day['transactions'] }} Tx)">
                                            </div>
                                        @else
                                            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-t-sm"
                                                title="Tidak ada penjualan"></div>
                                        @endif
                                    </div>

                                    {{-- X-Axis Day, Omset, Transaction, & Cash Audit Selisih --}}
                                    <div class="mt-3 text-center space-y-0.5">
                                        <span
                                            class="text-xs font-black uppercase tracking-tight block {{ $isPeak ? 'text-primary-blue' : 'text-gray-800 dark:text-white' }}">
                                            {{ $day['day_name'] }}
                                        </span>
                                        <span
                                            class="text-[10px] font-black block tracking-tight {{ $day['revenue'] > 0 ? 'text-primary-blue dark:text-primary-blue-light' : 'text-gray-400' }}">
                                            Rp{{ number_format($day['revenue'], 0, ',', '.') }}
                                        </span>
                                        <span
                                            class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">
                                            {{ $day['transactions'] }} Tx
                                        </span>
                                        @if($day['has_audit'])
                                            <div class="text-[9px] font-black uppercase tracking-tight leading-none pt-1">
                                                @if($day['cash_diff'] > 0)
                                                    <span class="text-emerald-600 dark:text-emerald-400" title="Kas Lebih (Untung Kas Laci): +Rp{{ number_format($day['cash_diff']) }}">
                                                        Selisih: +Rp{{ number_format($day['cash_diff'], 0, ',', '.') }}
                                                    </span>
                                                @elseif($day['cash_diff'] < 0)
                                                    <span class="text-rose-600 dark:text-rose-400" title="Kas Kurang (Rugi/Loss Kas Laci): -Rp{{ number_format(abs($day['cash_diff'])) }}">
                                                        Selisih: -Rp{{ number_format(abs($day['cash_diff']), 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400" title="Kas Cocok (Match / Tidak Ada Selisih)">
                                                        Selisih: Rp0
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== STEP 2: KINERJA KASIR & EVALUASI ==================== --}}
        @elseif($currentStep === 2)
            <div class="space-y-10">
                {{-- Top 3 and Bottom Performers --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10 items-stretch">
                    {{-- Top 3 Kasir Berkinerja Terbaik --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3rem] p-6 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between space-y-6">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <h2
                                    class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-emerald-600 dark:text-emerald-400 leading-none">
                                    Bintang Piket (Top 3)</h2>
                                <span
                                    class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                    Terbaik
                                </span>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Kedisiplinan
                                Piket, Kepatuhan Tugas & Omset Tertinggi</p>
                        </div>
                        <div class="space-y-3.5 flex-1">
                            @forelse($topPerformers as $idx => $item)
                                <div
                                    class="flex items-center justify-between p-4 sm:p-5 bg-gray-50/80 dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-800/80 hover:border-emerald-500/30 transition-all">
                                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                                        <span
                                            class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-sm sm:text-base flex items-center justify-center border border-emerald-500/20 shrink-0">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div class="min-w-0">
                                            <h4
                                                class="font-black text-gray-800 dark:text-white text-xs sm:text-sm uppercase tracking-tight truncate">
                                                {{ $item->user->name }}</h4>
                                            <p
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 truncate">
                                                {{ $item->total_tx }} Tx • Omset
                                                Rp{{ number_format($item->total_sales) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 pl-3">
                                        <span
                                            class="px-3 py-1 bg-emerald-500/15 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 font-black text-xs rounded-xl border border-emerald-500/20">
                                            {{ $item->overall_score }} Pts
                                        </span>
                                        <button wire:click="viewCashierDetail('{{ $item->user->id }}')"
                                            class="block text-[10px] font-black text-primary-blue uppercase tracking-widest hover:underline mt-1.5 ml-auto cursor-pointer">
                                            Log Detail
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 py-6 text-center italic">Belum ada data kinerja kasir.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Kasir Perlu Evaluasi --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3rem] p-6 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between space-y-6">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <h2
                                    class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-primary-red leading-none">
                                    Perlu
                                    Evaluasi</h2>
                                <span
                                    class="px-3 py-1 bg-rose-500/10 text-primary-red border border-rose-500/20 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                    Catatan
                                </span>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Kasir dengan
                                Tugas Tertunda atau Kehadiran Kurang</p>
                        </div>
                        <div class="space-y-3.5 flex-1">
                            @forelse($bottomPerformers as $item)
                                <div
                                    class="p-4 sm:p-5 bg-rose-50/40 dark:bg-rose-950/20 rounded-2xl border border-rose-100/80 dark:border-rose-900/30 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <h4
                                            class="font-black text-gray-800 dark:text-white text-xs sm:text-sm uppercase tracking-tight">
                                            {{ $item->user->name }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2.5 py-0.5 bg-rose-500/15 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 text-[10px] font-black uppercase tracking-wider rounded-xl border border-rose-500/20">
                                                Skor: {{ $item->overall_score }}
                                            </span>
                                            <button wire:click="viewCashierDetail('{{ $item->user->id }}')"
                                                class="text-[10px] font-black text-primary-blue uppercase tracking-widest hover:underline cursor-pointer">
                                                Detail
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-rose-700 dark:text-rose-300 font-semibold leading-relaxed">
                                        {{ $item->evaluation_notes }}</p>
                                    <div
                                        class="text-[10px] font-bold text-gray-400 pt-2 flex justify-between border-t border-rose-100/80 dark:border-rose-900/30 uppercase tracking-wider">
                                        <span>Omset: Rp{{ number_format($item->total_sales) }}</span>
                                        <span>{{ $item->total_tx }} Transaksi</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                                    <svg class="w-12 h-12 text-emerald-500 mb-3 opacity-80" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p
                                        class="text-xs text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-wider">
                                        Semua kasir berkinerja disiplin tanpa catatan evaluasi!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Tabel Seluruh Kinerja Kasir matching category-recap-table styling --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/80 overflow-hidden">
                    <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h2
                                class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">
                                Daftar Audit Kinerja Seluruh Kasir</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Rekap
                                Absensi, Kepatuhan Tugas, dan Total Omset Tiap Kasir</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th
                                        class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Nama Kasir</th>
                                    <th
                                        class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Shift Terjadwal</th>
                                    <th
                                        class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Kehadiran</th>
                                    <th
                                        class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Tugas Selesai</th>
                                    <th
                                        class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Omset Kasir</th>
                                    <th
                                        class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Skor</th>
                                    <th
                                        class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($cashierPerformanceList as $c)
                                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all">
                                        <td class="px-10 py-8">
                                            <span
                                                class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $c->user->name }}</span>
                                        </td>
                                        <td
                                            class="px-6 py-8 text-center text-sm font-bold text-gray-500 dark:text-gray-300">
                                            {{ $c->scheduled_count }} Shift
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            @php
                                                $attRate =
                                                    $c->scheduled_count > 0
                                                        ? round(($c->attended_count / $c->scheduled_count) * 100)
                                                        : 100;
                                                $missedShift = max(0, $c->scheduled_count - $c->attended_count);
                                            @endphp
                                            <div class="space-y-1">
                                                <span
                                                    class="px-3 py-1 rounded-xl text-xs font-black inline-block {{ $attRate >= 80 ? 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-500/20' : 'bg-rose-500/15 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-500/20' }}">
                                                    {{ $c->attended_count }} / {{ $c->scheduled_count }} ({{ $attRate }}%)
                                                </span>
                                                <div class="flex items-center justify-center gap-1.5 text-[10px] font-black uppercase tracking-tight">
                                                    <span class="text-emerald-600 dark:text-emerald-400" title="Hadir Tepat Waktu">+{{ $c->on_time_count }} Tepat</span>
                                                    @if($c->late_count > 0)
                                                        <span class="text-amber-600 dark:text-amber-400" title="Terlambat Piket">-{{ $c->late_count }} Telat</span>
                                                    @endif
                                                    @if($missedShift > 0)
                                                        <span class="text-rose-600 dark:text-rose-400" title="Tidak Hadir Piket">-{{ $missedShift }} Absen</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-8 text-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                            {{ $c->approved_tasks }} / {{ $c->assigned_tasks }}
                                        </td>
                                        <td class="px-6 py-8 text-right">
                                            <span
                                                class="text-base font-black text-primary-blue tracking-tight">Rp{{ number_format($c->total_sales, 0, ',', '.') }}</span>
                                            <span
                                                class="block text-[9px] font-bold text-gray-400 uppercase">{{ $c->total_tx }}
                                                Transaksi</span>
                                        </td>
                                        <td class="px-6 py-8 text-center">
                                            <span
                                                class="px-3 py-1 rounded-xl font-black text-xs {{ $c->overall_score >= 80 ? 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-500/20' : ($c->overall_score >= 60 ? 'bg-amber-500/15 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-500/20' : 'bg-rose-500/15 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-500/20') }}">
                                                {{ $c->overall_score }} Pts
                                            </span>
                                        </td>
                                        <td class="px-10 py-8 text-right">
                                            <button wire:click="viewCashierDetail('{{ $c->user->id }}')"
                                                class="px-4 py-2 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-xs">
                                                Audit Log
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-10 py-8 text-center text-gray-400 italic">Tidak
                                            ada kasir terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ==================== STEP 3: ANALISIS PRODUK & STOK ==================== --}}
        @elseif($currentStep === 3)
            <div class="space-y-10">
                {{-- Product Comparison Chart --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3.5rem] p-6 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/80 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">
                                Grafik Perbandingan Produk Terlaris</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Volume
                                Penjualan
                                Periode Ini vs Periode Lalu (Internal TEFA vs Supplier)</p>
                        </div>
                        <div class="flex items-center gap-3 self-start sm:self-center">
                            <span
                                class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-blue"></span> Periode Ini
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-300 dark:bg-gray-600"></span> Periode
                                Lalu
                            </span>
                        </div>
                    </div>

                    {{-- Cards Grid 2 Kolom untuk Produk Terlaris --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2">
                        @foreach ($topSellingProducts as $p)
                            @php
                                $maxQty = max(1, max($p->qty_sold, $p->prev_qty));
                                $currWidth = round(($p->qty_sold / $maxQty) * 100);
                                $prevWidth = round(($p->prev_qty / $maxQty) * 100);
                            @endphp
                            <div
                                class="p-5 bg-gray-50/70 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800/80 space-y-3.5 hover:border-primary-blue/30 transition-all">
                                <div class="flex items-start justify-between gap-3 text-xs">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="font-black text-gray-800 dark:text-white text-sm uppercase tracking-tight truncate max-w-[220px]"
                                                title="{{ $p->product->name }}">{{ $p->product->name }}</span>
                                            <span
                                                class="px-2.5 py-0.5 {{ $p->is_tefa_internal ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20' : 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20' }} border rounded-lg text-[9px] font-black uppercase tracking-wider">
                                                {{ $p->is_tefa_internal ? 'TEFA Internal' : 'Supplier' }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mt-1">
                                            Omset: Rp{{ number_format($p->omset) }}
                                        </span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span
                                            class="inline-block px-2.5 py-1 rounded-xl text-xs font-black {{ $p->qty_growth >= 0 ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/15 text-primary-red border border-rose-500/20' }}">
                                            {{ $p->qty_sold }} pcs
                                        </span>
                                        <span
                                            class="block text-[9px] font-bold {{ $p->qty_growth >= 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-400' }} mt-0.5">
                                            {{ $p->qty_growth >= 0 ? '+' . $p->qty_growth . '%' : $p->qty_growth . '%' }}
                                            vs lalu
                                        </span>
                                    </div>
                                </div>

                                {{-- Visual Progress Bars --}}
                                <div class="space-y-2 pt-1 border-t border-gray-100 dark:border-gray-800/80">
                                    {{-- Periode Ini --}}
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-[10px] font-bold">
                                            <span
                                                class="text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode
                                                Ini</span>
                                            <span class="text-primary-blue font-black">{{ $p->qty_sold }} pcs</span>
                                        </div>
                                        <div
                                            class="w-full bg-gray-200/80 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden">
                                            <div class="bg-gradient-to-r from-blue-600 to-indigo-500 h-full rounded-full transition-all duration-500 shadow-xs"
                                                style="width: {{ max(5, $currWidth) }}%"></div>
                                        </div>
                                    </div>

                                    {{-- Periode Lalu --}}
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-[10px] font-bold">
                                            <span class="text-gray-400 uppercase tracking-wider">Periode Lalu</span>
                                            <span class="text-gray-400 font-black">{{ $p->prev_qty }} pcs</span>
                                        </div>
                                        <div
                                            class="w-full bg-gray-200/80 dark:bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gray-400/80 dark:bg-gray-500 h-full rounded-full transition-all duration-500"
                                                style="width: {{ max(4, $prevWidth) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 10 Slow-Moving Products matching grid --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3.5rem] p-6 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/80 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-amber-500 dark:text-amber-400 leading-none">
                                10 Produk Stagnan / Slow-Moving</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Perlu Atensi
                                Promosi Bundling atau Rotasi Stok</p>
                        </div>
                        <span
                            class="px-3.5 py-1.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-xl text-[10px] font-black uppercase tracking-wider self-start sm:self-center">
                            Perlu Rotasi
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5">
                        @forelse ($leastSellingProducts as $p)
                            <div
                                class="p-4 bg-gray-50/70 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800/80 text-xs flex flex-col justify-between space-y-3 hover:border-amber-500/30 transition-all">
                                <div>
                                    <span
                                        class="font-black text-gray-800 dark:text-white block truncate text-xs sm:text-sm uppercase tracking-tight"
                                        title="{{ $p->product->name }}">{{ $p->product->name }}</span>
                                    <span
                                        class="text-[10px] text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider block mt-1">Sisa:
                                        {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</span>
                                </div>
                                <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                                    <span
                                        class="text-primary-red font-black block bg-rose-500/10 text-primary-red py-1.5 px-2 rounded-xl text-center border border-rose-500/20 text-xs">
                                        {{ $p->qty_sold }} Terjual
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full p-8 text-center bg-gray-50/50 dark:bg-gray-900/30 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <p class="text-xs text-gray-400 italic">Tidak ada data stok produk yang tercatat pada
                                    rentang minggu ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ==================== STEP 4: AUDIT PIKET HARIAN & CATATAN ==================== --}}
        @elseif($currentStep === 4)
            <div class="space-y-12">
                {{-- Kesimpulan Eksekutif --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 space-y-6">
                    <div>
                        <h2
                            class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">
                            Kesimpulan & Rekomendasi Evaluasi</h2>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Poin Penting
                            Hasil Evaluasi Operasional Mingguan</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- 1. Analisis Penjualan --}}
                        <div
                            class="p-6 bg-blue-50/60 dark:bg-gray-900/60 rounded-3xl border border-blue-100/80 dark:border-blue-900/30 space-y-3">
                            <h4 class="font-black text-primary-blue dark:text-blue-400 text-sm uppercase tracking-wider">
                                1. Analisis Penjualan</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                Puncak omset terjadi pada hari <strong
                                    class="text-gray-900 dark:text-gray-200 font-bold">{{ $peakDay['day'] }}</strong>
                                sebesar Rp{{ number_format($peakDay['revenue']) }}. Dianjurkan menambah program promo
                                khusus pada hari-hari dengan omset lebih rendah.
                            </p>
                        </div>

                        {{-- 2. Disiplin & Tugas Kasir --}}
                        <div
                            class="p-6 bg-emerald-50/60 dark:bg-gray-900/60 rounded-3xl border border-emerald-100/80 dark:border-emerald-900/30 space-y-3">
                            <h4
                                class="font-black text-emerald-600 dark:text-emerald-400 text-sm uppercase tracking-wider">
                                2. Disiplin & Tugas Kasir</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                Kehadiran kasir piket tercapai <strong
                                    class="text-gray-900 dark:text-gray-200 font-bold">{{ $shiftFulfillmentRate }}%</strong>
                                dan tingkat penyelesaian tugas piket yang disetujui sebesar <strong
                                    class="text-gray-900 dark:text-gray-200 font-bold">{{ $taskApprovedRate }}%</strong>.
                            </p>
                        </div>

                        {{-- 3. Rotasi Stok Barang --}}
                        <div
                            class="p-6 bg-amber-50/60 dark:bg-gray-900/60 rounded-3xl border border-amber-100/80 dark:border-amber-900/30 space-y-3">
                            <h4 class="font-black text-amber-600 dark:text-amber-400 text-sm uppercase tracking-wider">
                                3. Rotasi Stok Barang</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                Buat penawaran diskon atau bundling paket untuk 10 barang lambat terjual guna
                                menghindari risiko barang rusak atau kedaluwarsa.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Audit Piket & Tugas per Hari (Accordion Interaktif Fokus Performa Harian & Nested Kasir) --}}
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3.5rem] p-6 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/80 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">
                                Audit Performa & Piket Kasir Harian</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Evaluasi
                                Performa Toko Per Hari dengan Rincian Kasir Terjadwal Piket</p>
                        </div>
                        <span
                            class="px-3.5 py-1.5 bg-blue-500/10 text-primary-blue dark:text-blue-400 border border-blue-500/20 rounded-xl text-[10px] font-black uppercase tracking-wider self-start sm:self-center">
                            {{ count($dailyShiftAudits) }} Hari Terdata
                        </span>
                    </div>

                    {{-- Daily Accordion List --}}
                    <div class="space-y-4 pt-2">
                        @forelse($dailyShiftAudits as $idx => $audit)
                            @php
                                $isDayOpen = ($activeDay === $idx);
                            @endphp
                            <div
                                class="border border-gray-100 dark:border-gray-800/80 rounded-3xl overflow-hidden transition-all duration-300 bg-gray-50/50 dark:bg-gray-900/40">
                                {{-- Accordion Header (Ringkasan Performa Hari Tersebut) --}}
                                <button type="button"
                                    wire:click="toggleDay({{ $idx }})"
                                    class="w-full text-left p-5 sm:p-6 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer hover:bg-gray-100/60 dark:hover:bg-gray-800/60 {{ $isDayOpen ? 'bg-blue-50/30 dark:bg-blue-950/20 border-b border-gray-200/70 dark:border-gray-800' : '' }}">

                                    {{-- Kolom Kiri: Nama Hari, Tanggal, dan Status Ringkas --}}
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center font-black transition-all shrink-0 {{ $isDayOpen ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/30' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200/60 dark:border-gray-700' }}">
                                            <span
                                                class="text-[9px] uppercase tracking-wider leading-none opacity-70">{{ substr($audit['day_name'], 0, 3) }}</span>
                                            <span
                                                class="text-sm font-black leading-none mt-0.5">{{ explode(' ', $audit['date'])[0] }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-sm sm:text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                                    {{ $audit['day_name'] }}, {{ $audit['date'] }}
                                                </span>
                                                <span
                                                    class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $audit['attended_count'] === $audit['scheduled_count'] ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20' }}">
                                                    {{ $audit['attended_count'] }}/{{ $audit['scheduled_count'] }}
                                                    Kasir Hadir
                                                </span>
                                            </div>
                                            <p
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 truncate">
                                                Piket:
                                                {{ collect($audit['cashiers'])->pluck('user.name')->implode(', ') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan: Metrik Performa Hari Itu (Omset & Transaksi) & Icon Accordion --}}
                                    <div
                                        class="flex items-center justify-between md:justify-end gap-6 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-800">
                                        <div class="flex items-center gap-4 sm:gap-6 text-right">
                                            <div>
                                                <span
                                                    class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Omset
                                                    Hari Ini</span>
                                                <span
                                                    class="text-sm sm:text-base font-black text-primary-blue dark:text-primary-blue-light tracking-tight">
                                                    Rp{{ number_format($audit['day_revenue'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div>
                                                <span
                                                    class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Audit Kas (Selisih)</span>
                                                @if($audit['has_audit'])
                                                    @if($audit['cash_diff'] == 0)
                                                        <span class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 flex items-center justify-end gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                            Match (Rp0)
                                                        </span>
                                                    @elseif($audit['cash_diff'] < 0)
                                                        <span class="text-xs sm:text-sm font-black text-rose-600 dark:text-rose-400 flex items-center justify-end gap-1" title="Rugi / Defisit Kas Laci">
                                                            -Rp{{ number_format(abs($audit['cash_diff']), 0, ',', '.') }} (Selisih Loss)
                                                        </span>
                                                    @else
                                                        <span class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 flex items-center justify-end gap-1" title="Surplus / Kelebihan Kas Laci">
                                                            +Rp{{ number_format($audit['cash_diff'], 0, ',', '.') }} (Kas Lebih)
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-xs sm:text-sm font-bold text-gray-400 italic">
                                                        Belum Diaudit
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="hidden sm:block">
                                                <span
                                                    class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Transaksi</span>
                                                <span
                                                    class="text-xs sm:text-sm font-black text-gray-700 dark:text-gray-200">
                                                    {{ $audit['day_tx'] }} Tx
                                                </span>
                                            </div>
                                            <div>
                                                <span
                                                    class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Tugas</span>
                                                <span
                                                    class="text-xs sm:text-sm font-black {{ $audit['task_rate'] >= 80 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                    {{ $audit['task_rate'] }}%
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Toggle Arrow --}}
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform duration-300 bg-white dark:bg-gray-800 text-gray-400 border border-gray-100 dark:border-gray-700 {{ $isDayOpen ? 'rotate-180 text-primary-blue bg-blue-50 dark:bg-blue-950/40' : '' }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </button>

                                {{-- Accordion Body Level 1: Kasir Terjadwal Piket pada Hari Tersebut --}}
                                @if($isDayOpen)
                                    <div class="p-5 sm:p-7 space-y-4 bg-white dark:bg-gray-800/80">
                                        <div
                                            class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                                            <h4 class="text-xs font-black uppercase tracking-widest text-gray-400">
                                                Daftar Kasir Terjadwal Piket ({{ count($audit['cashiers']) }} Orang)
                                            </h4>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">
                                                Total Omset Dicatat:
                                                Rp{{ number_format(collect($audit['cashiers'])->sum('sales_omset'), 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- NESTED ACCORDION: Masing-masing Kasir Memiliki Accordion Lagi --}}
                                        <div class="space-y-3">
                                            @foreach ($audit['cashiers'] as $cIdx => $c)
                                                @php
                                                    $cashierKey = $idx . '-' . $cIdx;
                                                    $isCashierOpen = ($activeCashier === $cashierKey);
                                                @endphp
                                                <div
                                                    class="rounded-2xl border border-gray-100 dark:border-gray-800/80 overflow-hidden bg-gray-50/70 dark:bg-gray-900/60 transition-all">
                                                    
                                                    {{-- Kasir Accordion Header (Klik untuk Buka/Tutup Rincian Tugas) --}}
                                                    <button type="button"
                                                        wire:click="toggleCashier('{{ $cashierKey }}')"
                                                        class="w-full text-left p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer hover:bg-gray-100/70 dark:hover:bg-gray-800/60 transition-all">
                                                        
                                                        <div class="space-y-2 min-w-0 flex-1">
                                                            <div class="flex items-center gap-3 flex-wrap">
                                                                <span
                                                                    class="font-black text-gray-800 dark:text-white text-sm sm:text-base uppercase tracking-tight">
                                                                    {{ $c['user']->name ?? 'Kasir' }}
                                                                </span>
                                                                <span
                                                                    class="px-2.5 py-0.5 rounded-lg font-black text-[10px] uppercase {{ $c['attended'] ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/15 text-primary-red border border-rose-500/20' }}">
                                                                    {{ $c['attended'] ? 'Hadir Piket' : 'Tidak Hadir' }}
                                                                </span>
                                                                @if ($c['attended'] && $c['clock_in'])
                                                                    <span class="text-[10px] font-bold text-gray-400">
                                                                        Masuk: {{ $c['clock_in'] }}
                                                                        ({{ ucfirst(str_replace('_', ' ', $c['clock_in_status'])) }})
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            {{-- Metrik Kasir Pada Hari Itu --}}
                                                            <div
                                                                class="text-gray-400 flex flex-wrap items-center gap-4 sm:gap-6 text-xs font-bold">
                                                                <span>Omset: <strong
                                                                        class="text-gray-800 dark:text-white">Rp{{ number_format($c['sales_omset']) }}</strong></span>
                                                                <span>Transaksi: <strong
                                                                        class="text-gray-800 dark:text-white">{{ $c['sales_tx'] }}
                                                                        Tx</strong></span>
                                                                <span>Tugas: <strong
                                                                        class="text-gray-800 dark:text-white">{{ max(0, $c['assigned_task_count'] - $c['uncompleted_task_count']) }}
                                                                        / {{ $c['assigned_task_count'] }} Selesai</strong></span>
                                                            </div>
                                                        </div>

                                                        {{-- Status Tombol & Arrow Nested Accordion --}}
                                                        <div class="flex items-center gap-3 self-end md:self-center shrink-0">
                                                            <span class="text-[10px] font-black uppercase tracking-widest text-primary-blue dark:text-blue-400">
                                                                Rincian Tugas ({{ count($c['task_details']) }})
                                                            </span>
                                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-transform duration-200 bg-white dark:bg-gray-800 text-gray-400 border border-gray-200/60 dark:border-gray-700 {{ $isCashierOpen ? 'rotate-180 text-primary-blue' : '' }}">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </button>

                                                    {{-- Kasir Accordion Body Level 2: Rincian Lengkap Tugas Kasir --}}
                                                    @if($isCashierOpen)
                                                        <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-gray-800 bg-white/60 dark:bg-gray-900/80 space-y-3">
                                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                                                Daftar Check-List Tugas Kasir ({{ $c['user']->name ?? 'Kasir' }}):
                                                            </div>

                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                                @forelse ($c['task_details'] as $t)
                                                                    <div class="p-3 rounded-xl border {{ $t['status'] === 'Disetujui' ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-500/20' : 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-500/20' }} flex items-center justify-between gap-3">
                                                                        <div class="min-w-0">
                                                                            <p class="text-xs font-bold text-gray-800 dark:text-gray-100 truncate">
                                                                                {{ $t['task_name'] }}
                                                                            </p>
                                                                            @if(!empty($t['rejection_note']))
                                                                                <p class="text-[10px] text-primary-red font-semibold mt-0.5">
                                                                                    Catatan: {{ $t['rejection_note'] }}
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                        <span class="shrink-0 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase {{ $t['status'] === 'Disetujui' ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/20 text-amber-600 dark:text-amber-400' }}">
                                                                            {{ $t['status'] }}
                                                                        </span>
                                                                    </div>
                                                                @empty
                                                                    <div class="col-span-full py-2 text-center text-xs text-gray-400 italic">
                                                                        Tidak ada tugas penugasan khusus pada hari ini.
                                                                    </div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @empty
                                <div
                                    class="p-8 text-center bg-gray-50 dark:bg-gray-900/40 rounded-3xl border border-gray-100 dark:border-gray-800">
                                    <p class="text-xs text-gray-400 italic">Belum ada data audit jadwal untuk rentang
                                        tanggal ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            {{-- ==================== STEP 5: KRITIK, SARAN & DISKUSI TIM ==================== --}}
            @elseif($currentStep === 5)
                <div class="space-y-10">
                    {{-- Header / Context Banner --}}
                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-primary-blue rounded-[3rem] p-8 sm:p-12 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden">
                        <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
                            <svg class="w-56 h-56 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div class="max-w-2xl space-y-3">
                            <span class="px-3.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-widest inline-block">
                                Forum Terbuka & Tanya Jawab
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-black uppercase tracking-tight text-white leading-tight">
                                Kritik, Saran & Tanya Jawab Tim Kasir
                            </h2>
                            <p class="text-xs sm:text-sm text-white/80 font-medium leading-relaxed">
                                Ruang diskusi interaktif untuk menampung masukan langsung dari kasir piket mengenai kemudahan sistem, kendala teknis, serta komunikasi dengan pengelola.
                            </p>
                        </div>
                    </div>

                    {{-- 3 Kartu Panduan Diskusi Interaktif --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Card 1: Sistem & Aplikasi POS --}}
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between space-y-6 group hover:border-primary-blue/40 transition-all">
                            <div class="space-y-4">
                                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-primary-blue dark:text-blue-400 flex items-center justify-center border border-blue-500/20 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 dark:text-white">
                                    Sistem & Aplikasi POS
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                                    Apakah ada bug, fitur kasir yang membingungkan, kendala cetak struk, atau alur transaksi yang terasa lambat saat jam ramai?
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700/80">
                                <span class="text-[10px] font-black uppercase tracking-widest text-primary-blue dark:text-blue-400 block">
                                    Topik: UX Kasir, Kecepatan & Stabilitas
                                </span>
                            </div>
                        </div>

                        {{-- Card 2: Pengelola & Admin --}}
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between space-y-6 group hover:border-emerald-500/40 transition-all">
                            <div class="space-y-4">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 dark:text-white">
                                    Koordinasi Tim & Admin
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                                    Bagaimana alur komunikasi dengan Admin/Pengelola? Apakah persetujuan tugas, stok opname, dan penanganan uang kas sudah responsif?
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700/80">
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 block">
                                    Topik: Respon Admin & Sinkronisasi Data
                                </span>
                            </div>
                        </div>

                        {{-- Card 3: Piket & Operasional Harian --}}
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between space-y-6 group hover:border-amber-500/40 transition-all">
                            <div class="space-y-4">
                                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 dark:text-white">
                                    Tanya Jawab Piket & Jadwal
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                                    Ada pertanyaan terkait pembagian shift piket, bobot tugas rutin harian, atau jam kedatangan absen yang belum jelas?
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700/80">
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 block">
                                    Topik: Pembagian Shift, Absensi & Beban Tugas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- ==================== STEP 6: PENUTUP & SESI EVALUASI LANGSUNG ==================== --}}
            @elseif($currentStep === 6)
                <div class="space-y-10">
                    {{-- Hero Banner Penutup --}}
                    <div class="bg-primary-blue rounded-[3rem] p-8 sm:p-14 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden text-center space-y-6">
                        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                            <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 text-white text-xs font-black uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Sesi Langsung Sedang Berlangsung
                        </div>
                        <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight text-white leading-tight">
                            Penutup & Sesi Evaluasi Tatap Muka
                        </h2>
                        <p class="text-sm sm:text-base font-medium text-white/80 max-w-2xl mx-auto leading-relaxed">
                            Pemaparan data performa mingguan toko telah selesai. Sekarang adalah waktu untuk berdiskusi, memberikan apresiasi, dan menetapkan kesepakatan bersama secara langsung.
                        </p>
                    </div>

                    {{-- Agenda Sesi Evaluasi Tatap Muka --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Checklist Agenda Tatap Muka --}}
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3rem] p-8 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/80 space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 dark:text-white">
                                        Panduan Alur Evaluasi Langsung
                                    </h3>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Langkah Diskusi Bersama Tim Kasir</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800">
                                    <span class="w-7 h-7 rounded-lg bg-primary-blue text-white text-xs font-black flex items-center justify-center shrink-0 mt-0.5">1</span>
                                    <div>
                                        <h4 class="text-xs font-black uppercase text-gray-800 dark:text-white">Apresiasi Kasir Terbaik</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 font-medium">Beri apresiasi terbuka bagi kasir Top 3 dengan disiplin piket dan omset tertinggi minggu ini.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800">
                                    <span class="w-7 h-7 rounded-lg bg-primary-blue text-white text-xs font-black flex items-center justify-center shrink-0 mt-0.5">2</span>
                                    <div>
                                        <h4 class="text-xs font-black uppercase text-gray-800 dark:text-white">Klarifikasi Kasir Perlu Perbaikan</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 font-medium">Dengarkan kendala personal/jadwal secara suportif bagi kasir yang tugasnya tertunda atau terlambat.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800">
                                    <span class="w-7 h-7 rounded-lg bg-primary-blue text-white text-xs font-black flex items-center justify-center shrink-0 mt-0.5">3</span>
                                    <div>
                                        <h4 class="text-xs font-black uppercase text-gray-800 dark:text-white">Komitmen Target Minggu Depan</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 font-medium">Sepakati bersama target omset, jadwal piket yang tidak bentrok, dan penyelesaian tugas tepat waktu.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ringkasan Komitmen & Tindak Lanjut --}}
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] sm:rounded-[3rem] p-8 sm:p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/80 flex flex-col justify-between space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-black">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 dark:text-white">
                                            Penetapan Tindak Lanjut
                                        </h3>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Hasil Musyawarah Mingguan</p>
                                    </div>
                                </div>

                                <div class="p-5 rounded-2xl bg-blue-50/60 dark:bg-gray-900/60 border border-blue-100/80 dark:border-blue-900/30 space-y-2">
                                    <h4 class="text-xs font-black uppercase text-primary-blue dark:text-blue-400 tracking-wider">
                                        Catatan Penting Tim:
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed font-medium">
                                        Seluruh catatan evaluasi dan usulan dari sesi tatap muka ini menjadi acuan kerja untuk meningkatkan efektivitas kasir dan kenyamanan belanja pelanggan di periode berikutnya.
                                    </p>
                                </div>
                            </div>

                            <div class="text-center pt-6 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                    "Kerja sama yang solid adalah kunci keberhasilan pelayanan toko kita."
                                </p>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider">
                                    Terima kasih atas dedikasi dan kerja keras seluruh tim kasir!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- BOTTOM WIZARD NAVIGATION BAR MATCHING 2.5rem CORNERS --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4 mt-12">
            {{-- Tombol Sebelumnya --}}
            <div>
                @if ($currentStep > 1)
                    <button wire:click="prevStep"
                        class="flex items-center gap-3 px-6 py-3.5 bg-gray-100 dark:bg-gray-700/60 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Sebelumnya</span>
                    </button>
                @else
                    <span class="text-xs font-black uppercase tracking-widest text-gray-400 pl-2">Tahap Awal</span>
                @endif
            </div>

            {{-- Step Indicator Dots (6 Steps) --}}
            <div class="flex items-center gap-2.5">
                @for ($st = 1; $st <= 6; $st++)
                    <button wire:click="setStep({{ $st }})"
                        class="h-2.5 rounded-full transition-all cursor-pointer {{ $currentStep === $st ? 'bg-primary-blue w-10' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 w-2.5' }}"
                        title="Ke Tahap {{ $st }}">
                    </button>
                @endfor
            </div>

            {{-- Tombol Selanjutnya / Kembali ke Awal --}}
            <div>
                @if ($currentStep < 6)
                    <button wire:click="nextStep"
                        class="flex items-center gap-3 px-8 py-3.5 bg-primary-blue hover:bg-blue-600 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-xl shadow-blue-500/20 cursor-pointer">
                        <span>Selanjutnya</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <button wire:click="setStep(1)"
                        class="flex items-center gap-3 px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-xl shadow-emerald-600/20 cursor-pointer">
                        <span>Selesai & Ke Tahap 1</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        {{-- MODAL AUDIT KASIR DETAIL --}}
        @if ($showCashierDetailModal && $modalCashierData)
            <div x-data="{ activeAccordion: null }" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">

                <div x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="bg-white dark:bg-gray-800 w-full max-w-xl rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col max-h-[85vh]">

                    {{-- Modal Header --}}
                    <div
                        class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50 shrink-0">
                        <div>
                            <h3 class="font-black text-gray-800 dark:text-white text-base uppercase tracking-tight">Detail
                                Log Harian: {{ $modalCashierData['user']->name }}</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Rekap Kehadiran,
                                Tugas, & Catatan Piket</p>
                        </div>
                        <button wire:click="closeCashierDetailModal"
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl transition-all cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body: Scrollable with Smooth Accordion Per Day --}}
                    <div
                        class="p-6 overflow-y-auto space-y-3 text-xs flex-1 divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($modalCashierData['daily_breakdown'] as $idx => $dayLog)
                            @php
                                $hasContent =
                                    $dayLog['attendance'] || count($dayLog['tasks']) > 0 || $dayLog['sales_omset'] > 0;
                            @endphp
                            <div class="pt-3 first:pt-0">
                                {{-- Accordion Header / Trigger Button --}}
                                <button type="button"
                                    @click="activeAccordion = (activeAccordion === {{ $idx }} ? null : {{ $idx }})"
                                    class="w-full text-left p-3.5 rounded-2xl transition-all flex items-center justify-between gap-3 cursor-pointer {{ $dayLog['attendance'] ? 'bg-emerald-50/50 dark:bg-emerald-950/20 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' : 'bg-gray-50 dark:bg-gray-900/40 hover:bg-gray-100 dark:hover:bg-gray-900/70' }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span
                                            class="w-2 h-2 rounded-full shrink-0 {{ $dayLog['attendance'] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight truncate">
                                                    {{ $dayLog['day_name'] }} ({{ $dayLog['date'] }})
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-gray-400 font-bold block mt-0.5">
                                                Omset: Rp{{ number_format($dayLog['sales_omset'] / 1000, 0) }}k •
                                                {{ count($dayLog['tasks']) }} Tugas
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider {{ $dayLog['attendance'] ? 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-500/20' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                            {{ $dayLog['attendance'] ? 'Absen Masuk' : 'Tidak Absen' }}
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                            :class="activeAccordion === {{ $idx }} ? 'rotate-180 text-primary-blue' :
                                                ''"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                {{-- Accordion Collapsible Panel Content --}}
                                <div x-show="activeAccordion === {{ $idx }}" x-collapse
                                    class="mt-2.5 px-4 py-3 bg-gray-50 dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-3">

                                    {{-- Sales & Attendance Meta --}}
                                    <div
                                        class="grid grid-cols-2 gap-2 text-[11px] font-bold text-gray-500 dark:text-gray-400">
                                        <div>Omset: <strong
                                                class="text-gray-800 dark:text-white">Rp{{ number_format($dayLog['sales_omset']) }}</strong>
                                            ({{ $dayLog['sales_count'] }} Tx)</div>
                                        <div>Piket: <strong
                                                class="text-gray-800 dark:text-white">{{ $dayLog['is_scheduled'] ? 'Terjadwal' : 'Tidak Ada Jadwal' }}</strong>
                                        </div>
                                    </div>

                                    {{-- Task List --}}
                                    @if (count($dayLog['tasks']) > 0)
                                        <div class="pt-2 border-t border-gray-200 dark:border-gray-800 space-y-1.5">
                                            <span
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Daftar
                                                Tugas:</span>
                                            @foreach ($dayLog['tasks'] as $t)
                                                <div
                                                    class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between gap-2">
                                                    <span class="truncate">•
                                                        {{ $t->taskDefinition->task_name ?? 'Tugas' }}</span>
                                                    <span
                                                        class="font-bold text-[10px] shrink-0 {{ $t->latestSubmission && $t->latestSubmission->approval_status === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                        {{ $t->latestSubmission ? ucfirst($t->latestSubmission->approval_status) : 'Belum Submit' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div
                                            class="pt-2 border-t border-gray-200 dark:border-gray-800 text-[10px] text-gray-400 italic">
                                            Tidak ada penugasan tugas piket pada hari ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Modal Footer --}}
                    <div
                        class="p-5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 text-right shrink-0">
                        <button wire:click="closeCashierDetailModal"
                            class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-black rounded-xl text-xs uppercase tracking-wider transition-all cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
