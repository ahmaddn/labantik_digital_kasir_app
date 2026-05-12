<div wire:loading.delay.longest class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/40 backdrop-blur-[2px] transition-all duration-300">
    <div class="flex flex-col items-center gap-6 p-10 bg-white dark:bg-gray-900 rounded-[3rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.3)] border-t-4 border-primary-blue animate-in zoom-in duration-300">
        <div class="relative w-20 h-20">
            <div class="absolute inset-0 border-4 border-gray-100 dark:border-gray-800 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-primary-blue border-t-transparent rounded-full animate-spin"></div>
        </div>
        <div class="text-center">
            <h3 class="text-xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Memproses...</h3>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-2">Mohon Tunggu Sebentar</p>
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
