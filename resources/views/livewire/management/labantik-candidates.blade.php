<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">
                Data Calon Labantik
            </h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">
                Daftar registrasi calon anggota baru Labantik
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if ($isSuperAdmin)
                <div class="w-64">
                    <select wire:model.live="selectedJurusanId"
                        class="w-full px-4 py-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-blue">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach ($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button wire:click="toggleRegistration"
                class="inline-flex items-center px-5 py-3.5 {{ $isRegistrationOpen ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }} text-white rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                @if ($isRegistrationOpen)
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Pendaftaran: BUKA
                @else
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Pendaftaran: TUTUP
                @endif
            </button>

            <button wire:click="$set('showWaLinkModal', true)"
                class="inline-flex items-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                Grup WA
            </button>

            <button wire:click="exportExcel"
                class="inline-flex items-center px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel (.xlsx)
            </button>
        </div>
    </div>

    <!-- Filters & Table Card -->
    <div x-data="{ selectedCandidate: null, showModal: false }"
        class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
        
        <!-- Search bar -->
        <div class="mb-6 max-w-md w-full">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau kelas..."
                    class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm font-semibold">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">No</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Nama Lengkap</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Kelas</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Jurusan</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">No HP</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Penyakit Bawaan</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Grup WA</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($candidates as $index => $candidate)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4 text-sm font-bold text-gray-800 dark:text-white">
                                {{ $candidates->firstItem() + $index }}
                            </td>
                            <td class="py-4 text-sm font-bold text-gray-900 dark:text-white">
                                {{ $candidate->full_name }}
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-bold uppercase">
                                {{ $candidate->class_name }}
                            </td>
                            <td class="py-4 text-sm text-gray-500 dark:text-gray-400 font-bold">
                                {{ $candidate->jurusan ? $candidate->jurusan->name : 'Global' }}
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                {{ $candidate->phone_number }}
                            </td>
                            <td class="py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if ($candidate->illness_history)
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 dark:bg-red-950/20 dark:text-red-400 rounded-full text-[10px] font-black uppercase">
                                        {{ $candidate->illness_history }}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-semibold italic text-xs">Tidak ada</span>
                                @endif
                            </td>
                            <td class="py-4 text-sm font-semibold">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                        wire:click="toggleJoinedGroup('{{ $candidate->id }}')"
                                        {{ $candidate->is_joined_group ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                                    <span class="ml-2 text-xs font-bold {{ $candidate->is_joined_group ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">
                                        {{ $candidate->is_joined_group ? 'Sudah' : 'Belum' }}
                                    </span>
                                </label>
                            </td>
                            <td class="py-4 text-right pr-4 flex items-center justify-end gap-2">
                                <button type="button"
                                    @click="selectedCandidate = {{ json_encode($candidate) }}; showModal = true"
                                    class="px-4 py-2 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-lg font-black text-xs uppercase tracking-wider transition-all">
                                    Detail
                                </button>
                                <button type="button"
                                    wire:click="confirmDelete('{{ $candidate->id }}')"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-black text-xs uppercase tracking-wider transition-all">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 italic font-semibold">
                                Belum ada pendaftaran calon Labantik
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $candidates->links() }}
        </div>

        <!-- Detail Modal (Alpine.js powered) -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
                @click="showModal = false"></div>
            <div x-show="showModal" x-transition.scale
                class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[85vh] overflow-y-auto">
                
                <template x-if="selectedCandidate">
                    <div>
                        <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6" x-text="selectedCandidate.full_name"></h2>
                        
                        <div class="space-y-6">
                            <!-- Basic details grid -->
                            <div class="grid grid-cols-2 gap-4 border-b border-gray-100 dark:border-gray-700 pb-4">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kelas</div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-white uppercase mt-1" x-text="selectedCandidate.class_name"></div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">Jurusan Tujuan</div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-white mt-1" x-text="selectedCandidate.jurusan ? selectedCandidate.jurusan.name : 'Global'"></div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">No HP Calon</div>
                                    <div class="text-sm font-semibold text-gray-850 dark:text-gray-200 mt-1" x-text="selectedCandidate.phone_number"></div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">No HP Orang Tua</div>
                                    <div class="text-sm font-semibold text-gray-855 dark:text-gray-200 mt-1" x-text="selectedCandidate.parent_phone_number"></div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status Grup WA</div>
                                    <div class="text-sm font-bold mt-1" :class="selectedCandidate.is_joined_group ? 'text-emerald-500' : 'text-amber-500'" x-text="selectedCandidate.is_joined_group ? 'Sudah Masuk' : 'Belum Masuk'"></div>
                                </div>
                            </div>

                            <!-- Illness history -->
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Riwayat Penyakit Bawaan</div>
                                <div class="text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                    <span x-text="selectedCandidate.illness_history ? selectedCandidate.illness_history : 'Tidak ada penyakit bawaan yang dilaporkan.'"
                                        :class="selectedCandidate.illness_history ? 'text-red-500 font-bold' : 'text-gray-500 italic'"></span>
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Alamat Rumah</div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed" x-text="selectedCandidate.address"></div>
                            </div>

                            <!-- Reason -->
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Alasan & Motivasi Masuk Labantik</div>
                                <div class="p-4 bg-gray-55 dark:bg-gray-900 rounded-2xl text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed" x-text="selectedCandidate.reason"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-6 mt-6 border-t border-gray-150 dark:border-gray-700">
                    <button type="button" @click="showModal = false"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg font-black text-xs uppercase transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Link Modal -->
    @if ($showWaLinkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 animate-in zoom-in-95 duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">
                        Link Grup WhatsApp
                    </h3>
                    <button wire:click="$set('showWaLinkModal', false)" class="p-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveWaLink" class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Input Link Grup WhatsApp</label>
                        <input type="text" wire:model="waGroupLink" placeholder="https://chat.whatsapp.com/..."
                            class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white">
                        @error('waGroupLink') <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span> @enderror
                        <p class="text-[10px] text-gray-400 font-medium mt-2 ml-1">
                            Link ini akan ditampilkan kepada calon pendaftar setelah mereka berhasil mengirimkan formulir pendaftaran.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="$set('showWaLinkModal', false)"
                            class="px-6 py-3.5 bg-gray-100 dark:bg-gray-900 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-2xl font-black italic uppercase text-xs tracking-widest transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3.5 bg-primary-blue text-white rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all font-black italic uppercase text-xs tracking-widest">
                            Simpan Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal with Smooth Alpine Transition -->
    <div x-data="{ showDelete: @entangle('showDeleteModal') }" x-show="showDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <!-- Backdrop -->
        <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showDeleteModal', false)"></div>

        <!-- Content -->
        <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-50 dark:bg-red-950/30 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-855 dark:text-white uppercase italic tracking-tight">Hapus Calon Anggota</h2>
                <p class="text-gray-400 text-sm font-medium mt-2">Apakah Anda yakin ingin menghapus data calon anggota ini secara permanen? Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <div class="flex justify-center gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteCandidate" class="flex-1 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all shadow-xl shadow-red-500/10">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
