<div>
    @if($showSwitcher && count($availableUnits) > 1)
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-black uppercase tracking-wider hover:text-primary-blue hover:border-primary-blue/30 active:scale-95 transition-all shadow-sm">
                <svg class="w-4 h-4 text-primary-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.656 48.656 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3M4.5 12c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l-3 3m3-3 3 3"/></svg>
                Pindah TEFA: {{ session('active_jurusan_name') }}
                <svg class="w-3.5 h-3.5 ml-1 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-2xl z-[400] p-2 space-y-1"
                x-cloak
            >
                <div class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400">Pilih Unit TEFA Aktif</div>
                @foreach($availableUnits as $unit)
                    <button 
                        wire:click="switchUnit('{{ $unit['id'] }}')" 
                        @click="open = false"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs font-bold transition-all flex items-center justify-between {{ session('active_jurusan_id') === $unit['id'] ? 'bg-primary-blue/10 text-primary-blue dark:bg-primary-blue/20 dark:text-primary-yellow' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900' }}"
                    >
                        <span>TEFA {{ $unit['name'] }}</span>
                        @if(session('active_jurusan_id') === $unit['id'])
                            <svg class="w-4 h-4 text-primary-blue dark:text-primary-yellow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
