<!-- Toast Notification Container -->
<div 
    x-data="{ 
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
            text: $event.detail.message
        });
        setTimeout(() => remove(id), 5000);
    "
    class="fixed top-10 right-10 z-[200] flex flex-col gap-4 pointer-events-none"
>
    <template x-for="msg in messages" :key="msg.id">
        <div 
            x-show="true"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-20 scale-90"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-10 scale-95"
            class="pointer-events-auto bg-white dark:bg-gray-800 px-8 py-5 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-gray-700 flex items-center gap-4 min-w-[300px]"
            :class="msg.type === 'error' ? 'border-l-4 border-l-primary-red' : 'border-l-4 border-l-primary-blue'"
        >
            <div 
                class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-lg"
                :class="msg.type === 'error' ? 'bg-primary-red' : 'bg-primary-blue'"
            >
                <template x-if="msg.type === 'error'">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </template>
                <template x-if="msg.type !== 'error'">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </template>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="msg.type === 'error' ? 'Peringatan' : 'Berhasil'"></p>
                <p class="text-xs font-black uppercase tracking-tight text-gray-800 dark:text-white italic" x-text="msg.text"></p>
            </div>
            <button @click="remove(msg.id)" class="ml-auto p-2 text-gray-300 hover:text-gray-500 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </template>
</div>
