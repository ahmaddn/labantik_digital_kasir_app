<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/40 backdrop-blur-sm" x-transition>
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 p-8 space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-700/50 pb-4">
                    <div>
                        <h3 class="text-xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow flex items-center gap-2">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 1.5 1.5M15.5 7.5 14 6m3 3 1.5-1.5M17 9l1.5 1.5"/></svg>
                            Ganti Password
                        </h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Perbarui Kredensial Keamanan Akun</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-primary-red hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="savePassword" class="space-y-4">
                    <!-- Current Password -->
                    <div class="space-y-1">
                        <label class="block text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Password Saat Ini</label>
                        <input wire:model="currentPassword" type="password" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm" placeholder="••••••••">
                        @error('currentPassword') <span class="text-xs text-primary-red font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- New Password -->
                    <div class="space-y-1">
                        <label class="block text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Password Baru</label>
                        <input wire:model="newPassword" type="password" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm" placeholder="••••••••">
                        @error('newPassword') <span class="text-xs text-primary-red font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- New Password Confirmation -->
                    <div class="space-y-1">
                        <label class="block text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Konfirmasi Password Baru</label>
                        <input wire:model="newPasswordConfirmation" type="password" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all text-sm" placeholder="••••••••">
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3.5 bg-primary-blue text-white rounded-xl font-black text-xs uppercase italic tracking-wider transition-all shadow-lg shadow-blue-900/10 hover:bg-blue-800">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
