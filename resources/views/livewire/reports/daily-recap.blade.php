<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Rekap Harian</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Pembukuan Transaksi Digital</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div x-data="{ value: @entangle('selectedDate').live, instance: null }" 
                 x-init="instance = flatpickr($refs.input, { dateFormat: 'Y-m-d', defaultDate: value, onChange: (s, d) => value = d }); $watch('value', v => instance.setDate(v, false))"
                 class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-primary-blue mr-3 cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <input x-ref="input" type="text" readonly class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white cursor-pointer w-28">
            </div>

            <a href="{{ route('inventory-report', ['date' => $selectedDate]) }}" class="px-8 py-4 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                Audit Stok
            </a>

            @if($recap)
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="px-8 py-4 bg-primary-red text-white rounded-2xl shadow-xl shadow-red-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg wire:loading.remove wire:target="exportExcel" class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <svg wire:loading wire:target="exportExcel" class="animate-spin w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Export Excel
            </button>
            @endif

        </div>
    </div>

    @if($recap)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Revenue Card -->
        <div class="bg-primary-blue rounded-[3rem] p-10 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Total Omzet Tunai</h3>
            <p class="text-4xl font-black italic text-white" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_real, 0, ',', '.') }}</p>
            <div class="mt-8 pt-8 border-t border-white/10 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-[9px] font-bold opacity-50 uppercase tracking-widest">Murni Jurusan:</span>
                    <span class="text-xs font-black" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_internal_revenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[9px] font-bold opacity-50 uppercase tracking-widest">Gross Total:</span>
                    <span class="text-xs font-black" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_all, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Keuntungan Bersih</h3>
            <p class="text-4xl font-black italic text-primary-red" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_profit, 0, ',', '.') }}</p>
            <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Modal Pokok:</span>
                <span class="text-xs font-black text-gray-800 dark:text-white" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_modal, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6">Status Pembayaran</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Lunas</span>
                    </div>
                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $recap->count_received }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-primary-blue mr-3"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Pending</span>
                    </div>
                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $recap->count_unpaid_change }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-primary-red mr-3"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Hutang</span>
                    </div>
                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $recap->count_no_payment }}</span>
                </div>
            </div>
        </div>

        <!-- Date Info Card -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 flex flex-col justify-center text-center">
            <p class="text-[10px] font-black text-primary-blue uppercase tracking-widest mb-1">{{ $recap->month_name }}</p>
            <p class="text-3xl font-black italic text-gray-800 dark:text-white">Minggu ke-{{ $recap->month_week }}</p>
            <div class="mt-6 flex items-center justify-center text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                <svg class="w-3 h-3 mr-2 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Sync: {{ $recap->generated_at->format('H:i:s') }}
            </div>
        </div>
    </div>

    <!-- Cash Reconciliation (Audit Uang Kas) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">Audit Uang Kas</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Bandingkan Uang Fisik vs Sistem</p>
                </div>
                <div class="p-4 bg-primary-blue/5 rounded-2xl">
                    <svg class="w-6 h-6 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Uang Fisik di Laci (Cash on Hand)</label>
                    <div class="relative group">
                        <span class="absolute left-6 inset-y-0 flex items-center text-sm font-black text-gray-400 group-focus-within:text-primary-blue transition-colors">Rp</span>
                        <input 
                            type="number" 
                            wire:model.live="actualCash"
                            class="w-full pl-14 pr-8 py-5 bg-gray-50 dark:bg-gray-900 border-none rounded-[1.5rem] focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-gray-800 dark:text-white shadow-inner transition-all"
                            placeholder="Total uang laci..."
                        >
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-primary-blue dark:text-primary-blue-light uppercase tracking-widest ml-4">Kembalian Ditahan (Esok Hari)</label>
                    <div class="relative group">
                        <span class="absolute left-6 inset-y-0 flex items-center text-sm font-black text-gray-400 group-focus-within:text-primary-blue transition-colors">Rp</span>
                        <input 
                            type="number" 
                            wire:model.live="retainedChangeCash"
                            class="w-full pl-14 pr-8 py-5 bg-gray-50 dark:bg-gray-900 border-none rounded-[1.5rem] focus:ring-4 focus:ring-primary-blue/10 font-black text-lg text-gray-800 dark:text-white shadow-inner transition-all"
                            placeholder="Uang kembalian ditahan..."
                        >
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Catatan Audit</label>
                    <input 
                        type="text" 
                        wire:model.live="cashNote"
                        class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-900 border-none rounded-[1.5rem] focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white shadow-inner transition-all-none h-[68px]"
                        placeholder="Contoh: Selisih karena parkir..."
                    >
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-700 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex items-center gap-8">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status Audit</span>
                        @php
                            $diff = ((float)$actualCash - (float)$startingChangeCash) - (float)$recap->total_revenue_real;
                        @endphp
                        @if($actualCash == 0 && !$cashNote)
                            <span class="text-xs font-black text-gray-300 uppercase tracking-tighter">BELUM DIAUDIT</span>
                        @elseif($diff == 0)
                            <span class="text-xs font-black text-green-500 uppercase tracking-tighter flex items-center">
                                <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                MATCH / COCOK
                            </span>
                        @else
                            <span class="text-xs font-black {{ $diff < 0 ? 'text-primary-red' : 'text-amber-500' }} uppercase tracking-tighter flex items-center">
                                <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12" y1="17" y2="17"/></svg>
                                ADA SELISIH
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status Buku Kas</span>
                        @if($isPosted)
                            <span class="text-xs font-black text-green-500 uppercase tracking-tighter flex flex-col items-start">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                                    SUDAH DIPOSTING
                                </span>
                                @if($dailyRecapModel && $dailyRecapModel->postedBy)
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider pl-4 mt-0.5">
                                        Oleh: {{ $dailyRecapModel->postedBy->name }}
                                    </span>
                                @endif
                            </span>
                        @else
                            <span class="text-xs font-black text-gray-400 uppercase tracking-tighter flex items-center">
                                <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                                BELUM DIPOSTING
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <button 
                        wire:click="saveCashAudit"
                        class="px-6 py-4 bg-gray-900 dark:bg-slate-800 text-white rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all font-black italic uppercase text-xs tracking-widest"
                    >
                        Simpan Hasil Audit
                    </button>
                    @if($isPosted)
                        <div class="px-5 py-4 bg-green-600/15 border border-green-500/30 text-green-400 rounded-2xl flex items-center gap-2 font-black italic uppercase text-xs tracking-widest">
                            <svg class="w-4 h-4 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Sudah Diposting
                        </div>
                        <button 
                            wire:click="postToCashBook"
                            class="px-6 py-4 bg-amber-500 hover:bg-amber-600 text-black rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all font-black italic uppercase text-xs tracking-widest flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" /></svg>
                            Posting Ulang
                        </button>
                    @else
                        <button 
                            wire:click="postToCashBook"
                            class="px-6 py-4 bg-primary-blue text-white rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all font-black italic uppercase text-xs tracking-widest"
                        >
                            Posting ke Buku Kas
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-gray-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-gray-900/20 relative overflow-hidden flex flex-col justify-center border-t-8 border-primary-blue">
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-40 mb-2">Selisih Uang Kas</h3>
            @php
                $diff = ((float)$actualCash - (float)$retainedChangeCash) - (float)$recap->total_revenue_real;
            @endphp
            <p class="text-5xl font-black italic {{ $diff < 0 ? 'text-primary-red' : ($diff > 0 ? 'text-green-400' : 'text-white') }} tracking-tighter" :class="censorMode ? 'privacy-blur' : ''">
                {{ $diff > 0 ? '+' : '' }}Rp{{ number_format($diff, 0, ',', '.') }}
            </p>
            <p class="text-[9px] font-black uppercase tracking-widest mt-4 opacity-40 leading-relaxed">
                *Selisih = (Uang Fisik - Kembalian Ditahan) - Total Omzet Sistem.
            </p>
            
            <div class="mt-8 flex flex-col gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest opacity-60">Modal Awal Kembalian (Laci)</p>
                        <p class="text-xs font-black" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format((float)$startingChangeCash, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest opacity-60">Setoran Bersih (Fisik - Ditahan)</p>
                        <p class="text-xs font-black" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format((float)$actualCash - (float)$retainedChangeCash, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest opacity-60">Sistem (Expected)</p>
                        <p class="text-xs font-black italic" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($recap->total_revenue_real, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance Section -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 mb-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">Performa Per Kategori</h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Rekap Modal & Keuntungan Berdasarkan Jenis Produk Hari Ini</p>
            </div>
            <div class="p-4 bg-primary-red/5 rounded-2xl text-primary-red">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-gray-700">
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Volume</th>
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Total Modal (HPP)</th>
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Keuntungan</th>
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($categoryRecap as $stats)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all">
                        <td class="py-8">
                            <div class="flex items-center gap-3">
                                <span class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $stats->name }}</span>
                                <a href="{{ route('category-detail', ['categoryId' => $stats->id, 'type' => 'daily', 'date' => $selectedDate]) }}" wire:navigate class="px-3 py-1.5 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white text-[9px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-3 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Detail
                                </a>
                            </div>
                        </td>
                        <td class="py-8 text-center">
                            <span class="text-sm font-black text-gray-800 dark:text-white">{{ $stats->qty }} <span class="text-[9px] text-gray-400 uppercase ml-1">Unit</span></span>
                        </td>
                        <td class="py-8 text-right">
                            <span class="text-sm font-bold text-gray-400 italic">Rp{{ number_format($stats->modal, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-8 text-right">
                            <span class="text-lg font-black text-primary-red italic tracking-tighter">Rp{{ number_format($stats->profit, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-8 text-right">
                            <span class="text-lg font-black text-primary-blue italic tracking-tighter">Rp{{ number_format($stats->revenue, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Detail Transaksi Harian</h2>
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <!-- Search -->
                <div class="relative group w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Cari transaksi..." class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300 shadow-inner">
                </div>

                <!-- Filter Status -->
                <div class="relative group w-full md:w-48">
                    <select wire:model.live="filterStatus" class="w-full pl-6 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest appearance-none shadow-inner">
                        <option value="">Semua Status</option>
                        <option value="uang_diterima">Uang Diterima</option>
                        <option value="belum_kembalian">Belum Kembalian</option>
                        <option value="belum_menerima_uang">Belum Bayar</option>
                    </select>
                </div>

                <!-- Filter Kategori -->
                <div class="relative group w-full md:w-48">
                    <select wire:model.live="filterCategory" class="w-full pl-6 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest appearance-none shadow-inner">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jam</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Ref</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pembeli</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Items</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Bayar</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-10 py-8">
                            <span class="text-xs font-black text-gray-400 uppercase">{{ \Carbon\Carbon::parse($tx->transacted_at)->format('H:i') }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-primary-blue uppercase tracking-tight">{{ $tx->reference }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $tx->buyer_name ?? 'GUEST' }}</div>
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="px-4 py-2 bg-gray-100 dark:bg-gray-900 rounded-xl text-xs font-black text-gray-600 dark:text-gray-400">
                                {{ $tx->total_qty }} <span class="text-[9px] uppercase ml-1 opacity-50">Unit</span>
                            </span>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-lg font-black text-primary-red italic">Rp{{ number_format($tx->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <span class="text-[9px] font-black uppercase px-4 py-1.5 rounded-full {{ $tx->status === 'uang_diterima' ? 'bg-green-100 text-green-700' : 'bg-primary-red/10 text-primary-red' }}">
                                {{ str_replace('_', ' ', $tx->status) }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <button wire:click="viewDetails('{{ $tx->reference }}')" class="p-3 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-primary-blue rounded-xl transition-all">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-10 py-32 text-center opacity-20">
                            <p class="text-xs font-black uppercase tracking-widest italic">Tidak ada transaksi ditemukan</p>
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

    <!-- Transaction Detail Modal -->
    <div 
        x-data="{ show: @entangle('showDetailsModal') }" 
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
            class="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300"
        >
            <div class="p-10 bg-primary-blue text-white relative">
                <div class="absolute right-10 top-10">
                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <h3 class="text-3xl font-black italic uppercase tracking-tighter mb-1">Detail Transaksi</h3>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] opacity-60">Reference: {{ $detailReference }}</p>
            </div>

            <div class="p-10 max-h-[60vh] overflow-y-auto no-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Item</th>
                            <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Qty</th>
                            <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Harga</th>
                            <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($this->detailItems as $item)
                        <tr>
                            <td class="py-6">
                                <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $item->product->name }}</div>
                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $item->product->category->name ?? 'Uncategorized' }}</div>
                            </td>
                            <td class="py-6 text-center">
                                <span class="text-sm font-black text-gray-800 dark:text-white">{{ $item->quantity }}</span>
                            </td>
                            <td class="py-6 text-right text-xs font-bold text-gray-400 italic">
                                Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="py-6 text-right text-sm font-black text-primary-red italic">
                                Rp{{ number_format($item->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-10 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Pembayaran</p>
                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase {{ $this->detailItems->first()->status ?? '' === 'uang_diterima' ? 'bg-green-100 text-green-700' : 'bg-primary-red/10 text-primary-red' }}">
                        {{ str_replace('_', ' ', $this->detailItems->first()->status ?? 'Unknown') }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Transaksi</p>
                    <p class="text-4xl font-black text-primary-blue italic tracking-tighter">Rp{{ number_format($this->detailItems->sum('total_price'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-[4rem] p-32 border border-gray-100 dark:border-gray-700 text-center flex flex-col items-center shadow-xl shadow-blue-900/5">
        <div class="w-32 h-32 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center mb-10 text-gray-200 dark:text-gray-700 shadow-inner">
            <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
        </div>
        <h3 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Belum Ada Catatan</h3>
        <p class="text-gray-400 font-bold text-sm mt-4 uppercase tracking-[0.3em] italic">Tidak ada aktivitas transaksi pada {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
    </div>
    @endif

    <!-- Import Options -->
    <div class="fixed bottom-10 right-10 z-[100]" x-data="{ open: false }">
        <button @click="open = !open" class="px-10 py-5 bg-green-500 text-white rounded-[2rem] shadow-2xl shadow-green-500/40 font-black italic uppercase text-sm tracking-[0.2em] transform hover:-translate-y-2 hover:scale-105 transition-all flex items-center gap-4 group">
            <svg class="w-6 h-6 group-hover:-translate-y-1 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>Import Data</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute bottom-full right-0 mb-6 w-80 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800 p-6 flex flex-col gap-4">
            <div class="text-center">
                <h4 class="text-lg font-black uppercase italic tracking-tight text-gray-800 dark:text-white">Import Excel</h4>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Upload file dari device lain</p>
            </div>
            
            <div class="relative group mt-2">
                <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="block w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-primary-blue/10 file:text-primary-blue hover:file:bg-primary-blue/20 transition-all cursor-pointer">
                <div wire:loading wire:target="importFile" class="text-xs text-primary-blue mt-2 font-bold italic">Uploading...</div>
                @error('importFile') <span class="text-[10px] text-primary-red font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <label class="flex items-start gap-3 mt-2 cursor-pointer group">
                <div class="relative flex items-center justify-center">
                    <input type="checkbox" wire:model="reopenSession" class="peer sr-only">
                    <div class="w-5 h-5 bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded transition-all peer-checked:bg-primary-blue peer-checked:border-primary-blue"></div>
                    <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest mt-0.5 group-hover:text-primary-blue transition-colors">Buka Kembali Sesi Kasir</span>
                    <span class="block text-[9px] font-bold text-gray-400 mt-1 leading-relaxed">Centang ini jika Anda ingin melanjutkan transaksi setelah import selesai.</span>
                </div>
            </label>

            <button wire:click="importExcel" wire:loading.attr="disabled" @click="open = false" class="w-full mt-2 py-4 bg-primary-blue text-white rounded-2xl font-black italic uppercase text-xs tracking-widest hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                <svg wire:loading.remove wire:target="importExcel" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <svg wire:loading wire:target="importExcel" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Proses Import
            </button>
        </div>
    </div>

</div>
