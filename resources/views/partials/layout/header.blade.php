<!-- Global Admin Top Navbar -->
<header class="sticky top-0 z-40 w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 px-8 py-4 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-4 flex-1">
        <!-- Sidebar Toggle (Desktop) -->
        <button @click="toggleSidebar" class="hidden md:flex p-3 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue transition-all">
            <svg x-show="sidebarOpen" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            <svg x-show="!sidebarOpen" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>

        <!-- Global Search Trigger -->
        <button @click="console.log('Dispatching open-global-search'); $dispatch('open-global-search')" class="hidden md:flex items-center gap-3 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:border-primary-blue/30 transition-all group flex-1 max-w-md">
            <svg class="w-4 h-4 group-hover:text-primary-blue" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <span class="text-sm font-medium">Cari apa saja...</span>
            <div class="ml-auto flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 text-[10px] font-black bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm">Ctrl</kbd>
                <kbd class="px-1.5 py-0.5 text-[10px] font-black bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm">K</kbd>
            </div>
        </button>

        <!-- Page Context (Mobile Branding) -->
        <div class="md:hidden flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-blue rounded-xl flex items-center justify-center text-white font-black italic shadow-lg">LA</div>
            <span class="font-black italic uppercase text-sm text-primary-blue dark:text-white">Admin</span>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <!-- Note Notifications Dropdown Bell -->
        @livewire('note-notifications')

        <!-- Theme Toggle -->
        <button @click="toggleTheme()" class="p-3.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all shadow-sm">
            <svg x-show="!darkMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            <svg x-show="darkMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
        </button>

        <div class="h-6 w-px bg-gray-100 dark:bg-gray-800 mx-1"></div>

        <!-- User Profile & Logout -->
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex flex-col items-end mr-2">
                <span class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tighter">{{ auth()->user()->name ?? 'Admin' }}</span>
                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Administrator</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-3.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-red hover:bg-primary-red/5 transition-all shadow-sm group">
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</header>
