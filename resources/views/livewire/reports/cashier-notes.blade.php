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

            <div id="note-card-{{ $note->id }}" class="rounded-3xl p-6 border-2 shadow-xl shadow-black/5 flex flex-col justify-between transition-all hover:-translate-y-1 relative group {{ $cardStyles }} {{ $note->is_pinned ? 'ring-2 ring-primary-blue dark:ring-blue-400' : '' }}">
                <div>
                    <!-- Badges Row: Pin & Target User -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($note->is_pinned)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-blue/10 dark:bg-blue-400/20 text-primary-blue dark:text-blue-300 text-[10px] font-black uppercase tracking-widest border border-primary-blue/20">
                                <svg class="w-3 h-3 rotate-45" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                                Disematkan
                            </div>
                        @endif

                        @if($note->target_user_id && $note->target_user_id === $note->user_id)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-gray-500/10 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 text-[10px] font-black uppercase tracking-widest border border-gray-500/20">
                                🔒 Pribadi (Hanya Diri Sendiri)
                            </div>
                        @elseif($note->target_user_id)
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

                        <div data-html2canvas-ignore class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                            <!-- Toggle Pin -->
                            <button wire:click="togglePin({{ $note->id }})" title="{{ $note->is_pinned ? 'Lepas Sematan' : 'Sematkan Catatan' }}" class="p-1.5 hover:bg-black/10 dark:hover:bg-white/10 rounded-xl transition-colors {{ $note->is_pinned ? 'text-primary-blue dark:text-blue-400 font-bold' : 'text-gray-400' }}">
                                <svg class="w-4 h-4 {{ $note->is_pinned ? 'rotate-45 fill-current' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            </button>
                            <!-- Share to WA -->
                            <button class="btn-share-wa p-1.5 hover:bg-black/10 dark:hover:bg-white/10 rounded-xl text-green-600 dark:text-green-400 transition-colors"
                                    title="Kirim Gambar ke WA"
                                    data-id="{{ $note->id }}"
                                    data-title="{{ $note->title ?: 'Tanpa Judul' }}"
                                    data-creator="{{ $note->user->name ?? 'Kasir' }}"
                                    data-date="{{ $note->date ? \Carbon\Carbon::parse($note->date)->translatedFormat('d M Y') : '' }}"
                                    data-content="{{ $note->content }}">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
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

                    <!-- Content (Raw HTML output for Rich Text) -->
                    <div class="prose dark:prose-invert max-w-none text-sm font-medium leading-relaxed opacity-95 mb-6 whitespace-pre-wrap">
                        {!! $note->content !!}
                    </div>
                </div>

                <div>
                    <!-- Reply Button & Replies Summary -->
                    <div data-html2canvas-ignore class="mb-4">
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
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 animate-in zoom-in-95 duration-300 max-h-[90vh] overflow-y-auto">
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
                            <option value="{{ auth()->id() }}">🔒 Pribadi (Hanya Diri Sendiri)</option>
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
                    <!-- Content with Quill Rich Text Editor -->
                    <div wire:ignore 
                         x-data="{ 
                             content: @entangle('content').live,
                             init() {
                                 const initQuill = () => {
                                     if (typeof Quill === 'undefined') {
                                         setTimeout(initQuill, 50);
                                         return;
                                     }
                                     const quill = new Quill(this.$refs.editor, {
                                         theme: 'snow',
                                         placeholder: 'Tulis rincian pesan, instruksi pengelola, atau pengingat untuk sesama kasir...',
                                         modules: {
                                             toolbar: [
                                                 ['bold', 'italic', 'underline'],
                                                 [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                 ['clean']
                                             ]
                                         }
                                     });

                                     // Set initial value
                                     quill.root.innerHTML = this.content || '';

                                     // Listen for text changes inside Quill
                                     quill.on('text-change', () => {
                                         this.content = quill.root.innerHTML;
                                     });

                                     // Watch Alpine state changes to sync Quill
                                     this.$watch('content', value => {
                                         if (quill.root.innerHTML !== value) {
                                             quill.root.innerHTML = value || '';
                                         }
                                     });
                                     
                                     // Listen to specific edit event to reset Quill content
                                     window.addEventListener('quill-update', event => {
                                         quill.root.innerHTML = event.detail.content || '';
                                     });
                                 };
                                 initQuill();
                             }
                         }" 
                         class="relative">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Isi Catatan</label>
                        <style>
                            /* Custom styles to match theme inputs */
                            .ql-toolbar.ql-snow {
                                border-top-left-radius: 1rem !important;
                                border-top-right-radius: 1rem !important;
                                border: 2px solid #e2e8f0 !important;
                                border-bottom: none !important;
                                background-color: #f8fafc;
                                padding: 8px 12px !important;
                            }
                            .ql-container.ql-snow {
                                border-bottom-left-radius: 1rem !important;
                                border-bottom-right-radius: 1rem !important;
                                border: 2px solid #e2e8f0 !important;
                                border-top: none !important;
                                font-family: inherit !important;
                            }
                            .dark .ql-toolbar.ql-snow {
                                background-color: #0f172a !important;
                                border-color: #334155 !important;
                            }
                            .dark .ql-container.ql-snow {
                                background-color: #0f172a !important;
                                border-color: #334155 !important;
                            }
                            .dark .ql-snow .ql-stroke {
                                stroke: #cbd5e1 !important;
                            }
                            .dark .ql-snow .ql-fill {
                                fill: #cbd5e1 !important;
                            }
                            .dark .ql-snow .ql-picker {
                                color: #cbd5e1 !important;
                            }
                            .dark .ql-snow .ql-picker-options {
                                background-color: #0f172a !important;
                                border-color: #334155 !important;
                            }
                        </style>
                        <div class="rounded-2xl overflow-hidden">
                            <div x-ref="editor" class="h-44 text-sm font-bold text-gray-800 dark:text-white" style="border: none;"></div>
                        </div>
                    </div>
                    @error('content') <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span> @enderror

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
                                    <button type="button" wire:click="$set('color', '{{ $c }}')" class="w-8 h-8 rounded-full {{ $bg }} transition-all flex items-center justify-center {{ $this->color === $c ? 'ring-4 ring-offset-2 ring-primary-blue dark:ring-offset-gray-800 scale-110' : 'hover:scale-105 opacity-80' }}">
                                        @if($this->color === $c)
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

    <!-- Premium WhatsApp Modal Overlay -->
    <div id="waModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[9999] hidden items-center justify-center transition-all duration-300">
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 max-w-sm w-[90%] shadow-[0_30px_70px_rgba(0,0,0,0.25)] border-2 border-emerald-500/20 text-center transform scale-95 transition-all duration-300">
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                </svg>
            </div>
            <h3 id="waModalTitle" class="text-xl font-black uppercase tracking-tight text-gray-800 dark:text-white mb-2">CATATAN DISALIN!</h3>
            <p id="waModalDesc" class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6 leading-relaxed">
                Gambar catatan telah disalin ke clipboard.<br><br>
                Silakan lakukan <b>Paste (Ctrl + V)</b> langsung pada room chat WhatsApp untuk mengirim gambar.
            </p>
            <button id="waModalBtn" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black uppercase text-xs tracking-widest py-4 px-6 rounded-2xl transition-all shadow-lg shadow-emerald-500/20">
                Buka WhatsApp
            </button>
        </div>
    </div>
