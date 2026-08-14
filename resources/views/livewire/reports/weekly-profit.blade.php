<div class="p-10 space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">
        <div class="w-full lg:w-auto">
            <h1 class="text-5xl lg:text-6xl font-black italic uppercase tracking-tighter text-primary-blue leading-none">Bagi Hasil</h1>
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 mt-6 w-full">
                <button wire:click="$set('viewMode', 'weekly')"
                    class="flex-1 sm:flex-initial px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $viewMode === 'weekly' ? 'bg-primary-blue text-white shadow-lg' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">Laporan
                    Mingguan</button>
                <button wire:click="$set('viewMode', 'monthly')"
                    class="flex-1 sm:flex-initial px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $viewMode === 'monthly' ? 'bg-primary-blue text-white shadow-lg' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">Rekap
                    Bulanan</button>
                <button wire:click="$set('viewMode', 'yearly')"
                    class="flex-1 sm:flex-initial px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $viewMode === 'yearly' ? 'bg-primary-blue text-white shadow-lg' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">Rekap
                    Tahunan</button>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full lg:w-auto">
            @if ($viewMode === 'weekly')
                <div wire:ignore x-data="{ start: @entangle('startDate').live, end: @entangle('endDate').live, startInstance: null, endInstance: null }"
                    x-init="startInstance = flatpickr($refs.startDate, { dateFormat: 'Y-m-d', defaultDate: start, onChange: (s, d) => start = d }); $watch('start', v => startInstance.setDate(v, false)); endInstance = flatpickr($refs.endDate, { dateFormat: 'Y-m-d', defaultDate: end, onChange: (s, d) => end = d }); $watch('end', v => endInstance.setDate(v, false))"
                    class="flex items-center justify-between bg-white dark:bg-gray-900 p-2 rounded-[2rem] shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 cursor-pointer w-full sm:w-auto">
                    <div class="px-5 py-2 flex-1 sm:flex-initial">
                        <p class="text-[8px] font-black text-primary-blue uppercase tracking-widest mb-1">DARI TANGGAL</p>
                        <input x-ref="startDate" type="text" readonly
                            class="bg-transparent border-none p-0 text-[11px] font-black text-gray-800 dark:text-white focus:ring-0 cursor-pointer w-20">
                    </div>

                    <div class="w-8 h-8 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>

                    <div class="px-5 py-2 flex-1 sm:flex-initial">
                        <p class="text-[8px] font-black text-primary-red uppercase tracking-widest mb-1">SAMPAI TANGGAL</p>
                        <input x-ref="endDate" type="text" readonly
                            class="bg-transparent border-none p-0 text-[11px] font-black text-gray-800 dark:text-white focus:ring-0 cursor-pointer w-20">
                    </div>
                </div>

                @if ($canProcess)
                    <button wire:click="generateReport"
                        class="px-10 py-5 bg-primary-blue text-white rounded-[2rem] shadow-2xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 w-full sm:w-auto">
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                        PROSES BAGI HASIL
                    </button>
                @else
                    <button disabled
                        class="px-10 py-5 bg-gray-200 dark:bg-gray-800 text-gray-405 rounded-[2rem] font-black italic uppercase text-xs tracking-widest cursor-not-allowed flex items-center justify-center gap-4 opacity-50 w-full sm:w-auto">
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        JUMAT - MINGGU
                    </button>
                @endif
            @elseif($viewMode === 'monthly')
                <div
                    class="flex items-center bg-white dark:bg-gray-900 p-2 rounded-[2rem] shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 w-full sm:w-auto">
                    <div class="px-6 py-2 w-full">
                        <p class="text-[8px] font-black text-primary-blue uppercase tracking-widest mb-1">PILIH BULAN</p>
                        <select wire:model.live="selectedMonth"
                            class="bg-transparent border-none p-0 text-[11px] font-black text-gray-800 dark:text-white focus:ring-0 cursor-pointer w-full">
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($viewMode === 'weekly')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left: Current Week Live Preview -->
            <div class="lg:col-span-2 space-y-10">
                <div
                    class="bg-white dark:bg-gray-900 rounded-[4rem] p-12 shadow-2xl shadow-blue-900/5 border border-gray-50 dark:border-gray-800 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 p-20 opacity-[0.03] group-hover:scale-110 transition-transform duration-700 pointer-events-none">
                        <svg class="w-64 h-64 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-12 gap-6">
                            <div>
                                <span
                                    class="px-5 py-2 bg-blue-50 dark:bg-blue-900/20 text-primary-blue rounded-full text-[10px] font-black uppercase tracking-widest">Periode
                                    Terpilih</span>
                                <h2
                                    class="text-3xl sm:text-4xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mt-4">
                                    Estimasi Keuntungan</h2>
                                <p class="text-sm font-bold text-gray-400 mt-2 italic uppercase">
                                    {{ $currentWeek['start']->translatedFormat('d M') }} —
                                    {{ $currentWeek['end']->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Omzet
                                    Internal</p>
                                <p class="text-4xl sm:text-5xl font-black text-primary-blue italic tracking-tighter" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($currentWeek['profit'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div
                                class="bg-gray-50 dark:bg-gray-800/50 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-700/50">
                                <div class="flex items-center gap-4 mb-6">
                                    <div
                                        class="w-12 h-12 bg-primary-red/10 rounded-2xl flex items-center justify-center text-primary-red">
                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                            <polyline points="9 22 9 12 15 12 15 22" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kas
                                            Jurusan (40%)</p>
                                        <p
                                            class="text-2xl font-black text-gray-800 dark:text-white italic tracking-tighter mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                            Rp{{ number_format($currentWeek['profit'] * 0.4, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-primary-blue rounded-[3rem] p-10 text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden">
                                <div class="flex items-center gap-4 mb-6 relative z-10">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-white/60 uppercase tracking-widest">Bagi
                                            Hasil Eksternal (60%)</p>
                                        <p class="text-2xl font-black italic tracking-tighter mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                            Rp{{ number_format($currentWeek['profit'] * 0.6, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Profit Breakdown -->
                <div x-data="{ open: false }" class="bg-gray-50 dark:bg-gray-900/50 rounded-[4rem] p-12 border border-gray-100 dark:border-gray-800/50 mb-10 transition-all duration-300">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left focus:outline-none cursor-pointer">
                        <div>
                            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                                Rincian Keuntungan Harian</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">Detail keuntungan sistem dan penyesuaian selisih kas per hari</p>
                        </div>
                        <div class="w-12 h-12 bg-white dark:bg-gray-900 rounded-[1.5rem] flex items-center justify-center border border-gray-100 dark:border-gray-800 transition-transform duration-300 shadow-sm" :class="open ? 'rotate-180' : ''">
                            <svg class="w-5 h-5 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="open" x-collapse x-cloak class="grid grid-cols-1 gap-6 mt-10">
                        @foreach($currentWeek['dailyBreakdown'] as $day)
                            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all">
                                <div>
                                    <p class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                        {{ $day['date']->translatedFormat('l, d F Y') }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        <span>Sistem: <strong class="text-gray-700 dark:text-gray-200">Rp{{ number_format($day['system_profit'], 0, ',', '.') }}</strong></span>
                                        @if($day['has_audit'])
                                            <span>•</span>
                                            @if($day['diff'] < 0)
                                                <span class="text-primary-red font-black">Selisih Kurang: -Rp{{ number_format(abs($day['diff']), 0, ',', '.') }}</span>
                                            @elseif($day['diff'] > 0)
                                                <span class="text-amber-500 font-black">Selisih Lebih: +Rp{{ number_format($day['diff'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-green-500 font-black">Uang Kas Match</span>
                                            @endif
                                        @else
                                            <span>•</span>
                                            <span class="text-gray-300 dark:text-gray-600">Belum Audit Fisik</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Keuntungan Riil</p>
                                    <p class="text-xl font-black text-primary-blue italic mt-1">
                                        Rp{{ number_format($day['net_profit'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Admin Contribution Breakdown -->
                <div
                    class="bg-gray-50 dark:bg-gray-900/50 rounded-[4rem] p-12 border border-gray-100 dark:border-gray-800/50">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h3
                                class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                                Rincian Pembagian Eksternal</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">Porsi pembagian bagi hasil mingguan (Najmy 30% & Labantik 30%)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($currentWeek['adminContributions'] as $contrib)
                            <div
                                class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 flex items-center justify-between border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all group">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-lg font-black text-primary-blue border border-gray-100 dark:border-gray-700 group-hover:scale-110 transition-transform">
                                        {{ substr($contrib->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                            {{ $contrib->user->name ?? 'Unknown Admin' }}</p>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                            {{ $contrib->portion_name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-black text-primary-blue italic">
                                        Rp{{ number_format($contrib->user_profit, 0, ',', '.') }}</p>
                                    <p class="text-[9px] font-bold text-green-500 uppercase tracking-widest mt-1">
                                        Persentase: {{ $contrib->percentage }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-20 text-center opacity-20">
                                <p class="text-xs font-black uppercase tracking-widest italic">Belum ada rincian pembagian</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right: Historical Reports -->
            <div class="space-y-10">
                <div
                    class="bg-white dark:bg-gray-900 rounded-[4rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-50 dark:border-gray-800 h-full">
                    <h3
                        class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-8">
                        Riwayat Laporan</h3>

                    <div class="space-y-6">
                        @forelse($reports as $report)
                            <div
                                class="p-8 bg-gray-50 dark:bg-gray-800/50 rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 hover:border-primary-blue/30 transition-all group relative">
                                <button wire:click.stop="confirmDelete('{{ $report->id }}')"
                                    class="absolute top-6 right-6 p-2 text-gray-300 hover:text-primary-red opacity-0 group-hover:opacity-100 transition-all z-30 cursor-pointer pointer-events-auto">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18" />
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                    </svg>
                                </button>
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <span
                                            class="px-4 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full text-[8px] font-black uppercase tracking-widest">Minggu
                                            {{ $report->week_number }}</span>
                                        <h4
                                            class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight mt-3">
                                            {{ $report->month_name }}</h4>
                                        <p
                                            class="text-[8px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-2">
                                            {{ $report->created_at->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="h-px bg-gray-100 dark:bg-gray-700 mb-6"></div>
                                <div class="flex justify-between items-center">
                                    <div class="text-left">
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Profit
                                        </p>
                                        <p class="text-xs font-black text-primary-blue italic">
                                            Rp{{ number_format($report->total_profit, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Share
                                        </p>
                                        <p class="text-xs font-black text-green-500 italic">
                                            Rp{{ number_format($report->shared_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-32 text-center opacity-20">
                                <p class="text-xs font-black uppercase tracking-widest italic">Belum ada riwayat</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $reports->links('livewire.partials.custom-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    @elseif ($viewMode === 'monthly')
        <!-- Monthly Rekap Mode -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($monthlyReports as $mReport)
                <div
                    class="bg-white dark:bg-gray-900 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 shadow-xl shadow-blue-900/5 relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-10">
                            <div>
                                <h3
                                    class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                                    {{ $mReport->month_name }}</h3>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                    {{ $mReport->weeks_count }} Laporan Mingguan</p>
                                <p
                                    class="text-[8px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-widest mt-2">
                                    {{ $mReport->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-primary-blue/10 rounded-2xl flex items-center justify-center text-primary-blue">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 2v4" />
                                    <path d="M16 2v4" />
                                    <rect width="18" height="18" x="3" y="4" rx="2" />
                                    <path d="M3 10h18" />
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex justify-between items-center p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total
                                    Keuntungan</span>
                                <span
                                    class="text-lg font-black text-gray-800 dark:text-white italic" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($mReport->total_profit, 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-primary-blue/10">
                                    <p class="text-[9px] font-black text-primary-blue uppercase tracking-widest mb-2">
                                        Kas Jurusan</p>
                                    <p class="text-sm font-black text-primary-blue italic" :class="censorMode ? 'privacy-blur' : ''">
                                        Rp{{ number_format($mReport->total_kas, 0, ',', '.') }}</p>
                                </div>
                                <div
                                    class="p-6 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-500/10">
                                    <p class="text-[9px] font-black text-green-500 uppercase tracking-widest mb-2">Bagi
                                        Hasil</p>
                                    <p class="text-sm font-black text-green-500 italic" :class="censorMode ? 'privacy-blur' : ''">
                                        Rp{{ number_format($mReport->total_shared, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-3 py-32 bg-white dark:bg-gray-900 rounded-[4rem] text-center opacity-20 border border-dashed border-gray-300">
                    <p class="text-xs font-black uppercase tracking-widest italic">Belum ada data untuk bulan ini</p>
                </div>
            @endforelse
        </div>
    @elseif($viewMode === 'yearly')
        <!-- Yearly Recap Mode -->
        @if ($yearlyData && $yearlyData->total_profit > 0)
            <div class="grid grid-cols-1 gap-10">
                <!-- Main Yearly Summary Card -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-[4rem] p-16 shadow-2xl shadow-blue-900/5 border border-gray-50 dark:border-gray-800 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 p-20 opacity-[0.03] group-hover:scale-110 transition-transform duration-700 pointer-events-none">
                        <svg class="w-64 h-64 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v18" />
                            <path d="M3 9h18" />
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-16">
                            <div>
                                <span
                                    class="px-5 py-2 bg-blue-50 dark:bg-blue-900/20 text-primary-blue rounded-full text-[10px] font-black uppercase tracking-widest">Rekap
                                    Tahunan</span>
                                <h2
                                    class="text-5xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mt-6">
                                    Tahun {{ $currentYear }}</h2>
                                <p class="text-sm font-bold text-gray-400 mt-3 italic uppercase">
                                    {{ $yearlyData->total_months }} Bulan • {{ $yearlyData->total_weeks }} Minggu</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Total
                                    Keuntungan Tahunan</p>
                                <p class="text-6xl font-black text-primary-blue italic tracking-tighter" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($yearlyData->total_profit, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div
                                class="bg-gray-50 dark:bg-gray-800/50 rounded-[3rem] p-12 border border-gray-100 dark:border-gray-700/50">
                                <div class="flex items-center gap-4 mb-8">
                                    <div
                                        class="w-14 h-14 bg-primary-red/10 rounded-2xl flex items-center justify-center text-primary-red">
                                        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                            <polyline points="9 22 9 12 15 12 15 22" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kas
                                            Jurusan (50%)</p>
                                        <p
                                            class="text-3xl font-black text-gray-800 dark:text-white italic tracking-tighter mt-2" :class="censorMode ? 'privacy-blur' : ''">
                                            Rp{{ number_format($yearlyData->total_kas, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-primary-blue rounded-[3rem] p-12 text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden">
                                <div class="flex items-center gap-4 mb-8 relative z-10">
                                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                                        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-white/60 uppercase tracking-widest">Bagi
                                            Hasil Admin (50%)</p>
                                        <p class="text-3xl font-black italic tracking-tighter mt-2" :class="censorMode ? 'privacy-blur' : ''">
                                            Rp{{ number_format($yearlyData->total_shared, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Breakdown -->
                        <div class="mt-16 pt-16 border-t border-gray-100 dark:border-gray-800">
                            <h3
                                class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-10">
                                Rincian Per Bulan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @forelse($allMonthlyData as $mData)
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800/50 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-700/50 hover:border-primary-blue/30 transition-all">
                                        <h4
                                            class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight mb-6">
                                            {{ $mData->month_name }}</h4>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center">
                                                <p
                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                    Profit</p>
                                                <p class="text-sm font-black text-primary-blue italic">
                                                    Rp{{ number_format($mData->total_profit, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="h-px bg-gray-200 dark:bg-gray-700"></div>
                                            <div class="flex justify-between items-center">
                                                <p
                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                    Kas</p>
                                                <p class="text-sm font-black text-gray-800 dark:text-white italic">
                                                    Rp{{ number_format($mData->total_kas, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <p
                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                    Bagian</p>
                                                <p class="text-sm font-black text-green-500 italic">
                                                    Rp{{ number_format($mData->total_shared, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div
                class="py-32 bg-white dark:bg-gray-900 rounded-[4rem] text-center opacity-20 border border-dashed border-gray-300">
                <p class="text-xs font-black uppercase tracking-widest italic">Belum ada data untuk tahun ini</p>
            </div>
        @endif
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('showDeleteModal').live }" x-show="show" x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-sm rounded-[3rem] shadow-2xl flex flex-col p-10 gap-8">
            <div class="text-center">
                <div
                    class="w-20 h-20 bg-primary-red/10 text-primary-red rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Hapus
                    Laporan?</h3>
                <p class="text-xs font-bold text-gray-400 mt-4 uppercase tracking-widest leading-loose">Tindakan ini
                    tidak dapat dibatalkan. Laporan bagi hasil akan dihapus permanen.</p>
            </div>

            <div class="flex gap-4 mt-2">
                <button @click="show = false"
                    class="flex-1 py-4 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-gray-100 transition-all">Batal</button>
                <button wire:click="delete"
                    class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 active:scale-95 transition-all">Ya,
                    Hapus</button>
            </div>
        </div>
    </div>
</div>
