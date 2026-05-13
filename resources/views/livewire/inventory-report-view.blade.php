<div class="p-4 sm:p-8 bg-[#f8fafc] dark:bg-gray-950 min-h-screen font-outfit">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                    Laporan <span class="text-primary-blue">Selisih Stok</span>
                </h1>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-2">Inventory Audit & Discrepancy Analysis</p>
            </div>

            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-green-500 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-green-500/20 animate-in fade-in slide-in-from-top-4 duration-500">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="date" wire:model.live="selectedDate" class="bg-white dark:bg-gray-900 border-none rounded-2xl px-6 py-3 text-sm font-black text-gray-700 dark:text-gray-200 shadow-xl shadow-blue-900/5 focus:ring-4 focus:ring-primary-blue/10">
                </div>
                
                <button onclick="exportInventoryExcel('Laporan_Stok_{{ $selectedDate }}')" class="bg-primary-red text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 transition-all flex items-center">
                    <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Export XLSX
                </button>

                <a href="{{ route('kasir') }}" class="bg-gray-800 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-gray-900/20 hover:scale-105 transition-all">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            
            <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border-t-4 border-primary-blue">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Total Unit Terjual</span>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black italic text-gray-800 dark:text-white leading-none">{{ $totalSold }}</span>
                    <span class="text-xs font-black text-primary-blue uppercase tracking-tighter mb-1">Items</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border-t-4 border-primary-red">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Total Selisih Fisik</span>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black italic {{ $totalDiscrepancy < 0 ? 'text-primary-red' : ($totalDiscrepancy > 0 ? 'text-green-500' : 'text-gray-800 dark:text-white') }} leading-none">
                        {{ $totalDiscrepancy > 0 ? '+' : '' }}{{ $totalDiscrepancy }}
                    </span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-tighter mb-1">Items</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border-t-4 border-amber-500">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Produk Bermasalah</span>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black italic text-amber-500 leading-none">{{ $itemsWithIssue }}</span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-tighter mb-1">SKU</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-10">
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 px-6 py-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-inner">
                    <svg class="w-4 h-4 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <input type="date" wire:model.live="selectedDate" class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white uppercase">
                </div>

                <div class="flex-1 min-w-[300px] relative group">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 group-focus-within:text-primary-blue transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="CARI NAMA PRODUK..." class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300 shadow-inner">
                </div>

                <div class="w-full md:w-64 relative group">
                    <select wire:model.live="filterCategory" class="w-full pl-6 pr-10 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-widest appearance-none shadow-inner">
                        <option value="">SEMUA KATEGORI</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Daftar Audit Produk</h2>
                <div class="px-6 py-2 bg-blue-50 dark:bg-blue-900/30 text-primary-blue rounded-full text-[10px] font-black uppercase tracking-widest">
                    Showing {{ count($reportData) }} Items
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Produk</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kategori</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Stok Awal</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Terjual</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Sistem</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Fisik</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Selisih</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($reportData as $row)
                        <tr wire:key="inventory-row-{{ $row->id }}" class="group hover:bg-gray-50/30 dark:hover:bg-gray-800/30 transition-all">
                            <td class="px-10 py-6">
                                <span class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight group-hover:text-primary-blue transition-colors">{{ $row->name }}</span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="px-4 py-2 bg-gray-100 dark:bg-gray-900 rounded-xl text-[9px] font-black text-gray-500 uppercase tracking-widest">{{ $row->category }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                @if($editingProductId == $row->id)
                                    <input type="number" wire:model="newOpeningStock" class="w-20 px-3 py-2 bg-gray-50 dark:bg-gray-900 border-2 border-primary-blue rounded-xl text-sm font-black text-center focus:ring-0">
                                @else
                                    <span class="text-sm font-black text-gray-600 dark:text-gray-400">{{ $row->opening }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-primary-blue rounded-lg text-xs font-black">
                                    {{ $row->sold }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-sm font-black text-gray-600 dark:text-gray-400 italic">{{ $row->expected }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                @if($editingProductId == $row->id)
                                    <input type="number" wire:model="newClosingStock" class="w-24 px-3 py-2 bg-gray-50 dark:bg-gray-900 border-2 border-primary-blue rounded-xl text-sm font-black text-center focus:ring-0">
                                @else
                                    <span class="text-sm font-black text-gray-800 dark:text-white">{{ $row->closing }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-6 text-right">
                                @if($row->discrepancy == 0)
                                    <span class="text-[10px] font-black text-green-500 uppercase tracking-widest">Cocok</span>
                                @else
                                    <div class="flex flex-col items-end">
                                        <span class="text-lg font-black italic {{ $row->discrepancy < 0 ? 'text-primary-red' : 'text-amber-500' }}">
                                            {{ $row->discrepancy > 0 ? '+' : '' }}{{ $row->discrepancy }}
                                        </span>
                                        <span class="text-[8px] font-black uppercase tracking-tighter {{ $row->discrepancy < 0 ? 'text-primary-red' : 'text-amber-500' }}">
                                            {{ $row->discrepancy < 0 ? 'Hilang/Kurang' : 'Lebih' }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-10 py-6 text-right">
                                @if($editingProductId == $row->id)
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="updateStock" wire:loading.attr="disabled" class="p-2 bg-green-500 text-white rounded-lg hover:scale-110 transition-all shadow-lg shadow-green-500/20">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </button>
                                        <button wire:click="cancelEdit" wire:loading.attr="disabled" class="p-2 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:scale-110 transition-all">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <button wire:click="editStock('{{ $row->id }}', {{ $row->opening }}, {{ $row->closing }})" class="p-3 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-primary-blue hover:bg-primary-blue/5 rounded-xl transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-200 mb-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 11h10"/><path d="M7 16h10"/><path d="M7 6h10"/></svg>
                                    <span class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">Tidak ada data produk ditemukan</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-10 border-t border-gray-50 dark:border-gray-700/50">
                {{ $products->links('livewire.custom-pagination') }}
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-10 flex items-center justify-between px-8">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Laporan digenerate pada: <span class="text-gray-600 dark:text-gray-300">{{ now()->translatedFormat('d F Y H:i') }}</span>
            </p>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">All Data Synced</span>
            </div>
        </div>
    <!-- Export Options -->
    <div class="fixed bottom-10 right-10 z-[100]" x-data="{ open: false }">
        <button @click="open = !open" class="px-10 py-5 bg-primary-red text-white rounded-[2rem] shadow-2xl shadow-red-500/40 font-black italic uppercase text-sm tracking-[0.2em] transform hover:-translate-y-2 hover:scale-105 transition-all flex items-center gap-4 group">
            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Export Data</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute bottom-full right-0 mb-6 w-72 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800 p-4 flex flex-col gap-2">
            <button @click="exportInventoryData('xlsx'); open = false" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-2xl transition-all text-left group">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-xl flex items-center justify-center font-black italic text-xs">XLSX</div>
                <div>
                    <p class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Microsoft Excel</p>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Format Tabel Berwarna</p>
                </div>
            </button>
            <button @click="exportInventoryData('csv'); open = false" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-2xl transition-all text-left group">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center font-black italic text-xs">CSV</div>
                <div>
                    <p class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Comma Separated</p>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Format Data Mentah</p>
                </div>
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script>
        async function exportInventoryData(format = 'xlsx') {
            const filename = `Laporan_Stok_{{ $dateFormatted }}`;

            if (format === 'csv') {
                const wb = XLSX.utils.book_new();
                const summaryData = [
                    ["LAPORAN AUDIT STOK - LABANTIK"],
                    ["Tanggal Audit", "{{ $dateFormatted }}"],
                    ["Total Unit Terjual", {{ $totalSold }}],
                    ["Total Selisih", {{ $totalDiscrepancy }}]
                ];
                const ws = XLSX.utils.aoa_to_sheet(summaryData);
                XLSX.utils.book_append_sheet(wb, ws, "Rekap");
                XLSX.writeFile(wb, `${filename}.csv`, { bookType: 'csv' });
                return;
            }

            // ExcelJS for Styled XLSX
            const workbook = new ExcelJS.Workbook();
            const sheet = workbook.addWorksheet('Ringkasan');

            sheet.columns = [
                { header: '', key: 'col1', width: 35 },
                { header: '', key: 'col2', width: 25 },
                { header: '', key: 'col3', width: 15 },
                { header: '', key: 'col4', width: 15 },
                { header: '', key: 'col5', width: 15 }
            ];

            // 1. Header
            const titleRow = sheet.addRow(['LAPORAN AUDIT STOK - LABANTIK']);
            titleRow.font = { name: 'Arial Black', size: 16, italic: true, color: { argb: 'FF1E40AF' } };
            sheet.addRow(['Tanggal Audit', "{{ $dateFormatted }}"]);
            sheet.addRow(['Dicetak Pada', new Date().toLocaleString('id-ID')]);
            sheet.addRow([]);

            // 2. Summary Table
            const summaryHeader = sheet.addRow(['RINGKASAN AUDIT']);
            summaryHeader.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            summaryHeader.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1E40AF' } };

            const dataRows = [
                ["Total Unit Terjual", {{ $totalSold }}],
                ["Total Selisih Fisik", {{ $totalDiscrepancy }}],
                ["Produk dengan Selisih", {{ $itemsWithIssue }}]
            ];

            dataRows.forEach(row => {
                sheet.addRow(row);
            });

            sheet.addRow([]);

            // 3. Detail Table
            const detSheet = workbook.addWorksheet('Audit Detail');
            detSheet.columns = [
                { header: 'Produk', key: 'name', width: 35 },
                { header: 'Kategori', key: 'cat', width: 20 },
                { header: 'Stok Awal', key: 'opening', width: 12 },
                { header: 'Terjual', key: 'sold', width: 12 },
                { header: 'Ekspektasi', key: 'exp', width: 15 },
                { header: 'Stok Fisik', key: 'closing', width: 12 },
                { header: 'Selisih', key: 'disc', width: 12 }
            ];

            const hRow = detSheet.getRow(1);
            hRow.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            hRow.eachCell(cell => {
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEF4444' } };
                cell.alignment = { horizontal: 'center' };
                cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
            });

            @foreach($reportData as $row)
                {
                    detSheet.addRow([
                        "{{ $row->name }}",
                        "{{ $row->category }}",
                        {{ $row->opening }},
                        {{ $row->sold }},
                        {{ $row->expected }},
                        {{ $row->closing }},
                        {{ $row->discrepancy }}
                    ]).eachCell(cell => {
                        cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                    });
                }
            @endforeach

            const buffer = await workbook.xlsx.writeBuffer();
            saveAs(new Blob([buffer]), `${filename}.xlsx`);
        }
    </script>
</div>
</div>
