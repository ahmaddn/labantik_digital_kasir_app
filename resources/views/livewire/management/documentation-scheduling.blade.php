<div class="space-y-8 pt-6">
    @if(isset($dbError) && $dbError)
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center gap-3 text-xs font-bold">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="uppercase font-black tracking-wider">Perhatian: Terjadi Kesalahan Database Server</p>
                <p class="font-normal mt-0.5">{{ $dbError }}</p>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-2xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Penjadwalan Dokumentasi</h1>
            <p class="text-gray-400 text-xs md:text-sm font-semibold uppercase tracking-widest mt-1">Kelola penugasan dokumentasi kegiatan khusus anggota kasir</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if(session('active_role_name') === 'superadmin')
                <div class="w-full sm:w-64">
                    <select wire:model.live="selectedJurusanId" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']))
                <button wire:click="openCreateActivityModal" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-emerald-900/10 active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Buat Kegiatan
                </button>

                @if($selectedActivityId)
                    <button wire:click="openRandomModal" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-amber-900/10 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                        Randomize
                    </button>

                    <button wire:click="openCreateScheduleModal" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah Penugasan
                    </button>

                    <button wire:click="exportExcel" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-green-900/10 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </button>

                    <button type="button" onclick="exportDocScheduleToImage()" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-indigo-900/10 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Gambar
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Activity Selector Card -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex-1 max-w-xl">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Kegiatan Dokumentasi</label>
                <select wire:model.live="selectedActivityId" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach($activities as $act)
                        <option value="{{ $act->id }}">{{ $act->title }} ({{ $act->start_date->format('d/m/Y') }} - {{ $act->end_date->format('d/m/Y') }})</option>
                    @endforeach
                </select>
            </div>

            @if($activeActivity && in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']))
                <div class="flex items-center gap-2 self-end md:self-center">
                    <button wire:click="openEditActivityModal({{ $activeActivity->id }})" class="p-2.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Event
                    </button>
                    <button wire:click="confirmDeleteActivity({{ $activeActivity->id }})" class="p-2.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Event
                    </button>
                </div>
            @endif
        </div>

        @if($activeActivity)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div class="bg-blue-50 dark:bg-blue-950/30 rounded-2xl p-4 border border-blue-100 dark:border-blue-900/50">
                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-500 block mb-1">Nama Kegiatan</span>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white leading-snug">{{ $activeActivity->title }}</h2>
                </div>
                <div class="bg-amber-50 dark:bg-amber-950/30 rounded-2xl p-4 border border-amber-100 dark:border-amber-900/50">
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-1">Rentang Tanggal</span>
                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                        {{ $activeActivity->start_date->translatedFormat('d M Y') }} s/d {{ $activeActivity->end_date->translatedFormat('d M Y') }}
                    </p>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-950/30 rounded-2xl p-4 border border-emerald-100 dark:border-emerald-900/50">
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-1">Keterangan / Detail</span>
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 line-clamp-2">
                        {{ $activeActivity->description ?: 'Tidak ada keterangan khusus.' }}
                    </p>
                </div>
            </div>
        @else
            <div class="py-8 text-center bg-gray-50 dark:bg-gray-800/40 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm font-bold text-gray-600 dark:text-gray-400">Belum ada kegiatan dokumentasi yang dipilih.</p>
                <p class="text-xs text-gray-400 mt-1">Pilih kegiatan di atas atau buat kegiatan baru untuk mulai mengatur penugasan.</p>
            </div>
        @endif
    </div>

    <!-- Schedules Grid View -->
    @if($activeActivity)
        <div id="doc-schedule-capture-area" class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-md space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div>
                    <h2 class="text-xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Jadwal Tugas Dokumentasi</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Total Penugasan: {{ $activitySchedules->count() }} Anggota Kasir</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($daysList as $dayCarbon)
                    @php
                        $dayStr = $dayCarbon->toDateString();
                        $dayScheds = $activitySchedules->filter(fn($s) => $s->date->toDateString() === $dayStr);
                        $isToday = $dayCarbon->isToday();
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-3xl p-5 border {{ $isToday ? 'border-primary-blue ring-2 ring-primary-blue/20' : 'border-gray-100 dark:border-gray-700/50' }} shadow-sm flex flex-col min-h-[260px]">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="font-black text-gray-800 dark:text-white text-base uppercase italic leading-tight">{{ $dayCarbon->translatedFormat('l') }}</h3>
                                <span class="text-xs text-gray-400 font-bold mt-0.5 block">{{ $dayCarbon->translatedFormat('d M Y') }}</span>
                            </div>
                            @if($isToday)
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider bg-primary-blue text-white rounded-full">Hari Ini</span>
                            @endif
                        </div>

                        <div class="flex-1 space-y-3">
                            @forelse($dayScheds as $sched)
                                <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-150 dark:border-gray-800 shadow-sm space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-gray-800 dark:text-white text-sm truncate leading-tight">{{ $sched->user->name }}</h4>

                                        @if($sched->user->grade_level)
                                            @php
                                                $gLevel = (string) $sched->user->grade_level;
                                                $gBadge = match($gLevel) {
                                                    '12' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                                    '11' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                                                    '10' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                                    default => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-[9px] font-black rounded-lg border {{ $gBadge }} shrink-0">
                                                Tingkat {{ $gLevel }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-1 border-t border-gray-100 dark:border-gray-800">
                                        <span class="text-[10px] font-medium truncate max-w-[140px]" title="{{ $sched->notes }}">{{ $sched->notes }}</span>

                                        @if(in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']))
                                            <button wire:click="confirmDeleteSchedule({{ $sched->id }})" class="p-1 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-gray-400 hover:text-rose-500 rounded-lg transition-all" title="Hapus Tugas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-gray-400 text-xs italic bg-white/50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                                    Tidak ada tugas
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Cashier History Recap Table -->
    <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-md space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div>
                <h2 class="text-xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Rekapitulasi Riwayat Dokumentasi</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Akumulasi total penugasan dokumentasi per anggota kasir untuk pembagian adil</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 text-[11px] font-black uppercase text-gray-400 tracking-wider">
                        <th class="py-3 px-4">Nama Anggota Kasir</th>
                        <th class="py-3 px-4">Tingkatan</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4 text-center">Total Riwayat Tugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs font-semibold">
                    @forelse($cashierStats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="py-3 px-4 text-gray-800 dark:text-white font-bold">{{ $stat['name'] }}</td>
                            <td class="py-3 px-4">
                                @if($stat['grade_level'])
                                    @php
                                        $gBadge = match((string)$stat['grade_level']) {
                                            '12' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                            '11' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                                            '10' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                            default => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-lg border {{ $gBadge }}">
                                        Tingkat {{ $stat['grade_level'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Belum diatur</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-500 dark:text-gray-400">{{ $stat['email'] }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full font-black text-xs">
                                    {{ $stat['doc_count'] }} Shift
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400 italic">Belum ada kasir murni yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Activity Form -->
    @if($showActivityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white uppercase tracking-tight">
                        {{ $editingActivityId ? 'Edit Kegiatan Dokumentasi' : 'Buat Kegiatan Dokumentasi Baru' }}
                    </h3>
                    <button wire:click="$set('showActivityModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveActivity" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Kegiatan</label>
                        <input type="text" wire:model="activityTitle" placeholder="Contoh: Liputan Pameran TEFA / Event Olahraga" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                        @error('activityTitle') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                            <input type="date" wire:model="activityStartDate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                            @error('activityStartDate') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                            <input type="date" wire:model="activityEndDate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                            @error('activityEndDate') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keterangan / Catatan Tambahan (Opsional)</label>
                        <textarea wire:model="activityDescription" rows="3" placeholder="Contoh: Dokumentasi foto & video kegiatan boot exhibition..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue"></textarea>
                        @error('activityDescription') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showActivityModal', false)" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-xs uppercase tracking-wider rounded-xl">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-blue hover:bg-blue-900 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-blue-900/20">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: Create Schedule Form -->
    @if($showScheduleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 max-w-md w-full border border-gray-100 dark:border-gray-800 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white uppercase tracking-tight">Tambah Penugasan Manual</h3>
                    <button wire:click="$set('showScheduleModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveSchedule" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Anggota Kasir</label>
                        <select wire:model="selectedUserId" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                            <option value="">-- Pilih Kasir --</option>
                            @foreach($cashiers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} {{ $c->grade_level ? '(Tingkat ' . $c->grade_level . ')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('selectedUserId') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal Penugasan</label>
                        <input type="date" wire:model="date" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                        @error('date') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan Tugas</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Foto & Video liputan utama" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                        @error('notes') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showScheduleModal', false)" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-xs uppercase tracking-wider rounded-xl">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary-blue hover:bg-blue-900 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-blue-900/20">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: Randomize Schedule Form -->
    @if($showRandomModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white uppercase tracking-tight">Randomize Penugasan Dokumentasi</h3>
                    <button wire:click="$set('showRandomModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="bg-blue-50 dark:bg-blue-950/30 rounded-2xl p-4 border border-blue-100 dark:border-blue-900/50 text-xs text-blue-700 dark:text-blue-300 space-y-1">
                    <p class="font-bold uppercase tracking-wider">Algoritma Anti-Bentrok & Pemerataan Shift:</p>
                    <p>1. Memprioritaskan kasir dengan <strong>riwayat penugasan paling sedikit</strong>.</p>
                    <p>2. Otomatis <strong>menghindari kasir yang sedang piket kasir</strong> pada tanggal tersebut agar tidak bentrok.</p>
                </div>

                <form wire:submit.prevent="randomizeSchedules" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                            <input type="date" wire:model="randomizeStartDate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                            <input type="date" wire:model="randomizeEndDate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-blue">
                        </div>
                    </div>

                    <!-- Grade Level Quotas Toggle -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-200 dark:border-gray-700/60 space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="useGradeQuotas" class="w-4 h-4 rounded text-primary-blue border-gray-300 focus:ring-primary-blue">
                            <span class="text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wider">Atur Kuota per Tingkatan (12, 11, 10, dll)</span>
                        </label>

                        @if($useGradeQuotas)
                            <div class="grid grid-cols-3 gap-3 pt-2">
                                @foreach($availableGrades as $g)
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-1">Tingkat {{ $g }} (Orang/Hari)</label>
                                        <input type="number" min="0" max="10" wire:model="gradeQuotas.{{ $g }}" class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-800 dark:text-white">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jumlah Kasir per Hari</label>
                                <input type="number" min="1" max="10" wire:model="maxCashiersPerDay" class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-800 dark:text-white">
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showRandomModal', false)" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-xs uppercase tracking-wider rounded-xl">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-amber-900/20">Generate Random</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: Delete Schedule Confirmation -->
    @if($showDeleteScheduleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 max-w-sm w-full border border-gray-100 dark:border-gray-800 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white uppercase tracking-tight">Hapus Penugasan Ini?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Penugasan kasir ini akan dihapus dari jadwal kegiatan.</p>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button wire:click="$set('showDeleteScheduleModal', false)" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-xs uppercase tracking-wider rounded-xl">Batal</button>
                    <button wire:click="deleteSchedule" class="px-6 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-rose-900/20">Hapus</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Delete Activity Confirmation -->
    @if($showDeleteActivityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 max-w-sm w-full border border-gray-100 dark:border-gray-800 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white uppercase tracking-tight">Hapus Kegiatan Dokumentasi?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Seluruh jadwal penugasan yang terkait dengan kegiatan ini juga akan ikut terhapus.</p>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button wire:click="$set('showDeleteActivityModal', false)" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-xs uppercase tracking-wider rounded-xl">Batal</button>
                    <button wire:click="deleteActivity" class="px-6 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-rose-900/20">Hapus Event</button>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
<script>
    function exportDocScheduleToImage() {
        const element = document.getElementById('doc-schedule-capture-area');
        if (!element) return;

        htmlToImage.toPng(element, {
            quality: 0.95,
            pixelRatio: 2,
            backgroundColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
            skipFonts: true,
            filter: (node) => {
                if (node.tagName === 'BUTTON' || (node.classList && node.classList.contains('capture-exclude'))) {
                    return false;
                }
                return true;
            }
        })
        .then(function (dataUrl) {
            const link = document.createElement('a');
            let titleStr = '{{ $activeActivity ? \Illuminate\Support\Str::slug($activeActivity->title) : "Dokumentasi" }}';
            link.download = 'Jadwal_Dokumentasi_' + titleStr + '.png';
            link.href = dataUrl;
            link.click();
        })
        .catch(function (error) {
            console.error('Oops, export image error:', error);
        });
    }
</script>
