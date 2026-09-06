<div class="py-6 w-full space-y-6">
    <!-- Active Leaderboard Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-2xl p-6 md:p-8 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 w-full space-y-6">
        <div>
            <span
                class="px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black tracking-widest uppercase rounded-full border border-amber-500/20 flex items-center gap-1.5 w-fit">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.776 1.848l-3.59 1.54 3.59 1.54a1 1 0 11-.776 1.848l-4-1.714a.999.999 0 01-.356-.257l-2.644-1.133a1 1 0 000-1.84l7-3a1 1 0 00-.788 0l-7-3a1 1 0 000-1.84l7-3z" />
                </svg>
                Sistem Peringkat & Gamifikasi
            </span>
            <h1 class="text-2xl md:text-3xl font-black mt-2 tracking-tight italic uppercase text-gray-900 dark:text-white">Papan Skor & Poin Kasir</h1>
            <p class="text-gray-400 mt-1 text-xs md:text-sm font-semibold">
                Lakukan performa terbaik untuk memimpin papan skor dan kumpulkan poin!
            </p>
        </div>

        <!-- Dynamic Motivation Banner -->
        @php
            $bannerStyle = match ($motivation['type']) {
                'gold' => 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400',
                'silver' => 'bg-gray-500/10 border-gray-400/30 text-gray-700 dark:text-gray-300',
                'bronze' => 'bg-amber-700/10 border-amber-700/30 text-amber-700 dark:text-amber-500',
                'purple' => 'bg-purple-500/10 border-purple-500/30 text-purple-600 dark:text-purple-400',
                default => 'bg-red-500/10 border-red-500/30 text-red-600 dark:text-red-400',
            };
        @endphp
        <div class="{{ $bannerStyle }} border p-6 rounded-3xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-white/20 dark:bg-gray-800/40 rounded-2xl shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-black uppercase tracking-wider text-sm">{{ $motivation['title'] }}</h4>
                        <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-widest rounded-full bg-white/30 dark:bg-gray-800/50 border border-current">
                            {{ $motivation['badge'] }}
                        </span>
                    </div>
                    <p class="text-xs font-semibold mt-1 opacity-90 leading-relaxed">{{ $motivation['message'] }}</p>
                </div>
            </div>
            <button wire:click="viewUserDetail('{{ auth()->id() }}')"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 font-black text-xs uppercase tracking-widest rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 hover:scale-105 active:scale-95 transition-all text-gray-800 dark:text-white shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Detail Poin Saya
            </button>
        </div>

        <!-- Podium Layout (Rank 1, 2, 3) -->
        @php
            $top3 = $leaderboard->take(3);
        @endphp
        <div class="grid grid-cols-3 gap-4 items-end justify-center pt-8 pb-10 border-b border-gray-150 dark:border-gray-800">
            <!-- Rank 2 -->
            @if ($top3->count() > 1)
                @php $u2 = $top3->values()->get(1); @endphp
                <div class="flex flex-col items-center">
                    <div class="w-14 h-14 bg-gray-200 dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-700 rounded-full flex items-center justify-center font-black text-gray-700 dark:text-gray-300 uppercase text-xs">
                        {{ substr($u2->name, 0, 2) }}
                    </div>
                    <div class="text-center mt-2 min-w-0 w-full">
                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $u2->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $u2->total_score }} Pts</p>
                    </div>
                    <div class="w-full h-16 bg-gray-100 dark:bg-gray-800 rounded-t-xl mt-3 flex items-center justify-center font-black text-gray-500 dark:text-gray-400 text-lg">
                        2
                    </div>
                </div>
            @endif

            <!-- Rank 1 -->
            @if ($top3->count() > 0)
                @php $u1 = $top3->values()->get(0); @endphp
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <svg class="w-6 h-6 text-amber-500 absolute -top-5 left-1/2 transform -translate-x-1/2"
                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <div class="w-18 h-18 bg-amber-500/20 border-4 border-amber-500 rounded-full flex items-center justify-center font-black text-amber-600 dark:text-amber-400 uppercase text-sm">
                            {{ substr($u1->name, 0, 2) }}
                        </div>
                    </div>
                    <div class="text-center mt-2 min-w-0 w-full">
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $u1->name }}</p>
                        <p class="text-xs font-black text-amber-500 mt-0.5">{{ $u1->total_score }} Pts</p>
                    </div>
                    <div class="w-full h-24 bg-amber-500/10 dark:bg-amber-500/20 border-t-2 border-amber-500 rounded-t-xl mt-3 flex items-center justify-center font-black text-amber-600 dark:text-amber-400 text-2xl">
                        1
                    </div>
                </div>
            @endif

            <!-- Rank 3 -->
            @if ($top3->count() > 2)
                @php $u3 = $top3->values()->get(2); @endphp
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-amber-900/10 dark:bg-amber-955/20 border-2 border-amber-800/30 rounded-full flex items-center justify-center font-black text-amber-800 dark:text-amber-600 uppercase text-[10px]">
                        {{ substr($u3->name, 0, 2) }}
                    </div>
                    <div class="text-center mt-2 min-w-0 w-full">
                        <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $u3->name }}</p>
                        <p class="text-[10px] font-bold text-gray-455 mt-0.5">{{ $u3->total_score }} Pts</p>
                    </div>
                    <div class="w-full h-12 bg-gray-50 dark:bg-gray-900 rounded-t-xl mt-3 flex items-center justify-center font-black text-gray-455 text-base">
                        3
                    </div>
                </div>
            @endif
        </div>

        <!-- Leaderboard Table -->
        <div class="overflow-x-auto w-full rounded-2xl border border-gray-100 dark:border-gray-800">
            <table class="w-full text-left min-w-[500px] md:min-w-0">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-16">Peringkat</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kasir</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-24">Streak</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-32">Total Poin</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    @foreach ($leaderboard as $index => $u)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors {{ $u->id === auth()->id() ? 'bg-primary-blue/5 dark:bg-primary-blue/10 font-bold' : '' }}">
                            <td class="px-6 py-4 text-center text-xs font-black text-gray-500 dark:text-gray-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[10px] font-black uppercase text-primary-blue border border-gray-200 dark:border-gray-700">
                                        {{ substr($u->name, 0, 2) }}
                                    </div>
                                    <span class="text-xs text-gray-800 dark:text-gray-200">{{ $u->name }}</span>
                                    @if ($u->id === auth()->id())
                                        <span class="px-1.5 py-0.5 bg-primary-blue text-white rounded text-[8px] font-black uppercase tracking-wider">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-xs font-black text-orange-500">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.94-.209.381-.363.887-.453 1.488-.12.802-.073 1.84.195 2.87l.062.24c.024.1.039.223.05.375a2.037 2.037 0 01-.029.511c-.048.24-.154.48-.32.647a.997.997 0 01-1.08.16c-.461-.247-.744-.623-.926-1.08-.182-.456-.224-.959-.224-1.347V6a1 1 0 00-1-1 3 3 0 00-2 2.22c0 1.258.18 2.5.474 3.738.152.64.4 1.25.753 1.807.353.558.836 1.057 1.443 1.487a8.007 8.007 0 005.19 2.09c.477.027.947-.033 1.4-.18a7.995 7.995 0 003.86-2.482c.187-.228.34-.483.47-.752.43-.892.652-1.928.652-3.141 0-1.622-.515-2.91-1.293-3.812a6.002 6.002 0 00-.825-1.012l-.011-.011-.002-.002a1 1 0 00-1.436.17l-.02.027a4.01 4.01 0 01-.262.33c-.758.874-1.808 1.47-3.2 1.47V2.553z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $u->streak }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-black text-gray-800 dark:text-white">
                                {{ $u->total_score }} Pts
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="viewUserDetail('{{ $u->id }}')"
                                    class="p-2 bg-gray-100 hover:bg-primary-blue hover:text-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-xl transition-colors"
                                    title="Lihat Sumber & Rincian Poin">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Points Rule Guide -->
        <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-800 w-full space-y-4">
            <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-widest">Aturan Perolehan Skor & Poin</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 text-primary-blue rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white">Melayani POS</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selesaikan checkout di POS: <strong>+5 Poin & +1 Streak</strong></p>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white">Selesaikan Tugas</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Poin menyesuaikan urgensi:<br>
                            - Rendah: <strong>+5 Poin</strong><br>
                            - Sedang: <strong>+10 Poin</strong><br>
                            - Tinggi: <strong>+20 Poin</strong><br>
                            - Paling Penting: <strong>+30 Poin</strong><br>
                            Beserta <strong>+1 Streak</strong> setelah di-ACC.
                        </p>
                    </div>


                </div>

                <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white">Buka & Tutup Sesi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Buka laci & stock awal: <strong>+15 Poin</strong>. Tutup laci & stock akhir: <strong>+15 Poin</strong>.</p>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                    <div class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white">Aturan Pulang Cepat & Lembur</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Clock-out lebih cepat dari jadwal: <strong>Pengurangan poin sesuai pengaturan absensi admin</strong>. Clock-out tepat waktu / melebihi waktu akhir: <strong>Bonus +10 Poin & +1 Streak (Lembur)</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Point Breakdown Detail Modal -->
    <div x-data="{ show: @entangle('showPointDetailModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-cloak>
        <!-- Backdrop -->
        <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs" @click="show = false"></div>

        <!-- Modal Container -->
        <div x-show="show"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden border border-gray-100 dark:border-gray-700 max-h-[90vh] z-10 relative">
            
            <!-- Modal Header -->
            <div class="p-6 md:p-8 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 dark:text-amber-400 flex items-center justify-center font-black uppercase text-base border border-amber-500/20 shrink-0">
                        {{ substr($detailUser->name ?? 'K', 0, 2) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $detailUser->name ?? 'Kasir' }}</h3>
                            <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-widest bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-md border border-amber-500/20">
                                Detail Poin
                            </span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-widest mt-0.5">Rincian & Asal Perolehan Poin Kasir</p>
                    </div>
                </div>
                <button @click="show = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors rounded-xl bg-gray-100 dark:bg-gray-700/60">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable Accordions) -->
            <div class="p-6 md:p-8 space-y-4 overflow-y-auto flex-1 no-scrollbar bg-white dark:bg-gray-800" x-data="{ openGroup: 'summary' }">
                <!-- Accordion 1: Ringkasan Sumber Poin -->
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700/80 overflow-hidden bg-gray-50/50 dark:bg-gray-800/40">
                    <button type="button" @click="openGroup = (openGroup === 'summary' ? null : 'summary')"
                        class="w-full p-4 flex items-center justify-between text-left font-black uppercase tracking-widest text-xs text-gray-800 dark:text-gray-200 hover:bg-gray-100/50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-500/10 text-amber-500 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <span>Ringkasan & Total Perolehan Poin</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-amber-500 dark:text-amber-400 italic text-sm font-black">{{ ($detailUser->points ?? 0) + ($detailUser->pending_points ?? 0) }} Pts</span>
                            <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': openGroup === 'summary' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openGroup === 'summary'" x-collapse class="p-4 pt-0 border-t border-gray-100 dark:border-gray-700/60 space-y-3">
                        <div class="grid grid-cols-2 gap-3 pt-3">
                            <div class="p-4 bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-700/70">
                                <span class="text-[9px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest block">Total Poin</span>
                                <div class="text-xl font-black text-amber-500 dark:text-amber-400 italic mt-0.5">
                                    {{ ($detailUser->points ?? 0) + ($detailUser->pending_points ?? 0) }} Pts
                                </div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-700/70">
                                <span class="text-[9px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest block">Streak Kerja</span>
                                <div class="text-xl font-black text-orange-500 dark:text-orange-400 italic mt-0.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.94-.209.381-.363.887-.453 1.488-.12.802-.073 1.84.195 2.87l.062.24c.024.1.039.223.05.375a2.037 2.037 0 01-.029.511c-.048.24-.154.48-.32.647a.997.997 0 01-1.08.16c-.461-.247-.744-.623-.926-1.08-.182-.456-.224-.959-.224-1.347V6a1 1 0 00-1-1 3 3 0 00-2 2.22c0 1.258.18 2.5.474 3.738.152.64.4 1.25.753 1.807.353.558.836 1.057 1.443 1.487a8.007 8.007 0 005.19 2.09c.477.027.947-.033 1.4-.18a7.995 7.995 0 003.86-2.482c.187-.228.34-.483.47-.752.43-.892.652-1.928.652-3.141 0-1.622-.515-2.91-1.293-3.812a6.002 6.002 0 00-.825-1.012l-.011-.011-.002-.002a1 1 0 00-1.436.17l-.02.027a4.01 4.01 0 01-.262.33c-.758.874-1.808 1.47-3.2 1.47V2.553z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $detailUser->streak ?? 0 }} Hari
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accordion 2: Penjualan POS Kasir -->
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700/80 overflow-hidden bg-gray-50/50 dark:bg-gray-800/40">
                    <button type="button" @click="openGroup = (openGroup === 'pos' ? null : 'pos')"
                        class="w-full p-4 flex items-center justify-between text-left font-black uppercase tracking-widest text-xs text-gray-800 dark:text-gray-200 hover:bg-gray-100/50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 text-blue-500 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <span>Poin Penjualan Kasir (POS)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-blue-500 dark:text-blue-400 font-black text-xs">+{{ $userStats['pos_points'] }} Pts</span>
                            <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': openGroup === 'pos' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openGroup === 'pos'" x-collapse class="p-4 pt-0 border-t border-gray-100 dark:border-gray-700/60 space-y-2">
                        <div class="p-3 bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-700/70 flex items-center justify-between mt-3">
                            <div>
                                <h5 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Akumulasi Checkout Sukses</h5>
                                <p class="text-[9px] font-semibold text-gray-400 mt-0.5">{{ $userStats['total_transactions'] }} Transaksi Ditangani (@ +5 Pts/Transaksi)</p>
                            </div>
                            <span class="text-xs font-black text-blue-500 dark:text-blue-400">+{{ $userStats['pos_points'] }} Pts</span>
                        </div>
                    </div>
                </div>

                <!-- Accordion 3: Tugas Kasir Selesai -->
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700/80 overflow-hidden bg-gray-50/50 dark:bg-gray-800/40">
                    <button type="button" @click="openGroup = (openGroup === 'tasks' ? null : 'tasks')"
                        class="w-full p-4 flex items-center justify-between text-left font-black uppercase tracking-widest text-xs text-gray-800 dark:text-gray-200 hover:bg-gray-100/50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-500/10 text-indigo-500 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                </svg>
                            </div>
                            <span>Poin Tugas Kasir</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-indigo-500 dark:text-indigo-400 font-black text-xs">+{{ $userStats['task_points'] }} Pts</span>
                            <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': openGroup === 'tasks' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openGroup === 'tasks'" x-collapse class="p-4 pt-0 border-t border-gray-100 dark:border-gray-700/60 space-y-2">
                        <div class="p-3 bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-700/70 flex items-center justify-between mt-3">
                            <div>
                                <h5 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Tugas Kasir Ter-ACC</h5>
                                <p class="text-[9px] font-semibold text-gray-400 mt-0.5">{{ $userStats['completed_tasks'] }} Laporan Tugas Disetujui Admin</p>
                            </div>
                            <span class="text-xs font-black text-indigo-500 dark:text-indigo-400">+{{ $userStats['task_points'] }} Pts</span>
                        </div>
                    </div>
                </div>

                <!-- Accordion 4: Absensi & Sesi Laci Kasir -->
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700/80 overflow-hidden bg-gray-50/50 dark:bg-gray-800/40">
                    <button type="button" @click="openGroup = (openGroup === 'attendance' ? null : 'attendance')"
                        class="w-full p-4 flex items-center justify-between text-left font-black uppercase tracking-widest text-xs text-gray-800 dark:text-gray-200 hover:bg-gray-100/50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-500/10 text-emerald-500 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span>Poin Absensi & Sesi Laci</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-500 dark:text-emerald-400 font-black text-xs">+{{ $userStats['attendance_points'] }} Pts</span>
                            <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': openGroup === 'attendance' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openGroup === 'attendance'" x-collapse class="p-4 pt-0 border-t border-gray-100 dark:border-gray-700/60 space-y-2">
                        <div class="p-3 bg-white dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-700/70 flex items-center justify-between mt-3">
                            <div>
                                <h5 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Sesi Shift & Jam Kerja</h5>
                                <p class="text-[9px] font-semibold text-gray-400 mt-0.5">{{ $userStats['attendance_count'] }} Kali Hadir & Melakukan Closing</p>
                            </div>
                            <span class="text-xs font-black text-emerald-500 dark:text-emerald-400">+{{ $userStats['attendance_points'] }} Pts</span>
                        </div>
                    </div>
                </div>

                <!-- Accordion 5: Tabel Timeline Aktivitas Lengkap -->
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700/80 overflow-hidden bg-gray-50/50 dark:bg-gray-800/40">
                    <button type="button" @click="openGroup = (openGroup === 'logs' ? null : 'logs')"
                        class="w-full p-4 flex items-center justify-between text-left font-black uppercase tracking-widest text-xs text-gray-800 dark:text-gray-200 hover:bg-gray-100/50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-500/10 text-purple-500 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </div>
                            <span>Tabel Riwayat Aktivitas Lengkap</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 font-bold text-[10px] uppercase tracking-wider">{{ $recentLogs->count() }} Item</span>
                            <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': openGroup === 'logs' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openGroup === 'logs'" x-collapse class="p-4 pt-0 border-t border-gray-100 dark:border-gray-700/60">
                        <div class="rounded-xl border border-gray-100 dark:border-gray-700/70 overflow-hidden max-h-[250px] overflow-y-auto mt-3">
                            <table class="w-full text-left border-collapse min-w-[480px]">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-100/90 dark:bg-gray-900 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-200 dark:border-gray-700/70">
                                        <th class="py-3 px-4">Aktivitas</th>
                                        <th class="py-3 px-4 text-center">Kategori</th>
                                        <th class="py-3 px-4 text-right">Poin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-900/60">
                                    @forelse($recentLogs as $log)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="py-3 px-4 align-middle">
                                                <div class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                                    {{ $log['title'] }}
                                                </div>
                                                <div class="text-[9px] font-semibold text-gray-400 dark:text-gray-400 mt-0.5 uppercase tracking-widest">
                                                    {{ \Carbon\Carbon::parse($log['date'])->format('d M Y - H:i') }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 align-middle text-center">
                                                <span class="px-2 py-0.5 text-[8px] font-black rounded-lg uppercase tracking-wider border {{ $log['badge_color'] }}">
                                                    {{ $log['badge'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 align-middle text-right text-xs font-black text-emerald-500 dark:text-emerald-400">
                                                {{ $log['points'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-xs text-gray-400 dark:text-gray-400 uppercase tracking-widest font-bold">
                                                Belum ada aktivitas poin tercatat
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-5 md:p-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end shrink-0">
                <button type="button" @click="show = false"
                    class="px-6 py-3 bg-gray-900 dark:bg-gray-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-800 dark:hover:bg-gray-600 transition-all border border-gray-800 dark:border-gray-600 shadow-md">
                    Tutup Modal
                </button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
