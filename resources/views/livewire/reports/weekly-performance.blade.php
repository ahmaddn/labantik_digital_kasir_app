<div class="p-3 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
    {{-- Header & Week Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700/60">
        <div class="flex items-center gap-3">
            <div class="p-2.5 sm:p-3 bg-primary-blue/10 text-primary-blue dark:text-blue-400 rounded-xl sm:rounded-2xl shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Performa Penjualan & Kasir Mingguan</h1>
                <p class="text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-400">Laporan evaluasi omset toko, audit tugas piket harian, & stok barang</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
            {{-- Week Picker Dropdown --}}
            <div class="relative w-full sm:w-auto">
                <select wire:model.live="selectedWeekDate" class="w-full sm:w-auto appearance-none bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white text-xs font-bold rounded-xl sm:rounded-2xl px-4 py-3 pr-10 focus:ring-2 focus:ring-primary-blue cursor-pointer shadow-xs">
                    @foreach($availableWeeks as $w)
                        <option value="{{ $w['date'] }}">{{ $w['label'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Toggle Presentation Mode Button --}}
            <button wire:click="togglePresentationMode" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold rounded-xl sm:rounded-2xl shadow-lg shadow-blue-500/20 transition-all hover:scale-[1.01] active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12H4z" />
                </svg>
                <span>Mode Presentasi (Deck)</span>
            </button>
        </div>
    </div>

    {{-- PRESENTATION MODE MODAL DECK --}}
    @if($isPresentationMode)
        <div class="fixed inset-0 z-50 bg-gray-950/95 backdrop-blur-xl flex flex-col p-3 sm:p-6 md:p-8 text-white animate-fadeIn overflow-hidden">
            {{-- Slide Header Nav --}}
            <div class="flex items-center justify-between border-b border-gray-800 pb-3 mb-4 sm:mb-6">
                <div class="flex items-center gap-2 sm:gap-3 truncate">
                    <span class="px-2.5 py-1 bg-primary-blue/20 border border-primary-blue/40 text-primary-blue text-[10px] sm:text-xs font-extrabold uppercase rounded-full tracking-wider shrink-0">Slide {{ $activeSlide }}/4</span>
                    <h2 class="text-sm sm:text-lg md:text-xl font-black tracking-tight text-gray-100 truncate">
                        @if($activeSlide === 1) Slide 1: Ringkasan Omset Penjualan
                        @elseif($activeSlide === 2) Slide 2: Evaluasi Kasir & Tugas Piket
                        @elseif($activeSlide === 3) Slide 3: Analisis Barang Laku vs Stagnan
                        @elseif($activeSlide === 4) Slide 4: Kesimpulan & Rekomendasi Rapat
                        @endif
                    </h2>
                </div>

                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <button wire:click="prevSlide" @disabled($activeSlide <= 1) class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-800 hover:bg-gray-700 disabled:opacity-30 text-white rounded-xl text-xs font-bold transition">← <span class="hidden sm:inline">Sebelum</span></button>
                    <button wire:click="nextSlide" @disabled($activeSlide >= 4) class="px-3 py-1.5 sm:px-4 sm:py-2 bg-primary-blue hover:bg-blue-600 disabled:opacity-30 text-white rounded-xl text-xs font-bold transition"><span class="hidden sm:inline">Berikut</span> →</button>
                    <button wire:click="togglePresentationMode" class="ml-2 p-2 bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 rounded-xl transition">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Slide Body Container --}}
            <div class="flex-1 overflow-y-auto pr-1 space-y-4 sm:space-y-6">
                @if($activeSlide === 1)
                    {{-- SLIDE 1: Ringkasan Omset & Grafik --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6">
                        <div class="bg-gradient-to-br from-blue-900/40 to-indigo-900/40 border border-blue-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <p class="text-[10px] sm:text-xs uppercase tracking-widest text-blue-300 font-bold mb-1">Total Omset Mingguan</p>
                            <h3 class="text-lg sm:text-3xl font-black text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                            <span class="text-[10px] sm:text-xs font-semibold {{ $revenueGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }} vs minggu lalu
                            </span>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-900/40 to-teal-900/40 border border-emerald-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <p class="text-[10px] sm:text-xs uppercase tracking-widest text-emerald-300 font-bold mb-1">Estimasi Profit</p>
                            <h3 class="text-lg sm:text-3xl font-black text-emerald-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                            <span class="text-[10px] sm:text-xs text-emerald-300/80">Margin Bersih Toko</span>
                        </div>

                        <div class="bg-gradient-to-br from-purple-900/40 to-violet-900/40 border border-purple-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <p class="text-[10px] sm:text-xs uppercase tracking-widest text-purple-300 font-bold mb-1">Total Transaksi</p>
                            <h3 class="text-lg sm:text-3xl font-black text-purple-300">{{ number_format($totalTransactions) }} <span class="text-xs font-normal">Struk</span></h3>
                            <p class="text-[10px] sm:text-xs text-purple-300/80">Rata-rata: Rp {{ number_format($avgBasketSize) }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-amber-900/40 to-orange-900/40 border border-amber-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <p class="text-[10px] sm:text-xs uppercase tracking-widest text-amber-300 font-bold mb-1">Hari Puncak Omset</p>
                            <h3 class="text-lg sm:text-2xl font-black text-amber-300">{{ $peakDay['day'] }}</h3>
                            <p class="text-[10px] sm:text-xs text-amber-200/80">Rp {{ number_format($peakDay['revenue']) }}</p>
                        </div>
                    </div>

                    {{-- Visual Bar Chart Slide 1 --}}
                    <div class="bg-gray-900/80 border border-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                        <h4 class="text-xs sm:text-sm font-bold text-gray-300 uppercase tracking-wider mb-4 sm:mb-6">Tren Penjualan Harian (Senin - Minggu)</h4>
                        <div class="h-48 sm:h-64 flex items-end justify-between gap-2 sm:gap-3 pt-6 px-1 border-b border-gray-800">
                            @foreach($dailySales as $day)
                                @php $heightPct = $maxDailyRevenue > 0 ? max(10, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 10; @endphp
                                <div class="flex-1 flex flex-col items-center gap-1.5 group">
                                    <span class="text-[9px] sm:text-[11px] font-bold text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">Rp {{ number_format($day['revenue'] / 1000, 0) }}k</span>
                                    <div class="w-full bg-blue-600/30 group-hover:bg-blue-500 rounded-t-lg transition-all duration-300 relative" style="height: {{ $heightPct }}%">
                                        <div class="absolute inset-x-0 top-0 h-1 bg-blue-400 rounded-t-lg"></div>
                                    </div>
                                    <span class="text-[10px] sm:text-xs font-bold text-gray-400">{{ substr($day['day_name'], 0, 3) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                @elseif($activeSlide === 2)
                    {{-- SLIDE 2: Raport Kasir --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-emerald-950/40 border border-emerald-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <h3 class="text-base sm:text-lg font-black text-emerald-400 mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                Top 3 Kasir Terbaik (Bintang Piket)
                            </h3>
                            <div class="space-y-3">
                                @forelse($topPerformers as $idx => $item)
                                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-900/80 rounded-xl sm:rounded-2xl border border-emerald-500/20">
                                        <div class="flex items-center gap-2.5 sm:gap-3">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl font-black bg-amber-500 text-gray-950 text-xs sm:text-sm">#{{ $idx + 1 }}</div>
                                            <div>
                                                <h4 class="font-bold text-white text-xs sm:text-sm">{{ $item->user->name }}</h4>
                                                <p class="text-[11px] text-gray-400">{{ $item->total_tx }} Tx | Rp {{ number_format($item->total_sales) }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-[10px] sm:text-xs font-bold">{{ $item->overall_score }} Pts</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400">Belum ada data kasir.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-rose-950/40 border border-rose-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <h3 class="text-base sm:text-lg font-black text-rose-400 mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Catatan Pelanggaran & Evaluasi Tugas
                            </h3>
                            <div class="space-y-3">
                                @forelse($bottomPerformers as $idx => $item)
                                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-900/80 rounded-xl sm:rounded-2xl border border-rose-500/20">
                                        <div class="flex items-center gap-2.5 sm:gap-3">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl font-black bg-rose-500/30 text-rose-300 text-xs sm:text-sm">!</div>
                                            <div>
                                                <h4 class="font-bold text-white text-xs sm:text-sm">{{ $item->user->name }}</h4>
                                                <p class="text-[11px] text-rose-300 font-semibold">{{ $item->evaluation_notes }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 bg-rose-500/20 text-rose-300 rounded-full text-[10px] sm:text-xs font-bold">Skor: {{ $item->overall_score }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-emerald-400">Semua kasir memiliki kinerja yang memuaskan!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 3)
                    {{-- SLIDE 3: Produk Laku vs Stagnan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-gray-900/80 border border-emerald-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <h3 class="text-base sm:text-lg font-black text-emerald-400 mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                5 Produk Terlaris Mingguan
                            </h3>
                            <div class="space-y-2.5">
                                @foreach($topSellingProducts as $p)
                                    <div class="flex items-center justify-between p-3 bg-gray-800/60 rounded-xl">
                                        <span class="font-bold text-xs sm:text-sm text-gray-100 truncate max-w-[180px] sm:max-w-none">{{ $p->product->name }}</span>
                                        <div class="text-right">
                                            <span class="text-emerald-400 font-black text-xs sm:text-sm block">{{ $p->qty_sold }} Terjual</span>
                                            <span class="text-[10px] text-gray-400">Rp {{ number_format($p->omset) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-gray-900/80 border border-amber-500/30 p-4 sm:p-6 rounded-2xl sm:rounded-3xl">
                            <h3 class="text-base sm:text-lg font-black text-amber-400 mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                5 Produk Kurang Laku / Stagnan
                            </h3>
                            <div class="space-y-2.5">
                                @foreach($leastSellingProducts as $p)
                                    <div class="flex items-center justify-between p-3 bg-gray-800/60 rounded-xl">
                                        <div>
                                            <span class="font-bold text-xs sm:text-sm text-gray-100 block truncate max-w-[160px] sm:max-w-none">{{ $p->product->name }}</span>
                                            <span class="text-[10px] text-gray-400">Stok Tersisa: {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-rose-400 font-bold text-xs sm:text-sm block">{{ $p->qty_sold }} Terjual</span>
                                            <span class="text-[10px] text-amber-400">Slow Moving</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @elseif($activeSlide === 4)
                    {{-- SLIDE 4: Evaluasi & Rencana Aksi --}}
                    <div class="bg-gradient-to-br from-gray-900 to-blue-950 border border-blue-500/30 p-5 sm:p-8 rounded-2xl sm:rounded-3xl space-y-4 sm:space-y-6">
                        <h3 class="text-lg sm:text-2xl font-black text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Kesimpulan & Catatan Evaluasi Rapat
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                            <div class="p-4 sm:p-5 bg-gray-900/80 border border-gray-800 rounded-2xl">
                                <h4 class="font-bold text-blue-400 text-xs sm:text-sm mb-1.5">1. Evaluasi Penjualan</h4>
                                <p class="text-[11px] sm:text-xs text-gray-300 leading-relaxed">Puncak omset terjadi pada hari <strong class="text-white">{{ $peakDay['day'] }}</strong> sebesar Rp {{ number_format($peakDay['revenue']) }}. Strategi promosi dapat ditingkatkan pada hari yang cenderung lebih sepi.</p>
                            </div>
                            <div class="p-4 sm:p-5 bg-gray-900/80 border border-gray-800 rounded-2xl">
                                <h4 class="font-bold text-emerald-400 text-xs sm:text-sm mb-1.5">2. Kinerja & Absensi Piket</h4>
                                <p class="text-[11px] sm:text-xs text-gray-300 leading-relaxed">Tingkat kehadiran kasir berada di angka <strong class="text-emerald-400">{{ $shiftFulfillmentRate }}%</strong> dan kepatuhan tugas disetujui sebesar <strong class="text-emerald-400">{{ $taskApprovedRate }}%</strong>.</p>
                            </div>
                            <div class="p-4 sm:p-5 bg-gray-900/80 border border-gray-800 rounded-2xl">
                                <h4 class="font-bold text-amber-400 text-xs sm:text-sm mb-1.5">3. Manajemen Stok</h4>
                                <p class="text-[11px] sm:text-xs text-gray-300 leading-relaxed">Disarankan untuk mengevaluasi ulang stok produk stagnan untuk mengurangi resiko barang kadaluarsa/penumpukan modal.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Slide Footer Indicators --}}
            <div class="flex items-center justify-center gap-2 pt-3 border-t border-gray-800 shrink-0">
                @for($s = 1; $s <= 4; $s++)
                    <button wire:click="setSlide({{ $s }})" class="w-2.5 h-2.5 rounded-full transition-all {{ $activeSlide === $s ? 'bg-primary-blue w-7' : 'bg-gray-700 hover:bg-gray-600' }}"></button>
                @endfor
            </div>
        </div>
    @endif

    {{-- REGULAR DASHBOARD VIEW --}}

    {{-- Executive Top Scorecards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        {{-- Card 1: Omset --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Omset</span>
                <div class="p-2 bg-primary-blue/10 text-primary-blue dark:text-blue-400 rounded-xl sm:rounded-2xl shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-base sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <div class="mt-2 sm:mt-3 flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold">
                <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }}
                </span>
                <span class="text-gray-400 dark:text-gray-500 font-normal">vs mg lalu</span>
            </div>
        </div>

        {{-- Card 2: Net Profit --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-gray-400">Net Profit</span>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl sm:rounded-2xl shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <h3 class="text-base sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            <div class="mt-2 sm:mt-3 flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold">
                <span class="{{ $profitGrowth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $profitGrowth >= 0 ? '+'.$profitGrowth.'%' : $profitGrowth.'%' }}
                </span>
                <span class="text-gray-400 dark:text-gray-500 font-normal">margin</span>
            </div>
        </div>

        {{-- Card 3: Total Transaksi --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-gray-400">Transaksi</span>
                <div class="p-2 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-xl sm:rounded-2xl shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <h3 class="text-base sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalTransactions) }} <span class="text-[10px] sm:text-xs font-bold text-gray-400">Struk</span></h3>
            <div class="mt-2 sm:mt-3 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 font-medium truncate">
                Rata: <strong class="text-gray-800 dark:text-gray-200">Rp {{ number_format($avgBasketSize) }}</strong>
            </div>
        </div>

        {{-- Card 4: Kepatuhan --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-gray-400">Piket & Kepatuhan</span>
                <div class="p-2 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl sm:rounded-2xl shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-base sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $shiftFulfillmentRate }}%</h3>
            <div class="mt-2 sm:mt-3 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 font-medium">
                Tugas: <strong class="text-emerald-600 dark:text-emerald-400">{{ $taskApprovedRate }}%</strong> Disetujui
            </div>
        </div>
    </div>

    {{-- Visual Chart Harian (Senin - Minggu) --}}
    <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 sm:mb-6">
            <div>
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Tren Penjualan Harian</h3>
                <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Grafik omset harian sepanjang minggu dari Senin hingga Minggu</p>
            </div>
            <div class="inline-flex self-start sm:self-auto px-3 py-1.5 bg-primary-blue/10 text-primary-blue dark:text-blue-400 text-[10px] sm:text-xs font-bold rounded-xl border border-primary-blue/20">
                Puncak: {{ $peakDay['day'] }} (Rp {{ number_format($peakDay['revenue']) }})
            </div>
        </div>

        <div class="h-48 sm:h-64 flex items-end justify-between gap-2 sm:gap-3 pt-6 pb-2 border-b border-gray-100 dark:border-gray-700">
            @foreach($dailySales as $day)
                @php $heightPct = $maxDailyRevenue > 0 ? max(10, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 10; @endphp
                <div class="flex-1 flex flex-col items-center gap-1.5 group">
                    <span class="text-[9px] sm:text-[11px] font-bold text-primary-blue dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">Rp {{ number_format($day['revenue'] / 1000, 0) }}k</span>
                    <div class="w-full bg-primary-blue/20 dark:bg-blue-500/10 group-hover:bg-primary-blue dark:group-hover:bg-blue-500 rounded-t-xl transition-all duration-300 relative" style="height: {{ $heightPct }}%">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-primary-blue dark:bg-blue-400 rounded-t-xl"></div>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-gray-700 dark:text-gray-300">{{ substr($day['day_name'], 0, 3) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION: FACTUAL DAILY SHIFT & TASK AUDIT (PER PIKET HARI DEMI HARI) --}}
    <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs space-y-4 sm:space-y-6">
        <div>
            <h3 class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Audit Piket & Tugas Kasir Harian
            </h3>
            <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Rincian faktual siapa saja kasir yang piket di tiap hari, status kehadiran, omset, & kelengkapan tugasnya</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @forelse($dailyShiftAudits as $audit)
                <div class="bg-gray-50/80 dark:bg-gray-700/30 p-4 sm:p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="font-black text-gray-900 dark:text-white text-xs sm:text-sm">{{ $audit['day_name'] }}</span>
                        <span class="text-[10px] sm:text-xs text-gray-400 font-semibold">{{ $audit['date'] }}</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($audit['cashiers'] as $c)
                            <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-2xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-primary-blue/10 text-primary-blue dark:text-blue-300 font-extrabold text-[10px] sm:text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($c['user']->name, 0, 1)) }}
                                        </div>
                                        <span class="font-extrabold text-xs text-gray-900 dark:text-white truncate max-w-[110px] sm:max-w-none">{{ $c['user']->name }}</span>
                                    </div>
                                    @if($c['attended'])
                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-md shrink-0">Hadir {{ $c['clock_in'] ?? '' }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-[10px] font-bold rounded-md shrink-0">Tidak Absen</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                                    <span>Omset: <strong class="text-gray-800 dark:text-gray-200">Rp {{ number_format($c['sales_omset']) }}</strong></span>
                                    <span>{{ $c['sales_tx'] }} Tx</span>
                                </div>

                                {{-- Task Status List --}}
                                @if(!empty($c['task_details']) && count($c['task_details']) > 0)
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-1">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tugas Piket:</span>
                                        @foreach($c['task_details'] as $t)
                                            <div class="flex items-center justify-between text-[11px]">
                                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[130px] sm:max-w-[160px]">• {{ $t['task_name'] }}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0 {{ $t['badge_class'] }}">
                                                    {{ $t['status'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="pt-1 text-[10px] text-gray-400 italic">Tidak ada penugasan khusus.</div>
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

    {{-- SECTION: KINERJA KASIR PER PIKET & SCOREBOARD --}}
    <div class="space-y-4 sm:space-y-6">
        <div>
            <h3 class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Papan Evaluasi Kasir Piket</h3>
            <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Analisis akumulasi kinerja individu kasir meliputi omset penjualan, kedisiplinan piket, & kelengkapan tugas</p>
        </div>

        {{-- Top Performers Highlight Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            @foreach($topPerformers as $index => $cashier)
                <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/80 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-amber-200 dark:border-amber-500/30 shadow-xs relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 w-14 h-14 bg-amber-500/10 rounded-full flex items-center justify-center font-black text-amber-500 text-base sm:text-lg">
                        #{{ $index + 1 }}
                    </div>

                    <div class="flex items-center gap-3 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-500 text-white font-black text-sm sm:text-lg rounded-xl sm:rounded-2xl flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                            {{ strtoupper(substr($cashier->user->name, 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <h4 class="font-extrabold text-gray-900 dark:text-white text-xs sm:text-base truncate">{{ $cashier->user->name }}</h4>
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded-full text-[9px] sm:text-[10px] font-bold">Top Kasir #{{ $index + 1 }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 sm:gap-3 pt-3 border-t border-gray-100 dark:border-gray-700/60 text-[11px] sm:text-xs">
                        <div>
                            <span class="text-gray-400 block font-medium">Omset POS</span>
                            <strong class="text-gray-900 dark:text-white font-black">Rp {{ number_format($cashier->total_sales) }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Transaksi</span>
                            <strong class="text-gray-900 dark:text-white font-black">{{ $cashier->total_tx }} Tx</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Absensi</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $cashier->attended_count }} Shift</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Tugas</span>
                            <strong class="text-primary-blue dark:text-blue-400 font-bold">{{ $cashier->approved_tasks }} Disetujui</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Complete Cashiers Performance Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <h4 class="font-black text-gray-900 dark:text-white text-xs sm:text-sm">Rincian Kinerja Seluruh Kasir Mingguan</h4>
                <span class="text-[10px] sm:text-xs text-gray-400 font-semibold">{{ $cashierPerformanceList->count() }} Kasir Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/30 text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-gray-700/60">
                            <th class="px-4 sm:px-6 py-3.5">Kasir Piket</th>
                            <th class="px-4 sm:px-6 py-3.5">Omset Penjualan</th>
                            <th class="px-4 sm:px-6 py-3.5">Transaksi</th>
                            <th class="px-4 sm:px-6 py-3.5">Absensi Piket</th>
                            <th class="px-4 sm:px-6 py-3.5">Status Tugas</th>
                            <th class="px-4 sm:px-6 py-3.5">Catatan Evaluasi</th>
                            <th class="px-4 sm:px-6 py-3.5 text-center">Skor</th>
                            <th class="px-4 sm:px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($cashierPerformanceList as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-primary-blue/10 text-primary-blue dark:text-blue-300 font-bold flex items-center justify-center shrink-0 text-xs">
                                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-gray-900 dark:text-white block">{{ $item->user->name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $item->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 font-black text-gray-900 dark:text-white">
                                    Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                    {{ $item->total_tx }} Struk
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-md font-bold text-[10px]">{{ $item->on_time_count }} Tepat Waktu</span>
                                        @if($item->late_count > 0)
                                            <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-md font-bold text-[10px]">{{ $item->late_count }} Terlambat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="space-y-0.5">
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold block text-[11px]">{{ $item->approved_tasks }} Disetujui</span>
                                        @if($item->unsubmitted_tasks > 0)
                                            <span class="text-rose-500 font-bold text-[10px] block">{{ $item->unsubmitted_tasks }} Belum Dilaporkan</span>
                                        @endif
                                        @if($item->rejected_tasks > 0)
                                            <span class="text-rose-500 font-bold text-[10px] block">{{ $item->rejected_tasks }} Ditolak</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <span class="text-[11px] font-medium {{ $item->unsubmitted_tasks > 0 || $item->rejected_tasks > 0 ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $item->evaluation_notes }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 bg-primary-blue/10 text-primary-blue dark:text-blue-300 rounded-full font-black text-xs">
                                        {{ $item->overall_score }} Pts
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 text-right">
                                    <button wire:click="viewCashierDetail('{{ $item->user->id }}')" class="px-3 py-1.5 bg-gray-100 hover:bg-primary-blue hover:text-white dark:bg-gray-700 dark:hover:bg-primary-blue text-gray-700 dark:text-gray-200 text-xs font-bold rounded-xl transition">
                                        Audit Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">Tidak ada data kasir pada minggu ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SECTION: PERFORMA BARANG LAKU VS KURANG LAKU --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        {{-- Top Selling Products --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs space-y-3 sm:space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 sm:p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs sm:text-sm font-bold shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white text-xs sm:text-base">5 Barang Paling Laku</h3>
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-emerald-600 dark:text-emerald-400">Terlaris</span>
            </div>

            <div class="space-y-2.5">
                @forelse($topSellingProducts as $item)
                    <div class="p-3 sm:p-4 bg-gray-50/50 dark:bg-gray-700/30 rounded-xl sm:rounded-2xl flex items-center justify-between">
                        <div class="truncate max-w-[150px] sm:max-w-none">
                            <h4 class="font-extrabold text-gray-900 dark:text-white text-xs sm:text-sm truncate">{{ $item->product->name }}</h4>
                            <p class="text-[10px] sm:text-[11px] text-gray-400 font-medium">Kat: {{ $item->product->category->name ?? '-' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-emerald-600 dark:text-emerald-400 font-black text-xs sm:text-sm block">{{ $item->qty_sold }} Terjual</span>
                            <span class="text-[10px] text-gray-500 font-semibold">Omset: Rp {{ number_format($item->omset) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-4 text-center">Belum ada transaksi barang pada minggu ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Least Selling Products --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-xs space-y-3 sm:space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 sm:p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl text-xs sm:text-sm font-bold shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white text-xs sm:text-base">5 Barang Kurang Laku / Stagnan</h3>
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-amber-600 dark:text-amber-400">Perlu Evaluasi Stok</span>
            </div>

            <div class="space-y-2.5">
                @forelse($leastSellingProducts as $item)
                    <div class="p-3 sm:p-4 bg-gray-50/50 dark:bg-gray-700/30 rounded-xl sm:rounded-2xl flex items-center justify-between">
                        <div class="truncate max-w-[150px] sm:max-w-none">
                            <h4 class="font-extrabold text-gray-900 dark:text-white text-xs sm:text-sm truncate">{{ $item->product->name }}</h4>
                            <p class="text-[10px] sm:text-[11px] text-gray-400 font-medium">Stok: <strong class="text-amber-600 dark:text-amber-400">{{ $item->stock }} {{ $item->product->unit ?? 'pcs' }}</strong></p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-rose-500 font-black text-xs sm:text-sm block">{{ $item->qty_sold }} Terjual</span>
                            <span class="text-[10px] text-gray-400">Slow Moving</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-4 text-center">Semua barang terjual dengan baik.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- CASHIER DETAIL AUDIT MODAL --}}
    @if($showCashierDetailModal && $modalCashierData)
        <div class="fixed inset-0 z-50 bg-gray-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white dark:bg-gray-800 max-w-3xl w-full rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 max-h-[90vh] flex flex-col overflow-hidden animate-fadeIn">
                <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-primary-blue text-white font-black flex items-center justify-center text-sm sm:text-base shrink-0">
                            {{ strtoupper(substr($modalCashierData['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm sm:text-lg">{{ $modalCashierData['user']->name }}</h3>
                            <p class="text-[10px] sm:text-xs text-gray-400">Audit Detail Piket, Absensi, Penjualan, & Tugas Mingguan</p>
                        </div>
                    </div>
                    <button wire:click="closeCashierDetailModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 sm:p-6 overflow-y-auto space-y-3 sm:space-y-4 text-xs">
                    @foreach($modalCashierData['daily_breakdown'] as $day)
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-700/30 space-y-2.5">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white">{{ $day['day_name'] }} ({{ $day['date'] }})</span>
                                @if($day['is_scheduled'])
                                    <span class="px-2 py-0.5 bg-primary-blue/10 text-primary-blue dark:text-blue-400 font-bold rounded-md text-[10px]">Terdaftar Piket</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-400 font-medium rounded-md text-[10px]">Bukan Jadwal Piket</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-gray-600 dark:text-gray-300 text-[11px]">
                                <div>
                                    <span class="text-gray-400 block font-medium">Status Absensi</span>
                                    @if($day['attendance'])
                                        <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Hadir {{ $day['attendance']->clock_in ? \Carbon\Carbon::parse($day['attendance']->clock_in)->format('H:i') : '' }}</strong>
                                    @else
                                        <strong class="text-rose-500 font-bold">Tidak Absen / Kosong</strong>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-gray-400 block font-medium">Penjualan POS</span>
                                    <strong class="text-gray-900 dark:text-white font-black">Rp {{ number_format($day['sales_omset']) }} ({{ $day['sales_count'] }} Tx)</strong>
                                </div>
                            </div>

                            {{-- Tasks assigned on this day --}}
                            <div class="space-y-1.5 pt-2 border-t border-gray-200 dark:border-gray-600">
                                <span class="font-bold text-gray-700 dark:text-gray-300 block text-[11px]">Daftar Tugas & Pelaporan:</span>
                                @forelse($day['tasks'] as $asg)
                                    @php $sub = $asg->latestSubmission; @endphp
                                    <div class="p-2.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <div class="truncate max-w-[160px] sm:max-w-none">
                                            <span class="font-bold text-gray-900 dark:text-white block text-xs truncate">{{ $asg->taskDefinition->task_name ?? 'Tugas Piket' }}</span>
                                            @if($sub && $sub->rejection_note)
                                                <span class="text-rose-500 text-[10px] block truncate">Alasan Ditolak: {{ $sub->rejection_note }}</span>
                                            @endif
                                        </div>
                                        <div class="shrink-0">
                                            @if(!$sub)
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 rounded-md font-bold text-[10px]">Belum Dilaporkan</span>
                                            @elseif($sub->approval_status === 'approved')
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-md font-bold text-[10px]">Disetujui</span>
                                            @elseif($sub->approval_status === 'rejected')
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 rounded-md font-bold text-[10px]">Ditolak</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 rounded-md font-bold text-[10px]">Menunggu Review</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-gray-400 italic text-[10px]">Tidak ada tugas khusus.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-3.5 sm:p-4 bg-gray-50 dark:bg-gray-700/40 border-t border-gray-100 dark:border-gray-700 text-right">
                    <button wire:click="closeCashierDetailModal" class="px-4 py-2 bg-gray-800 text-white rounded-xl font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
