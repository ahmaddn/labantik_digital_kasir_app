<div class="w-full max-w-5xl my-8 px-4">
    <!-- Logos and header -->
    <div class="text-center mb-10">
        <div class="flex justify-center items-center gap-4 mb-4">
            <img src="{{ asset('rpl.png') }}" alt="Logo TEFA" class="w-16 h-16 drop-shadow-xl">
            <div class="w-px h-8 bg-gray-300 dark:bg-gray-700"></div>
            <img src="{{ asset('labantik.png') }}" alt="Logo Sekolah" class="w-16 h-16 drop-shadow-xl saturate-50 brightness-110">
        </div>
        <h1 class="text-2xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Superapps TEFA</h1>
        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mt-1">RPL x Labantik</p>
    </div>

    <!-- Main Container -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 p-5 md:p-10 border border-gray-100 dark:border-gray-700">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-black text-gray-850 dark:text-white mb-2 uppercase italic tracking-tight">Pilih Hak Akses</h2>
            <p class="text-gray-400 text-sm font-medium">Silakan pilih salah satu hak akses di bawah ini untuk melanjutkan.</p>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($accesses as $access)
                @php
                    $access = (object) $access;
                    
                    // Style attributes based on role
                    if ($access->role_name === 'superadmin') {
                        $hoverBorder = 'hover:border-primary-blue hover:shadow-xl hover:shadow-blue-500/5';
                        $iconBg = 'bg-primary-blue/10 text-primary-blue';
                        $badgeColor = 'bg-primary-blue/10 text-primary-blue dark:bg-primary-blue/20 dark:text-blue-300';
                    } elseif ($access->role_name === 'pengelola_jurusan') {
                        $hoverBorder = 'hover:border-primary-red hover:shadow-xl hover:shadow-primary-red/5';
                        $iconBg = 'bg-primary-red/10 text-primary-red';
                        $badgeColor = 'bg-primary-red/10 text-primary-red dark:bg-primary-red/20 dark:text-red-300';
                    } else { // kasir
                        $hoverBorder = 'hover:border-primary-blue hover:shadow-xl hover:shadow-blue-500/5';
                        $iconBg = 'bg-blue-900/10 text-primary-blue-dark dark:text-blue-400';
                        $badgeColor = 'bg-blue-900/10 text-primary-blue-dark dark:bg-blue-950/40 dark:text-blue-300';
                    }
                @endphp

                <button type="button" wire:click="selectAccess('{{ $access->access_id }}')" 
                    class="flex flex-col p-6 bg-white dark:bg-gray-900/30 rounded-2xl border border-gray-200/80 dark:border-gray-700/60 {{ $hoverBorder }} transition-all duration-300 text-left group hover:-translate-y-1 shadow-xs justify-between min-h-[160px]">
                    
                    <div class="flex items-start gap-4 w-full">
                        <!-- Icon -->
                        <div class="w-12 h-12 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                            @if($access->role_name === 'superadmin')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            @elseif($access->role_name === 'pengelola_jurusan')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <h3 class="text-base font-bold text-gray-800 dark:text-white group-hover:text-primary-blue dark:group-hover:text-primary-yellow transition-colors leading-tight truncate">
                                {{ $access->role_label }}
                            </h3>
                            <div>
                                @if($access->jurusan_name)
                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-black rounded uppercase tracking-wider {{ $badgeColor }}">
                                        TEFA {{ $access->jurusan_name }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-black bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded uppercase tracking-wider">
                                        GLOBAL
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action/Description -->
                    <div class="mt-5 pt-3 border-t border-gray-100/50 dark:border-gray-800/50 w-full flex items-center justify-between text-xs text-gray-400 font-semibold group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">
                        <span>{{ $access->jurusan_name ? 'Masuk ke TEFA ' . $access->jurusan_name : 'Masuk Akses Global' }}</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform text-gray-300 group-hover:text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs">
            <span class="text-gray-400 font-bold uppercase tracking-widest">
                Pengguna: <span class="text-gray-700 dark:text-gray-300 font-black">{{ auth()->user()->name }}</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="font-black text-primary-red hover:text-primary-red-dark hover:underline uppercase tracking-wider">
                    Keluar / Ganti Akun
                </button>
            </form>
        </div>
    </div>

    <p class="text-center mt-8 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
        Developed for LabAntik Jurusan &copy; 2026
    </p>
</div>
