<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">API Key TEFA</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola Kunci Akses Integrasi Aplikasi (Dompet Siswa, dll)</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95 cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Generate API Key Baru
            </button>
        </div>
    </div>

    <!-- Header -->

    <!-- Config Dompet Siswa API Key Card -->
    <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20 rounded-3xl p-6 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-black shadow-lg shadow-amber-500/30">
                    DS
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white uppercase tracking-tight">API Key Dompet Siswa (Koneksi Outbound)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                        API Key resmi dari server Dompet Siswa SMKN 1 Talaga (<code class="font-mono bg-amber-100 dark:bg-gray-800 text-amber-700 dark:text-amber-300 px-1.5 py-0.5 rounded">ds_live_...</code>) untuk menarik transaksi real-time secara otomatis.
                    </p>
                </div>
            </div>
            <form wire:submit.prevent="saveDompetSiswaApiKey" class="flex items-center gap-3 w-full lg:w-auto">
                <div class="relative flex-1 lg:w-80">
                    <input wire:model="dompetSiswaApiKey" type="text" placeholder="ds_live_..." class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl font-mono text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
                <button type="submit" class="px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-xs uppercase tracking-wider transition-all cursor-pointer shadow-md shrink-0">
                    Simpan Key
                </button>
            </form>
        </div>
    </div>

    <!-- Controls Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-2xl shadow-blue-950/5 border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari nama aplikasi/key..." class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm font-medium">
            </div>
        </div>
    </div>

    <!-- API Keys Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl shadow-blue-950/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 dark:bg-gray-900/30">
                        <th class="py-4 px-6">Nama Aplikasi / Klien</th>
                        <th class="py-4 px-6">API Key</th>
                        <th class="py-4 px-6">Scope / Merchant</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Terakhir Digunakan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @forelse($apiKeys as $key)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                {{ $key->name }}
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-2" x-data="{ copied: false }">
                                    <span class="bg-gray-100 dark:bg-gray-900 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 font-mono tracking-wider max-w-[200px] truncate">
                                        {{ $key->key }}
                                    </span>
                                    <button 
                                        @click="navigator.clipboard.writeText('{{ $key->key }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                        type="button"
                                        class="p-1.5 text-gray-400 hover:text-primary-blue transition-colors cursor-pointer"
                                        title="Salin Key"
                                    >
                                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs font-semibold">
                                @if($key->jurusan)
                                    <span class="inline-flex px-2.5 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-lg font-bold">
                                        {{ $key->jurusan->name }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 rounded-lg font-bold">
                                        Global (Semua Merchant)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <button wire:click="toggleActive('{{ $key->id }}')" type="button" class="cursor-pointer">
                                    @if($key->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                            ● Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                                            ○ Non-Aktif
                                        </span>
                                    @endif
                                </button>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-500 dark:text-gray-400">
                                {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Belum Pernah' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button wire:click="deleteKey('{{ $key->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus API Key ini?" class="p-2 text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 transition-colors cursor-pointer" title="Hapus Key">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <p class="font-bold text-base">Belum Ada API Key</p>
                                <p class="text-xs mt-1">Klik tombol "Generate API Key Baru" untuk membuat kunci akses pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $apiKeys->links() }}
        </div>
    </div>

    <!-- Modal Generate API Key -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-lg p-6 sm:p-8 space-y-6">
                
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Generate API Key Baru</h3>
                    <button wire:click="closeModal" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                @if($newlyGeneratedKey)
                    <!-- Newly Generated Key Screen -->
                    <div class="space-y-4 p-5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-slate-900 dark:text-white">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>API Key Berhasil Dibuat!</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Silakan salin API Key di bawah ini. Masukkan nilai ini pada header <code class="font-mono bg-gray-200 dark:bg-gray-900 px-1 py-0.5 rounded">X-API-Key</code> saat melakukan request dari Dompet Siswa.</p>
                        
                        <div class="flex items-center gap-2 bg-white dark:bg-gray-900 p-3 rounded-xl border border-gray-200 dark:border-gray-700 font-mono text-xs break-all" x-data="{ copied: false }">
                            <span class="flex-1 font-bold">{{ $newlyGeneratedKey }}</span>
                            <button 
                                @click="navigator.clipboard.writeText('{{ $newlyGeneratedKey }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                type="button"
                                class="px-3 py-1.5 bg-primary-blue text-white rounded-lg text-xs font-bold shrink-0 cursor-pointer"
                            >
                                <span x-show="!copied">Salin</span>
                                <span x-show="copied" x-cloak>Tersalin!</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button wire:click="closeModal" type="button" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-slate-900 dark:text-white rounded-2xl font-bold text-sm">
                            Selesai & Tutup
                        </button>
                    </div>
                @else
                    <!-- Input Form -->
                    <form wire:submit.prevent="generateKey" class="space-y-5">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nama Aplikasi / Klien</label>
                            <input wire:model="name" type="text" placeholder="Contoh: Aplikasi Dompet Siswa" class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-primary-blue text-sm dark:text-white font-medium">
                            @error('name') <p class="text-xs text-rose-500 mt-1.5 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button wire:click="closeModal" type="button" class="px-5 py-3.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-bold text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-3.5 bg-primary-blue text-primary-yellow rounded-2xl font-black text-sm uppercase italic tracking-wider shadow-lg">
                                Generate Key
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    @endif
</div>
