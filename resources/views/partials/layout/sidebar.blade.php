<!-- Mobile Sidebar Overlay Backdrop -->
<div x-show="sidebarOpen" @click="toggleSidebar" class="lg:hidden fixed inset-0 z-40 bg-gray-950/40 backdrop-blur-sm"
    style="display: none;" x-transition></div>

<aside x-data="{ isMobile: window.innerWidth < 1024 }" x-on:resize.window="isMobile = window.innerWidth < 1024"
    class="z-50 flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-all duration-500 ease-in-out shadow-2xl md:shadow-none flex"
    :class="isMobile
        ?
        'fixed inset-y-0 left-0 w-64 ' + (sidebarOpen ? 'translate-x-0 opacity-100' :
            '-translate-x-full opacity-0 pointer-events-none w-0 overflow-hidden') :
        'relative ' + (sidebarOpen ? 'translate-x-0 opacity-100 w-64' :
            'w-0 opacity-0 -translate-x-full overflow-hidden')">
    <div class="flex flex-col h-full">
        <!-- Brand -->
        <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div class="flex items-center overflow-hidden">
                @php
                    $activeJurusanId = session('active_jurusan_id');
                    $themeSettings = null;
                    if ($activeJurusanId) {
                        $jurusanModel = \App\Models\Jurusan::find($activeJurusanId);
                        if ($jurusanModel && $jurusanModel->theme_settings) {
                            $themeSettings = $jurusanModel->theme_settings;
                        }
                    }
                    $tefaName = $themeSettings['tefa_name'] ?? 'Superapps Tefa';
                    $tefaLogo = $themeSettings['tefa_logo'] ?? '';
                    $roleLabel =
                        session('active_role_label') ??
                        (session('active_role_name') === 'superadmin'
                            ? 'Superadmin'
                            : 'Tefa ' . session('active_jurusan_name', 'RPL'));
                @endphp
                <img src="{{ $tefaLogo ? asset('storage/' . $tefaLogo) : asset('favicon.png') }}"
                    class="w-10 h-10 bg-white rounded-[1.25rem] p-2 flex items-center justify-center shadow-2xl shadow-blue-900/20 border-4 border-white dark:border-gray-800 flex-shrink-0 object-contain">
                <div class="ml-4 whitespace-nowrap">
                    <h1
                        class="text-md font-bold tracking-tight leading-none text-primary-blue dark:text-primary-blue-light uppercase italic">
                        {{ $tefaName }}</h1>
                    <span class="text-[8px] font-bold text-primary-red tracking-wider uppercase italic">
                        {{ $roleLabel }}
                    </span>
                </div>
            </div>
            <!-- Close Button (Mobile Only) -->
            <button @click="toggleSidebar"
                class="md:hidden p-2.5 bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-primary-red rounded-xl transition-all shadow-sm"
                title="Tutup Menu">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto no-scrollbar pb-10">
            <div class="px-3 mb-4">
                @livewire('layout.tefa-switcher')
            </div>

            <p class="text-[8px] font-bold text-primary-blue uppercase tracking-[0.3em] mb-4 ml-5">Beranda & Utama</p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect width="7" height="9" x="3" y="3" rx="1" />
                    <rect width="7" height="5" x="14" y="3" rx="1" />
                    <rect width="7" height="9" x="14" y="12" rx="1" />
                    <rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>
                Dashboard Overview
            </a>

            @if (auth()->check() && count(auth()->user()->getAvailableAccesses()) > 1)
                <a href="{{ route('select-role') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl text-amber-500 dark:text-amber-400 hover:bg-amber-500/5 transition-all">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="m17 11 3-3-3-3" />
                        <path d="m20 8-8 0" />
                    </svg>
                    Ganti Hak Akses
                </a>
            @endif

            @if (session('active_role_name') !== 'superadmin')
                @php
                    $isSessionFinished = \App\Models\DailyRecap::whereDate('date', now())
                        ->where('jurusan_id', session('active_jurusan_id'))
                        ->where('actual_cash', '>', 0)
                        ->exists();

                    $hasClockedOut = \App\Models\CashierAttendance::where('user_id', auth()->id())
                        ->where('jurusan_id', session('active_jurusan_id'))
                        ->where('date', now()->toDateString())
                        ->whereNotNull('clock_out')
                        ->exists();

                    $isScheduled = true;
                    $hasHigherRole = auth()
                        ->user()
                        ->roles()
                        ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
                        ->exists();

                    if (session('active_role_name') === 'kasir') {
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

                @if ($hasHigherRole)
                    @if ($isSessionFinished || $hasClockedOut)
                        <div
                            class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-gray-400 dark:bg-gray-800 text-white shadow-xl opacity-80 cursor-not-allowed uppercase italic tracking-wider">
                            <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                                <path d="M3 6h18" />
                                <path d="M16 10a4 4 0 0 1-8 0" />
                            </svg>
                            Sesi Kasir Selesai
                        </div>
                    @else
                        <a href="{{ route('kasir') }}"
                            class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-primary-red text-white shadow-2xl shadow-red-500/30 hover:scale-105 active:scale-95 transition-all uppercase italic tracking-wider">
                            <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                                <path d="M3 6h18" />
                                <path d="M16 10a4 4 0 0 1-8 0" />
                            </svg>
                            Buka Mode Kasir
                        </a>
                    @endif
                @elseif(!$isScheduled)
                    <div
                        class="flex flex-col gap-2 px-6 py-4 rounded-[2rem] bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 shadow-xl opacity-90 cursor-not-allowed">
                        <div class="flex items-center text-xs font-black uppercase italic tracking-wider">
                            <svg class="w-5 h-5 mr-3 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            Tidak Ada Jadwal
                        </div>
                        <span class="text-[9px] font-bold leading-normal uppercase">Anda tidak dijadwalkan jaga kasir
                            hari ini.</span>
                    </div>
                @elseif($hasClockedOut)
                    <div
                        class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-gray-400 dark:bg-gray-800 text-white shadow-xl opacity-80 cursor-not-allowed uppercase italic tracking-wider">
                        <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                            <path d="M3 6h18" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                        Sesi Kasir Selesai
                    </div>
                @elseif($isSessionFinished && session('active_role_name') !== 'kasir')
                    <a href="{{ route('late-report') }}"
                        class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-primary-red text-white shadow-2xl shadow-red-500/30 hover:scale-105 active:scale-95 transition-all uppercase italic tracking-wider">
                        <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                        </svg>
                        Isi Laporan & Clock Out
                    </a>
                @else
                    <a href="{{ route('kasir') }}"
                        class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-primary-red text-white shadow-2xl shadow-red-500/30 hover:scale-105 active:scale-95 transition-all uppercase italic tracking-wider">
                        <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                            <path d="M3 6h18" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                        Buka Mode Kasir
                    </a>
                @endif
            @endif

            <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Aktivitas Harian</p>

            @if (session('active_role_name') === 'kasir')
                @php
                    $myPendingTasksCount = \App\Models\CashierTaskAssignment::where('assigned_to', auth()->id())
                        ->whereHas('taskDefinition', function ($q) {
                            $q->where('date', '>=', now()->toDateString());
                        })
                        ->whereDoesntHave('submissions', function ($q) {
                            $q->where('approval_status', 'approved');
                        })
                        ->count();
                @endphp
                <a href="{{ route('my-tasks') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('my-tasks') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                        <path d="M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2" />
                        <path d="m9 14 2 2 4-4" />
                    </svg>
                    Tugas Saya
                    @if ($myPendingTasksCount > 0)
                        <span
                            class="ml-auto px-2 py-0.5 min-w-[22px] text-center text-[10px] font-black bg-rose-500 text-white rounded-full">{{ $myPendingTasksCount }}</span>
                    @endif
                </a>
            @endif

            @if (session('active_role_name') !== 'kasir')
                <a href="{{ route('buku-kas') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('buku-kas') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10h12" />
                        <path d="M4 14h9" />
                        <path d="M19 6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8l-2-2Z" />
                    </svg>
                    Buku Kas Internal
                </a>
            @endif

            @if (in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan', 'kasir']))
                <a href="{{ route('daily-recap') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('daily-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                        <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                        <path d="M9 15h6" />
                        <path d="M9 11h6" />
                        <path d="M9 19h6" />
                    </svg>
                    Rekap Harian & Audit
                </a>
            @endif

            <a href="{{ route('transactions') }}"
                class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('transactions') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20" />
                    <path d="m17 5-5-3-5 3" />
                    <path d="m17 19-5 3-5-3" />
                    <path d="M2 12h20" />
                    <path d="m5 7-3 5 3 5" />
                    <path d="m19 7 3 5-3 5" />
                </svg>
                History Transaksi
            </a>

            @if (in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan', 'kasir']))
                <a href="{{ route('debts') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('debts') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M19 8v2" />
                        <path d="M20 18v2" />
                        <path d="M12 12h.01" />
                        <path d="M16 12h.01" />
                        <path d="M8 12h.01" />
                        <path d="M12 16h.01" />
                        <path d="M16 16h.01" />
                        <path d="M8 16h.01" />
                        <rect width="20" height="14" x="2" y="6" rx="2" />
                    </svg>
                    Hutang & Kembalian
                </a>
            @endif

            @if (session('active_role_name') === 'superadmin' ||
                    session('active_role_name') === 'pengelola_jurusan' ||
                    session('active_role_name') === 'kasir')
                
                @if (session('active_role_name') !== 'kasir')
                    <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

                    <!-- 1. MANAJEMEN INVENTARIS -->
                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Manajemen
                        Inventaris</p>

                    <a href="{{ route('products') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('products') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15" />
                            <path
                                d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                            <path d="m3.3 7 8.7 5 8.7-5" />
                            <path d="M12 22V12" />
                        </svg>
                        Manajemen Produk
                    </a>

                    <a href="{{ route('categories') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('categories') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18" />
                            <path d="M9 21V9" />
                        </svg>
                        Kategori Produk
                    </a>

                    <a href="{{ route('modifiers') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('modifiers') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        Topping Produk
                    </a>

                    <a href="{{ route('suppliers') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('suppliers') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Manajemen Supplier
                    </a>
                @endif

                <!-- 2. MANAJEMEN OPERASIONAL -->
                <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>
                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Manajemen
                    Operasional</p>

                <a href="{{ route('schedules') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('schedules') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                        <line x1="16" x2="16" y1="2" y2="6" />
                        <line x1="8" x2="8" y1="2" y2="6" />
                        <line x1="3" x2="21" y1="10" y2="10" />
                    </svg>
                    Jadwal Kasir
                </a>

                <a href="{{ route('labantik.candidates') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('labantik.candidates') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M19 8v2" />
                        <path d="M20 18v2" />
                    </svg>
                    Data Calon Labantik
                </a>

                @if (session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan')
                    <a href="{{ route('tasks') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('tasks') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                        </svg>
                        Tugas Harian Kasir
                    </a>
                @endif

                <!-- 3. MANAJEMEN SISTEM -->
                @if (session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan')
                    <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>
                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Manajemen
                        Sistem</p>

                    <a href="{{ route('users') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('users') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Manajemen User
                    </a>

                    <a href="{{ route('jurusans') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('jurusans') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Manajemen Jurusan
                    </a>

                    @if (session('active_role_name') === 'superadmin')
                        <a href="{{ route('roles') }}"
                            class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('roles') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" />
                                <path d="M9 17v-4h6v4M9 7v4h6V7" />
                            </svg>
                            Manajemen Role
                        </a>
                    @endif

                    <a href="{{ route('theme-customizer') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('theme-customizer') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" />
                            <circle cx="7.5" cy="10.5" r="1.5" />
                            <circle cx="11.5" cy="7.5" r="1.5" />
                            <circle cx="16.5" cy="9.5" r="1.5" />
                            <circle cx="15.5" cy="14.5" r="1.5" />
                        </svg>
                        Kustomisasi Tema
                    </a>

                    <a href="{{ route('security-logs') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('security-logs') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        Log Keamanan
                    </a>
                @endif
            @endif

            @if (true)
                <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Analisis & Laporan
                </p>

                <a href="{{ route('cashier-notes') }}"
                    class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('cashier-notes') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Catatan Kasir
                </a>

                @if (in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan', 'kasir']))
                    <a href="{{ route('attendances') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('attendances') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75M9 21h6m-3-10V3" />
                        </svg>
                        Absensi & Shift Kasir
                    </a>
                @endif

                @if (session('active_role_name') !== 'kasir')
                    <a href="{{ route('monthly-recap') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('monthly-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                            <path d="M8 14h.01" />
                            <path d="M12 14h.01" />
                            <path d="M16 14h.01" />
                            <path d="M8 18h.01" />
                            <path d="M12 18h.01" />
                            <path d="M16 18h.01" />
                        </svg>
                        Rekap Bulanan
                    </a>

                    <a href="{{ route('yearly-recap') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('yearly-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 14 4-4" />
                            <path d="M3.34 19a10 10 0 1 1 17.32 0" />
                            <path d="m9.05 9 5.64 5.64" />
                            <circle cx="12" cy="12" r="2" />
                        </svg>
                        Rekap Tahunan
                    </a>
                @endif

                @if (session('active_role_name') !== 'kasir')
                    <a href="{{ route('inventory-report') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('inventory-report') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20" />
                            <path d="M2 12h20" />
                            <path d="m5 7-3 5 3 5" />
                            <path d="m19 7 3 5-3 5" />
                        </svg>
                        Laporan Stok & Selisih
                    </a>

                    <a href="{{ route('supplier-report') }}"
                        class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('supplier-report') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Bagi Hasil Supplier
                    </a>

                    @php
                        $isScheduledCashier = false;
                        if (session('active_role_name') === 'kasir') {
                            $isScheduledCashier = \App\Models\CashierSchedule::where('user_id', auth()->id())->exists();
                        }
                    @endphp

                    @if ($isScheduledCashier)
                        <div
                            class="flex items-center px-6 py-4 text-sm font-black text-gray-400 rounded-2xl cursor-not-allowed opacity-50 select-none bg-gray-50/50 dark:bg-gray-800/30">
                            <svg class="w-5 h-5 mr-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v2" />
                                <path d="M20 18v2" />
                                <rect width="20" height="14" x="2" y="6" rx="2" />
                            </svg>
                            Bagi Hasil Mingguan <span
                                class="ml-auto text-[9px] bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400 px-2 py-1 rounded-md font-bold uppercase tracking-wider">Terkunci</span>
                        </div>
                    @else
                        <a href="{{ route('bagi-hasil') }}"
                            class="flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('bagi-hasil') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v2" />
                                <path d="M20 18v2" />
                                <rect width="20" height="14" x="2" y="6" rx="2" />
                            </svg>
                            Bagi Hasil Mingguan
                        </a>
                    @endif
                @endif
            @endif

            <div class="h-10"></div>
        </nav>
    </div>
</aside>
