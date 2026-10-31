<div class="p-4 sm:p-6">
    <x-hub-tabs hub="laporan" title="Rekap Bulanan" subtitle="Konsolidasi laporan omzet bulanan, profit bersih TEFA, dan performa per kategori." />

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
        <div></div>
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <div class="flex items-center bg-white dark:bg-slate-900 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 transition-all">
                <svg class="w-4 h-4 text-blue-600 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                <select wire:model.live="selectedMonth" class="border-none p-0 focus:ring-0 font-bold text-xs bg-transparent dark:text-white mr-2">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
                <select wire:model.live="selectedYear" class="border-none p-0 focus:ring-0 font-bold text-xs bg-transparent dark:text-white">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            @if($recap)
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Mengekspor...</span>
            </button>
            @endif
        </div>
    </div>

    @if($recap)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-sm relative overflow-hidden group" id="card-revenue">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-48 h-48 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Total Omzet Tunai</h3>
            <p class="text-5xl font-black tracking-tighter" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_real, 0, ',', '.') }}</p>
            <div class="mt-6 pt-6 border-t border-white/10 space-y-2">
                <div class="flex justify-between items-center text-[10px] font-bold opacity-70 uppercase tracking-widest">
                    <span>Murni Jurusan:</span>
                    <span :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_internal_revenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] font-bold opacity-40 uppercase tracking-widest">
                    <span>Gross Total:</span>
                    <span :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_all, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group" id="card-profit">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-48 h-48 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Keuntungan Estimasi</h3>
            <p class="text-5xl font-black tracking-tighter text-primary-red" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_profit, 0, ',', '.') }}</p>
            <p class="mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Modal Terputar <span :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_modal, 0, ',', '.') }}</span></p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between" id="card-volume">
            <div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6">Volume Transaksi</h3>
                <div class="flex items-baseline gap-4">
                    <p class="text-5xl font-black italic tracking-tighter text-gray-800 dark:text-white">{{ $recap->total_transactions }}</p>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest italic">Order Sukses</span>
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <div class="flex-1 p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Rata-rata/Hari</p>
                    <p class="text-lg font-black text-gray-800 dark:text-white" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_real / max(1, $recap->days_count), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-10 mb-12 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Tren Profit Bulanan</h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Visualisasi Keuntungan Harian Selama Sebulan</p>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-primary-red rounded-full mr-2"></div>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Daily Profit</span>
            </div>
        </div>
        <div class="relative h-[300px]" wire:ignore>
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>

    <!-- Category Performance Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-10 mb-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">Performa Per Kategori</h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Rekap Modal & Keuntungan Berdasarkan Jenis Produk Bulan Ini</p>
            </div>
            <div class="p-4 bg-primary-red/5 rounded-2xl text-primary-red">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
        </div>

        <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto no-scrollbar">
            <table class="w-full text-left" id="category-recap-table">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Volume</th>
                        <th class="pb-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Total Modal (HPP)</th>
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
                                <a href="{{ route('category-detail', ['categoryId' => $stats->id, 'type' => 'monthly', 'month' => $selectedMonth, 'year' => $selectedYear]) }}" wire:navigate class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white text-[10px] font-bold uppercase rounded-lg transition-all flex items-center gap-1 border border-blue-200 dark:border-blue-800">
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
                    <a href="{{ route('category-detail', ['categoryId' => $stats->id, 'type' => 'monthly', 'month' => $selectedMonth, 'year' => $selectedYear]) }}" wire:navigate class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase rounded-lg border border-blue-200 dark:border-blue-800 flex items-center gap-1">
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


    <script>
        document.addEventListener('livewire:navigated', function () {
            const canvas = document.getElementById('monthlyTrendChart');
            if (!canvas) return;

            // Destroy existing instance if it exists
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            const breakdown = @json($dailyBreakdown);
            
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: breakdown.map(d => d.date.split('-')[2]),
                    datasets: [
                        {
                            label: 'Daily Profit',
                            data: breakdown.map(d => d.total_profit),
                            backgroundColor: '#ef4444',
                            borderRadius: 8,
                            hoverBackgroundColor: '#dc2626'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Profit: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                },
                                title: function(context) {
                                    const date = breakdown[context[0].dataIndex].date;
                                    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: { size: 10, weight: 'bold' },
                                color: '#9ca3af',
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', { 
                                        style: 'currency', 
                                        currency: 'IDR', 
                                        maximumFractionDigits: 0 
                                    }).format(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                        }
                    }
                }
            });
        });
    </script>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Breakdown Harian ({{ \Carbon\Carbon::create(null, $selectedMonth)->translatedFormat('F') }})</h2>
        </div>
        <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Transaksi</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Pendapatan</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Kas Fisik</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Selisih</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Profit</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($dailyBreakdown as $day)
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ \Carbon\Carbon::parse($day->date)->translatedFormat('d F Y') }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Minggu ke-{{ $day->month_week }}</div>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $day->total_transactions }} trx</span>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap text-xs font-bold text-blue-600 dark:text-blue-400 font-mono">
                            Rp{{ number_format($day->total_revenue_real, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap text-xs font-semibold text-gray-800 dark:text-white font-mono">
                            @if($day->actual_cash !== null)
                                <div>Rp{{ number_format((float)$day->actual_cash - (float)$day->starting_change_cash, 0, ',', '.') }}</div>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded text-[10px] text-gray-400 font-normal">Belum Audit</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap text-xs font-bold font-mono">
                            @if($day->actual_cash !== null)
                                @php
                                    $diff = ((float)$day->actual_cash - (float)$day->starting_change_cash) - (float)$day->total_revenue_real;
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
                            Rp{{ number_format($day->total_profit, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('daily-recap', ['date' => $day->date]) }}" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-lg transition-all inline-block">
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
            @foreach($dailyBreakdown as $day)
            <div class="p-3 bg-white dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col gap-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ \Carbon\Carbon::parse($day->date)->translatedFormat('d M Y') }}</span>
                        <div class="text-[10px] text-gray-400 mt-0.5">Minggu ke-{{ $day->month_week }} &bull; {{ $day->total_transactions }} Transaksi</div>
                    </div>
                    <a href="{{ route('daily-recap', ['date' => $day->date]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800 shrink-0">
                        Detail
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-2 py-2 px-2.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-center text-xs">
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Omzet</span>
                        <span class="font-bold font-mono text-blue-600 dark:text-blue-400 mt-0.5">Rp{{ number_format($day->total_revenue_real, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Profit</span>
                        <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-0.5">Rp{{ number_format($day->total_profit, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-gray-400 block font-medium">Selisih</span>
                        @if($day->actual_cash !== null)
                            @php
                                $diff = ((float)$day->actual_cash - (float)$day->starting_change_cash) - (float)$day->total_revenue_real;
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
            <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
        </div>
        <h3 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Belum Ada Catatan</h3>
        <p class="text-gray-400 font-bold text-sm mt-4 uppercase tracking-[0.3em] italic">Tidak ada aktivitas transaksi pada bulan {{ \Carbon\Carbon::create(null, $selectedMonth)->translatedFormat('F Y') }}</p>
    </div>
    @endif
</div>
