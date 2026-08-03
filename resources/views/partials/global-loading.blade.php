<div wire:loading.delay.longest wire:target="selectAccess, checkout, saveQuickExpense, saveOpeningStock, saveClosingStockAndNext, submitClosingReport, save, update, store, delete, exportExcel, importExcel, importFile, submitTaskCompletion" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-md transition-all duration-300">
    <div class="nb-card p-12 bg-white dark:bg-dark-soft flex flex-col items-center gap-8 animate-brutal-bounce">
        <div class="relative w-24 h-24">
            <div class="absolute inset-0 border-[6px] border-black/10 dark:border-white/10 rounded-none"></div>
            <div class="absolute inset-0 border-[6px] border-primary-blue border-t-transparent rounded-none animate-brutal-spin shadow-[4px_4px_0_0_black] dark:shadow-[4px_4px_0_0_white]"></div>
        </div>
        <div class="text-center">
            <h3 class="text-3xl font-black italic uppercase tracking-tighter text-black dark:text-white leading-none">MEMPROSES...</h3>
            <p class="text-[10px] font-black text-primary-red dark:text-primary-blue uppercase tracking-[0.5em] mt-4">JANGAN TUTUP HALAMAN</p>
        </div>
    </div>
</div>

<style>
    /* Premium Page Progress Bar */
    #nprogress { pointer-events: none; }
    #nprogress .bar {
        background: #3b82f6;
        position: fixed;
        z-index: 1031;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
    }
    #nprogress .peg {
        display: block;
        position: absolute;
        right: 0px;
        width: 100px;
        height: 100%;
        box-shadow: 0 0 10px #3b82f6, 0 0 5px #3b82f6;
        opacity: 1.0;
        transform: rotate(3deg) translate(0px, -4px);
    }
</style>

<script>
    // Handle Page Navigation Loading (Standard Redirects)
    window.addEventListener('beforeunload', function() {
        // Create progress bar on navigation
        const bar = document.createElement('div');
        bar.id = 'nprogress';
        bar.innerHTML = '<div class="bar"><div class="peg"></div></div>';
        document.body.appendChild(bar);
        
        let width = 0;
        const interval = setInterval(() => {
            width += Math.random() * 5;
            if (width > 95) {
                clearInterval(interval);
            } else {
                bar.querySelector('.bar').style.width = width + '%';
            }
        }, 100);
    });

    // Ensure loading hidden on page show (bfcache)
    window.addEventListener('pageshow', function() {
        const bar = document.getElementById('nprogress');
        if (bar) bar.remove();
    });
</script>
