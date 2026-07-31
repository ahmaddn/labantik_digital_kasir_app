<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Tugas Harian Kasir</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola tugas operasional untuk kasir</p>
        </div>
        <div class="flex items-center gap-3">
            @if(session('active_role_name') === 'superadmin')
                <div class="w-64">
                    <select wire:model.live="selectedJurusanId" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Tugas Baru
            </button>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari nama tugas..." class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">Tanggal</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Tugas</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Ditugaskan Ke</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Status & Laporan</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Bukti Tugas</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($tasks as $task)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4 text-sm font-bold text-gray-800 dark:text-white">
                                {{ $task->date->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4">
                                <div class="font-bold text-gray-855 dark:text-gray-200">{{ $task->task_name }}</div>
                                @if($task->description)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $task->description }}</div>
                                @endif
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                {{ $task->user->name }}
                            </td>
                            <td class="py-4">
                                @if($task->approval_status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                                        Disetujui
                                    </span>
                                    <div class="text-[9px] text-gray-400 mt-1 font-bold">ACC: {{ $task->reviewed_at?->format('d/m/Y H:i') }} WIB{{ $task->reviewer ? ' oleh ' . $task->reviewer->name : '' }}</div>
                                @elseif($task->approval_status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                        Ditolak — Revisi
                                    </span>
                                    @if($task->rejection_note)
                                        <div class="text-[10px] font-bold text-red-500 bg-red-50 dark:bg-red-950/30 p-2 rounded-lg mt-2 max-w-xs break-words">
                                            Catatan: {{ $task->rejection_note }}
                                        </div>
                                    @endif
                                @elseif($task->approval_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                                        Menunggu ACC
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400">
                                        Belum Dikerjakan
                                    </span>
                                @endif

                                @if($task->completed_at)
                                    <div class="text-[9px] text-gray-400 mt-1 font-bold">Dikirim: {{ $task->completed_at?->format('d/m/Y H:i') }} WIB</div>
                                @endif
                                @if($task->completion_report)
                                    <div class="text-xs font-semibold bg-gray-50 dark:bg-gray-900 p-2 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg mt-2 text-slate-700 dark:text-slate-300 max-w-xs break-words">
                                        Laporan: {{ $task->completion_report }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4">
                                @if($task->proof_image)
                                    <a href="{{ asset('storage/' . $task->proof_image) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-black text-primary-blue hover:text-blue-900 transition-colors uppercase italic">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Lihat Foto Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada bukti</span>
                                @endif
                            </td>
                            <td class="py-4 text-right pr-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($task->approval_status === 'pending')
                                        <button wire:click="approveTask('{{ $task->id }}')" wire:loading.attr="disabled" title="Setujui (ACC) tugas" class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-emerald-500/20">
                                            ACC
                                        </button>
                                        <button wire:click="openRejectModal('{{ $task->id }}')" title="Tolak & minta revisi" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-red-500/20">
                                            Tolak
                                        </button>
                                    @endif
                                    <button wire:click="confirmDelete('{{ $task->id }}')" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 italic font-semibold">Belum ada tugas ditambahkan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showCreateModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 animate-fade-in">
            <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">Tambah Tugas Harian</h2>

            <form wire:submit.prevent="saveTask" class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Ditugaskan Ke</label>
                    <select wire:model="assignedTo" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                        <option value="">-- Pilih Kasir --</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('assignedTo') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Tanggal</label>
                    <input type="date" wire:model="date" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('date') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama Tugas</label>
                    <input type="text" wire:model="taskName" placeholder="Contoh: Bersihkan meja pos kasir" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('taskName') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Deskripsi Tambahan</label>
                    <textarea wire:model="description" placeholder="Instruksi tambahan jika ada..." rows="3" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm"></textarea>
                    @error('description') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Kirim Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showDeleteModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-center">
            <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic mb-4">Hapus Tugas?</h2>
            <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus tugas harian ini?</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-3 bg-gray-105 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteTask" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-data="{ show: @entangle('showRejectModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showRejectModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic mb-2">Tolak Laporan Tugas</h2>
            <p class="text-gray-400 text-sm mb-6">Tugas akan dikembalikan ke kasir untuk direvisi. Berikan catatan agar kasir tahu apa yang harus diperbaiki.</p>

            <form wire:submit.prevent="rejectTask" class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Catatan Penolakan</label>
                    <textarea wire:model="rejectionNote" placeholder="Contoh: Foto bukti kurang jelas, mohon foto ulang..." rows="3" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-red-500 dark:text-white text-sm"></textarea>
                    @error('rejectionNote') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Tolak & Minta Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
