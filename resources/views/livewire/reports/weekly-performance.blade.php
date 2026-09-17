<div class="p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 bg-slate-100 dark:bg-slate-950 min-h-screen text-slate-900 dark:text-slate-100">
    {{-- Header & Week Selection --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-xl shrink-0 border border-blue-200 dark:border-blue-900/50">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">Performa Penjualan & Kasir Mingguan</h1>
                <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 mt-0.5">Laporan evaluasi omset toko, audit tugas piket harian, & stok barang</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            {{-- Week Select --}}
            <div class="relative w-full sm:w-auto">
                <select wire:model.live="selectedWeekDate" class="w-full sm:w-auto appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-bold rounded-xl px-4 py-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                    @foreach($availableWeeks as $w)
                        <option value="{{ $w['date'] }}">{{ $w['label'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Toggle Presentation Mode Button --}}
            <button wire:click="togglePresentationMode" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12H4z" />
                </svg>
                <span>Mode Presentasi (Deck)</span>
            </button>
        </div>
    </div>

    {{-- PRESENTATION MODE SLIDE DECK (HIGH CONTRAST SOLID DESIGN) --}}
    @if($isPresentationMode)
        <div class="fixed inset-0 z-50 bg-slate-950 flex flex-col p-4 sm:p-8 text-white animate-fadeIn overflow-hidden">
            {{-- Slide Header --}}
            <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-blue-600/30 border border-blue-500/50 text-blue-400 text-xs font-extrabold uppercase rounded-lg tracking-wider">Slide {{ $activeSlide }} dari 4</span>
                    <h2 class="text-base sm:text-xl font-black text-white">
                        @if($activeSlide === 1) Slide 1: Ringkasan Omset & Profit Mingguan
                        @elseif($activeSlide === 2) Slide 2: Evaluasi Kasir & Kepatuhan Tugas Piket
                        @elseif($activeSlide === 3) Slide 3: Analisis Produk Terlaris vs Stagnan
                        @elseif($activeSlide === 4) Slide 4: Kesimpulan & Rekomendasi Rapat Evaluasi
                        @endif
                    </h2>
                </div>

                <div class="flex items-center gap-2">
                    <button wire:click="prevSlide" @disabled($activeSlide <= 1) class="px-4 py-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 text-white rounded-lg text-xs font-bold">← Sebelum</button>
                    <button wire:click="nextSlide" @disabled($activeSlide >= 4) class="px-4 py-2 bg-blue-600 hover:bg-blue-500 disabled:opacity-30 text-white rounded-lg text-xs font-bold">Berikut →</button>
                    <button wire:click="togglePresentationMode" class="ml-3 p-2 bg-rose-950 text-rose-300 border border-rose-800 hover:bg-rose-900 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Slide Content --}}
            <div class="flex-1 overflow-y-auto space-y-6 pr-2">
                @if($activeSlide === 1)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-slate-900 border border-slate-800 p-5 rounded-xl">
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Total Omset Mingguan</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                            <span class="text-xs font-semibold {{ $revenueGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }} block mt-2">
                                {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }} vs minggu lalu
                            </span>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 p-5 rounded-xl">
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Estimasi Net Profit</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-emerald-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                            <span class="text-xs text-slate-400 block mt-2">Margin Keuntungan Toko</span>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 p-5 rounded-xl">
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Total Transaksi</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-purple-400">{{ number_format($totalTransactions) }} <span class="text-sm font-semibold text-slate-400">Struk</span></h3>
                            <span class="text-xs text-slate-400 block mt-2">Rata: Rp {{ number_format($avgBasketSize) }}/struk</span>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 p-5 rounded-xl">
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Hari Puncak Omset</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-amber-400">{{ $peakDay['day'] }}</h3>
                            <span class="text-xs text-slate-400 block mt-2">Rp {{ number_format($peakDay['revenue']) }} ({{ $peakDay['transactions'] }} Tx)</span>
                        </div>
                    </div>

                    {{-- Presentation Chart --}}
                    <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl">
                        <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-4">Grafik Penjualan Harian</h4>
                        <div class="h-64 flex items-end justify-between gap-3 pt-8 pb-4 border-b border-slate-800">
                            @foreach($dailySales as $day)
                                @php $barPct = $maxDailyRevenue > 0 ? max(12, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 12; @endphp
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <span class="text-xs font-bold text-blue-400">Rp {{ number_format($day['revenue'] / 1000, 0) }}k</span>
                                    <div class="w-full bg-blue-600 rounded-t-lg transition-all" style="height: {{ $barPct }}%"></div>
                                    <span class="text-xs font-bold text-slate-300">{{ substr($day['day_name'], 0, 3) }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $day['transactions'] }} Tx</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                @elseif($activeSlide === 2)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-900 border border-emerald-900/50 p-6 rounded-xl space-y-4">
                            <h3 class="text-base font-black text-emerald-400 uppercase tracking-wider">Top 3 Kasir Terbaik (Bintang Piket)</h3>
                            <div class="space-y-3">
                                @forelse($topPerformers as $idx => $item)
                                    <div class="flex items-center justify-between p-4 bg-slate-800 rounded-lg border border-slate-700">
                                        <div>
                                            <h4 class="font-bold text-white text-sm">#{{ $idx + 1 }} {{ $item->user->name }}</h4>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $item->total_tx }} Transaksi | Omset Rp {{ number_format($item->total_sales) }}</p>
                                        </div>
                                        <span class="px-3 py-1 bg-emerald-950 text-emerald-300 border border-emerald-800 text-xs font-black rounded-lg">{{ $item->overall_score }} Pts</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400">Belum ada data kasir.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-slate-900 border border-rose-900/50 p-6 rounded-xl space-y-4">
                            <h3 class="text-base font-black text-rose-400 uppercase tracking-wider">Kasir Perlu Evaluasi & Catatan Tugas</h3>
                            <div class="space-y-3">
                                @forelse($bottomPerformers as $item)
                                    <div class="p-4 bg-slate-800 rounded-lg border border-slate-700 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-bold text-white text-sm">{{ $item->user->name }}</h4>
                                            <span class="px-2.5 py-0.5 bg-rose-950 text-rose-300 border border-rose-800 text-xs font-bold rounded-lg">Skor: {{ $item->overall_score }}</span>
                                        </div>
                                        <p class="text-xs text-rose-300 font-semibold">{{ $item->evaluation_notes }}</p>
                                        <p class="text-[11px] text-slate-400">Omset: Rp {{ number_format($item->total_sales) }} ({{ $item->total_tx }} Tx)</p>
                                    </div>
                                @empty
                                    <p class="text-xs text-emerald-400">Semua kasir memiliki kinerja baik!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 3)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-4">
                            <h3 class="text-base font-black text-emerald-400 uppercase tracking-wider">5 Produk Paling Laku (Top Selling)</h3>
                            <div class="space-y-3">
                                @foreach($topSellingProducts as $p)
                                    <div class="flex items-center justify-between p-3.5 bg-slate-800 rounded-lg">
                                        <div>
                                            <h4 class="font-bold text-white text-sm">{{ $p->product->name }}</h4>
                                            <p class="text-xs text-slate-400">Kategori: {{ $p->product->category->name ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-emerald-400 font-black text-sm block">{{ $p->qty_sold }} Terjual</span>
                                            <span class="text-xs text-slate-400">Rp {{ number_format($p->omset) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-4">
                            <h3 class="text-base font-black text-amber-400 uppercase tracking-wider">5 Produk Kurang Laku / Stagnan</h3>
                            <div class="space-y-3">
                                @foreach($leastSellingProducts as $p)
                                    <div class="flex items-center justify-between p-3.5 bg-slate-800 rounded-lg">
                                        <div>
                                            <h4 class="font-bold text-white text-sm">{{ $p->product->name }}</h4>
                                            <p class="text-xs text-amber-400 font-medium">Stok Tersisa: {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-rose-400 font-black text-sm block">{{ $p->qty_sold }} Terjual</span>
                                            <span class="text-xs text-slate-400">Slow Moving</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 4)
                    <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl space-y-6">
                        <h3 class="text-xl font-black text-white border-b border-slate-800 pb-3">Kesimpulan & Catatan Evaluasi Rapat Mingguan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-5 bg-slate-800 rounded-lg border border-slate-700">
                                <h4 class="font-bold text-blue-400 text-sm mb-2">1. Evaluasi Penjualan</h4>
                                <p class="text-xs text-slate-300 leading-relaxed">Puncak omset terjadi pada hari <strong class="text-white">{{ $peakDay['day'] }}</strong> sebesar Rp {{ number_format($peakDay['revenue']) }}. Tingkatkan promosi pada hari sepi.</p>
                            </div>
                            <div class="p-5 bg-slate-800 rounded-lg border border-slate-700">
                                <h4 class="font-bold text-emerald-400 text-sm mb-2">2. Kepatuhan Kasir</h4>
                                <p class="text-xs text-slate-300 leading-relaxed">Kehadiran kasir piket di angka <strong class="text-white">{{ $shiftFulfillmentRate }}%</strong> dan persentase kelengkapan tugas disetujui sebesar <strong class="text-white">{{ $taskApprovedRate }}%</strong>.</p>
                            </div>
                            <div class="p-5 bg-slate-800 rounded-lg border border-slate-700">
                                <h4 class="font-bold text-amber-400 text-sm mb-2">3. Manajemen Persediaan</h4>
                                <p class="text-xs text-slate-300 leading-relaxed">Lakukan evaluasi stok barang slow-moving untuk mencegah penumpukan modal dan resiko barang rusak.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer Indicators --}}
            <div class="flex items-center justify-center gap-2 pt-4 border-t border-slate-800 shrink-0">
                @for($s = 1; $s <= 4; $s++)
                    <button wire:click="setSlide({{ $s }})" class="w-3 h-3 rounded-full transition-all {{ $activeSlide === $s ? 'bg-blue-500 w-8' : 'bg-slate-700 hover:bg-slate-600' }}"></button>
                @endfor
            </div>
        </div>
    @endif

    {{-- REGULAR DASHBOARD VIEW (LIGHT & DARK CONTRAST MATCH) --}}

    {{-- SECTION 1: EXECUTIVE KEY METRICS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Omset --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Omset</span>
                <div class="p-2 bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900/50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <div class="mt-2 text-xs font-semibold flex items-center gap-1.5">
                <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                    {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }}
                </span>
                <span class="text-slate-500 dark:text-slate-400 font-normal">dibanding minggu lalu</span>
            </div>
        </div>

        {{-- Card 2: Net Profit --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Net Profit Toko</span>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            <div class="mt-2 text-xs font-semibold flex items-center gap-1.5">
                <span class="{{ $profitGrowth >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                    {{ $profitGrowth >= 0 ? '+'.$profitGrowth.'%' : $profitGrowth.'%' }}
                </span>
                <span class="text-slate-500 dark:text-slate-400 font-normal">margin keuntungan</span>
            </div>
        </div>

        {{-- Card 3: Transaksi & Basket --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Transaksi</span>
                <div class="p-2 bg-purple-50 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-900/50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalTransactions) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Struk</span></h3>
            <div class="mt-2 text-xs text-slate-600 dark:text-slate-400 font-medium">
                Rata: <strong class="text-slate-900 dark:text-slate-200">Rp {{ number_format($avgBasketSize) }}</strong> / belanja
            </div>
        </div>

        {{-- Card 4: Kehadiran Piket & Kepatuhan --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kepatuhan Piket</span>
                <div class="p-2 bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $shiftFulfillmentRate }}%</h3>
            <div class="mt-2 text-xs text-slate-600 dark:text-slate-400 font-medium">
                Tugas: <strong class="text-emerald-700 dark:text-emerald-400">{{ $taskApprovedRate }}%</strong> Disetujui
            </div>
        </div>
    </div>

    {{-- SECTION 2: HIGH CONTRAST VISIBLE CHART HARIAN --}}
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Tren Penjualan Harian (Senin - Minggu)</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Rincian omset penjualan dan jumlah transaksi harian sepanjang minggu</p>
            </div>
            <div class="self-start sm:self-auto px-4 py-2 bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900/50 rounded-xl text-xs font-bold">
                Puncak Omset: {{ $peakDay['day'] }} (Rp {{ number_format($peakDay['revenue']) }})
            </div>
        </div>

        {{-- VISIBLE BAR CHART --}}
        <div class="pt-6 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div class="h-56 flex items-end justify-between gap-2 sm:gap-4 px-2">
                @foreach($dailySales as $day)
                    @php 
                        $barPct = $maxDailyRevenue > 0 ? max(15, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 15;
                        $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                        {{-- Omset Rp Label above bar --}}
                        <span class="text-[11px] font-extrabold {{ $isPeak ? 'text-blue-700 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300' }}">
                            Rp {{ number_format($day['revenue'] / 1000, 0) }}k
                        </span>

                        {{-- Bar container --}}
                        <div class="w-full max-w-[48px] rounded-t-lg transition-all duration-300 {{ $isPeak ? 'bg-blue-600 dark:bg-blue-500' : 'bg-slate-300 dark:bg-slate-700 group-hover:bg-blue-500' }}" style="height: {{ $barPct }}%">
                        </div>

                        {{-- Day Name & Tx Count below bar --}}
                        <div class="text-center pt-1">
                            <span class="text-xs font-bold block text-slate-900 dark:text-white">{{ $day['day_name'] }}</span>
                            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">{{ $day['transactions'] }} Tx</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SECTION 3: AUDIT PIKET & TUGAS KASIR HARIAN --}}
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
        <div>
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Audit Piket & Tugas Kasir Harian
            </h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Rincian faktual kasir yang piket di tiap hari, status kehadiran, omset, & kelengkapan tugasnya</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($dailyShiftAudits as $audit)
                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                        <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $audit['day_name'] }}</span>
                        <span class="text-xs text-slate-600 dark:text-slate-400 font-semibold">{{ $audit['date'] }}</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($audit['cashiers'] as $c)
                            <div class="p-3 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $c['user']->name }}</span>
                                    @if($c['attended'])
                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-bold rounded">Hadir {{ $c['clock_in'] ?? '' }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-[10px] font-bold rounded">Tidak Absen</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                                    <span>Omset POS: <strong class="text-slate-900 dark:text-white">Rp {{ number_format($c['sales_omset']) }}</strong></span>
                                    <span>{{ $c['sales_tx'] }} Tx</span>
                                </div>

                                {{-- Task Status List --}}
                                @if(!empty($c['task_details']) && count($c['task_details']) > 0)
                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1">
                                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Tugas Piket:</span>
                                        @foreach($c['task_details'] as $t)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-slate-700 dark:text-slate-300 truncate max-w-[140px]">• {{ $t['task_name'] }}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $t['badge_class'] }}">
                                                    {{ $t['status'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="pt-1 text-[11px] text-slate-500 dark:text-slate-400 italic">Tidak ada penugasan khusus.</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-xs text-slate-500 dark:text-slate-400">Belum ada data jadwal piket kasir pada minggu ini.</div>
            @endforelse
        </div>
    </div>

    {{-- SECTION 4: PAPAN EVALUASI KASIR MINGGUAN --}}
    <div class="space-y-5">
        <div>
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Papan Evaluasi Kasir Piket</h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Analisis akumulasi kinerja individu kasir meliputi omset penjualan, kedisiplinan piket, & kelengkapan tugas</p>
        </div>

        {{-- Top 3 Highlight Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($topPerformers as $index => $cashier)
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-500 text-slate-950 font-black text-sm rounded-xl flex items-center justify-center shrink-0">
                                #{{ $index + 1 }}
                            </div>
                            <div class="truncate">
                                <h4 class="font-extrabold text-slate-900 dark:text-white text-sm truncate">{{ $cashier->user->name }}</h4>
                                <span class="text-xs text-amber-700 dark:text-amber-400 font-bold">Top Kasir #{{ $index + 1 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block font-medium">Omset POS</span>
                            <strong class="text-slate-900 dark:text-white font-black">Rp {{ number_format($cashier->total_sales) }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block font-medium">Transaksi</span>
                            <strong class="text-slate-900 dark:text-white font-black">{{ $cashier->total_tx }} Tx</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block font-medium">Absensi</span>
                            <strong class="text-emerald-700 dark:text-emerald-400 font-bold">{{ $cashier->attended_count }} Shift</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block font-medium">Tugas</span>
                            <strong class="text-blue-700 dark:text-blue-400 font-bold">{{ $cashier->approved_tasks }} Disetujui</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Full Table --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h4 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm">Rincian Kinerja Seluruh Kasir Mingguan</h4>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ $cashierPerformanceList->count() }} Kasir Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[750px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-5 py-3.5">Kasir Piket</th>
                            <th class="px-5 py-3.5">Omset Penjualan</th>
                            <th class="px-5 py-3.5">Transaksi</th>
                            <th class="px-5 py-3.5">Absensi Piket</th>
                            <th class="px-5 py-3.5">Status Tugas</th>
                            <th class="px-5 py-3.5">Catatan Evaluasi</th>
                            <th class="px-5 py-3.5 text-center">Skor</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                        @forelse($cashierPerformanceList as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 dark:text-white block">{{ $item->user->name }}</span>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $item->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-black text-slate-900 dark:text-white">
                                    Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $item->total_tx }} Struk
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-800 rounded font-bold text-[11px]">{{ $item->on_time_count }} Tepat Waktu</span>
                                        @if($item->late_count > 0)
                                            <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/80 dark:text-rose-300 dark:border-rose-800 rounded font-bold text-[11px]">{{ $item->late_count }} Terlambat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-0.5">
                                        <span class="text-emerald-700 dark:text-emerald-400 font-bold block">{{ $item->approved_tasks }} Disetujui</span>
                                        @if($item->unsubmitted_tasks > 0)
                                            <span class="text-rose-700 dark:text-rose-400 font-bold text-[11px] block">{{ $item->unsubmitted_tasks }} Belum Dilaporkan</span>
                                        @endif
                                        @if($item->rejected_tasks > 0)
                                            <span class="text-rose-700 dark:text-rose-400 font-bold text-[11px] block">{{ $item->rejected_tasks }} Ditolak</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-xs font-medium {{ $item->unsubmitted_tasks > 0 || $item->rejected_tasks > 0 ? 'text-rose-700 dark:text-rose-300 font-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                        {{ $item->evaluation_notes }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800 font-black rounded-lg">
                                        {{ $item->overall_score }} Pts
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button wire:click="viewCashierDetail('{{ $item->user->id }}')" class="px-3.5 py-1.5 bg-slate-100 hover:bg-blue-600 hover:text-white dark:bg-slate-800 dark:hover:bg-blue-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-lg transition">
                                        Audit Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada data kasir pada minggu ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SECTION 5: BARANG TERLARIS VS STAGNAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Top Selling --}}
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50 rounded-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">5 Barang Paling Laku (Top Selling)</h3>
                </div>
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Terlaris</span>
            </div>

            <div class="space-y-3">
                @forelse($topSellingProducts as $item)
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl flex items-center justify-between border border-slate-200 dark:border-slate-800">
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $item->product->name }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kat: {{ $item->product->category->name ?? '-' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-emerald-700 dark:text-emerald-400 font-black text-sm block">{{ $item->qty_sold }} Terjual</span>
                            <span class="text-xs text-slate-600 dark:text-slate-400 font-semibold">Omset: Rp {{ number_format($item->omset) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 dark:text-slate-400 py-4 text-center">Belum ada transaksi barang pada minggu ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Least Selling --}}
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 rounded-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">5 Barang Kurang Laku / Stagnan</h3>
                </div>
                <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Evaluasi Stok</span>
            </div>

            <div class="space-y-3">
                @forelse($leastSellingProducts as $item)
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl flex items-center justify-between border border-slate-200 dark:border-slate-800">
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $item->product->name }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Stok Tersedia: <strong class="text-amber-700 dark:text-amber-400">{{ $item->stock }} {{ $item->product->unit ?? 'pcs' }}</strong></p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-rose-700 dark:text-rose-400 font-black text-sm block">{{ $item->qty_sold }} Terjual</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Slow Moving</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 dark:text-slate-400 py-4 text-center">Semua barang terjual dengan baik.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DETAIL AUDIT MODAL --}}
    @if($showCashierDetailModal && $modalCashierData)
        <div class="fixed inset-0 z-50 bg-slate-950/80 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 max-w-3xl w-full rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] flex flex-col overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shrink-0">
                            {{ strtoupper(substr($modalCashierData['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-black text-slate-900 dark:text-white text-base sm:text-lg">{{ $modalCashierData['user']->name }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Audit Detail Piket, Absensi, Penjualan, & Tugas Mingguan</p>
                        </div>
                    </div>
                    <button wire:click="closeCashierDetailModal" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 overflow-y-auto space-y-4 text-xs">
                    @foreach($modalCashierData['daily_breakdown'] as $day)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                                <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $day['day_name'] }} ({{ $day['date'] }})</span>
                                @if($day['is_scheduled'])
                                    <span class="px-2.5 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800 font-bold rounded text-xs">Terdaftar Piket</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded text-xs">Bukan Jadwal Piket</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block font-medium">Status Absensi</span>
                                    @if($day['attendance'])
                                        <strong class="text-emerald-700 dark:text-emerald-400 font-bold">Hadir {{ $day['attendance']->clock_in ? \Carbon\Carbon::parse($day['attendance']->clock_in)->format('H:i') : '' }}</strong>
                                    @else
                                        <strong class="text-rose-700 dark:text-rose-400 font-bold">Tidak Absen / Kosong</strong>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block font-medium">Penjualan POS</span>
                                    <strong class="text-slate-900 dark:text-white font-black">Rp {{ number_format($day['sales_omset']) }} ({{ $day['sales_count'] }} Tx)</strong>
                                </div>
                            </div>

                            {{-- Tasks --}}
                            <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs">Daftar Tugas & Pelaporan:</span>
                                @forelse($day['tasks'] as $asg)
                                    @php $sub = $asg->latestSubmission; @endphp
                                    <div class="p-3 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block text-xs">{{ $asg->taskDefinition->task_name ?? 'Tugas Piket' }}</span>
                                            @if($sub && $sub->rejection_note)
                                                <span class="text-rose-700 dark:text-rose-400 text-xs block font-semibold mt-0.5">Alasan Ditolak: {{ $sub->rejection_note }}</span>
                                            @endif
                                        </div>
                                        <div class="shrink-0">
                                            @if(!$sub)
                                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800 font-bold text-xs rounded">Belum Dilaporkan</span>
                                            @elseif($sub->approval_status === 'approved')
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800 font-bold text-xs rounded">Disetujui</span>
                                            @elseif($sub->approval_status === 'rejected')
                                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800 font-bold text-xs rounded">Ditolak</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800 font-bold text-xs rounded">Menunggu Review</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-slate-500 dark:text-slate-400 italic text-xs">Tidak ada tugas khusus.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800 text-right">
                    <button wire:click="closeCashierDetailModal" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
