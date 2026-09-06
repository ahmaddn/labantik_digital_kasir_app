<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">Buku Kas Internal</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Pencatatan Uang Riil & Operasional</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="px-5 sm:px-5 py-2.5 sm:py-4 bg-green-500 text-white rounded-2xl shadow-xl shadow-green-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center justify-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Mengekspor...</span>
            </button>

            @if($isSubUnit)
            <button wire:click="openConsolidateModal" class="px-5 sm:px-5 py-2.5 sm:py-4 bg-amber-500 text-white rounded-2xl shadow-xl shadow-amber-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center justify-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 5-5-5-5-8 3"/><path d="M7 21A14 14 0 0 1 21 7"/></svg>
                Gabungkan Ke Kas Induk
            </button>
            @endif

            <button wire:click="openModal" class="px-5 sm:px-5 py-2.5 sm:py-4 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center justify-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Catat Kas Baru
            </button>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-[2rem] p-5 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 items-end gap-4 w-full xl:w-auto">
            <!-- Filter Type Select -->
            <div class="flex flex-col w-full">
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Periode Analisis</span>
                <div class="relative w-full">
                    <select wire:model.live="filterType" class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider focus:ring-4 focus:ring-primary-blue/10 appearance-none">
                        <option value="weekly">Mingguan</option>
                        <option value="monthly">Bulanan</option>
                        <option value="cumulative">Kumulatif (Semua)</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Conditional filters based on Filter Type -->
            @if($filterType !== 'cumulative')
                <div class="flex flex-col w-full">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Bulan</span>
                    <input type="month" wire:model.live="filterMonth" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-black text-xs text-gray-800 dark:text-white focus:ring-4 focus:ring-primary-blue/10">
                </div>
            @endif

            @if($filterType === 'weekly')
                <div class="flex flex-col w-full">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Pilih Minggu</span>
                    <div class="relative w-full">
                        <select wire:model.live="filterWeek" class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider focus:ring-4 focus:ring-primary-blue/10 appearance-none">
                            <option value="this_week">Minggu Ini (Berjalan)</option>
                            <option value="last_week">Minggu Lalu</option>
                            <option value="week_1">Minggu 1 (Tgl 1 - 7)</option>
                            <option value="week_2">Minggu 2 (Tgl 8 - 14)</option>
                            <option value="week_3">Minggu 3 (Tgl 15 - 21)</option>
                            <option value="week_4">Minggu 4 (Tgl 22 - 28)</option>
                            <option value="week_5">Minggu 5 (Tgl 29+)</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($startDate && $endDate)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40 rounded-2xl px-5 py-3.5 flex items-center gap-3 shrink-0 self-stretch xl:self-auto justify-between xl:justify-start">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary-blue text-white rounded-xl shrink-0">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    </div>
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-0.5">Rentang Tanggal</span>
                        <span class="text-xs font-black text-primary-blue dark:text-blue-400 uppercase">
                            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-12">
        <!-- Total Saldo Kas Card -->
        <div class="bg-gradient-to-br from-emerald-800 to-teal-950 rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-emerald-950/30 relative overflow-hidden group border-b-8 border-emerald-500">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-3 text-emerald-300">Total Saldo Kas</h3>
            <p class="text-2xl xl:text-3xl font-black text-white {{ ($currentModalBalance + $currentProfitBalance) < 0 ? 'text-primary-red' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($currentModalBalance + $currentProfitBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Saldo Modal Card -->
        <div class="bg-gray-900 rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-gray-900/20 relative overflow-hidden group border-b-8 border-primary-blue">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Saldo Kas Modal</h3>
            <p class="text-2xl xl:text-3xl font-black text-white {{ $currentModalBalance < 0 ? 'text-primary-red' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($currentModalBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Saldo Keuntungan Card -->
        <div class="bg-primary-blue rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-blue-900/20 relative overflow-hidden group border-b-8 border-white/20">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m16 12-4-4-4 4"/><path d="M12 16V8"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-3">Saldo Kas Keuntungan</h3>
            <p class="text-2xl xl:text-3xl font-black text-white {{ $currentProfitBalance < 0 ? 'text-red-300' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($currentProfitBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-gray-900 rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-gray-900/20 relative overflow-hidden group border-b-8 border-green-500">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-green-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-green-400 opacity-80 mb-3">Total Pemasukan</h3>
            <p class="text-2xl xl:text-3xl font-black text-green-400" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($displayIncome, 0, ',', '.') }}</p>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-gray-900 rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-gray-900/20 relative overflow-hidden group border-b-8 border-primary-red">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-red-400 opacity-80 mb-3">Total Pengeluaran</h3>
            <p class="text-2xl xl:text-3xl font-black text-primary-red" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($displayExpense, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Period Flow Summary Banner (Only if filter is active) -->
    @if($startDate)
    <div class="bg-gradient-to-br from-gray-900 via-slate-900 to-gray-950 rounded-2xl p-5 shadow-xl border border-white/5 text-white mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left mb-6 pb-6 border-b border-white/10">
            <div class="flex flex-col justify-center">
                <span class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1">Saldo Awal Periode</span>
                <p class="text-xl font-black text-gray-300">Rp{{ number_format($startingBalance, 0, ',', '.') }}</p>
            </div>
            <div class="flex flex-col justify-center border-y md:border-y-0 md:border-x border-white/10 px-4 text-center">
                <span class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1">Perubahan Arus Kas (Net Flow)</span>
                @php $netFlow = $displayIncome - $displayExpense; @endphp
                <p class="text-2xl font-black italic {{ $netFlow >= 0 ? 'text-green-400' : 'text-primary-red' }}">
                    {{ $netFlow >= 0 ? '+' : '' }}Rp{{ number_format($netFlow, 0, ',', '.') }}
                </p>
            </div>
            <div class="flex flex-col justify-center md:items-end">
                <span class="text-[9px] font-black uppercase tracking-widest opacity-65 mb-1 text-emerald-450">Saldo Akhir Periode</span>
                <p class="text-xl font-black text-white">Rp{{ number_format($startingBalance + $netFlow, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Audit Hasil Fisik Status -->
        @php $auditNet = $totalSurplus - $totalDeficit; @endphp
        <div x-data="{ open: false }"
             class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden">

            {{-- Header / Toggle --}}
            <button @click="open = !open"
                    class="w-full flex flex-col sm:flex-row items-center justify-between gap-4 p-4 text-left transition-colors hover:bg-white/5">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl {{ $auditNet < 0 ? 'bg-red-500/20 text-red-400' : ($auditNet > 0 ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400') }}">
                        @if($auditNet < 0)
                            {{-- Rugi: net negatif --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @elseif($auditNet > 0)
                            {{-- Lebih: net positif --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="16"/><line x1="8" x2="16" y1="12" y2="12"/></svg>
                        @else
                            {{-- Klop: net nol --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-widest opacity-60 block">Hasil Audit Penyesuaian Kas Fisik</span>
                        <span class="text-xs font-black uppercase tracking-wider">
                            @if($auditNet < 0)
                                PERINGATAN: TERDAPAT SELISIH KURANG (RUGI) FISIK BERSIH
                            @elseif($auditNet > 0)
                                INFO: TERDAPAT SELISIH LEBIH FISIK BERSIH
                            @else
                                SALDO FISIK & SISTEM SESUAI (AMAN)
                            @endif
                        </span>
                        {{-- Penjelasan jika ada campuran --}}
                        @if($totalDeficit > 0 && $totalSurplus > 0)
                            <span class="text-[8px] opacity-50 block mt-0.5">
                                Ada {{ $adjustments->where('type','expense')->count() }} entri rugi & {{ $adjustments->where('type','income')->count() }} entri lebih — dihitung dari net bersih
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        @if($auditNet < 0)
                            <span class="text-sm font-black text-red-400">Rp{{ number_format($auditNet, 0, ',', '.') }} (Rugi Bersih)</span>
                        @elseif($auditNet > 0)
                            <span class="text-sm font-black text-amber-400">+Rp{{ number_format($auditNet, 0, ',', '.') }} (Lebih Bersih)</span>
                        @else
                            <span class="text-xs font-black text-emerald-400 uppercase">Semua Penyesuaian Klop</span>
                        @endif
                    </div>
                    {{-- Chevron --}}
                    <svg class="w-4 h-4 opacity-50 transition-transform duration-200 shrink-0"
                         :class="open ? 'rotate-180' : ''"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            {{-- Accordion Body --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="border-t border-white/10 px-4 pb-4 pt-3">

                @if($adjustments->isEmpty())
                    <p class="text-[10px] font-black uppercase tracking-widest text-center opacity-40 py-3">
                        Tidak ada data penyesuaian pada periode ini
                    </p>
                @else
                    <div class="space-y-2">
                        {{-- Header kolom --}}
                        <div class="grid grid-cols-12 gap-2 text-[8px] font-black uppercase tracking-widest opacity-40 px-2 pb-1 border-b border-white/5">
                            <div class="col-span-2">Tanggal</div>
                            <div class="col-span-5">Deskripsi</div>
                            <div class="col-span-2 text-center">Tipe</div>
                            <div class="col-span-3 text-right">Jumlah</div>
                        </div>

                        @foreach($adjustments as $adj)
                            <div class="grid grid-cols-12 gap-2 items-center px-2 py-2 rounded-xl
                                {{ $adj->type === 'expense' ? 'bg-red-500/10' : 'bg-amber-500/10' }}">

                                {{-- Tanggal --}}
                                <div class="col-span-2 text-[9px] font-black opacity-60">
                                    {{ \Carbon\Carbon::parse($adj->date)->format('d/m/Y') }}
                                </div>

                                {{-- Deskripsi --}}
                                <div class="col-span-5 text-[9px] font-bold leading-tight">
                                    {{ $adj->description }}
                                </div>

                                {{-- Badge tipe --}}
                                <div class="col-span-2 flex justify-center">
                                    @if($adj->type === 'expense')
                                        <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-red-500/20 text-red-400">
                                            Rugi Selisih
                                        </span>
                                    @else
                                        <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400">
                                            Lebih
                                        </span>
                                    @endif
                                </div>

                                {{-- Jumlah --}}
                                <div class="col-span-3 text-right text-sm font-black
                                    {{ $adj->type === 'expense' ? 'text-red-400' : 'text-amber-400' }}">
                                    {{ $adj->type === 'expense' ? '-' : '+' }}Rp{{ number_format($adj->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach

                        {{-- Total row --}}
                        <div class="grid grid-cols-12 gap-2 items-center px-2 py-2 mt-1 border-t border-white/10">
                            <div class="col-span-9 text-[9px] font-black uppercase tracking-widest opacity-60">
                                Total Bersih
                                <span class="normal-case opacity-70 ml-1">(Lebih - Rugi = Net)</span>
                            </div>
                            <div class="col-span-3 text-right text-sm font-black
                                {{ $auditNet < 0 ? 'text-red-400' : ($auditNet > 0 ? 'text-amber-400' : 'text-emerald-400') }}">
                                {{ $auditNet >= 0 ? '+' : '' }}Rp{{ number_format($auditNet, 0, ',', '.') }}
                                <span class="text-[8px] block opacity-70">
                                    {{ $auditNet < 0 ? 'Rugi Bersih' : ($auditNet > 0 ? 'Lebih Bersih' : 'Klop') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Visual Cash Flow Charts (Responsive) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-12"
         x-data="{
            inflowData: @entangle('chartData.inflowValues'),
            inflowLabels: @entangle('chartData.inflowLabels'),
            outflowData: @entangle('chartData.outflowValues'),
            outflowLabels: @entangle('chartData.outflowLabels'),
            inflowChart: null,
            outflowChart: null,
            initCharts() {
                this.$nextTick(() => {
                    const inCanvas = document.getElementById('inflowChart');
                    if (inCanvas) {
                        if (this.inflowChart) this.inflowChart.destroy();
                        this.inflowChart = new Chart(inCanvas, {
                            type: 'doughnut',
                            data: {
                                labels: this.inflowLabels,
                                datasets: [{
                                    data: this.inflowData,
                                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6', '#f43f5e'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { color: '#9ca3af', font: { weight: 'bold', size: 10 } } }
                                }
                            }
                        });
                    }

                    const outCanvas = document.getElementById('outflowChart');
                    if (outCanvas) {
                        if (this.outflowChart) this.outflowChart.destroy();
                        this.outflowChart = new Chart(outCanvas, {
                            type: 'doughnut',
                            data: {
                                labels: this.outflowLabels,
                                datasets: [{
                                    data: this.outflowData,
                                    backgroundColor: ['#ef4444', '#f97316', '#eab308', '#ec4899', '#a855f7', '#64748b', '#06b6d4', '#d946ef'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { color: '#9ca3af', font: { weight: 'bold', size: 10 } } }
                                }
                            }
                        });
                    }
                });
            }
         }"
         x-init="initCharts(); $watch('inflowData', () => initCharts()); $watch('outflowData', () => initCharts());"
    >
        <!-- Inflow Chart Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-5 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col" wire:ignore>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 dark:text-white italic">Sumber Kas Masuk</h3>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Pemasukan Berdasarkan Kategori</p>
                </div>
                <span class="px-3 py-1 bg-green-500/10 text-green-500 rounded-full text-[9px] font-black uppercase tracking-widest">
                    Rp{{ number_format($displayIncome, 0, ',', '.') }}
                </span>
            </div>
            <div class="relative flex-1 min-h-[260px] flex items-center justify-center">
                <div x-show="inflowData.length === 0" class="absolute inset-0 flex items-center justify-center">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic text-center">Tidak ada pemasukan pada periode ini</p>
                </div>
                <div x-show="inflowData.length > 0" class="w-full max-w-[260px] mx-auto h-[260px]">
                    <canvas id="inflowChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Outflow Chart Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-5 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 flex flex-col" wire:ignore>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 dark:text-white italic">Tujuan Kas Keluar</h3>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Pengeluaran Berdasarkan Kategori</p>
                </div>
                <span class="px-3 py-1 bg-primary-red/10 text-primary-red rounded-full text-[9px] font-black uppercase tracking-widest">
                    Rp{{ number_format($displayExpense, 0, ',', '.') }}
                </span>
            </div>
            <div class="relative flex-1 min-h-[260px] flex items-center justify-center">
                <div x-show="outflowData.length === 0" class="absolute inset-0 flex items-center justify-center">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic text-center">Tidak ada pengeluaran pada periode ini</p>
                </div>
                <div x-show="outflowData.length > 0" class="w-full max-w-[260px] mx-auto h-[260px]">
                    <canvas id="outflowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Stats Cards -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white mb-6">Ringkasan Per Kategori</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($categoryStats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-24 h-24 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="flex justify-between items-start mb-4 gap-2 relative z-10">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-800 dark:text-white">{{ $stat['name'] }}</h3>
                    @if(!in_array($stat['name'], ['Modal Awal', 'Penjualan Toko / POS', 'Pembelian Stok Barang', 'Biaya Operasional', 'Gaji & Insentif Kasir', 'Lain-lain / Dana Darurat', 'Keuntungan Jurusan', 'Bagi Hasil Mingguan', 'Bagi Hasil Supplier', 'Bagi Hasil Pengelola', 'Bagi Hasil Labantik', 'Kas Doku', 'Konsolidasi Sub-Unit']))
                    <button wire:click="confirmDeleteCategory('{{ $stat['id'] }}', '{{ $stat['name'] }}')" class="text-red-400 hover:text-red-600 transition-colors hover:scale-110 transform" title="Hapus Kategori">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                    @endif
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Pemasukan</span>
                        <span class="font-black text-green-500" :class="censorMode ? 'privacy-blur' : ''">+Rp{{ number_format($stat['income'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Pengeluaran</span>
                        <span class="font-black text-primary-red" :class="censorMode ? 'privacy-blur' : ''">-Rp{{ number_format($stat['expense'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs pt-1 border-t border-dashed border-gray-100 dark:border-gray-700">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Modal</span>
                        <span class="font-black text-primary-blue" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stat['modal_balance'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Keuntungan</span>
                        <span class="font-black text-emerald-500" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stat['profit_balance'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 mt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Saldo</span>
                        <span class="text-lg font-black {{ $stat['balance'] >= 0 ? 'text-primary-blue' : 'text-primary-red' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stat['balance'], 0, ',', '.') }}</span>
                    </div>
                    @if($activeTab === 'cumulative' && $stat['name'] !== 'Bagi Hasil Mingguan')
                        <div class="mt-3 pt-2 border-t border-dashed border-gray-150 dark:border-gray-700 flex gap-2">
                            <button wire:click="openCategoryDetail('{{ $stat['id'] }}', '{{ $stat['name'] }}')"
                                class="flex-1 text-center py-2.5 bg-gray-50 hover:bg-gray-800 hover:text-white dark:bg-gray-700/50 dark:hover:bg-gray-600 dark:text-gray-300 dark:hover:text-white text-[9px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                Lihat Detail
                            </button>
                            <button wire:click="openAdjustModal('{{ $stat['id'] }}', '{{ $stat['name'] }}', {{ $stat['balance'] }})"
                                class="flex-1 text-center py-2.5 bg-gray-50 hover:bg-primary-blue hover:text-white dark:bg-gray-700/50 dark:hover:bg-primary-blue dark:text-gray-300 dark:hover:text-white text-[9px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                Sesuaikan Saldo
                            </button>
                        </div>
                    @else
                        <div class="mt-3 pt-2 border-t border-dashed border-gray-150 dark:border-gray-700">
                            <button wire:click="openCategoryDetail('{{ $stat['id'] }}', '{{ $stat['name'] }}')"
                                class="w-full text-center py-2.5 bg-gray-50 hover:bg-gray-800 hover:text-white dark:bg-gray-700/50 dark:hover:bg-gray-600 dark:text-gray-300 dark:hover:text-white text-[9px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                Lihat Detail
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-3xl p-5 text-center border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-black text-gray-400 uppercase tracking-widest italic">Belum ada kategori kas.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden mb-12">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Riwayat Transaksi Kas</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori Kas</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Nominal</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black text-gray-800 dark:text-white uppercase">{{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">{{ $tx->cashCategory->name ?? '-' }}</div>
                            <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $tx->cash_type === 'modal' ? 'bg-primary-blue/10 text-primary-blue' : 'bg-green-500/10 text-green-500' }}">
                                {{ $tx->cash_type === 'modal' ? 'Kas Modal' : 'Kas Keuntungan' }}
                            </span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white tracking-tight">{{ $tx->description }}</div>
                        </td>
                        <td class="px-10 py-8">
                            @if($tx->type === 'income')
                                <span class="px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest">Masuk</span>
                            @else
                                <span class="px-4 py-1.5 bg-red-100 text-red-700 rounded-full text-[9px] font-black uppercase tracking-widest">Keluar</span>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-right">
                            <span class="text-lg font-black italic {{ $tx->type === 'income' ? 'text-green-500' : 'text-primary-red' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right flex justify-end gap-2">
                            <button 
                                wire:click="editTransaction('{{ $tx->id }}')"
                                class="p-3 bg-white dark:bg-gray-700 text-primary-blue rounded-xl shadow-sm hover:scale-110 transition-transform border border-gray-100 dark:border-gray-600"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                            </button>
                            <button 
                                wire:click="confirmDelete('{{ $tx->id }}')"
                                class="p-3 bg-red-50 dark:bg-red-900/20 text-red-400 hover:text-red-600 rounded-xl hover:scale-110 transition-transform"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-32 text-center opacity-20">
                            <p class="text-xs font-black uppercase tracking-widest italic">Tidak ada catatan kas bulan ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
            {{ $transactions->links('livewire.partials.custom-pagination') }}
        </div>
    </div>

    <!-- Modal Form -->
    <div 
        x-data="{ show: @entangle('showModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[3rem] shadow-2xl flex flex-col max-h-[90vh] overflow-y-auto no-scrollbar animate-in zoom-in-95 duration-300"
        >
            <div class="p-10 bg-primary-blue text-white relative">
                <div class="absolute right-10 top-10">
                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <h3 class="text-2xl font-bold uppercase tracking-tight mb-1">{{ $editingId ? 'Edit Kas' : 'Catat Kas Baru' }}</h3>
            </div>

            <div class="p-10 space-y-6">
                <!-- Tipe (Jenis Transaksi) -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer relative">
                            <input type="radio" wire:model.live="type" value="income" class="peer sr-only">
                            <div class="text-center px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-xs tracking-widest peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-green-500 transition-all">
                                Pemasukan
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" wire:model.live="type" value="expense" class="peer sr-only">
                            <div class="text-center px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-xs tracking-widest peer-checked:border-primary-red peer-checked:bg-primary-red/10 peer-checked:text-primary-red transition-all">
                                Pengeluaran
                            </div>
                        </label>
                    </div>
                    @error('type') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori Kas -->
                <div x-data="{ isAddingNew: false }">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Kategori Kas</label>
                    <div x-show="!isAddingNew" class="relative">
                        <select wire:model.live="cashCategoryId" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-gray-800 dark:text-white appearance-none">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        
                        @if($cashCategoryId)
                        <div class="mt-3 ml-2 flex flex-wrap gap-x-4 gap-y-1 text-[11px] font-bold text-gray-500 dark:text-gray-400">
                            <div>Saldo Modal Kategori: <span class="text-primary-blue font-extrabold">Rp{{ number_format($selectedCategoryModalBalance, 0, ',', '.') }}</span></div>
                            <div>Saldo Keuntungan Kategori: <span class="text-emerald-500 font-extrabold">Rp{{ number_format($selectedCategoryProfitBalance, 0, ',', '.') }}</span></div>
                        </div>
                        @endif

                        <div class="mt-2 text-right">
                            <button type="button" @click="isAddingNew = true" class="text-[10px] font-black text-primary-blue uppercase tracking-widest hover:underline">+ Buat Kategori Baru</button>
                        </div>
                    </div>
                    <div x-show="isAddingNew" x-cloak class="flex items-center gap-2">
                        <input type="text" wire:model="newCategoryName" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-gray-800 dark:text-white" placeholder="Nama Kategori Baru">
                        <button type="button" wire:click="saveCategory" @click="isAddingNew = false" class="px-6 py-4 bg-green-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-transform shadow-xl shadow-green-500/20">Simpan</button>
                        <button type="button" @click="isAddingNew = false" class="px-4 py-4 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-2xl font-black text-xs uppercase hover:scale-105 transition-transform">Batal</button>
                    </div>
                    @error('cashCategoryId') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                    @error('newCategoryName') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Sumber/Tujuan Kas -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Sumber / Tujuan Kas</label>
                    <div class="grid {{ $type === 'expense' ? 'grid-cols-3' : 'grid-cols-2' }} gap-3">
                        <label class="cursor-pointer relative h-full">
                            <input type="radio" wire:model.live="cashType" value="modal" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center text-center p-3 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-[10px] tracking-widest peer-checked:border-primary-blue peer-checked:bg-primary-blue/10 peer-checked:text-primary-blue transition-all h-full">
                                <span>Kas Modal</span>
                                <span class="text-[8px] font-bold opacity-70 mt-1 normal-case tracking-normal">(Belanja Stok)</span>
                            </div>
                        </label>
                        <label class="cursor-pointer relative h-full">
                            <input type="radio" wire:model.live="cashType" value="keuntungan" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center text-center p-3 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-[10px] tracking-widest peer-checked:border-primary-blue peer-checked:bg-primary-blue/10 peer-checked:text-primary-blue transition-all h-full">
                                <span>Kas Untung</span>
                                <span class="text-[8px] font-bold opacity-70 mt-1 normal-case tracking-normal">(Laba Bersih)</span>
                            </div>
                        </label>
                        @if($type === 'expense')
                        <label class="cursor-pointer relative h-full">
                            <input type="radio" wire:model.live="cashType" value="keduanya" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center text-center p-3 rounded-2xl border-2 border-gray-100 dark:border-gray-800 text-gray-400 font-black uppercase text-[10px] tracking-widest peer-checked:border-primary-blue peer-checked:bg-primary-blue/10 peer-checked:text-primary-blue transition-all h-full">
                                <span>Keduanya</span>
                                <span class="text-[8px] font-bold opacity-70 mt-1 normal-case tracking-normal">(Modal & Untung)</span>
                            </div>
                        </label>
                        @endif
                    </div>
                    @error('cashType') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Tanggal</label>
                    <input type="date" wire:model="date" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-gray-800 dark:text-white">
                    @error('date') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                @if($cashType === 'keduanya')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Nominal Kas Modal (Rp)</label>
                        <input type="number" wire:model.live="amountModal" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-gray-800 dark:text-white" placeholder="0">
                        @error('amountModal') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Nominal Kas Untung (Rp)</label>
                        <input type="number" wire:model.live="amountProfit" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-gray-800 dark:text-white" placeholder="0">
                        @error('amountProfit') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Total Pengeluaran (Rp)</label>
                    <input type="text" readonly value="Rp{{ number_format((float)($amountModal ?: 0) + (float)($amountProfit ?: 0), 0, ',', '.') }}" class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-700/50 border-none rounded-2xl font-black text-2xl text-gray-500 dark:text-gray-300 cursor-not-allowed">
                    @error('amount') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>
                @else
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Nominal (Rp)</label>
                    <input type="number" wire:model="amount" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-2xl text-gray-800 dark:text-white" placeholder="0">
                    @error('amount') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>
                @endif

                <!-- Keterangan -->
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 mb-2">Keterangan / Deskripsi</label>
                    <input type="text" wire:model="description" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-gray-800 dark:text-white" placeholder="Contoh: Beli Galon & Kopi">
                    @error('description') <span class="text-primary-red text-xs mt-1 block ml-4">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="p-10 bg-gray-50 dark:bg-gray-800/50 flex justify-end">
                <button wire:click="saveTransaction" class="px-10 py-4 bg-primary-blue text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all">
                    {{ $editingId ? 'Simpan Perubahan' : 'Simpan Data Kas' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
        x-data="{ show: @entangle('showDeleteConfirmation') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-md rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300"
        >
            <div class="p-5 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 text-primary-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white mb-2">Hapus Catatan Kas</h3>
                <p class="text-sm font-bold text-gray-400">Apakah Anda yakin ingin menghapus catatan kas ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="p-5 bg-gray-50 dark:bg-gray-800/50 flex gap-4">
                <button @click="show = false" class="flex-1 py-4 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-transform">
                    Batal
                </button>
                <button wire:click="deleteTransaction" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 active:scale-95 transition-all">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Category Confirmation Modal -->
    <div 
        x-data="{ show: @entangle('showDeleteCategoryConfirmation') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-md rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300"
        >
            <div class="p-5 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 text-primary-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white mb-2">
                    {{ $confirmingDeleteCategoryTxCount > 0 ? 'Hapus Paksa Kategori' : 'Hapus Kategori Kas' }}
                </h3>
                
                @if($confirmingDeleteCategoryTxCount > 0)
                    <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/30 rounded-2xl p-5 mb-4 text-left">
                        <p class="text-xs font-black uppercase text-primary-red tracking-widest mb-1">Peringatan Penting</p>
                        <p class="text-xs font-bold text-red-650 dark:text-red-400">
                            Kategori "<span class="font-extrabold">{{ $confirmingDeleteCategoryName }}</span>" memiliki <span class="font-extrabold text-sm">{{ $confirmingDeleteCategoryTxCount }}</span> transaksi kas di dalamnya.
                        </p>
                        <p class="text-xs font-bold text-red-500/80 dark:text-red-400/80 mt-2 leading-relaxed">
                            Menghapus kategori ini akan **menghapus secara permanen seluruh {{ $confirmingDeleteCategoryTxCount }} transaksi** kas tersebut dari sistem keuangan.
                        </p>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Apakah Anda yakin ingin menghapus paksa kategori ini beserta seluruh transaksinya?</p>
                @else
                    <p class="text-sm font-bold text-gray-400">Apakah Anda yakin ingin menghapus kategori kas "<span class="font-extrabold text-gray-800 dark:text-white">{{ $confirmingDeleteCategoryName }}</span>"? Tindakan ini tidak dapat dibatalkan.</p>
                @endif
            </div>
            <div class="p-5 bg-gray-50 dark:bg-gray-800/50 flex gap-4">
                <button @click="show = false" class="flex-1 py-4 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-transform">
                    Batal
                </button>
                <button wire:click="deleteCategory" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 active:scale-95 transition-all">
                    {{ $confirmingDeleteCategoryTxCount > 0 ? 'Ya, Hapus Paksa' : 'Ya, Hapus' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Physical Cash Adjustment Modal -->
    <div 
        x-data="{ show: @entangle('showAdjustModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300 border-t-8 border-t-primary-blue"
        >
            <div class="p-10 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Sesuaikan Saldo Kas Fisik</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Kategori: {{ $adjustCategoryName }}</p>
            </div>
            
            <div class="p-10 space-y-6">
                <!-- System Balance -->
                <div class="bg-gray-50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Saldo Sistem Saat Ini</p>
                    <p class="text-xl font-black text-gray-800 dark:text-white">Rp{{ number_format($adjustSystemBalance, 0, ',', '.') }}</p>
                </div>

                <!-- Input Physical Cash -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Nominal Uang Kas Fisik Riil (Rp)</label>
                    <input type="number" wire:model="adjustPhysicalBalance" placeholder="Contoh: 165000"
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-gray-200 dark:border-gray-700 focus:border-primary-blue dark:focus:border-primary-blue rounded-2xl focus:ring-0 text-sm font-bold dark:text-white transition-colors">
                    @error('adjustPhysicalBalance') <span class="text-xs text-primary-red font-bold uppercase tracking-wide mt-2 block">{{ $message }}</span> @enderror
                </div>

                <!-- Cash Type Classification -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Klasifikasi Akun Selisih</label>
                    <select wire:model="adjustCashType"
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-gray-200 dark:border-gray-700 focus:border-primary-blue dark:focus:border-primary-blue rounded-2xl focus:ring-0 text-sm font-bold dark:text-white transition-colors">
                        <option value="keuntungan">KAS KEUNTUNGAN (Rekomendasi untuk selisih harian)</option>
                        <option value="modal">KAS MODAL (Gunakan jika modal fisik bertambah/berkurang)</option>
                    </select>
                    @error('adjustCashType') <span class="text-xs text-primary-red font-bold uppercase tracking-wide mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="p-10 bg-gray-50 dark:bg-gray-800/50 flex gap-4">
                <button @click="show = false" class="flex-1 py-4 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-transform">
                    Batal
                </button>
                <button wire:click="submitAdjustment" class="flex-1 py-4 bg-primary-blue text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all">
                    Simpan Penyesuaian
                </button>
            </div>
        </div>
    </div>

    <!-- Category Detail Modal -->
    <div
        x-data="{ show: @entangle('showCategoryDetailModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[300] flex items-end sm:items-center justify-center sm:p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full sm:max-w-xl rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden"
            style="max-height: 85vh;"
        >
            {{-- Header --}}
            <div class="flex items-start justify-between px-7 pt-7 pb-5 border-b border-gray-100 dark:border-gray-800 shrink-0">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Riwayat Transaksi Kas</p>
                    <h3 class="text-lg font-black uppercase tracking-tight text-gray-800 dark:text-white leading-tight">
                        {{ $categoryDetailName }}
                    </h3>
                </div>
                <button @click="show = false"
                    class="shrink-0 ml-4 mt-1 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-6 py-5">
                @if(empty($categoryDetailTransactions))
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-14 h-14 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Belum ada transaksi</p>
                        <p class="text-[10px] text-gray-400 mt-1">Kategori ini belum memiliki catatan kas apapun</p>
                    </div>
                @else
                    {{-- Summary --}}
                    @php
                        $detailIncome  = collect($categoryDetailTransactions)->where('type', 'income')->sum('amount');
                        $detailExpense = collect($categoryDetailTransactions)->where('type', 'expense')->sum('amount');
                        $detailNet     = $detailIncome - $detailExpense;
                    @endphp
                    <div class="grid grid-cols-3 gap-2 mb-5">
                        <div class="bg-green-500/10 rounded-2xl p-3 text-center">
                            <p class="text-[8px] font-black uppercase tracking-widest text-green-600 dark:text-green-400 mb-1">Masuk</p>
                            <p class="text-sm font-black text-green-600 dark:text-green-400 leading-tight">
                                +Rp{{ number_format($detailIncome, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-red-500/10 rounded-2xl p-3 text-center">
                            <p class="text-[8px] font-black uppercase tracking-widest text-red-500 mb-1">Keluar</p>
                            <p class="text-sm font-black text-red-500 leading-tight">
                                -Rp{{ number_format($detailExpense, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="{{ $detailNet >= 0 ? 'bg-primary-blue/10' : 'bg-red-500/10' }} rounded-2xl p-3 text-center">
                            <p class="text-[8px] font-black uppercase tracking-widest {{ $detailNet >= 0 ? 'text-primary-blue' : 'text-red-500' }} mb-1">Net</p>
                            <p class="text-sm font-black {{ $detailNet >= 0 ? 'text-primary-blue' : 'text-red-500' }} leading-tight">
                                {{ $detailNet >= 0 ? '+' : '' }}Rp{{ number_format($detailNet, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- List --}}
                    <div class="space-y-2">
                        @foreach($categoryDetailTransactions as $tx)
                            <div class="flex items-center gap-3 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50">
                                {{-- Icon --}}
                                <div class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center
                                    {{ $tx['type'] === 'income' ? 'bg-green-500/15 text-green-500' : 'bg-red-500/15 text-red-500' }}">
                                    @if($tx['type'] === 'income')
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                        </svg>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-black text-gray-800 dark:text-white leading-snug truncate">
                                        {{ $tx['description'] ?: '(Tanpa keterangan)' }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[9px] font-bold text-gray-400">{{ $tx['date'] }}</span>
                                        <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md
                                            {{ $tx['cash_type'] === 'modal' ? 'bg-primary-blue/10 text-primary-blue' : 'bg-emerald-500/10 text-emerald-500' }}">
                                            {{ $tx['cash_type'] === 'modal' ? 'Modal' : 'Untung' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Amount + Hapus --}}
                                <div class="shrink-0 flex items-center gap-2">
                                    <span class="text-sm font-black {{ $tx['type'] === 'income' ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $tx['type'] === 'income' ? '+' : '-' }}Rp{{ number_format($tx['amount'], 0, ',', '.') }}
                                    </span>
                                    <button
                                        wire:click="confirmDelete('{{ $tx['id'] }}')"
                                        @click="show = false"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                                        title="Hapus">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-800 shrink-0">
                <button @click="show = false"
                    class="w-full py-4 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Consolidation Modal -->
    <div 
        x-data="{ show: @entangle('showConsolidateModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="show = false"
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300 border-t-8 border-t-amber-500"
        >
            <div class="p-10 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Gabungkan Saldo ke Kas Induk</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Konsolidasi Keuangan Sub-Unit</p>
            </div>
            
            <div class="p-10 space-y-6">
                <!-- Info Message -->
                <div class="p-4 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-500/20 text-xs font-semibold">
                    Tindakan ini akan memotong saldo kas sub-unit saat ini dan menambahkannya ke Buku Kas Induk Jurusan sebagai pencatatan konsolidasi transfer masuk.
                </div>

                <!-- Choose Cash Type -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Tipe Saldo yang Dikirim</label>
                    <select wire:model="consolidateCashType"
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-gray-205 dark:border-gray-700 focus:border-amber-500 dark:focus:border-amber-500 rounded-2xl focus:ring-0 text-sm font-bold dark:text-white transition-colors">
                        <option value="keuntungan">KAS KEUNTUNGAN (Rekomendasi transfer laba)</option>
                        <option value="modal">KAS MODAL (Gunakan untuk pengembalian dana modal)</option>
                    </select>
                    @error('consolidateCashType') <span class="text-xs text-primary-red font-bold uppercase tracking-wide mt-2 block">{{ $message }}</span> @enderror
                </div>

                <!-- Input Amount -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Nominal Saldo yang Dikirim (Rp)</label>
                    <input type="number" wire:model="consolidateAmount" placeholder="Contoh: 250000"
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-gray-205 dark:border-gray-700 focus:border-amber-500 dark:focus:border-amber-500 rounded-2xl focus:ring-0 text-sm font-bold dark:text-white transition-colors">
                    @error('consolidateAmount') <span class="text-xs text-primary-red font-bold uppercase tracking-wide mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="p-10 bg-gray-50 dark:bg-gray-800/50 flex gap-4">
                <button @click="show = false" class="flex-1 py-4 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-transform">
                    Batal
                </button>
                <button wire:click="consolidateToParent" class="flex-1 py-4 bg-amber-500 text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all">
                    Gabungkan Saldo
                </button>
            </div>
        </div>
    </div>
</div>

