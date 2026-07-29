<!-- Sidebar -->
<aside
    class="hidden md:flex md:flex-shrink-0 flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 relative z-50 transition-all duration-500 ease-in-out"
    :class="sidebarOpen ? 'w-80 opacity-100 translate-x-0' : 'w-0 opacity-0 -translate-x-full overflow-hidden'">
    <div class="flex flex-col h-full">
        <!-- Brand -->
        <div class="p-10 flex items-center justify-between">
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
                    $roleLabel = session('active_role_label') ?? (session('active_role_name') === 'superadmin' ? 'Superadmin' : 'Tefa ' . session('active_jurusan_name', 'RPL'));
                @endphp
                <img src="{{ $tefaLogo ? asset('storage/' . $tefaLogo) : asset('favicon.png') }}" 
                    class="w-14 h-14 bg-white rounded-[1.25rem] p-2 flex items-center justify-center shadow-2xl shadow-blue-900/20 border-4 border-white dark:border-gray-800 flex-shrink-0 object-contain">
                <div class="ml-4 whitespace-nowrap">
                    <h1 class="text-xl font-black tracking-tight leading-none text-primary-blue dark:text-primary-blue-light uppercase italic">
                        {{ $tefaName }}</h1>
                    <span class="text-[9px] font-black text-primary-red tracking-wider uppercase italic">
                        {{ $roleLabel }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-8 space-y-2 mt-4 overflow-y-auto no-scrollbar pb-10">
            <p class="text-[9px] font-black text-primary-blue uppercase tracking-[0.3em] mb-4 ml-5">Beranda & Utama</p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" /></svg>
                Dashboard Overview
            </a>

            @if(auth()->check() && count(auth()->user()->getAvailableAccesses()) > 1)
            <a href="{{ route('select-role') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl text-amber-500 dark:text-amber-400 hover:bg-amber-500/5 transition-all">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m17 11 3-3-3-3"/><path d="m20 8-8 0"/></svg>
                Ganti Hak Akses
            </a>
            @endif

            @if(session('active_role_name') !== 'superadmin')
                @php
                    $isSessionFinished = \App\Models\DailyRecap::whereDate('date', now())
                        ->where('jurusan_id', session('active_jurusan_id'))
                        ->where('actual_cash', '>', 0)
                        ->exists();
                @endphp

                @if($isSessionFinished)
                    <div class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-gray-400 dark:bg-gray-800 text-white shadow-xl opacity-80 cursor-not-allowed uppercase italic tracking-wider">
                        <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" /><path d="M3 6h18" /><path d="M16 10a4 4 0 0 1-8 0" /></svg>
                        Sesi Kasir Berakhir
                    </div>
                @else
                    <a href="{{ route('kasir') }}"
                        class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-primary-red text-white shadow-2xl shadow-red-500/30 hover:scale-105 active:scale-95 transition-all uppercase italic tracking-wider">
                        <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" /><path d="M3 6h18" /><path d="M16 10a4 4 0 0 1-8 0" /></svg>
                        Buka Mode Kasir
                    </a>
                @endif
            @endif

            <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Aktivitas Harian</p>

            @if(session('active_role_name') === 'pengelola_jurusan' || session('active_role_name') === 'kasir')
                <a href="{{ route('buku-kas') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('buku-kas') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h12"/><path d="M4 14h9"/><path d="M19 6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8l-2-2Z"/></svg>
                    Buku Kas Internal
                </a>

                <a href="{{ route('monthly-closing') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('monthly-closing') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    Tutup Buku Bulanan
                </a>

                <a href="{{ route('daily-recap') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('daily-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" /><path d="M14 2v4a2 2 0 0 0 2 2h4" /><path d="M9 15h6" /><path d="M9 11h6" /><path d="M9 19h6" /></svg>
                    Rekap Harian & Audit
                </a>
            @endif

            <a href="{{ route('transactions') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('transactions') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20" /><path d="m17 5-5-3-5 3" /><path d="m17 19-5 3-5-3" /><path d="M2 12h20" /><path d="m5 7-3 5 3 5" /><path d="m19 7 3 5-3 5" /></svg>
                History Transaksi
            </a>

            @if(session('active_role_name') === 'pengelola_jurusan' || session('active_role_name') === 'kasir')
                <a href="{{ route('debts') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('debts') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M19 8v2" /><path d="M20 18v2" /><path d="M12 12h.01" /><path d="M16 12h.01" /><path d="M8 12h.01" /><path d="M12 16h.01" /><path d="M16 16h.01" /><path d="M8 16h.01" /><rect width="20" height="14" x="2" y="6" rx="2" /></svg>
                    Hutang & Kembalian
                </a>
            @endif

            @if(session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan' || session('active_role_name') === 'kasir')
                <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Manajemen Data</p>

                @if(session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan' || session('active_role_name') === 'kasir')
                    <a href="{{ route('products') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('products') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15" /><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" /><path d="m3.3 7 8.7 5 8.7-5" /><path d="M12 22V12" /></svg>
                        Manajemen Produk
                    </a>

                    <a href="{{ route('categories') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('categories') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" /><path d="M3 9h18" /><path d="M9 21V9" /></svg>
                        Kategori Produk
                    </a>

                    <a href="{{ route('suppliers') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('suppliers') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
                        Manajemen Supplier
                    </a>
                @endif

                <a href="{{ route('theme-customizer') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('theme-customizer') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" /><circle cx="7.5" cy="10.5" r="1.5" /><circle cx="11.5" cy="7.5" r="1.5" /><circle cx="16.5" cy="9.5" r="1.5" /><circle cx="15.5" cy="14.5" r="1.5" /></svg>
                    Kustomisasi Tema
                </a>

                @if(in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan', 'kasir']))
                    <a href="{{ route('schedules') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('schedules') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        Jadwal Kasir
                    </a>
                @endif

                @if(session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan')
                    <a href="{{ route('tasks') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('tasks') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        Tugas Harian Kasir
                    </a>
                @endif

                @if(session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan')
                    <a href="{{ route('users') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('users') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Manajemen User
                    </a>
                @endif

                @if(session('active_role_name') === 'superadmin')
                    <a href="{{ route('jurusans') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('jurusans') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Manajemen Jurusan
                    </a>
                    <a href="{{ route('roles') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('roles') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 17v-4h6v4M9 7v4h6V7"/></svg>
                        Manajemen Role
                    </a>
                @endif
            @endif

            @if(true)
                <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Analisis & Laporan</p>

                <a href="{{ route('cashier-notes') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('cashier-notes') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Catatan Kasir
                </a>

                @if(in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan', 'kasir']))
                    <a href="{{ route('attendances') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('attendances') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75M9 21h6m-3-10V3"/></svg>
                        Absensi & Shift Kasir
                    </a>
                @endif

                <a href="{{ route('monthly-recap') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('monthly-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /><path d="M8 14h.01" /><path d="M12 14h.01" /><path d="M16 14h.01" /><path d="M8 18h.01" /><path d="M12 18h.01" /><path d="M16 18h.01" /></svg>
                    Rekap Bulanan
                </a>

                <a href="{{ route('yearly-recap') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('yearly-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4" /><path d="M3.34 19a10 10 0 1 1 17.32 0" /><path d="m9.05 9 5.64 5.64" /><circle cx="12" cy="12" r="2" /></svg>
                    Rekap Tahunan
                </a>

                <a href="{{ route('inventory-report') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('inventory-report') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20" /><path d="M2 12h20" /><path d="m5 7-3 5 3 5" /><path d="m19 7 3 5-3 5" /></svg>
                    Laporan Stok & Selisih
                </a>

                <a href="{{ route('supplier-report') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('supplier-report') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
                    Bagi Hasil Supplier
                </a>

                <a href="{{ route('bagi-hasil') }}"
                    class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('bagi-hasil') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M19 8v2" /><path d="M20 18v2" /><rect width="20" height="14" x="2" y="6" rx="2" /></svg>
                    Bagi Hasil Mingguan
                </a>
            @endif

            <div class="h-10"></div>
        </nav>
    </div>
</aside>
