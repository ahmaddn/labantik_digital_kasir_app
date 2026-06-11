<!-- Sidebar -->
<aside
    class="hidden md:flex md:flex-shrink-0 flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 relative z-50 transition-all duration-500 ease-in-out"
    :class="sidebarOpen ? 'w-80 opacity-100 translate-x-0' : 'w-0 opacity-0 -translate-x-full overflow-hidden'">
    <div class="flex flex-col h-full">
        <!-- Brand -->
        <div class="p-10 flex items-center justify-between">
            <div class="flex items-center overflow-hidden">
                <img src="{{ asset('favicon.png') }}" 
                    class="w-14 h-14 bg-white rounded-[1.25rem] p-2 flex items-center justify-center shadow-2xl shadow-blue-900/20 border-4 border-white dark:border-gray-800 flex-shrink-0 object-contain">
                <div class="ml-5 whitespace-nowrap">
                    <h1
                        class="text-2xl font-black tracking-tighter leading-none text-primary-blue dark:text-primary-blue-light uppercase italic">
                        LabAntik</h1>
                    <span
                        class="text-[10px] font-black text-primary-red tracking-[0.3em] uppercase italic">POS Digital
                        System</span>
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

            @php
                $isSessionFinished = \App\Models\DailyRecap::whereDate('date', now())
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

            <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Aktivitas Harian</p>

            <a href="{{ route('buku-kas') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('buku-kas') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h12"/><path d="M4 14h9"/><path d="M19 6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8l-2-2Z"/></svg>
                Buku Kas Internal
            </a>

            <a href="{{ route('daily-recap') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('daily-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" /><path d="M14 2v4a2 2 0 0 0 2 2h4" /><path d="M9 15h6" /><path d="M9 11h6" /><path d="M9 19h6" /></svg>
                Rekap Harian & Audit
            </a>

            <a href="{{ route('transactions') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('transactions') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20" /><path d="m17 5-5-3-5 3" /><path d="m17 19-5 3-5-3" /><path d="M2 12h20" /><path d="m5 7-3 5 3 5" /><path d="m19 7 3 5-3 5" /></svg>
                History Transaksi
            </a>

            <a href="{{ route('debts') }}"
                class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('debts') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M19 8v2" /><path d="M20 18v2" /><path d="M12 12h.01" /><path d="M16 12h.01" /><path d="M8 12h.01" /><path d="M12 16h.01" /><path d="M16 16h.01" /><path d="M8 16h.01" /><rect width="20" height="14" x="2" y="6" rx="2" /></svg>
                Hutang & Kembalian
            </a>

            <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Manajemen Data</p>

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

            <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4 ml-5">Analisis & Laporan</p>

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

            <div class="h-10"></div>
        </nav>
    </div>
</aside>
