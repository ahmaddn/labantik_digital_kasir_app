<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Laporan Absensi & Shift</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Daftar kehadiran & laporan closing harian kasir</p>
        </div>
        <div class="flex items-center gap-3">
            @if(session('active_role_name') === 'superadmin')
                <div class="w-64">
                    <select wire:model.live="selectedJurusanId" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800 p-4 md:p-6">
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari nama kasir..." class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm font-semibold">
            </div>

            <div>
                <input type="date" wire:model.live="dateFrom" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm font-semibold">
            </div>

            <div>
                <input type="date" wire:model.live="dateTo" class="w-full px-4 py-3 bg-gray-55 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white text-sm font-semibold">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">Kasir</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-center">Total Kehadiran</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-emerald-500 text-center">Tepat Waktu</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-rose-500 text-center">Terlambat</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-amber-500 text-center">Pulang Cepat</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($cashiers as $cashier)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4">
                                <div class="text-sm font-bold text-gray-800 dark:text-white">{{ $cashier->name }}</div>
                                <div class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $cashier->email }}</div>
                            </td>
                            <td class="py-4 text-center text-sm font-bold text-gray-800 dark:text-white">
                                {{ $cashier->total_attendances }} Kali
                            </td>
                            <td class="py-4 text-center text-sm font-bold text-emerald-500">
                                {{ $cashier->present_count }} Kali
                            </td>
                            <td class="py-4 text-center text-sm font-bold text-rose-500">
                                {{ $cashier->late_count }} Kali
                            </td>
                            <td class="py-4 text-center text-sm font-bold text-amber-500">
                                {{ $cashier->early_checkout_count }} Kali
                            </td>
                            <td class="py-4 text-right pr-4">
                                <button wire:click="showDetails('{{ $cashier->id }}')" class="px-4 py-2 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase italic tracking-wider transition-all">
                                    Detail Absen
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 italic font-semibold">Tidak ada data kasir ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $cashiers->links() }}
        </div>
    </div>

    <!-- Details Attendance Modal -->
    <div x-data="{ show: @entangle('showDetailModal') }" x-show="show" class="fixed inset-0 z-[200] flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showDetailModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-5 border border-gray-100 dark:border-gray-700 z-10 max-h-[85vh] flex flex-col">
            <div class="flex justify-between items-center mb-6 flex-shrink-0">
                <div>
                    <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">Rincian Absensi Kasir</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase mt-1">Nama: {{ $detailUserName }}</p>
                </div>
                <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="overflow-y-auto flex-grow pr-2">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 text-xs font-black uppercase text-gray-400">Tanggal</th>
                            <th class="pb-3 text-xs font-black uppercase text-gray-400">Buka (Opening)</th>
                            <th class="pb-3 text-xs font-black uppercase text-gray-400">Tutup (Closing)</th>
                            <th class="pb-3 text-xs font-black uppercase text-gray-400">Status</th>
                            <th class="pb-3 text-xs font-black uppercase text-gray-400 text-right">Laporan Shift</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($detailAttendances as $att)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                <td class="py-4 text-sm font-bold text-gray-800 dark:text-white">
                                    {{ $att->date->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-4">
                                    <div class="text-sm font-bold text-gray-800 dark:text-white">
                                        {{ $att->clock_in ? $att->clock_in->format('H:i') . ' WIB' : '-' }}
                                    </div>
                                    <div class="text-[9px] text-gray-400 font-bold mt-0.5">Uang Awal: Rp{{ number_format($att->opening_cash, 0, ',', '.') }}</div>
                                </td>
                                <td class="py-4">
                                    <div class="text-sm font-bold text-gray-800 dark:text-white">
                                        {{ $att->clock_out ? $att->clock_out->format('H:i') . ' WIB' : '-' }}
                                    </div>
                                    <div class="text-[9px] text-gray-400 font-bold mt-0.5">Uang Akhir: Rp{{ number_format($att->closing_cash, 0, ',', '.') }}</div>
                                </td>
                                <td class="py-4">
                                    @php
                                        $statusText = $att->status;
                                        $color = 'bg-gray-50 text-gray-500';

                                        if (str_contains($att->status, '_')) {
                                            $parts = explode('_', $att->status);
                                            $in = $parts[0];
                                            $out = $parts[1];

                                            $inText = $in === 'late' ? 'Terlambat' : 'Tepat Waktu';
                                            $outText = $out === 'early_checkout' ? 'Pulang Cepat' : ($out === 'overtime' ? 'Lembur' : 'Tepat Waktu');
                                            $statusText = "In: {$inText} | Out: {$outText}";

                                            if ($in === 'late' && $out === 'early_checkout') {
                                                $color = 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-450';
                                            } elseif ($in === 'late' || $out === 'early_checkout') {
                                                $color = 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400';
                                            } else {
                                                $color = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400';
                                            }
                                        } else {
                                            if ($att->status === 'present') { 
                                                $color = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400'; 
                                                $statusText = 'Tepat Waktu';
                                            }
                                            elseif ($att->status === 'late') { 
                                                $color = 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-450'; 
                                                $statusText = 'Terlambat';
                                            }
                                            elseif ($att->status === 'early_checkout') { 
                                                $color = 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-450'; 
                                                $statusText = 'Pulang Cepat';
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $color }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="py-4 text-right">
                                    @if($att->clock_out)
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button wire:click="viewReport('{{ $att->id }}')" class="px-3 py-1.5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-lg font-black text-[10px] uppercase italic tracking-wider transition-all">
                                                Lihat
                                            </button>
                                            @if(($att->user_id === auth()->id() && $att->edit_count < 1) || session('active_role_name') === 'superadmin')
                                                <button wire:click="openEditReport('{{ $att->id }}')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-black text-[10px] uppercase italic tracking-wider transition-all">
                                                    Edit
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum Closing</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 italic">Tidak ada data rincian absensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end pt-6 flex-shrink-0 border-t border-gray-100 dark:border-gray-700/50 mt-4">
                <button wire:click="$set('showDetailModal', false)" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div x-data="{ show: @entangle('showReportModal') }" x-show="show" class="fixed inset-0 z-[300] flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showReportModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-5 border border-gray-100 dark:border-gray-700 z-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-gray-855 dark:text-white uppercase italic tracking-tight">Laporan Closing Shift</h2>
                <button wire:click="$set('showReportModal', false)" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">Oleh Kasir: <span class="text-gray-800 dark:text-white font-black">{{ $activeReportUser }}</span></div>
                <div class="bg-gray-50 dark:bg-gray-900 p-5 rounded-2xl border border-gray-100 dark:border-gray-800 text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                    {{ $activeReportText }}
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <button wire:click="$set('showReportModal', false)" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Report Modal -->
    <div x-data="{ show: @entangle('editingAttendanceId') }" x-show="show" class="fixed inset-0 z-[300] flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('editingAttendanceId', null)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-5 border border-gray-100 dark:border-gray-700 z-10">
            <h2 class="text-xl font-black text-gray-855 dark:text-white uppercase italic mb-6">Edit Laporan Closing Shift</h2>
            
            <form wire:submit.prevent="updateReport" class="space-y-5">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Isi Laporan Aktivitas</label>
                    <textarea wire:model="editedClosingReport" rows="6" class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white" required placeholder="Tulis aktivitas shift Anda disini..."></textarea>
                </div>

                <div class="text-[10px] text-amber-500 font-bold italic">
                    * Perhatian: Pengeditan laporan oleh Kasir dibatasi maksimal hanya 1 kali saja.
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('editingAttendanceId', null)" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 md:p-5 border border-gray-100 dark:border-gray-700/50 mt-8">
        <h2 class="text-xl font-black text-gray-855 dark:text-white uppercase italic tracking-tight mb-6">Kalender Absensi Kasir</h2>
        
        <!-- FullCalendar Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
        
        <style>
            /* Responsive styling for FullCalendar toolbar on mobile */
            @media (max-width: 640px) {
                .fc .fc-toolbar {
                    flex-direction: column !important;
                    gap: 0.75rem !important;
                    align-items: center !important;
                }
                .fc .fc-toolbar-title {
                    font-size: 1.25rem !important;
                    text-align: center !important;
                }
                .fc .fc-button-group {
                    display: inline-flex !important;
                }
                .fc .fc-toolbar-chunk {
                    display: flex !important;
                    justify-content: center !important;
                    width: 100% !important;
                }
            }
            
            /* Custom Scrollbar for horizontal scrolling calendar */
            .calendar-scroll::-webkit-scrollbar {
                height: 6px;
            }
            .calendar-scroll::-webkit-scrollbar-track {
                background: transparent;
            }
            .calendar-scroll::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }
            .dark .calendar-scroll::-webkit-scrollbar-thumb {
                background: #475569;
            }
        </style>
        
        <div wire:ignore class="overflow-x-auto w-full calendar-scroll pb-2">
            <div id="attendance-calendar" class="dark:text-white w-full min-w-[700px] md:min-w-0"></div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('attendance-calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    locale: 'id',
                    events: {!! $allAttendancesJson !!},
                });
                calendar.render();
                
                // Force size update to fix width issues on mobile rendering
                setTimeout(() => {
                    calendar.updateSize();
                }, 150);

                // Re-render calendar when Livewire updates components
                window.addEventListener('livewire:navigated', () => {
                    calendar.refetchEvents();
                    setTimeout(() => {
                        calendar.updateSize();
                    }, 150);
                });
            });
        </script>
    </div>
</div>
