@php
    // Determine the visual status of the task
    if ($task->approval_status === 'approved') {
        $badge = [
            'label' => 'Disetujui',
            'class' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400',
        ];
    } elseif ($task->approval_status === 'rejected') {
        $badge = [
            'label' => 'Ditolak — Revisi',
            'class' => 'bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400',
        ];
    } elseif ($task->approval_status === 'pending') {
        $badge = [
            'label' => 'Menunggu ACC',
            'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400',
        ];
    } else {
        $badge = [
            'label' => 'Belum Dikerjakan',
            'class' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400',
        ];
    }
@endphp

<div
    class="p-5 rounded-3xl border {{ $task->approval_status === 'rejected' ? 'border-red-200 dark:border-red-900 bg-red-50/30 dark:bg-red-950/10' : 'border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/40' }} flex flex-col gap-3 hover:shadow-lg transition-shadow">
    <div class="flex flex-col gap-3">
        <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4
                class="text-sm font-black uppercase tracking-tight {{ $task->approval_status === 'approved' ? 'line-through text-gray-400' : 'text-gray-800 dark:text-white' }}">
                {{ $task->task_name }}
            </h4>
            <span
                class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ $badge['class'] }}">
                {{ $badge['label'] }}
            </span>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <!-- Tanggal Tugas -->
            <span class="text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-350 px-2.5 py-1 rounded-full">
                Tgl: {{ $task->date->translatedFormat('d M Y') }}
            </span>

            @if ($task->category)
                <span class="text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/30 dark:text-purple-400 px-2.5 py-1 rounded-full">
                    Kategori: {{ $task->category }}
                </span>
            @endif

            @php
                $statusBadgeClass = match ($task->status) {
                    'new' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400',
                    'pending' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
                    'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
                    default => 'bg-slate-50 text-slate-600 dark:bg-slate-900/30 dark:text-slate-300',
                };
                $priorityBadgeClass = match ($task->priority) {
                    'low' => 'bg-slate-100 text-slate-600 dark:bg-slate-850 dark:text-slate-400',
                    'high' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/30 dark:text-orange-400',
                    'critical' => 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400',
                    default => 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
                };
                $priorityText = match ($task->priority) {
                    'low' => 'Rendah',
                    'high' => 'Tinggi',
                    'critical' => 'Paling Penting',
                    default => 'Sedang',
                };
            @endphp

            <span class="text-[10px] font-bold {{ $statusBadgeClass }} px-2.5 py-1 rounded-full">
                Status: {{ $task->status_label }}
            </span>

            <span class="text-[10px] font-bold {{ $priorityBadgeClass }} px-2.5 py-1 rounded-full">
                Urgensi: {{ $priorityText }}
            </span>

            @if ($task->deadline_at)
                <span class="text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-450 px-2.5 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Batas: {{ $task->deadline_at->translatedFormat('d M H:i') }} WIB
                </span>
            @elseif($task->is_routine && isset($task->computed_deadline) && $task->computed_deadline)
                <span class="text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-450 px-2.5 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Batas: {{ \Carbon\Carbon::parse($task->computed_deadline)->translatedFormat('d M H:i') }} WIB
                </span>
            @endif
        </div>

        @if ($task->description)
            <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold leading-relaxed line-clamp-2">
                {!! $task->description !!}</div>
        @endif

        @if ($task->approval_status === 'rejected' && $task->rejection_note)
            <p class="text-[10px] font-bold text-red-500 bg-red-50 dark:bg-red-950/30 p-2.5 rounded-xl leading-relaxed">
                Catatan admin: {{ $task->rejection_note }}
            </p>
        @endif
    </div>

    @if ($task->approval_status === 'approved')
        <button wire:click="showTaskDetail('{{ $task->id }}')"
            class="mt-auto w-full py-2.5 px-4 rounded-xl text-[10px] font-black uppercase tracking-wider bg-gray-150 dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all text-center">
            Lihat Instruksi & Laporan
        </button>
    @else
        <div class="mt-auto flex gap-2">
            <button wire:click="showTaskDetail('{{ $task->id }}')"
                class="flex-1 py-2.5 px-3 rounded-xl text-[10px] font-black uppercase tracking-wider bg-gray-105 hover:bg-gray-205 dark:bg-gray-800 text-gray-505 dark:text-gray-300 transition-all text-center">
                Detail Instruksi
            </button>
            <button wire:click="selectTaskForCompletion('{{ $task->id }}')"
                class="flex-1 py-2.5 px-3 rounded-xl text-[10px] font-black uppercase tracking-wider text-center transition-all active:scale-95 bg-primary-blue hover:bg-blue-900 text-white shadow-lg shadow-blue-900/10">
                @if ($task->approval_status === 'rejected')
                    Revisi Laporan
                @elseif($task->approval_status === 'pending')
                    Edit Laporan
                @else
                    Kirim Laporan
                @endif
            </button>
        </div>
    @endif
</div>
