<div class="p-6 space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Topping & Modifikasi Produk</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Atur variasi pilihan tambahan menu di kasir</p>
        </div>
        <div class="flex items-center bg-white dark:bg-gray-800 px-4 py-1.5 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800">
            <button wire:click="$set('activeTab', 'modifiers')" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'modifiers' ? 'bg-primary-blue text-white shadow-lg' : 'text-gray-400' }}">Daftar Topping</button>
            <button wire:click="$set('activeTab', 'groups')" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'groups' ? 'bg-primary-blue text-white shadow-lg' : 'text-gray-400' }}">Kelompok Topping</button>
        </div>
    </div>

    @if($activeTab === 'modifiers')
        <!-- Tab Daftar Topping -->
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-md p-8">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-850 pb-4">
                <h3 class="text-xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">List Item Topping</h3>
                <button wire:click="openModifierModal()" class="px-4 py-2 bg-primary-blue text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:scale-105 transition-all">Tambah Topping</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="pb-3">Nama Topping</th>
                            <th class="pb-3 text-center">Harga Tambahan</th>
                            <th class="pb-3 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800/30">
                        @forelse($modifiers as $mod)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/10 transition-colors">
                                <td class="py-4 text-sm font-bold text-gray-800 dark:text-white uppercase">{{ $mod->name }}</td>
                                <td class="py-4 text-sm font-black text-center text-primary-red">Rp{{ number_format($mod->price, 0, ',', '.') }}</td>
                                <td class="py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="openModifierModal('{{ $mod->id }}')" class="p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button wire:click="$dispatch('trigger-delete-modifier', { id: '{{ $mod->id }}' })" class="p-2 bg-red-500 hover:bg-red-650 text-white rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-xs text-gray-400 italic">Belum ada item topping terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $modifiers->links() }}
            </div>
        </div>
    @else
        <!-- Tab Kelompok Topping -->
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-md p-8">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-850 pb-4">
                <h3 class="text-xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Daftar Kelompok Topping</h3>
                <button wire:click="openGroupModal()" class="px-4 py-2 bg-primary-blue text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:scale-105 transition-all">Tambah Kelompok</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="pb-3">Nama Kelompok</th>
                            <th class="pb-3">Topping Terikat</th>
                            <th class="pb-3 text-center">Aturan Pilihan</th>
                            <th class="pb-3 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800/30">
                        @forelse($groups as $grp)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/10 transition-colors">
                                <td class="py-4 text-sm font-bold text-gray-800 dark:text-white uppercase">{{ $grp->name }}</td>
                                <td class="py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($grp->modifiers as $m)
                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-850 text-gray-600 dark:text-gray-300 text-[9px] font-black border rounded-lg border-gray-250 dark:border-gray-700 uppercase">{{ $m->name }} (+{{ number_format($m->price, 0) }})</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 text-xs font-semibold text-center text-gray-600 dark:text-gray-400">
                                    Min: {{ $grp->min_selection }} | Max: {{ $grp->max_selection }}
                                </td>
                                <td class="py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="openGroupModal('{{ $grp->id }}')" class="p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button wire:click="$dispatch('trigger-delete-group', { id: '{{ $grp->id }}' })" class="p-2 bg-red-500 hover:bg-red-650 text-white rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs text-gray-400 italic">Belum ada kelompok topping terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $groups->links() }}
            </div>
        </div>
    @endif

    <!-- Modal Modifier (Topping) -->
    <div x-data="{ show: @entangle('showModifierModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;" x-transition>
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-left">
            <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">{{ $editingModifierId ? 'Edit Topping' : 'Tambah Topping' }}</h2>
            <form wire:submit.prevent="saveModifier" class="space-y-4">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama Topping</label>
                    <input type="text" wire:model="modifierName" placeholder="Es Batu / Keju / dll" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue text-sm uppercase dark:text-white">
                    @error('modifierName') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Harga Tambahan (Rp)</label>
                    <input type="number" wire:model="modifierPrice" placeholder="1000" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue text-sm dark:text-white font-bold">
                    @error('modifierPrice') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" @click="show = false" class="flex-1 py-3 bg-gray-100 dark:bg-gray-900 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-primary-blue text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modifier Group -->
    <div x-data="{ show: @entangle('showGroupModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;" x-transition>
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-left">
            <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">{{ $editingGroupId ? 'Edit Kelompok Topping' : 'Tambah Kelompok Topping' }}</h2>
            <form wire:submit.prevent="saveGroup" class="space-y-4">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama Kelompok</label>
                    <input type="text" wire:model="groupName" placeholder="Pilihan Suhu / Ekstra Topping" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue text-sm uppercase dark:text-white">
                    @error('groupName') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Min Pilihan</label>
                        <input type="number" wire:model="minSelection" min="0" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-sm font-semibold dark:text-white">
                        @error('minSelection') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Max Pilihan</label>
                        <input type="number" wire:model="maxSelection" min="1" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-sm font-semibold dark:text-white">
                        @error('maxSelection') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Pilih Topping yang Diikat</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto no-scrollbar bg-gray-50 dark:bg-gray-900 p-4 rounded-xl">
                        @foreach($allModifiers as $mod)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="selectedModifiers" value="{{ $mod->id }}" class="rounded border-gray-300 text-primary-blue focus:ring-primary-blue dark:bg-gray-800 dark:border-gray-700">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">{{ $mod->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedModifiers') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" @click="show = false" class="flex-1 py-3 bg-gray-100 dark:bg-gray-900 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-primary-blue text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('trigger-delete-modifier', (event) => {
                const modifierId = event.detail.id;
                Swal.fire({
                    title: 'HAPUS TOPPING?',
                    text: 'Tindakan ini akan menghapus item topping dari daftar secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'YA, HAPUS!',
                    cancelButtonText: 'BATAL',
                    customClass: {
                        popup: 'nb-popup-card',
                        confirmButton: 'nb-popup-confirm',
                        cancelButton: 'nb-popup-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deleteModifier', modifierId);
                    }
                });
            });

            window.addEventListener('trigger-delete-group', (event) => {
                const groupId = event.detail.id;
                Swal.fire({
                    title: 'HAPUS KELOMPOK?',
                    text: 'Seluruh ikatan topping pada kelompok ini akan terlepas!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'YA, HAPUS!',
                    cancelButtonText: 'BATAL',
                    customClass: {
                        popup: 'nb-popup-card',
                        confirmButton: 'nb-popup-confirm',
                        cancelButton: 'nb-popup-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deleteGroup', groupId);
                    }
                });
            });
        });
    </script>
</div>
