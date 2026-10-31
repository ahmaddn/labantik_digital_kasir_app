<div class="p-6">
    <x-hub-tabs hub="laporan" title="Bagi Hasil Supplier" subtitle="Rekap Penjualan & Pelunasan Barang Titipan Mitra Konsinyasi." />

    <!-- Actions -->
    <div class="flex justify-end mb-6">
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
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Qty Terjual</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Omzet</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Hak Supplier (Modal)</th>
                        <th class="px-6 py-4 text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider text-right">Profit Toko</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi Pelunasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($unsettledReports as $report)
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $report->supplier_name }}</div>
                            @if($report->last_settled_at)
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5">Terakhir: {{ \Carbon\Carbon::parse($report->last_settled_at)->translatedFormat('d M Y H:i') }}</div>
                            @else
                                <div class="text-[10px] text-gray-400 mt-0.5">Belum Pernah Dilunasi</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ number_format($report->total_qty, 0, ',', '.') }} pcs
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-900 dark:text-white font-mono whitespace-nowrap">
                            Rp{{ number_format($report->total_sales, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-blue-600 dark:text-blue-400 font-mono whitespace-nowrap">
                            Rp{{ number_format($report->total_supplier_share, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono whitespace-nowrap">
                            Rp{{ number_format($report->total_shop_profit, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('supplier-settlement.print', ['supplierId' => $report->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-2.5 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-lg transition-all border border-blue-200 dark:border-blue-800">
                                    Struk
                                </a>
                                <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                                    Bayar
                                </button>
                                <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }}, true)" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-all" title="Tandai lunas tanpa kas">
                                    Tandai
                                </button>
                                <button wire:click="settleAndShare('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-2.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg transition-all" title="WA & Lunas">
                                    WA
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-xs">
                            Tidak ada tagihan supplier aktif yang belum dilunasi pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Cards View (< md) -->
        <div class="block md:hidden p-3.5 space-y-2.5">
            @forelse($unsettledReports as $report)
            <div class="p-3 bg-white dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col gap-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase truncate">{{ $report->supplier_name }}</h4>
                        <div class="text-[10px] text-gray-400 mt-0.5">
                            {{ number_format($report->total_qty, 0, ',', '.') }} pcs terjual
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[9px] text-gray-400 block">Hak Supplier</span>
                        <span class="text-xs font-bold font-mono text-blue-600 dark:text-blue-400">Rp{{ number_format($report->total_supplier_share, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs py-1.5 px-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <div>
                        <span class="text-[9px] text-gray-400 block">Omzet</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 font-mono">Rp{{ number_format($report->total_sales, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] text-gray-400 block">Profit Toko</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">Rp{{ number_format($report->total_shop_profit, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-1.5 pt-1.5 border-t border-gray-50 dark:border-gray-700/50 flex-wrap">
                    <a href="{{ route('supplier-settlement.print', ['supplierId' => $report->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-2.5 py-1.5 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800">
                        Struk
                    </a>
                    <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }}, true)" class="px-2.5 py-1.5 bg-amber-500 text-white text-xs font-bold rounded-lg">
                        Tandai
                    </button>
                    <button wire:click="settleSupplier('{{ $report->supplier_id }}', '{{ addslashes($report->supplier_name) }}', {{ $report->total_supplier_share }})" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm">
                        Bayar Lunas
                    </button>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold">Tidak ada tagihan supplier aktif yang belum dilunasi pada periode ini</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- TAB 2: RIWAYAT PELUNASAN -->
    @if($activeTab === 'history')
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu Pelunasan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Total Dibayar</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($settlementHistory as $history)
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d M Y H:i') }}</div>
                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $history->reference }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $history->supplier_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                            Rp{{ number_format($history->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($history->supplier_id)
                                <a href="{{ route('supplier-settlement.print', ['supplierId' => $history->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-2.5 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-lg transition-all border border-blue-200 dark:border-blue-800">
                                    Struk
                                </a>
                                @endif
                                <button wire:click="unsettleSupplier('{{ $history->id }}')" 
                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan pelunasan ini?')"
                                    class="px-2.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white text-xs font-bold rounded-lg transition-all border border-rose-200 dark:border-rose-800">
                                    Batalkan
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-xs">
                            Belum ada riwayat pelunasan bagi hasil supplier pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Cards View (< md) -->
        <div class="block md:hidden p-3.5 space-y-2.5">
            @forelse($settlementHistory as $history)
            <div class="p-3 bg-white dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col gap-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase truncate">{{ $history->supplier_name }}</h4>
                        <span class="text-[10px] text-gray-400 font-mono">{{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d M Y H:i') }}</span>
                    </div>
                    <span class="text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400 shrink-0">
                        Rp{{ number_format($history->amount, 0, ',', '.') }}
                    </span>
                </div>
                @if($history->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->description }}</p>
                @endif
                <div class="flex items-center justify-end gap-1.5 pt-1.5 border-t border-gray-50 dark:border-gray-700/50">
                    @if($history->supplier_id)
                    <a href="{{ route('supplier-settlement.print', ['supplierId' => $history->supplier_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800">
                        Struk
                    </a>
                    @endif
                    <button wire:click="unsettleSupplier('{{ $history->id }}')" 
                        onclick="return confirm('Apakah Anda yakin ingin membatalkan pelunasan ini?')"
                        class="px-2.5 py-1 bg-rose-50 text-rose-600 text-xs font-bold rounded-lg border border-rose-200 dark:border-rose-800">
                        Batalkan
                    </button>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold">Belum ada riwayat pelunasan bagi hasil supplier pada periode ini</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-link', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</div>

