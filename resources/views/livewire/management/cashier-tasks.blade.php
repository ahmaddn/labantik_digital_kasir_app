<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">
                Tugas Harian Kasir</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola tugas operasional untuk
                kasir</p>
        </div>
        <div class="flex items-center gap-3">
            @if (session('active_role_name') === 'superadmin')
                <div class="w-64">
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
                class="inline-flex items-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Tugas Baru
            </button>
        </div>
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
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Deadline</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($tasks as $task)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4 text-sm font-bold text-gray-800 dark:text-white">
                                {{ \Carbon\Carbon::parse($task->date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4">
                                <div class="font-bold text-gray-855 dark:text-gray-200">{{ $task->task_name }}</div>
                                @if ($task->description)
                                    <div class="text-xs text-gray-400 mt-0.5">{!! $task->description !!}</div>
                                @endif
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                {{ $task->category ?: 'Umum' }}
                                @if ($task->is_routine)
                                    <div class="text-[10px] font-black uppercase tracking-widest text-primary-blue mt-1">Rutin Harian</div>
                                @endif
                            </td>
                            <td class="py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $task->priorityBadgeClass }}">
                                    {{ $task->priority_label }}
                                </span>
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                {{ $task->group_assignees_names }}
                            </td>
                            <td class="py-4">
                                @if ($task->approval_status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                                        Disetujui
                                    </span>
                                @elseif($task->approval_status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                        Ditolak
                                    </span>
                                @elseif($task->approval_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                                        Menunggu ACC
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400">
                                        Belum Dikerjakan
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                {{ $task->deadline_at ? $task->deadline_at->translatedFormat('d M Y H:i') . ' WIB' : '-' }}
                            </td>
                            <td class="py-4 text-right pr-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($task->is_completed || $task->approval_status === 'pending' || $task->completion_report)
                                        <button wire:click="openReviewModal('{{ $task->id }}')"
                                            title="Review Laporan & Bukti"
                                            class="px-2.5 py-1.5 bg-primary-blue hover:bg-blue-950 text-primary-yellow rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-md">
                                            Review
                                        </button>
                                    @else
                                        <button wire:click="openReviewModal('{{ $task->id }}')"
                                            title="Lihat Detail Tugas"
                                            class="px-2.5 py-1.5 bg-gray-150 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95">
                                            Detail
                                        </button>
                                    @endif
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 italic font-semibold">Belum ada
                                tugas ditambahkan</td>
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
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showCreateModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-2xl md:max-w-3xl lg:max-w-4xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-10 border border-gray-100 dark:border-gray-700 z-10 animate-fade-in">
            <h2 class="text-3xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">
                {{ $isEditMode ? 'Edit Tugas Harian' : 'Tambah Tugas Harian' }}</h2>

            <form wire:submit.prevent="prepareTask" class="space-y-4">
                @unless ($isRoutine)
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Mode Pilihan
                                Kasir</p>
                            <div
                                class="inline-flex rounded-full border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
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
                            <label
                                class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Ditugaskan
                                Ke</label>
                            <button type="button" @click="open = !open"
                                class="w-full text-left px-4 py-3 bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm flex items-center justify-between gap-2">
                                <span x-text="selected.length > 0 ? selected.length + ' kasir dipilih' : 'Pilih kasir...'"
                                    class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
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
                                        <label
                                            x-show="query === '' || '{{ strtolower($c->name) }}'.includes(query.toLowerCase())"
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

                        @error('assignedTo')
                            <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                @endunless

                <div>
                    <label
                        class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Tanggal</label>
                    <input type="date" wire:model="date"
                        class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('date')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nama
                        Tugas</label>
                    <input type="text" wire:model="taskName" placeholder="Contoh: Bersihkan meja pos kasir"
                        class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('taskName')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div wire:ignore 
                                     x-data="{ 
                                         content: @entangle('description'),
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

                                                 quill.root.innerHTML = this.content || '';

                                                 quill.on('text-change', () => {
                                                     this.content = quill.root.innerHTML;
                                                 });

                                                 this.$watch('content', value => {
                                                     if (quill.root.innerHTML !== value) {
                                                         quill.root.innerHTML = value || '';
                                                     }
                                                 });
                                             };
                                             initQuill();
                                         }
                                     }">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Deskripsi Tambahan</label>
                    <div x-ref="editor" class="bg-gray-55 dark:bg-gray-900 border-none rounded-xl text-gray-800 dark:text-white text-sm" style="min-height: 120px;"></div>
                    @error('description')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Kategori
                            Tugas</label>
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
                        @error('category')
                            <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Prioritas</label>
                        <select wire:model="priority"
                            class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                            <option value="critical">Paling Penting</option>
                        </select>
                        @error('priority')
                            <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @unless ($isRoutine)
                        <div>
                            <label
                                class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Deadline</label>
                            <input wire:model="deadlineAt" type="datetime-local"
                                class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                            @error('deadlineAt')
                                <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <div class="flex items-center h-full">
                            <div class="text-xs text-gray-500">Deadline akan otomatis ditentukan (8 jam sejak clock-in
                                pertama kasir).</div>
                        </div>
                    @endunless
                    <div class="flex flex-col justify-end">
                        <label
                            class="inline-flex items-center gap-3 py-3 px-4 bg-gray-55 dark:bg-gray-900 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 cursor-pointer">
                            <input wire:model.live="isRoutine" type="checkbox"
                                class="form-checkbox h-5 w-5 text-primary-blue rounded" />
                            <span>Tugas rutin harian untuk semua kasir</span>
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

    <!-- Add Category Modal -->
    <div x-data="{ show: @entangle('showAddCategoryModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
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
                    class="flex-1 py-2 bg-gray-100 rounded-xl">Batal</button>
                <button type="button" wire:click="storeCategory"
                    class="flex-1 py-2 bg-primary-blue text-white rounded-xl">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Confirm Modal for multiple assignees -->
    <div x-data="{ show: @entangle('showConfirmModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showConfirmModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-6 border border-gray-100 dark:border-gray-700 z-10">
            <h3 class="text-lg font-black mb-2">Konfirmasi Tugas untuk Banyak Kasir</h3>
            <p class="text-sm text-gray-500 mb-4">Anda akan membuat tugas ini untuk
                <strong>{{ count($pendingAssignees) }}</strong> kasir. Pastikan data sudah benar.
            </p>
            <div class="space-y-2 pb-4">
                <div class="text-xs font-black uppercase text-gray-400">Nama Tugas</div>
                <div class="font-bold">{{ $taskName }}</div>
                <div class="text-xs font-black uppercase text-gray-400 mt-2">Tanggal</div>
                <div>{{ $date }}</div>
                <div class="text-xs font-black uppercase text-gray-400 mt-2">Prioritas</div>
                <div>{{ \App\Models\CashierTask::priorityLabels()[$priority] ?? $priority }}</div>
            </div>
            <div class="flex gap-3">
                <button type="button" wire:click="$set('showConfirmModal', false)"
                    class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">Batal</button>
                <button type="button" wire:click="finalSaveTask"
                    class="flex-1 py-2 bg-primary-blue text-white rounded-xl">Konfirmasi & Simpan</button>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showDeleteModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-center">
            <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic mb-4">Hapus Tugas?</h2>
            <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus tugas harian ini?</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)"
                    class="flex-1 py-3 bg-gray-105 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteTask"
                    class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-data="{ show: @entangle('showRejectModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showRejectModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic mb-2">Tolak Laporan Tugas</h2>
            <p class="text-gray-400 text-sm mb-6">Tugas akan dikembalikan ke kasir untuk direvisi. Berikan catatan agar
                kasir tahu apa yang harus diperbaiki.</p>

            <form wire:submit.prevent="rejectTask" class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Catatan
                        Penolakan</label>
                    <textarea wire:model="rejectionNote" placeholder="Contoh: Foto bukti kurang jelas, mohon foto ulang..."
                        rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-red-500 dark:text-white text-sm"></textarea>
                    @error('rejectionNote')
                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showRejectModal', false)"
                        class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Tolak & Minta Revisi
                    </button>
                </div>
            </form>
         </div>
     </div>

    <!-- Task Review Modal -->
    <div x-data="{ show: @entangle('showReviewModal'), showLightbox: false, lightboxImg: '' }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showReviewModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-10 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto no-scrollbar">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 dark:border-gray-700 mb-6">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-primary-blue dark:text-blue-400">Detail & Review Tugas</span>
                    <h3 class="text-2xl font-black uppercase text-gray-850 dark:text-white tracking-tight mt-1">Review Laporan Kerja</h3>
                </div>
                <button wire:click="$set('showReviewModal', false)" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            @if ($reviewingTaskId)
                @php
                    $reviewTask = \App\Models\CashierTask::with(['user', 'creator', 'reviewer'])->find($reviewingTaskId);
                @endphp
                @if ($reviewTask)
                    <div class="space-y-6 text-left">
                        <!-- Task Details Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Nama Tugas</span>
                                <h4 class="text-sm font-black uppercase text-gray-800 dark:text-white">{{ $reviewTask->task_name }}</h4>
                            </div>
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Tanggal Tugas</span>
                                <div class="text-xs font-semibold text-gray-750 dark:text-gray-300">{{ \Carbon\Carbon::parse($reviewTask->date)->translatedFormat('d F Y') }}</div>
                            </div>
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Kategori</span>
                                <div class="text-xs font-black uppercase text-slate-700 dark:text-slate-300">{{ $reviewTask->category ?: 'Umum' }}</div>
                            </div>
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Prioritas</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $reviewTask->priorityBadgeClass }}">{{ $reviewTask->priority_label }}</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Ditugaskan Ke</span>
                                <div class="text-xs font-bold text-gray-750 dark:text-gray-200">{{ $reviewTask->group_assignees_names }}</div>
                            </div>
                        </div>

                        <!-- Task Description -->
                        @if ($reviewTask->description)
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Deskripsi Tugas</span>
                                <div class="text-xs font-semibold text-gray-750 dark:text-gray-250 prose prose-sm max-w-none">{!! $reviewTask->description !!}</div>
                            </div>
                        @endif

                        <hr class="border-gray-100 dark:border-gray-700" />

                        <!-- Completion Status Banner -->
                        @if ($reviewTask->approval_status === 'approved')
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-250 dark:border-emerald-900/50 rounded-2xl flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">✓</div>
                                <div>
                                    <div class="text-xs font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">Tugas Disetujui (ACC)</div>
                                    <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-300 mt-0.5">Disetujui oleh {{ $reviewTask->reviewer->name ?? 'Admin' }} pada {{ $reviewTask->reviewed_at?->format('d/m/Y H:i') }} WIB</div>
                                </div>
                            </div>
                        @elseif ($reviewTask->approval_status === 'rejected')
                            <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-250 dark:border-red-900/50 rounded-2xl flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">!</div>
                                <div>
                                    <div class="text-xs font-black text-red-800 dark:text-red-400 uppercase tracking-wide">Tugas Ditolak - Butuh Revisi</div>
                                    <div class="text-[10px] font-bold text-red-600 dark:text-red-300 mt-0.5">Catatan Penolakan: "{{ $reviewTask->rejection_note }}"</div>
                                </div>
                            </div>
                        @endif

                        <!-- Completion Report & Proof Image -->
                        @if ($reviewTask->completion_report || $reviewTask->proof_image)
                            <div class="space-y-4">
                                @if ($reviewTask->completion_report)
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Laporan Hasil Pekerjaan Kasir</span>
                                        <div class="text-sm font-semibold bg-gray-50 dark:bg-gray-900 p-4 border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-2xl leading-relaxed text-gray-800 dark:text-white prose prose-sm max-w-none">
                                            {!! $reviewTask->completion_report !!}
                                        </div>
                                    </div>
                                @endif

                                @if ($reviewTask->proof_image)
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">Foto / Gambar Bukti Kerja (Klik untuk Perbesar)</span>
                                        <div class="relative cursor-zoom-in rounded-3xl overflow-hidden border-2 border-gray-150 dark:border-gray-700 hover:opacity-95 transition-opacity max-w-md mx-auto"
                                             @click="lightboxImg = '{{ asset('storage/' . $reviewTask->proof_image) }}'; showLightbox = true">
                                            <img src="{{ asset('storage/' . $reviewTask->proof_image) }}" class="w-full object-cover max-h-64" alt="Bukti Tugas" />
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-6 text-xs text-gray-400 italic font-semibold uppercase tracking-wider">
                                Kasir belum mengirimkan laporan hasil pekerjaan.
                            </div>
                        @endif

                        <!-- Action Buttons inside Modal -->
                        @if ($reviewTask->approval_status === 'pending')
                            <div class="flex gap-4 pt-6 border-t border-gray-100 dark:border-gray-700 mt-6">
                                <button type="button" wire:click="openRejectModal('{{ $reviewTask->id }}')" class="flex-1 py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-red-500/20">
                                    TOLAK & REVISI
                                </button>
                                <button type="button" wire:click="approveTask('{{ $reviewTask->id }}')" class="flex-1 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-emerald-500/20">
                                    SETUJUI (ACC)
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        <!-- Lightbox Overlay -->
        <div x-show="showLightbox" x-transition.opacity class="fixed inset-0 z-60 bg-black/90 flex items-center justify-center p-4 backdrop-blur-xs" @click="showLightbox = false" x-cloak>
            <button class="absolute top-6 right-6 text-white hover:text-gray-300 p-3 rounded-full bg-black/40 hover:bg-black/60 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img :src="lightboxImg" class="max-w-full max-h-[90vh] rounded-3xl shadow-2xl border-4 border-white/10" @click.stop />
        </div>
    </div>
</div>
