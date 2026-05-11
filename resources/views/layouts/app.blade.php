<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Kasir LabAntik') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body x-data="{
    sidebarOpen: localStorage.getItem('sidebar-open') !== 'false',
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebar-open', this.sidebarOpen);
    }
}"
    class="bg-bone-white dark:bg-dark-soft text-slate-900 dark:text-bone-white antialiased transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="hidden md:flex md:flex-shrink-0 flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 relative z-50 transition-all duration-500 ease-in-out"
            :class="sidebarOpen ? 'w-80 opacity-100 translate-x-0' : 'w-0 opacity-0 -translate-x-full overflow-hidden'">
            <div class="flex flex-col h-full">
                <!-- Brand -->
                <div class="p-10 flex items-center justify-between">
                    <div class="flex items-center overflow-hidden">
                        <div
                            class="w-14 h-14 bg-primary-blue rounded-[1.25rem] flex items-center justify-center text-white font-black italic shadow-2xl shadow-blue-900/20 text-xl border-4 border-white dark:border-gray-800 flex-shrink-0">
                            LA
                        </div>
                        <div class="ml-5 whitespace-nowrap">
                            <h1
                                class="text-2xl font-black tracking-tighter leading-none text-primary-blue dark:text-primary-blue-light uppercase italic">
                                LabAntik</h1>
                            <span
                                class="text-[10px] font-black text-primary-red tracking-[0.3em] uppercase italic">Digital
                                System</span>
                        </div>
                    </div>
                    <button @click="toggleSidebar" class="p-2 text-gray-400 hover:text-primary-blue transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </button>
                </div>

                <!-- Nav -->
                <nav class="flex-1 px-8 space-y-2 mt-4 overflow-y-auto no-scrollbar pb-10">
                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.3em] mb-4 ml-5">Main Dashboard
                    </p>

                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1" />
                            <rect width="7" height="5" x="14" y="3" rx="1" />
                            <rect width="7" height="9" x="14" y="12" rx="1" />
                            <rect width="7" height="5" x="3" y="16" rx="1" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('kasir') }}"
                        class="flex items-center px-6 py-5 text-sm font-black rounded-[2rem] bg-primary-red text-white shadow-2xl shadow-red-500/30 hover:scale-105 active:scale-95 transition-all uppercase italic tracking-wider">
                        <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                            <path d="M3 6h18" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                        Buka Kasir
                    </a>

                    <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.3em] mb-4 ml-5">Data &
                        Inventaris</p>

                    <a href="{{ route('transactions') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('transactions') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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

                    <a href="{{ route('products') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('products') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('categories') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18" />
                            <path d="M9 21V9" />
                        </svg>
                        Kategori Produk
                    </a>

                    <a href="{{ route('suppliers') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('suppliers') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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

                    <a href="{{ route('debts') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('debts') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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

                    <div class="h-px bg-gray-100 dark:bg-gray-800 my-8 mx-4"></div>

                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.3em] mb-4 ml-5">Laporan & Rekap
                    </p>

                    <a href="{{ route('daily-recap') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('daily-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                            <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                            <path d="M9 15h6" />
                            <path d="M9 11h6" />
                            <path d="M9 19h6" />
                        </svg>
                        Rekap Harian
                    </a>

                    <a href="{{ route('monthly-recap') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('monthly-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('yearly-recap') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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

                    <a href="{{ route('inventory-report') }}"
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('inventory-report') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
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
                        class="flex items-center px-6 py-4 text-sm font-black rounded-2xl transition-all {{ request()->routeIs('supplier-report') ? 'bg-primary-blue text-white shadow-xl shadow-blue-900/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Laporan Bagi Hasil Supplier
                    </a>


                    <div class="pt-10">
                        <button id="theme-toggle"
                            class="w-full flex items-center justify-between px-8 py-5 bg-gray-50 dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-inner group transition-all">
                            <span
                                class="text-xs font-black uppercase tracking-widest text-gray-400 group-hover:text-primary-blue transition-colors">Tema
                                Sistem</span>
                            <div class="flex items-center space-x-3">
                                <div id="theme-toggle-light-icon"
                                    class="hidden p-2 bg-white dark:bg-gray-900 rounded-xl shadow-sm text-yellow-500">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="4" />
                                        <path d="M12 2v2" />
                                        <path d="M12 20v2" />
                                        <path d="m4.93 4.93 1.41 1.41" />
                                        <path d="m17.66 17.66 1.41 1.41" />
                                        <path d="M2 12h2" />
                                        <path d="M20 12h2" />
                                        <path d="m6.34 17.66-1.41 1.41" />
                                        <path d="m19.07 4.93-1.41 1.41" />
                                    </svg>
                                </div>
                                <div id="theme-toggle-dark-icon"
                                    class="hidden p-2 bg-white dark:bg-gray-900 rounded-xl shadow-sm text-primary-blue">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                                    </svg>
                                </div>
                            </div>
                        </button>
                    </div>
                </nav>

                <!-- User Footer -->
                <div class="p-8 border-t border-gray-100 dark:border-gray-800">
                    <div
                        class="p-5 bg-gray-50 dark:bg-gray-800 rounded-[2rem] flex items-center justify-between border border-gray-100 dark:border-gray-700 shadow-inner group">
                        <div class="flex items-center overflow-hidden">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary-blue text-white flex items-center justify-center font-black text-lg shadow-lg group-hover:scale-110 transition-transform">
                                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="ml-4 overflow-hidden">
                                <p
                                    class="text-sm font-black truncate text-gray-800 dark:text-white uppercase italic tracking-tighter">
                                    {{ auth()->user()->name ?? 'Admin' }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Administrator
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="p-3 text-gray-400 hover:text-primary-red hover:bg-white dark:hover:bg-gray-900 rounded-xl transition-all shadow-sm">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main
            class="flex-1 overflow-y-auto relative no-scrollbar bg-bone-white dark:bg-dark-bg transition-all duration-500">
            <!-- Floating Toggle Button (Visible when sidebar is closed) -->
            <button x-show="!sidebarOpen" @click="toggleSidebar"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-10"
                x-transition:enter-end="opacity-100 translate-x-0"
                class="fixed left-6 top-6 z-[60] hidden md:flex w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl items-center justify-center text-primary-blue shadow-2xl shadow-blue-900/10 border border-gray-100 dark:border-gray-700 hover:scale-110 transition-all">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </button>

            <!-- Mobile Header -->
            <header
                class="md:hidden flex items-center justify-between p-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 sticky top-0 z-[60]">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-primary-blue rounded-xl flex items-center justify-center text-white font-black italic shadow-lg border-2 border-white dark:border-gray-800">
                        LA</div>
                    <span
                        class="ml-4 font-black italic uppercase text-lg tracking-tighter text-primary-blue dark:text-white">LabAntik</span>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="theme-toggle-mobile"
                        class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 shadow-inner">
                        <div id="theme-toggle-light-icon-mobile" class="hidden text-yellow-500">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2v2" />
                                <path d="M12 20v2" />
                                <path d="m4.93 4.93 1.41 1.41" />
                                <path d="m17.66 17.66 1.41 1.41" />
                                <path d="M2 12h2" />
                                <path d="M20 12h2" />
                                <path d="m6.34 17.66-1.41 1.41" />
                                <path d="m19.07 4.93-1.41 1.41" />
                            </svg>
                        </div>
                        <div id="theme-toggle-dark-icon-mobile" class="hidden text-primary-blue">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                            </svg>
                        </div>
                    </button>
                    <button
                        class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 shadow-inner">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 12h16" />
                            <path d="M4 6h16" />
                            <path d="M4 18h16" />
                        </svg>
                    </button>
                </div>
            </header>

            <div class="w-full px-6 md:px-10 min-h-screen pb-20 transition-all duration-500"
                :class="!sidebarOpen ? 'md:pl-14' : ''">
                {{ $slot }}
            </div>

            @include('partials.toasts')
        </main>
    </div>

    @livewireScripts
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        var themeToggleDarkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
        var themeToggleLightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
            themeToggleLightIconMobile.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
            themeToggleDarkIconMobile.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');
        var themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');

        function toggleTheme() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');
            themeToggleDarkIconMobile.classList.toggle('hidden');
            themeToggleLightIconMobile.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

                // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        }

        themeToggleBtn.addEventListener('click', toggleTheme);
        themeToggleBtnMobile.addEventListener('click', toggleTheme);
    </script>
</body>

</html>
