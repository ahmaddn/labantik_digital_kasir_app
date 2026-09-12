<div x-data="{ 
        showVolumeModal: true, 
        isPassed: {{ $candidate && $candidate->isFinalPassed() ? 'true' : 'false' }},
        confirmVolume() {
            this.showVolumeModal = false;
            if (this.isPassed) {
                this.playAudioAndConfetti();
            }
        },
        playAudioAndConfetti() {
            const audio = document.getElementById('bg-music');
            if (audio) {
                audio.play().catch(e => console.log('Audio autoplay prevented or error:', e));
            }
            if (window.confetti) {
                const duration = 5 * 1000;
                const animationEnd = Date.now() + duration;
                const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

                function randomInRange(min, max) {
                    return Math.random() * (max - min) + min;
                }

                const interval = setInterval(function() {
                    const timeLeft = animationEnd - Date.now();
                    if (timeLeft <= 0) {
                        return clearInterval(interval);
                    }
                    const particleCount = 50 * (timeLeft / duration);
                    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                }, 250);
            }
        }
    }" 
    class="w-full max-w-2xl mx-auto my-auto relative">

    <!-- Include Canvas Confetti JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    @if ($candidate && $candidate->isFinalPassed())
        <!-- Background Music (Only for passed candidates) -->
        <audio id="bg-music" src="{{ asset('audio/terimakasih-sudah-bertahan.mp3') }}" preload="auto" loop></audio>
    @endif

    <!-- VOLUME WARNING MODAL -->
    <div x-show="showVolumeModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md transition-opacity">
        <div class="bg-white dark:bg-gray-900 border border-amber-500/30 rounded-3xl p-8 max-w-md w-full shadow-2xl text-center space-y-6 animate-bounce-subtle">
            <div class="w-20 h-20 bg-amber-500/10 text-amber-500 rounded-3xl mx-auto flex items-center justify-center border border-amber-500/20 shadow-lg">
                <svg class="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.414 0-.75-.336-.75-.75V9c0-.414.336-.75.75-.75h2.24z"/>
                </svg>
            </div>

            <div class="space-y-2">
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight italic">
                    Peringatan Volume Perangkat
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-300 font-semibold leading-relaxed">
                    Silakan <span class="text-amber-500 font-black uppercase">nyalakan dan besarkan volume speaker / earphone</span> Anda sebelum masuk untuk mendengar pengumuman hasil seleksi!
                </p>
            </div>

            <button @click="confirmVolume()"
                class="w-full py-4 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-gray-950 font-black text-sm uppercase tracking-widest italic rounded-2xl shadow-xl shadow-amber-500/25 active:scale-95 transition-all duration-200">
                Saya Sudah Nyalakan Volume
            </button>
        </div>
    </div>

    <!-- MAIN RESULT CONTENT -->
    <div class="bg-white/90 dark:bg-gray-850/90 backdrop-blur-xl border border-gray-200 dark:border-gray-700/60 rounded-3xl p-8 md:p-10 shadow-2xl space-y-8">
        
        <!-- Header Info -->
        <div class="flex items-center justify-between border-b border-gray-150 dark:border-gray-700/50 pb-6">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Peserta Seleksi</span>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase">{{ $candidate->full_name }}</h2>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $candidate->class_name }}</p>
            </div>
            <button wire:click="logoutCandidate" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-xs font-bold transition-all">
                Keluar
            </button>
        </div>

        @if ($candidate->isFinalPassed())
            <!-- STATUS: LOLOS (PASSED) -->
            <div class="text-center space-y-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-500/10 text-emerald-500 rounded-full border-2 border-emerald-500/30 shadow-2xl shadow-emerald-500/20">
                    <svg class="w-12 h-12 animate-bounce" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div class="space-y-3">
                    <span class="px-4 py-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black rounded-full text-xs uppercase tracking-widest border border-emerald-500/20 italic">
                        Pengumuman Kelulusan
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black text-emerald-600 dark:text-emerald-400 uppercase italic tracking-tight">
                        SELAMAT! ANDA LOLOS SELEKSI LABANTIK
                    </h1>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 max-w-lg mx-auto leading-relaxed">
                        Selamat bergabung menjadi bagian dari keluarga besar <strong class="text-emerald-500">Labantik</strong>! Perjuangan dan ketekunan Anda membuahkan hasil terbaik. Terima kasih sudah bertahan hingga tahap akhir ini! 🎶
                    </p>
                </div>

                @if ($waGroupLink)
                    <div class="pt-4 border-t border-gray-150 dark:border-gray-700/50">
                        <a href="{{ $waGroupLink }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center justify-center gap-3 w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm uppercase tracking-wider rounded-2xl shadow-xl shadow-emerald-600/30 active:scale-95 transition-all">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                            </svg>
                            <span>GABUNG GRUP WHATSAPP LABANTIK</span>
                        </a>
                    </div>
                @endif
            </div>

        @elseif ($candidate->final_status === 'pending')
            <!-- STATUS: PENDING / PROCESS -->
            <div class="text-center space-y-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-amber-500/10 text-amber-500 rounded-full border-2 border-amber-500/30 shadow-2xl">
                    <svg class="w-12 h-12 animate-spin" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-amber-500 uppercase tracking-tight italic">
                        SELEKSI SEDANG PROSES
                    </h2>
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Anda masuk dalam 15 Besar calon anggota Labantik. Status akhir kelulusan sedang dalam tahap penetapan oleh pengelola. Harap cek kembali secara berkala!
                    </p>
                </div>
            </div>

        @else
            <!-- STATUS: TIDAK LOLOS (REJECTED) -->
            <div class="text-center space-y-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-rose-500/10 text-rose-500 rounded-full border-2 border-rose-500/30 shadow-2xl">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </div>

                <div class="space-y-3">
                    <span class="px-4 py-1.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 font-black rounded-full text-xs uppercase tracking-widest border border-rose-500/20 italic">
                        Pengumuman Seleksi
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-gray-100 uppercase italic tracking-tight">
                        TETAP SEMANGAT & TERIMA KASIH!
                    </h1>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 max-w-md mx-auto leading-relaxed">
                        Terima kasih banyak telah mendaftar dan mengikuti seluruh tahapan seleksi calon anggota Labantik. Walau kesempatan kali ini belum berpihak, perjuangan dan usaha Anda sangat luar biasa. Jangan pernah patah semangat!
                    </p>
                </div>
            </div>
        @endif

    </div>
</div>
