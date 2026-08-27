<!-- Global Admin Top Navbar -->
<header class="sticky top-0 z-40 w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 px-5 py-2.5 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-4 flex-1">
        <!-- Sidebar Toggle (Desktop) -->
        <button @click="toggleSidebar" class="hidden lg:flex p-2 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue transition-all">
            <svg x-show="sidebarOpen" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            <svg x-show="!sidebarOpen" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>

        <!-- Global Search Trigger -->
        <button @click="console.log('Dispatching open-global-search'); $dispatch('open-global-search')" class="hidden lg:flex items-center gap-3 px-3 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:border-primary-blue/30 transition-all group flex-1 max-w-md">
            <svg class="w-4 h-4 group-hover:text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <span class="text-sm font-medium">Cari apa saja...</span>
            <div class="ml-auto flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 text-[10px] font-black bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm">Ctrl</kbd>
                <kbd class="px-1.5 py-0.5 text-[10px] font-black bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm">K</kbd>
            </div>
        </button>

        <!-- Page Context (Mobile Branding) -->
        <div class="lg:hidden flex items-center gap-2">
            <button @click="toggleSidebar" class="p-2.5 bg-gray-50 dark:bg-gray-800 text-gray-405 rounded-xl hover:text-primary-blue transition-all shadow-sm" title="Menu">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <!-- Note Notifications Dropdown Bell -->
        @livewire('note-notifications')

        <!-- Theme Toggle -->
        <button @click="toggleTheme()" class="p-2.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all shadow-sm">
            <svg x-show="!darkMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            <svg x-show="darkMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
        </button>

        <!-- Sensor Toggle -->
        <button @click="toggleCensor()" class="p-2.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all shadow-sm" title="Mode Sensor (Sembunyikan Angka Keuangan)">
            <svg x-show="!censorMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg x-show="censorMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
        </button>

        <div class="h-6 w-px bg-gray-100 dark:bg-gray-800 mx-1"></div>

        <!-- Gamification Widgets (Points & Streak) -->
        @auth
            <div class="hidden sm:flex items-center gap-4 mr-2 bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-2xl border border-gray-200 dark:border-gray-700">
                <!-- Streak Badge -->
                <div class="flex items-center gap-1.5" title="Streak Aktivitas Hari Ini">
                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.94-.209.381-.363.887-.453 1.488-.12.802-.073 1.84.195 2.87l.062.24c.024.1.039.223.05.375a2.037 2.037 0 01-.029.511c-.048.24-.154.48-.32.647a.997.997 0 01-1.08.16c-.461-.247-.744-.623-.926-1.08-.182-.456-.224-.959-.224-1.347V6a1 1 0 00-1-1 3 3 0 00-2 2.22c0 1.258.18 2.5.474 3.738.152.64.4 1.25.753 1.807.353.558.836 1.057 1.443 1.487a8.007 8.007 0 005.19 2.09c.477.027.947-.033 1.4-.18a7.995 7.995 0 003.86-2.482c.187-.228.34-.483.47-.752.43-.892.652-1.928.652-3.141 0-1.622-.515-2.91-1.293-3.812a6.002 6.002 0 00-.825-1.012l-.011-.011-.002-.002a1 1 0 00-1.436.17l-.02.027a4.01 4.01 0 01-.262.33c-.758.874-1.808 1.47-3.2 1.47V2.553z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-[10px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                        {{ auth()->user()->streak }} Streak
                    </span>
                </div>
                
                <div class="w-px h-4 bg-gray-200 dark:bg-gray-700"></div>
                
                <!-- Points Badge -->
                <div class="flex items-center gap-1.5" title="Poin Terkumpul Bulan Ini">
                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-[10px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                        {{ auth()->user()->points + auth()->user()->pending_points }} Pts
                    </span>
                </div>
            </div>
        @endauth

        <!-- User Profile Dropdown (Unified Mobile & Desktop) -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="w-10 h-10 bg-primary-blue text-white font-black italic rounded-xl flex items-center justify-center shadow-lg transition-transform active:scale-95 focus:outline-none" title="Menu Akun">
                {{ auth()->check() ? auth()->user()->initials() : 'LA' }}
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-2 z-50 transform origin-top-right">
                
                <!-- User Info Header -->
                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-700/50">
                    <span class="block text-xs font-black text-gray-800 dark:text-white uppercase tracking-tighter">{{ auth()->user()->name ?? 'Guest' }}</span>
                    <span class="block text-[8px] font-black text-gray-400 uppercase tracking-widest mt-0.5">{{ session('active_role_label', 'User') }}</span>
                    
                    <!-- Gamification Widgets (Points & Streak) - Mobile Only -->
                    <div class="flex sm:hidden items-center justify-between gap-2 mt-3 bg-gray-50 dark:bg-gray-900/50 px-3 py-2 rounded-xl border border-gray-100 dark:border-gray-700">
                        <!-- Streak Badge -->
                        <div class="flex items-center gap-1.5" title="Streak Aktivitas Hari Ini">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.94-.209.381-.363.887-.453 1.488-.12.802-.073 1.84.195 2.87l.062.24c.024.1.039.223.05.375a2.037 2.037 0 01-.029.511c-.048.24-.154.48-.32.647a.997.997 0 01-1.08.16c-.461-.247-.744-.623-.926-1.08-.182-.456-.224-.959-.224-1.347V6a1 1 0 00-1-1 3 3 0 00-2 2.22c0 1.258.18 2.5.474 3.738.152.64.4 1.25.753 1.807.353.558.836 1.057 1.443 1.487a8.007 8.007 0 005.19 2.09c.477.027.947-.033 1.4-.18a7.995 7.995 0 003.86-2.482c.187-.228.34-.483.47-.752.43-.892.652-1.928.652-3.141 0-1.622-.515-2.91-1.293-3.812a6.002 6.002 0 00-.825-1.012l-.011-.011-.002-.002a1 1 0 00-1.436.17l-.02.027a4.01 4.01 0 01-.262.33c-.758.874-1.808 1.47-3.2 1.47V2.553z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-[9px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                {{ auth()->user()->streak }} Streak
                            </span>
                        </div>
                        
                        <div class="w-px h-3 bg-gray-200 dark:bg-gray-700"></div>
                        
                        <!-- Points Badge -->
                        <div class="flex items-center gap-1.5" title="Poin Terkumpul Bulan Ini">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-[9px] font-black text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                {{ auth()->user()->points + auth()->user()->pending_points }} Pts
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Ganti Password -->
                <button @click="open = false; $dispatch('open-change-password-modal')" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black uppercase tracking-wider italic text-gray-500 dark:text-gray-300 hover:text-amber-500 dark:hover:text-primary-yellow hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all text-left">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 1.5 1.5M15.5 7.5 14 6m3 3 1.5-1.5M17 9l1.5 1.5"/></svg>
                    Ganti Password
                </button>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black uppercase tracking-wider italic text-gray-500 dark:text-gray-300 hover:text-primary-red hover:bg-red-50 dark:hover:bg-red-950/20 transition-all text-left border-t border-gray-50 dark:border-gray-700/50">
                        <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
