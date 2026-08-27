<div class="p-4 md:p-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 md:mb-12">
        <div>
            <h1
                class="text-2xl md:text-4xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-blue-light">
                Dashboard Digital</h1>
            <p class="text-gray-400 font-semibold text-[10px] uppercase tracking-[0.2em]">
                {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 sm:gap-4 w-full lg:w-auto lg:justify-end">
            @if (!session('active_jurusan_id'))
                <div
                    class="flex items-center bg-white dark:bg-gray-800 px-4 md:px-6 py-2 md:py-2.5 rounded-xl shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-800">
                    <svg class="w-5 h-5 text-primary-blue mr-3" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                        <line x1="4" y1="22" x2="4" y2="15" />
                    </svg>
                    <select wire:model.live="filterJurusan"
                        class="border-none p-0 focus:ring-0 font-black text-sm bg-transparent dark:text-white uppercase tracking-widest cursor-pointer">
                        <option value="">Semua Jurusan / Global</option>
                        @foreach ($jurusans as $jur)
                            <option value="{{ $jur->id }}">TEFA {{ $jur->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <a href="{{ route('guide') }}"
                class="px-4 py-3 sm:px-5 sm:py-3 md:py-3.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-white rounded-xl md:rounded-2xl shadow-xl shadow-blue-900/5 font-bold uppercase tracking-wide text-xs transition transform hover:-translate-y-1 hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 flex items-center">
                <svg class="w-5.5 h-5.5 mr-2.5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 16v-4" />
                    <path d="M12 8h.01" />
                </svg>
                Petunjuk & SOP
            </a>

            <a href="{{ route('leaderboard') }}"
                class="px-4 py-3 sm:px-5 sm:py-3 md:py-3.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-white rounded-xl md:rounded-2xl shadow-xl shadow-blue-900/5 font-bold uppercase tracking-wide text-xs transition transform hover:-translate-y-1 hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 flex items-center">
                <svg class="w-5.5 h-5.5 mr-2.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 16v-4" />
                    <path d="M12 8h.01" />
                </svg>
                Peringkat & Poin
            </a>

            @if (session('active_role_name') !== 'superadmin')
                @php
                    $hasHigherRole = auth()
                        ->user()
                        ->roles()
                        ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
                        ->exists();
                    $isScheduled = true;
                    if (session('active_role_name') === 'kasir' && !$hasHigherRole) {
                        $activeJurusanId = session('active_jurusan_id');
                        $activeJurusan = \App\Models\Jurusan::find($activeJurusanId);
                        $allowedJurusanIds = [$activeJurusanId];
                        if ($activeJurusan && $activeJurusan->parent_id) {
                            $allowedJurusanIds[] = $activeJurusan->parent_id;
                        }
                        $isScheduled = \App\Models\CashierSchedule::where('user_id', auth()->id())
                            ->whereIn('jurusan_id', $allowedJurusanIds)
                            ->where('date', now()->toDateString())
                            ->exists();
                    }
                @endphp

                @if ($isSessionFinished)
                    <div class="flex items-center space-x-2" x-data="{ showEmergencyConfirm: false }">
                        <button disabled
                            class="px-4 py-3 sm:px-5 sm:py-3 md:py-3.5 bg-gray-400 text-white rounded-xl md:rounded-2xl shadow-xl font-bold uppercase tracking-wide text-xs cursor-not-allowed flex flex-col items-center leading-tight">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Sesi Selesai
                            </div>
                            <span class="text-[8px] font-black uppercase tracking-widest mt-1 opacity-60">Data hari ini
                                telah dikunci</span>
                        </button>
                        <button type="button" @click="showEmergencyConfirm = true" @class([
                            'px-4 py-3 sm:px-5 sm:py-3 md:py-3.5 bg-red-600 hover:bg-red-700 text-white rounded-xl md:rounded-2xl shadow-xl shadow-red-600/30 font-bold uppercase tracking-wide text-xs flex flex-col items-center leading-tight transition transform hover:-translate-y-1 active:scale-95',
                            'hidden' => !$hasHigherRole,
                        ])>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                    <path d="M3 3v5h5" />
                                </svg>
                                Darurat
                            </div>
                            <span class="text-[8px] font-black uppercase tracking-widest mt-1 opacity-90">Aktifkan
                                Sesi</span>
                        </button>

                        <!-- Emergency Reactivate Confirmation Modal -->
                        <div x-show="showEmergencyConfirm" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" @keydown.window.escape="showEmergencyConfirm = false"
                            class="fixed inset-0 z-[500] flex items-center justify-center p-4 bg-gray-950/80 backdrop-blur-md text-left"
                            style="display: none;">
                            <div @click.outside="showEmergencyConfirm = false"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="bg-white dark:bg-gray-900 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
                                <!-- Header -->
                                <div
                                    class="p-8 bg-gradient-to-br from-red-600 to-red-700 text-white relative overflow-hidden">
                                    <div
                                        class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl">
                                    </div>
                                    <div class="relative z-10 flex items-center gap-4">
                                        <div
                                            class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 flex-shrink-0">
                                            <svg class="w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span
                                                class="px-2.5 py-0.5 bg-white/25 text-[10px] font-black uppercase tracking-widest rounded-full border border-white/10">Tindakan
                                                Darurat</span>
                                            <h3 class="text-2xl font-black italic tracking-tight mt-1">Aktifkan Kembali
                                                Sesi?</h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-8">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Anda yakin ingin mengaktifkan kembali sesi ini? <strong
                                            class="text-primary-red">Data rekap hari ini akan dihapus</strong> dan Anda
                                        harus melakukan tutup kasir ulang nantinya.
                                    </p>
                                </div>

                                <!-- Footer -->
                                <div
                                    class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-3 justify-end">
                                    <button type="button" @click="showEmergencyConfirm = false"
                                        class="px-6 py-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-black rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-all text-center uppercase tracking-widest">
                                        Batal
                                    </button>
                                    <button type="button" wire:click="emergencyReactivateSession"
                                        @click="showEmergencyConfirm = false"
                                        class="px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-black rounded-2xl hover:bg-red-700 shadow-lg shadow-red-600/25 transition-all text-center uppercase tracking-widest">
                                        Ya, Aktifkan Sesi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(!$isScheduled)
                    <div
                        class="px-4 py-3 sm:px-5 sm:py-3 md:py-3.5 bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 rounded-xl md:rounded-2xl shadow-xl opacity-90 cursor-not-allowed flex flex-col items-center justify-center leading-tight text-center">
                        <div class="flex items-center text-xs font-black uppercase italic tracking-wider">
                            <svg class="w-5 h-5 mr-2.5 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            Tidak Ada Jadwal
                        </div>
                        <span class="text-[8px] font-black uppercase tracking-widest mt-1 opacity-90">Anda tidak
                            dijadwalkan hari ini</span>
                    </div>
                @else
                    <a href="{{ route('kasir') }}"
                        class="px-4 py-3 sm:px-5 sm:py-3 md:py-3.5 bg-primary-red text-white rounded-xl md:rounded-2xl shadow-2xl shadow-red-500/30 font-bold uppercase tracking-wide text-xs transition transform hover:-translate-y-2 active:scale-95 flex items-center">
                        <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                            <path d="M3 6h18" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                        Buka Kasir
                    </a>
                @endif
            @endif
        </div>
    </div>

    @if ($orphanCarryForwardAmount > 0 && in_array(session('active_role_name'), ['pengelola_jurusan', 'superadmin']))
        <div
            class="mb-8 bg-amber-500/10 border-2 border-dashed border-amber-500 rounded-3xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-amber-600/80 dark:text-amber-400/80 mt-1">
                    Ditemukan saldo bawaan sebesar Rp{{ number_format($orphanCarryForwardAmount, 0, ',', '.') }} tanggal
                    {{ now()->startOfMonth()->translatedFormat('d F Y') }}.
                    Ini membuat statistik kas terlihat lebih besar dari transaksi riil bulan ini.
                </p>
            </div>
            <button wire:click="cleanupOrphanCarryForward"
                class="px-6 py-4 bg-amber-500 text-white rounded-2xl font-black italic uppercase text-xs tracking-widest shadow-xl shadow-amber-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all whitespace-nowrap">
                Bersihkan Saldo Yatim
            </button>
        </div>
    @endif

    <!-- Summary Cards (Today vs Yesterday) -->
    <div class="mb-8 md:mb-10">
        <div class="flex items-center gap-4 mb-4 md:mb-5">
            <div class="h-8 w-2 bg-primary-blue rounded-full"></div>
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">Statistik
                Hari Ini</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div
                class="bg-primary-blue rounded-3xl md:rounded-[3rem] p-5 md:p-10 shadow-2xl shadow-blue-900/30 border border-transparent hover:scale-105 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-10 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-white" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-3 bg-white/10 rounded-xl text-white">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20" />
                            <path d="m17 5-5-3-5 3" />
                            <path d="m17 19-5 3-5-3" />
                            <path d="M2 12h20" />
                            <path d="m5 7-3 5 3 5" />
                            <path d="m19 7 3 5-3 5" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-white/60 bg-white/5 px-4 py-2 rounded-full">Omzet</span>
                </div>
                <h3 class="text-white/60 text-[10px] font-black uppercase tracking-widest mb-1">Total Omzet Tunai</h3>
                <p class="text-xl md:text-2xl font-bold text-white tracking-tight"
                    :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stats->today_revenue, 0, ',', '.') }}
                </p>
                <div class="mt-4 pt-4 border-t border-white/10 flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-white/40 uppercase tracking-widest">Murni
                            Jurusan:</span>
                        <span class="text-xs font-black text-white italic"
                            :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stats->today_internal_revenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[9px] font-black uppercase px-2 py-1 rounded-md bg-white/20 text-white">
                            {{ $stats->revenue_change >= 0 ? '+' : '' }}{{ number_format($stats->revenue_change, 1) }}%
                            vs Kemarin
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-primary-red transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m22 7-8.5 8.5-5-5L2 17" />
                        <polyline points="18 7 22 7 22 11" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-3 bg-primary-red/10 rounded-xl text-primary-red">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-primary-red bg-primary-red/5 px-4 py-2 rounded-full">Profit</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Keuntungan Bersih</h3>
                <p class="text-xl md:text-2xl font-bold text-primary-red tracking-tight"
                    :class="censorMode ? 'privacy-blur' : ''">Rp{{ number_format($stats->today_profit, 0, ',', '.') }}
                </p>
                <div class="mt-4 flex items-center">
                    <span
                        class="text-[9px] font-black uppercase px-2 py-1 rounded-md bg-primary-red/10 text-primary-red">
                        {{ $stats->profit_change >= 0 ? '+' : '' }}{{ number_format($stats->profit_change, 1) }}% vs
                        Kemarin
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- General Insights (All Time) -->
    <div class="mb-8 md:mb-16">
        <div class="flex items-center gap-4 mb-4 md:mb-5">
            <div class="h-8 w-2 bg-green-500 rounded-full"></div>
            <h2 class="text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">General
                Insights</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <!-- Pemasukan (Bulan Ini) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-green-500 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-green-500" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-3 bg-green-500/10 rounded-xl text-green-500">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="12" x="2" y="6" rx="2" />
                            <circle cx="12" cy="12" r="2" />
                            <path d="M6 12h.01M18 12h.01" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-500/5 px-4 py-2 rounded-full">Pemasukan</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Bulan Ini</h3>
                <p class="text-xl md:text-2xl font-bold text-green-600 dark:text-green-400 tracking-tight"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->monthly_income, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Kas Masuk</p>
            </div>

            <!-- Pengeluaran (Bulan Ini) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-red-500 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-red-500" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-4 md:p-5 bg-red-500/10 rounded-2xl text-red-500">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="12" x="2" y="6" rx="2" />
                            <circle cx="12" cy="12" r="2" />
                            <path d="M6 12h.01M18 12h.01" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-red-600 bg-red-500/5 px-4 py-2 rounded-full">Pengeluaran</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Bulan Ini</h3>
                <p class="text-2xl md:text-4xl font-black text-red-600 dark:text-red-400 italic tracking-tighter"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->monthly_expense, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Kas Keluar</p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-green-500 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-green-500" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20" />
                        <path d="m17 5-5-3-5 3" />
                        <path d="m17 19-5 3-5-3" />
                        <path d="M2 12h20" />
                        <path d="m5 7-3 5 3 5" />
                        <path d="m19 7 3 5-3 5" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-3 bg-green-500/10 rounded-xl text-green-500">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 15h2a2 2 0 1 0 0-4h-2a2 2 0 1 1 0-4h2" />
                            <path d="M12 17V5" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-500/5 px-4 py-2 rounded-full">Total
                        Untung</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Seluruh Waktu</h3>
                <p class="text-2xl md:text-4xl font-black text-gray-800 dark:text-white italic tracking-tighter"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_all_time_profit, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Profit Bersih
                </p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-primary-blue transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="12" x="2" y="6" rx="2" />
                        <circle cx="12" cy="12" r="2" />
                        <path d="M6 12h.01M18 12h.01" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-4 md:p-5 bg-primary-blue/10 rounded-2xl text-primary-blue">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7" />
                            <path d="m16 19 2 2 4-4" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-primary-blue bg-primary-blue/5 px-4 py-2 rounded-full">Kas
                        Terverifikasi</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Kas Terverifikasi</h3>
                <p class="text-2xl md:text-4xl font-black text-gray-800 dark:text-white italic tracking-tighter"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_audit_cash, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total Uang Fisik Terkumpul
                </p>
            </div>



            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-primary-red transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-primary-red" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                        <path d="M12 9v4" />
                        <path d="M12 17h.01" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-3 bg-primary-red/10 rounded-xl text-primary-red">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-primary-red bg-primary-red/5 px-4 py-2 rounded-full">Hutang</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Piutang Belum Bayar
                </h3>
                <p class="text-2xl md:text-4xl font-black text-gray-800 dark:text-white italic tracking-tighter"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_outstanding_debt, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total Tagihan Piutang</p>
            </div>



            <!-- Total Omzet (Seluruh Waktu) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-indigo-500 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-indigo-500" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-4 md:p-5 bg-indigo-500/10 rounded-2xl text-indigo-500">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="12" x="2" y="6" rx="2" />
                            <circle cx="12" cy="12" r="2" />
                            <path d="M6 12h.01M18 12h.01" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-indigo-600 bg-indigo-500/5 px-4 py-2 rounded-full">Total
                        Omzet</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Seluruh Waktu</h3>
                <p class="text-2xl md:text-4xl font-black text-gray-800 dark:text-white italic tracking-tighter"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_all_time_revenue, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Pendapatan Kotor
                </p>
            </div>

            <!-- Keuntungan (Bulan Ini) -->
            <!-- Saldo Modal (Seluruh Waktu) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-6 shadow-xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 hover:border-indigo-500 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32 text-indigo-500" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="12" x="2" y="6" rx="2" />
                        <circle cx="12" cy="12" r="2" />
                        <path d="M6 12h.01M18 12h.01" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-4 md:mb-5">
                    <div class="p-4 md:p-5 bg-indigo-500/10 rounded-2xl text-indigo-500">
                        <svg class="w-6 h-6 md:w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="12" x="2" y="6" rx="2" />
                            <circle cx="12" cy="12" r="2" />
                            <path d="M6 12h.01M18 12h.01" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-indigo-600 bg-indigo-500/5 px-4 py-2 rounded-full">Saldo
                        Modal</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Seluruh Waktu</h3>
                <p class="text-2xl md:text-4xl font-black text-indigo-600 dark:text-indigo-400 italic tracking-tighter"
                    :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_modal_balance, 0, ',', '.') }}</p>
                <p class="mt-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Akumulasi Kas Modal Aktif
                </p>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div
        class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-[3.5rem] p-5 md:p-10 mb-8 md:mb-16 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-10">
            <div>
                <h2
                    class="text-xl md:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white leading-none">
                    Grafik Performa Mingguan</h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Analisis Omzet & Profit 7
                    Hari Terakhir</p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-primary-blue rounded-full mr-2"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Omzet</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-primary-red rounded-full mr-2"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Profit</span>
                </div>
            </div>
        </div>
        <div class="relative h-[280px] md:h-[400px]" wire:ignore>
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', function() {
            const ctx = document.getElementById('weeklyChart');
            if (!ctx) return;

            const data = @json($weeklyData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.day),
                    datasets: [{
                            label: 'Omzet',
                            data: data.map(d => d.revenue),
                            borderColor: '#1e40af',
                            backgroundColor: 'rgba(30, 64, 175, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 6,
                            pointBackgroundColor: '#1e40af',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Profit',
                            data: data.map(d => d.profit),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 6,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: {
                                size: 12,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 11
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            maximumFractionDigits: 0
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9ca3af',
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        maximumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });
        });
    </script>

    <div class="grid grid-cols-1 gap-6 md:gap-12">
        <!-- Top Products -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-5 md:p-10 border-b border-gray-100 dark:border-gray-700">
                <h2
                    class="text-xl md:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">
                    Produk Terlaris (All-Time)</h2>
            </div>
            <div class="p-5 md:p-10 space-y-6 md:space-y-10">
                @forelse($topProducts as $top)
                    <div class="flex items-center group">
                        <div
                            class="flex-shrink-0 w-12 h-12 md:w-16 md:h-16 bg-primary-blue dark:bg-gray-900 text-white rounded-2xl md:rounded-[1.5rem] flex items-center justify-center font-black italic shadow-2xl shadow-blue-900/10 group-hover:scale-110 transition-transform">
                            {{ $loop->iteration }}
                        </div>
                        <div class="ml-4 md:ml-6 flex-1">
                            <h4
                                class="text-sm md:text-base font-black text-gray-800 dark:text-white uppercase tracking-tight leading-tight">
                                {{ $top->product->name }}</h4>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                {{ $top->total_qty }} Unit Terjual</p>
                        </div>
                        <div class="text-right">
                            <p class="text-base md:text-lg font-black text-primary-red italic">
                                Rp{{ number_format($top->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-16 md:py-32 text-center opacity-20">
                        <svg class="w-16 h-16 md:w-24 md:h-24 mx-auto mb-4 md:mb-6" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                            <path d="M12 18v-6" />
                            <path d="m9 15 3 3 3-3" />
                        </svg>
                        <p class="text-xs font-black uppercase tracking-widest italic">Data Kosong</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- New Visualization Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12 mt-8 md:mt-12 mb-8 md:mb-16">
        <!-- Category Revenue Chart -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
            <div class="p-5 md:p-10 border-b border-gray-100 dark:border-gray-700">
                <h2
                    class="text-xl md:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">
                    Revenue by Category</h2>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Distribusi Omzet Per
                    Kategori</p>
            </div>
            <div class="p-5 md:p-10 flex-1 flex flex-col justify-center">
                <div class="relative h-[250px] md:h-[300px]" wire:ignore>
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-6 md:mt-8 grid grid-cols-2 gap-3 md:gap-4">
                    @foreach ($categoryData->take(4) as $cat)
                        <div
                            class="flex items-center justify-between text-[10px] font-black uppercase tracking-tight bg-gray-50 dark:bg-gray-900/50 p-3 md:p-4 rounded-2xl">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full mr-2"
                                    style="background-color: {{ ['#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#8b5cf6', '#06b6d4'][$loop->index % 6] }}">
                                </div>
                                <span class="text-gray-500 line-clamp-1">{{ $cat->category_name }}</span>
                            </div>
                            <span
                                class="text-gray-800 dark:text-white ml-2">Rp{{ number_format($cat->total_revenue, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Monthly Growth Chart -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-[3.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
            <div class="p-5 md:p-10 border-b border-gray-100 dark:border-gray-700">
                <h2
                    class="text-xl md:text-2xl font-bold uppercase tracking-tight text-gray-800 dark:text-white">
                    Monthly Growth</h2>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Tren Omzet 6 Bulan
                    Terakhir</p>
            </div>
            <div class="p-5 md:p-10 flex-1">
                <div class="relative h-[280px] md:h-[350px]" wire:ignore>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', function() {
            // Function to safely create/recreate charts
            const initChart = (id, config) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;

                // Destroy existing instance if it exists
                const existingChart = Chart.getChart(canvas);
                if (existingChart) {
                    existingChart.destroy();
                }

                return new Chart(canvas, config);
            };

            // 1. Weekly Performance Chart
            const weeklyData = @json($weeklyData);
            initChart('weeklyChart', {
                type: 'line',
                data: {
                    labels: weeklyData.map(d => d.day),
                    datasets: [{
                            label: 'Omzet',
                            data: weeklyData.map(d => d.revenue),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                        },
                        {
                            label: 'Profit',
                            data: weeklyData.map(d => d.profit),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 4,
                            pointBackgroundColor: '#ef4444',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            callbacks: {
                                label: (context) => {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    label += new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        maximumFractionDigits: 0
                                    }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9ca3af',
                                callback: (value) => new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0
                                }).format(value)
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });

            // 2. Category Distribution Chart
            const catData = @json($categoryData);
            initChart('categoryChart', {
                type: 'doughnut',
                data: {
                    labels: catData.map(d => d.category_name),
                    datasets: [{
                        data: catData.map(d => d.total_revenue),
                        backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#8b5cf6',
                            '#06b6d4'
                        ],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            callbacks: {
                                label: (context) => {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    label += new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        maximumFractionDigits: 0
                                    }).format(context.parsed);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // 3. Monthly Growth Chart
            const monthlyData = @json($monthlyData);
            initChart('monthlyChart', {
                type: 'bar',
                data: {
                    labels: monthlyData.map(d => d.month),
                    datasets: [{
                        label: 'Omzet',
                        data: monthlyData.map(d => d.revenue),
                        backgroundColor: '#3b82f6',
                        borderRadius: 12,
                        barThickness: 30,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            callbacks: {
                                label: (context) => {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    label += new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        maximumFractionDigits: 0
                                    }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9ca3af',
                                callback: (value) => new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0
                                }).format(value)
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });
        });
    </script>

    <!-- Welcome SOP Modal -->
    <div x-data="{
        showWelcome: false,
        init() {
            const isStaff = ['kasir', 'pengelola_jurusan'].includes('{{ session('active_role_name') }}');
            if (isStaff && !localStorage.getItem('hasSeenWelcomeGuide_v1')) {
                setTimeout(() => { this.showWelcome = true; }, 800);
            }
        },
        closeWelcome(goToGuide = false) {
            localStorage.setItem('hasSeenWelcomeGuide_v1', 'true');
            this.showWelcome = false;
            if (goToGuide) {
                window.location.href = '{{ route('guide') }}';
            }
        }
    }" x-show="showWelcome" x-transition:enter="transition ease-out duration-350"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/80 backdrop-blur-md"
        style="display: none;">

        <div
            class="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800 transition-all max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div
                class="p-8 bg-gradient-to-br from-primary-blue to-blue-700 text-white relative overflow-hidden flex-shrink-0">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                        <svg class="w-8 h-8 text-white animate-bounce mt-1" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <span
                            class="px-2.5 py-0.5 bg-white/25 text-[10px] font-black uppercase tracking-widest rounded-full border border-white/10">Staff
                            Baru / Operator</span>
                        <h3 class="text-2xl font-black italic tracking-tight mt-1">Halo, Selamat Datang!</h3>
                        <p class="text-xs text-blue-100/90 mt-1">Kami telah menyiapkan ringkasan SOP kerja kasir untuk
                            Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-8 space-y-6 overflow-y-auto no-scrollbar flex-1">
                <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">3 Langkah Utama
                    Alur Kerja Kasir:</h4>

                <div class="space-y-4">
                    <!-- Step 1 -->
                    <div
                        class="flex gap-4 items-start p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-black flex-shrink-0">
                            1</div>
                        <div>
                            <h5 class="text-sm font-black text-gray-900 dark:text-white leading-tight">Absen Buka &
                                Stok Awal</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                Klik tombol <strong>"Buka Kasir"</strong> di dashboard utama. Isi absen masuk Anda, lalu
                                input jumlah <strong>Stok Awal (Opening Stock)</strong> untuk semua produk aktif di toko
                                sebelum memulai pelayanan.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="flex gap-4 items-start p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-black flex-shrink-0">
                            2</div>
                        <div>
                            <h5 class="text-sm font-black text-gray-900 dark:text-white leading-tight">Proses Transaksi
                                & Struk</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                Pilih produk customer di menu instan atau scan barcode. Tentukan kuantitas dan status
                                pelunasan (Lunas, Hutang/Piutang, atau Pending Kembalian), lalu enter untuk mencetak
                                struk belanja.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div
                        class="flex gap-4 items-start p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-black flex-shrink-0">
                            3</div>
                        <div>
                            <h5 class="text-sm font-black text-gray-900 dark:text-white leading-tight">Stok Akhir &
                                Tutup Sesi</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                Di akhir shift, tekan tombol <strong>"Selesai"</strong> di POS. Masukkan sisa produk
                                fisik (Closing Stock) di rak, hitung uang fisik laci kasir secara manual, dan tulis
                                laporan serah terima.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div
                        class="flex gap-4 items-start p-4 bg-red-500/5 dark:bg-red-950/10 rounded-2xl border border-red-500/10">
                        <div
                            class="w-8 h-8 rounded-full bg-primary-red text-white flex items-center justify-center text-xs font-black flex-shrink-0">
                            4</div>
                        <div>
                            <h5 class="text-sm font-black text-primary-red leading-tight">Kerahasiaan & Keamanan Data
                            </h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                <strong>Dilarang keras</strong> mengambil screenshot, merekam, atau menyebarkan data
                                transaksi, kas, dan informasi keuangan aplikasi ini ke pihak luar. Seluruh tindakan
                                mencurigakan akan terekam otomatis di log audit keamanan.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="p-4 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-500/20 text-xs font-semibold flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Anda dapat membuka halaman panduan lengkap & SOP interaktif kapan saja melalui menu
                        <strong>"Petunjuk & SOP"</strong> di sidebar.</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div
                class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-3 justify-end flex-shrink-0">
                <button @click="closeWelcome(false)"
                    class="px-6 py-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-black rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-all text-center">
                    Langsung Kerja
                </button>
                <button @click="closeWelcome(true)"
                    class="px-8 py-3.5 bg-gradient-to-r from-primary-blue to-blue-600 text-white text-xs font-black rounded-2xl hover:bg-blue-600 shadow-lg shadow-blue-500/25 transition-all text-center">
                    Pelajari SOP Lengkap
                </button>
            </div>
        </div>

    </div>
</div>
