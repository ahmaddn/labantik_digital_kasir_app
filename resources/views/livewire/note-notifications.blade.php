<div class="relative" wire:poll.5s>
    <!-- Bell Icon Trigger Button -->
    <button wire:click="toggleDropdown" class="relative p-3.5 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-xl hover:text-primary-blue hover:bg-primary-blue/5 transition-all shadow-sm">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full animate-ping"></span>
            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-gray-800"></span>
        @endif
    </button>

    <!-- Dropdown Notification Menu -->
    @if($isOpen)
        <div class="absolute right-0 mt-3 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-white">Notifikasi Balasan</h4>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-primary-blue text-white text-[9px] font-black">{{ $unreadCount }} Baru</span>
                    @endif
                </div>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-xs">
                    &times;
                </button>
            </div>

            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($notifications as $notif)
                    <a href="{{ route('cashier-notes') }}" wire:navigate wire:click="$set('isOpen', false)" class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors block">
                        <div class="w-8 h-8 rounded-full bg-primary-blue/10 dark:bg-blue-400/20 text-primary-blue dark:text-blue-300 flex items-center justify-center font-black text-xs shrink-0">
                            {{ substr($notif->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                <span class="font-black text-primary-blue dark:text-blue-400">{{ $notif->user->name ?? 'Seseorang' }}</span> membalas catatan
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5 italic">
                                "{{ $notif->content }}"
                            </p>
                            <span class="text-[9px] font-bold text-gray-400 block mt-1">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                        Tidak ada balasan catatan baru.
                    </div>
                @endforelse
            </div>

            <div class="p-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 text-center">
                <a href="{{ route('cashier-notes') }}" wire:navigate wire:click="$set('isOpen', false)" class="text-[10px] font-black uppercase tracking-widest text-primary-blue dark:text-blue-400 hover:underline">
                    Buka Semua Catatan &rarr;
                </a>
            </div>
        </div>
    @endif
</div>
