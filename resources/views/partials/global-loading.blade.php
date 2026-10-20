<!-- Global Heavy Action Modal Overlay (for saves, updates, deletions, checkouts) -->
<div wire:loading.delay.shorter wire:target="selectAccess, checkout, saveQuickExpense, saveOpeningStock, saveClosingStockAndNext, submitClosingReport, save, update, store, delete, exportExcel, importExcel, importFile, submitTaskCompletion, finishSession, emergencyReactivateSession, editOpeningStock" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm transition-all duration-200">
    <div class="p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl flex flex-col items-center gap-4 max-w-xs text-center animate-in fade-in zoom-in-95 duration-150">
        <div class="relative w-12 h-12">
            <div class="absolute inset-0 rounded-full border-4 border-blue-600/20"></div>
            <div class="absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent animate-spin"></div>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Memproses Data</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mohon tunggu sebentar, sistem sedang memproses permintaan Anda...</p>
        </div>
    </div>
</div>

<!-- Floating Action Pill (Top Center) for any in-flight Livewire request -->
<div id="global-livewire-pill" style="display: none;" class="fixed top-3 left-1/2 -translate-x-1/2 z-[9998] pointer-events-none transition-all duration-200">
    <div class="flex items-center gap-2.5 px-4 py-2 bg-slate-900/95 dark:bg-slate-800/95 text-white text-xs font-semibold rounded-full shadow-lg border border-slate-700/50 backdrop-blur-md">
        <svg class="w-3.5 h-3.5 animate-spin text-blue-400" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Memproses...</span>
    </div>
</div>

<style>
    /* Top Progress Bar */
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
        transition: width 0.2s ease, opacity 0.3s ease;
    }

    /* Button Processing Feedback */
    .btn-submitting {
        position: relative !important;
        pointer-events: none !important;
        opacity: 0.75 !important;
        cursor: not-allowed !important;
    }
    .btn-submitting::after {
        content: '';
        display: inline-block;
        width: 0.9em;
        height: 0.9em;
        margin-left: 0.4rem;
        vertical-align: -0.1em;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin-loader 0.6s linear infinite;
    }
    @keyframes spin-loader {
        to { transform: rotate(360deg); }
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
            bar.style.width = '15%';

            clearInterval(progressInterval);
            let width = 15;
            progressInterval = setInterval(() => {
                if (width < 85) {
                    width += Math.random() * 10;
                    bar.style.width = width + '%';
                }
            }, 150);
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
            }, 100);
        }

        function showPill() {
            clearTimeout(pillTimeout);
            pillTimeout = setTimeout(() => {
                const pill = document.getElementById('global-livewire-pill');
                if (pill) pill.style.display = 'block';
            }, 120);
        }

        function hidePill() {
            clearTimeout(pillTimeout);
            const pill = document.getElementById('global-livewire-pill');
            if (pill) pill.style.display = 'none';
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
