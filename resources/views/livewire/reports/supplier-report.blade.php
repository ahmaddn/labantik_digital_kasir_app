<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-6">
        <div>
            <h1 class="text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">Laporan Bagi Hasil Supplier</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Rekap Penjualan & Pelunasan Barang Titipan</p>
        </div>
        
        <div class="flex items-center gap-4">
            @if(($activeTab === 'unsettled' ? $unsettledReports->count() : $settlementHistory->count()) > 0)
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="bg-primary-red text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 transition-all flex items-center">
                <flux:icon.arrow-down-tray class="w-4 h-4 mr-3" />
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

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-3 mb-6">
        <button wire:click="setTab('unsettled')" 
            class="px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider transition-all flex items-center gap-2.5 shadow-sm {{ $activeTab === 'unsettled' ? 'bg-primary-blue text-white shadow-blue-500/20' : 'bg-white dark:bg-gray-800 text-gray-500 hover:text-gray-800 dark:hover:text-white' }}">
            <flux:icon.clock class="w-4 h-4" />
            <span>Tagihan Aktif (Belum Dilunasi)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'unsettled' ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                {{ $unsettledReports->count() }}
            </span>
        </button>

        <button wire:click="setTab('history')" 
            class="px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider transition-all flex items-center gap-2.5 shadow-sm {{ $activeTab === 'history' ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-white dark:bg-gray-800 text-gray-500 hover:text-gray-800 dark:hover:text-white' }}">
            <flux:icon.check-circle class="w-4 h-4" />
            <span>Riwayat Pelunasan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'history' ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                {{ $settlementHistory->count() }}
            </span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Filter Supplier</label>
                <select wire:model.live="supplierId" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                @if($activeTab === 'unsettled')
                <div class="w-full p-3.5 bg-primary-blue/5 rounded-xl border border-primary-blue/10 flex items-center justify-between">
                    <span class="text-[9px] font-black text-primary-blue uppercase tracking-widest">Total Belum Dibayar</span>
                    <span class="text-base font-black text-primary-blue">Rp{{ number_format($unsettledReports->sum('total_supplier_share'), 0, ',', '.') }}</span>
                </div>
                @else
                <div class="w-full p-3.5 bg-emerald-500/5 rounded-xl border border-emerald-500/10 flex items-center justify-between">
                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Total Sudah Dilunasi</span>
                    <span class="text-base font-black text-emerald-600 dark:text-emerald-400">Rp{{ number_format($settlementHistory->sum('amount'), 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB 1: TAGIHAN AK TIF / BELUM DILUNASI -->
    @if($activeTab === 'unsettled')
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Qty Terjual</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Omzet</th>
                        <th class="px-8 py-5 text-[10px] font-black text-primary-blue uppercase tracking-widest">Hak Supplier (Modal)</th>
                        <th class="px-8 py-5 text-[10px] font-black text-emerald-500 uppercase tracking-widest text-right">Profit Toko</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi Pelunasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($unsettledReports as $report)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ $report->supplier_name }}</div>
                            @if($report->last_settled_at)
                                <div class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest mt-1">Terakhir Lunas: {{ \Carbon\Carbon::parse($report->last_settled_at)->translatedFormat('d M Y H:i') }}</div>
                            @else
                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Belum Pernah Dilunasi</div>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center font-black text-gray-600 dark:text-gray-400">
                            {{ number_format($report->total_qty, 0, ',', '.') }} pcs
                        </td>
                        <td class="px-8 py-6 font-black text-gray-800 dark:text-white">
                            Rp{{ number_format($report->total_sales, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 font-black text-primary-blue italic text-base">
                            Rp{{ number_format($report->total_supplier_share, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-right font-black text-emerald-500 italic">
                            Rp{{ number_format($report->total_shop_profit, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-center flex items-center justify-center gap-2">
                            <a href="{{ route('supplier-settlement.print', ['supplierId' => $report->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-3 py-2 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1 shadow-sm">
                                Struk
                            </a>
                            
                            <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1 shadow-md shadow-emerald-500/10">
                                Bayar Lunas
                            </button>
                            <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }}, true)" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1 shadow-md shadow-amber-500/10" title="Tandai lunas tanpa memotong kas">
                                Tandai Lunas
                            </button>
                            <button wire:click="settleAndShare('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-3.5 py-2 bg-green-500 hover:bg-green-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1 shadow-md shadow-green-500/10" title="Bayar & Kirim Rincian ke WA">
                                WA & Lunas
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center opacity-40">
                            <flux:icon.check-circle class="w-12 h-12 mx-auto mb-3 text-emerald-500" />
                            <p class="text-xs font-black uppercase tracking-widest italic">Tidak ada tagihan supplier aktif yang belum dilunasi pada periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($unsettledReports->count() > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    <tr>
                        <td class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">TOTAL TAGIHAN AKTIF</td>
                        <td class="px-8 py-5 text-center font-black text-gray-800 dark:text-white">{{ number_format($unsettledReports->sum('total_qty'), 0, ',', '.') }} pcs</td>
                        <td class="px-8 py-5 font-black text-gray-800 dark:text-white">Rp{{ number_format($unsettledReports->sum('total_sales'), 0, ',', '.') }}</td>
                        <td class="px-8 py-5 font-black text-primary-blue text-lg">Rp{{ number_format($unsettledReports->sum('total_supplier_share'), 0, ',', '.') }}</td>
                        <td class="px-8 py-5 text-right font-black text-emerald-500 text-lg">Rp{{ number_format($unsettledReports->sum('total_shop_profit'), 0, ',', '.') }}</td>
                        <td class="px-8 py-5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 2: RIWAYAT PELUNASAN -->
    @if($activeTab === 'history')
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Pelunasan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-emerald-500 uppercase tracking-widest">Total Dibayar</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($settlementHistory as $history)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d M Y H:i:s') }}</div>
                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $history->reference }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ $history->supplier_name }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-tight">{{ $history->description }}</div>
                        </td>
                        <td class="px-8 py-6 font-black text-emerald-600 dark:text-emerald-400 text-base italic">
                            Rp{{ number_format($history->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-center flex items-center justify-center gap-2">
                            @if($history->supplier_id)
                            <a href="{{ route('supplier-settlement.print', ['supplierId' => $history->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-3 py-2 bg-primary-blue/10 hover:bg-primary-blue text-primary-blue hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1 shadow-sm">
                                Struk
                            </a>
                            @endif

                            <button wire:click="unsettleSupplier('{{ $history->id }}')" 
                                onclick="return confirm('Apakah Anda yakin ingin membatalkan pelunasan ini? Transaksi bagi hasil akan dikembalikan ke status Belum Dilunasi.')"
                                class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all inline-flex items-center gap-1 shadow-sm"
                                title="Batalkan pelunasan ini & kembalikan transaksi ke Tagihan Aktif">
                                Batalkan Pelunasan
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center opacity-40">
                            <p class="text-xs font-black uppercase tracking-widest italic">Belum ada riwayat pelunasan bagi hasil supplier pada periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($settlementHistory->count() > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    <tr>
                        <td colspan="3" class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">TOTAL RIWAYAT PELUNASAN</td>
                        <td class="px-8 py-5 font-black text-emerald-600 dark:text-emerald-400 text-lg">Rp{{ number_format($settlementHistory->sum('amount'), 0, ',', '.') }}</td>
                        <td class="px-8 py-5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-link', (event) => {
            window.open(event.url, '_blank');
        });
    });
</script>
