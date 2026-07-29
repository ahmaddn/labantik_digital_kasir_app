<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Manajemen Jurusan</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola Unit TEFA Jurusan SMKN 1 Talaga</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-6 py-4 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Jurusan Baru
            </button>
        </div>
    </div>

    <!-- Table & Filters Container -->
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
        <!-- Search & Filter -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari nama jurusan..." class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">ID Jurusan (UUID)</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Nama Jurusan / TEFA</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($jurusans as $jurusan)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-5 pl-4 text-sm font-semibold text-gray-400">
                                {{ $jurusan->id }}
                            </td>
                            <td class="py-5 font-bold text-gray-800 dark:text-white group-hover:text-primary-blue dark:group-hover:text-primary-yellow transition-colors">
                                TEFA {{ $jurusan->name }}
                                @if($jurusan->parent)
                                    <span class="ml-2 px-2.5 py-1 bg-blue-500/10 text-primary-blue dark:text-primary-yellow text-[10px] font-black uppercase rounded-lg border border-blue-500/20">
                                        Sub-Unit: {{ $jurusan->parent->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 text-right pr-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if(session('active_role_name') !== 'pengelola_jurusan' || !is_null($jurusan->parent_id))
                                        <button wire:click="openEditModal('{{ $jurusan->id }}')" class="p-2 text-gray-400 hover:text-primary-blue dark:hover:text-primary-yellow hover:bg-gray-100 dark:hover:bg-gray-900 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="confirmDelete('{{ $jurusan->id }}')" class="p-2 text-gray-400 hover:text-primary-red hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @else
                                        <span class="text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest mr-2 italic">Akses Terbatas</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-400 font-semibold italic">
                                Tidak ada data jurusan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards) -->
        <div class="block md:hidden space-y-4">
            @forelse($jurusans as $jurusan)
                <div class="p-5 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">TEFA {{ $jurusan->name }}</h3>
                            <p class="text-[10px] text-gray-400 font-semibold mt-1">ID: {{ $jurusan->id }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            @if(session('active_role_name') !== 'pengelola_jurusan' || !is_null($jurusan->parent_id))
                                <button wire:click="openEditModal('{{ $jurusan->id }}')" class="p-2 text-gray-400 hover:text-primary-blue dark:hover:text-primary-yellow hover:bg-white dark:hover:bg-gray-800 rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="confirmDelete('{{ $jurusan->id }}')" class="p-2 text-gray-400 hover:text-primary-red hover:bg-white dark:hover:bg-gray-800 rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @else
                                <span class="text-[9px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest mr-1 italic">Terbatas</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 font-semibold italic">
                    Tidak ada data jurusan yang ditemukan.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $jurusans->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal with Smooth Alpine Transition -->
    <div x-data="{ show: @entangle('showModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <!-- Backdrop -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showModal', false)"></div>

        <!-- Content -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">
                    {{ $jurusanId ? 'Edit Sub-Unit' : 'Tambah Sub-Unit' }}
                </h2>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveJurusan" class="space-y-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama Sub-Unit / TEFA</label>
                    <input wire:model="name" type="text" required placeholder="Contoh: Angkringan Doku" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
                    @error('name') <span class="text-xs text-primary-red font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">
                        Unit Induk (Parent TEFA) {!! session('active_role_name') === 'pengelola_jurusan' ? '<span class="text-primary-red">* Wajib</span>' : '- Opsional' !!}
                    </label>
                    <select wire:model="parent_id" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white focus:outline-none transition-all text-sm">
                        @if(session('active_role_name') !== 'pengelola_jurusan')
                            <option value="">-- Tanpa Induk (Unit Utama / Jurusan) --</option>
                        @else
                            <option value="">-- Pilih Unit Induk / Jurusan --</option>
                        @endif
                        @foreach($parentOptions as $parentOpt)
                            <option value="{{ $parentOpt->id }}">TEFA {{ $parentOpt->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <span class="text-xs text-primary-red font-bold mt-1 block">{{ $message }}</span> @enderror
                    <p class="text-[9px] text-gray-400 font-semibold mt-2 ml-1">Hubungkan unit ini sebagai sub-unit usaha di bawah jurusan induk (contoh: Angkringan Doku di bawah RPL).</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                    <button type="button" wire:click="$set('showModal', false)" class="px-6 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-4 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all shadow-xl shadow-blue-900/10">
                        Simpan Jurusan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal with Smooth Alpine Transition -->
    <div x-data="{ showDelete: @entangle('showDeleteModal') }" x-show="showDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <!-- Backdrop -->
        <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showDeleteModal', false)"></div>

        <!-- Content -->
        <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-50 dark:bg-red-950/30 text-primary-red rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">Hapus Jurusan</h2>
                <p class="text-gray-400 text-sm font-medium mt-2">Apakah Anda yakin ingin menghapus jurusan ini secara permanen? Data hak akses user yang berkaitan dengan jurusan ini juga akan terhapus.</p>
            </div>

            <div class="flex justify-center gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteJurusan" class="flex-1 py-4 bg-primary-red hover:bg-red-700 text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all shadow-xl shadow-red-500/10">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
