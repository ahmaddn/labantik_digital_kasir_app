<div class="py-6 w-full space-y-8">
    <!-- Header Section -->
    <div
        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-amber-500 to-yellow-600 dark:from-amber-950 dark:to-yellow-905 p-6 md:p-8 rounded-3xl shadow-xl text-white relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-20 top-2 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>

        <div class="relative z-10">
            <span
                class="px-3 py-1 bg-white/20 text-xs font-black tracking-widest uppercase rounded-full border border-white/10 flex items-center gap-1.5 w-fit">
                <svg class="w-3.5 h-3.5 text-yellow-405" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.776 1.848l-3.59 1.54 3.59 1.54a1 1 0 11-.776 1.848l-4-1.714a.999.999 0 01-.356-.257l-2.644-1.133a1 1 0 000-1.84l7-3a1 1 0 00-.788 0l-7-3a1 1 0 000-1.84l7-3z" />
                </svg>
                Sistem Peringkat & Gamifikasi
            </span>
            <h1 class="text-2xl md:text-3xl font-black mt-2 tracking-tight italic uppercase">Papan Skor & Poin Kasir</h1>
            <p class="text-amber-50 mt-2 text-xs md:text-sm max-w-2xl font-medium">
                Kumpulkan poin dari melayani transaksi, menyelesaikan tugas rutin, dan menjaga performa absensi Anda untuk menaikkan peringkat!
            </p>
        </div>
    </div>

    <!-- Active Leaderboard Card -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-800 w-full space-y-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Papan Peringkat Kasir</h2>
                <p class="text-sm text-gray-400 mt-1 uppercase tracking-wider font-bold">Lakukan performa terbaik untuk memimpin papan skor!</p>
            </div>
        </div>

        <!-- Success Banner -->
        @if (auth()->user()->points + auth()->user()->pending_points > 50)
            <div class="bg-gradient-to-r from-amber-500/10 to-yellow-500/10 border border-amber-500/20 p-6 rounded-3xl flex items-center justify-between gap-4 animate-pulse">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-amber-500/20 text-amber-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider text-xs">Kerja Bagus, {{ auth()->user()->name }}!</h4>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold mt-0.5">Skor Anda telah melampaui 50 Pts. Terus pertahankan performa kerja Anda!</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Podium Layout (Rank 1, 2, 3) -->
        @php
            $top3 = $leaderboard->take(3);
        @endphp
        <div class="grid grid-cols-3 gap-6 items-end justify-center pt-10 pb-12 border-b border-gray-100 dark:border-gray-800 max-w-3xl mx-auto">
            <!-- Rank 2 (Silver) -->
            @if ($top3->count() > 1)
                @php $u2 = $top3->values()->get(1); @endphp
                <div class="flex flex-col items-center animate-fade-in">
                    <div class="relative group">
                        <!-- Shiny Aura -->
                        <div class="absolute inset-0 rounded-full bg-slate-400/20 blur-md group-hover:blur-lg transition-all"></div>
                        <!-- Silver Badge/Medal -->
                        <div class="absolute -top-3 -right-2 bg-slate-300 text-slate-800 dark:bg-slate-700 dark:text-slate-100 w-6 h-6 rounded-full flex items-center justify-center font-black text-[10px] border-2 border-white dark:border-gray-900 shadow">
                            2nd
                        </div>
                        <div class="relative w-16 h-16 bg-gradient-to-tr from-slate-400 to-slate-205 dark:from-slate-800 dark:to-slate-700 border-4 border-slate-350 dark:border-slate-650 rounded-full flex items-center justify-center font-black text-slate-700 dark:text-slate-205 uppercase text-sm shadow-xl">
                            {{ substr($u2->name, 0, 2) }}
                        </div>
                    </div>
                    <div class="text-center mt-3 min-w-0 w-full px-2">
                        <p class="text-xs font-black text-gray-850 dark:text-white truncate leading-tight">{{ $u2->name }}</p>
                        <span class="inline-block px-2.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-black mt-1">
                            {{ $u2->total_score }} Pts
                        </span>
                    </div>
                    <!-- Silver Podium Block -->
                    <div class="w-full h-24 bg-gradient-to-b from-slate-300/40 to-slate-400/10 dark:from-slate-850/60 dark:to-slate-900/20 border-t-4 border-slate-400 dark:border-slate-600 rounded-t-3xl mt-4 flex flex-col items-center justify-center shadow-lg relative overflow-hidden">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-5xl font-black text-slate-400/20 dark:text-slate-600/20">2</div>
                        <span class="text-xl font-black text-slate-500 dark:text-slate-300 relative z-10">II</span>
                    </div>
                </div>
            @endif

            <!-- Rank 1 (Gold) -->
            @if ($top3->count() > 0)
                @php $u1 = $top3->values()->get(0); @endphp
                <div class="flex flex-col items-center animate-fade-in">
                    <div class="relative group -mt-6">
                        <!-- Golden Glow Aura -->
                        <div class="absolute inset-0 rounded-full bg-amber-500/30 blur-lg group-hover:blur-xl transition-all animate-pulse"></div>
                        <!-- Crown Header -->
                        <div class="absolute -top-7 left-1/2 transform -translate-x-1/2">
                            <svg class="w-8 h-8 text-amber-500 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <!-- Gold Badge/Medal -->
                        <div class="absolute -top-2 -right-2 bg-amber-500 text-white w-7 h-7 rounded-full flex items-center justify-center font-black text-xs border-2 border-white dark:border-gray-900 shadow-lg">
                            1st
                        </div>
                        <div class="relative w-20 h-20 bg-gradient-to-tr from-amber-400 to-yellow-200 dark:from-amber-600 dark:to-yellow-500 border-4 border-amber-500 rounded-full flex items-center justify-center font-black text-amber-955 dark:text-amber-100 uppercase text-lg shadow-2xl">
                            {{ substr($u1->name, 0, 2) }}
                        </div>
                    </div>
                    <div class="text-center mt-3 min-w-0 w-full px-2">
                        <p class="text-sm font-black text-amber-550 truncate leading-tight">{{ $u1->name }}</p>
                        <span class="inline-block px-3 py-1 bg-amber-550/20 text-amber-650 dark:text-amber-400 rounded-full text-xs font-black mt-1 border border-amber-500/20">
                            {{ $u1->total_score }} Pts
                        </span>
                    </div>
                    <!-- Gold Podium Block -->
                    <div class="w-full h-36 bg-gradient-to-b from-amber-500/40 to-yellow-500/10 dark:from-amber-650/50 dark:to-amber-900/10 border-t-4 border-amber-500 rounded-t-3xl mt-4 flex flex-col items-center justify-center shadow-2xl relative overflow-hidden">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-7xl font-black text-amber-500/15 dark:text-amber-500/10">1</div>
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-400 relative z-10">I</span>
                    </div>
                </div>
            @endif

            <!-- Rank 3 (Bronze) -->
            @if ($top3->count() > 2)
                @php $u3 = $top3->values()->get(2); @endphp
                <div class="flex flex-col items-center animate-fade-in">
                    <div class="relative group">
                        <!-- Shiny Aura -->
                        <div class="absolute inset-0 rounded-full bg-amber-700/20 blur-md group-hover:blur-lg transition-all"></div>
                        <!-- Bronze Badge/Medal -->
                        <div class="absolute -top-3 -right-2 bg-amber-700 text-white w-6 h-6 rounded-full flex items-center justify-center font-black text-[10px] border-2 border-white dark:border-gray-900 shadow">
                            3rd
                        </div>
                        <div class="relative w-14 h-14 bg-gradient-to-tr from-amber-600 to-amber-405 dark:from-amber-800 dark:to-amber-700 border-4 border-amber-700 rounded-full flex items-center justify-center font-black text-amber-955 dark:text-amber-205 uppercase text-xs shadow-lg">
                            {{ substr($u3->name, 0, 2) }}
                        </div>
                    </div>
                    <div class="text-center mt-3 min-w-0 w-full px-2">
                        <p class="text-xs font-black text-gray-850 dark:text-white truncate leading-tight">{{ $u3->name }}</p>
                        <span class="inline-block px-2.5 py-0.5 bg-amber-900/10 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 rounded-full text-[10px] font-black mt-1">
                            {{ $u3->total_score }} Pts
                        </span>
                    </div>
                    <!-- Bronze Podium Block -->
                    <div class="w-full h-16 bg-gradient-to-b from-amber-700/30 to-amber-900/10 dark:from-amber-800/40 dark:to-amber-950/10 border-t-4 border-amber-700 rounded-t-3xl mt-4 flex flex-col items-center justify-center shadow-md relative overflow-hidden">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-4xl font-black text-amber-700/20 dark:text-amber-850/10">3</div>
                        <span class="text-lg font-black text-amber-705 dark:text-amber-500 relative z-10">III</span>
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
                        <p class="text-sm font-black text-gray-800 dark:text-white">Sanksi Keterlambatan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Clock-in atau clock-out terlambat: <strong>Pengurangan poin sesuai pengaturan absensi admin</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
