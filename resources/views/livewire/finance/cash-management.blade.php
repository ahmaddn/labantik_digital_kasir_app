<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Buku Kas Internal</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Pencatatan Uang Riil & Operasional</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 transition-all">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <input type="month" wire:model.live="filterMonth" class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white">
            </div>

            <button wire:click="exportExcel" wire:loading.attr="disabled" class="px-8 py-4 bg-green-500 text-white rounded-2xl shadow-xl shadow-green-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Mengekspor...</span>
            </button>

            @if($isSubUnit)
            <button wire:click="openConsolidateModal" class="px-8 py-4 bg-amber-500 text-white rounded-2xl shadow-xl shadow-amber-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 5-5-5-5-8 3"/><path d="M7 21A14 14 0 0 1 21 7"/></svg>
                Gabungkan Ke Kas Induk
            </button>
            @endif

            <button wire:click="openModal" class="px-8 py-4 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 font-black italic uppercase text-xs tracking-widest transform hover:-translate-y-1 transition-all flex items-center">
                <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Catat Kas Baru
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8 mb-12">
        <!-- Total Saldo Kas Card -->
        <div class="bg-gradient-to-br from-emerald-800 to-teal-950 rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-emerald-950/30 relative overflow-hidden group border-b-8 border-emerald-500">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-3 text-emerald-300">Total Saldo Kas</h3>
            <p class="text-2xl xl:text-3xl font-black italic text-white {{ ($currentModalBalance + $currentProfitBalance) < 0 ? 'text-primary-red' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($currentModalBalance + $currentProfitBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Saldo Modal Card -->
        <div class="bg-gray-900 rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-gray-900/20 relative overflow-hidden group border-b-8 border-primary-blue">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-3">Saldo Kas Modal</h3>
            <p class="text-2xl xl:text-3xl font-black italic text-white {{ $currentModalBalance < 0 ? 'text-primary-red' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($currentModalBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Saldo Keuntungan Card -->
        <div class="bg-primary-blue rounded-[3rem] p-6 md:p-7 text-white shadow-2xl shadow-blue-900/20 relative overflow-hidden group border-b-8 border-white/20">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m16 12-4-4-4 4"/><path d="M12 16V8"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-3">Saldo Kas Keuntungan</h3>
            <p class="text-2xl xl:text-3xl font-black italic text-white {{ $currentProfitBalance < 0 ? 'text-red-300' : '' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($currentProfitBalance, 0, ',', '.') }}</p>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-6 md:p-7 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-green-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Pemasukan (Bulan Ini)</h3>
            <p class="text-2xl xl:text-3xl font-black italic text-green-500" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($monthlyIncome, 0, ',', '.') }}</p>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-6 md:p-7 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
            </div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">Pengeluaran (Bulan Ini)</h3>
            <p class="text-2xl xl:text-3xl font-black italic text-primary-red" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($monthlyExpense, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Category Stats Cards -->
    <div class="mb-12">
        <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-6">Ringkasan Per Kategori</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($categoryStats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-24 h-24 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-800 dark:text-white mb-4">{{ $stat['name'] }}</h3>
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
                        <span class="text-lg font-black italic {{ $stat['balance'] >= 0 ? 'text-primary-blue' : 'text-primary-red' }}" :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stat['balance'], 0, ',', '.') }}</span>
                    </div>
                    @if($stat['name'] !== 'Bagi Hasil Mingguan')
                        <div class="mt-3 pt-2 border-t border-dashed border-gray-150 dark:border-gray-700">
                            <button wire:click="openAdjustModal('{{ $stat['id'] }}', '{{ $stat['name'] }}', {{ $stat['balance'] }})" class="w-full text-center py-2.5 bg-gray-50 hover:bg-primary-blue hover:text-white dark:bg-gray-700/50 dark:hover:bg-primary-blue dark:text-gray-300 dark:hover:text-white text-[9px] font-black uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                Sesuaikan Saldo Fisik
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-3xl p-8 text-center border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-black text-gray-400 uppercase tracking-widest italic">Belum ada kategori kas.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden mb-12">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Riwayat Transaksi Kas</h2>
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
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300"
        >
            <div class="p-10 bg-primary-blue text-white relative">
                <div class="absolute right-10 top-10">
                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <h3 class="text-3xl font-black italic uppercase tracking-tighter mb-1">{{ $editingId ? 'Edit Kas' : 'Catat Kas Baru' }}</h3>
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
            class="bg-white dark:bg-gray-900 w-full max-w-md rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300"
        >
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 text-primary-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-2">Hapus Catatan Kas</h3>
                <p class="text-sm font-bold text-gray-400">Apakah Anda yakin ingin menghapus catatan kas ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="p-8 bg-gray-50 dark:bg-gray-800/50 flex gap-4">
                <button @click="show = false" class="flex-1 py-4 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-transform">
                    Batal
                </button>
                <button wire:click="deleteTransaction" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-red-500/20 hover:scale-105 active:scale-95 transition-all">
                    Ya, Hapus
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
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300 border-t-8 border-t-primary-blue"
        >
            <div class="p-10 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Sesuaikan Saldo Kas Fisik</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Kategori: {{ $adjustCategoryName }}</p>
            </div>
            
            <div class="p-10 space-y-6">
                <!-- System Balance -->
                <div class="bg-gray-50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Saldo Sistem Saat Ini</p>
                    <p class="text-xl font-black italic text-gray-800 dark:text-white">Rp{{ number_format($adjustSystemBalance, 0, ',', '.') }}</p>
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
            class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300 border-t-8 border-t-amber-500"
        >
            <div class="p-10 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Gabungkan Saldo ke Kas Induk</h3>
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

