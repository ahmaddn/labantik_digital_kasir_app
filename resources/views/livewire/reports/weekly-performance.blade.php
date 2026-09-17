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

            {{-- Toggle Presentation Mode Button --}}
            <button wire:click="togglePresentationMode" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-blue hover:bg-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/15 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12H4z" />
                </svg>
                <span class="whitespace-nowrap">Mode Presentasi (Deck)</span>
            </button>
        </div>
    </div>

    {{-- PRESENTATION MODE SLIDE DECK (TRUE FULLSCREEN EXECUTABLE DECK) --}}
    @if($isPresentationMode)
        <div class="fixed inset-0 z-[9999] bg-slate-950 text-white w-screen h-screen flex flex-col p-6 sm:p-10 overflow-hidden">
            {{-- Slide Header --}}
            <div class="flex items-center justify-between border-b border-slate-800 pb-5 mb-6 shrink-0 max-w-7xl mx-auto w-full">
                <div class="flex items-center gap-4">
                    <span class="px-3.5 py-1.5 bg-blue-600/20 border border-blue-500/40 text-blue-400 text-xs font-black uppercase rounded-xl tracking-wider">
                        Slide {{ $activeSlide }} / 4
                    </span>
                    <h2 class="text-lg sm:text-2xl font-black text-white tracking-tight">
                        @if($activeSlide === 1) Slide 1: Ringkasan Omset & Tren Penjualan Mingguan
                        @elseif($activeSlide === 2) Slide 2: Evaluasi Kasir & Kepatuhan Tugas Piket
                        @elseif($activeSlide === 3) Slide 3: Analisis Pergerakan Produk (Top vs Stagnan)
                        @elseif($activeSlide === 4) Slide 4: Kesimpulan & Rekomendasi Evaluasi Rapat
                        @endif
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <button wire:click="prevSlide" @disabled($activeSlide <= 1) class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 text-white rounded-xl text-xs font-extrabold transition-all border border-slate-700">
                        ← Sebelumnya
                    </button>
                    <button wire:click="nextSlide" @disabled($activeSlide >= 4) class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 disabled:opacity-30 text-white rounded-xl text-xs font-extrabold transition-all shadow-md shadow-blue-500/20">
                        Berikutnya →
                    </button>
                    <button wire:click="togglePresentationMode" title="Tutup Presentasi" class="ml-2 px-4 py-2.5 bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-800/80 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tutup Deck</span>
                    </button>
                </div>
            </div>

            {{-- Slide Content Canvas --}}
            <div class="flex-1 overflow-y-auto max-w-7xl mx-auto w-full flex flex-col justify-center py-2 pr-2">
                @if($activeSlide === 1)
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
                                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Omset Toko</span>
                                <h3 class="text-3xl font-black text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                                <div class="pt-1 text-xs font-semibold flex items-center justify-between border-t border-slate-800/80">
                                    <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }}
                                    </span>
                                    <span class="text-slate-400 font-medium">Periode Lalu: Rp {{ number_format($prevRevenue) }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
                                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Net Profit Toko</span>
                                <h3 class="text-3xl font-black text-emerald-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                                <div class="pt-1 text-xs font-semibold flex items-center justify-between border-t border-slate-800/80">
                                    <span class="{{ $profitGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $profitGrowth >= 0 ? '+'.$profitGrowth.'%' : $profitGrowth.'%' }}
                                    </span>
                                    <span class="text-slate-400 font-medium">Periode Lalu: Rp {{ number_format($prevProfit) }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
                                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Transaksi</span>
                                <h3 class="text-3xl font-black text-blue-400">{{ number_format($totalTransactions) }} Tx</h3>
                                <div class="pt-1 text-xs font-semibold flex items-center justify-between border-t border-slate-800/80">
                                    <span class="{{ $txGrowth >= 0 ? 'text-blue-400' : 'text-rose-400' }}">
                                        {{ $txGrowth >= 0 ? '+'.$txGrowth.'%' : $txGrowth.'%' }}
                                    </span>
                                    <span class="text-slate-400 font-medium">Periode Lalu: {{ number_format($prevTxCount) }} Tx</span>
                                </div>
                            </div>

                            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Hari Puncak Omset</span>
                                <h3 class="text-3xl font-black text-amber-400">{{ $peakDay['day'] }}</h3>
                                <span class="text-xs font-bold text-slate-400 block mt-2">Rp {{ number_format($peakDay['revenue']) }} ({{ $peakDay['transactions'] }} Tx)</span>
                            </div>
                        </div>

                        {{-- Presentation Chart --}}
                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Grafik Penjualan Harian</h4>
                            <div class="h-60 flex items-end justify-between gap-3 pt-4 pb-2 border-b border-slate-800 overflow-x-auto">
                                @foreach($dailySales as $day)
                                    @php 
                                        $barPct = $maxDailyRevenue > 0 ? max(12, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 12;
                                        $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0);
                                    @endphp
                                    <div class="flex-1 flex flex-col items-center justify-end h-full min-w-[55px] group">
                                        <span class="text-xs font-black text-blue-400 mb-2 whitespace-nowrap">Rp {{ number_format($day['revenue'] / 1000, 0) }}k</span>
                                        <div class="w-full max-w-[44px] bg-slate-800 rounded-t-xl flex items-end h-36 p-1 border border-slate-700 shrink-0">
                                            <div class="w-full rounded-t-lg transition-all shadow-lg {{ $isPeak ? 'bg-blue-500 shadow-blue-500/30' : 'bg-blue-600/80 group-hover:bg-blue-500' }}" style="height: {{ $barPct }}%"></div>
                                        </div>
                                        <span class="text-xs font-black text-slate-200 pt-2.5">{{ substr($day['day_name'], 0, 3) }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $day['transactions'] }} Tx</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 2)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
                            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Top 3 Kasir Terbaik (Bintang Piket)</span>
                                <span class="text-xs text-slate-400 font-normal">Disiplin & Penjualan</span>
                            </h3>
                            <div class="space-y-3">
                                @forelse($topPerformers as $idx => $item)
                                    <div class="flex items-center justify-between p-4 bg-slate-800/80 rounded-xl border border-slate-700">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-500/30">#{{ $idx + 1 }}</span>
                                            <div>
                                                <h4 class="font-bold text-white text-sm">{{ $item->user->name }}</h4>
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $item->total_tx }} Transaksi | Omset Rp {{ number_format($item->total_sales) }}</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-emerald-950 text-emerald-300 border border-emerald-800 text-xs font-black rounded-lg">{{ $item->overall_score }} Pts</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada data kinerja kasir.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
                            <h3 class="text-sm font-black text-rose-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Kasir Perlu Evaluasi & Catatan Tugas</span>
                                <span class="text-xs text-slate-400 font-normal">Tugas Belum Selesai</span>
                            </h3>
                            <div class="space-y-3">
                                @forelse($bottomPerformers as $item)
                                    <div class="p-4 bg-slate-800/80 rounded-xl border border-slate-700 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-bold text-white text-sm">{{ $item->user->name }}</h4>
                                            <span class="px-2.5 py-0.5 bg-rose-950 text-rose-300 border border-rose-800 text-xs font-bold rounded-lg">Skor: {{ $item->overall_score }}</span>
                                        </div>
                                        <p class="text-xs text-rose-300 font-medium leading-relaxed">{{ $item->evaluation_notes }}</p>
                                        <div class="text-[11px] text-slate-400 pt-1 flex justify-between border-t border-slate-700/60">
                                            <span>Omset POS: Rp {{ number_format($item->total_sales) }}</span>
                                            <span>{{ $item->total_tx }} Transaksi</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-emerald-400 py-4 text-center">Semua kasir memiliki kinerja baik & disiplin!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 3)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
                            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">5 Produk Paling Laku (Top Selling)</h3>
                            <div class="space-y-3">
                                @foreach($topSellingProducts as $p)
                                    <div class="flex items-center justify-between p-3.5 bg-slate-800/80 rounded-xl border border-slate-700/60">
                                        <div>
                                            <h4 class="font-bold text-white text-sm">{{ $p->product->name }}</h4>
                                            <p class="text-xs text-slate-400">Kategori: {{ $p->product->category->name ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-emerald-400 font-black text-sm block">{{ $p->qty_sold }} Terjual</span>
                                            <span class="text-xs text-slate-400">Omset: Rp {{ number_format($p->omset) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
                            <h3 class="text-sm font-black text-amber-400 uppercase tracking-wider">5 Produk Kurang Laku / Stagnan</h3>
                            <div class="space-y-3">
                                @foreach($leastSellingProducts as $p)
                                    <div class="flex items-center justify-between p-3.5 bg-slate-800/80 rounded-xl border border-slate-700/60">
                                        <div>
                                            <h4 class="font-bold text-white text-sm">{{ $p->product->name }}</h4>
                                            <p class="text-xs text-amber-400 font-medium">Stok Tersisa: {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-rose-400 font-black text-sm block">{{ $p->qty_sold }} Terjual</span>
                                            <span class="text-xs text-slate-400">Perlu Penanganan Stok</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 4)
                    <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl space-y-6">
                        <h3 class="text-xl font-black text-white border-b border-slate-800 pb-4 flex items-center justify-between">
                            <span>Kesimpulan & Catatan Evaluasi Rapat Mingguan</span>
                            <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Pengelolaan Kasir & Toko</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-6 bg-slate-800/80 rounded-2xl border border-slate-700 space-y-2">
                                <h4 class="font-black text-blue-400 text-sm uppercase tracking-wider">1. Evaluasi Penjualan</h4>
                                <p class="text-xs text-slate-300 leading-relaxed">Puncak omset terjadi pada hari <strong class="text-white font-bold">{{ $peakDay['day'] }}</strong> sebesar Rp {{ number_format($peakDay['revenue']) }}. Disarankan untuk meningkatkan promosi dan variasi produk pada hari-hari sepi.</p>
                            </div>
                            <div class="p-6 bg-slate-800/80 rounded-2xl border border-slate-700 space-y-2">
                                <h4 class="font-black text-emerald-400 text-sm uppercase tracking-wider">2. Kepatuhan Kasir & Piket</h4>
                                <p class="text-xs text-slate-300 leading-relaxed">Kehadiran kasir piket berada di angka <strong class="text-white font-bold">{{ $shiftFulfillmentRate }}%</strong> dan tingkat penyelesaian tugas piket yang disetujui sebesar <strong class="text-white font-bold">{{ $taskApprovedRate }}%</strong>.</p>
                            </div>
                            <div class="p-6 bg-slate-800/80 rounded-2xl border border-slate-700 space-y-2">
                                <h4 class="font-black text-amber-400 text-sm uppercase tracking-wider">3. Manajemen Persediaan</h4>
                                <p class="text-xs text-slate-300 leading-relaxed">Segera lakukan rotasi atau promosi bundling untuk produk-produk slow-moving untuk mencegah risiko kadaluarsa dan penumpukan stok.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer Indicators --}}
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-slate-800 shrink-0 max-w-7xl mx-auto w-full">
                @for($s = 1; $s <= 4; $s++)
                    <button wire:click="setSlide({{ $s }})" class="h-3 rounded-full transition-all {{ $activeSlide === $s ? 'bg-blue-500 w-10' : 'bg-slate-700 hover:bg-slate-600 w-3' }}"></button>
                @endfor
            </div>
        </div>
    @endif

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

        {{-- VISIBLE BAR CHART --}}
        <div class="pt-4 pb-2 border-b border-gray-100 dark:border-gray-700/60 overflow-x-auto">
            <div class="flex items-end justify-between gap-3 min-w-[600px] h-56 pt-2">
                @foreach($dailySales as $day)
                    @php 
                        $barPct = $maxDailyRevenue > 0 ? max(10, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 10;
                        $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0);
                    @endphp
                    <div class="flex-1 flex flex-col items-center justify-end h-full min-w-[55px] group">
                        {{-- Omset Rp Label above bar --}}
                        <span class="text-[11px] font-black mb-1.5 whitespace-nowrap {{ $isPeak ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                            Rp {{ number_format($day['revenue'] / 1000, 0) }}k
                        </span>

                        {{-- Bar Track & Inner Bar --}}
                        <div class="w-full max-w-[44px] bg-gray-100 dark:bg-gray-700/60 rounded-t-xl flex items-end h-36 p-1 border border-gray-200/50 dark:border-gray-600/40 shrink-0">
                            <div class="w-full rounded-t-lg transition-all duration-500 {{ $isPeak ? 'bg-blue-600 dark:bg-blue-500 shadow-md shadow-blue-500/30' : 'bg-blue-500/80 dark:bg-blue-400/80 group-hover:bg-blue-600' }}" style="height: {{ $barPct }}%;"></div>
                        </div>

                        {{-- Day Name & Tx Count below bar --}}
                        <div class="text-center pt-2">
                            <span class="text-xs font-black block text-gray-900 dark:text-white">{{ $day['day_name'] }}</span>
                            <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 block">{{ $day['transactions'] }} Tx</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SECTION 3: AUDIT PIKET & TUGAS KASIR HARIAN --}}
    <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-5">
        <div>
            <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-blue dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Audit Piket & Tugas Kasir Harian
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian faktual kasir yang piket di tiap hari, status kehadiran, omset, & kelengkapan tugasnya</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($dailyShiftAudits as $audit)
                <div class="bg-gray-50/80 dark:bg-gray-700/40 p-4 rounded-xl border border-gray-200/80 dark:border-gray-600/60 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="font-extrabold text-sm text-gray-900 dark:text-white">{{ $audit['day_name'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ $audit['date'] }}</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($audit['cashiers'] as $c)
                            <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-xs text-gray-900 dark:text-white">{{ $c['user']->name }}</span>
                                    @if($c['attended'])
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-400 dark:border-emerald-800 text-[10px] font-bold rounded">Hadir {{ $c['clock_in'] ?? '' }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-400 dark:border-rose-800 text-[10px] font-bold rounded">Tidak Absen</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                                    <span>Omset POS: <strong class="text-gray-900 dark:text-white">Rp {{ number_format($c['sales_omset']) }}</strong></span>
                                    <span>{{ $c['sales_tx'] }} Tx</span>
                                </div>

                                {{-- Task Status List --}}
                                @if(!empty($c['task_details']) && count($c['task_details']) > 0)
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-1">
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Tugas Piket:</span>
                                        @foreach($c['task_details'] as $t)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[140px]">• {{ $t['task_name'] }}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $t['badge_class'] }}">
                                                    {{ $t['status'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="pt-1 text-[11px] text-gray-500 dark:text-gray-400 italic">Tidak ada penugasan khusus.</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-xs text-gray-400">Belum ada data jadwal piket kasir pada minggu ini.</div>
            @endforelse
        </div>
    </div>

    {{-- SECTION 4: PAPAN EVALUASI KASIR MINGGUAN --}}
    <div class="space-y-5">
        <div>
            <h3 class="text-lg font-black text-gray-900 dark:text-white">Papan Evaluasi Kasir Piket</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Analisis akumulasi kinerja individu kasir meliputi omset penjualan, kedisiplinan piket, & kelengkapan tugas</p>
        </div>

        {{-- Top 3 Highlight Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($topPerformers as $index => $cashier)
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-500 text-gray-950 font-black text-sm rounded-xl flex items-center justify-center shrink-0">
                                #{{ $index + 1 }}
                            </div>
                            <div class="truncate">
                                <h4 class="font-extrabold text-gray-900 dark:text-white text-sm truncate">{{ $cashier->user->name }}</h4>
                                <span class="text-xs text-amber-600 dark:text-amber-400 font-bold">Top Kasir #{{ $index + 1 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-gray-100 dark:border-gray-700/60 text-xs">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block font-medium">Omset POS</span>
                            <strong class="text-gray-900 dark:text-white font-black">Rp {{ number_format($cashier->total_sales) }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block font-medium">Transaksi</span>
                            <strong class="text-gray-900 dark:text-white font-black">{{ $cashier->total_tx }} Tx</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block font-medium">Absensi</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $cashier->attended_count }} Shift</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block font-medium">Tugas</span>
                            <strong class="text-primary-blue dark:text-blue-400 font-bold">{{ $cashier->approved_tasks }} Disetujui</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Full Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <h4 class="font-black text-gray-900 dark:text-white text-xs sm:text-sm">Rincian Kinerja Seluruh Kasir Mingguan</h4>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ $cashierPerformanceList->count() }} Kasir Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[750px]">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/30 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700/60">
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
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($cashierPerformanceList as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-primary-blue/10 text-primary-blue dark:text-blue-300 font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-gray-900 dark:text-white block">{{ $item->user->name }}</span>
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $item->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-black text-gray-900 dark:text-white block">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</span>
                                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold block">Profit: Rp {{ number_format($item->total_profit, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 block">Rata Keranjang: Rp {{ number_format($item->avg_basket, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-gray-700 dark:text-gray-300">
                                    <span class="font-bold text-gray-900 dark:text-white block text-sm">{{ number_format($item->total_tx) }} Transaksi</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <span class="text-xs font-extrabold text-gray-900 dark:text-white block">{{ $item->attended_count }}/{{ $item->scheduled_count }} Shift ({{ $item->on_time_rate }}% Tepat Waktu)</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-400 dark:border-emerald-800 rounded font-bold text-[10px]">{{ $item->on_time_count }} On-Time</span>
                                            @if($item->late_count > 0)
                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-400 dark:border-rose-800 rounded font-bold text-[10px]">{{ $item->late_count }} Terlambat</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <span class="text-xs font-black text-primary-blue dark:text-blue-400 block">{{ $item->task_completion_rate }}% Selesai</span>
                                        <div class="text-[11px] font-semibold space-y-0.5">
                                            <span class="text-emerald-600 dark:text-emerald-400 block">• {{ $item->approved_tasks }} Disetujui</span>
                                            @if($item->unsubmitted_tasks > 0)
                                                <span class="text-rose-600 dark:text-rose-400 block">• {{ $item->unsubmitted_tasks }} Belum Dilaporkan</span>
                                            @endif
                                            @if($item->rejected_tasks > 0)
                                                <span class="text-rose-600 dark:text-rose-400 block">• {{ $item->rejected_tasks }} Ditolak</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-xs font-medium {{ $item->unsubmitted_tasks > 0 || $item->rejected_tasks > 0 ? 'text-rose-600 dark:text-rose-300 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $item->evaluation_notes }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 bg-primary-blue/10 text-primary-blue dark:text-blue-300 font-black rounded-lg">
                                        {{ $item->overall_score }} Pts
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button wire:click="viewCashierDetail('{{ $item->user->id }}')" class="px-3.5 py-1.5 bg-gray-100 hover:bg-primary-blue hover:text-white dark:bg-gray-700 dark:hover:bg-primary-blue text-gray-700 dark:text-gray-200 text-xs font-bold rounded-lg transition">
                                        Audit Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada data kasir pada minggu ini.</td>
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
        <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-lg shrink-0 border border-emerald-200 dark:border-emerald-900/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white text-base">5 Barang Paling Laku (Top Selling)</h3>
                </div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Terlaris</span>
            </div>

            <div class="space-y-3">
                @forelse($topSellingProducts as $item)
                    <div class="p-4 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl flex items-center justify-between border border-gray-100 dark:border-gray-700/60">
                        <div>
                            <h4 class="font-extrabold text-gray-900 dark:text-white text-xs sm:text-sm">{{ $item->product->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Kat: {{ $item->product->category->name ?? '-' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-emerald-600 dark:text-emerald-400 font-black text-sm block">{{ $item->qty_sold }} Terjual</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Omset: Rp {{ number_format($item->omset) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400 py-4 text-center">Belum ada transaksi barang pada minggu ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Least Selling --}}
        <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-lg shrink-0 border border-amber-200 dark:border-amber-900/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white text-base">5 Barang Kurang Laku / Stagnan</h3>
                </div>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Evaluasi Stok</span>
            </div>

            <div class="space-y-3">
                @forelse($leastSellingProducts as $item)
                    <div class="p-4 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl flex items-center justify-between border border-gray-100 dark:border-gray-700/60">
                        <div>
                            <h4 class="font-extrabold text-gray-900 dark:text-white text-xs sm:text-sm">{{ $item->product->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Stok Tersedia: <strong class="text-amber-600 dark:text-amber-400">{{ $item->stock }} {{ $item->product->unit ?? 'pcs' }}</strong></p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-rose-600 dark:text-rose-400 font-black text-sm block">{{ $item->qty_sold }} Terjual</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Slow Moving</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400 py-4 text-center">Semua barang terjual dengan baik.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DETAIL AUDIT MODAL --}}
    @if($showCashierDetailModal && $modalCashierData)
        <div class="fixed inset-0 z-50 bg-gray-950/80 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 max-w-3xl w-full rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 max-h-[90vh] flex flex-col overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-blue text-white font-black flex items-center justify-center text-base shrink-0">
                            {{ strtoupper(substr($modalCashierData['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white text-base sm:text-lg">{{ $modalCashierData['user']->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Audit Detail Piket, Absensi, Penjualan, & Tugas Mingguan</p>
                        </div>
                    </div>
                    <button wire:click="closeCashierDetailModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 overflow-y-auto space-y-4 text-xs">
                    @foreach($modalCashierData['daily_breakdown'] as $day)
                        <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-700/40 space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="font-extrabold text-sm text-gray-900 dark:text-white">{{ $day['day_name'] }} ({{ $day['date'] }})</span>
                                @if($day['is_scheduled'])
                                    <span class="px-2.5 py-0.5 bg-primary-blue/10 text-primary-blue dark:text-blue-300 font-bold rounded text-xs border border-primary-blue/20">Terdaftar Piket</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded text-xs">Bukan Jadwal Piket</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Status Absensi</span>
                                    @if($day['attendance'])
                                        <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Hadir {{ $day['attendance']->clock_in ? \Carbon\Carbon::parse($day['attendance']->clock_in)->format('H:i') : '' }}</strong>
                                    @else
                                        <strong class="text-rose-600 dark:text-rose-400 font-bold">Tidak Absen / Kosong</strong>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Penjualan POS</span>
                                    <strong class="text-gray-900 dark:text-white font-black">Rp {{ number_format($day['sales_omset']) }} ({{ $day['sales_count'] }} Tx)</strong>
                                </div>
                            </div>

                            {{-- Tasks --}}
                            <div class="space-y-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                <span class="font-bold text-gray-800 dark:text-gray-200 block text-xs">Daftar Tugas & Pelaporan:</span>
                                @forelse($day['tasks'] as $asg)
                                    @php $sub = $asg->latestSubmission; @endphp
                                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block text-xs">{{ $asg->taskDefinition->task_name ?? 'Tugas Piket' }}</span>
                                            @if($sub && $sub->rejection_note)
                                                <span class="text-rose-600 dark:text-rose-400 text-xs block font-semibold mt-0.5">Alasan Ditolak: {{ $sub->rejection_note }}</span>
                                            @endif
                                        </div>
                                        <div class="shrink-0">
                                            @if(!$sub)
                                                <span class="px-2.5 py-1 bg-rose-50 text-rose-600 border border-rose-200 dark:bg-rose-950/80 dark:text-rose-300 dark:border-rose-800 font-bold text-xs rounded">Belum Dilaporkan</span>
                                            @elseif($sub->approval_status === 'approved')
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-800 font-bold text-xs rounded">Disetujui</span>
                                            @elseif($sub->approval_status === 'rejected')
                                                <span class="px-2.5 py-1 bg-rose-50 text-rose-600 border border-rose-200 dark:bg-rose-950/80 dark:text-rose-300 dark:border-rose-800 font-bold text-xs rounded">Ditolak</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-950/80 dark:text-blue-300 dark:border-blue-800 font-bold text-xs rounded">Menunggu Review</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-gray-500 dark:text-gray-400 italic text-xs">Tidak ada tugas khusus.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700/40 border-t border-gray-100 dark:border-gray-700 text-right">
                    <button wire:click="closeCashierDetailModal" class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
