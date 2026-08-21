<div class="space-y-8 pt-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">Log Audit Keamanan</h1>
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest mt-1">Pelacakan Aktivitas Perekaman Layar, Print, & Screenshot</p>
        </div>
        
        @if(session('active_role_name') === 'superadmin')
            <div x-data="{ showClearConfirm: false }">
                <button type="button" @click="showClearConfirm = true" class="inline-flex items-center px-5 py-3.5 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-900/30 text-primary-red rounded-2xl font-black text-sm uppercase italic tracking-wider transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Kosongkan Log
                </button>

                <!-- Clear Logs Confirmation Modal -->
                <div x-show="showClearConfirm" x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @keydown.window.escape="showClearConfirm = false"
                    class="fixed inset-0 z-[500] flex items-center justify-center p-4 bg-gray-950/80 backdrop-blur-md text-left"
                    style="display: none;">
                    <div @click.outside="showClearConfirm = false"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="bg-white dark:bg-gray-900 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
                        <!-- Header -->
                        <div class="p-8 bg-gradient-to-br from-red-600 to-red-700 text-white relative overflow-hidden">
                            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 flex-shrink-0">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </div>
                                <div>
                                    <span class="px-2.5 py-0.5 bg-white/25 text-[10px] font-black uppercase tracking-widest rounded-full border border-white/10">Tindakan Permanen</span>
                                    <h3 class="text-2xl font-black italic tracking-tight mt-1">Kosongkan Semua Log?</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-8">
                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                Apakah Anda yakin ingin menghapus <strong class="text-primary-red">semua log audit keamanan</strong>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-3 justify-end">
                            <button type="button" @click="showClearConfirm = false" class="px-6 py-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-black rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-all text-center uppercase tracking-widest">
                                Batal
                            </button>
                            <button type="button" wire:click="clearLogs" @click="showClearConfirm = false" class="px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-black rounded-2xl hover:bg-red-700 shadow-lg shadow-red-600/25 transition-all text-center uppercase tracking-widest">
                                Ya, Kosongkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Security Note -->
    <div class="p-6 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-3xl flex items-start gap-4">
        <div class="p-3 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 flex-shrink-0">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-black uppercase tracking-wider italic leading-tight">Pengingat Audit Keamanan</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed font-semibold">
                Sistem secara otomatis mendeteksi ketika pengguna melakukan screenshot, menggunakan kombinasi pintasan tangkapan layar, memicu dialog pencetakan data, atau mencoba membuka DevTools browser. Tindakan ini memicu peringatan keamanan langsung di layar pengguna dan mencatat log kejadiannya.
            </p>
        </div>
    </div>

    <!-- Logs List -->
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pl-4 w-48">Waktu Kejadian</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 w-56">Pengguna & Akun</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400">Pemicu Keamanan</th>
                        <th class="pb-4 text-xs font-black uppercase tracking-widest text-gray-400 pr-4">Halaman URL Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($logs as $log)
                        @php
                            // Parse message details
                            // Format: User 'Najmy Admin' (admin@gmail.com) triggered a possible PrintScreen Key Press action on page: http://...
                            $userName = 'Unknown User';
                            $userEmail = '';
                            $triggerType = 'Screenshot/Print';
                            $targetUrl = '';
                            
                            if (preg_match("/User '(.*?)' \((.*?)\) triggered a possible (.*?) action on page: (.*)/i", $log['message'], $matches)) {
                                $userName = $matches[1];
                                $userEmail = $matches[2];
                                $triggerType = $matches[3];
                                $targetUrl = $matches[4];
                            } else {
                                $triggerType = $log['message'];
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="py-4 pl-4 text-xs font-mono text-gray-500 dark:text-gray-400">
                                {{ $log['timestamp'] }}
                            </td>
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $userName }}</span>
                                    <span class="text-[10px] text-gray-400 font-semibold">{{ $userEmail }}</span>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="inline-flex items-center px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-wider bg-red-50 text-primary-red dark:bg-red-950/30 dark:text-red-400 border border-red-100 dark:border-red-900/30">
                                    {{ $triggerType }}
                                </span>
                            </td>
                            <td class="py-4 pr-4 max-w-xs truncate text-xs font-medium text-gray-500 dark:text-gray-400" title="{{ $targetUrl }}">
                                {{ $targetUrl ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 font-semibold italic">
                                Belum ada aktivitas ancaman keamanan terdeteksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </div>
</div>
