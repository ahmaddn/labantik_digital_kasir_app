<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Rekap Tahunan</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Performansi Keuangan Per Tahun</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <select wire:model.live="selectedYear" class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            @if($recap)
            <button onclick="exportYearlyExcel('Rekap_Tahunan_{{ $selectedYear }}')" class="px-8 py-4 bg-primary-red text-white rounded-2xl shadow-xl shadow-red-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Export XLSX
            </button>
            @endif
        </div>
    </div>

    @if($recap)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-primary-blue rounded-[3.5rem] p-12 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-64 h-64 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="text-xs font-black uppercase tracking-[0.4em] opacity-60 mb-4">Total Omzet Tahunan</h3>
            <p class="text-6xl font-black italic tracking-tighter">Rp{{ number_format($recap->total_revenue_real, 0, ',', '.') }}</p>
            <div class="mt-12 space-y-4 border-t border-white/10 pt-8">
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40">Murni Jurusan</p>
                    <p class="text-2xl font-black italic">Rp{{ number_format($recap->total_internal_revenue, 0, ',', '.') }}</p>
                </div>
                <div class="flex justify-between items-center opacity-40">
                    <p class="text-[10px] font-black uppercase tracking-widest">Gross Omzet</p>
                    <p class="text-xl font-black italic">Rp{{ number_format($recap->total_revenue_all, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-64 h-64 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.5 8.5-5-5L2 17"/><polyline points="18 7 22 7 22 11"/></svg>
            </div>
            <h3 class="text-xs font-black uppercase tracking-[0.4em] text-gray-400 mb-4">Total Keuntungan Bersih</h3>
            <p class="text-6xl font-black italic tracking-tighter text-primary-red">Rp{{ number_format($recap->total_profit, 0, ',', '.') }}</p>
            <div class="mt-12 flex gap-10">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Modal Berjalan</p>
                    <p class="text-xl font-black italic text-gray-800 dark:text-white">Rp{{ number_format($recap->total_modal, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Avg Profit/Mo</p>
                    <p class="text-xl font-black italic text-gray-800 dark:text-white">Rp{{ number_format($recap->total_profit / max(1, $recap->months_count), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance Section -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-10 mb-12 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">Performa Per Kategori ({{ $selectedYear }})</h2>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Rekap Tahunan Berdasarkan Jenis Produk</p>
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
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Total Modal</th>
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Keuntungan</th>
                        <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($categoryRecap as $catName => $stats)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all">
                        <td class="py-8">
                            <span class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight group-hover:text-primary-blue transition-colors">{{ $catName }}</span>
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

    <!-- Monthly Stats Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Trend Bulanan ({{ $selectedYear }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Bulan</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Volume</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pendapatan</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keuntungan</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($monthlyBreakdown as $month)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ \Carbon\Carbon::create(null, $month->month)->translatedFormat('F') }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ $month->total_transactions }} Transaksi</span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-primary-blue italic">Rp{{ number_format($month->total_revenue_real, 0, ',', '.') }}</div>
                            <div class="w-full bg-gray-100 dark:bg-gray-900 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-primary-blue h-full transition-all duration-1000" style="width: {{ ($recap->total_revenue_real > 0) ? ($month->total_revenue_real / $recap->total_revenue_real * 100) : 0 }}%"></div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-primary-red italic">Rp{{ number_format($month->total_profit, 0, ',', '.') }}</div>
                            <div class="w-full bg-gray-100 dark:bg-gray-900 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-primary-red h-full transition-all duration-1000" style="width: {{ ($recap->total_profit > 0) ? ($month->total_profit / $recap->total_profit * 100) : 0 }}%"></div>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <a href="{{ route('monthly-recap', ['month' => $month->month, 'year' => $selectedYear]) }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-primary-blue hover:text-white transition-all opacity-0 group-hover:opacity-100">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    <!-- Export Options -->
    <div class="fixed bottom-10 right-10 z-[100]" x-data="{ open: false }">
        <button @click="open = !open" class="px-10 py-5 bg-primary-red text-white rounded-[2rem] shadow-2xl shadow-red-500/40 font-black italic uppercase text-sm tracking-[0.2em] transform hover:-translate-y-2 hover:scale-105 transition-all flex items-center gap-4 group">
            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Export Data</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute bottom-full right-0 mb-6 w-72 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800 p-4 flex flex-col gap-2">
            <button @click="exportYearlyData('xlsx'); open = false" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-2xl transition-all text-left group">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-xl flex items-center justify-center font-black italic text-xs">XLSX</div>
                <div>
                    <p class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Microsoft Excel</p>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Format Tabel Berwarna</p>
                </div>
            </button>
            <button @click="exportYearlyData('csv'); open = false" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-2xl transition-all text-left group">
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
        async function exportYearlyData(format = 'xlsx') {
            const filename = `Rekap_Tahunan_{{ $selectedYear }}`;

            if (format === 'csv') {
                const wb = XLSX.utils.book_new();
                const summaryData = [
                    ["LAPORAN REKAP TAHUNAN - LABANTIK"],
                    ["Tahun", "{{ $selectedYear }}"],
                    ["Total Omzet", {{ $recap->total_revenue_real ?? 0 }}],
                    ["Total Profit", {{ $recap->total_profit ?? 0 }}]
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
                { header: '', key: 'col3', width: 20 },
                { header: '', key: 'col4', width: 20 },
                { header: '', key: 'col5', width: 20 }
            ];

            // 1. Header
            const titleRow = sheet.addRow(['LAPORAN REKAP TAHUNAN - LABANTIK']);
            titleRow.font = { name: 'Arial Black', size: 16, italic: true, color: { argb: 'FF1E40AF' } };
            sheet.addRow(['Tahun', "{{ $selectedYear }}"]);
            sheet.addRow(['Dicetak Pada', new Date().toLocaleString('id-ID')]);
            sheet.addRow([]);

            // 2. Summary Table
            const summaryHeader = sheet.addRow(['RINGKASAN UTAMA']);
            summaryHeader.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            summaryHeader.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1E40AF' } };

            const dataRows = [
                ["Total Omzet Tahunan", {{ $recap->total_revenue_real ?? 0 }}],
                ["Omzet Internal (Murni Jurusan)", {{ $recap->total_internal_revenue ?? 0 }}],
                ["Total Omzet Kotor", {{ $recap->total_revenue_all ?? 0 }}],
                ["Total Keuntungan", {{ $recap->total_profit ?? 0 }}],
                ["Total Modal Berjalan", {{ $recap->total_modal ?? 0 }}],
                ["Total Transaksi", {{ $recap->total_transactions ?? 0 }}]
            ];

            dataRows.forEach(row => {
                const r = sheet.addRow(row);
                r.getCell(2).numFmt = '#,##0';
            });

            sheet.addRow([]);

            // 3. Category Table
            const catHeader = sheet.addRow(['Kategori', 'Volume', 'Modal', 'Keuntungan', 'Omzet']);
            catHeader.eachCell(cell => {
                cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEF4444' } };
                cell.alignment = { horizontal: 'center' };
                cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
            });

            @foreach($categoryRecap as $catName => $stats)
                {
                    const catRow = sheet.addRow(["{{ $catName }}", {{ $stats->qty }}, {{ $stats->modal }}, {{ $stats->profit }}, {{ $stats->revenue }}]);
                    catRow.eachCell((cell, colNumber) => {
                        if (colNumber > 2) cell.numFmt = '#,##0';
                        cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                    });
                }
            @endforeach

            // 4. Monthly Trend Sheet
            const trendSheet = workbook.addWorksheet('Tren Bulanan');
            trendSheet.columns = [
                { header: 'Bulan', key: 'month', width: 25 },
                { header: 'Transaksi', key: 'tx', width: 15 },
                { header: 'Pendapatan', key: 'rev', width: 25 },
                { header: 'Profit', key: 'profit', width: 25 }
            ];

            const tHeader = trendSheet.getRow(1);
            tHeader.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            tHeader.eachCell(cell => {
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1E40AF' } };
                cell.alignment = { horizontal: 'center' };
            });

            @foreach($monthlyBreakdown as $month)
                trendSheet.addRow([
                    "{{ \Carbon\Carbon::create(null, $month->month)->translatedFormat('F') }}",
                    {{ $month->total_transactions }},
                    {{ $month->total_revenue_real }},
                    {{ $month->total_profit }}
                ]).eachCell((cell, colNumber) => {
                    if (colNumber > 2) cell.numFmt = '#,##0';
                });
            @endforeach

            const buffer = await workbook.xlsx.writeBuffer();
            saveAs(new Blob([buffer]), `${filename}.xlsx`);
        }
    </script>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-[4rem] p-32 border border-gray-100 dark:border-gray-700 text-center flex flex-col items-center shadow-xl shadow-blue-900/5">
        <div class="w-32 h-32 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center mb-10 text-gray-200 dark:text-gray-700 shadow-inner">
            <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/><path d="m9.05 9 5.64 5.64"/><circle cx="12" cy="12" r="2"/></svg>
        </div>
        <h3 class="text-3xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Data Belum Tersedia</h3>
        <p class="text-gray-400 font-bold text-sm mt-4 uppercase tracking-[0.3em] italic">Tidak ada rekaman transaksi untuk tahun {{ $selectedYear }}</p>
    </div>
    @endif
</div>
