<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">Laporan Bagi Hasil Supplier</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Rekap Penjualan Barang Titipan</p>
        </div>
        
        <div class="flex items-center gap-4">
            @if($reports->count() > 0)
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="bg-primary-red text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span wire:loading.remove wire:target="exportExcel">Export XLSX</span>
                <span wire:loading wire:target="exportExcel">Mengekspor...</span>
            </button>
            @endif

            @php
                $isSessionFinished = \App\Models\DailyRecap::whereDate('date', now())->where('actual_cash', '>', 0)->exists();
            @endphp
            <a href="{{ $isSessionFinished ? route('dashboard') : route('kasir') }}" class="bg-gray-800 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-gray-900/20 hover:scale-105 transition-all">
                Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-2">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-6 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-2">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-6 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-2">Supplier</label>
                <select wire:model.live="supplierId" class="w-full px-6 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="w-full p-4 bg-primary-blue/5 rounded-xl border border-primary-blue/10 flex items-center justify-between">
                    <span class="text-[9px] font-black text-primary-blue uppercase tracking-widest">Total Bayar Supplier</span>
                    <span class="text-lg font-black text-primary-blue italic">Rp{{ number_format($reports->sum('total_supplier_share'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Total Item</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Omzet</th>
                        <th class="px-10 py-6 text-[10px] font-black text-primary-blue uppercase tracking-widest">Hak Supplier (Modal)</th>
                        <th class="px-10 py-6 text-[10px] font-black text-green-500 uppercase tracking-widest text-right">Profit Toko</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ $report->supplier_name }}</div>
                            @if($report->last_settled_date)
                                <div class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Terakhir Lunas: {{ \Carbon\Carbon::parse($report->last_settled_date)->translatedFormat('d M Y') }}</div>
                            @else
                                <div class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Belum Pernah Dilunasi</div>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-center font-black text-gray-600 dark:text-gray-400">
                            {{ number_format($report->total_qty, 0, ',', '.') }}
                        </td>
                        <td class="px-10 py-8 font-black text-gray-800 dark:text-white">
                            Rp{{ number_format($report->total_sales, 0, ',', '.') }}
                        </td>
                        <td class="px-10 py-8 font-black text-primary-blue italic">
                            Rp{{ number_format($report->total_supplier_share, 0, ',', '.') }}
                        </td>
                        <td class="px-10 py-8 text-right font-black text-green-500 italic">
                            Rp{{ number_format($report->total_shop_profit, 0, ',', '.') }}
                        </td>
                        <td class="px-10 py-8 text-center flex items-center justify-center gap-2">
                            @if($report->supplier_id)
                                <a href="{{ route('supplier-settlement.print', ['supplierId' => $report->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-4 py-2 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    Struk
                                </a>
                                
                                @if($report->is_settled)
                                    <span class="px-4 py-2 bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-widest rounded-xl inline-flex items-center gap-1.5 border border-green-500/20">
                                        ✅ Lunas
                                    </span>
                                @else
                                    <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1.5 shadow-md shadow-emerald-500/10">
                                        Bayar Lunas
                                    </button>
                                    <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }}, true)" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1.5 shadow-md shadow-amber-500/10" title="Tandai lunas tanpa memotong kas (tidak mencatat pengeluaran)">
                                        Tandai Lunas
                                    </button>
                                    <button wire:click="settleAndShare('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1.5 shadow-md shadow-green-500/10" title="Bayar & Kirim Rincian ke WhatsApp">
                                        WA & Lunas
                                    </button>
                                @endif
                            @else
                                <span class="text-[9px] px-2.5 py-1 bg-gray-100 dark:bg-gray-800 rounded font-bold text-gray-400 uppercase">Toko</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-32 text-center opacity-20">
                            <p class="text-xs font-black uppercase tracking-widest italic">Tidak ada data transaksi supplier pada periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    <tr>
                        <td class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">TOTAL</td>
                        <td class="px-10 py-6 text-center font-black text-gray-800 dark:text-white">{{ number_format($reports->sum('total_qty'), 0, ',', '.') }}</td>
                        <td class="px-10 py-6 font-black text-gray-800 dark:text-white">Rp{{ number_format($reports->sum('total_sales'), 0, ',', '.') }}</td>
                        <td class="px-10 py-6 font-black text-primary-blue italic text-lg">Rp{{ number_format($reports->sum('total_supplier_share'), 0, ',', '.') }}</td>
                        <td class="px-10 py-6 text-right font-black text-green-500 italic text-lg">Rp{{ number_format($reports->sum('total_shop_profit'), 0, ',', '.') }}</td>
                        <td class="px-10 py-6"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-link', (event) => {
            window.open(event.url, '_blank');
        });
    });
</script>
