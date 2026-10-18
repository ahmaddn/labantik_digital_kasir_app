<!-- Global Top Header Navbar -->
<header class="sticky top-0 z-30 w-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-6 py-2.5 flex items-center justify-between">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <!-- Sidebar Toggle (Desktop) -->
        <button @click="toggleSidebar" type="button"
            class="hidden lg:flex p-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            title="Sembunyikan / Tampilkan Sidebar">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <!-- Global Search Trigger -->
        <button @click="$dispatch('open-global-search')" type="button"
            class="hidden sm:flex items-center gap-2.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-800/70 border border-slate-200/60 dark:border-slate-700/60 rounded-xl text-slate-400 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800 hover:border-blue-500/40 transition-all flex-1 max-w-sm text-left">
            <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Cari produk, transaksi, menu...</span>
            <span class="ml-auto flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 text-[9px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded text-slate-500 shadow-xs">Ctrl+K</kbd>
            </span>
        </button>

        <!-- Mobile Brand Title (When on small screens) -->
        <div class="sm:hidden flex items-center gap-2 min-w-0">
            <span class="text-sm font-bold tracking-tight text-slate-900 dark:text-white truncate">
                {{ session('active_jurusan_name') ? 'TEFA ' . session('active_jurusan_name') : 'LabAntik TEFA' }}
            </span>
        </div>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Note Notifications -->
        @livewire('note-notifications')

        <!-- Theme Toggle (Light / Dark) -->
        <button @click="toggleTheme()" type="button"
            class="p-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            title="Ganti Mode Gelap/Terang">
            <svg x-show="!darkMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
            <svg x-show="darkMode" class="w-5 h-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
        </button>

        <!-- Sensor Mode (Blur Sensitive Financial Data) -->
        <button @click="toggleCensor()" type="button"
            class="p-2 rounded-xl transition-colors"
            :class="censorMode ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'"
            title="Mode Sensor Keuangan (Sembunyikan Saldo)">
            <svg x-show="!censorMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg x-show="censorMode" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>

        <!-- Divider -->
        <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 mx-1"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" type="button"
                class="w-9 h-9 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center shadow-xs transition-transform active:scale-95"
                title="Menu Pengguna">
                {{ auth()->check() ? auth()->user()->initials() : 'LA' }}
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 py-1.5 z-50">
                
                <div class="px-3.5 py-2 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                        {{ auth()->user()->name ?? 'Pengguna' }}
                    </p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-0.5">
                        {{ session('active_role_label', 'User') }}
                    </p>
                    @auth
                        <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                            <span class="inline-flex items-center gap-1 text-orange-600 dark:text-orange-400">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.124 1.942-1.392 2.65-.25.663-.442 1.348-.567 2.05-.286-.337-.56-.693-.81-1.07A1 1 0 005.8 7.08c-.466.86-.74 1.838-.795 2.85-.062 1.15.228 2.308.85 3.278A7.003 7.003 0 0012 17a7.001 7.001 0 006.126-10.377 1 1 0 00-.91-.564c-.466.014-.945-.04-1.398-.18-.847-.263-1.602-.756-2.183-1.408-.6-.672-.98-1.503-1.24-1.918z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ auth()->user()->streak }} Streak</span>
                            </span>
                            <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>{{ auth()->user()->points + auth()->user()->pending_points }} Poin</span>
                            </span>
                        </div>
                    @endauth
                </div>

                <!-- Ganti Hak Akses -->
                <a href="{{ route('select-role') }}"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-left">
                    <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                    <span>Ganti Hak Akses</span>
                </a>

                <!-- Ganti Password -->
                <button @click="open = false; $dispatch('open-change-password-modal')" type="button"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-left">
                    <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                    <span>Ganti Password</span>
                </button>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors text-left border-t border-slate-100 dark:border-slate-800">
                        <svg class="w-4 h-4 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
