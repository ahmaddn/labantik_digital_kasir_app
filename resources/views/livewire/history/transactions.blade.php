<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Riwayat Transaksi</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Log Aktivitas Penjualan</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                <svg class="w-4 h-4 text-gray-400 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" wire:model.live="search" placeholder="Cari transaksi..." class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white w-48">
            </div>

            <div class="flex items-center bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                <svg class="w-4 h-4 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <select wire:model.live="filterStatus" class="border-none p-0 focus:ring-0 font-black text-xs bg-transparent dark:text-white uppercase tracking-widest">
                    <option value="">Semua Status</option>
                    <option value="uang_diterima">Lunas</option>
                    <option value="belum_kembalian">Pending</option>
                    <option value="belum_menerima_uang">Hutang</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Ref</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pembeli</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Items</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Bayar</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ \Carbon\Carbon::parse($tx->transacted_at)->format('d M Y') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">{{ \Carbon\Carbon::parse($tx->transacted_at)->format('H:i') }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="text-sm font-black text-primary-blue uppercase tracking-tight">{{ $tx->reference }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-[10px] font-black text-primary-blue border border-gray-100 dark:border-gray-800 mr-3">
                                    {{ substr($tx->buyer_name ?? 'G', 0, 1) }}
                                </div>
                                <span class="text-sm font-black italic uppercase tracking-tighter text-gray-700 dark:text-gray-300">
                                    {{ $tx->buyer_name ?? 'Guest Customer' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="px-4 py-2 bg-gray-100 dark:bg-gray-900 rounded-xl text-xs font-black text-gray-600 dark:text-gray-400">
                                {{ $tx->total_qty }} <span class="text-[9px] uppercase ml-1 opacity-50">Unit</span>
                            </span>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-lg font-black text-primary-red italic">Rp{{ number_format($tx->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <span class="px-4 py-1.5 text-[9px] font-black rounded-full uppercase tracking-widest {{ $tx->status === 'uang_diterima' ? 'bg-green-100 text-green-700' : 'bg-primary-red/10 text-primary-red' }}">
                                {{ str_replace('_', ' ', $tx->status) }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="viewDetails('{{ $tx->reference }}')" class="p-4 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-primary-blue rounded-2xl transition-all hover:scale-110 active:scale-95" title="View Details">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button wire:click="edit('{{ $tx->reference }}')" class="p-4 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-amber-500 rounded-2xl transition-all hover:scale-110 active:scale-95" title="Edit Transaction">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                <button @click="$dispatch('open-delete-transaction', { id: '{{ $tx->reference }}' })" class="p-4 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-primary-red rounded-2xl transition-all hover:scale-110 active:scale-95" title="Delete Transaction">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                </button>
                            </div>
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

    <!-- Edit Modal -->
    <div 
        x-data="{ show: @entangle('showEditModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
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
            <div class="p-10 bg-amber-500 text-white relative">
                <div class="absolute right-10 top-10">
                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <h3 class="text-3xl font-black italic uppercase tracking-tighter mb-1">Edit Transaksi</h3>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] opacity-60">Reference: {{ $editingReference }}</p>
            </div>

            <form wire:submit.prevent="update" class="flex flex-col h-full">
                <div class="p-10 space-y-8 max-h-[50vh] overflow-y-auto no-scrollbar">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pembeli</label>
                            <input type="text" wire:model="editBuyerName" class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl px-6 py-4 text-sm font-black text-gray-800 dark:text-white focus:ring-4 focus:ring-amber-500/10 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Pembayaran</label>
                            <select wire:model="editStatus" class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl px-6 py-4 text-sm font-black text-gray-800 dark:text-white focus:ring-4 focus:ring-amber-500/10 transition-all">
                                <option value="uang_diterima">Lunas</option>
                                <option value="belum_kembalian">Pending</option>
                                <option value="belum_menerima_uang">Hutang</option>
                                <option value="uang_dipinjam">Uang Dipinjam</option>
                            </select>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block border-b border-gray-100 dark:border-gray-800 pb-2">Daftar Produk</label>
                        @foreach($editItems as $index => $item)
                        <div class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/50 group">
                            <div class="flex-1">
                                <p class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">{{ $item['name'] }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">@ Rp{{ number_format($item['unit_price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 px-3 py-1 shadow-sm">
                                    <button type="button" @click="$wire.set('editItems.{{ $index }}.quantity', Math.max(1, {{ $item['quantity'] }} - 1))" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-amber-500 transition-colors">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                                    </button>
                                    <input type="number" wire:model.live="editItems.{{ $index }}.quantity" class="w-12 text-center bg-transparent border-none p-0 focus:ring-0 text-sm font-black text-gray-800 dark:text-white" min="1">
                                    <button type="button" @click="$wire.set('editItems.{{ $index }}.quantity', {{ $item['quantity'] }} + 1)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-amber-500 transition-colors">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                    </button>
                                </div>
                                <div class="w-24 text-right">
                                    <p class="text-sm font-black text-primary-red italic">Rp{{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-10 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center mt-auto">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estimasi Total Baru</p>
                        <p class="text-3xl font-black text-amber-500 italic tracking-tighter">
                            Rp{{ number_format(collect($editItems)->sum(fn($i) => $i['unit_price'] * $i['quantity']), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" @click="show = false" class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-400 rounded-2xl font-black uppercase text-[10px] tracking-widest border border-gray-100 dark:border-gray-700 hover:text-gray-600 transition-all shadow-sm">Batal</button>
                        <button type="submit" class="px-8 py-4 bg-amber-500 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-amber-500/30 hover:scale-105 transition-all">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div 
        x-data="{ 
            show: false, 
            id: null,
            open(event) {
                this.id = event.detail.id;
                this.show = true;
            },
            close() {
                this.show = false;
                this.id = null;
            },
            confirm() {
                $wire.delete(this.id);
                this.close();
            }
        }" 
        @open-delete-transaction.window="open($event)"
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="close()"
            class="bg-white dark:bg-gray-800 rounded-[3rem] p-12 max-w-md w-full shadow-2xl text-center"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
        >
            <div class="w-20 h-20 bg-primary-red/10 text-primary-red rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </div>
            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-4">Hapus Transaksi?</h3>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-10 leading-relaxed">Penghapusan transaksi akan mempengaruhi laporan keuangan. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-4">
                <button @click="close()" class="flex-1 py-4 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:text-gray-600 transition-all">Batal</button>
                <button @click="confirm()" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-500/30 hover:scale-105 transition-all">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div 
        x-data="{ show: @entangle('showDetailsModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-sm"
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
                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase {{ ($this->detailItems->first()->status ?? '') === 'uang_diterima' ? 'bg-green-100 text-green-700' : 'bg-primary-red/10 text-primary-red' }}">
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
</div>
