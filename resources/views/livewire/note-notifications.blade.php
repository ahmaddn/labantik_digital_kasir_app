<div class="relative" wire:poll.5s id="global-notifications-container" x-data="{ open: false }" @click.outside="open = false" @keydown.window.escape="open = false">
    <!-- Bell Icon Trigger Button (Alpine: buka instan tanpa round-trip server) -->
    <button @click="open = !open; if (open) $wire.markAllAsRead()" class="relative p-3.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all shadow-sm">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span id="unread-count-badge" data-count="{{ $unreadCount }}" class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full animate-ping"></span>
            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-gray-800"></span>
        @endif
    </button>

    <!-- Dropdown Notification Menu -->
    <div x-show="open" x-cloak class="fixed sm:absolute top-20 sm:top-auto left-4 sm:left-0 right-4 sm:right-auto mt-3 w-auto sm:w-96 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-white">Notifikasi Aktivitas</h4>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-primary-blue text-white text-[9px] font-black">{{ $unreadCount }} Baru</span>
                    @endif
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-xs font-black">
                    &times;
                </button>
            </div>

            <!-- WhatsApp Mobile Link Promo Banner -->
            {{-- <div class="p-3 bg-gradient-to-r from-green-500/10 to-emerald-500/10 dark:from-green-950/20 dark:to-emerald-950/20 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.717-1.454L0 24zm6.59-4.846c1.6.95 3.197 1.451 4.887 1.452 5.4 0 9.794-4.394 9.797-9.798.002-2.618-1.017-5.08-2.868-6.931-1.85-1.85-4.311-2.867-6.924-2.869-5.405 0-9.8 4.394-9.803 9.799 0 1.77.464 3.498 1.348 5.048l-.995 3.637 3.733-.979a9.715 9.715 0 0 0 4.625 1.22z"/></svg>
                    <span class="text-[9px] font-black uppercase text-gray-500 dark:text-gray-400 truncate">Notifikasi ke HP (WhatsApp)</span>
                </div>
                <a href="https://api.whatsapp.com/send?phone=6282218080000&text=DAFTAR%20NOTIFIKASI%20TEFA%20{{ urlencode(auth()->user()->email) }}" target="_blank" class="px-2.5 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-[8px] font-black uppercase tracking-wider transition-all shrink-0">
                    Aktifkan
                </a>
            </div> --}}

            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($notifications as $notif)
                    @php
                        $iconBg = match($notif->type) {
                            'task' => 'bg-amber-50 text-amber-500 dark:bg-amber-950/20 dark:text-amber-400',
                            'note' => 'bg-blue-50 text-primary-blue dark:bg-blue-950/20 dark:text-blue-400',
                            default => 'bg-gray-50 text-gray-500 dark:bg-gray-900 dark:text-gray-400'
                        };
                        $iconSvg = match($notif->type) {
                            'task' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
                            'note' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
                            default => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        };
                    @endphp
                    <a href="{{ $notif->action_url ? url($notif->action_url) : '#' }}" wire:navigate @click="open = false" class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors block">
                        <div class="w-8 h-8 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0">
                            {!! $iconSvg !!}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-gray-800 dark:text-gray-200">
                                {{ $notif->title }}
                            </p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                {{ $notif->body }}
                            </p>
                            <span class="text-[8px] font-bold text-gray-450 dark:text-gray-500 block mt-1 uppercase tracking-wider">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs font-bold text-gray-450 uppercase tracking-widest italic">
                        Tidak ada notifikasi aktivitas.
                    </div>
                @endforelse
            </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            const badgeCount = () => {
                const badge = document.querySelector('#unread-count-badge');
                return badge ? parseInt(badge.getAttribute('data-count') || '0') : 0;
            };

            let lastCount = badgeCount();
            const nativeSupported = 'Notification' in window;
            let swRegistration = null;

            // Register the service worker so notifications can reach the phone's
            // notification tray (Android Chrome requires registration.showNotification())
            if (nativeSupported && 'serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => { swRegistration = reg; })
                    .catch(() => { /* SW unavailable, fall back to other methods */ });
            }

            // Check native notification permission (only where the API exists,
            // iOS Safari does not expose window.Notification at all)
            if (nativeSupported && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            // --- Sound & vibration (browsers only allow this after a user gesture,
            //     so the AudioContext is unlocked on the first tap/click) ---
            let audioCtx = null;
            const unlockAudio = () => {
                try {
                    if (!audioCtx) {
                        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    if (audioCtx.state === 'suspended') {
                        audioCtx.resume();
                    }
                } catch (e) { /* Web Audio not available */ }
            };
            document.addEventListener('click', unlockAudio, { once: true });
            document.addEventListener('touchstart', unlockAudio, { once: true });

            const playAlertSound = () => {
                if (!audioCtx || audioCtx.state !== 'running') return;
                try {
                    // Two short "ding" tones as the notification sound
                    [0, 0.18].forEach((delay, i) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = i === 0 ? 880 : 1174;
                        gain.gain.setValueAtTime(0.0001, audioCtx.currentTime + delay);
                        gain.gain.exponentialRampToValueAtTime(0.35, audioCtx.currentTime + delay + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + delay + 0.3);
                        osc.connect(gain).connect(audioCtx.destination);
                        osc.start(audioCtx.currentTime + delay);
                        osc.stop(audioCtx.currentTime + delay + 0.35);
                    });
                } catch (e) { /* ignore audio errors */ }
            };

            const triggerVibration = () => {
                // Modern Android Chrome ignores the notification `vibrate` option,
                // so vibrate straight from the page as well
                if ('vibrate' in navigator) {
                    try { navigator.vibrate([200, 100, 200]); } catch (e) { /* ignore */ }
                }
            };

            const showPopup = () => {
                // Try to extract the title and body of the latest notification from the DOM
                const firstNotif = document.querySelector('#global-notifications-container a');
                let title = 'LabAntik Kasir';
                let body = 'Anda mendapatkan tugas, catatan, atau pesan baru!';
                if (firstNotif) {
                    const titleEl = firstNotif.querySelector('p.text-xs');
                    const bodyEl = firstNotif.querySelector('p.text-\\[10px\\]');
                    if (titleEl && titleEl.textContent.trim()) {
                        title = titleEl.textContent.trim();
                    }
                    if (bodyEl && bodyEl.textContent.trim()) {
                        body = bodyEl.textContent.trim();
                    }
                }

                // Always give haptic + audible feedback while the app is open
                triggerVibration();
                playAlertSound();

                if (nativeSupported && Notification.permission === 'granted') {
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                    if (isMobile && swRegistration) {
                        // Use Service Worker directly on mobile
                        swRegistration.showNotification(title, {
                            body: body,
                            icon: '/favicon.png',
                            badge: '/favicon.png',
                            vibrate: [200, 100, 200],
                            silent: false,
                            tag: 'labantik-notif',
                            renotify: true,
                            data: { url: '/' }
                        }).catch(() => {});
                        return;
                    } else {
                        // Use standard Notification constructor on desktop
                        try {
                            new Notification(title, {
                                body: body,
                                icon: '/favicon.png',
                                silent: false
                            });
                            return;
                        } catch (e) {
                            // Fallback to Service Worker if standard constructor fails on desktop
                            if (swRegistration) {
                                swRegistration.showNotification(title, {
                                    body: body,
                                    icon: '/favicon.png',
                                    badge: '/favicon.png',
                                    vibrate: [200, 100, 200],
                                    silent: false,
                                    tag: 'labantik-notif',
                                    renotify: true,
                                    data: { url: '/' }
                                }).catch(() => {});
                                return;
                            }
                        }
                    }
                }

                // 3. In-app popup fallback (iOS Safari / permission denied)
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: body, type: 'warning' }
                }));
            };

            // Hook into Livewire response to check unread notification increments
            Livewire.hook('request', ({ respond }) => {
                respond(({ status }) => {
                    const currentCount = badgeCount();
                    if (currentCount > lastCount) {
                        showPopup();
                    }
                    lastCount = currentCount;
                });
            });
        });
    </script>
</div>
