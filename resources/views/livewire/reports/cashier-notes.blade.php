<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Catatan Kasir & Pengelola</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Komunikasi Internal & Diskusi Shift Kasir</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4">
            <!-- Search -->
            <div class="w-full sm:w-72">
                <div class="flex items-center bg-white dark:bg-gray-800 px-5 py-3.5 rounded-2xl shadow-lg shadow-blue-900/5 border border-gray-100 dark:border-gray-700/60">
                    <svg class="w-4 h-4 text-gray-400 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari catatan..." class="w-full border-none p-0 focus:ring-0 text-sm bg-transparent dark:text-white placeholder-gray-400 font-bold">
                </div>
            </div>

            <!-- Create Note Button -->
            <button wire:click="openCreateModal" class="w-full sm:w-auto px-6 py-3.5 bg-primary-blue text-white rounded-2xl shadow-xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all font-black italic uppercase text-xs tracking-widest flex items-center justify-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Catatan Baru
            </button>
        </div>
    </div>

    <!-- Notes Grid / Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($notes as $note)
            @php
                $color = $note->color ?? 'default';
                
                $cardStyles = [
                    'default' => 'bg-white dark:bg-gray-800/90 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100',
                    'blue' => 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/80 dark:border-blue-800/60 text-blue-950 dark:text-blue-100',
                    'emerald' => 'bg-emerald-50/70 dark:bg-emerald-950/40 border-emerald-200/80 dark:border-emerald-800/60 text-emerald-950 dark:text-emerald-100',
                    'amber' => 'bg-amber-50/70 dark:bg-amber-950/40 border-amber-200/80 dark:border-amber-800/60 text-amber-950 dark:text-amber-100',
                    'rose' => 'bg-rose-50/70 dark:bg-rose-950/40 border-rose-200/80 dark:border-rose-800/60 text-rose-950 dark:text-rose-100',
                    'purple' => 'bg-purple-50/70 dark:bg-purple-950/40 border-purple-200/80 dark:border-purple-800/60 text-purple-950 dark:text-purple-100',
                ][$color] ?? 'bg-white dark:bg-gray-800/90 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100';

                $badgeStyles = [
                    'default' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                    'blue' => 'bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-200',
                    'emerald' => 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200',
                    'amber' => 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200',
                    'rose' => 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200',
                    'purple' => 'bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-200',
                ][$color] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200';
            @endphp

            <div class="rounded-3xl p-6 border-2 shadow-xl shadow-black/5 flex flex-col justify-between transition-all hover:-translate-y-1 relative group {{ $cardStyles }} {{ $note->is_pinned ? 'ring-2 ring-primary-blue dark:ring-blue-400' : '' }}">
                <div>
                    <!-- Badges Row: Pin & Target User -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($note->is_pinned)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-blue/10 dark:bg-blue-400/20 text-primary-blue dark:text-blue-300 text-[10px] font-black uppercase tracking-widest border border-primary-blue/20">
                                <svg class="w-3 h-3 rotate-45" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                                Disematkan
                            </div>
                        @endif

                        @if($note->target_user_id)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-purple-500/10 dark:bg-purple-400/20 text-purple-700 dark:text-purple-300 text-[10px] font-black uppercase tracking-widest border border-purple-500/20" title="Catatan Khusus">
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                Kepada: {{ $note->targetUser->name ?? 'User' }}
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gray-500/10 text-gray-500 dark:text-gray-400 text-[9px] font-bold uppercase tracking-wider">
                                Publik / Semua
                            </div>
                        @endif
                    </div>

                    <!-- Header Note: Title & Actions -->
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <h3 class="text-lg font-black uppercase tracking-tight leading-snug">
                            {{ $note->title ?: 'Tanpa Judul' }}
                        </h3>

                        <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                            <!-- Toggle Pin -->
                            <button wire:click="togglePin({{ $note->id }})" title="{{ $note->is_pinned ? 'Lepas Sematan' : 'Sematkan Catatan' }}" class="p-1.5 hover:bg-black/10 dark:hover:bg-white/10 rounded-xl transition-colors {{ $note->is_pinned ? 'text-primary-blue dark:text-blue-400 font-bold' : 'text-gray-400' }}">
                                <svg class="w-4 h-4 {{ $note->is_pinned ? 'rotate-45 fill-current' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            </button>
                            <!-- Edit -->
                            @if($note->user_id === auth()->id() || auth()->user()->hasRole('admin') || auth()->user()->hasRole('pengelola_jurusan'))
                                <button wire:click="openEditModal({{ $note->id }})" title="Edit Catatan" class="p-1.5 hover:bg-black/10 dark:hover:bg-white/10 rounded-xl transition-colors">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $note->id }})" title="Hapus Catatan" class="p-1.5 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl transition-colors">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="whitespace-pre-line text-sm font-medium leading-relaxed opacity-95 mb-6">
                        {{ $note->content }}
                    </div>
                </div>

                <div>
                    <!-- Reply Button & Replies Summary -->
                    <div class="mb-4">
                        <button wire:click="openReplyModal({{ $note->id }})" class="w-full py-2.5 px-4 bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 rounded-2xl flex items-center justify-between text-xs font-bold transition-all">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-blue dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                {{ $note->replies->count() }} Balasan
                            </span>
                            <span class="text-[10px] uppercase tracking-wider font-black text-primary-blue dark:text-blue-300">Buka Diskusi &rarr;</span>
                        </button>
                    </div>

                    <!-- Footer Note Info -->
                    <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between text-[11px] font-bold">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full uppercase tracking-wider text-[9px] font-black {{ $badgeStyles }}">
                                Dari: {{ $note->user->name ?? 'Kasir' }}
                            </span>
                            @if($note->date)
                                <span class="opacity-75">
                                    {{ \Carbon\Carbon::parse($note->date)->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                        <span class="opacity-60 text-[10px]">
                            {{ $note->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border border-gray-100 dark:border-gray-700 shadow-xl">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                <h3 class="text-lg font-black uppercase text-gray-700 dark:text-gray-300 tracking-wider mb-1">Belum Ada Catatan</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Klik tombol 'Catatan Baru' untuk membuat pesan antar kasir atau pengelola.</p>
                <button wire:click="openCreateModal" class="px-6 py-3 bg-primary-blue text-white rounded-2xl shadow-lg font-black italic uppercase text-xs tracking-widest inline-flex items-center gap-2">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Buat Catatan Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notes->hasPages())
        <div class="mt-8">
            {{ $notes->links() }}
        </div>
    @endif

    <!-- Create / Edit Note Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 animate-in zoom-in-95 duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">
                        {{ $noteId ? 'Edit Catatan' : 'Tambah Catatan Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="p-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveNote" class="space-y-5">
                    <!-- Target User Selection -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Tujuan Catatan (Target User)</label>
                        <select wire:model="target_user_id" class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white">
                            <option value="">-- Catatan Umum / Publik (Dapat dilihat semua) --</option>
                            @foreach($usersList as $usr)
                                <option value="{{ $usr->id }}">Khusus untuk: {{ $usr->name }} ({{ $usr->email }})</option>
                            @endforeach
                        </select>
                        @error('target_user_id') <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Judul Catatan (Opsional)</label>
                        <input type="text" wire:model="title" placeholder="Contoh: Pesan untuk Pengelola, Operan Shift Siang..." class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white">
                        @error('title') <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Tanggal Terkait</label>
                        <input type="date" wire:model="date" class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white">
                        @error('date') <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Isi Catatan</label>
                        <textarea wire:model="content" rows="4" placeholder="Tulis rincian pesan, instruksi pengelola, atau pengingat untuk sesama kasir..." class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white"></textarea>
                        @error('content') <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Options: Pinning & Color -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- Pin Checkbox -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Status Sematan</label>
                            <label class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-900 rounded-2xl cursor-pointer">
                                <input type="checkbox" wire:model="is_pinned" class="w-4 h-4 rounded text-primary-blue focus:ring-primary-blue/20">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Sematkan ke atas</span>
                            </label>
                        </div>

                        <!-- Soft Color Picker -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Aksen Warna Card</label>
                            <div class="flex items-center gap-2">
                                @php
                                    $pickerColors = [
                                        'default' => 'bg-gray-200 dark:bg-gray-600',
                                        'blue' => 'bg-blue-400',
                                        'emerald' => 'bg-emerald-400',
                                        'amber' => 'bg-amber-400',
                                        'rose' => 'bg-rose-400',
                                        'purple' => 'bg-purple-400',
                                    ];
                                @endphp
                                @foreach($pickerColors as $c => $bg)
                                    <button type="button" wire:click="$set('color', '{{ $c }}')" class="w-8 h-8 rounded-full {{ $bg }} transition-all flex items-center justify-center {{ $color === $c ? 'ring-4 ring-offset-2 ring-primary-blue dark:ring-offset-gray-800 scale-110' : 'hover:scale-105 opacity-80' }}">
                                        @if($color === $c)
                                            <svg class="w-3.5 h-3.5 text-white dark:text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="$set('showModal', false)" class="px-6 py-3.5 bg-gray-100 dark:bg-gray-900 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-2xl font-black italic uppercase text-xs tracking-widest transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3.5 bg-primary-blue text-white rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all font-black italic uppercase text-xs tracking-widest">
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Replies Discussion Modal -->
    @if($showReplyModal && $activeNote)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 flex flex-col max-h-[85vh] animate-in zoom-in-95 duration-300">
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-primary-blue dark:text-blue-400">Diskusi & Balasan Catatan</span>
                        <h3 class="text-xl font-black uppercase text-gray-800 dark:text-white leading-tight">
                            {{ $activeNote->title ?: 'Tanpa Judul' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showReplyModal', false)" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Main Note Summary Box -->
                <div class="my-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/80 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between text-xs font-bold text-gray-500 mb-1">
                        <span>Dari {{ $activeNote->user->name ?? 'Kasir' }} &bull; {{ $activeNote->created_at->diffForHumans() }}</span>
                        @if($activeNote->target_user_id)
                            <span class="text-purple-600 dark:text-purple-400 uppercase font-black text-[10px]">Khusus: {{ $activeNote->targetUser->name ?? 'User' }}</span>
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $activeNote->content }}</p>
                </div>

                <!-- Replies List (Scrollable) -->
                <div class="flex-1 overflow-y-auto space-y-4 my-2 pr-2">
                    @forelse($activeNote->replies as $reply)
                        <div class="p-4 rounded-2xl {{ $reply->user_id === auth()->id() ? 'bg-primary-blue/10 border border-primary-blue/20 ml-6' : 'bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 mr-6' }}">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase {{ $reply->user_id === auth()->id() ? 'text-primary-blue dark:text-blue-400' : 'text-gray-800 dark:text-gray-200' }}">
                                        {{ $reply->user->name ?? 'User' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-bold">&bull; {{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                @if($reply->user_id === auth()->id() || auth()->user()->hasRole('admin') || auth()->user()->hasRole('pengelola_jurusan'))
                                    <button wire:click="deleteReply({{ $reply->id }})" title="Hapus balasan" class="text-gray-400 hover:text-rose-500">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line font-medium">{{ $reply->content }}</p>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 text-xs font-bold uppercase tracking-wider">
                            Belum ada balasan. Tulis balasan pertama di bawah!
                        </div>
                    @endforelse
                </div>

                <!-- Input Reply Form -->
                <form wire:submit.prevent="addReply" class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <input type="text" wire:model="replyContent" placeholder="Tulis balasan atau pesan..." class="flex-1 px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white">
                    <button type="submit" class="px-6 py-3.5 bg-primary-blue text-white rounded-2xl shadow-lg font-black italic uppercase text-xs tracking-widest hover:scale-105 active:scale-95 transition-all">
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 text-center animate-in zoom-in-95 duration-300">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                <h3 class="text-xl font-black italic uppercase text-gray-800 dark:text-white mb-2">Hapus Catatan Ini?</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Tindakan ini akan menghapus catatan dan semua balasan di dalamnya.</p>
                <div class="flex justify-center space-x-3">
                    <button wire:click="$set('showDeleteModal', false)" class="px-6 py-3.5 bg-gray-100 dark:bg-gray-900 text-gray-500 rounded-2xl font-black italic uppercase text-xs tracking-widest">
                        Batal
                    </button>
                    <button wire:click="deleteNote" class="px-6 py-3.5 bg-rose-500 text-white rounded-2xl shadow-xl hover:scale-105 active:scale-95 font-black italic uppercase text-xs tracking-widest">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
