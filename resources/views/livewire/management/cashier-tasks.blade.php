<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">
                Tugas Harian Kasir</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola tugas operasional untuk
                kasir</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            @if (session('active_role_name') === 'superadmin')
                <div class="w-full sm:w-64">
                    <select wire:model.live="selectedJurusanId"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach ($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95 w-full sm:w-auto">
                <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Tugas Baru
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto flex-nowrap [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <button wire:click="$set('activeTab', 'active')"
            class="px-6 py-3 font-black text-sm uppercase tracking-wider flex items-center gap-2 shrink-0 {{ $activeTab === 'active' ? 'text-primary-blue dark:text-primary-yellow border-b-2 border-primary-blue dark:border-primary-yellow' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="shrink-0">Daftar Tugas</span>
        </button>
        <button wire:click="$set('activeTab', 'pending_review')"
            class="px-6 py-3 font-black text-sm uppercase tracking-wider flex items-center gap-2 shrink-0 {{ $activeTab === 'pending_review' ? 'text-primary-blue dark:text-primary-yellow border-b-2 border-primary-blue dark:border-primary-yellow' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="shrink-0">Menunggu Review</span>
            @if($pendingReviewCount > 0)
                <span class="px-2 py-0.5 text-[10px] font-black bg-rose-500 text-white rounded-full leading-none shrink-0">{{ $pendingReviewCount }}</span>
            @endif
        </button>
        <button wire:click="$set('activeTab', 'history')"
            class="px-6 py-3 font-black text-sm uppercase tracking-wider flex items-center gap-2 shrink-0 {{ $activeTab === 'history' ? 'text-primary-blue dark:text-primary-yellow border-b-2 border-primary-blue dark:border-primary-yellow' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="shrink-0">Riwayat Tugas</span>
        </button>
    </div>

    <!-- Filters & Table -->
    <div
        class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari nama tugas..."
                    class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">Tanggal</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Tugas</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Kategori</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Prioritas</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Ditugaskan Ke</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Status Submission</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Deadline</th>
                        <th
                            class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4 w-40">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($tasks as $task)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4 text-sm font-bold text-gray-800 dark:text-white">
                                {{ $task->date?->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="py-4">
                                <div class="font-bold text-gray-855 dark:text-gray-200">{{ $task->task_name }}</div>
                                @if ($task->description)
                                    <div class="text-xs text-gray-400 mt-0.5">{!! \Str::limit($task->description, 100) !!}</div>
                                @endif
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                {{ $task->category ?: 'Umum' }}
                                @if ($task->is_routine)
                                    <div
                                        class="text-[10px] font-black uppercase tracking-widest text-primary-blue mt-1">
                                        Rutin Harian</div>
                                @endif
                            </td>
                            <td class="py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $task->priorityBadgeClass }}">
                                    {{ $task->priority_label }}
                                </span>
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                @if ($task->is_routine)
                                    <span>Semua Kasir</span>
                                @else
                                    @foreach ($task->assignments as $assignment)
                                        <div class="text-xs">{{ $assignment->assignee->name }}</div>
                                    @endforeach
                                @endif
                            </td>
                            <td class="py-4 text-sm">
                                @php
                                    $submissions = $task->assignments->flatMap(fn($a) => $a->submissions)->countBy('approval_status');
                                @endphp
                                <div class="text-xs space-y-1">
                                    @if ($submissions->get('approved', 0) > 0)
                                        <span class="block text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            {{ $submissions->get('approved') }} ACC
                                        </span>
                                    @endif
                                    @if ($submissions->get('pending', 0) > 0)
                                        <span class="block text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $submissions->get('pending') }} Pending
                                        </span>
                                    @endif
                                    @if ($submissions->get('rejected', 0) > 0)
                                        <span class="block text-red-600 dark:text-red-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                            {{ $submissions->get('rejected') }} Rejected
                                        </span>
                                    @endif
                                    @if ($submissions->isEmpty() && $task->assignments->count() > 0)
                                        <span class="block text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                            Belum ada submission
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                {{ $task->deadline_at ? $task->deadline_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB' : '-' }}
                            </td>
                            <td class="py-4 text-right pr-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($activeTab === 'pending_review')
                                        <button wire:click="openReviewModal('{{ $task->id }}')"
                                            title="Review Submissions dari Kasir"
                                            class="px-2.5 py-1.5 bg-primary-blue hover:bg-blue-950 text-primary-yellow rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-md">
                                            Review
                                        </button>
                                    @endif
                                    @if ($activeTab === 'history')
                                        <button wire:click="openDetailModal('{{ $task->id }}')"
                                            title="Lihat Detail Submissions"
                                            class="px-2.5 py-1.5 bg-primary-blue hover:bg-blue-950 text-primary-yellow rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-md">
                                            Detail
                                        </button>
                                    @endif
                                    @if ($activeTab === 'active')
                                        <button wire:click="editTask('{{ $task->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-primary-blue hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-xl transition-all"
                                            title="Edit tugas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete('{{ $task->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 italic font-semibold">Belum ada tugas ditambahkan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showCreateModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-2xl md:max-w-3xl lg:max-w-4xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-10 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto">
            <h2 class="text-3xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">
                {{ $isEditMode ? 'Edit Tugas Harian' : 'Tambah Tugas Harian' }}</h2>
    
            <form wire:submit.prevent="prepareTask" class="space-y-4">
                @unless ($isRoutine)
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Mode Pilihan Kasir</p>
                            <div class="inline-flex rounded-full border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
                                <button type="button" wire:click="setAssigneeMode('scheduled')"
                                    class="px-4 py-3 text-xs font-black uppercase tracking-widest transition-all {{ $assigneeMode === 'scheduled' ? 'bg-primary-blue text-white' : 'text-gray-700 dark:text-gray-200' }}">
                                    Sesuai Jadwal Hari Ini
                                </button>
                                <button type="button" wire:click="setAssigneeMode('all')"
                                    class="px-4 py-3 text-xs font-black uppercase tracking-widest transition-all {{ $assigneeMode === 'all' ? 'bg-primary-blue text-white' : 'text-gray-700 dark:text-gray-200' }}">
                                    Umum (Semua Kasir)
                                </button>
                            </div>
                        </div>
    
                        <div x-data="{ open: false, query: '', selected: @entangle('assignedTo') }" class="relative">
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Ditugaskan Ke</label>
                            <button type="button" @click="open = !open"
                                class="w-full text-left px-4 py-3 bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm flex items-center justify-between gap-2">
                                <span x-text="selected.length > 0 ? selected.length + ' kasir dipilih' : 'Pilih kasir...'" class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
    
                            <div x-show="open" x-cloak @click.outside="open = false"
                                class="absolute z-40 mt-2 w-full max-h-72 overflow-hidden rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 shadow-2xl">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                    <input type="text" x-model="query" placeholder="Cari kasir..."
                                        class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-900 rounded-2xl text-sm text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 focus:outline-none">
                                </div>
                                <div class="max-h-56 overflow-y-auto">
                                    @forelse ($cashiers as $c)
                                        <label x-show="query === '' || '{{ strtolower($c->name) }}'.includes(query.toLowerCase())"
                                            class="flex items-center justify-between gap-3 px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 text-sm text-gray-900 dark:text-gray-100">
                                            <span>{{ $c->name }}</span>
                                            <input type="checkbox" value="{{ $c->id }}" wire:model="assignedTo"
                                                class="h-4 w-4 text-primary-blue rounded border-gray-300 dark:border-gray-600" />
                                        </label>
                                    @empty
                                        <div class="px-4 py-4 text-xs text-gray-500">Tidak ada kasir tersedia.</div>
                                    @endforelse
                                </div>
                            </div>
    
                            <div class="mt-2 text-[10px] text-gray-400">Pilih lebih dari satu kasir jika diperlukan.</div>
                        </div>
                    </div>
                @endunless
    
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Tanggal</label>
                    <input type="date" wire:model="date"
                        class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('date')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
    
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama Tugas</label>
                    <input type="text" wire:model="taskName" placeholder="Contoh: Bersihkan meja pos kasir"
                        class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('taskName')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
    
                <!-- Description with Quill Rich Text Editor -->
                <div wire:ignore 
                     x-data="{ 
                         description: @entangle('description').live,
                         init() {
                             const initQuill = () => {
                                 if (typeof Quill === 'undefined') {
                                     setTimeout(initQuill, 50);
                                     return;
                                 }
                                 const quill = new Quill(this.$refs.editor, {
                                     theme: 'snow',
                                     placeholder: 'Instruksi tambahan jika ada...',
                                     modules: {
                                         toolbar: [
                                             ['bold', 'italic', 'underline'],
                                             [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                             ['clean']
                                         ]
                                     }
                                 });

                                 // Set initial value
                                 quill.root.innerHTML = this.description || '';

                                 // Listen for text changes inside Quill
                                 quill.on('text-change', () => {
                                     this.description = quill.root.innerHTML;
                                 });

                                 // Watch Alpine state changes to sync Quill
                                 this.$watch('description', value => {
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
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Deskripsi Tambahan</label>
                    <style>
                        /* Custom styles to match theme inputs */
                        .ql-toolbar.ql-snow {
                            border: 1px solid rgba(0, 0, 0, 0.05) !important;
                            border-top-left-radius: 1rem !important;
                            border-top-right-radius: 1rem !important;
                            background: rgba(248, 250, 252, 0.8);
                        }
                        .dark .ql-toolbar.ql-snow {
                            border-color: rgba(255, 255, 255, 0.05) !important;
                            background: rgba(15, 23, 42, 0.8);
                        }
                        .ql-container.ql-snow {
                            border: 1px solid rgba(0, 0, 0, 0.05) !important;
                            border-bottom-left-radius: 1rem !important;
                            border-bottom-right-radius: 1rem !important;
                            min-height: 120px !important;
                            max-height: 300px !important;
                            overflow-y: auto !important;
                            font-family: inherit !important;
                            font-size: 0.875rem !important;
                        }
                        .dark .ql-container.ql-snow {
                            border-color: rgba(255, 255, 255, 0.05) !important;
                            color: white !important;
                        }
                        .ql-editor.ql-blank::before {
                            color: #9ca3af !important;
                            font-style: normal !important;
                        }
                        .dark .ql-editor.ql-blank::before {
                            color: #6b7280 !important;
                        }
                    </style>
                    <div x-ref="editor" class="bg-gray-55 dark:bg-gray-900 rounded-xl"></div>
                    @error('description')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
    
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Kategori Tugas</label>
                        <div class="flex gap-2">
                            <select wire:model="category"
                                class="flex-1 px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="openAddCategoryModal"
                                class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-xl text-xs font-black">Tambah</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Prioritas</label>
                        <select wire:model="priority"
                            class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                            <option value="critical">Paling Penting</option>
                        </select>
                    </div>
                </div>
    
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @unless ($isRoutine)
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Deadline</label>
                            <input wire:model="deadlineAt" type="datetime-local"
                                class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                        </div>
                    @else
                        <div class="flex items-center h-full">
                            <div class="text-xs text-gray-500">Deadline akan otomatis ditentukan per kasir.</div>
                        </div>
                    @endunless
                    <div class="flex flex-col justify-end gap-3">
                        <label class="inline-flex items-center gap-3 py-3 px-4 bg-gray-55 dark:bg-gray-900 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 cursor-pointer">
                            <input wire:model.live="isRoutine" type="checkbox"
                                class="form-checkbox h-5 w-5 text-primary-blue rounded" />
                            <span>Tugas rutin harian untuk semua kasir</span>
                        </label>
                        <label class="inline-flex items-center gap-3 py-3 px-4 bg-gray-55 dark:bg-gray-900 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 cursor-pointer">
                            <input wire:model="requiresProof" type="checkbox"
                                class="form-checkbox h-5 w-5 text-primary-blue rounded" />
                            <span>Memerlukan bukti foto / gambar dari kasir</span>
                        </label>
                    </div>
                </div>
    
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showCreateModal', false)"
                        class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Kirim Tugas' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Review Modal - Menampilkan SUBMISSIONS TERPISAH per Kasir -->
    <div x-data="{ show: @entangle('showReviewModal'), lightboxOpen: false, lightboxImg: '' }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showReviewModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto">
            
            <div class="mb-6">
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-2">
                    Review Submission - Tugas Kasir</h2>
                <p class="text-sm text-gray-500">Setiap kasir memiliki submission INDEPENDENT yang bisa direview terpisah</p>
            </div>
    
            @if(empty($currentReviewingSubmissions))
                <div class="py-8 text-center text-gray-400 italic font-semibold">
                    Belum ada submission untuk tugas ini
                </div>
            @else
                <!-- Submissions List (Sidebar-like) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="md:col-span-1">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 space-y-2 max-h-96 overflow-y-auto">
                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Submissions</div>
                            
                            @foreach($currentReviewingSubmissions as $sub)
                                <button 
                                    wire:click="selectSubmissionForReview('{{ $sub['id'] }}')"
                                    class="w-full text-left px-4 py-3 rounded-lg transition-all {{ $selectedSubmissionForReview === $sub['id'] ? 'bg-primary-blue text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    
                                    <div class="text-sm font-bold">{{ $sub['submitter']['name'] ?? 'Unknown' }}</div>
                                    
                                    <div class="text-xs mt-1">
                                        @if($sub['approval_status'] === 'approved')
                                            <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Approved
                                            </span>
                                        @elseif($sub['approval_status'] === 'rejected')
                                            <span class="text-red-600 dark:text-red-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="text-xs text-gray-400 mt-1">
                                        v{{ $sub['submission_version'] }} • {{ \Carbon\Carbon::parse($sub['submitted_at'])->setTimezone('Asia/Jakarta')->format('d M H:i') }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
    
                    <!-- Detail Submission -->
                    <div class="md:col-span-2">
                        @if($selectedSubmissionForReview)
                            @php
                                $selectedSub = collect($currentReviewingSubmissions)->firstWhere('id', $selectedSubmissionForReview);
                            @endphp
    
                            @if($selectedSub)
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl p-6 space-y-4 border border-gray-200 dark:border-gray-700">
                                    
                                    <!-- Kasir Info -->
                                    <div>
                                        <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Kasir Yang Submit</div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-blue text-white flex items-center justify-center font-black">
                                                {{ strtoupper(substr($selectedSub['submitter']['name'] ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 dark:text-white">{{ $selectedSub['submitter']['name'] }}</div>
                                                <div class="text-xs text-gray-500">Submission v{{ $selectedSub['submission_version'] }}</div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <!-- Status Badge -->
                                    <div>
                                        <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Status</div>
                                        @if($selectedSub['approval_status'] === 'approved')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                DISETUJUI
                                            </span>
                                        @elseif($selectedSub['approval_status'] === 'rejected')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-black bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                DITOLAK
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                MENUNGGU REVIEW
                                            </span>
                                        @endif
                                    </div>
    
                                    <!-- Submitted Time -->
                                    <div>
                                        <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Waktu Submit</div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($selectedSub['submitted_at'])->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i WIB') }}</div>
                                    </div>
    
                                    <!-- Report -->
                                    <div>
                                        <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Laporan</div>
                                        <div class="p-3 bg-white dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 min-h-24">
                                            {!! nl2br(htmlspecialchars($selectedSub['report'] ?? '-')) !!}
                                        </div>
                                    </div>
    
                                    <!-- Proof Image -->
                                    @if($selectedSub['proof_image'])
                                        <div>
                                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Bukti Foto</div>
                                            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-black/5 hover:bg-black/10 dark:bg-white/5 dark:hover:bg-white/10 transition-all cursor-pointer group/img"
                                                @click="lightboxOpen = true; lightboxImg = '{{ asset('storage/' . $selectedSub['proof_image']) }}'">
                                                <img src="{{ asset('storage/' . $selectedSub['proof_image']) }}" alt="Proof" 
                                                    class="w-full h-auto max-h-48 object-cover group-hover/img:scale-105 group-hover/img:opacity-90 transition-all duration-300">
                                            </div>
                                        </div>
                                    @endif
    
                                    <!-- Rejection Note (jika ditolak) -->
                                    @if($selectedSub['approval_status'] === 'rejected' && $selectedSub['rejection_note'])
                                        <div>
                                            <div class="text-xs font-black uppercase tracking-widest text-red-600 dark:text-red-400 mb-2">Catatan Penolakan</div>
                                            <div class="p-3 bg-red-50 dark:bg-red-950/30 rounded-lg text-sm text-red-700 dark:text-red-300">
                                                {{ $selectedSub['rejection_note'] }}
                                            </div>
                                        </div>
                                    @endif
    
                                    <!-- Reviewed Info (jika sudah direview) -->
                                    @if($selectedSub['reviewed_by'])
                                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Direview Oleh</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $selectedSub['reviewer']['name'] ?? 'Admin' }} • {{ \Carbon\Carbon::parse($selectedSub['reviewed_at'])->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i WIB') }}
                                            </div>
                                        </div>
                                    @endif
    
                                    <!-- Action Buttons -->
                                    @if($selectedSub['approval_status'] === 'pending')
                                        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <button wire:click="openRejectModal"
                                                class="flex-1 py-2 px-3 bg-red-100 hover:bg-red-200 dark:bg-red-950/40 dark:hover:bg-red-950/60 text-red-700 dark:text-red-400 rounded-lg font-black text-xs uppercase transition-all">
                                                Tolak & Revisi
                                            </button>
                                            <button wire:click="approveSubmission"
                                                class="flex-1 py-2 px-3 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950/40 dark:hover:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 rounded-lg font-black text-xs uppercase transition-all">
                                                Setujui ✓
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 text-gray-400">
                                Pilih submission di daftar untuk melihat detailnya
                            </div>
                        @endif
                    </div>
                </div>
            @endif
    
            <!-- Close Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="$set('showReviewModal', false)"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg font-black text-xs uppercase transition-all">
                    Tutup
                </button>
            </div>
        </div>
        
        <!-- Lightbox Overlay -->
        <div x-show="lightboxOpen" 
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4"
            x-transition.opacity
            @click="lightboxOpen = false"
            x-cloak>
            <div class="relative max-w-4xl max-h-full flex items-center justify-center">
                <button type="button" class="absolute top-4 right-4 text-white hover:text-gray-300 focus:outline-none bg-black/50 p-2 rounded-full z-10" @click="lightboxOpen = false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img :src="lightboxImg" class="max-w-full max-h-[85vh] rounded-lg object-contain shadow-2xl">
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div x-data="{ show: @entangle('showRejectModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showRejectModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-6 border border-gray-100 dark:border-gray-700 z-10">
            <h3 class="text-lg font-black mb-4">Tolak & Minta Revisi</h3>
            <p class="text-sm text-gray-500 mb-4">Kasir akan menerima notifikasi dan bisa merevisi submission mereka.</p>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Penolakan</label>
                <textarea wire:model="rejectionNote" rows="4" placeholder="Jelaskan apa yang perlu direvisi..."
                    class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-red-500 dark:text-white text-sm"></textarea>
                @error('rejectionNote')
                    <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" wire:click="$set('showRejectModal', false)"
                    class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase transition-all">
                    Batal
                </button>
                <button type="button" wire:click="rejectSubmission"
                    class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase transition-all">
                    Tolak
                </button>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showDeleteModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-center">
            <h2 class="text-xl font-black text-gray-850 dark:text-white mb-2">Hapus Tugas?</h2>
            <p class="text-sm text-gray-500 mb-6">Tindakan ini tidak bisa dibatalkan. Semua data submission akan ikut terhapus.</p>
            <div class="flex gap-3">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                    class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase transition-all">
                    Batal
                </button>
                <button type="button" wire:click="deleteTask"
                    class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase transition-all">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    
    <!-- Add Category Modal -->
    <div x-data="{ show: @entangle('showAddCategoryModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showAddCategoryModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-6 border border-gray-100 dark:border-gray-700 z-10">
            <h3 class="text-lg font-black mb-4">Tambah Kategori Tugas</h3>
            <div>
                <input wire:model="newCategoryName" type="text" placeholder="Nama kategori"
                    class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue text-sm">
                @error('newCategoryName')
                    <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" wire:click="$set('showAddCategoryModal', false)"
                    class="flex-1 py-2 bg-gray-100 rounded-xl text-gray-800 dark:bg-gray-700 dark:text-white font-black text-xs uppercase">
                    Batal
                </button>
                <button type="button" wire:click="storeCategory"
                    class="flex-1 py-2 bg-primary-blue text-primary-yellow rounded-xl font-black text-xs uppercase">
                    Simpan
                </button>
            </div>
        </div>
    </div>
    
    <!-- Confirm Multiple Assignees Modal -->
    <div x-data="{ show: @entangle('showConfirmModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showConfirmModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-6 border border-gray-100 dark:border-gray-700 z-10">
            <h3 class="text-lg font-black mb-2">Konfirmasi Tugas untuk Banyak Kasir</h3>
            <p class="text-sm text-gray-500 mb-4">Anda akan membuat tugas ini untuk <strong>{{ count($pendingAssignees) }}</strong> kasir. Laporan mereka akan di-track TERPISAH.</p>
            <div class="space-y-2 pb-4 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg">
                <div class="text-xs font-black uppercase text-gray-400">Nama Tugas</div>
                <div class="font-bold text-gray-800 dark:text-white">{{ $taskName }}</div>
                <div class="text-xs font-black uppercase text-gray-400 mt-2">Tanggal</div>
                <div class="text-gray-700 dark:text-gray-300">{{ $date }}</div>
                <div class="text-xs font-black uppercase text-gray-400 mt-2">Prioritas</div>
                <div class="text-gray-700 dark:text-gray-300">{{ \App\Models\CashierTaskDefinition::statusLabels()[$priority] ?? $priority }}</div>
            </div>
            <div class="flex gap-3">
                <button type="button" wire:click="$set('showConfirmModal', false)"
                    class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase transition-all">
                    Batal
                </button>
                <button type="button" wire:click="finalSaveTask"
                    class="flex-1 py-2 bg-primary-blue text-primary-yellow rounded-xl font-black text-xs uppercase transition-all">
                    Konfirmasi & Simpan
                </button>
            </div>
        </div>
    </div>
</div>