</div>
<script src="{{ asset('js/html2canvas-pro.min.js') }}"></script>
<script>
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-share-wa');
    if (!btn) return;
    
    e.preventDefault();
    
    const noteId = btn.getAttribute('data-id');
    const title = btn.getAttribute('data-title');
    const creator = btn.getAttribute('data-creator');
    const date = btn.getAttribute('data-date');
    const content = btn.getAttribute('data-content');
    
    shareToWhatsApp(noteId, title, creator, date, content);
});

function shareToWhatsApp(noteId, title, creator, date, content) {
    const card = document.getElementById('note-card-' + noteId);
    if (!card) return;
    
    const h2c = window.html2canvas || window.html2canvasPro;
    if (!h2c) {
        window.dispatchEvent(new CustomEvent('toast', { 
            detail: { message: 'Error: Gagal memuat library perekam gambar.', type: 'error' } 
        }));
        return;
    }
    
    document.body.style.cursor = 'wait';

    // Build the WhatsApp message
    let msg = "📢 *CATATAN KASIR / PENGELOLA*\n";
    msg += "📌 *Judul:* " + title + "\n";
    msg += "👤 *Dari:* " + creator + "\n";
    if (date) {
        msg += "📅 *Tanggal:* " + date + "\n";
    }
    // Remove HTML tags for clean WhatsApp plain text
    const cleanContent = content.replace(/<\/?[^>]+(>|$)/g, "");
    msg += "\n" + cleanContent + "\n\n";
    
    // 1. Render card to canvas
    h2c(card, {
        useCORS: true,
        scale: 2,
        backgroundColor: '#1e293b', // Dark background to make the notebook page pop
        onclone: (clonedDoc) => {
            const clonedCard = clonedDoc.getElementById('note-card-' + noteId);
            if (clonedCard) {
                // Style the card container to look like a premium notepad page
                clonedCard.style.width = '520px';
                clonedCard.style.minWidth = '520px';
                clonedCard.style.maxWidth = '520px';
                clonedCard.style.height = 'auto'; // Adjust height to content dynamically
                clonedCard.style.minHeight = 'initial';
                clonedCard.style.display = 'flex';
                clonedCard.style.flexDirection = 'column';
                clonedCard.style.justifyContent = 'flex-start';
                clonedCard.style.gap = '20px';
                clonedCard.style.padding = '45px 35px 35px 75px'; // Balanced padding (extra left padding for holes)
                clonedCard.style.borderRadius = '8px'; // Notepad page corners
                clonedCard.style.backgroundColor = '#fffdf6'; // Warm high-contrast cream paper
                clonedCard.style.border = '1px solid #ebdcb9';
                clonedCard.style.boxShadow = '0 30px 60px rgba(0,0,0,0.35)';
                clonedCard.style.position = 'relative';
                clonedCard.style.color = '#0f172a'; // Clear high-contrast dark text
                clonedCard.style.fontFamily = 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                clonedCard.style.fontSize = '14px';
                clonedCard.style.lineHeight = '1.6';
                
                // Clear ruling lines for maximum text clarity
                clonedCard.style.backgroundImage = 'none';
                
                // Create binder holes container on the left side
                const spiralContainer = clonedDoc.createElement('div');
                spiralContainer.style.position = 'absolute';
                spiralContainer.style.left = '0';
                spiralContainer.style.top = '0';
                spiralContainer.style.bottom = '0';
                spiralContainer.style.width = '50px';
                spiralContainer.style.display = 'flex';
                spiralContainer.style.flexDirection = 'column';
                spiralContainer.style.justifyContent = 'flex-start';
                spiralContainer.style.gap = '24px';
                spiralContainer.style.paddingTop = '45px';
                spiralContainer.style.pointerEvents = 'none';
                
                // Render 12 realistic small round binder holes (dots)
                for (let i = 0; i < 12; i++) {
                    const hole = clonedDoc.createElement('div');
                    hole.style.width = '8px';
                    hole.style.height = '8px';
                    hole.style.backgroundColor = '#1e293b'; // Matches dark preview bg
                    hole.style.borderRadius = '50%';
                    hole.style.border = '1px solid #cbd5e1';
                    hole.style.boxShadow = 'inset 0 1px 2px rgba(0,0,0,0.6)';
                    hole.style.margin = '0 auto';
                    spiralContainer.appendChild(hole);
                }
                clonedCard.appendChild(spiralContainer);
                
                // Create the red vertical notebook margin line (aligned perfectly with holes)
                const marginLine = clonedDoc.createElement('div');
                marginLine.style.position = 'absolute';
                marginLine.style.left = '60px';
                marginLine.style.top = '0';
                marginLine.style.bottom = '0';
                marginLine.style.width = '2px';
                marginLine.style.backgroundColor = 'rgba(239, 68, 68, 0.4)'; // Classic red margin
                marginLine.style.pointerEvents = 'none';
                clonedCard.appendChild(marginLine);

                // Clean up title banner
                const headerBanner = clonedDoc.createElement('div');
                headerBanner.style.marginBottom = '15px';
                headerBanner.style.paddingBottom = '10px';
                headerBanner.style.borderBottom = '2px solid rgba(0,0,0,0.06)';
                headerBanner.style.width = '100%';
                headerBanner.style.fontFamily = 'system-ui, sans-serif';
                headerBanner.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <span style="font-size: 11px; font-weight: 900; color: #ef4444; letter-spacing: 2px; text-transform: uppercase;">★ KASIR MEMO ★</span>
                        <span style="font-size: 10px; font-weight: bold; color: #64748b;">${date || ''}</span>
                    </div>
                `;
                clonedCard.insertBefore(headerBanner, clonedCard.firstChild);
            }
        }
    }).then(canvas => {
        document.body.style.cursor = 'default';
        canvas.toBlob(blob => {
            if (!blob) {
                const waUrl = "https://api.whatsapp.com/send?text=" + encodeURIComponent(msg + "_[Gambar catatan gagal dibuat]_");
                showWaModal('error', waUrl, 'Gagal membuat file gambar dari elemen catatan.');
                return;
            }
            
            // 2. Perform clipboard copy or download
            if (navigator.clipboard && navigator.clipboard.write) {
                const item = new ClipboardItem({ 'image/png': blob });
                navigator.clipboard.write([item]).then(() => {
                    const waUrl = "https://api.whatsapp.com/send?text=" + encodeURIComponent(msg + "_[Gambar catatan sudah disalin ke clipboard. Silakan PASTE (Ctrl+V) langsung pada chat WhatsApp Anda!]_");
                    showWaModal('success', waUrl);
                }).catch(err => {
                    console.error("Clipboard write failed: ", err);
                    triggerDownload(canvas, title);
                    const waUrl = "https://api.whatsapp.com/send?text=" + encodeURIComponent(msg + "_[Gambar catatan diunduh. Silakan lampirkan gambar tersebut saat mengirim pesan ini!]_");
                    showWaModal('warning', waUrl);
                });
            } else {
                triggerDownload(canvas, title);
                const waUrl = "https://api.whatsapp.com/send?text=" + encodeURIComponent(msg + "_[Gambar catatan diunduh. Silakan lampirkan gambar tersebut saat mengirim pesan ini!]_");
                showWaModal('warning', waUrl);
            }
        }, 'image/png');
    }).catch(err => {
        document.body.style.cursor = 'default';
        showWaModal('error', '', err.message || err);
    });

    function triggerDownload(canvas, title) {
        try {
            const link = document.createElement('a');
            link.download = 'catatan-' + title.toLowerCase().replace(/[^a-z0-9]/g, '-') + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        } catch (e) {
            console.error("Download failed: ", e);
        }
    }

    function showWaModal(state, waUrl, errorMsg = '') {
        const modal = document.getElementById('waModal');
        const card = modal.querySelector('.bg-white');
        const titleEl = document.getElementById('waModalTitle');
        const descEl = document.getElementById('waModalDesc');
        const btnEl = document.getElementById('waModalBtn');
        const iconContainer = modal.querySelector('.rounded-full');
        
        // Reset classes
        card.className = "bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 max-w-sm w-[90%] shadow-[0_30px_70px_rgba(0,0,0,0.25)] border-2 text-center transform scale-95 transition-all duration-300";
        btnEl.className = "w-full text-white font-black uppercase text-xs tracking-widest py-4 px-6 rounded-2xl transition-all shadow-lg active:scale-95";
        
        if (state === 'success') {
            card.classList.add('border-emerald-500/20');
            btnEl.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-emerald-500/20');
            titleEl.textContent = 'CATATAN DISALIN!';
            descEl.innerHTML = 'Gambar catatan telah disalin ke clipboard.<br><br>Silakan lakukan <b>Paste (Ctrl + V)</b> langsung pada chat WhatsApp Anda.';
            btnEl.textContent = 'Buka WhatsApp';
            
            iconContainer.className = 'w-16 h-16 bg-emerald-100 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-5';
            iconContainer.innerHTML = `<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>`;
            
            btnEl.onclick = function() {
                window.open(waUrl, '_blank');
                modal.classList.replace('flex', 'hidden');
            };
        } else if (state === 'warning') {
            card.classList.add('border-amber-500/20');
            btnEl.classList.add('bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-500/20');
            titleEl.textContent = 'GAMBAR DIUNDUH!';
            descEl.innerHTML = 'Akses salin dibatasi browser (Gunakan localhost/HTTPS).<br>Gambar telah diunduh ke folder <b>Downloads (Unduhan)</b>.<br><br>Silakan lampirkan file gambar tersebut pada chat WhatsApp Anda.';
            btnEl.textContent = 'Buka WhatsApp';
            
            iconContainer.className = 'w-16 h-16 bg-amber-100 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-5';
            iconContainer.innerHTML = `<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>`;
            
            btnEl.onclick = function() {
                window.open(waUrl, '_blank');
                modal.classList.replace('flex', 'hidden');
            };
        } else if (state === 'error') {
            card.classList.add('border-rose-500/20');
            btnEl.classList.add('bg-rose-600', 'hover:bg-rose-700', 'shadow-rose-500/20');
            titleEl.textContent = 'GAGAL MEREKAM!';
            descEl.innerHTML = 'Gagal memproses gambar catatan:<br><br><span class="text-rose-500 font-mono text-[11px] block bg-rose-50 dark:bg-rose-950/30 p-3 rounded-xl border border-rose-500/10 text-left overflow-x-auto whitespace-pre-wrap max-h-32">' + errorMsg + '</span>';
            btnEl.textContent = 'Tutup';
            
            iconContainer.className = 'w-16 h-16 bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto mb-5';
            iconContainer.innerHTML = `<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>`;
            
            btnEl.onclick = function() {
                modal.classList.replace('flex', 'hidden');
            };
        }
        
        modal.classList.replace('hidden', 'flex');
    }
}
</script>
