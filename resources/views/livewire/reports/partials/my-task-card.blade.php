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
            <span
                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $task->date->translatedFormat('d M Y') }}</span>
            @if ($task->category)
                <span
                    class="text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-700 dark:bg-slate-950/40 dark:text-slate-300 px-2 py-1 rounded-full">{{ $task->category }}</span>
            @endif
            <span
                class="text-[9px] font-black uppercase tracking-widest {{ $task->statusBadgeClass }} px-2 py-1 rounded-full">{{ $task->status_label }}</span>
            <span
                class="text-[9px] font-black uppercase tracking-widest {{ $task->priorityBadgeClass }} px-2 py-1 rounded-full">{{ $task->priority_label }}</span>
            @if ($task->deadline_at)
                <span
                    class="text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-700 dark:bg-slate-950/40 dark:text-slate-300 px-2 py-1 rounded-full">Deadline
                    {{ $task->deadline_at->translatedFormat('d M H:i') }}</span>
            @elseif($task->is_routine && isset($task->computed_deadline) && $task->computed_deadline)
                <span
                    class="text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-700 dark:bg-slate-950/40 dark:text-slate-300 px-2 py-1 rounded-full">Deadline
                    {{ \Carbon\Carbon::parse($task->computed_deadline)->translatedFormat('d M H:i') }}</span>
            @endif
        </div>

        @if ($task->description)
            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold leading-relaxed line-clamp-2">
                {{ $task->description }}</p>
        @endif

        @if ($task->approval_status === 'rejected' && $task->rejection_note)
            <p class="text-[10px] font-bold text-red-500 bg-red-50 dark:bg-red-950/30 p-2.5 rounded-xl leading-relaxed">
                Catatan admin: {{ $task->rejection_note }}
            </p>
        @endif
    </div>

    <button wire:click="selectTaskForCompletion('{{ $task->id }}')"
        class="mt-auto py-2.5 px-4 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all active:scale-95
        {{ $task->approval_status === 'approved'
            ? 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400'
            : ($task->approval_status === 'rejected'
                ? 'bg-red-500 hover:bg-red-600 text-white shadow-lg shadow-red-500/20'
                : 'bg-primary-blue hover:bg-blue-900 text-white shadow-lg shadow-blue-900/10') }}">
        @if ($task->approval_status === 'approved')
            Lihat Detail & Laporan
        @elseif($task->approval_status === 'rejected')
            Revisi & Kirim Ulang
        @elseif($task->approval_status === 'pending')
            Lihat / Perbarui Laporan
        @else
            Selesaikan Tugas
        @endif
    </button>
</div>
