<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Laporan Bagi Hasil Supplier</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Rekap Penjualan Barang Titipan</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-10">
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ $report->supplier->name ?? 'Unknown' }}</div>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-32 text-center opacity-20">
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
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
