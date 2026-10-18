<div 
    x-data="{
        darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }
        }
    }"
    class="w-full max-w-4xl my-4 sm:my-8 px-4"
>
    <!-- Top Bar: Logo & Mode Toggle -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white text-sm">
                LA
            </div>
            <div>
                <span class="block font-bold text-sm text-slate-900 dark:text-white leading-tight">LabAntik Superapps</span>
                <span class="block text-[11px] text-slate-500 dark:text-slate-400">SMKN 1 Talaga</span>
            </div>
        </div>

        <button 
            @click="toggleTheme()" 
            type="button"
            class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-medium cursor-pointer transition-colors"
            title="Ganti Mode Tampilan"
        >
            <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
        </button>
    </div>

    <!-- Main Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Pilih Hak Akses
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pilih salah satu peran dan unit kerja di bawah ini untuk memulai sesi kasir atau manajemen.
            </p>
        </div>

        <!-- Role Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($accesses as $access)
                @php
                    $access = (object) $access;
                    $isSuperadmin = $access->role_name === 'superadmin';
                    $isPengelola = $access->role_name === 'pengelola_jurusan';
                    $isKasir = $access->role_name === 'kasir';
                    
                    if ($isSuperadmin) {
                        $badgeClass = 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-900';
                        $desc = 'Konfigurasi sistem, audit data, dan pengelolaan global seluruh jurusan.';
                    } elseif ($isPengelola) {
                        $badgeClass = 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900';
                        $desc = 'Manajemen produk, stok inventaris, dan pembukuan keuangan unit jurusan.';
                    } else {
                        $badgeClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900';
                        $desc = 'Operasional kasir penjualan, cetak struk, dan pencatatan transaksi harian.';
                    }
                @endphp

                <button type="button" wire:click="selectAccess('{{ $access->access_id }}')" 
                    class="flex flex-col justify-between p-5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700/80 hover:border-blue-600 dark:hover:border-blue-500 hover:bg-white dark:hover:bg-slate-800 transition-colors text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-600">
                    
                    <div>
                        <!-- Header Card: Unit Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">
                                {{ $access->role_label }}
                            </span>
                            @if($access->jurusan_name)
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $badgeClass }}">
                                    {{ $access->jurusan_name }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                    Global
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            {{ $desc }}
                        </p>
                    </div>

                    <!-- Action Link -->
                    <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between text-xs font-semibold text-blue-600 dark:text-blue-400">
                        <span>Pilih Akses Ini</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- User Info & Logout -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs">
            <div class="text-slate-600 dark:text-slate-400">
                <span>Akun masuk:</span>
                <strong class="text-slate-900 dark:text-white ml-1">{{ auth()->user()->name }}</strong>
                <span class="text-slate-400 dark:text-slate-500 ml-1">({{ auth()->user()->email }})</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                    class="text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 cursor-pointer">
                    Keluar / Ganti Akun
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-6 text-xs text-slate-400 dark:text-slate-500">
        Teaching Factory &copy; {{ date('Y') }} SMKN 1 Talaga
    </footer>
</div>
