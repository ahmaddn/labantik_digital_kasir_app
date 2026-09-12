<div class="w-full max-w-md mx-auto my-auto">
    <div class="bg-white/90 dark:bg-gray-850/90 backdrop-blur-xl border border-gray-200 dark:border-gray-700/60 rounded-3xl p-8 shadow-2xl space-y-6 transition-all duration-300">
        <!-- Brand / Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl shadow-lg shadow-blue-500/30 text-white mb-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight italic">
                Portal Calon Labantik
            </h2>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                Cek Hasil & Status Seleksi Pendaftaran
            </p>
        </div>

        @if ($errorMessage)
            <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-600 dark:text-rose-400 text-xs font-bold flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="loginCandidate" class="space-y-4">
            <div>
                <label class="block text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                    Nama Depan
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <input wire:model="firstName" type="text" required placeholder="Contoh: Ahmad"
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl font-bold text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                    4 Digit Terakhir No. HP
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input wire:model="lastFourPhone" type="text" maxlength="6" required placeholder="Contoh: 5678"
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl font-bold text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-sm uppercase tracking-widest italic rounded-2xl shadow-xl shadow-blue-500/25 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 mt-2">
                <span>Masuk & Cek Hasil</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </form>

        <div class="pt-4 border-t border-gray-150 dark:border-gray-700/50 text-center">
            <p class="text-xs text-gray-400 font-semibold">
                Belum mendaftar? 
                <a href="{{ route('labantik.form-registration') }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                    Isi Formulir Pendaftaran
                </a>
            </p>
        </div>
    </div>
</div>
