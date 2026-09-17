<div class="fixed inset-0 z-50 bg-slate-950 text-white w-screen h-screen flex flex-col p-4 sm:p-8 overflow-hidden select-none">
    {{-- Presentation Header Bar --}}
    <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-5 shrink-0 max-w-7xl mx-auto w-full">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-blue-600/20 border border-blue-500/40 text-blue-400 text-xs font-black uppercase rounded-xl tracking-wider">
                Slide {{ $activeSlide }} / 4
            </span>
            <div>
                <h2 class="text-base sm:text-xl font-black text-white tracking-tight">
                    @if($activeSlide === 1) Slide 1: Ringkasan Omset & Tren Penjualan Mingguan
                    @elseif($activeSlide === 2) Slide 2: Evaluasi Kasir & Kepatuhan Tugas Piket
                    @elseif($activeSlide === 3) Slide 3: Analisis Pergerakan Produk (Top vs Stagnan)
                    @elseif($activeSlide === 4) Slide 4: Kesimpulan & Rekomendasi Evaluasi Rapat
                    @endif
                </h2>
                <p class="text-[11px] text-slate-400 font-medium">Periode: {{ $weekStart->format('d M Y') }} - {{ $weekEnd->format('d M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="prevSlide" @disabled($activeSlide <= 1) class="px-4 py-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-25 text-white rounded-xl text-xs font-extrabold transition-all border border-slate-700">
                ← Sebelumnya
            </button>
            <button wire:click="nextSlide" @disabled($activeSlide >= 4) class="px-4 py-2 bg-blue-600 hover:bg-blue-500 disabled:opacity-25 text-white rounded-xl text-xs font-extrabold transition-all shadow-md shadow-blue-500/20">
                Berikutnya →
            </button>
            <a href="{{ route('weekly-performance', ['startDate' => $startDate, 'endDate' => $endDate]) }}" class="ml-2 px-3.5 py-2 bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-800/80 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Keluar Presentasi</span>
            </a>
        </div>
    </div>

    {{-- Slide Content Canvas --}}
    <div class="flex-1 overflow-y-auto max-w-7xl mx-auto w-full flex flex-col justify-center py-2 pr-1">
        @if($activeSlide === 1)
            <div class="space-y-5">
                {{-- Executive KPI Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Omset Toko</span>
                        <h3 class="text-2xl sm:text-3xl font-black text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <div class="pt-1 text-xs font-semibold flex items-center justify-between border-t border-slate-800/80">
                            <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $revenueGrowth >= 0 ? '+'.$revenueGrowth.'%' : $revenueGrowth.'%' }}
                            </span>
                            <span class="text-slate-400 font-medium">Lalu: Rp {{ number_format($prevRevenue) }}</span>
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Net Profit Toko</span>
                        <h3 class="text-2xl sm:text-3xl font-black text-emerald-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                        <div class="pt-1 text-xs font-semibold flex items-center justify-between border-t border-slate-800/80">
                            <span class="{{ $profitGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $profitGrowth >= 0 ? '+'.$profitGrowth.'%' : $profitGrowth.'%' }}
                            </span>
                            <span class="text-slate-400 font-medium">Lalu: Rp {{ number_format($prevProfit) }}</span>
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Total Transaksi</span>
                        <h3 class="text-2xl sm:text-3xl font-black text-blue-400">{{ number_format($totalTransactions) }} Tx</h3>
                        <div class="pt-1 text-xs font-semibold flex items-center justify-between border-t border-slate-800/80">
                            <span class="{{ $txGrowth >= 0 ? 'text-blue-400' : 'text-rose-400' }}">
                                {{ $txGrowth >= 0 ? '+'.$txGrowth.'%' : $txGrowth.'%' }}
                            </span>
                            <span class="text-slate-400 font-medium">Lalu: {{ number_format($prevTxCount) }} Tx</span>
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl">
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Hari Puncak Omset</span>
                        <h3 class="text-2xl sm:text-3xl font-black text-amber-400">{{ $peakDay['day'] }}</h3>
                        <span class="text-xs font-bold text-slate-400 block mt-2">Rp {{ number_format($peakDay['revenue']) }} ({{ $peakDay['transactions'] }} Tx)</span>
                    </div>
                </div>

                {{-- Authentic Bar Chart --}}
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-3">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Grafik Penjualan Harian</h4>
                    <div class="relative h-64 flex items-end">
                        {{-- Y-Axis Grid Lines --}}
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-12">
                            <div class="w-full border-b border-dashed border-slate-800 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-900 pr-2">Rp {{ number_format($maxDailyRevenue / 1000, 0) }}k</span>
                            </div>
                            <div class="w-full border-b border-dashed border-slate-800 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-900 pr-2">Rp {{ number_format(($maxDailyRevenue * 0.5) / 1000, 0) }}k</span>
                            </div>
                            <div class="w-full border-b border-slate-700 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-900 pr-2">Rp 0</span>
                            </div>
                        </div>

                        {{-- Bar Columns --}}
                        <div class="relative z-10 w-full flex items-end justify-around pl-16 pr-4 h-full pb-12">
                            @foreach($dailySales as $day)
                                @php 
                                    $barPct = ($maxDailyRevenue > 0 && $day['revenue'] > 0) ? max(5, round(($day['revenue'] / $maxDailyRevenue) * 100)) : 0;
                                    $isPeak = ($day['day_name'] === $peakDay['day'] && $day['revenue'] > 0);
                                @endphp
                                <div class="flex-1 flex flex-col items-center justify-end h-full group max-w-[70px]">
                                    <span class="text-[11px] font-black {{ $isPeak ? 'text-blue-400' : ($day['revenue'] > 0 ? 'text-slate-300' : 'text-slate-600') }} mb-1 whitespace-nowrap">
                                        {{ $day['revenue'] > 0 ? 'Rp ' . number_format($day['revenue'] / 1000, 0) . 'k' : 'Rp 0' }}
                                    </span>
                                    <div class="w-8 sm:w-11 flex items-end justify-center h-full">
                                        @if($barPct > 0)
                                            <div class="w-full rounded-t-md transition-all duration-500 {{ $isPeak ? 'bg-blue-500 shadow-lg shadow-blue-500/50 ring-2 ring-blue-300' : 'bg-blue-600/75 hover:bg-blue-400' }}" style="height: {{ $barPct }}%;"></div>
                                        @else
                                            <div class="w-full h-1 bg-slate-800 rounded-t-sm"></div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-11 text-center">
                                        <span class="text-xs font-black block {{ $isPeak ? 'text-blue-400' : 'text-slate-300' }}">{{ $day['day_name'] }}</span>
                                        <span class="text-[10px] font-semibold text-slate-500 block">{{ $day['transactions'] }} Tx</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="h-3"></div>
                </div>
            </div>

        @elseif($activeSlide === 2)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Top 3 Performers --}}
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

                {{-- Evaluation Notes --}}
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
                            <p class="text-xs text-emerald-400 py-4 text-center">Semua kasir berkinerja baik & disiplin!</p>
                        @endforelse
                    </div>
                </div>
            </div>

        @elseif($activeSlide === 3)
            <div class="space-y-5">
                {{-- Product Comparison Chart --}}
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Grafik Perbandingan Penjualan Produk Terlaris (Periode Ini vs Periode Lalu)</h3>
                        <span class="text-xs text-slate-400 font-medium">TEFA & Supplier Reguler</span>
                    </div>
                    <div class="space-y-3 pt-1">
                        @foreach($topSellingProducts as $p)
                            @php 
                                $maxQty = max(1, max($p->qty_sold, $p->prev_qty));
                                $currWidth = round(($p->qty_sold / $maxQty) * 100);
                                $prevWidth = round(($p->prev_qty / $maxQty) * 100);
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-white">{{ $p->product->name }}</span>
                                        <span class="px-1.5 py-0.5 {{ $p->is_tefa_internal ? 'bg-blue-950 text-blue-300 border-blue-800' : 'bg-purple-950 text-purple-300 border-purple-800' }} border rounded text-[10px] font-bold">
                                            {{ $p->is_tefa_internal ? 'TEFA' : 'Supplier' }}
                                        </span>
                                    </div>
                                    <span class="font-bold {{ $p->qty_growth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $p->qty_sold }} pcs ({{ $p->qty_growth >= 0 ? '+'.$p->qty_growth.'%' : $p->qty_growth.'%' }} vs {{ $p->prev_qty }} pcs lalu)
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 text-[10px]">
                                        <span class="w-16 text-slate-400 font-semibold shrink-0">Ini: {{ $p->qty_sold }} pcs</span>
                                        <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-700">
                                            <div class="bg-blue-500 h-full rounded-full transition-all" style="width: {{ max(6, $currWidth) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px]">
                                        <span class="w-16 text-slate-400 shrink-0">Lalu: {{ $p->prev_qty }} pcs</span>
                                        <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden p-0.5">
                                            <div class="bg-slate-600 h-full rounded-full transition-all" style="width: {{ max(4, $prevWidth) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 10 Stagnant Items --}}
                <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-3">
                    <h3 class="text-sm font-black text-amber-400 uppercase tracking-wider">10 Produk Stagnan / Slow-Moving</h3>
                    <div class="grid grid-cols-2 gap-3 max-h-[220px] overflow-y-auto pr-1">
                        @foreach($leastSellingProducts as $p)
                            <div class="flex items-center justify-between p-2.5 bg-slate-800/80 rounded-xl border border-slate-700/60 text-xs">
                                <div>
                                    <span class="font-bold text-white block truncate max-w-[180px]">{{ $p->product->name }}</span>
                                    <span class="text-[11px] text-amber-400 font-medium">Stok: {{ $p->stock }} {{ $p->product->unit ?? 'pcs' }}</span>
                                </div>
                                <span class="text-rose-400 font-bold shrink-0">{{ $p->qty_sold }} Terjual</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($activeSlide === 4)
            <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl space-y-6">
                <h3 class="text-xl font-black text-white border-b border-slate-800 pb-4 flex items-center justify-between">
                    <span>Kesimpulan & Catatan Evaluasi Rapat Mingguan</span>
                    <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Superapps TEFA</span>
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

    {{-- Slide Dots Navigation Footer --}}
    <div class="flex items-center justify-center gap-3 pt-4 border-t border-slate-800/80 shrink-0 max-w-7xl mx-auto w-full">
        @for($s = 1; $s <= 4; $s++)
            <button wire:click="setSlide({{ $s }})" class="h-3 rounded-full transition-all {{ $activeSlide === $s ? 'bg-blue-500 w-10' : 'bg-slate-700 hover:bg-slate-600 w-3' }}"></button>
        @endfor
    </div>
</div>
