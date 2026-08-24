@php
    $isNajmy = str_contains(strtolower(auth()->user()->name ?? ''), 'najmy');
@endphp
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

        <div class="flex flex-wrap items-center gap-3">
            @if ($isSuperAdmin)
                <div class="w-48">
                    <select wire:model.live="selectedJurusanId"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-blue">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach ($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($isPengelola)
                <button wire:click="toggleRegistration"
                    class="inline-flex items-center px-5 py-3.5 {{ $isRegistrationOpen ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }} text-white rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                    Pendaftaran: {{ $isRegistrationOpen ? 'BUKA' : 'TUTUP' }}
                </button>

                <button wire:click="$set('showFinishConfirmModal', true)"
                    class="inline-flex items-center px-5 py-3.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                    Selesai Seleksi
                </button>
                <button wire:click="openCreateModal"
                    class="inline-flex items-center px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                    Tambah Calon
                </button>
            @endif

            <button wire:click="$set('showWaLinkModal', true)"
                class="inline-flex items-center px-5 py-3.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                DAFTAR BADMINTON
            </button>

            <button wire:click="exportExcel"
                class="inline-flex items-center px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-sm uppercase italic tracking-wider transition-all duration-300 shadow-xl active:scale-95">
                Export Excel (.xlsx)
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div
        class="flex border-b border-gray-200 dark:border-gray-700 gap-4 overflow-x-auto whitespace-nowrap scrollbar-none pb-1">
        <button wire:click="$set('activeTab', 'candidates')"
            class="pb-4 text-xs font-black uppercase tracking-widest border-b-2 {{ $activeTab === 'candidates' ? 'border-primary-blue dark:border-primary-yellow text-primary-blue dark:text-primary-yellow' : 'border-transparent text-gray-400 hover:text-gray-650' }}">
            Pendaftar
        </button>
        @if ($isPengelola)
            <button wire:click="$set('activeTab', 'scoring')"
                class="pb-4 text-xs font-black uppercase tracking-widest border-b-2 {{ $activeTab === 'scoring' ? 'border-primary-blue dark:border-primary-yellow text-primary-blue dark:text-primary-yellow' : 'border-transparent text-gray-400 hover:text-gray-650' }}">
                Input Nilai & Absen
            </button>
        @endif
        <button wire:click="$set('activeTab', 'accepted')"
            class="pb-4 text-xs font-black uppercase tracking-widest border-b-2 {{ $activeTab === 'accepted' ? 'border-primary-blue dark:border-primary-yellow text-primary-blue dark:text-primary-yellow' : 'border-transparent text-gray-400 hover:text-gray-650' }}">
            Lolos Seleksi (15 Besar)
        </button>
    </div>

    <!-- TAB 1: Pendaftar -->
    @if ($activeTab === 'candidates')
        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
            <div class="mb-6 max-w-md w-full">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau kelas..."
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm font-semibold">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">No</th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Nama Lengkap
                            </th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Kelas</th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Jurusan</th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">No HP</th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Penyakit Bawaan
                            </th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Grup WA</th>
                            <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4">
                                Aksi</th>
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
                                        <span
                                            class="px-2.5 py-1 bg-red-50 text-red-600 dark:bg-red-950/20 dark:text-red-400 rounded-full text-[10px] font-black uppercase">
                                            {{ $candidate->illness_history }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 font-semibold italic text-xs">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="py-4 text-sm font-semibold">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:click="toggleJoinedGroup('{{ $candidate->id }}')"
                                            {{ $candidate->is_joined_group ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500">
                                        </div>
                                        <span
                                            class="ml-2 text-xs font-bold {{ $candidate->is_joined_group ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">
                                            {{ $candidate->is_joined_group ? 'Sudah' : 'Belum' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="py-4 text-right pr-4 flex items-center justify-end gap-2">
                                    <button type="button" wire:click="showDetails('{{ $candidate->id }}')"
                                        title="Detail Calon"
                                        class="p-2 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    @if ($isPengelola)
                                        <button type="button"
                                            wire:click="openSingleScoringModal('{{ $candidate->id }}')"
                                            title="Beri Nilai & Absen"
                                            class="p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl transition-all active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </button>
                                    @endif
                                    <button type="button" wire:click="confirmDelete('{{ $candidate->id }}')"
                                        title="Hapus"
                                        class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
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
        </div>
    @endif

    <!-- TAB 2: Input Nilai & Absen -->
    @if ($activeTab === 'scoring' && $isPengelola)
        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-black text-gray-400 dark:text-gray-300 uppercase tracking-widest">Input
                        Penilaian Pekan:</span>
                    <select wire:model.live="selectedWeek"
                        class="px-5 py-3 bg-gray-55 dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-2xl font-black text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-blue transition-all">
                        @for ($w = 1; $w <= 12; $w++)
                            <option value="{{ $w }}">Pekan {{ $w }}</option>
                        @endfor
                    </select>
                </div>

                <button wire:click="saveScoring"
                    class="px-6 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
                    Simpan Pekan Ini
                </button>
            </div>

            <div class="overflow-x-auto rounded-3xl border border-gray-150 dark:border-gray-700/60">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr
                            class="bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 border-b border-gray-150 dark:border-gray-700/80">
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest w-48">Nama Calon</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest text-center w-28">Nilai
                                Akademik</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest text-center w-28">Nilai
                                Perilaku / Attitude</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest text-center w-64">
                                Kehadiran</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest">Alasan Sakit / Izin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach ($scoringCandidates as $candidate)
                            @continue(!$candidate->id)
                            <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-900/20 transition-all">
                                <td class="py-4 px-6">
                                    <div class="text-sm font-black text-gray-900 dark:text-white">
                                        {{ $candidate->full_name }}</div>
                                    <div
                                        class="text-[10px] text-gray-400 dark:text-gray-550 font-black uppercase tracking-wider mt-0.5">
                                        {{ $candidate->class_name }}</div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $statusVal = $attendances[$candidate->id]['status'] ?? 'hadir';
                                        $isNotHadir = $statusVal !== 'hadir';
                                    @endphp
                                    <input type="number" min="0" max="100"
                                        wire:model="scores.{{ $candidate->id }}.score" placeholder="-"
                                        {{ $isNotHadir ? 'disabled' : '' }}
                                        class="w-20 px-3 py-2 text-center bg-gray-55 dark:bg-gray-955 border border-gray-250 dark:border-gray-700 rounded-xl font-black text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-blue disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <input type="number" min="0" max="100"
                                        wire:model="scores.{{ $candidate->id }}.attitude_score" placeholder="-"
                                        {{ $isNotHadir ? 'disabled' : '' }}
                                        class="w-20 px-3 py-2 text-center bg-gray-55 dark:bg-gray-955 border border-gray-250 dark:border-gray-700 rounded-xl font-black text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-blue disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                </td>
                                <td class="py-4 px-6">
                                    <div
                                        class="flex items-center justify-between gap-1 bg-gray-55 dark:bg-gray-955 border border-gray-250 dark:border-gray-700 p-1 rounded-xl max-w-[240px] mx-auto">
                                        @foreach ([
        'hadir' => ['H', 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-md shadow-emerald-500/20', 'Hadir'],
        'sakit' => ['S', 'bg-blue-500 hover:bg-blue-600 text-white shadow-md shadow-blue-500/20', 'Sakit'],
        'izin' => ['I', 'bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/20', 'Izin'],
        'alfa' => ['A', 'bg-red-500 hover:bg-red-650 text-white shadow-md shadow-red-500/20', 'Alfa'],
    ] as $status => $meta)
                                            <button type="button"
                                                @if($isNajmy)
                                                    wire:click="$set('attendances.{{ $candidate->id }}.status', '{{ $status }}')"
                                                @else
                                                    disabled
                                                @endif
                                                class="w-9 h-9 rounded-lg font-black text-xs transition-all flex items-center justify-center
                                                {{ ($attendances[$candidate->id]['status'] ?? 'hadir') === $status
                                                    ? $meta[1]
                                                    : 'text-gray-400 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900' }}
                                                disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="{{ $meta[2] }}">
                                                {{ $meta[0] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $statusVal = $attendances[$candidate->id]['status'] ?? 'hadir';
                                        $showReason = in_array($statusVal, ['sakit', 'izin']);
                                    @endphp
                                    @if ($showReason)
                                        <input type="text" wire:model="attendances.{{ $candidate->id }}.reason"
                                            placeholder="Tulis alasan {{ $statusVal }} (Wajib)..."
                                            {{ !$isNajmy ? 'disabled' : '' }}
                                            class="w-full px-4 py-2 bg-gray-55 dark:bg-gray-955 border border-dashed border-red-300 dark:border-red-800/40 rounded-xl font-semibold text-xs text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-600 italic">Hadir/Alfa tidak
                                            perlu alasan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="w-full sm:w-auto">
                    {{ $scoringCandidates->links() }}
                </div>
                <button wire:click="saveScoring"
                    class="w-full sm:w-auto px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
                    Simpan Pekan Ini
                </button>
            </div>
        </div>
    @endif

    <!-- TAB 3: Lolos Seleksi (15 Besar) -->
    @if ($activeTab === 'accepted')
        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
            <div class="flex items-center gap-4 mb-8">
                <span
                    class="p-3 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </span>
                <div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase italic tracking-tight">Hasil
                        Akhir Seleksi (15 Besar)</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-0.5">Daftar calon anggota dengan ranking nilai
                        akhir terbaik (termasuk potongan nilai ketidakhadiran)</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-3xl border border-gray-150 dark:border-gray-700/60">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr
                            class="bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 border-b border-gray-150 dark:border-gray-700/80">
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest pl-6 w-24 text-center">
                                Rank</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest">Nama Lengkap</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest">Kelas</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest">Jurusan</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest text-center w-36">
                                Rata-rata Nilai</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest text-center w-36">Skor
                                Akhir</th>
                            <th class="py-4 px-6 text-xs font-black uppercase tracking-widest text-right pr-6 w-24">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($acceptedCandidates as $idx => $ac)
                            @php
                                $rank = $idx + 1;
                                $rankStyle = 'bg-gray-100 dark:bg-gray-900 text-gray-500';
                                if ($rank === 1) {
                                    $rankStyle = 'bg-yellow-500 text-white shadow-lg shadow-yellow-500/20';
                                } elseif ($rank === 2) {
                                    $rankStyle =
                                        'bg-slate-300 dark:bg-slate-500 text-gray-800 dark:text-white shadow-lg';
                                } elseif ($rank === 3) {
                                    $rankStyle = 'bg-amber-600 text-white shadow-lg shadow-amber-600/20';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-900/20 transition-all">
                                <td class="py-4 px-6 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full font-black text-xs {{ $rankStyle }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-sm font-black text-gray-900 dark:text-white">{{ $ac->full_name }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-700 dark:text-gray-300 font-bold uppercase">
                                    {{ $ac->class_name }}
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400 font-bold">
                                    {{ $ac->jurusan ? $ac->jurusan->name : 'Global' }}
                                </td>
                                <td class="py-4 px-6 text-center text-sm font-bold text-gray-850 dark:text-gray-200">
                                    {{ number_format($ac->scores->avg('score') ?: 0, 1) }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 bg-yellow-500/10 text-yellow-600 dark:text-primary-yellow rounded-2xl text-xs font-black tracking-widest border border-yellow-500/20">
                                        {{ number_format($ac->final_score, 1) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right pr-6">
                                    <button type="button" wire:click="showDetails('{{ $ac->id }}')"
                                        title="Detail Seleksi"
                                        class="p-2.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl transition-all active:scale-95 shadow-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="py-12 text-center text-gray-400 dark:text-gray-500 italic font-bold">
                                    Proses seleksi belum diselesaikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $acceptedCandidates->links() }}
            </div>
        </div>
    @endif

    <!-- Detail Candidate Modal with Attendance Calendar -->
    @if ($showDetailModal && $detailCandidate)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/70 backdrop-blur-xs">
            <div
                class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">
                            {{ $detailCandidate->full_name }}</h2>
                        <p class="text-xs text-gray-400 font-bold uppercase mt-1">Kelas
                            {{ $detailCandidate->class_name }} |
                            {{ $detailCandidate->jurusan ? $detailCandidate->jurusan->name : 'Global' }}</p>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="p-2 text-gray-400 hover:text-gray-650">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Left Column: Details -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-5 rounded-2xl space-y-4">
                            <h4 class="text-xs font-black uppercase text-gray-400 tracking-wider">Biodata Calon</h4>
                            <div>
                                <div class="text-[9px] font-black uppercase text-gray-400">No HP</div>
                                <div class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">
                                    {{ $detailCandidate->phone_number }}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black uppercase text-gray-400">No HP Orang Tua</div>
                                <div class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">
                                    {{ $detailCandidate->parent_phone_number }}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black uppercase text-gray-400">Penyakit Bawaan</div>
                                <div class="text-xs font-bold text-red-500 mt-0.5">
                                    {{ $detailCandidate->illness_history ?: 'Tidak ada' }}</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 p-5 rounded-2xl space-y-4">
                            <h4 class="text-xs font-black uppercase text-gray-400 tracking-wider">Riwayat Nilai Seleksi
                            </h4>
                            <div class="max-h-48 overflow-y-auto space-y-2.5 pr-2">
                                @forelse($detailCandidate->scores as $score)
                                    <div
                                        class="flex items-center justify-between text-xs border-b border-gray-150 dark:border-gray-800 pb-1.5">
                                        <span class="font-bold text-gray-500">Pekan {{ $score->week_number }}</span>
                                        <span class="font-black text-gray-800 dark:text-white">{{ $score->score }}
                                            Poin</span>
                                    </div>
                                @empty
                                    <div class="text-xs text-gray-400 italic">Belum ada input nilai</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: FullCalendar -->
                    <div class="md:col-span-2 space-y-6">
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-[2rem] border border-gray-150 dark:border-gray-800">
                            <h4 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-4">Kalender Absensi
                                Seleksi</h4>

                            <!-- Calendar container -->
                            <div wire:ignore class="w-full">
                                <div id="candidate-attendance-calendar" class="dark:text-white w-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-6 mt-6 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="$set('showDetailModal', false)"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Tutup
                    </button>
                </div>
            </div>

            <!-- FullCalendar Scripts (only runs if calendar script isn't loaded) -->
            <script>
                if (typeof FullCalendar === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
                    script.onload = initCandidateCalendar;
                    document.head.appendChild(script);
                } else {
                    // Small delay to ensure HTML is rendered
                    setTimeout(initCandidateCalendar, 50);
                }

                function initCandidateCalendar() {
                    const calendarEl = document.getElementById('candidate-attendance-calendar');
                    if (!calendarEl) return;

                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        initialDate: '2026-08-01', // Lock to selection start date
                        height: 'auto',
                        headerToolbar: {
                            left: 'prev,next',
                            center: 'title',
                            right: 'dayGridMonth'
                        },
                        locale: 'id',
                        events: {!! $detailAttendancesJson !!},
                    });
                    calendar.render();
                }
            </script>
        </div>
    @endif

    <!-- WhatsApp Link Modal -->
    @if ($showWaLinkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 animate-in zoom-in-95 duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3
                        class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white leading-none">
                        Link Grup WhatsApp
                    </h3>
                    <button wire:click="$set('showWaLinkModal', false)"
                        class="p-2.5 text-gray-400 hover:text-gray-650">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveWaLink" class="space-y-5">
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Input
                            Link Grup WhatsApp</label>
                        <input type="text" wire:model="waGroupLink" placeholder="https://chat.whatsapp.com/..."
                            class="w-full px-5 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white">
                        @error('waGroupLink')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
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

    <!-- Delete Confirmation Modal -->
    <div x-data="{ showDelete: @entangle('showDeleteModal') }" x-show="showDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="showDelete" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showDeleteModal', false)"></div>
        <div x-show="showDelete" x-transition.scale
            class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <div class="text-center mb-6">
                <div
                    class="w-16 h-16 bg-red-50 dark:bg-red-950/30 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-855 dark:text-white uppercase italic tracking-tight">Hapus
                    Calon Anggota</h2>
                <p class="text-gray-400 text-sm font-medium mt-2">Apakah Anda yakin ingin menghapus data calon anggota
                    ini secara permanen? Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <div class="flex justify-center gap-3">
                <button wire:click="$set('showDeleteModal', false)"
                    class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteCandidate"
                    class="flex-1 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all shadow-xl shadow-red-500/10">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Create Candidate Modal -->
    <div x-data="{ showCreate: @entangle('showCreateModal') }" x-show="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="showCreate" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showCreateModal', false)"></div>
        <div x-show="showCreate" x-transition.scale
            class="relative w-full bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 max-h-[90vh] overflow-y-auto"
            style="max-width: 900px;">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">
                    Tambah Calon Anggota
                </h3>
                <button wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-650">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="storeCandidate" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Nama
                            Lengkap</label>
                        <input type="text" wire:model="new_full_name" required
                            class="w-full px-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white">
                        @error('new_full_name')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Kelas</label>
                        <input type="text" wire:model="new_class_name" required placeholder="Contoh: X RPL 1"
                            class="w-full px-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white">
                        @error('new_class_name')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Jurusan
                            Tujuan</label>
                        <select wire:model="new_jurusan_id"
                            class="w-full px-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach ($jurusans as $j)
                                <option value="{{ $j->id }}">{{ $j->name }}</option>
                            @endforeach
                        </select>
                        @error('new_jurusan_id')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Penyakit
                            Bawaan</label>
                        <input type="text" wire:model="new_illness_history"
                            placeholder="Tulis jika ada penyakit bawaan..."
                            class="w-full px-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white">
                        @error('new_illness_history')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">No
                            HP Calon</label>
                        <input type="text" wire:model="new_phone_number" required
                            class="w-full px-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white">
                        @error('new_phone_number')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">No
                            HP Orang Tua</label>
                        <input type="text" wire:model="new_parent_phone_number" required
                            class="w-full px-4 py-3.5 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white">
                        @error('new_parent_phone_number')
                            <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Alamat
                        Rumah</label>
                    <textarea wire:model="new_address" required rows="3"
                        class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white"></textarea>
                    @error('new_address')
                        <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Alasan
                        Masuk Labantik</label>
                    <textarea wire:model="new_reason" rows="3"
                        class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl font-bold text-sm text-gray-800 dark:text-white"></textarea>
                    @error('new_reason')
                        <span class="text-xs text-red-500 font-bold mt-1 ml-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-t-gray-100 dark:border-t-gray-700">
                    <button type="button" wire:click="$set('showCreateModal', false)"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-650 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-8 py-3 bg-primary-blue text-primary-yellow rounded-xl shadow-xl font-black text-xs uppercase tracking-wider transition-all">
                        Simpan Calon
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Finish Selection Confirmation Modal -->
    <div x-data="{ showFinish: @entangle('showFinishConfirmModal') }" x-show="showFinish" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak>
        <div x-show="showFinish" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showFinishConfirmModal', false)"></div>
        <div x-show="showFinish" x-transition.scale
            class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-center animate-in zoom-in-95 duration-200">
            <div
                class="w-16 h-16 bg-amber-100 dark:bg-amber-950/20 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white mb-2">
                Selesaikan Seleksi?
            </h3>
            <p class="text-sm font-semibold text-gray-400 leading-relaxed mb-6">
                Sistem akan menghitung nilai rata-rata tiap peserta (nilai akademik + nilai attitude) dari seluruh
                penginput, memberikan bonus kehadiran (+5 poin per Hadir), memotong penalti absensi (-10 poin per Alfa,
                -2 poin per Izin), dan meloloskan 15 calon terbaik secara otomatis. Tindakan ini akan memperbarui status
                pendaftar.
            </p>
            <div class="flex gap-4">
                <button wire:click="$set('showFinishConfirmModal', false)"
                    class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-transform">
                    Batal
                </button>
                <button wire:click="finishSelection" @click="showFinish = false"
                    class="flex-1 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-amber-500/20 transition-all">
                    Ya, Selesaikan
                </button>
            </div>
        </div>
    </div>

    <!-- Single Candidate Scoring Modal (Weeks 1-12) -->
    <div x-data="{ showSingleScoring: @entangle('showSingleScoringModal') }" x-show="showSingleScoring"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="showSingleScoring" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs"
            wire:click="$set('showSingleScoringModal', false)"></div>
        <div x-show="showSingleScoring" x-transition.scale
            class="relative w-full max-w-5xl bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-105 dark:border-gray-700 z-10 max-h-[85vh] flex flex-col animate-in zoom-in-95 duration-200">
            <div class="flex justify-between items-center mb-6 flex-shrink-0">
                <div>
                    <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">Input
                        Nilai & Absensi Calon</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase mt-1">Peserta: <span
                            class="text-gray-800 dark:text-white font-black">{{ $scoringCandidate ? $scoringCandidate->full_name : '' }}</span>
                        ({{ $scoringCandidate ? $scoringCandidate->class_name : '' }})</p>
                </div>
                <button wire:click="$set('showSingleScoringModal', false)" class="text-gray-400 hover:text-gray-650">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="saveSingleScoring"
                class="flex-grow overflow-y-auto pr-2 space-y-4 flex flex-col justify-between">
                <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700/60">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead>
                            <tr
                                class="bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                <th class="py-3 px-4 text-xs font-black uppercase tracking-widest w-24">Pekan</th>
                                <th class="py-3 px-4 text-xs font-black uppercase tracking-widest text-center w-28">
                                    Nilai Akademik</th>
                                <th class="py-3 px-4 text-xs font-black uppercase tracking-widest text-center w-28">
                                    Nilai Attitude</th>
                                <th class="py-3 px-4 text-xs font-black uppercase tracking-widest text-center w-64">
                                    Kehadiran</th>
                                <th class="py-3 px-4 text-xs font-black uppercase tracking-widest">Alasan Sakit / Izin
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-55 dark:divide-gray-700/50">
                            @for ($w = 1; $w <= 12; $w++)
                                <tr class="hover:bg-gray-50/20 dark:hover:bg-gray-900/10">
                                    <td class="py-3 px-4 text-sm font-bold text-gray-800 dark:text-white">Pekan
                                        {{ $w }}</td>
                                    @php
                                        $statusVal = $singleAttendances[$w]['status'] ?? 'hadir';
                                        $isNotHadir = $statusVal !== 'hadir';
                                    @endphp
                                    <td class="py-3 px-4 text-center">
                                        <input type="number" min="0" max="100"
                                            wire:model="singleScores.{{ $w }}.score" placeholder="-"
                                            {{ $isNotHadir ? 'disabled' : '' }}
                                            class="w-20 px-3 py-1.5 text-center bg-gray-55 dark:bg-gray-955 border border-gray-250 dark:border-gray-700 rounded-xl font-black text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-blue disabled:opacity-30 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <input type="number" min="0" max="100"
                                            wire:model="singleScores.{{ $w }}.attitude_score"
                                            placeholder="-" {{ $isNotHadir ? 'disabled' : '' }}
                                            class="w-20 px-3 py-1.5 text-center bg-gray-55 dark:bg-gray-955 border border-gray-250 dark:border-gray-700 rounded-xl font-black text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-blue disabled:opacity-30 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div
                                            class="flex items-center justify-between gap-1 bg-gray-55 dark:bg-gray-955 border border-gray-250 dark:border-gray-700 p-1 rounded-xl max-w-[240px] mx-auto">
                                            @foreach (['hadir' => 'H', 'sakit' => 'S', 'izin' => 'I', 'alfa' => 'A'] as $status => $label)
                                                @php
                                                    $activeClass =
                                                        ($singleAttendances[$w]['status'] ?? 'hadir') === $status
                                                            ? ($status === 'hadir'
                                                                ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-md'
                                                                : ($status === 'sakit'
                                                                    ? 'bg-blue-500 hover:bg-blue-600 text-white shadow-md'
                                                                    : ($status === 'izin'
                                                                        ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-md'
                                                                        : 'bg-red-500 hover:bg-red-650 text-white shadow-md')))
                                                            : 'text-gray-400 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900';
                                                @endphp
                                                <button type="button"
                                                    @if($isNajmy)
                                                        wire:click="$set('singleAttendances.{{ $w }}.status', '{{ $status }}')"
                                                    @else
                                                        disabled
                                                    @endif
                                                    class="w-9 h-9 rounded-lg font-black text-xs transition-all flex items-center justify-center {{ $activeClass }} disabled:opacity-50 disabled:cursor-not-allowed">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $statusVal = $singleAttendances[$w]['status'] ?? 'hadir';
                                            $showReason = in_array($statusVal, ['sakit', 'izin']);
                                        @endphp
                                        @if ($showReason)
                                            <input type="text"
                                                wire:model="singleAttendances.{{ $w }}.reason"
                                                placeholder="Tulis alasan {{ $statusVal }} (Wajib)..."
                                                {{ !$isNajmy ? 'disabled' : '' }}
                                                class="w-full px-3 py-1.5 bg-gray-55 dark:bg-gray-955 border border-dashed border-red-300 dark:border-red-800/40 rounded-xl font-semibold text-xs text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-600 italic">Hadir/Alfa
                                                tidak perlu alasan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/50 flex-shrink-0">
                    <button type="button" wire:click="$set('showSingleScoringModal', false)"
                        class="px-6 py-3.5 bg-gray-100 dark:bg-gray-700 text-gray-650 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-8 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-lg shadow-emerald-500/20">
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
