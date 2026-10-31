<div class="p-4 sm:p-6">
    <x-hub-tabs hub="inventaris" title="Supplier Rekanan" subtitle="Data pihak ketiga, mitra penitip barang, dan perincian konsinyasi TEFA." />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 sticky top-6">
                <div class="flex items-center mb-8">
                    <div class="w-12 h-12 bg-primary-blue rounded-2xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-900/20">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h2 class="text-xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">
                        {{ $editingId ? 'Edit Supplier' : 'Supplier Baru' }}
                    </h2>
                </div>

                <form wire:submit.prevent="saveSupplier" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Nama Supplier / Penitip</label>
                        <input type="text" wire:model="name" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white uppercase tracking-tight">
                        @error('name') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Kontak (WhatsApp)</label>
                        <input type="text" wire:model="contact" placeholder="08..." class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white uppercase tracking-tight">
                        @error('contact') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Alamat</label>
                        <textarea wire:model="address" rows="3" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white uppercase tracking-tight"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Catatan</label>
                        <input type="text" wire:model="note" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah Supplier' }}
                        </button>
                        @if($editingId)
                        <button type="button" wire:click="cancelEdit" title="Batal Edit" class="p-3 bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-600 rounded-xl transition-colors">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative group w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Cari supplier..." class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300">
                    </div>
                </div>

                    <!-- Desktop Table View -->
                    <table class="hidden md:table w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kontak</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @forelse($suppliers as $supplier)
                            <tr class="transition-all duration-500 group {{ $highlight == $supplier->id ? 'bg-amber-400/10 border-2 border-amber-400 animate-highlight-breath z-10 relative' : 'hover:bg-gray-50 dark:hover:bg-gray-900/30' }}">
                                <td class="px-10 py-8">
                                    <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ $supplier->name }}</div>
                                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $supplier->address ?: 'Tidak ada alamat' }}</div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="text-xs font-black text-primary-blue tracking-widest">{{ $supplier->contact ?: '-' }}</div>
                                </td>
                                <td class="px-10 py-8">
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900 rounded-lg text-[9px] font-black text-gray-500 uppercase tracking-widest">
                                        {{ $supplier->products_count ?? $supplier->products()->count() }} Item
                                    </span>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="editSupplier('{{ $supplier->id }}')" class="p-3 bg-white dark:bg-gray-800 text-primary-blue rounded-xl shadow-sm hover:scale-110 transition-transform">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>
                                        <button @click="$dispatch('open-delete-supplier', { id: '{{ $supplier->id }}' })" class="p-3 bg-white dark:bg-gray-800 text-primary-red rounded-xl shadow-sm hover:scale-110 transition-transform">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-10 py-32 text-center opacity-20">
                                    <p class="text-xs font-black uppercase tracking-widest italic">Belum ada supplier</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Mobile List View: Clean, Minimalist, Thumb-friendly Lucide Icons -->
                    <div class="block md:hidden p-3.5 space-y-2.5">
                        @forelse($suppliers as $supplier)
                            <div class="p-3.5 sm:p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors {{ $highlight == $supplier->id ? 'border-amber-400 bg-amber-400/5' : '' }} shadow-2xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $supplier->name }}</h3>
                                        @if($supplier->address)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $supplier->address }}</p>
                                        @endif
                                        <div class="flex items-center gap-2 mt-2">
                                            @if($supplier->contact)
                                                <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 rounded text-[10px] font-medium">
                                                    {{ $supplier->contact }}
                                                </span>
                                            @endif
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded text-[10px] font-medium">
                                                {{ $supplier->products_count ?? $supplier->products()->count() }} Item
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Aksi: Lucide Icons -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button wire:click="editSupplier('{{ $supplier->id }}')" title="Edit" class="p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>
                                        <button @click="$dispatch('open-delete-supplier', { id: '{{ $supplier->id }}' })" title="Hapus" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <p class="text-xs text-slate-400 italic">Belum ada supplier</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
                    {{ $suppliers->links('livewire.partials.custom-pagination') }}
                </div>
            </div>
        </div>
    <!-- Delete Modal (Clean & Compact) -->
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
                $wire.deleteSupplier(this.id);
                this.close();
            }
        }" 
        @open-delete-supplier.window="open($event)"
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="close()"
            class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-sm w-full shadow-2xl text-center border border-slate-200 dark:border-slate-800"
            x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-200 transform"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="w-12 h-12 bg-rose-500/10 text-rose-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">Hapus Supplier?</h3>
            <p class="text-slate-500 dark:text-slate-400 text-xs mb-6">Tindakan ini tidak dapat dibatalkan. Seluruh data terkait supplier ini akan dihapus permanen.</p>
            <div class="flex gap-2.5">
                <button type="button" @click="close()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors">Batal</button>
                <button type="button" @click="confirm()" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">Hapus</button>
            </div>
        </div>
    </div>
</div>
