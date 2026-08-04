<div class="space-y-8 pt-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">
            Tugas Saya</h1>
        <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Kelola dan laporkan tugas harian
            yang ditugaskan</p>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700">
        <button wire:click="$set('activeTab', 'today')"
            class="px-6 py-3 font-black text-sm uppercase tracking-wider {{ $activeTab === 'today' ? 'text-primary-blue dark:text-primary-yellow border-b-2 border-primary-blue dark:border-primary-yellow' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            Tugas Hari Ini
        </button>
        <button wire:click="$set('activeTab', 'history')"
            class="px-6 py-3 font-black text-sm uppercase tracking-wider {{ $activeTab === 'history' ? 'text-primary-blue dark:text-primary-yellow border-b-2 border-primary-blue dark:border-primary-yellow' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            Riwayat
        </button>
    </div>

    <!-- Today's Tasks -->
    @if ($activeTab === 'today')
        <div class="space-y-4">
            @forelse($todayAssignments as $assignment)
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 hover:shadow-xl transition-all">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <!-- Task Name & Description -->
                            <div class="mb-3">
                                <h3 class="text-xl font-black text-gray-850 dark:text-white uppercase tracking-tight">
                                    {{ $assignment->taskDefinition->task_name }}
                                </h3>
                                @if ($assignment->taskDefinition->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {!! \Str::limit($assignment->taskDefinition->description, 150) !!}
                                    </p>
                                @endif
                            </div>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full {{ $assignment->taskDefinition->priorityBadgeClass }}">
                                    {{ $assignment->taskDefinition->priority_label }}
                                </span>

                                @if ($assignment->taskDefinition->category)
                                    <span class="text-gray-500 dark:text-gray-400">
                                        {{ $assignment->taskDefinition->category }}
                                    </span>
                                @endif

                                @if ($assignment->taskDefinition->is_routine)
                                    <span class="text-primary-blue dark:text-primary-yellow font-black">RUTIN HARIAN</span>
                                @endif
                            </div>

                            <!-- Deadline -->
                            @php
                                $deadline = $assignment->taskDefinition->deadline_at ?? $assignment->taskDefinition->computed_deadline;
                            @endphp
                            @if ($deadline)
                                <div class="mt-2 text-xs text-gray-500">
                                    ⏱ Deadline: {{ \Carbon\Carbon::parse($deadline)->translatedFormat('d M Y H:i WIB') }}
                                </div>
                            @endif
                        </div>

                        <!-- Status & Action -->
                        <div class="flex flex-col items-end gap-3">
                            @php
                                $latestSubmission = $assignment->submissions->first();
                            @endphp

                            @if (!$latestSubmission)
                                <!-- No submission yet -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                                    BELUM DIKERJAKAN
                                </span>
                                <button wire:click="selectTaskForSubmission('{{ $assignment->id }}')"
                                    class="px-4 py-2 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-lg font-black text-xs uppercase tracking-wider transition-all">
                                    Kerjakan
                                </button>
                            @elseif($latestSubmission->approval_status === 'pending')
                                <!-- Pending Review -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                    ⏳ MENUNGGU ACC
                                </span>
                                <button wire:click="showTaskDetail('{{ $assignment->id }}')"
                                    class="px-4 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-950/40 dark:hover:bg-blue-950/60 text-blue-700 dark:text-blue-400 rounded-lg font-black text-xs uppercase tracking-wider transition-all">
                                    Lihat Status
                                </button>
                            @elseif($latestSubmission->approval_status === 'rejected')
                                <!-- Rejected - Can Revise -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400">
                                    ✗ DITOLAK
                                </span>
                                <button wire:click="selectTaskForSubmission('{{ $assignment->id }}')"
                                    class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-950/40 dark:hover:bg-red-950/60 text-red-700 dark:text-red-400 rounded-lg font-black text-xs uppercase tracking-wider transition-all">
                                    Revisi & Ulang
                                </button>
                            @elseif($latestSubmission->approval_status === 'approved')
                                <!-- Approved -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                    ✓ DISETUJUI
                                </span>
                                <button wire:click="showTaskDetail('{{ $assignment->id }}')"
                                    class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950/40 dark:hover:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 rounded-lg font-black text-xs uppercase tracking-wider transition-all">
                                    Lihat Detail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg p-8 text-center">
                    <div class="text-gray-400 italic font-semibold">
                        Tidak ada tugas untuk hari ini
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    <!-- History -->
    @if ($activeTab === 'history')
        <div class="space-y-4">
            @forelse($historyAssignments as $assignment)
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 opacity-75 hover:opacity-100 transition-all">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-black text-gray-850 dark:text-white uppercase tracking-tight">
                                {{ $assignment->taskDefinition->task_name }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $assignment->taskDefinition->date->translatedFormat('d M Y') }}
                            </p>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            @php
                                $latestSubmission = $assignment->submissions->first();
                            @endphp

                            @if ($latestSubmission)
                                @if ($latestSubmission->approval_status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        ✓ DISETUJUI
                                    </span>
                                @elseif($latestSubmission->approval_status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400">
                                        ✗ DITOLAK
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                        ⏳ PENDING
                                    </span>
                                @endif
                                <button wire:click="showTaskDetail('{{ $assignment->id }}')"
                                    class="px-3 py-1 text-xs font-black text-gray-600 dark:text-gray-400 hover:text-primary-blue dark:hover:text-primary-yellow transition-all">
                                    Detail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-lg p-8 text-center">
                    <div class="text-gray-400 italic font-semibold">
                        Tidak ada riwayat tugas
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-6">
                {{ $historyAssignments->links() }}
            </div>
        </div>
    @endif

    <!-- Task Detail Modal -->
    <div x-data="{ show: @entangle('showTaskDetailModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showTaskDetailModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto">
            
            @if($selectedAssignment)
                @php
                    $taskDef = $selectedAssignment['task_definition'];
                    $submissions = $selectedAssignment['submissions'] ?? [];
                    $latestSubmission = $submissions[0] ?? null;
                @endphp
    
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">
                    {{ $taskDef['task_name'] }}
                </h2>
    
                <!-- Task Info -->
                <div class="space-y-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    @if ($taskDef['description'])
                        <div>
                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Deskripsi</div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {!! nl2br(htmlspecialchars($taskDef['description'])) !!}
                            </div>
                        </div>
                    @endif
    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Prioritas</div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black {{ $taskDef['priority_badge_class'] }}">
                                {{ $taskDef['priority_label'] }}
                            </span>
                        </div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Kategori</div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">{{ $taskDef['category'] ?? 'Umum' }}</div>
                        </div>
                    </div>
    
                    @if ($taskDef['deadline_at'] || $taskDef['computed_deadline'] ?? null)
                        <div>
                            <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Deadline</div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                @if($taskDef['computed_deadline'] ?? null)
                                    {{ \Carbon\Carbon::parse($taskDef['computed_deadline'])->translatedFormat('d M Y H:i WIB') }}
                                @else
                                    {{ \Carbon\Carbon::parse($taskDef['deadline_at'])->translatedFormat('d M Y H:i WIB') }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
    
                <!-- Submission History -->
                @if(!empty($submissions))
                    <div class="mb-6">
                        <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Riwayat Submission</div>
                        <div class="space-y-3">
                            @foreach($submissions as $sub)
                                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <div class="font-bold text-gray-800 dark:text-white">
                                                Version {{ $sub['submission_version'] }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($sub['submitted_at'])->translatedFormat('d M Y H:i WIB') }}
                                            </div>
                                        </div>
                                        @if($sub['approval_status'] === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                                ✓ APPROVED
                                            </span>
                                        @elseif($sub['approval_status'] === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400">
                                                ✗ REJECTED
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                                ⏳ PENDING
                                            </span>
                                        @endif
                                    </div>
    
                                    <div class="text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded mb-2">
                                        {!! nl2br(htmlspecialchars($sub['report'])) !!}
                                    </div>
    
                                    @if($sub['proof_image'])
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $sub['proof_image']) }}" alt="Proof" class="w-full h-auto max-h-32 object-cover rounded">
                                        </div>
                                    @endif
    
                                    @if($sub['approval_status'] === 'rejected' && $sub['rejection_note'])
                                        <div class="mt-2 p-2 bg-red-50 dark:bg-red-950/30 rounded text-sm text-red-700 dark:text-red-400">
                                            <strong>Catatan Penolakan:</strong> {{ $sub['rejection_note'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
    
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="$set('showTaskDetailModal', false)"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg font-black text-xs uppercase transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    
    <!-- Submission Modal -->
    <div x-data="{ show: @entangle('showSubmissionModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showSubmissionModal', false)"></div>
        <div x-show="show" x-transition.scale
            class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto">
            
            @if($selectedAssignment)
                @php
                    $taskDef = $selectedAssignment['task_definition'];
                    $latestSubmission = $selectedAssignment['submissions'][0] ?? null;
                @endphp
    
                <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">
                    {{ $latestSubmission && $latestSubmission['approval_status'] === 'rejected' ? 'Revisi Tugas' : 'Submit Laporan Tugas' }}
                </h2>
    
                <form wire:submit.prevent="submitTaskCompletion" class="space-y-4">
                    <!-- Report -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Laporan Pengerjaan</label>
                        <textarea wire:model="submissionReport" rows="6" placeholder="Jelaskan bagaimana Anda menyelesaikan tugas ini..."
                            class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm"></textarea>
                        @error('submissionReport')
                            <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
    
                    <!-- Proof Image -->
                    @if($taskDef['requires_proof'])
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Bukti Foto / Gambar</label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4">
                                <input wire:model="submissionProofImage" type="file" accept="image/*"
                                    class="w-full text-sm text-gray-500">
                                <p class="text-xs text-gray-400 mt-2">Maksimal 2MB. Format: JPG, PNG, GIF</p>
                            </div>
                            @error('submissionProofImage')
                                <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
    
                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="$set('showSubmissionModal', false)"
                            class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                            {{ $latestSubmission && $latestSubmission['approval_status'] === 'rejected' ? 'Kirim Revisi' : 'Submit Laporan' }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>