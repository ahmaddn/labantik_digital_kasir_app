<div class="py-6 w-full">
    <!-- Header Section -->
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-primary-blue to-blue-600 dark:from-gray-900 dark:to-gray-800 p-6 md:p-8 rounded-3xl shadow-xl text-white relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-20 top-2 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
        
        <div class="relative z-10">
            <span class="px-3 py-1 bg-white/20 text-xs font-black tracking-widest uppercase rounded-full border border-white/10 flex items-center gap-1.5 w-fit">
                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.776 1.848l-3.59 1.54 3.59 1.54a1 1 0 11-.776 1.848l-4-1.714a.999.999 0 01-.356-.257l-2.644-1.133a1 1 0 000-1.84l7-3a1 1 0 00-.788 0l7 3a1 1 0 000 1.84l-2.644 1.133a.999.999 0 01-.356.257l-4 1.714a1 1 0 11-.776-1.848l3.59-1.54-3.59-1.54a1 1 0 11.776-1.848l4 1.714a.999.999 0 01.356.257l2.644 1.133a1 1 0 000 1.84l-7 3a1 1 0 00-.788 0l-7-3a1 1 0 000-1.84l7-3z"/>
                </svg>
                Pusat Edukasi Kasir
            </span>
            <h1 class="text-2xl md:text-3xl font-black mt-2 tracking-tight italic uppercase">Petunjuk Penggunaan & SOP Kasir</h1>
            <p class="text-blue-100 mt-2 text-xs md:text-sm max-w-2xl font-medium">
                Selamat datang di sistem Kasir LabAntik. Halaman ini dirancang untuk membimbing Anda memahami alur kerja operasional kasir secara cepat, aman, dan profesional.
            </p>
        </div>
        
        <div class="flex-shrink-0 relative z-10 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Reset Welcome Modal -->
            <button @click="localStorage.removeItem('hasSeenWelcomeGuide_v1'); $dispatch('toast', { message: 'Modal SOP selamat datang diaktifkan kembali untuk Dashboard.' })" class="px-4 py-3 text-xs font-black rounded-2xl bg-amber-500 hover:bg-amber-600 text-white shadow-md transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"/></svg>
                Aktifkan Ulang Modal
            </button>

            <!-- Disabled Contact Button -->
            <button disabled class="px-4 py-3 text-xs font-black rounded-2xl bg-gray-200 dark:bg-gray-800 text-gray-400 dark:text-gray-500 cursor-not-allowed shadow-none border border-gray-150 dark:border-gray-700 opacity-60">
                Hubungi Pengelola (Nonaktif)
            </button>
        </div>
    </div>

    <!-- Horizontal Navigation Pills (Top Layout) -->
    <div class="flex flex-wrap gap-2 mb-8 bg-white dark:bg-gray-900 p-2.5 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm w-full">
        <button wire:click="selectTab('sop-transaksi')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'sop-transaksi' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            SOP Transaksi
        </button>

        <button wire:click="selectTab('panduan-menu')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'panduan-menu' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            Panduan Menu Sidebar
        </button>
        
        <button wire:click="selectTab('buku-kas')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'buku-kas' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pengelolaan Kas
        </button>

        <button wire:click="selectTab('tutup-buku')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'tutup-buku' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Tutup Sesi & Audit
        </button>

        <button wire:click="selectTab('presensi-tugas')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'presensi-tugas' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Presensi & Tugas
        </button>

        <button wire:click="selectTab('leaderboard')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'leaderboard' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
            </svg>
            Papan Skor & Streak
        </button>

        <button wire:click="selectTab('faq')"
            class="flex items-center px-5 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 {{ $activeTab === 'faq' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pertanyaan (FAQ)
        </button>
    </div>

    <!-- Active Tab Content Area (Spans full width of the screen, matching Buku Kas style) -->
    <div class="w-full">
        @if($activeTab === 'sop-transaksi')
            <!-- TAB: SOP TRANSAKSI (Wide Grid Layout) -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-855 w-full space-y-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-primary-blue dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">SOP Transaksi Penjualan</h2>
                        <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-bold">Langkah operasional melayani customer di kasir</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                    <!-- Step 1 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 relative flex flex-col justify-between">
                        <span class="absolute right-4 top-4 text-3xl font-black text-blue-500/20">01</span>
                        <div class="space-y-2">
                            <h4 class="font-black text-sm text-gray-950 dark:text-white uppercase">1. Buka Sesi & Absen</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Klik tombol <strong>"Buka Kasir"</strong> di dashboard. Sistem memicu modal absen masuk. Isi keterangan kehadiran Anda, lalu input <strong>Stok Awal (Opening Stock)</strong> semua produk di rak.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 relative flex flex-col justify-between">
                        <span class="absolute right-4 top-4 text-3xl font-black text-blue-500/20">02</span>
                        <div class="space-y-2">
                            <h4 class="font-black text-sm text-gray-950 dark:text-white uppercase">2. Input Penjualan (POS)</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Pilih produk di menu instan atau scan barcode. Sesuaikan jumlah pesanan di keranjang belanja. Input nama pembeli dan pilih tipe pembayaran (Lunas/Hutang/Sisa Kembalian).
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 relative flex flex-col justify-between">
                        <span class="absolute right-4 top-4 text-3xl font-black text-blue-500/20">03</span>
                        <div class="space-y-2">
                            <h4 class="font-black text-sm text-gray-950 dark:text-white uppercase">3. Catat Pengeluaran</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Bila ada pengeluaran mendadak untuk shift operasional (seperti membeli lakban atau sabun cuci), klik tombol <strong>"Catat Pengeluaran"</strong> di atas POS untuk menjaga kebersihan laci kasir.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 relative flex flex-col justify-between">
                        <span class="absolute right-4 top-4 text-3xl font-black text-blue-500/20">04</span>
                        <div class="space-y-2">
                            <h4 class="font-black text-sm text-gray-950 dark:text-white uppercase">4. Selesai Sesi (Tutup)</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Tekan tombol <strong>"Selesai"</strong> di POS. Masukkan jumlah <strong>Stok Sisa Fisik (Closing Stock)</strong>, hitung uang fisik laci kasir, dan klik Simpan untuk menutup giliran jaga Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'panduan-menu')
            <!-- TAB: PANDUAN MENU SIDEBAR (Spacious Grid Layout) -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-855 w-full space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-primary-blue dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Panduan Menu Sidebar Kasir</h2>
                        <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-bold">Langkah demi langkah dalam menggunakan fitur yang tersedia di setiap halaman kasir</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                    <!-- Menu Item 1 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Dashboard Overview
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Halaman ringkasan status shift operasional kasir yang sedang aktif.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Buka dashboard untuk melihat rangkuman omzet dan margin hari ini.</li>
                                <li>Memeriksa status kehadiran (presensi) Anda yang tercatat di tab kanan atas.</li>
                                <li>Selesaikan checklist **Tugas Harian** Anda di bagian bawah agar poin dan streak Anda bertambah.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 2 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Petunjuk & SOP
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Pusat edukasi petunjuk pengoperasian kasir dan papan peringkat gamifikasi.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Membaca detail SOP transaksi penjualan untuk memandu langkah kasir.</li>
                                <li>Memeriksa tab **Papan Skor & Streak** untuk melihat pencapaian poin dan memotivasi persaingan sehat dengan kasir lainnya.</li>
                                <li>Mencari jawaban cepat dari modul **FAQ** jika printer bermasalah atau salah input belanja.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 3 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Buka Mode Kasir (POS)
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Halaman pelayanan kasir instan untuk transaksi retail maupun makanan/minuman.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Klik tombol menu atau cari nama barang di kolom pencarian instan.</li>
                                <li>Gunakan tombol **+ / -** untuk menyesuaikan jumlah barang belanjaan di keranjang.</li>
                                <li>Ketik nama pelanggan, masukkan jumlah pembayaran tunai dari pelanggan (sistem otomatis menghitung uang kembalian).</li>
                                <li>Pilih status pelunasan (Lunas, Hutang, atau Pending Kembalian).</li>
                                <li>Klik tombol **Bayar** atau tekan **Enter** untuk menyelesaikan transaksi dan mencetak struk thermal.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 4 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Buku Kas Internal
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Melakukan pencatatan arus masuk/keluar kas non-POS secara manual.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Pilih jenis kas yang akan digunakan (Modal atau Keuntungan).</li>
                                <li>Klik tombol **"Catat Transaksi Kas"** untuk mengisi nominal, tipe (pemasukan/pengeluaran), serta keterangan tertulis.</li>
                                <li><strong>Kasir Sub-Unit Usaha (Angkringan):</strong> Memiliki tombol **"Gabungkan ke Kas Induk"** untuk menyetor dan mentransfer sisa keuntungan/dana harian langsung ke kas utama unit induk secara real-time.</li>
                                <li><strong>Kasir Unit Utama / Biasa:</strong> Tidak memiliki tombol penggabungan. Anda **hanya dapat memantau dan mencatat data mutasi kas** lokal unit Anda sendiri.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 5 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Rekap Harian & Audit
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Melakukan audit akhir dan meninjau laporan shift kasir yang telah ditutup.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Pilih tanggal audit sesi kasir.</li>
                                <li>Bandingkan nominal pendapatan sistem dengan nominal uang fisik laci kasir yang dilaporkan oleh kasir.</li>
                                <li>Tinjau laporan catatan tertulis kasir untuk mengonfirmasi jika ada temuan khusus pada shift tersebut.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 6 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            History Transaksi
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Log lengkap jejak rekam seluruh penjualan kasir.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Cari kode referensi atau nama pembeli jika ingin meninjau ulang detail pesanan.</li>
                                <li>Klik tombol detail mata untuk melihat item-item produk dan kasir yang menginput transaksi.</li>
                                <li>Gunakan tombol printer untuk mencetak ulang struk thermal pembeli yang hilang.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 7 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Hutang & Kembalian
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Mengelola catatan piutang belanja dan sisa uang kembalian pelanggan.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Melihat daftar nama pelanggan yang masih memiliki sangkutan hutang atau kembalian sisa belanja.</li>
                                <li>Ketika pembeli datang membayar hutang, klik tombol aksi untuk menandai transaksi telah diselesaikan/lunas di kasir.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Menu Item 8 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-150 dark:border-gray-800 space-y-3">
                        <h4 class="font-black text-sm text-primary-blue uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-lg bg-primary-blue shrink-0"></span>
                            Laporan Stok & Selisih
                        </h4>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 leading-relaxed">
                            <p><strong>Fungsi:</strong> Melakukan audit fisik persediaan produk harian.</p>
                            <p><strong>Alur Penggunaan:</strong></p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Pilih tanggal audit yang ingin diperiksa.</li>
                                <li>Lihat kolom 'Ekspektasi Sistem' (`Stok Awal - Terjual`).</li>
                                <li>Hitung jumlah fisik barang di rak pajang, lalu klik ikon pensil edit di kanan untuk mengisi jumlah fisik sebenarnya.</li>
                                <li>Sistem otomatis menghitung nilai **Selisih** (Lebih atau Hilang/Kurang) secara akurat demi keamanan inventaris.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'buku-kas')
            <!-- TAB: PENGELOLAAN KAS -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-800 w-full space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Pengelolaan Kas Jurusan & Sub-Unit</h2>
                        <p class="text-sm text-gray-400 mt-1 uppercase tracking-wider font-bold">Memahami alur pemisahan modal kerja dan keuntungan usaha</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100/50 dark:border-blue-900/20">
                            <h3 class="font-black text-gray-950 dark:text-white text-base uppercase">Buku Kas Modal</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                                Digunakan untuk mencatat dana modal operasional, seperti pembelian bahan baku ke supplier, pembayaran utang operasional, dan kas awal laci kasir.
                            </p>
                        </div>
                        
                        <div class="p-6 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100/50 dark:border-emerald-900/20">
                            <h3 class="font-black text-gray-950 dark:text-white text-base uppercase">Buku Kas Keuntungan</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                                Digunakan khusus untuk mengumpulkan margin/keuntungan bersih dari hasil penjualan retail/kuliner yang nantinya dialokasikan untuk kas utama jurusan.
                            </p>
                        </div>
                    </div>

                    <div class="p-6 bg-amber-50 dark:bg-gray-800 rounded-3xl border border-amber-200/50 dark:border-gray-700 mt-4">
                        <h4 class="font-black text-amber-700 dark:text-amber-400 text-sm uppercase tracking-wider">Fitur Konsolidasi Sub-Unit Usaha</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 leading-relaxed font-medium">
                            Bagi kasir sub-unit (seperti Angkringan Doku), saldo harian yang telah dipotong modal dapat dikirim secara langsung ke unit induk (TEFA Jurusan utama) menggunakan tombol <strong>"Gabungkan ke Kas Induk"</strong> di halaman Buku Kas Internal. Transfer ini akan memotong saldo kas sub-unit dan mencatat penambahan dana masuk di kas induk secara otomatis.
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed italic font-semibold">
                            *Catatan: Untuk kasir unit utama/biasa, fitur penggabungan ini tidak aktif, dan Anda hanya memantau serta mencatat data mutasi kas lokal unit Anda sendiri.
                        </p>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'tutup-buku')
            <!-- TAB: AUDIT & TUTUP BUKU -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-800 w-full space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Audit Bulanan & Selisih Stok</h2>
                        <p class="text-sm text-gray-400 mt-1 uppercase tracking-wider font-bold">Bagaimana pengelola memeriksa kecocokan uang dan produk fisik</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6">
                    <div class="space-y-4">
                        <h3 class="font-black text-gray-900 dark:text-white text-base">1. Laporan Selisih Stok (Audit Fisik)</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Pengelola jurusan dan administrator memantau kecocokan inventaris pada menu <strong>Laporan Selisih Stok</strong>. Halaman ini membandingkan data stok awal, penjualan di POS, serta stok sisa akhir (closing) yang Anda input secara fisik.
                        </p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl text-xs text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                            <strong>Indikator Selisih:</strong> Jika fisik akhir kurang dari sistem, status produk akan ditandai dengan badge merah <strong>"Hilang / Kurang"</strong>. Pengelola dapat melacak identitas kasir penginput stok harian tersebut untuk evaluasi.
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <h3 class="font-black text-gray-900 dark:text-white text-base">2. Tutup Buku Bulanan (Monthly Closing)</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Di akhir bulan, Pengelola Jurusan akan mengeksekusi menu <strong>Tutup Buku Bulanan</strong>. Proses ini akan mengarsipkan seluruh data transaksi bulan tersebut agar pembukuan tidak dapat diubah kembali dan mentransfer sisa saldo kas ke saldo awal modal bulan berikutnya.
                        </p>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'presensi-tugas')
            <!-- TAB: PRESENSI & TUGAS -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-800 w-full space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Presensi & Tugas Operasional</h2>
                        <p class="text-sm text-gray-400 mt-1 uppercase tracking-wider font-bold">SOP kehadiran dan tanggung jawab tugas harian kasir</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6">
                    <div class="space-y-4">
                        <h3 class="font-black text-gray-900 dark:text-white text-base">1. SOP Presensi (Check-in & Check-out)</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-150 dark:border-gray-700 space-y-2">
                                <span class="px-2.5 py-1 bg-blue-500/10 text-primary-blue text-[10px] font-black rounded-lg border border-blue-500/20 uppercase tracking-widest">Presensi Masuk (Clock-In)</span>
                                <h4 class="font-black text-sm text-gray-900 dark:text-white mt-2">Bagaimana cara Clock-In?</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    Ketika pertama kali membuka tombol <strong>"Buka Kasir"</strong> di awal shift, Anda wajib mengisi modal absensi masuk Anda.
                                </p>
                            </div>

                            <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-150 dark:border-gray-700 space-y-2">
                                <span class="px-2.5 py-1 bg-red-500/10 text-primary-red text-[10px] font-black rounded-lg border border-red-500/20 uppercase tracking-widest">Presensi Keluar (Clock-Out)</span>
                                <h4 class="font-black text-sm text-gray-900 dark:text-white mt-2">Bagaimana cara Clock-Out?</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    Clock-out dilakukan terintegrasi saat Anda menekan tombol <strong>"Selesai"</strong> (Tutup Kasir) di POS. Keterangan penutupan shift Anda berfungsi sebagai pencatatan jam pulang.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tugas Harian -->
                    <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <h3 class="font-black text-gray-900 dark:text-white text-base">2. Pelaporan Tugas Harian</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Untuk melaporkan tugas yang selesai, kasir cukup melakukan <strong>centang (toggle checkbox)</strong> di samping nama tugas tersebut di halaman kasir. 
                        </p>
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-150 dark:border-gray-700 space-y-2">
                            <p class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider">Bentuk Bukti & Pelaporan:</p>
                            <ul class="list-disc list-inside text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <li><strong>Bukti Digital:</strong> Sistem otomatis mencatat siapa kasir yang mencentang beserta <strong>waktu penyelesaian (Timestamp)</strong> tugas tersebut.</li>
                                <li><strong>Bukti Deskriptif (Opsional):</strong> Di akhir shift saat Anda melakukan tutup buku (Clock-out), Anda dapat menulis laporan penutupan pada kolom <strong>"Laporan Penutupan / Catatan Kasir"</strong> untuk merinci status tugas tertentu atau jika ada temuan khusus.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'leaderboard')
            <!-- TAB: LEADERBOARD & PRESTASI -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-800 w-full space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Papan Peringkat Kasir Aktif</h2>
                        <p class="text-sm text-gray-400 mt-1 uppercase tracking-wider font-bold">Bulan ini. Lakukan performa terbaik untuk memimpin papan skor!</p>
                    </div>
                </div>

                <!-- Animation Success Banner (Hore!) -->
                @if(auth()->user()->points + auth()->user()->pending_points > 50)
                    <div class="bg-gradient-to-r from-amber-500/10 to-yellow-500/10 border border-amber-500/20 p-6 rounded-3xl flex items-center justify-between gap-4 animate-bounce mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-amber-500/20 text-amber-600 rounded-xl">
                                <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider text-xs">Kerja Bagus, {{ auth()->user()->name }}!</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold mt-0.5">Skor Anda telah melampaui 50 Pts. Terus pertahaman performa kerja beruntun Anda!</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Podium Layout (Rank 1, 2, 3) -->
                @php
                    $top3 = $leaderboard->take(3);
                @endphp
                <div class="grid grid-cols-3 gap-4 items-end justify-center pt-8 pb-10 border-b border-gray-150 dark:border-gray-800">
                    <!-- Rank 2 -->
                    @if($top3->count() > 1)
                        @php $u2 = $top3->values()->get(1); @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 bg-gray-200 dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-700 rounded-full flex items-center justify-center font-black text-gray-700 dark:text-gray-300 uppercase text-xs">
                                {{ substr($u2->name, 0, 2) }}
                            </div>
                            <div class="text-center mt-2 min-w-0 w-full">
                                <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $u2->name }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $u2->total_score }} Pts</p>
                            </div>
                            <div class="w-full h-16 bg-gray-100 dark:bg-gray-800 rounded-t-xl mt-3 flex items-center justify-center font-black text-gray-500 dark:text-gray-400 text-lg">
                                2
                            </div>
                        </div>
                    @endif

                    <!-- Rank 1 -->
                    @if($top3->count() > 0)
                        @php $u1 = $top3->values()->get(0); @endphp
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <!-- Crown SVG -->
                                <svg class="w-6 h-6 text-amber-500 absolute -top-5 left-1/2 transform -translate-x-1/2 animate-bounce" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <div class="w-18 h-18 bg-amber-500/20 border-4 border-amber-500 rounded-full flex items-center justify-center font-black text-amber-600 dark:text-amber-400 uppercase text-sm">
                                    {{ substr($u1->name, 0, 2) }}
                                </div>
                            </div>
                            <div class="text-center mt-2 min-w-0 w-full">
                                <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $u1->name }}</p>
                                <p class="text-xs font-black text-amber-500 mt-0.5">{{ $u1->total_score }} Pts</p>
                            </div>
                            <div class="w-full h-24 bg-amber-500/10 dark:bg-amber-500/20 border-t-2 border-amber-500 rounded-t-xl mt-3 flex items-center justify-center font-black text-amber-600 dark:text-amber-400 text-2xl">
                                1
                            </div>
                        </div>
                    @endif

                    <!-- Rank 3 -->
                    @if($top3->count() > 2)
                        @php $u3 = $top3->values()->get(2); @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-amber-900/10 dark:bg-amber-955/20 border-2 border-amber-800/30 rounded-full flex items-center justify-center font-black text-amber-800 dark:text-amber-600 uppercase text-[10px]">
                                {{ substr($u3->name, 0, 2) }}
                            </div>
                            <div class="text-center mt-2 min-w-0 w-full">
                                <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ $u3->name }}</p>
                                <p class="text-[10px] font-bold text-gray-455 mt-0.5">{{ $u3->total_score }} Pts</p>
                            </div>
                            <div class="w-full h-12 bg-gray-50 dark:bg-gray-900 rounded-t-xl mt-3 flex items-center justify-center font-black text-gray-455 text-base">
                                3
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Leaderboard Table -->
                <div class="overflow-x-auto w-full rounded-2xl border border-gray-100 dark:border-gray-800">
                    <table class="w-full text-left min-w-[500px] md:min-w-0">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-16">Peringkat</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kasir</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-24">Streak</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-32">Total Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                            @foreach($leaderboard as $index => $u)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors {{ $u->id === auth()->id() ? 'bg-primary-blue/5 dark:bg-primary-blue/10 font-bold' : '' }}">
                                    <td class="px-6 py-4 text-center text-xs font-black text-gray-500 dark:text-gray-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[10px] font-black uppercase text-primary-blue border border-gray-200 dark:border-gray-700">
                                                {{ substr($u->name, 0, 2) }}
                                            </div>
                                            <span class="text-xs text-gray-800 dark:text-gray-200">{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="px-1.5 py-0.5 bg-primary-blue text-white rounded text-[8px] font-black uppercase tracking-wider">Anda</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 text-xs font-black text-orange-500">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.94-.209.381-.363.887-.453 1.488-.12.802-.073 1.84.195 2.87l.062.24c.024.1.039.223.05.375a2.037 2.037 0 01-.029.511c-.048.24-.154.48-.32.647a.997.997 0 01-1.08.16c-.461-.247-.744-.623-.926-1.08-.182-.456-.224-.959-.224-1.347V6a1 1 0 00-1-1 3 3 0 00-2 2.22c0 1.258.18 2.5.474 3.738.152.64.4 1.25.753 1.807.353.558.836 1.057 1.443 1.487a8.007 8.007 0 005.19 2.09c.477.027.947-.033 1.4-.18a7.995 7.995 0 003.86-2.482c.187-.228.34-.483.47-.752.43-.892.652-1.928.652-3.141 0-1.622-.515-2.91-1.293-3.812a6.002 6.002 0 00-.825-1.012l-.011-.011-.002-.002a1 1 0 00-1.436.17l-.02.027a4.01 4.01 0 01-.262.33c-.758.874-1.808 1.47-3.2 1.47V2.553z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $u->streak }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs font-black text-gray-800 dark:text-white">
                                        {{ $u->total_score }} Pts
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Points Rule Guide -->
                <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-800 w-full">
                    <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-widest mb-3">Aturan Perolehan Skor & Poin</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 text-primary-blue rounded-lg shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-800 dark:text-white">Melayani POS</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Selesaikan checkout keranjang belanja di POS: <strong>+5 Poin & +1 Streak</strong></p>
                            </div>
                        </div>
                        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-800 dark:text-white">Menyelesaikan Tugas</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Centang/Toggle tugas harian kasir: <strong>+10 Poin & +1 Streak</strong></p>
                            </div>
                        </div>
                        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex items-start gap-3">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-800 dark:text-white">Buka & Selesai Sesi</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Input stok pembuka/penutup dan catat laci uang: <strong>+15 Poin</strong> (Streak kembali ke 0)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'faq')
            <!-- TAB: FAQ -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-gray-100 dark:border-gray-800 w-full space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white uppercase italic tracking-tighter">Pertanyaan Umum (FAQ)</h2>
                        <p class="text-sm text-gray-400 mt-1 uppercase tracking-wider font-bold">Solusi cepat saat Anda menemui kendala dalam aplikasi</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6" x-data="{ activeFaq: null }">
                    <!-- FAQ 1 -->
                    <div class="border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden transition-all">
                        <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full flex items-center justify-between p-5 text-left font-black text-sm text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span>Bagaimana jika printer struk macet atau mati saat transaksi?</span>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="activeFaq === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeFaq === 1" class="p-5 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 leading-relaxed">
                            Selesaikan transaksi terlebih dahulu agar tercatat di sistem. Periksa kabel power printer dan koneksi kabel data (USB). Jika kertas thermal habis, ganti dengan gulungan yang baru. Setelah printer kembali normal, Anda dapat mencetak ulang struk dari menu <strong>"History Transaksi"</strong>.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden transition-all">
                        <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full flex items-center justify-between p-5 text-left font-black text-sm text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span>Bagaimana jika salah menginput produk yang dibeli?</span>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="activeFaq === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeFaq === 2" class="p-5 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 leading-relaxed">
                            Jika pembayaran <strong>belum</strong> diselesaikan, Anda dapat langsung menghapus produk dari keranjang belanja dengan menekan tombol tempat sampah atau tombol kurangi (<strong>-</strong>) hingga kuantitas menjadi nol. Jika transaksi <strong>sudah</strong> disimpan, segera hubungi Pengelola Jurusan/Superadmin untuk melakukan pembatalan transaksi secara berwenang.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden transition-all">
                        <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full flex items-center justify-between p-5 text-left font-black text-sm text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span>Bagaimana cara mencatat uang kembalian yang dihutangkan?</span>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="activeFaq === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeFaq === 3" class="p-5 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 leading-relaxed">
                            Masuk ke menu <strong>"Hutang & Kembalian"</strong>. Anda dapat mendaftarkan transaksi piutang atau sisa kembalian customer yang belum terbayarkan di halaman tersebut dengan memasukkan nama customer, tanggal, dan nominal terkait agar tercatat rapi di pembukuan toko.
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
