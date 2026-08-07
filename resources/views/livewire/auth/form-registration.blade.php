<div class="w-full max-w-2xl mx-auto my-6 px-2 sm:px-4">
    <!-- Full-screen dark background override -->
    <div class="fixed inset-0 bg-gray-950 -z-10"></div>
    <!-- Main Card - Locked to Dark Mode styling -->
    <div
        class="bg-gray-900 rounded-[2.5rem] sm:rounded-[3.5rem] shadow-2xl border border-gray-800 overflow-hidden p-6 sm:p-10 md:p-14">

        <!-- Header Section -->
        <div class="text-center mb-10">
            <div class="inline-block mb-5">
                <div
                    class="w-24 h-24 sm:w-28 sm:h-28 bg-gray-800 rounded-full p-4 flex items-center justify-center border border-gray-700 shadow-md">
                    <img src="{{ asset('labantik.png') }}" alt="Logo Labantik"
                        class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                </div>
            </div>

            <div class="space-y-1">
                <h2 class="text-3xl sm:text-4xl font-black italic uppercase tracking-tighter text-primary-yellow">
                    LABANTIK
                </h2>
            </div>
        </div>

        @if (!$isOpen)
            <!-- Registration Closed Screen -->
            <div class="text-center py-10 space-y-6">
                <div class="inline-block">
                    <div
                        class="w-24 h-24 bg-gray-800 text-amber-500 rounded-full flex items-center justify-center mx-auto shadow-md border border-gray-700">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight italic">
                        Pendaftaran Ditutup
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-400 max-w-md mx-auto leading-relaxed font-semibold">
                        Mohon maaf, pendaftaran calon anggota baru Labantik saat ini telah ditutup. Silakan hubungi pengurus atau guru pembimbing untuk informasi lebih lanjut.
                    </p>
                </div>
            </div>
        @elseif ($isSubmitted)
            <!-- Success Screen with WA Group Redirection (Dark Mode Only) -->
            <div class="text-center py-10 space-y-8">
                <div class="inline-block">
                    <div
                        class="w-24 h-24 bg-emerald-950/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto shadow-md border border-emerald-900/30">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight italic">
                        Pendaftaran Terkirim
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-400 max-w-md mx-auto leading-relaxed font-semibold">
                        Terima kasih telah mendaftar. Data pendaftaran Anda telah tersimpan dengan aman di database
                        sistem kami.
                    </p>
                </div>

                @if ($waGroupLink)
                    <!-- WhatsApp Link Box (Dark Mode Only) -->
                    <div
                        class="p-6 sm:p-8 bg-emerald-950/20 border border-emerald-900 rounded-[2rem] max-w-md mx-auto shadow-md space-y-5">
                        <p class="text-xs sm:text-sm font-bold text-emerald-300 leading-relaxed">
                            Langkah Selanjutnya: Silakan gabung ke grup WhatsApp koordinasi calon anggota melalui tombol
                            di bawah ini:
                        </p>
                        <a href="{{ $waGroupLink }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center justify-center w-full px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black uppercase tracking-wider text-xs sm:text-sm transition-all shadow-md gap-3">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.993L2 22l5.233-1.371a9.936 9.936 0 0 0 4.777 1.224h.005c5.505 0 9.986-4.479 9.987-9.985A9.95 9.95 0 0 0 12.012 2zm5.836 14.199c-.32.9-1.845 1.747-2.529 1.83-.585.07-1.347.099-2.127-.15a13.34 13.34 0 0 1-5.632-3.486 11.23 11.23 0 0 1-2.45-3.834c-.39-.86-.02-1.33.27-1.63.22-.22.48-.56.73-.84.25-.28.33-.48.5-.8.16-.33.08-.62-.04-.9-.12-.28-.9-2.19-1.24-2.99-.32-.82-.67-.71-.92-.72-.24-.01-.52-.01-.8-.01s-.73.1-1.12.5c-.39.4-1.48 1.45-1.48 3.53 0 2.08 1.5 4.1 1.71 4.38.2.28 2.96 4.54 7.18 6.36 1 .43 1.79.69 2.4.89.92.29 1.77.25 2.43.15.74-.11 2.27-.93 2.59-1.83.32-.9.32-1.67.22-1.83-.09-.16-.36-.26-.77-.47s-2.42-1.2-2.79-1.33c-.37-.13-.64-.2-.91.2-.28.4-.1.9-.13.93-.03.03-.68.75-.83.92-.15.17-.3.19-.71-.02-.41-.21-1.74-.64-3.32-2.05-1.22-1.09-2.05-2.44-2.29-2.85-.24-.41-.02-.63.18-.83.18-.18.41-.47.61-.71.2-.24.27-.41.41-.69.14-.28.07-.53-.04-.74-.11-.21-.92-2.24-1.27-3.07-.34-.84-.71-.73-.97-.74h-.82z" />
                            </svg>
                            Masuk Grup WhatsApp
                        </a>
                    </div>
                @else
                    <p class="text-xs sm:text-sm text-gray-400 italic max-w-md mx-auto">
                        Anda akan segera dihubungi oleh pengurus melalui nomor WhatsApp yang terdaftar.
                    </p>
                @endif

                <div class="pt-4">
                    <button wire:click="$set('isSubmitted', false)"
                        class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-2xl font-black text-xs uppercase tracking-wider transition-all">
                        Kembali ke Form
                    </button>
                </div>
            </div>
        @else
            <!-- Registration Form (Dark Mode Only styling) -->
            <form wire:submit.prevent="submitRegistration" class="space-y-6">

                <!-- Nama Lengkap Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                        Nama Lengkap
                    </label>
                    <input type="text" wire:model="fullName" placeholder="Masukkan nama lengkap sesuai ijazah..."
                        class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all">
                    @error('fullName')
                        <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kelas Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                        Kelas
                    </label>
                    <select type="text" wire:model="className"
                        class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all">
                        <option value="X RPL 1">X RPL 1</option>
                        <option value="X RPL 2">X RPL 2</option>
                    </select>
                    @error('className')
                        <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone Inputs (Responsive Layout) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone Number -->
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                            No HP / WhatsApp
                        </label>
                        <input type="text" wire:model="phoneNumber" placeholder="Contoh: 081234567890..."
                            class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all">
                        @error('phoneNumber')
                            <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Parent Phone Number -->
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                            No HP Orang Tua
                        </label>
                        <input type="text" wire:model="parentPhoneNumber" placeholder="Contoh: 089876543210..."
                            class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all">
                        @error('parentPhoneNumber')
                            <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Illness History -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                        Riwayat Penyakit Bawaan (Jika Ada)
                    </label>
                    <input type="text" wire:model="illnessHistory"
                        placeholder="Contoh: Asma, Alergi (kosongkan jika tidak ada)..."
                        class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all">
                    @error('illnessHistory')
                        <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                        Alamat Rumah Lengkap
                    </label>
                    <textarea wire:model="address" rows="3" placeholder="Masukkan alamat lengkap rumah tinggal Anda sekarang..."
                        class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all leading-relaxed"></textarea>
                    @error('address')
                        <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Reason for Joining -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 ml-1">
                        Alasan Masuk Labantik
                    </label>
                    <textarea wire:model="reason" rows="4"
                        placeholder="Tuliskan alasan, minat, dan motivasi utama Anda mendaftar masuk Labantik..."
                        class="w-full px-5 py-4 bg-gray-950 border border-gray-800 rounded-2xl focus:ring-2 focus:ring-primary-yellow focus:border-primary-yellow text-white text-xs sm:text-sm font-semibold transition-all leading-relaxed"></textarea>
                    @error('reason')
                        <span class="text-xs text-red-500 font-bold mt-1 block ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-4 bg-primary-yellow text-gray-900 hover:bg-yellow-500 rounded-2xl font-black uppercase tracking-widest text-xs transition-all duration-300 shadow-md hover:scale-[1.01] active:scale-[0.99]">
                        Kirim Formulir Pendaftaran
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
