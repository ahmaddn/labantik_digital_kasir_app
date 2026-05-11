<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Manajemen Supplier</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Data Penitip Barang & Supplier</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 sticky top-10">
                <div class="flex items-center mb-8">
                    <div class="w-12 h-12 bg-primary-blue rounded-2xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-900/20">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h2 class="text-xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
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

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="flex-1 py-5 bg-primary-blue text-white rounded-[2rem] shadow-2xl shadow-blue-900/20 font-black italic uppercase tracking-wider transform hover:-translate-y-1 transition-all">
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah Supplier' }}
                        </button>
                        @if($editingId)
                        <button type="button" wire:click="cancelEdit" class="p-5 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-[2rem] hover:text-primary-red transition-all">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative group w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Cari supplier..." class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue/20 font-black text-[10px] text-gray-800 dark:text-white uppercase tracking-widest placeholder:text-gray-300">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
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
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
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
                                        <button wire:click="editSupplier({{ $supplier->id }})" class="p-3 bg-white dark:bg-gray-800 text-primary-blue rounded-xl shadow-sm hover:scale-110 transition-transform">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>
                                        <button onclick="confirm('Hapus supplier ini?') || event.stopImmediatePropagation()" wire:click="deleteSupplier({{ $supplier->id }})" class="p-3 bg-white dark:bg-gray-800 text-primary-red rounded-xl shadow-sm hover:scale-110 transition-transform">
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
                </div>
                <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
                    {{ $suppliers->links('livewire.custom-pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
