<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">
                Tugas Saya</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Lihat instruksi tugas, kirim laporan hasil kerja kasir & bukti, lalu tunggu review/admin ACC</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
        <button wire:click="$set('activeTab', 'today')"
            class="px-6 py-3 text-sm font-black uppercase tracking-wider border-b-4 transition-all duration-300 {{ $activeTab === 'today' ? 'border-primary-blue text-primary-blue dark:border-primary-yellow dark:text-primary-yellow' : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            Tugas Hari Ini ({{ count($todayTasks) }})
        </button>
        <button wire:click="$set('activeTab', 'history')"
            class="px-6 py-3 text-sm font-black uppercase tracking-wider border-b-4 transition-all duration-300 {{ $activeTab === 'history' ? 'border-primary-blue text-primary-blue dark:border-primary-yellow dark:text-primary-yellow' : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            Riwayat Tugas Sebelumnya
        </button>
    </div>

    <!-- Active Tab Content -->
    @if ($activeTab === 'today')
        <!-- Tugas Hari Ini -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-800 dark:text-white">Tugas Hari Ini</h2>
                <span class="px-2.5 py-1 rounded-full bg-primary-blue text-white text-[10px] font-black">{{ now()->translatedFormat('d F Y') }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($todayTasks as $task)
                    @include('livewire.reports.partials.my-task-card', ['task' => $task])
                @empty
                    <div class="col-span-full text-center py-12 text-sm text-gray-400 italic font-semibold">
                        Tidak ada tugas untuk hari ini. Santai dulu!
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Riwayat Tugas -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
            <h2 class="text-sm font-black uppercase tracking-widest text-gray-800 dark:text-white mb-6">Riwayat Tugas Sebelumnya</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($historyTasks as $task)
                    @include('livewire.reports.partials.my-task-card', ['task' => $task])
                @empty
                    <div class="col-span-full text-center py-12 text-sm text-gray-400 italic font-semibold">
                        Belum ada riwayat tugas.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $historyTasks->links() }}
            </div>
        </div>
    @endif

    <!-- Task Detail Modal (Read-Only) -->
    <div x-data="{ show: @entangle('showTaskDetailModal') }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[600] flex items-center justify-center p-6 bg-gray-950/40 backdrop-blur-sm">
        <div @click.away="show = false"
            class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-6 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-white relative">
                <button @click="show = false"
                    class="absolute right-6 top-6 p-2 bg-black/5 hover:bg-black/10 dark:bg-white/10 dark:hover:bg-white/20 rounded-xl transition-all">
                    <svg class="w-4 h-4 text-slate-600 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-xl font-black uppercase italic tracking-tighter">DETAIL INSTRUKSI TUGAS</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.3em] mt-1.5 opacity-60 text-slate-500 dark:text-slate-400">DETAIL & PENJELASAN TUGAS KASIR</p>
            </div>

            <div class="p-6 max-h-[60vh] overflow-y-auto no-scrollbar space-y-5 text-left">
                @if ($selectedTaskModel)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Nama Tugas</span>
                            <h4 class="text-sm font-black uppercase text-gray-800 dark:text-white">
                                {{ $selectedTaskModel->task_name }}</h4>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Kategori</span>
                            <div class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">
                                {{ $selectedTaskModel->category ?: 'Umum' }}</div>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Prioritas</span>
                            @php
                                $detailPriorityClass = match ($selectedTaskModel->priority) {
                                    'low' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    'high' => 'bg-orange-55 text-orange-700 dark:bg-orange-950/30 dark:text-orange-400',
                                    'critical' => 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400',
                                    default => 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
                                };
                                $detailPriorityText = match ($selectedTaskModel->priority) {
                                    'low' => 'Rendah',
                                    'high' => 'Tinggi',
                                    'critical' => 'Paling Penting',
                                    default => 'Sedang',
                                };
                            @endphp
                            <div class="text-xs font-black uppercase tracking-widest {{ $detailPriorityClass }} inline-flex items-center px-2.5 py-1 rounded-lg">
                                {{ $detailPriorityText }}</div>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Batas Waktu (Deadline)</span>
                            <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                @if ($selectedTaskModel->deadline_at)
                                    {{ $selectedTaskModel->deadline_at->translatedFormat('d M Y H:i') . ' WIB' }}
                                @elseif($selectedTaskModel->is_routine && isset($selectedTaskModel->computed_deadline) && $selectedTaskModel->computed_deadline)
                                    {{ \Carbon\Carbon::parse($selectedTaskModel->computed_deadline)->translatedFormat('d M Y H:i') . ' WIB' }}
                                @else
                                    Belum ditetapkan
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($selectedTaskModel->description)
                        <div class="bg-gray-50 dark:bg-gray-900/60 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-2">Deskripsi & Instruksi Tugas</span>
                            <div class="text-xs text-gray-700 dark:text-gray-250 font-semibold leading-relaxed">
                                {!! $selectedTaskModel->description !!}</div>
                        </div>
                    @endif
                @endif
            </div>
            
            <div class="p-6 bg-gray-50 dark:bg-gray-900/40 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="button" @click="show = false" class="px-6 py-2.5 bg-primary-blue text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    <!-- Task Completion Modal -->
    <div x-data="{ show: @entangle('showTaskCompletionModal') }" x-show="show" x-cloak @keydown.window.escape="show = false"
        class="fixed inset-0 z-[600] flex items-center justify-center p-6 bg-gray-950/40 backdrop-blur-sm">
        <div @click.away="show = false"
            class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-6 bg-primary-blue dark:bg-slate-900 text-white relative">
                <button @click="show = false"
                    class="absolute right-6 top-6 p-2 bg-white/10 hover:bg-white/20 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-xl font-black uppercase italic tracking-tighter">DETAIL INSTRUKSI & LAPORAN KASIR</h3>
                <p class="text-[9px] font-black uppercase tracking-[0.3em] mt-1.5 opacity-60">Lihat instruksi tugas, isi laporan hasil kerja kasir, lalu kirim untuk review admin.</p>
            </div>

            <div class="p-6 max-h-[60vh] overflow-y-auto no-scrollbar space-y-4 text-left">
                @if ($selectedTaskModel)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Nama
                                Tugas</span>
                            <h4 class="text-sm font-black uppercase text-gray-800 dark:text-white">
                                {{ $selectedTaskModel->task_name }}</h4>
                        </div>
                        <div>
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Kategori</span>
                            <div
                                class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">
                                {{ $selectedTaskModel->category ?: 'Umum' }}</div>
                        </div>
                        <div>
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Prioritas</span>
                            @php
                                $modalPriorityClass = match ($selectedTaskModel->priority) {
                                    'low' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    'high' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/30 dark:text-orange-400',
                                    'critical' => 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400',
                                    default => 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
                                };
                            @endphp
                            <div
                                class="text-xs font-black uppercase tracking-widest {{ $modalPriorityClass }} inline-flex items-center px-2 py-1 rounded-full">
                                {{ $selectedTaskModel->priority_label }}</div>
                        </div>
                        <div>
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Deadline</span>
                            <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                @if ($selectedTaskModel->deadline_at)
                                    {{ $selectedTaskModel->deadline_at->translatedFormat('d M Y H:i') . ' WIB' }}
                                @elseif(
                                    $selectedTaskModel->is_routine &&
                                        isset($selectedTaskModel->computed_deadline) &&
                                        $selectedTaskModel->computed_deadline)
                                    {{ \Carbon\Carbon::parse($selectedTaskModel->computed_deadline)->translatedFormat('d M Y H:i') . ' WIB' }}
                                @else
                                    Belum ditetapkan
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($selectedTaskModel->description)
                        <div>
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Deskripsi
                                Tugas</span>
                            <div class="text-xs text-gray-600 dark:text-gray-300 font-semibold">
                                {!! $selectedTaskModel->description !!}</div>
                        </div>
                    @endif

                    @if ($selectedTaskModel->approval_status === 'rejected')
                        <div
                            class="p-4 bg-red-50 dark:bg-red-950/30 border-2 border-dashed border-red-300 dark:border-red-800 rounded-2xl">
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-red-500 block mb-1">Ditolak
                                Admin — Catatan Revisi</span>
                            <p class="text-xs font-bold text-red-600 dark:text-red-400 leading-relaxed">
                                {{ $selectedTaskModel->rejection_note ?: 'Tidak ada catatan. Silakan perbaiki laporan/bukti lalu kirim ulang.' }}
                            </p>
                        </div>
                    @endif

                    <hr class="border-gray-100 dark:border-gray-700 my-4" />

                    @if ($selectedTaskModel->approval_status === 'approved')
                        <!-- Approved: read-only -->
                        <div class="space-y-4">
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                                Disetujui {{ $selectedTaskModel->reviewed_at?->format('d/m/Y H:i') }}
                            </span>
                            <div>
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Laporan
                                    Hasil</span>
                                <div
                                    class="text-xs font-semibold text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 p-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl leading-relaxed">
                                    {!! $selectedTaskModel->completion_report !!}</div>
                            </div>
                            @if ($selectedTaskModel->proof_image)
                                <div>
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-1">Foto
                                        Bukti</span>
                                    <a href="{{ asset('storage/' . $selectedTaskModel->proof_image) }}" target="_blank"
                                        class="block rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:opacity-90 transition-opacity">
                                        <img src="{{ asset('storage/' . $selectedTaskModel->proof_image) }}"
                                            class="w-full object-cover max-h-48" alt="Bukti Tugas" />
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Pending review / not yet submitted / rejected: editable form -->
                        <div class="space-y-4">
                            @if ($selectedTaskModel->approval_status === 'pending')
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">
                                    Menunggu ACC Admin — Anda masih bisa memperbarui laporan
                                </span>
                            @endif
                            <div wire:ignore 
                                 x-data="{ 
                                     content: @entangle('taskCompletionReport'),
                                     init() {
                                         const initQuill = () => {
                                             if (typeof Quill === 'undefined') {
                                                 setTimeout(initQuill, 50);
                                                 return;
                                             }
                                             const quill = new Quill(this.$refs.editor, {
                                                 theme: 'snow',
                                                 placeholder: 'Jelaskan detail pekerjaan/tugas yang telah selesai dilakukan...',
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
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Laporan Hasil Pekerjaan</label>
                                <div x-ref="editor" class="bg-gray-50 dark:bg-gray-900 border-none rounded-2xl text-gray-800 dark:text-white" style="min-height: 120px;"></div>
                                @error('taskCompletionReport')
                                    <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($selectedTaskModel->requires_proof)
                                <div>
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Foto
                                        / Gambar Bukti
                                        {{ $selectedTaskModel->proof_image ? '(kosongkan jika tetap pakai bukti lama)' : '' }}</label>
                                    <input type="file" wire:model="taskProofImage"
                                        class="w-full p-3 text-xs bg-gray-55 dark:bg-gray-900 rounded-2xl font-semibold dark:text-white">
                                    @error('taskProofImage')
                                        <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                                    @enderror
                                    <div wire:loading wire:target="taskProofImage"
                                        class="text-[10px] font-black text-amber-500 uppercase mt-2">Mengunggah gambar...
                                    </div>
                                    @if ($selectedTaskModel->proof_image)
                                        <a href="{{ asset('storage/' . $selectedTaskModel->proof_image) }}" target="_blank"
                                            class="inline-block text-[10px] font-black text-primary-blue uppercase mt-2 underline">Lihat
                                            bukti yang sudah diunggah</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

            @if ($selectedTaskModel && $selectedTaskModel->approval_status !== 'approved')
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                    <button @click="show = false" type="button"
                        class="flex-1 py-4 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-sm uppercase tracking-wider transition-all active:scale-95">BATAL</button>
                    <button wire:click="submitTaskCompletion" wire:loading.attr="disabled" type="button"
                        class="flex-[2] py-4 bg-primary-blue hover:bg-blue-900 text-white rounded-2xl font-black text-sm uppercase tracking-wider transition-all active:scale-95 shadow-xl shadow-blue-900/10">
                        <span wire:loading.remove
                            wire:target="submitTaskCompletion">{{ $selectedTaskModel->approval_status === 'rejected' ? 'KIRIM REVISI' : 'KIRIM LAPORAN' }}</span>
                        <span wire:loading wire:target="submitTaskCompletion">MENGIRIM...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
