<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Tutup Buku Bulanan</h1>
        <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Kunci & Arsipkan Transaksi Lama untuk Menghindari Selisih</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Input Form Card -->
        <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-[3rem] p-8 shadow-2xl border border-gray-150 dark:border-gray-700">
            <div class="space-y-6">
                <!-- Select Month -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Pilih Bulan Yang Akan Ditutup</label>
                    <select wire:model.live="selectedMonth" 
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 focus:border-primary-blue dark:focus:border-primary-blue rounded-2xl focus:ring-0 text-sm font-bold dark:text-white transition-colors uppercase">
                        @foreach($availableMonths as $month)
                            <option value="{{ $month }}">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</option>
                        @endforeach
                    </select>
                </div>

                @if($isClosed)
                    <!-- Already Closed State -->
                    <div class="bg-emerald-500/10 border-2 border-dashed border-emerald-500 text-emerald-600 dark:text-emerald-400 p-6 rounded-2xl text-center">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h4 class="text-base font-black uppercase tracking-wider">TUTUP BUKU SELESAI</h4>
                        <p class="text-xs font-bold mt-1 opacity-80">Seluruh transaksi di bulan {{ \Carbon\Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y') }} telah berhasil dikunci dan diarsipkan.</p>
                    </div>
                @else
                    <!-- Active Carry Forward Inputs -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-3xl space-y-6 border border-gray-100 dark:border-gray-800">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">PENYESUAIAN SALDO BAWAAN (CARRY FORWARD)</h3>
                        <p class="text-xs font-bold text-gray-400 leading-relaxed">
                            Masukkan nominal uang kas bawaan riil yang akan dipindahkan ke awal bulan berikutnya. Nominal di bawah ini otomatis terisi dari saldo akhir kas sistem bulan ini, namun dapat disesuaikan jika ada selisih uang fisik.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Saldo Awal Modal Baru (Rp)</label>
                                <input type="number" wire:model="carryForwardModal"
                                    class="w-full px-6 py-4 bg-white dark:bg-gray-800 border-2 border-gray-250 dark:border-gray-700 focus:border-primary-blue rounded-2xl focus:ring-0 text-sm font-bold dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Saldo Awal Keuntungan Baru (Rp)</label>
                                <input type="number" wire:model="carryForwardProfit"
                                    class="w-full px-6 py-4 bg-white dark:bg-gray-800 border-2 border-gray-250 dark:border-gray-700 focus:border-primary-blue rounded-2xl focus:ring-0 text-sm font-bold dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button wire:click="closeMonth" 
                        class="w-full py-5 bg-primary-blue text-white rounded-3xl font-black italic uppercase text-sm tracking-widest shadow-xl shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        PROSES TUTUP BUKU & KUNCI TRANSAKSI
                    </button>
                @endif
            </div>
        </div>

        <!-- Month Summary Stats Card -->
        <div class="bg-gray-900 text-white rounded-[3rem] p-8 shadow-2xl flex flex-col justify-between relative overflow-hidden border-b-8 border-primary-blue">
            <div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 mb-1">RINGKASAN AKHIR</h3>
                <h2 class="text-lg font-black uppercase italic text-white">{{ \Carbon\Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y') }}</h2>
                
                <div class="space-y-4 mt-8">
                    <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-800">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kas Modal</span>
                        <span class="font-black text-sm text-gray-200">Rp{{ number_format($monthStats['modal'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-800">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kas Keuntungan</span>
                        <span class="font-black text-sm text-emerald-400">Rp{{ number_format($monthStats['profit'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-gray-800">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">TOTAL SALDO AKHIR</p>
                <p class="text-3xl font-black italic text-white">Rp{{ number_format($monthStats['total'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
