<div class="p-6">
    <div class="flex items-center justify-between mb-12">
        <div>
            <h1 class="text-5xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Dashboard Digital</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.3em] italic">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('kasir') }}" class="px-10 py-5 bg-primary-red text-white rounded-[2rem] shadow-2xl shadow-red-500/30 font-black italic uppercase tracking-wider transition transform hover:-translate-y-2 active:scale-95 flex items-center">
                <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Buka Kasir
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
        <div class="bg-primary-blue rounded-[3rem] p-10 shadow-2xl shadow-blue-900/30 border border-transparent hover:scale-105 transition-all group overflow-hidden relative">
            <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-48 h-48 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="flex items-center justify-between mb-8">
                <div class="p-5 bg-white/10 rounded-2xl text-white">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/60 bg-white/5 px-4 py-2 rounded-full">Omzet</span>
            </div>
            <h3 class="text-white/60 text-[10px] font-black uppercase tracking-widest mb-1">Total Omzet Tunai</h3>
            <p class="text-4xl font-black text-white italic tracking-tighter">Rp{{ number_format($stats->today_revenue, 0, ',', '.') }}</p>
            <div class="mt-4 pt-4 border-t border-white/10 flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <span class="text-[9px] font-bold text-white/40 uppercase tracking-widest">Murni Jurusan:</span>
                    <span class="text-xs font-black text-white italic">Rp{{ number_format($stats->today_internal_revenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-[9px] font-black uppercase px-2 py-1 rounded-md bg-white/20 text-white">
                        {{ $stats->revenue_change >= 0 ? '+' : '' }}{{ number_format($stats->revenue_change, 1) }}% vs Kemarin
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-primary-red transition-all group overflow-hidden relative">
            <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-48 h-48 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
            </div>
            <div class="flex items-center justify-between mb-8">
                <div class="p-5 bg-primary-red/10 rounded-2xl text-primary-red">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-primary-red bg-primary-red/5 px-4 py-2 rounded-full">Profit</span>
            </div>
            <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Keuntungan Bersih</h3>
            <p class="text-4xl font-black text-primary-red italic tracking-tighter">Rp{{ number_format($stats->today_profit, 0, ',', '.') }}</p>
            <div class="mt-4 flex items-center">
                <span class="text-[9px] font-black uppercase px-2 py-1 rounded-md bg-primary-red/10 text-primary-red">
                    {{ $stats->profit_change >= 0 ? '+' : '' }}{{ number_format($stats->profit_change, 1) }}% vs Kemarin
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-primary-blue transition-all group overflow-hidden relative">
            <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-48 h-48 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.56-7.43H5.12"/></svg>
            </div>
            <div class="flex items-center justify-between mb-8">
                <div class="p-5 bg-primary-blue/10 rounded-2xl text-primary-blue">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-primary-blue bg-primary-blue/5 px-4 py-2 rounded-full">Volume</span>
            </div>
            <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Penjualan</h3>
            <p class="text-4xl font-black text-gray-800 dark:text-white italic tracking-tighter">{{ $stats->today_transactions }}</p>
            <div class="mt-4 flex items-center">
                <span class="text-[9px] font-black uppercase px-2 py-1 rounded-md bg-gray-100 text-gray-500">
                    {{ $stats->transactions_change >= 0 ? '+' : '' }}{{ number_format($stats->transactions_change, 1) }}% vs Kemarin
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-primary-red transition-all group overflow-hidden relative">
            <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-48 h-48 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="flex items-center justify-between mb-8">
                <div class="p-5 bg-primary-red/10 rounded-2xl text-primary-red">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15"/><path d="M7 11h8"/><path d="M7 15h8"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-primary-red bg-primary-red/5 px-4 py-2 rounded-full">Rata-rata</span>
            </div>
            <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Tiket Rata-rata</h3>
            <p class="text-4xl font-black text-gray-800 dark:text-white italic tracking-tighter">Rp{{ number_format($stats->avg_transaction, 0, ',', '.') }}</p>
            <div class="mt-4 flex items-center">
                <span class="text-[9px] font-black uppercase px-2 py-1 rounded-md bg-gray-100 text-gray-500">
                    Nilai Per Transaksi
                </span>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 mb-16 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">Grafik Performa Mingguan</h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Analisis Omzet & Profit 7 Hari Terakhir</p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-primary-blue rounded-full mr-2"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Omzet</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-primary-red rounded-full mr-2"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Profit</span>
                </div>
            </div>
        </div>
        <div class="relative h-[400px]" wire:ignore>
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', function () {
            const ctx = document.getElementById('weeklyChart');
            if (!ctx) return;

            const data = @json($weeklyData);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.day),
                    datasets: [
                        {
                            label: 'Omzet',
                            data: data.map(d => d.revenue),
                            borderColor: '#1e40af',
                            backgroundColor: 'rgba(30, 64, 175, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 6,
                            pointBackgroundColor: '#1e40af',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Profit',
                            data: data.map(d => d.profit),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 6,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                    }
                                    return label;
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
                                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { size: 10, weight: 'bold' },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });
        });
    </script>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Transaksi Terakhir</h2>
                <a href="{{ route('transactions') }}" class="text-[10px] font-black text-primary-blue uppercase tracking-widest hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jam</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-10 py-8 text-xs font-black text-gray-400">{{ $tx->transacted_at->format('H:i') }}</td>
                            <td class="px-10 py-8">
                                <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $tx->product->name }}</div>
                                <div class="text-[10px] font-bold text-gray-400 italic mt-1">Qty: {{ $tx->quantity }}</div>
                            </td>
                            <td class="px-10 py-8 text-lg font-black text-primary-red italic">Rp{{ number_format($tx->total_price, 0, ',', '.') }}</td>
                            <td class="px-10 py-8">
                                @php
                                    $statusClasses = [
                                        'uang_diterima' => 'bg-green-100 text-green-700',
                                        'belum_kembalian' => 'bg-primary-blue/10 text-primary-blue',
                                        'belum_menerima_uang' => 'bg-primary-red/10 text-primary-red',
                                        'uang_dipinjam' => 'bg-orange-100 text-orange-700',
                                    ];
                                @endphp
                                <span class="px-4 py-1.5 text-[9px] font-black rounded-full uppercase tracking-widest {{ $statusClasses[$tx->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ str_replace('_', ' ', $tx->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-10 py-32 text-center text-gray-400 font-bold uppercase text-xs tracking-widest italic opacity-20">Belum ada transaksi hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-10 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Produk Terlaris</h2>
            </div>
            <div class="p-10 space-y-10">
                @forelse($topProducts as $top)
                <div class="flex items-center group">
                    <div class="flex-shrink-0 w-16 h-16 bg-primary-blue dark:bg-gray-900 text-white rounded-[1.5rem] flex items-center justify-center font-black italic shadow-2xl shadow-blue-900/10 group-hover:scale-110 transition-transform">
                        {{ $loop->iteration }}
                    </div>
                    <div class="ml-6 flex-1">
                        <h4 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight leading-tight">{{ $top->product->name }}</h4>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ $top->total_qty }} Unit Terjual</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-black text-primary-red italic">Rp{{ number_format($top->total_revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <div class="py-32 text-center opacity-20">
                    <svg class="w-24 h-24 mx-auto mb-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/></svg>
                    <p class="text-xs font-black uppercase tracking-widest italic">Data Kosong</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
