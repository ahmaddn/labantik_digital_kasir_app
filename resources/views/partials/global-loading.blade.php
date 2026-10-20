<!-- Global Heavy Action Modal Overlay (for saves, updates, deletions, checkouts) -->
<div wire:loading.delay.shorter wire:target="selectAccess, checkout, saveQuickExpense, saveOpeningStock, saveClosingStockAndNext, submitClosingReport, save, update, store, delete, exportExcel, importExcel, importFile, submitTaskCompletion, fixUnfinishedSession" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm transition-all duration-200">
    <div class="p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl flex flex-col items-center gap-4 max-w-xs text-center animate-in fade-in zoom-in-95 duration-150">
        <div class="relative w-12 h-12">
            <div class="absolute inset-0 rounded-full border-4 border-blue-600/20"></div>
            <div class="absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent animate-spin"></div>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Memproses Data</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mohon tunggu sebentar, sistem sedang memproses data Anda...</p>
        </div>
    </div>
</div>

<!-- Floating Action Pill (Top Center) for any in-flight Livewire request with smooth fade/slide -->
<div id="global-livewire-pill" class="fixed top-3 left-1/2 -translate-x-1/2 z-[9998] pointer-events-none opacity-0 -translate-y-2 transition-all duration-300 ease-out">
    <div class="flex items-center gap-2.5 px-3.5 py-1.5 bg-slate-900/90 dark:bg-slate-800/90 text-white text-xs font-semibold rounded-full shadow-lg border border-slate-700/50 backdrop-blur-md">
        <svg class="w-3.5 h-3.5 animate-spin text-blue-400" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="tracking-wide text-[11px]">Memproses...</span>
    </div>
</div>

<style>
    /* Top Progress Bar with smooth glow */
    #global-top-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #60a5fa, #93c5fd);
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.7);
        z-index: 10000;
        pointer-events: none;
        transition: width 0.25s ease, opacity 0.3s ease;
    }

    /* Button Processing Feedback - smooth opacity transition without layout shift */
    .btn-submitting {
        pointer-events: none !important;
        opacity: 0.72 !important;
        transition: opacity 0.2s ease, transform 0.15s ease !important;
        cursor: wait !important;
    }
</style>

<script>
    (function() {
        let progressBar = null;
        let progressInterval = null;
        let pillTimeout = null;

        function getProgressBar() {
            if (!progressBar || !document.body.contains(progressBar)) {
                progressBar = document.createElement('div');
                progressBar.id = 'global-top-progress';
                document.body.appendChild(progressBar);
            }
            return progressBar;
        }

        function startProgress() {
            const bar = getProgressBar();
            bar.style.opacity = '1';
            bar.style.width = '20%';

            clearInterval(progressInterval);
            let width = 20;
            progressInterval = setInterval(() => {
                if (width < 85) {
                    width += Math.random() * 8;
                    bar.style.width = width + '%';
                }
            }, 120);
        }

        function finishProgress() {
            clearInterval(progressInterval);
            const bar = getProgressBar();
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.width = '0%';
                }, 300);
            }, 120);
        }

        function showPill() {
            clearTimeout(pillTimeout);
            pillTimeout = setTimeout(() => {
                const pill = document.getElementById('global-livewire-pill');
                if (pill) {
                    pill.classList.remove('opacity-0', '-translate-y-2');
                    pill.classList.add('opacity-100', 'translate-y-0');
                }
            }, 150);
        }

        function hidePill() {
            clearTimeout(pillTimeout);
            const pill = document.getElementById('global-livewire-pill');
            if (pill) {
                pill.classList.remove('opacity-100', 'translate-y-0');
                pill.classList.add('opacity-0', '-translate-y-2');
            }
        }

        function clearButtonStates() {
            document.querySelectorAll('.btn-submitting').forEach(el => {
                el.classList.remove('btn-submitting');
            });
        }

        // Click feedback on interactive elements
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button, [role="button"], a[wire\\:navigate]');
            if (!btn || btn.disabled) return;

            // Do not trigger button loading state on modal close / cancel triggers
            const btnText = (btn.textContent || '').trim().toLowerCase();
            const isCancelOrClose = btnText === 'batal' || 
                                   btnText === 'tutup' || 
                                   btnText === 'cancel' || 
                                   btnText === 'close' ||
                                   btn.hasAttribute('data-modal-close') ||
                                   btn.classList.contains('modal-close') ||
                                   btn.closest('[data-modal-close]') ||
                                   (btn.getAttribute('@click') || '').includes('show = false') ||
                                   (btn.getAttribute('@click') || '').includes('open = false');

            if (isCancelOrClose) {
                return;
            }

            const hasLivewire = btn.hasAttribute('wire:click') || 
                                (btn.hasAttribute('type') && btn.getAttribute('type') === 'submit') ||
                                btn.closest('form[wire\\:submit]') ||
                                btn.hasAttribute('wire:navigate');

            if (hasLivewire) {
                btn.classList.add('btn-submitting');
                startProgress();
            }
        });

        // Livewire 3 Hook Integration
        document.addEventListener('livewire:init', function() {
            if (window.Livewire) {
                window.Livewire.hook('commit', ({ commit, respond, succeed, fail }) => {
                    const calls = commit.calls || [];
                    const isPoll = calls.some(c => c.method === 'checkNewTasks' || c.method === '__poll');

                    if (!isPoll) {
                        startProgress();
                        showPill();
                    }

                    const cleanup = () => {
                        if (!isPoll) {
                            finishProgress();
                            hidePill();
                        }
                        clearButtonStates();
                    };

                    respond(cleanup);
                    succeed(cleanup);
                    fail(cleanup);
                });
            }
        });

        // SPA Navigation hooks (wire:navigate)
        document.addEventListener('livewire:navigating', function() {
            startProgress();
            showPill();
        });

        document.addEventListener('livewire:navigated', function() {
            finishProgress();
            hidePill();
            clearButtonStates();
        });

        // Browser full navigation
        window.addEventListener('beforeunload', function() {
            startProgress();
        });

        window.addEventListener('pageshow', function() {
            finishProgress();
            hidePill();
            clearButtonStates();
        });
    })();
</script>
