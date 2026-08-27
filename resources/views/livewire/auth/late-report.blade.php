<div class="w-full max-w-xl my-8 px-4 mx-auto flex flex-col justify-center min-h-screen">
    <!-- Logos and header -->
    <div class="text-center mb-8">
        <div class="flex justify-center items-center gap-4 mb-4">
            <img src="{{ asset('rpl.png') }}" alt="Logo TEFA" class="w-16 h-16 drop-shadow-xl">
            <div class="w-px h-8 bg-gray-300 dark:bg-gray-700"></div>
            <img src="{{ asset('labantik.png') }}" alt="Logo Sekolah" class="w-16 h-16 drop-shadow-xl saturate-50 brightness-110">
        </div>
        <h1 class="text-2xl font-bold uppercase tracking-tight text-primary-blue dark:text-primary-yellow">Superapps TEFA</h1>
        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mt-1">RPL x Labantik</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-blue-900/5 p-5 border border-gray-100 dark:border-gray-700">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-gray-850 dark:text-white mb-2 uppercase italic tracking-tight text-primary-red">Laporan Shift & Clock Out</h2>
            <p class="text-gray-400 text-sm font-medium">Sesi kasir hari ini telah diselesaikan oleh Pengelola. Harap isi laporan laci akhir untuk menyelesaikan shift Anda.</p>
        </div>

        <form wire:submit.prevent="submitReport" class="space-y-6">
            <!-- Closing Cash Input -->
            <div class="space-y-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Total Uang Laci Akhir (Rp)</label>
                <input type="number" wire:model="closingCashInput" placeholder="Contoh: 1500000" 
                    class="w-full p-4 text-sm font-bold bg-gray-50 dark:bg-gray-900 border-2 border-gray-250 dark:border-gray-700 rounded-2xl focus:outline-none focus:border-primary-blue text-black dark:text-white transition-all">
                @error('closingCashInput') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Closing Report Input -->
            <div class="space-y-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Laporan Aktivitas Selama Shift</label>
                <textarea wire:model="closingReportText" placeholder="Jelaskan apa saja yang Anda lakukan selama shift ini..." rows="4" 
                    class="w-full p-4 text-sm font-bold bg-gray-50 dark:bg-gray-900 border-2 border-gray-250 dark:border-gray-700 rounded-2xl focus:outline-none focus:border-primary-blue text-black dark:text-white transition-all"></textarea>
                @error('closingReportText') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full py-4 bg-primary-blue hover:bg-primary-blue-dark text-white rounded-2xl text-sm font-black uppercase tracking-wider shadow-lg shadow-blue-500/20 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                Kirim Laporan & Clock Out
            </button>
        </form>
    </div>

    <p class="text-center mt-8 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
        Developed for LabAntik Jurusan &copy; 2026
    </p>
</div>
