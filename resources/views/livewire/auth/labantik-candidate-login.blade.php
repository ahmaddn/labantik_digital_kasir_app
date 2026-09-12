<div class="w-full max-w-md mx-auto my-auto px-2 sm:px-4">
    <!-- Full-screen dark background override -->
    <div class="fixed inset-0 bg-gray-950 -z-10"></div>

    <div class="bg-gray-900 rounded-2xl sm:rounded-3xl border border-gray-800 p-6 sm:p-8 shadow-2xl space-y-6">
        <!-- Header -->
        <div class="text-center space-y-3">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-800 rounded-full p-3 flex items-center justify-center border border-gray-700 mx-auto shadow-md">
                <img src="{{ asset('labantik.png') }}" alt="Logo Labantik" class="w-10 h-10 sm:w-14 sm:h-14 object-contain">
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-primary-yellow">
                    PORTAL CALON LABANTIK
                </h2>
                <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-widest mt-1">
                    Cek Hasil & Status Seleksi Pendaftaran
                </p>
            </div>
        </div>

        @if ($errorMessage)
            <div class="p-3.5 sm:p-4 bg-red-950/40 border border-red-800 rounded-xl sm:rounded-2xl text-red-400 text-xs font-bold flex items-start gap-2.5">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="leading-relaxed">{{ $errorMessage }}</span>
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="loginCandidate" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-1.5">
                    Nama Depan <span class="text-primary-yellow text-[10px] lowercase">(huruf kapital)</span>
                </label>
                <input wire:model="firstName" type="text" required placeholder="Contoh: AHMAD"
                    class="w-full px-3.5 sm:px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl font-bold text-sm text-white uppercase focus:outline-none focus:ring-2 focus:ring-primary-blue transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-1.5">
                    4 Digit Terakhir No. HP
                </label>
                <input wire:model="lastFourPhone" type="text" maxlength="6" required placeholder="Contoh: 5678"
                    class="w-full px-3.5 sm:px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl font-bold text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-blue transition-all">
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl shadow-lg transition-all active:scale-95 mt-2">
                Masuk & Cek Hasil
            </button>
        </form>

        <div class="pt-4 border-t border-gray-800 text-center">
            <p class="text-xs text-gray-400 font-semibold">
                Belum mendaftar? 
                <a href="{{ route('labantik.form-registration') }}" class="text-primary-yellow font-bold hover:underline">
                    Isi Formulir Pendaftaran
                </a>
            </p>
        </div>
    </div>
</div>
