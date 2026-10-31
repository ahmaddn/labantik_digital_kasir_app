<div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-950 min-h-screen font-outfit">
    <div class="max-w-7xl mx-auto">
        <x-hub-tabs hub="inventaris" title="Laporan Selisih Stok" subtitle="Audit stok fisik harian, perbandingan barang keluar, dan analisis selisih stok." />

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-4 bg-emerald-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="selectedDate" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="exportExcel" wire:loading.attr="disabled" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl font-bold text-xs shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                    <span wire:loading wire:target="exportExcel">Mengekspor...</span>
                </button>

                @php
                    $isSessionFinished = \App\Models\DailyRecap::whereDate('date', now())->where('actual_cash', '>', 0)->exists();
                @endphp
                <a href="{{ $isSessionFinished ? route('dashboard') : route('kasir') }}" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition-colors">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            
            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-xl shadow-blue-900/5 border-t-4 border-primary-blue">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Total Unit Terjual</span>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black italic text-gray-800 dark:text-white leading-none">{{ $totalSold }}</span>
                    <span class="text-xs font-black text-primary-blue uppercase tracking-tighter mb-1">Items</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-xl shadow-blue-900/5 border-t-4 border-primary-red">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Total Selisih Fisik</span>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black italic {{ $totalDiscrepancy < 0 ? 'text-primary-red' : ($totalDiscrepancy > 0 ? 'text-green-500' : 'text-gray-800 dark:text-white') }} leading-none">
                        {{ $totalDiscrepancy > 0 ? '+' : '' }}{{ $totalDiscrepancy }}
                    </span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-tighter mb-1">Items</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-xl shadow-blue-900/5 border-t-4 border-amber-500">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Produk Bermasalah</span>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black italic text-amber-500 leading-none">{{ $itemsWithIssue }}</span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-tighter mb-1">SKU</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 mb-10">
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

                <div class="w-full md:w-64 relative group">
                    <select wire:model.live="filterStatus" class="w-full pl-6 pr-10 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue/20 font-black text-xs text-gray-800 dark:text-white uppercase tracking-widest appearance-none shadow-inner">
                        <option value="">SEMUA STATUS SELISIH</option>
                        <option value="bermasalah">BERMASALAH (ADA SELISIH)</option>
                        <option value="normal">NORMAL (SESUAI)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Daftar Audit Produk</h2>
                <div class="px-6 py-2 bg-blue-50 dark:bg-blue-900/30 text-primary-blue rounded-full text-[10px] font-black uppercase tracking-widest">
                    Showing {{ count($reportData) }} Items
                </div>
            </div>
                    <!-- Desktop Table View (>= md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produk</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Stok Awal</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Terjual</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Sistem</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Fisik</th>
                        <th class="px-4 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Selisih</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($reportData as $row)
                    <tr wire:key="inventory-row-{{ $row->id }}" class="group hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $row->name }}</span>
                                @if($row->cashier_name)
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                        Penginput: {{ $row->cashier_name }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-[10px] font-semibold text-gray-600 dark:text-gray-400 uppercase">{{ $row->category }}</span>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            @if($editingProductId == $row->id)
                                <input type="number" wire:model="newOpeningStock" class="w-16 px-2 py-1 bg-white dark:bg-gray-800 border border-blue-500 rounded-lg text-xs font-bold text-center focus:ring-1 focus:ring-blue-500">
                            @else
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $row->opening }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold">
                                {{ $row->sold }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $row->expected }}</span>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            @if($editingProductId == $row->id)
                                <input type="number" wire:model="newClosingStock" class="w-16 px-2 py-1 bg-white dark:bg-gray-800 border border-blue-500 rounded-lg text-xs font-bold text-center focus:ring-1 focus:ring-blue-500">
                            @else
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $row->closing }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">
                            @if($row->discrepancy == 0)
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">Cocok</span>
                            @else
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-bold {{ $row->discrepancy < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ $row->discrepancy > 0 ? '+' : '' }}{{ $row->discrepancy }}
                                    </span>
                                    <span class="text-[9px] font-semibold {{ $row->discrepancy < 0 ? 'text-rose-500' : 'text-amber-500' }}">
                                        {{ $row->discrepancy < 0 ? 'Kurang' : 'Lebih' }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if($editingProductId == $row->id)
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="updateStock" wire:loading.attr="disabled" class="p-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors shadow-sm" title="Simpan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button wire:click="cancelEdit" wire:loading.attr="disabled" class="p-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 transition-colors" title="Batal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @else
                                <button wire:click="editStock('{{ $row->id }}', {{ $row->opening }}, {{ $row->closing }})" class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition-colors" title="Edit Stok">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <p class="text-xs font-semibold">Tidak ada data produk ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Cards View (< md) -->
        <div class="block md:hidden p-3.5 space-y-2.5">
            @forelse($reportData as $row)
            <div wire:key="inventory-card-{{ $row->id }}" class="p-3 bg-white dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col gap-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase truncate">{{ $row->name }}</h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase">{{ $row->category }}</span>
                            @if($row->cashier_name)
                                <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                                <span class="text-[9px] text-gray-400">{{ $row->cashier_name }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($row->discrepancy == 0)
                            <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">Cocok</span>
                        @else
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $row->discrepancy < 0 ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-800' : 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:border-amber-800' }}">
                                {{ $row->discrepancy > 0 ? '+' : '' }}{{ $row->discrepancy }} ({{ $row->discrepancy < 0 ? 'Kurang' : 'Lebih' }})
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Stock Metric Grid -->
                <div class="grid grid-cols-4 gap-1.5 py-2 px-2.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-center">
                    <div>
                        <div class="text-[9px] text-gray-400 font-medium">Awal</div>
                        @if($editingProductId == $row->id)
                            <input type="number" wire:model="newOpeningStock" class="w-full mt-1 px-1 py-0.5 text-xs font-bold text-center border border-blue-500 rounded bg-white dark:bg-gray-800">
                        @else
                            <div class="text-xs font-bold text-gray-700 dark:text-gray-300 mt-0.5">{{ $row->opening }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 font-medium">Terjual</div>
                        <div class="text-xs font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $row->sold }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 font-medium">Sistem</div>
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-0.5">{{ $row->expected }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 font-medium">Fisik</div>
                        @if($editingProductId == $row->id)
                            <input type="number" wire:model="newClosingStock" class="w-full mt-1 px-1 py-0.5 text-xs font-bold text-center border border-blue-500 rounded bg-white dark:bg-gray-800">
                        @else
                            <div class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">{{ $row->closing }}</div>
                        @endif
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="flex items-center justify-end pt-1">
                    @if($editingProductId == $row->id)
                        <div class="flex items-center gap-2">
                            <button wire:click="cancelEdit" wire:loading.attr="disabled" class="px-3 py-1 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button wire:click="updateStock" wire:loading.attr="disabled" class="px-3 py-1 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    @else
                        <button wire:click="editStock('{{ $row->id }}', {{ $row->opening }}, {{ $row->closing }})" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Sesuaikan Stok</span>
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold">Tidak ada data produk ditemukan</p>
            </div>
            @endforelse
        </div>

            <!-- Pagination -->
            <div class="p-10 border-t border-gray-50 dark:border-gray-700/50">
                {{ $products->links('livewire.partials.custom-pagination') }}
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
    </div>
</div>

