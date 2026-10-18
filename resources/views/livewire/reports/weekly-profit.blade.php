<div class="p-4 sm:p-6 space-y-6">
    <x-hub-tabs hub="laporan" title="Bagi Hasil Mingguan TEFA" subtitle="Perhitungan pembagian keuntungan bersih operasional, laba jurusan, kas kasir, dan pengelola." />

    <!-- View Mode Tabs & Filter Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl w-fit overflow-x-auto no-scrollbar">
            <button wire:click="$set('viewMode', 'weekly')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'weekly' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                Laporan Mingguan
            </button>
            <button wire:click="$set('viewMode', 'monthly')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'monthly' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                Rekap Bulanan
            </button>
            <button wire:click="$set('viewMode', 'yearly')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'yearly' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                Rekap Tahunan
            </button>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @if ($viewMode === 'weekly')
                <div wire:ignore x-data="{ start: @entangle('startDate').live, end: @entangle('endDate').live, startInstance: null, endInstance: null }"
                    x-init="startInstance = flatpickr($refs.startDate, { dateFormat: 'Y-m-d', defaultDate: start, onChange: (s, d) => start = d }); $watch('start', v => startInstance.setDate(v, false)); endInstance = flatpickr($refs.endDate, { dateFormat: 'Y-m-d', defaultDate: end, onChange: (s, d) => end = d }); $watch('end', v => endInstance.setDate(v, false))"
                    class="flex items-center justify-between bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs gap-3">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Dari</span>
                        <input x-ref="startDate" type="text" readonly
                            class="bg-transparent border-none p-0 text-xs font-bold text-gray-800 dark:text-white focus:ring-0 cursor-pointer w-24">
                    </div>

                    <div class="text-gray-400 dark:text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sampai</span>
                        <input x-ref="endDate" type="text" readonly
                            class="bg-transparent border-none p-0 text-xs font-bold text-gray-800 dark:text-white focus:ring-0 cursor-pointer w-24">
                    </div>
                </div>

                @if ($canProcess)
                    <button wire:click="generateReport"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl shadow-xs font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Proses Bagi Hasil
                    </button>
                @else
                    <div class="flex items-center gap-2">
                        <button disabled
                            class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-xl font-bold text-xs uppercase tracking-wider cursor-not-allowed flex items-center gap-2 border border-gray-200 dark:border-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Jumat - Minggu
                        </button>
                        <button wire:click="$toggle('overrideCanProcess')"
                            class="px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border flex items-center gap-1.5
                                {{ $overrideCanProcess ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/30' : 'bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-gray-100' }}"
                            title="Aktifkan bila minggu tidak penuh karena libur/ujian">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            {{ $overrideCanProcess ? 'Mode Pengecualian Aktif' : 'Minggu Pendek?' }}
                        </button>
                    </div>
                @endif
            @elseif($viewMode === 'monthly')
                <div class="flex items-center bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mr-2">Bulan:</span>
                    <select wire:model.live="selectedMonth"
                        class="bg-transparent border-none p-0 text-xs font-bold text-gray-800 dark:text-white focus:ring-0 cursor-pointer">
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
            @endif
        </div>
    </div>

    @if ($viewMode === 'weekly')
        <!-- SECTION 1: Estimasi Minggu Berjalan (Settlement Saat Ini) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200/80 dark:border-gray-700 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-gray-700/80">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-md text-[11px] font-bold uppercase tracking-wider">
                            Periode Aktif
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">Live Preview</span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                        Estimasi Keuntungan Berjalan ({{ $currentWeek['start']->translatedFormat('d M') }} — {{ $currentWeek['end']->translatedFormat('d M Y') }})
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Status Settlement:</span>
                    @if ($canProcess)
                        <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold flex items-center gap-1.5 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Siap Diproses
                        </span>
                    @else
                        <span class="px-3 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-bold flex items-center gap-1.5 border border-amber-200 dark:border-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu Akhir Pekan
                        </span>
                    @endif
                </div>
            </div>

            <!-- 3 Spacious KPI Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Total Keuntungan -->
                <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/80">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Estimasi Keuntungan</p>
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight mt-1" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($currentWeek['profit'], 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700/80 text-xs font-semibold">
                        <span class="text-emerald-600 dark:text-emerald-400">Tunai: Rp{{ number_format($currentWeek['cash_profit'] ?? 0, 0, ',', '.') }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span class="text-indigo-600 dark:text-indigo-400">Virtual: Rp{{ number_format($currentWeek['non_cash_profit'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Kas Jurusan 40% -->
                <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/80">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kas Jurusan (40%)</p>
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded text-[10px] font-bold">Laba Lembaga</span>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mt-1" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($currentWeek['profit'] * 0.4, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700/80 text-xs font-semibold text-gray-500 dark:text-gray-400">
                        <span>Tunai: Rp{{ number_format(($currentWeek['cash_profit'] ?? 0) * 0.4, 0, ',', '.') }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span>Virtual: Rp{{ number_format(($currentWeek['non_cash_profit'] ?? 0) * 0.4, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Bagi Hasil Eksternal 60% -->
                <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/80">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bagi Hasil Eksternal (60%)</p>
                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded text-[10px] font-bold">Pengelola</span>
                    </div>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight mt-1" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($currentWeek['profit'] * 0.6, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700/80 text-xs font-semibold text-gray-500 dark:text-gray-400">
                        <span>Najmy (30%) &amp; Labantik (30%)</span>
                    </div>
                </div>
            </div>

            <!-- Collapsible: Rincian Keuntungan Harian & Estimasi Pengelola -->
            <div x-data="{ openPreview: false }" class="border-t border-gray-100 dark:border-gray-700 pt-4">
                <button @click="openPreview = !openPreview"
                    class="flex items-center justify-between w-full text-left py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:text-blue-600 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Lihat Rincian Harian &amp; Rincian Porsi Pengelola
                    </span>
                    <span class="text-xs text-blue-600 font-semibold flex items-center gap-1">
                        <span x-text="openPreview ? 'Sembunyikan' : 'Buka Rincian'"></span>
                        <svg class="w-3.5 h-3.5 transition-transform" :class="openPreview ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>

                <div x-show="openPreview" x-collapse x-cloak class="mt-4 space-y-5">
                    <!-- Daily breakdown list -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keuntungan Per Hari &amp; Rekonsiliasi Kas:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($currentWeek['dailyBreakdown'] as $day)
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3.5 border border-gray-200/70 dark:border-gray-700/60">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $day['date']->translatedFormat('l, d M') }}</p>
                                        <p class="text-xs font-black text-blue-600 dark:text-blue-400">Rp{{ number_format($day['net_profit'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1.5 pt-1.5 border-t border-gray-200/60 dark:border-gray-800">
                                        <span>Sistem: Rp{{ number_format($day['system_profit'], 0, ',', '.') }}</span>
                                        @if($day['has_audit'])
                                            @if($day['diff'] < 0)
                                                <span class="text-red-500 font-semibold">Selisih: -Rp{{ number_format(abs($day['diff']), 0, ',', '.') }}</span>
                                            @elseif($day['diff'] > 0)
                                                <span class="text-amber-500 font-semibold">Lebih: +Rp{{ number_format($day['diff'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-emerald-500 font-semibold">Kas Match</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Belum Audit</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Admin Contributions -->
                    <div class="space-y-2 pt-2">
                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alokasi Penerima Bagi Hasil (60%):</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($currentWeek['adminContributions'] as $contrib)
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200/70 dark:border-gray-700/60 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white">{{ $contrib->user->name ?? 'Admin' }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $contrib->portion_name }} ({{ $contrib->percentage }})</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-emerald-600 dark:text-emerald-400">Rp{{ number_format($contrib->user_profit, 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">Tunai: Rp{{ number_format($contrib->cash_profit, 0, ',', '.') }} • Virtual: Rp{{ number_format($contrib->non_cash_profit, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Riwayat Pengambilan Keuntungan Setiap Minggu (Full Width, Clear & Spacious) -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        Riwayat Pengambilan Keuntungan &amp; Bagi Hasil Mingguan
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Daftar lengkap pembagian keuntungan yang telah ditarik dan dibukukan ke kas tunai serta kas virtual.
                    </p>
                </div>
                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold border border-gray-200 dark:border-gray-700 w-fit">
                    Total: {{ $reports->total() }} Laporan
                </span>
            </div>

            <div class="space-y-4">
                @forelse($reports as $report)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-xs hover:border-blue-500/40 transition-all space-y-4">
                        <!-- Card Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/80">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-black uppercase tracking-wider">
                                    Minggu ke-{{ $report->week_number }}
                                </span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($report->week_start)->translatedFormat('d F Y') }} — {{ \Carbon\Carbon::parse($report->week_end)->translatedFormat('d F Y') }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    ({{ $report->month_name }})
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Diproses oleh <strong class="text-gray-700 dark:text-gray-200">{{ $report->creator->name ?? 'Sistem' }}</strong></span>
                                    <span>•</span>
                                    <span>{{ $report->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>

                                <button wire:click.stop="confirmDelete('{{ $report->id }}')"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                    title="Hapus Laporan Bagi Hasil">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- 3 Clear Metric Columns (Prominent, High Legibility) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Total Profit -->
                            <div class="bg-gray-50/80 dark:bg-gray-900/40 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Laba Ditarik</span>
                                <p class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1 tracking-tight" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($report->total_profit, 0, ',', '.') }}
                                </p>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 block">100% Total Keuntungan Bersih</span>
                            </div>

                            <!-- Kas Jurusan 40% -->
                            <div class="bg-gray-50/80 dark:bg-gray-900/40 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kas Jurusan (40%)</span>
                                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded text-[10px] font-bold">Lembaga</span>
                                </div>
                                <p class="text-xl font-black text-gray-900 dark:text-white mt-1 tracking-tight" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($report->kas_amount ?? ($report->total_profit * 0.4), 0, ',', '.') }}
                                </p>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 block">Masuk ke Buku Kas Jurusan</span>
                            </div>

                            <!-- Pengelola 60% -->
                            <div class="bg-gray-50/80 dark:bg-gray-900/40 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bagi Hasil Pengelola (60%)</span>
                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 rounded text-[10px] font-bold">Terbagi</span>
                                </div>
                                <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($report->shared_amount ?? ($report->total_profit * 0.6), 0, ',', '.') }}
                                </p>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 block">Najmy (30%) &amp; Labantik (30%)</span>
                            </div>
                        </div>

                        <!-- Toggle Button to view detail breakdown -->
                        <div class="pt-2">
                            <button wire:click="toggleBreakdown('{{ $report->id }}')"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border
                                    {{ $expandedReportId === $report->id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 border-blue-200 dark:border-blue-800' : 'bg-gray-50 dark:bg-gray-900/30 text-gray-600 dark:text-gray-300 border-gray-200/80 dark:border-gray-700/80 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                    {{ $expandedReportId === $report->id ? 'Sembunyikan Rincian Penarikan & Kategori' : 'Lihat Rincian Penarikan & Pembagian Kategori Produk' }}
                                </span>
                                <svg class="w-4 h-4 transition-transform {{ $expandedReportId === $report->id ? 'rotate-180' : '' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Expanded Breakdown Container -->
                            @if($expandedReportId === $report->id)
                                <div class="mt-4 p-5 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                            Rincian Pembagian Per Penerima &amp; Kategori Transaksi
                                        </h4>
                                        <span class="text-[11px] text-gray-400">Tercatat di Kas Tunai &amp; Kas Virtual</span>
                                    </div>

                                    @if(empty($reportBreakdown))
                                        <div class="py-8 text-center text-xs font-semibold text-gray-400">
                                            Tidak ditemukan detail rincian transaksi pembagian untuk periode ini.
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($reportBreakdown as $recipient)
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-xs space-y-3">
                                                    <!-- Recipient Title & Total -->
                                                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100 dark:border-gray-700">
                                                        <div>
                                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $recipient['name'] }}</p>
                                                            <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded text-[10px] font-bold">
                                                                Porsi: {{ $recipient['percentage'] }}
                                                            </span>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-xs text-gray-400">Total Diterima</p>
                                                            <p class="text-base font-black text-blue-600 dark:text-blue-400" :class="censorMode ? 'privacy-blur' : ''">
                                                                Rp{{ number_format($recipient['total'], 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Sub-categories breakdown list -->
                                                    <div class="space-y-2">
                                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rincian Per Kategori Produk:</p>
                                                        <div class="space-y-1.5">
                                                            @foreach($recipient['categories'] as $cat)
                                                                <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                                                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $cat['name'] }}</span>
                                                                    <span class="font-bold text-red-500 dark:text-red-400">-Rp{{ number_format($cat['amount'], 0, ',', '.') }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Belum Ada Riwayat Laporan Bagi Hasil</p>
                        <p class="text-xs text-gray-400 mt-1">Laporan bagi hasil mingguan yang diproses akan tersimpan rapi di sini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $reports->links('livewire.partials.custom-pagination') }}
            </div>
        </div>

    @elseif ($viewMode === 'monthly')
        <!-- MONTHLY RECAP MODE -->
        <div class="space-y-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                    Rekap Keuntungan Bulanan
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Ringkasan pembagian laba yang diakumulasikan per bulan kalender.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($monthlyReports as $mReport)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xs space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ $mReport->month_name }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $mReport->weeks_count }} Laporan Mingguan</p>
                            </div>
                            <span class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/40 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Keuntungan</span>
                            <p class="text-xl font-black text-gray-900 dark:text-white mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                Rp{{ number_format($mReport->total_profit, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3.5 bg-blue-50/60 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/40">
                                <p class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">Kas Jurusan (40%)</p>
                                <p class="text-sm font-black text-blue-700 dark:text-blue-300 mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($mReport->total_kas, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="p-3.5 bg-emerald-50/60 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/40">
                                <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">Bagi Hasil (60%)</p>
                                <p class="text-sm font-black text-emerald-700 dark:text-emerald-300 mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                    Rp{{ number_format($mReport->total_shared, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 bg-white dark:bg-gray-800 rounded-2xl text-center border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-400">Belum ada rekap data untuk bulan ini</p>
                    </div>
                @endforelse
            </div>
        </div>

    @elseif($viewMode === 'yearly')
        <!-- YEARLY RECAP MODE -->
        @if ($yearlyData && $yearlyData->total_profit > 0)
            <div class="space-y-6">
                <!-- Main Yearly Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-700 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-md text-xs font-bold uppercase tracking-wider">
                                Rekap Tahunan
                            </span>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white mt-2">
                                Tahun {{ $currentYear }}
                            </h2>
                            <p class="text-xs text-gray-400 mt-1">
                                Akumulasi dari {{ $yearlyData->total_months }} Bulan • {{ $yearlyData->total_weeks }} Periode Mingguan
                            </p>
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Keuntungan Tahunan</span>
                            <p class="text-3xl font-black text-blue-600 dark:text-blue-400 tracking-tight mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                Rp{{ number_format($yearlyData->total_profit, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="p-5 bg-blue-50/60 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/40">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total Kas Jurusan (40%)</span>
                            <p class="text-2xl font-black text-blue-700 dark:text-blue-300 mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                Rp{{ number_format($yearlyData->total_kas, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alokasi kas pengembangan jurusan</p>
                        </div>

                        <div class="p-5 bg-emerald-50/60 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/40">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Total Bagi Hasil Eksternal (60%)</span>
                            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1" :class="censorMode ? 'privacy-blur' : ''">
                                Rp{{ number_format($yearlyData->total_shared, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alokasi pengelola eksternal</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly breakdown in yearly mode -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                        Rincian Per Bulan di Tahun {{ $currentYear }}
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($allMonthlyData as $mData)
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-xs space-y-3">
                                <h4 class="text-xs font-bold text-gray-800 dark:text-white uppercase">{{ $mData->month_name }}</h4>
                                <div class="space-y-1.5 text-xs pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Profit:</span>
                                        <span class="font-bold text-blue-600 dark:text-blue-400">Rp{{ number_format($mData->total_profit, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Kas (40%):</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">Rp{{ number_format($mData->total_kas, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Bagian (60%):</span>
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Rp{{ number_format($mData->total_shared, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="py-20 bg-white dark:bg-gray-800 rounded-2xl text-center border border-dashed border-gray-300 dark:border-gray-700">
                <p class="text-sm font-bold text-gray-600 dark:text-gray-400">Belum ada data untuk tahun {{ $currentYear }}</p>
            </div>
        @endif
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('showDeleteModal').live }" x-show="show" x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="show = false"
            class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-xl flex flex-col p-6 gap-5 border border-gray-200 dark:border-gray-700">
            <div class="text-center">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Hapus Laporan Bagi Hasil?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                    Tindakan ini akan membatalkan pembukuan bagi hasil pada periode ini dari buku kas tunai dan kas virtual. Tindakan tidak dapat diurungkan.
                </p>
            </div>

            <div class="flex gap-3">
                <button @click="show = false"
                    class="flex-1 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button wire:click="delete"
                    class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-xs transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
