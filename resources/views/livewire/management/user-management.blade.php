<div class="space-y-8 pt-6" x-data="{
    exportUser(name, email, role, initials) {
        this.$refs.cardName.innerText = name;
        this.$refs.cardEmail.innerText = email;
        this.$refs.cardRole.innerText = role;
        this.$refs.cardInitials.innerText = initials;
        
        const target = this.$refs.exportTarget;
        target.style.display = 'block';
        
        setTimeout(() => {
            html2canvas(target, {
                useCORS: true,
                backgroundColor: null,
                scale: 2
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'TEFA-CREDENTIALS-' + name.replace(/\s+/g, '-').toUpperCase() + '.png';
                link.href = canvas.toDataURL();
                link.click();
                target.style.display = 'none';
            });
        }, 100);
    },
    async exportAllCards(filterType) {
        const users = await this.$wire.getAllUsersForExport();
        const filteredUsers = users.filter(u => {
            if (filterType === 'all') return true;
            if (filterType === 'kasir') return u.isKasir;
            return false;
        });

        if (filteredUsers.length === 0) {
            this.$dispatch('toast', { message: 'Tidak ada data pengguna yang cocok.' });
            return;
        }

        const target = this.$refs.exportTarget;
        target.style.display = 'block';

        this.$dispatch('toast', { message: 'Mulai mengunduh ' + filteredUsers.length + ' kartu akses...' });

        for (const u of filteredUsers) {
            this.$refs.cardName.innerText = u.name;
            this.$refs.cardEmail.innerText = u.email;
            this.$refs.cardRole.innerText = u.role;
            this.$refs.cardInitials.innerText = u.initials;

            // Wait a tiny bit for render
            await new Promise(resolve => setTimeout(resolve, 100));

            const canvas = await html2canvas(target, {
                useCORS: true,
                backgroundColor: null,
                scale: 2
            });

            const link = document.createElement('a');
            link.download = 'TEFA-CREDENTIALS-' + u.name.replace(/\s+/g, '-').toUpperCase() + '.png';
            link.href = canvas.toDataURL();
            link.click();
            
            // Interval to prevent browser download locks
            await new Promise(resolve => setTimeout(resolve, 400));
        }

        target.style.display = 'none';
        this.$dispatch('toast', { message: 'Selesai! Semua kartu berhasil diunduh.' });
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Manajemen User</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola Pengguna & Hak Akses TEFA</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="downloadTemplate" class="inline-flex items-center px-5 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Template Excel
            </button>

            <button wire:click="$set('showImportModal', true)" class="inline-flex items-center px-5 py-3.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </button>

            <!-- Export Cards Dropdown -->
            <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                <button @click="openExport = !openExport" class="inline-flex items-center px-5 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-emerald-950/10 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Kartu
                </button>
                <div x-show="openExport" x-cloak
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-2 z-50 transform origin-top-right">
                    <button @click="openExport = false; exportAllCards('all')" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black uppercase tracking-wider italic text-gray-500 dark:text-gray-300 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all text-left">
                        Semua Akun
                    </button>
                    <button @click="openExport = false; exportAllCards('kasir')" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black uppercase tracking-wider italic text-gray-500 dark:text-gray-300 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all text-left border-t border-gray-50 dark:border-gray-700/50">
                        Hanya Kasir
                    </button>
                </div>
            </div>

            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna
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
                <input wire:model.live="search" type="text" placeholder="Cari nama atau email pengguna..." class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">Pengguna</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Email</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Akses Unit TEFA / Role</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($users as $user)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-5 pl-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md">
                                        {{ $user->initials() }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-bold text-gray-800 dark:text-white group-hover:text-primary-blue dark:group-hover:text-primary-yellow transition-colors">{{ $user->name }}</h3>
                                        <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">ID: {{ substr($user->id, 0, 8) }}...</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 text-sm text-gray-500 dark:text-gray-400 font-medium">
                                {{ $user->email }}
                            </td>
                            <td class="py-5">
                                <div class="flex flex-wrap gap-2 max-w-xl">
                                    @php
                                        $userAccesses = $user->getAvailableAccesses();
                                    @endphp
                                    @forelse($userAccesses as $acc)
                                        @php
                                            $color = 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
                                            if ($acc->role_name === 'superadmin') {
                                                $color = 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400';
                                            } elseif ($acc->role_name === 'pengelola_jurusan') {
                                                $color = 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
                                            } elseif ($acc->role_name === 'kasir') {
                                                $color = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-black rounded-full uppercase tracking-wider {{ $color }}">
                                            {{ $acc->role_label }} 
                                            @if($acc->jurusan_name)
                                                <span class="mx-1 text-gray-400">•</span> {{ $acc->jurusan_name }}
                                            @endif
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Belum ada hak akses</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="py-5 text-right pr-4">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="exportUser('{{ $user->name }}', '{{ $user->email }}', '{{ count($userAccesses) > 0 ? $userAccesses[0]->role_label : 'User' }}', '{{ $user->initials() }}')" class="p-2.5 text-gray-400 hover:text-emerald-500 hover:bg-emerald-500/10 rounded-xl transition-all" title="Cetak Kartu Akses">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"></path></svg>
                                    </button>
                                    <button wire:click="openEditModal('{{ $user->id }}')" class="p-2.5 text-gray-400 hover:text-primary-blue dark:hover:text-primary-yellow hover:bg-gray-100 dark:hover:bg-gray-900 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $user->id }}')" class="p-2.5 text-gray-400 hover:text-primary-red hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-all" @if($user->id === auth()->id()) disabled style="opacity: 0.3;" @endif>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 font-semibold italic">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards) -->
        <div class="block md:hidden space-y-4">
            @forelse($users as $user)
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md">
                            {{ $user->initials() }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>

                    <!-- User Access Tags on Mobile -->
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Akses Unit / Role:</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $userAccesses = $user->getAvailableAccesses();
                            @endphp
                            @forelse($userAccesses as $acc)
                                @php
                                    $color = 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
                                    if ($acc->role_name === 'superadmin') {
                                        $color = 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400';
                                    } elseif ($acc->role_name === 'pengelola_jurusan') {
                                        $color = 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
                                    } elseif ($acc->role_name === 'kasir') {
                                        $color = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-wider {{ $color }}">
                                    {{ $acc->role_label }} 
                                    @if($acc->jurusan_name)
                                        <span class="mx-1 text-gray-400">•</span> {{ $acc->jurusan_name }}
                                    @endif
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Belum ada hak akses</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-800">
                        <button @click="exportUser('{{ $user->name }}', '{{ $user->email }}', '{{ count($userAccesses) > 0 ? $userAccesses[0]->role_label : 'User' }}', '{{ $user->initials() }}')" class="inline-flex items-center px-4 py-2.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900 rounded-xl font-bold text-xs gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"></path></svg>
                            Export Kartu
                        </button>
                        <button wire:click="openEditModal('{{ $user->id }}')" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-250 dark:border-gray-700 rounded-xl font-bold text-xs gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </button>
                        <button wire:click="confirmDelete('{{ $user->id }}')" class="inline-flex items-center px-4 py-2.5 bg-red-50 hover:bg-red-100 text-primary-red border border-red-200 dark:bg-red-950/30 dark:border-red-900 rounded-xl font-bold text-xs gap-2 transition-all" @if($user->id === auth()->id()) disabled style="opacity: 0.3;" @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 font-semibold italic">
                    Tidak ada data pengguna yang ditemukan.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal with Smooth Alpine Transition -->
    <div x-data="{ show: @entangle('showModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <!-- Backdrop -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showModal', false)"></div>

        <!-- Content -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">
                    {{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                </h2>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveUser" class="space-y-6">
                <!-- Basic Info Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama Lengkap</label>
                        <input wire:model="name" type="text" required placeholder="Contoh: Budi Santoso" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
                        @error('name') <span class="text-xs text-primary-red font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Email</label>
                        <input wire:model="email" type="email" required placeholder="user@gmail.com" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
                        @error('email') <span class="text-xs text-primary-red font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">
                        Password {{ $userId ? '(Kosongkan jika tidak ingin diubah)' : '' }}
                    </label>
                    <input wire:model="password" type="password" placeholder="••••••••" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
                    @error('password') <span class="text-xs text-primary-red font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Access Settings Section -->
                <div class="border-t border-gray-100 dark:border-gray-700/50 pt-6">
                    <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-widest mb-4">Pengaturan Hak Akses</h3>

                    <!-- Add Access Panel -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Pilih Role</label>
                                <div x-data="{ 
                                    open: false, 
                                    selectedLabel: '-- Pilih Role --' 
                                }" 
                                x-init="
                                    $watch('$wire.selectedRoleId', val => {
                                        if(!val) { selectedLabel = '-- Pilih Role --'; return; }
                                        @this.roles.forEach(r => { if(r.id == val) selectedLabel = r.label; });
                                    });
                                    if($wire.selectedRoleId) {
                                        @this.roles.forEach(r => { if(r.id == $wire.selectedRoleId) selectedLabel = r.label; });
                                    }
                                "
                                class="relative">
                                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 transition-all focus:ring-2 focus:ring-primary-blue">
                                        <span x-text="selectedLabel"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-48 overflow-y-auto p-1.5">
                                        <button type="button" @click="$wire.set('selectedRoleId', ''); selectedLabel = '-- Pilih Role --'; open = false" class="w-full text-left px-3 py-2 text-xs font-semibold text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                                            -- Pilih Role --
                                        </button>
                                        @foreach($roles as $role)
                                            <button type="button" @click="$wire.set('selectedRoleId', '{{ $role->id }}'); selectedLabel = '{{ $role->label }}'; open = false" class="w-full text-left px-3 py-2 text-sm font-bold text-gray-750 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-primary-blue dark:hover:text-primary-yellow rounded-lg transition-colors">
                                                {{ $role->label }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Pilih Jurusan / TEFA</label>
                                <div x-data="{ 
                                    open: false, 
                                    selectedLabel: 'Superadmin / Global' 
                                }" 
                                x-init="
                                    $watch('$wire.selectedJurusanId', val => {
                                        if(!val) { selectedLabel = 'Superadmin / Global'; return; }
                                        @this.jurusans.forEach(j => { if(j.id == val) selectedLabel = j.name; });
                                    });
                                    if($wire.selectedJurusanId) {
                                        @this.jurusans.forEach(j => { if(j.id == $wire.selectedJurusanId) selectedLabel = j.name; });
                                    }
                                "
                                class="relative">
                                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 transition-all focus:ring-2 focus:ring-primary-blue">
                                        <span x-text="selectedLabel"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-48 overflow-y-auto p-1.5">
                                        <button type="button" @click="$wire.set('selectedJurusanId', ''); selectedLabel = 'Superadmin / Global'; open = false" class="w-full text-left px-3 py-2 text-sm font-bold text-gray-750 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-primary-blue dark:hover:text-primary-yellow rounded-lg transition-colors">
                                            Superadmin / Global
                                        </button>
                                        @foreach($jurusans as $jur)
                                            <button type="button" @click="$wire.set('selectedJurusanId', '{{ $jur->id }}'); selectedLabel = '{{ $jur->name }}'; open = false" class="w-full text-left px-3 py-2 text-sm font-bold text-gray-750 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-primary-blue dark:hover:text-primary-yellow rounded-lg transition-colors">
                                                {{ $jur->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="button" wire:click="addAccess" class="w-full py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                                    Tambah Akses
                                </button>
                            </div>
                        </div>
                        @error('selectedRoleId') <span class="text-xs text-primary-red font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Active Access List -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Akses yang Diberikan</label>
                        @if(count($assignedAccesses) > 0)
                            <div class="divide-y divide-gray-100 dark:divide-gray-700 bg-gray-50/50 dark:bg-gray-900/30 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700/50">
                                @foreach($assignedAccesses as $index => $acc)
                                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 rounded uppercase">
                                                {{ $acc['role_label'] }}
                                            </span>
                                            <span class="text-gray-400">di</span>
                                            <span class="font-bold text-gray-700 dark:text-gray-300">
                                                {{ $acc['jurusan_name'] }}
                                            </span>
                                        </div>
                                        <button type="button" wire:click="removeAccess({{ $index }})" class="text-primary-red hover:text-red-700 font-bold text-xs uppercase tracking-wider">
                                            Hapus
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-xs text-gray-400 italic p-4 bg-gray-50 dark:bg-gray-900/30 rounded-2xl text-center">
                                Belum ada hak akses yang ditambahkan. Gunakan panel di atas untuk menambahkan.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                    <button type="button" wire:click="$set('showModal', false)" class="px-6 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-4 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all shadow-xl shadow-blue-900/10">
                        Simpan Perubahan
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
        <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 animate-fade-in-up">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-50 dark:bg-red-950/30 text-primary-red rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">Hapus Pengguna</h2>
                <p class="text-gray-400 text-sm font-medium mt-2">Apakah Anda yakin ingin menghapus akun pengguna ini secara permanen? Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <div class="flex justify-center gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteUser" class="flex-1 py-4 bg-primary-red hover:bg-red-700 text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all shadow-xl shadow-red-500/10">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div x-data="{ showImport: @entangle('showImportModal') }" x-show="showImport" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="showImport" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showImportModal', false)"></div>
        <div x-show="showImport" x-transition.scale class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <h2 class="text-2xl font-black text-gray-855 dark:text-white uppercase italic mb-6">Import User dari Excel</h2>
            
            <form wire:submit.prevent="importExcel" class="space-y-5">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Pilih File Excel (.xlsx, .xls)</label>
                    <input type="file" wire:model="excelFile" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('excelFile') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="text-[10px] text-gray-400 font-semibold leading-relaxed">
                    * Pastikan format kolom file Excel Anda sesuai template:<br>
                    <strong>A: Nama Lengkap</strong>, 
                    <strong>B: Email</strong>, 
                    <strong>C: Role (kasir)</strong>, 
                    <strong>D: Jurusan (contoh: RPL)</strong>.<br>
                    <span class="text-amber-500 font-bold">* Password secara otomatis diset default menjadi '00000000' (nol 8 kali) untuk semua user yang diimport.</span>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showImportModal', false)" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Hidden Card Template for Export -->
    <div x-ref="exportTarget" style="display: none;" class="fixed -top-[9999px] -left-[9999px] opacity-0 pointer-events-none z-0">
        <div class="w-[400px] h-[250px] p-6 rounded-[2rem] text-white shadow-2xl relative overflow-hidden flex flex-col justify-between" 
             style="background: linear-gradient(135deg, #0f172a, #0b1329); border: 4px solid #2563eb; font-family: 'Outfit', sans-serif; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
            <!-- Card Pattern/Accents -->
            <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full blur-xl" style="background: rgba(37, 99, 235, 0.15); position: absolute; right: -64px; top: -64px; width: 128px; height: 128px; border-radius: 9999px; filter: blur(24px);"></div>
            <div class="absolute -left-16 -bottom-16 w-32 h-32 rounded-full blur-xl" style="background: rgba(239, 68, 68, 0.08); position: absolute; left: -64px; bottom: -64px; width: 128px; height: 128px; border-radius: 9999px; filter: blur(24px);"></div>
            
            <!-- Header -->
            <div class="flex items-center justify-between pb-4" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px;">
                <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 12px;">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black italic text-xs shadow-md" style="background: #2563eb; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; font-weight: 900; font-style: italic;">
                        LA
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span class="block text-[10px] font-black uppercase tracking-wider" style="color: #2563eb; display: block; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em;">LabAntik Kasir</span>
                        <span class="block text-[6px] font-black uppercase tracking-widest" style="color: #6b7280; display: block; font-size: 6px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em;">Digital Credentials</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[6px] font-black uppercase tracking-widest" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 6px; padding: 4px 10px; font-weight: 900; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.1em;">
                    ACTIVE MEMBER
                </span>
            </div>
            
            <!-- Body -->
            <div class="flex items-center gap-4 my-auto" style="display: flex; align-items: center; gap: 16px; margin-top: auto; margin-bottom: auto;">
                <!-- Initials Avatar -->
                <div x-ref="cardInitials" class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg" style="background: linear-gradient(135deg, #3b82f6, #4f46e5); border: 2px solid rgba(255, 255, 255, 0.2); width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 900; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);">
                    JD
                </div>
                <div style="display: flex; flex-direction: column;">
                    <h2 x-ref="cardName" class="text-lg font-black tracking-tight leading-tight uppercase italic" style="color: #ffffff; font-size: 18px; font-weight: 900; margin: 0; font-style: italic; letter-spacing: -0.025em; text-transform: uppercase;">John Doe</h2>
                    <p x-ref="cardEmail" class="text-xs font-semibold mt-1" style="color: #94a3b8; font-size: 12px; margin-top: 4px; margin-bottom: 0;">johndoe@example.com</p>
                    <div class="mt-2" style="margin-top: 8px;">
                        <span x-ref="cardRole" class="inline-flex px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest" style="background: rgba(255, 255, 255, 0.1); color: #fbbf24; border: 1px solid rgba(255, 255, 255, 0.05); font-size: 8px; font-weight: 900; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.1em;">
                            KASIR
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-between pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between; padding-top: 12px;">
                <div class="flex flex-col" style="display: flex; flex-direction: column;">
                    <span class="text-[5px] font-bold uppercase tracking-widest" style="color: #6b7280; font-size: 5px; text-transform: uppercase; letter-spacing: 0.1em;">Verification ID</span>
                    <span class="text-[8px] font-black tracking-wider font-mono" style="color: #2563eb; font-size: 8px; font-family: monospace; font-weight: 900; letter-spacing: 0.05em;">TEFA-SECURE-ID</span>
                </div>
                <!-- Mock Barcode -->
                <div class="flex items-center gap-0.5 opacity-40" style="display: flex; gap: 2px; opacity: 0.4;">
                    <div style="background: #ffffff; width: 2px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 4px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 2px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 6px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 2px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 2px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 4px; height: 24px;"></div>
                    <div style="background: #ffffff; width: 2px; height: 24px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- html2canvas dependency -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</div>
