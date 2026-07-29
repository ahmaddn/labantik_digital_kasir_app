<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Penjadwalan Kasir</h1>
            <p class="text-gray-400 text-xs md:text-sm font-semibold uppercase tracking-widest mt-1">Atur jadwal jaga kasir per unit TEFA</p>
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
                <button wire:click="openRandomModal" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                    Randomize
                </button>

                <button wire:click="openCreateModal" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase italic tracking-wider transition-all duration-300 shadow-xl shadow-blue-900/10 active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-between bg-white dark:bg-gray-800 px-4 py-4 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-md">
        <button wire:click="previousWeek" class="p-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 rounded-xl text-gray-600 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <span class="text-xs sm:text-sm md:text-lg font-black uppercase tracking-wider text-gray-800 dark:text-white italic text-center">
            {{ $weekRange }}
        </span>
        <button wire:click="nextWeek" class="p-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 rounded-xl text-gray-600 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <!-- Calendar view / list -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @php
            $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        @endphp
        @foreach($daysOfWeek as $index => $dayName)
            @php
                $targetDate = \Carbon\Carbon::parse($currentWeekStart)->addDays($index);
                $daySchedules = $weekSchedules->filter(fn($s) => $s->date->toDateString() === $targetDate->toDateString());
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-gray-100 dark:border-gray-700/50 shadow-lg flex flex-col min-h-[300px]">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                    <h3 class="font-black text-gray-800 dark:text-white text-lg leading-none uppercase italic">{{ $dayName }}</h3>
                    <span class="text-xs text-gray-400 font-bold mt-1 block">{{ $targetDate->translatedFormat('d M Y') }}</span>
                </div>
                
                <div class="flex-1 space-y-3">
                    @forelse($daySchedules as $sched)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 relative group">
                            <h4 class="font-bold text-gray-800 dark:text-white text-sm">{{ $sched->user->name }}</h4>
                            @if($sched->notes)
                                <p class="text-[10px] text-gray-400 font-medium mt-1">{{ $sched->notes }}</p>
                            @endif
                            
                            @if(in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']))
                                <button wire:click="confirmDelete('{{ $sched->id }}')" class="absolute top-2 right-2 p-1 text-gray-400 hover:text-red-500 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 italic text-center py-8">Tidak ada jadwal</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- FullCalendar Section -->
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-6 md:p-8 border border-gray-100 dark:border-gray-700/50">
        <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">Kalender Jadwal Kasir</h2>
        
        <!-- FullCalendar Stylesheet and Scripts -->
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
        
        <div wire:ignore>
            <div id="cashier-calendar" class="dark:text-white"></div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('cashier-calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    locale: 'id',
                    events: {!! $allSchedulesJson !!},
                    eventColor: '#2563eb',
                    eventTextColor: '#ffffff'
                });
                calendar.render();

                // Re-render calendar when Livewire updates components
                window.addEventListener('livewire:navigated', () => {
                    calendar.refetchEvents();
                });

                // Update calendar events dynamically without page refresh
                window.addEventListener('schedule-updated', event => {
                    calendar.removeAllEvents();
                    calendar.addEventSource(event.detail.schedules);
                });
            });
        </script>
    </div>

    <!-- Create Schedule Modal -->
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showCreateModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <h2 class="text-2xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight mb-6">Tambah Jadwal Kasir</h2>
            
            <form wire:submit.prevent="saveSchedule" class="space-y-5">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Pilih Kasir</label>
                    <select wire:model="selectedUserId" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                        <option value="">-- Pilih Kasir --</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedUserId') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Tanggal Tugas</label>
                    <input type="date" wire:model="date" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm font-semibold">
                    @error('date') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Catatan</label>
                    <input type="text" wire:model="notes" placeholder="Shift Pagi / Shift Sore" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm">
                    @error('notes') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showDeleteModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10 text-center">
            <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic mb-4">Hapus Jadwal?</h2>
            <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus jadwal penugasan kasir ini?</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-3 bg-gray-105 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Batal
                </button>
                <button wire:click="deleteSchedule" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Randomize Config Modal -->
    <div x-data="{ show: @entangle('showRandomModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showRandomModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <h2 class="text-2xl font-black text-gray-855 dark:text-white uppercase italic mb-6">Randomize Jadwal</h2>
            
            <form wire:submit.prevent="randomizeSchedules" class="space-y-5">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Tanggal Mulai</label>
                        <input type="date" wire:model="randomizeStartDate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-xs font-semibold">
                        @error('randomizeStartDate') <span class="text-[10px] text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Tanggal Selesai</label>
                        <input type="date" wire:model="randomizeEndDate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-xs font-semibold">
                        @error('randomizeEndDate') <span class="text-[10px] text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Kasir per Hari</label>
                    <input type="number" wire:model="maxCashiersPerDay" min="1" max="10" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm font-semibold">
                    @error('maxCashiersPerDay') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Maksimal Shift per Kasir (Minggu ini)</label>
                    <select wire:model="maxShiftsPerWeek" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm font-semibold">
                        <option value="1">1 Kali Jaga / Kasir</option>
                        <option value="2">Maksimal 2 Kali Jaga / Kasir</option>
                    </select>
                    @error('maxShiftsPerWeek') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="text-[10px] text-gray-400 font-bold leading-relaxed">
                    * Sistem akan mendistribusikan jadwal secara <strong>adil & merata</strong> ke setiap kasir sehingga beban kerja seimbang dan tidak memicu kesenjangan.
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showRandomModal', false)" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Generate Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
