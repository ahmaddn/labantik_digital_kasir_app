<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Kategori Produk</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Klasifikasi Inventaris Digital</p>
        </div>
        
        <div class="flex flex-col md:flex-row gap-4 items-center w-full md:w-auto">
            @if(!session('active_jurusan_id'))
            <div class="relative w-full md:w-48 bg-white dark:bg-gray-850 rounded-2xl border border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center shadow-lg shadow-blue-900/5">
                <svg class="w-4 h-4 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                <select wire:model.live="filterJurusan" class="bg-transparent border-none p-0 focus:ring-0 text-[10px] font-black uppercase tracking-widest text-gray-500 w-full">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $jur)
                        <option value="{{ $jur->id }}">{{ $jur->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="relative group w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 group-focus-within:text-primary-blue transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" wire:model.live="search" placeholder="Cari kategori..." class="w-full pl-14 pr-6 py-4 bg-white dark:bg-gray-850 rounded-2xl border border-gray-100 dark:border-gray-800 focus:ring-4 focus:ring-primary-blue/10 focus:border-primary-blue transition-all font-black text-sm text-gray-800 dark:text-white placeholder:text-gray-300 placeholder:italic">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 sticky top-10">
                <div class="flex items-center mb-8">
                    <div class="w-12 h-12 bg-primary-blue rounded-2xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-900/20">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </div>
                    <h2 class="text-xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                        {{ $isEditing ? 'Edit Kategori' : 'Kategori Baru' }}
                    </h2>
                </div>

                <form wire:submit.prevent="save" class="space-y-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Nama Kategori</label>
                        <input type="text" wire:model="name" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white uppercase tracking-tight">
                        @error('name') <span class="text-[10px] font-bold text-primary-red mt-2 ml-2 block uppercase italic">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Deskripsi (Opsional)</label>
                        <textarea wire:model="description" rows="4" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white placeholder:text-gray-300"></textarea>
                    </div>

                    @if(!session('active_jurusan_id'))
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Jurusan / Unit TEFA</label>
                        <select wire:model="jurusan_id" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-xs text-gray-800 dark:text-white">
                            <option value="">Kategori Global</option>
                            @foreach($jurusans as $jur)
                                <option value="{{ $jur->id }}">{{ $jur->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="flex-1 py-5 bg-primary-blue text-white rounded-[2rem] shadow-2xl shadow-blue-900/20 font-black italic uppercase tracking-wider transform hover:-translate-y-1 transition-all">
                            {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                        </button>
                        @if($isEditing)
                        <button type="button" wire:click="resetFields" class="p-5 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-[2rem] hover:text-primary-red transition-all">
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
                    <!-- Desktop Table View -->
                    <table class="hidden md:table w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produk</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @forelse($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-3">
                                        <div class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">{{ $category->name }}</div>
                                        @if($category->jurusan)
                                            <span class="px-1.5 py-0.5 text-[8px] font-black rounded uppercase tracking-wider bg-primary-red/10 text-primary-red">
                                                TEFA {{ $category->jurusan->name }}
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 text-[8px] font-black rounded uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-350">
                                                GLOBAL
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] font-medium text-gray-400 mt-1 line-clamp-1">{{ $category->description ?? 'Tidak ada deskripsi' }}</div>
                                </td>
                                <td class="px-10 py-8">
                                    <span class="px-4 py-1.5 bg-primary-blue/5 text-primary-blue rounded-full text-[10px] font-black uppercase tracking-widest">
                                        {{ $category->products_count ?? $category->products()->count() }} Produk
                                    </span>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit({{ $category->id }})" class="p-3 bg-white dark:bg-gray-800 text-primary-blue rounded-xl shadow-sm hover:scale-110 transition-transform">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>
                                        <button @click="$dispatch('open-delete-category', { id: {{ $category->id }} })" class="p-3 bg-white dark:bg-gray-800 text-primary-red rounded-xl shadow-sm hover:scale-110 transition-transform">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-10 py-32 text-center opacity-20">
                                    <p class="text-xs font-black uppercase tracking-widest italic">Belum ada kategori</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Mobile List View -->
                    <div class="block md:hidden p-4 space-y-4">
                        @forelse($categories as $category)
                            <div class="p-5 rounded-[2rem] border border-gray-150 dark:border-gray-800 bg-white dark:bg-gray-900/40">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-black text-gray-850 dark:text-white uppercase tracking-tight italic">{{ $category->name }}</h3>
                                            @if($category->jurusan)
                                                <span class="px-1.5 py-0.5 text-[8px] font-black rounded uppercase tracking-wider bg-primary-red/10 text-primary-red">
                                                    TEFA {{ $category->jurusan->name }}
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 text-[8px] font-black rounded uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-350">
                                                    GLOBAL
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-medium text-gray-400 mt-1.5">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                                    </div>
                                    
                                    <span class="px-3 py-1.5 bg-primary-blue/5 text-primary-blue rounded-full text-[9px] font-black uppercase tracking-widest shrink-0 border border-primary-blue/10">
                                        {{ $category->products_count ?? $category->products()->count() }} Produk
                                    </span>
                                </div>
                                
                                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                                    <button wire:click="edit({{ $category->id }})" class="p-2.5 bg-white dark:bg-gray-700 text-primary-blue rounded-xl shadow-sm border border-gray-150 dark:border-gray-600">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete-category', { id: {{ $category->id }} })" class="p-2.5 bg-white dark:bg-gray-700 text-primary-red rounded-xl shadow-sm border border-gray-150 dark:border-gray-600">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 text-center opacity-20">
                                <p class="text-xs font-black uppercase tracking-widest italic">Belum ada kategori</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="px-10 py-8 bg-gray-50 dark:bg-gray-900/50">
                    {{ $categories->links('livewire.partials.custom-pagination') }}
                </div>
            </div>
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
        @open-delete-category.window="open($event)"
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
            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-4">Hapus Kategori?</h3>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-10 leading-relaxed">Pastikan tidak ada produk yang menggunakan kategori ini. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-4">
                <button @click="close()" class="flex-1 py-4 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:text-gray-600 transition-all">Batal</button>
                <button @click="confirm()" class="flex-1 py-4 bg-primary-red text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-500/30 hover:scale-105 transition-all">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
