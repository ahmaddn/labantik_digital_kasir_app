<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Laporan Absensi & Shift</h1>
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
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
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
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4">Tanggal</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Kasir</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Buka (Opening)</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Tutup (Closing)</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right pr-4">Laporan Shift</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($attendances as $att)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4 text-sm font-bold text-gray-800 dark:text-white">
                                {{ $att->date->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4 text-sm text-gray-850 dark:text-gray-200 font-bold">
                                {{ $att->user->name }}
                            </td>
                            <td class="py-4">
                                <div class="text-sm font-bold text-gray-800 dark:text-white">
                                    {{ $att->clock_in ? $att->clock_in->format('H:i') . ' WIB' : '-' }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-bold mt-0.5">Uang Awal: Rp{{ number_format($att->opening_cash, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-4">
                                <div class="text-sm font-bold text-gray-800 dark:text-white">
                                    {{ $att->clock_out ? $att->clock_out->format('H:i') . ' WIB' : '-' }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-bold mt-0.5">Uang Akhir: Rp{{ number_format($att->closing_cash, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-4">
                                @php
                                    $color = 'bg-gray-50 text-gray-500';
                                    if ($att->status === 'present') { $color = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400'; }
                                    elseif ($att->status === 'late') { $color = 'bg-rose-50 text-rose-600 dark:bg-rose-955/30 dark:text-rose-450'; }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $color }}">
                                    {{ $att->status === 'present' ? 'Tepat Waktu' : ($att->status === 'late' ? 'Terlambat' : $att->status) }}
                                </span>
                            </td>
                            <td class="py-4 text-right pr-4">
                                @if($att->clock_out)
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="viewReport('{{ $att->id }}')" class="px-4 py-2 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-xl font-black text-xs uppercase italic tracking-wider transition-all">
                                            Lihat
                                        </button>
                                        @if(($att->user_id === auth()->id() && $att->edit_count < 1) || session('active_role_name') === 'superadmin')
                                            <button wire:click="openEditReport('{{ $att->id }}')" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all">
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
                            <td colspan="6" class="py-8 text-center text-gray-400 italic font-semibold">Tidak ada data absensi ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    </div>

    <!-- Report Modal -->
    <div x-data="{ show: @entangle('showReportModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('showReportModal', false)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-gray-850 dark:text-white uppercase italic tracking-tight">Laporan Closing Shift</h2>
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
    <div x-data="{ show: @entangle('editingAttendanceId') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-xs" wire:click="$set('editingAttendanceId', null)"></div>
        <div x-show="show" x-transition.scale class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 z-10">
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
</div>
