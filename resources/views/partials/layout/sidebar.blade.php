<!-- Mobile Sidebar Overlay Backdrop -->
<div x-show="sidebarOpen" @click="toggleSidebar" class="lg:hidden fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm"
    style="display: none;" x-transition:enter="transition-opacity ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

<aside x-data="{ isMobile: window.innerWidth < 1024 }" x-on:resize.window="isMobile = window.innerWidth < 1024"
    class="z-50 flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 ease-in-out shadow-xl lg:shadow-none flex shrink-0"
    :class="isMobile
        ? 'fixed inset-y-0 left-0 w-72 ' + (sidebarOpen ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0 pointer-events-none w-0 overflow-hidden')
        : 'relative ' + (sidebarOpen ? 'translate-x-0 opacity-100 w-64' : 'w-0 opacity-0 -translate-x-full overflow-hidden')">
    
    <div class="flex flex-col h-full">
        <!-- Brand Header -->
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                @php
                    $activeJurusanId = session('active_jurusan_id');
                    $themeSettings = null;
                    if ($activeJurusanId) {
                        $jurusanModel = \App\Models\Jurusan::find($activeJurusanId);
                        if ($jurusanModel && $jurusanModel->theme_settings) {
                            $themeSettings = $jurusanModel->theme_settings;
                        }
                    }
                    $tefaName = $themeSettings['tefa_name'] ?? 'Superapps TEFA';
                    $tefaLogo = $themeSettings['tefa_logo'] ?? '';
                    $roleLabel = session('active_role_label') ??
                        (session('active_role_name') === 'superadmin' ? 'Superadmin' : 'TEFA ' . session('active_jurusan_name', 'Unit'));
                @endphp
                <img src="{{ $tefaLogo ? asset('storage/' . $tefaLogo) : asset('favicon.png') }}"
                    alt="Logo TEFA"
                    class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 shrink-0 object-contain">
                <div class="min-w-0">
                    <h1 class="text-sm font-bold tracking-tight text-slate-900 dark:text-white truncate">
                        {{ $tefaName }}
                    </h1>
                    <span class="inline-block text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                        {{ $roleLabel }}
                    </span>
                </div>
            </div>
            
            <!-- Close Button (Mobile Only) -->
            <button @click="toggleSidebar"
                class="lg:hidden p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                title="Tutup Menu">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto no-scrollbar">
            <!-- TEFA Switcher -->
            <div>
                @livewire('layout.tefa-switcher')
            </div>

            <!-- 1. UTAMA -->
            <div class="space-y-1">
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Utama</p>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                @php
                    $activeJurusanId = session('active_jurusan_id');
                    $isSessionFinished = \App\Models\DailyRecap::withoutGlobalScope('active')
                        ->whereDate('date', now())
                        ->when($activeJurusanId, fn($q) => $q->where(fn($sub) => $sub->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id')))
                        ->where('actual_cash', '>', 0)
                        ->exists();

                    $hasClockedOut = \App\Models\CashierAttendance::where('user_id', auth()->id())
                        ->whereDate('date', now()->toDateString())
                        ->when($activeJurusanId, fn($q) => $q->where(fn($sub) => $sub->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id')))
                        ->whereNotNull('clock_out')
                        ->exists();

                    $hasHigherRole = auth()->user()->roles()->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])->exists()
                        || in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']);

                    $isScheduled = true;
                    if (session('active_role_name') === 'kasir' && !$hasHigherRole) {
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

                @if ($hasHigherRole)
                    {{-- Superadmin & Pengelola: Jika sesi tutup, tampil Kasir (Pasca-Closing), jika buka tampil Buka Mesin Kasir --}}
                    @if ($isSessionFinished)
                        <a href="{{ route('kasir') }}"
                            class="flex items-center gap-3 px-3 py-2 text-xs font-bold rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 transition-colors"
                            title="Buka Kasir Mode Pasca-Closing (Transaksi Masuk Besok H+1)">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Kasir (Pasca-Closing)</span>
                        </a>
                    @else
                        <a href="{{ route('kasir') }}"
                            class="flex items-center gap-3 px-3 py-2 text-xs font-bold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition-colors">
                            <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            <span>Buka Mesin Kasir</span>
                        </a>
                    @endif
                @elseif (session('active_role_name') === 'kasir')
                    {{-- Kasir Murni: Tidak ada pasca-closing atau darurat. Terkunci jika shift/sesi tutup --}}
                    @if ($isSessionFinished || $hasClockedOut)
                        <div class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-xl bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 cursor-not-allowed"
                            title="Sesi kasir hari ini telah selesai ditutup">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <span>Kasir: Sesi Ditutup</span>
                        </div>
                    @elseif (!$isScheduled)
                        <div class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-xl bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 cursor-not-allowed">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <span>Kasir: Tidak Terjadwal</span>
                        </div>
                    @else
                        <a href="{{ route('kasir') }}"
                            class="flex items-center gap-3 px-3 py-2 text-xs font-bold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition-colors">
                            <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            <span>Buka Mesin Kasir</span>
                        </a>
                    @endif
                @endif

                @if (auth()->check() && count(auth()->user()->getAvailableAccesses()) > 1)
                    <a href="{{ route('select-role') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-xl text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 transition-colors">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        <span>Ganti Hak Akses</span>
                    </a>
                @endif
            </div>

            @if (session('active_role_name') === 'kasir')
                <!-- === KASIR OPERATIONAL WORKFLOW === -->

                <!-- 2. OPERASIONAL & TUGAS KASIR -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Operasional & Tugas</p>

                    @php
                        $myPendingTasksCount = \App\Models\CashierTaskAssignment::where('assigned_to', auth()->id())
                            ->whereHas('taskDefinition', fn($q) => $q->where('date', '>=', now()->toDateString()))
                            ->whereDoesntHave('submissions', fn($q) => $q->where('approval_status', 'approved'))
                            ->count();
                    @endphp
                    <a href="{{ route('my-tasks') }}"
                        class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('my-tasks') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Tugas Saya</span>
                        </div>
                        @if ($myPendingTasksCount > 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-500 text-white rounded-full">
                                {{ $myPendingTasksCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('schedules') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('schedules') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <span>Jadwal Jaga Kasir</span>
                    </a>

                    <a href="{{ route('documentation-schedules') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('documentation-schedules') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <span>Jadwal Dokumentasi</span>
                    </a>

                    <a href="{{ route('cashier-notes') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('cashier-notes') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                        <span>Catatan & Serah Terima</span>
                    </a>

                    <a href="{{ route('leaderboard') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('leaderboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0V5.625c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125v8.625m6 0h-6" />
                        </svg>
                        <span>Papan Skor & Poin</span>
                    </a>
                </div>

                <!-- 3. TRANSAKSI & SESI KASIR -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Transaksi & Kasir</p>

                    <a href="{{ route('transactions') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('transactions') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z" />
                        </svg>
                        <span>Riwayat Transaksi</span>
                    </a>

                    <a href="{{ route('debts') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('debts') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Hutang & Kembalian</span>
                    </a>

                    <a href="{{ route('daily-recap') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('daily-recap') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Rekap Harian & Sesi</span>
                    </a>
                </div>

            @else
                <!-- === MANAGEMENT & SUPERADMIN WORKFLOW === -->

                <!-- 2. KATALOG & INVENTARIS -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Inventaris & Produk</p>

                    <a href="{{ route('products') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('products') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                        <span>Daftar Produk</span>
                    </a>

                    <a href="{{ route('categories') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('categories') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>Kategori Produk</span>
                    </a>

                    <a href="{{ route('modifiers') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('modifiers') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Topping & Varian</span>
                    </a>

                    <a href="{{ route('suppliers') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('suppliers') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.948c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75" />
                        </svg>
                        <span>Supplier Rekanan</span>
                    </a>

                    <a href="{{ route('inventory-report') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('inventory-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        <span>Laporan Stok & Selisih</span>
                    </a>
                </div>

                <!-- 3. KEUANGAN & KAS -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Keuangan & Kas</p>

                    <a href="{{ route('buku-kas') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('buku-kas') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Buku Kas Internal</span>
                    </a>

                    <a href="{{ route('virtual-cash') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('virtual-cash') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        <span>Kas Virtual (QRIS / Bank)</span>
                    </a>

                    <a href="{{ route('debts') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('debts') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Hutang & Kembalian</span>
                    </a>
                </div>

                <!-- 4. OPERASIONAL KASIR -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Operasional Kasir</p>

                    <a href="{{ route('tasks') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tasks') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>Penugasan & Verifikasi</span>
                    </a>

                    <a href="{{ route('schedules') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('schedules') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <span>Jadwal Piket Kasir</span>
                    </a>

                    <a href="{{ route('documentation-schedules') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('documentation-schedules') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <span>Jadwal Dokumentasi</span>
                    </a>

                    <a href="{{ route('attendances') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('attendances') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Presensi & Shift Kasir</span>
                    </a>

                    <a href="{{ route('cashier-notes') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('cashier-notes') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                        <span>Catatan & Briefing Kasir</span>
                    </a>

                    <a href="{{ route('leaderboard') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('leaderboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0V5.625c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125v8.625m6 0h-6" />
                        </svg>
                        <span>Papan Skor & Poin</span>
                    </a>
                </div>

                <!-- 5. LAPORAN & BAGI HASIL -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Laporan & Bagi Hasil</p>

                    <a href="{{ route('daily-recap') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('daily-recap') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Rekap Harian & Audit</span>
                    </a>

                    <a href="{{ route('transactions') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('transactions') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z" />
                        </svg>
                        <span>Riwayat Transaksi</span>
                    </a>

                    <a href="{{ route('supplier-report') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('supplier-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Bagi Hasil Supplier</span>
                    </a>

                    <a href="{{ route('bagi-hasil') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('bagi-hasil') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                        </svg>
                        <span>Bagi Hasil Mingguan TEFA</span>
                    </a>

                    <a href="{{ route('weekly-performance') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('weekly-performance') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        <span>Performa Mingguan</span>
                    </a>

                    <a href="{{ route('monthly-recap') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('monthly-recap') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <span>Rekap Bulanan</span>
                    </a>

                    <a href="{{ route('yearly-recap') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('yearly-recap') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        <span>Rekap Tahunan</span>
                    </a>
                </div>
            @endif

            <!-- 6. PENGATURAN SISTEM (SUPERADMIN / PENGELOLA) -->
            @if (in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']))
            <div class="space-y-1 pt-2 border-t border-slate-100 dark:border-slate-800">
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pengaturan Sistem</p>

                <a href="{{ route('users') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('users') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span>Manajemen User</span>
                </a>

                <a href="{{ route('jurusans') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('jurusans') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                    <span>Jurusan & Unit TEFA</span>
                </a>

                @if (session('active_role_name') === 'superadmin')
                    <a href="{{ route('roles') }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('roles') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        <span>Manajemen Role</span>
                    </a>
                @endif

                <a href="{{ route('theme-customizer') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('theme-customizer') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
                    </svg>
                    <span>Kustomisasi Tampilan</span>
                </a>

                <a href="{{ route('tefa.api-keys') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tefa.api-keys') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                    <span>API Key TEFA</span>
                </a>

                <a href="{{ route('security-logs') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('security-logs') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Log Keamanan</span>
                </a>

                <a href="{{ route('labantik.candidates') }}"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('labantik.candidates') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>Calon Anggota Labantik</span>
                </a>
            </div>
            @endif
        </nav>
    </div>
</aside>
