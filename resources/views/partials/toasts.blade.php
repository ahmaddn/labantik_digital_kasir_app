<!-- Toast Notification Container -->
<div x-data="{
    messages: [],
    remove(id) {
        this.messages = this.messages.filter(m => m.id !== id)
    }
}"
    @toast.window="
        const id = Date.now();
        messages.push({
            id,
            type: $event.detail.type || 'success',
            text: $event.detail.message,
            show: false
        });
        $nextTick(() => {
            const msg = messages.find(m => m.id === id);
            if (msg) msg.show = true;
        });
        setTimeout(() => {
            const msg = messages.find(m => m.id === id);
            if (msg) msg.show = false;
            setTimeout(() => remove(id), 500);
        }, 5000);
    "
    @new-task.window = "
        const id = Date.now();
        messages.push({
            id,
            type: 'success',
            text: $event.detail.message,
            cta: $event.detail.cta_url || null,
            show: false
        });
        $nextTick(() => {
            const msg = messages.find(m => m.id === id);
            if (msg) msg.show = true;
        });
        setTimeout(() => {
            const msg = messages.find(m => m.id === id);
            if (msg) msg.show = false;
            setTimeout(() => remove(id), 500);
        }, 5000);
    "
    x-init="@if(session()->has('toast'))
    $dispatch('toast', { message: '{{ session('toast') }}', type: 'success' });
    @endif
    @if(session()->has('error'))
    $dispatch('toast', { message: '{{ session('error') }}', type: 'error' });
    @endif"
    class="fixed top-6 right-6 sm:top-10 sm:right-10 z-[1000] flex flex-col gap-4 pointer-events-none">
    <template x-for="msg in messages" :key="msg.id">
        <div x-show="msg.show" x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
            x-transition:enter-start="opacity-0 translate-y-10 scale-50"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-20 scale-90"
            class="pointer-events-auto bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl px-6 py-5 sm:px-8 sm:py-6 rounded-[2.5rem] shadow-[0_30px_70px_rgba(0,0,0,0.15)] border-2 flex items-center gap-6 w-full sm:w-auto sm:min-w-[380px] sm:max-w-md relative overflow-hidden group"
            :class="{
                'border-emerald-500/20 shadow-emerald-500/10': msg.type === 'success',
                'border-amber-500/20 shadow-amber-500/10': msg.type === 'warning',
                'border-red-500/20 shadow-red-500/10': msg.type === 'error' || msg.type === 'danger'
            }">
            <!-- Background Accent -->
            <div class="absolute top-0 left-0 w-2.5 h-full opacity-40"
                :class="{
                    'bg-emerald-500': msg.type === 'success',
                    'bg-amber-500': msg.type === 'warning',
                    'bg-red-500': msg.type === 'error' || msg.type === 'danger'
                }">
            </div>

            <!-- Icon Container -->
            <div class="w-16 h-16 rounded-[1.8rem] flex items-center justify-center text-white shadow-2xl transition-transform group-hover:rotate-12 duration-500 relative overflow-hidden"
                :class="{
                    'bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-500/30': msg.type === 'success',
                    'bg-gradient-to-br from-amber-400 to-amber-600 shadow-amber-500/30': msg.type === 'warning',
                    'bg-gradient-to-br from-red-400 to-red-600 shadow-red-500/30': msg.type === 'error' || msg
                        .type === 'danger'
                }">
                <!-- Subtle Inner Glow -->
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                <template x-if="msg.type === 'success'">
                    <svg class="w-8 h-8 relative z-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </template>
                <template x-if="msg.type === 'warning'">
                    <svg class="w-8 h-8 relative z-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m12 9 4 4-4 4" />
                        <path d="M8 9h.01" />
                        <path d="M8 13h.01" />
                        <path d="M8 17h.01" />
                        <rect width="20" height="20" x="2" y="2" rx="2" />
                    </svg>
                </template>
                <template x-if="msg.type === 'error' || msg.type === 'danger'">
                    <svg class="w-8 h-8 relative z-10" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </template>
            </div>

            <!-- Text Content -->
            <div class="flex-1">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-1.5"
                    :class="{
                        'text-emerald-500': msg.type === 'success',
                        'text-amber-500': msg.type === 'warning',
                        'text-red-500': msg.type === 'error' || msg.type === 'danger'
                    }"
                    x-text="msg.type === 'success' ? 'Sukses' : (msg.type === 'warning' ? 'Perhatian' : 'Kesalahan Sistem')">
                </p>
                <p class="text-sm font-black uppercase tracking-tight text-gray-800 dark:text-white leading-tight italic"
                    x-text="msg.text"></p>
            </div>

            <!-- CTA Button -->
            <div x-show="msg.cta" class="flex-shrink-0">
                <a :href="msg.cta"
                    class="nb-btn px-3 py-2 bg-primary-blue text-white rounded-xl text-xs font-black uppercase tracking-wider"
                    target="_blank">Lihat Tugas</a>
            </div>

            <!-- Close Button -->
            <button @click="msg.show = false; setTimeout(() => remove(msg.id), 500)"
                class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

            <!-- Progress Bar Animation -->
            <div class="absolute bottom-0 left-0 h-2 bg-gray-100 dark:bg-gray-800/50 w-full">
                <div class="h-full transition-all duration-[5000ms] ease-linear"
                    :class="{
                        'bg-emerald-500': msg.type === 'success',
                        'bg-amber-500': msg.type === 'warning',
                        'bg-red-500': msg.type === 'error' || msg.type === 'danger'
                    }"
                    x-init="setTimeout(() => $el.style.width = '0%', 100)" style="width: 100%"></div>
            </div>
        </div>
    </template>
</div>
