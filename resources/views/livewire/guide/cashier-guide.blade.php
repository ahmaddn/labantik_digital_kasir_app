<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-primary-blue to-blue-600 dark:from-gray-900 dark:to-gray-850 p-8 rounded-3xl shadow-xl text-white relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-20 top-2 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
        
        <div class="relative z-10">
            <span class="px-3 py-1 bg-white/20 text-xs font-black tracking-widest uppercase rounded-full border border-white/10">
                Pusat Edukasi Kasir
            </span>
            <h1 class="text-3xl font-black mt-2 tracking-tight italic">PETUNJUK PENGGUNAAN & SOP KASIR</h1>
            <p class="text-blue-100 mt-2 text-sm max-w-2xl font-medium">
                Selamat datang di sistem Kasir LabAntik. Halaman ini dirancang untuk membimbing Anda memahami alur kerja operasional kasir secara cepat, aman, dan profesional.
            </p>
        </div>
        <div class="flex-shrink-0 relative z-10">
            <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg animate-pulse">
                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-blue-100 font-bold">Status Keaktifan</p>
                    <p class="text-sm font-black text-white">Sesi Siap Digunakan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Navigation Sidebar / Tabs -->
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 sticky top-6">
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Navigasi Panduan</p>
                <div class="space-y-1">
                    <button wire:click="selectTab('sop-transaksi')"
                        class="w-full flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all duration-300 {{ $activeTab === 'sop-transaksi' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-850 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        SOP Transaksi
                    </button>
                    
                    <button wire:click="selectTab('buku-kas')"
                        class="w-full flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all duration-300 {{ $activeTab === 'buku-kas' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-850 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pengelolaan Kas
                    </button>

                    <button wire:click="selectTab('tutup-buku')"
                        class="w-full flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all duration-300 {{ $activeTab === 'tutup-buku' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-850 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Tutup Sesi & Audit
                    </button>

                    <button wire:click="selectTab('presensi-tugas')"
                        class="w-full flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all duration-300 {{ $activeTab === 'presensi-tugas' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-850 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Presensi & Tugas
                    </button>

                    <button wire:click="selectTab('faq')"
                        class="w-full flex items-center px-4 py-3.5 text-sm font-black rounded-2xl transition-all duration-300 {{ $activeTab === 'faq' ? 'bg-primary-blue text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-850 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pertanyaan (FAQ)
                    </button>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 text-center">
                    <p class="text-xs text-gray-400 font-semibold mb-2">Butuh Bantuan Lainnya?</p>
                    <a href="https://wa.me/#" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-3 text-xs font-black rounded-xl bg-green-500 hover:bg-green-600 text-white shadow-md transition-all">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.455 5.703 1.458h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Hubungi Pengelola
                    </a>
                    
                    <button onclick="localStorage.removeItem('hasSeenWelcomeGuide_v1'); alert('Modal selamat datang diaktifkan kembali! Modal SOP akan muncul saat Anda masuk ke Dashboard berikutnya.');" class="mt-3 inline-flex items-center justify-center w-full px-4 py-3 text-xs font-black rounded-xl bg-amber-500 hover:bg-amber-600 text-white shadow-md transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"/></svg>
                        Aktifkan Ulang Modal
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3 space-y-6">
            @if($activeTab === 'sop-transaksi')
                <!-- TAB: SOP TRANSAKSI -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-primary-blue dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-none">SOP TRANSAKSI PENJUALAN</h2>
                            <p class="text-sm text-gray-400 mt-1 font-medium">Langkah demi langkah dalam melayani transaksi pembelian customer</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <!-- Stepper UI -->
                        <div class="relative pl-8 space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-blue-100 dark:before:bg-gray-800">
                            <!-- Step 1 -->
                            <div class="relative">
                                <div class="absolute -left-[29px] top-0.5 w-6 h-6 rounded-full bg-primary-blue text-white flex items-center justify-center text-xs font-black ring-4 ring-white dark:ring-gray-900">1</div>
                                <div class="bg-gray-50 dark:bg-gray-850/50 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800">
                                    <h4 class="font-black text-gray-900 dark:text-white">Buka Sesi Kasir & Input Modal Awal</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Sebelum melayani transaksi, pastikan Anda menekan tombol <strong>"Buka Mode Kasir"</strong> di dashboard atau sidebar. Masukkan jumlah <strong>Modal Uang Awal</strong> yang ada di laci kasir (misal untuk uang kembalian).
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="relative">
                                <div class="absolute -left-[29px] top-0.5 w-6 h-6 rounded-full bg-primary-blue text-white flex items-center justify-center text-xs font-black ring-4 ring-white dark:ring-gray-900">2</div>
                                <div class="bg-gray-50 dark:bg-gray-850/50 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800">
                                    <h4 class="font-black text-gray-900 dark:text-white">Pilih Produk & Kuantitas</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Klik pada produk yang diinginkan di halaman kasir, atau gunakan kotak pencarian cepat. Tentukan kuantitas item dengan menekan tombol <strong>+</strong> atau <strong>-</strong>.
                                    </p>
                                    <div class="mt-3 flex gap-2 flex-wrap">
                                        <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 text-xs font-black rounded-lg border border-amber-500/20">Tips: Gunakan Barcode Scanner jika tersedia</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="relative">
                                <div class="absolute -left-[29px] top-0.5 w-6 h-6 rounded-full bg-primary-blue text-white flex items-center justify-center text-xs font-black ring-4 ring-white dark:ring-gray-900">3</div>
                                <div class="bg-gray-50 dark:bg-gray-850/50 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800">
                                    <h4 class="font-black text-gray-900 dark:text-white">Proses Pembayaran</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Tekan tombol <strong>"Bayar / Selesaikan Transaksi"</strong>. Pilih metode pembayaran:
                                    </p>
                                    <ul class="list-disc list-inside text-xs text-gray-500 dark:text-gray-400 mt-2 space-y-1">
                                        <li><strong>Tunai:</strong> Masukkan uang yang diterima, sistem akan otomatis menghitung nominal kembalian.</li>
                                        <li><strong>QRIS/Non-Tunai:</strong> Tunjukkan kode QRIS (jika tersedia), pastikan status uang telah sukses masuk ke rekening sebelum mengonfirmasi pembayaran di kasir.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="relative">
                                <div class="absolute -left-[29px] top-0.5 w-6 h-6 rounded-full bg-primary-blue text-white flex items-center justify-center text-xs font-black ring-4 ring-white dark:ring-gray-900">4</div>
                                <div class="bg-gray-50 dark:bg-gray-850/50 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800">
                                    <h4 class="font-black text-gray-900 dark:text-white">Cetak & Berikan Struk</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Sistem akan mencetak struk secara otomatis (atau klik tombol <strong>"Cetak Struk"</strong>). Robek struk dengan rapi lalu serahkan struk belanja beserta uang kembalian ke customer dengan sopan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'buku-kas')
                <!-- TAB: PENGELOLAAN KAS -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-none">PENGELOLAAN BUKU KAS</h2>
                            <p class="text-sm text-gray-400 mt-1 font-medium">SOP pencatatan arus masuk/keluar uang kas internal di laci kasir</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6">
                        <div class="p-6 bg-blue-50 dark:bg-blue-950/20 rounded-2xl border border-blue-100 dark:border-blue-900/50">
                            <h3 class="font-black text-blue-900 dark:text-blue-300 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Apa itu Buku Kas Internal?
                            </h3>
                            <p class="text-sm text-blue-800/80 dark:text-blue-400/85 mt-2">
                                Buku kas digunakan untuk mencatat pengeluaran kecil kasir (seperti membeli kertas struk baru, air mineral galon toko, dll) atau penambahan saldo kas darurat. Hal ini krusial agar saldo sistem Anda tetap cocok dengan jumlah uang fisik di laci saat rekap audit.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border border-gray-100 dark:border-gray-800 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-850/40">
                                <span class="px-2.5 py-0.5 bg-green-500/10 text-green-500 text-[10px] font-black rounded-full uppercase border border-green-500/20">Kas Masuk (Income)</span>
                                <h4 class="font-black text-gray-900 dark:text-white mt-2">Kapan mencatat Kas Masuk?</h4>
                                <ul class="list-disc list-inside text-xs text-gray-500 dark:text-gray-400 mt-2 space-y-2">
                                    <li>Menerima tambahan uang modal receh dari Pengelola Jurusan.</li>
                                    <li>Pembayaran hutang customer yang diserahkan secara tunai di luar transaksi pos langsung.</li>
                                    <li>Pastikan jenis kas diisi dengan benar: <strong>Kas Penjualan</strong> atau <strong>Keuntungan</strong>.</li>
                                </ul>
                            </div>

                            <div class="border border-gray-100 dark:border-gray-800 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-850/40">
                                <span class="px-2.5 py-0.5 bg-red-500/10 text-red-500 text-[10px] font-black rounded-full uppercase border border-red-500/20">Kas Keluar (Expense)</span>
                                <h4 class="font-black text-gray-900 dark:text-white mt-2">Kapan mencatat Kas Keluar?</h4>
                                <ul class="list-disc list-inside text-xs text-gray-500 dark:text-gray-400 mt-2 space-y-2">
                                    <li>Membeli kebutuhan operasional mendadak (ATK, lakban, plastik).</li>
                                    <li>Pemberian kembalian transaksi hutang yang tidak langsung tuntas.</li>
                                    <li><strong>Wajib:</strong> Tulis alasan detail pada kolom catatan beserta nominal pengeluaran agar mempermudah proses verifikasi pengelola.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'tutup-buku')
                <!-- TAB: TUTUP SESI & AUDIT -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 dark:text-amber-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-none">SOP PENUTUPAN BUKU HARIAN</h2>
                            <p class="text-sm text-gray-400 mt-1 font-medium">Langkah penting di akhir shift untuk audit keakuratan dana</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6">
                        <!-- SOP Cards -->
                        <div class="grid grid-cols-1 gap-4">
                            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-850/30 border border-gray-100 dark:border-gray-850">
                                <span class="w-8 h-8 rounded-full bg-primary-blue/10 text-primary-blue flex items-center justify-center text-sm font-black flex-shrink-0">1</span>
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white">Hitung Uang Fisik Secara Manual</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Keluarkan semua uang kertas dan logam dari laci kasir. Hitung jumlah total uang fisik dengan cermat di akhir jam operasional/shift Anda.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-850/30 border border-gray-100 dark:border-gray-850">
                                <span class="w-8 h-8 rounded-full bg-primary-blue/10 text-primary-blue flex items-center justify-center text-sm font-black flex-shrink-0">2</span>
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white">Masuk Menu Rekap Harian & Audit</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Navigasi ke menu <strong>"Rekap Harian & Audit"</strong> di sidebar. Masukkan total nominal uang fisik yang Anda hitung pada kolom input <strong>"Actual Cash"</strong>.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-850/30 border border-gray-100 dark:border-gray-850">
                                <span class="w-8 h-8 rounded-full bg-primary-blue/10 text-primary-blue flex items-center justify-center text-sm font-black flex-shrink-0">3</span>
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white">Periksa Selisih (Difference)</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Sistem akan membandingkan uang fisik (Actual Cash) dengan rekaman transaksi sistem (System Cash). Jika terjadi selisih (minus/plus), <strong>tulis catatan/keterangan penyebab selisih</strong> di kolom yang disediakan (misal: "Kembalian Rp 500 dibulatkan" atau "Ada selisih barang rusak").
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50/50 dark:bg-gray-850/30 border border-gray-100 dark:border-gray-850">
                                <span class="w-8 h-8 rounded-full bg-primary-blue/10 text-primary-blue flex items-center justify-center text-sm font-black flex-shrink-0">4</span>
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white">Tutup Sesi Kasir</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Setelah menekan konfirmasi penutupan buku, sesi Anda pada hari tersebut akan terkunci. Sistem akan menampilkan status <strong>"Sesi Kasir Berakhir"</strong> di sidebar dan halaman penjualan akan dinonaktifkan hingga hari esok dibuka kembali.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-red-500/10 text-red-500 rounded-2xl border border-red-500/20 text-xs font-black flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <p class="uppercase">Peringatan Keras:</p>
                                <p class="font-bold text-red-700/80 dark:text-red-400/85 mt-1">Jangan pernah membagikan kredensial login Anda atau membiarkan orang lain mengoperasikan kasir menggunakan akun Anda tanpa pengawasan. Segala bentuk selisih uang fisik menjadi tanggung jawab petugas kasir aktif saat sesi tersebut berlangsung.</p>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'presensi-tugas')
                <!-- TAB: PRESENSI & TUGAS -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-none">PANDUAN PRESENSI & TUGAS HARIAN</h2>
                            <p class="text-sm text-gray-400 mt-1 font-medium">Cara melakukan absensi masuk/keluar serta melaporkan tugas harian kasir</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6">
                        <!-- Presensi Section -->
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-3">1. SOP Presensi (Check-in & Check-out)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="p-6 bg-gray-50 dark:bg-gray-850 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-3">
                                    <span class="px-2.5 py-0.5 bg-teal-500/10 text-teal-600 dark:text-teal-400 text-[10px] font-black rounded-full uppercase border border-teal-500/20">Presensi Masuk (Clock In)</span>
                                    <h4 class="font-black text-gray-900 dark:text-white">Bagaimana cara Clock-In?</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        Ketika pertama kali membuka tombol <strong>"Buka Mode Kasir"</strong> di hari tersebut, modal absensi masuk akan muncul secara otomatis.
                                    </p>
                                    <ul class="list-disc list-inside text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
                                        <li>Hitung uang modal awal di laci kasir secara teliti.</li>
                                        <li>Input jumlah tersebut di kolom <strong>"Modal Awal (Opening Cash)"</strong>.</li>
                                        <li>Klik <strong>"Simpan Absensi"</strong> untuk mulai melayani transaksi.</li>
                                    </ul>
                                </div>

                                <div class="p-6 bg-gray-50 dark:bg-gray-850 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-3">
                                    <span class="px-2.5 py-0.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black rounded-full uppercase border border-rose-500/20">Presensi Keluar (Clock Out)</span>
                                    <h4 class="font-black text-gray-900 dark:text-white">Bagaimana cara Clock-Out?</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        Clock-out dilakukan terintegrasi saat Anda melakukan penutupan kas di menu <strong>"Rekap Harian & Audit"</strong>.
                                    </p>
                                    <ul class="list-disc list-inside text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
                                        <li>Pilih opsi tutup sesi harian.</li>
                                        <li>Input stok sisa (Closing Stock) produk di laci kasir.</li>
                                        <li>Input jumlah uang fisik aktual yang ada di laci saat itu.</li>
                                        <li>Kirim data; sistem secara otomatis mencatat waktu <strong>Clock-out</strong> Anda.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tugas Section -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-3">2. Manajemen Tugas Harian Kasir</h3>
                            <div class="p-6 bg-blue-500/5 rounded-2xl border border-blue-500/10 space-y-4">
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white text-sm">Di mana Kasir Melihat Tugasnya?</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                        Seluruh tugas harian yang didelegasikan oleh Pengelola Jurusan dapat dipantau langsung di <strong>halaman Utama Mode Kasir</strong> (terdapat panel khusus <strong>"Tugas Harian Kasir"</strong>).
                                    </p>
                                </div>
                                
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white text-sm">Bagaimana Cara Melaporkan Tugas yang Selesai & Buktinya?</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                        Untuk melaporkan tugas yang selesai, kasir cukup melakukan <strong>centang (toggle checkbox)</strong> di samping nama tugas tersebut di halaman kasir. 
                                    </p>
                                    <div class="mt-3 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800 space-y-2">
                                        <p class="text-[11px] font-black text-gray-850 dark:text-gray-150">Bentuk Bukti & Pelaporan:</p>
                                        <ul class="list-disc list-inside text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
                                            <li><strong>Bukti Digital:</strong> Sistem otomatis mencatat siapa kasir yang mencentang beserta <strong>waktu penyelesaian (Timestamp)</strong> tugas tersebut.</li>
                                            <li><strong>Bukti Deskriptif (Opsional):</strong> Di akhir shift saat Anda melakukan tutup buku (Clock-out), Anda dapat menulis laporan penutupan pada kolom <strong>"Laporan Penutupan / Catatan Kasir"</strong> untuk merinci status tugas tertentu atau jika ada temuan khusus.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'faq')
                <!-- TAB: FAQ -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-none">PERTANYAAN UMUM (FAQ)</h2>
                            <p class="text-sm text-gray-400 mt-1 font-medium">Solusi cepat saat Anda menemui kendala dalam aplikasi</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6 space-y-6" x-data="{ activeFaq: null }">
                        <!-- FAQ 1 -->
                        <div class="border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden transition-all">
                            <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full flex items-center justify-between p-5 text-left font-black text-sm text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-850/20 hover:bg-gray-50 dark:hover:bg-gray-850">
                                <span>Bagaimana jika printer struk macet atau mati saat transaksi?</span>
                                <svg class="w-4 h-4 transition-transform duration-300" :class="activeFaq === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="activeFaq === 1" class="p-5 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 leading-relaxed">
                                Selesaikan transaksi terlebih dahulu agar tercatat di sistem. Periksa kabel power printer dan koneksi kabel data (USB). Jika kertas thermal habis, ganti dengan gulungan yang baru. Setelah printer kembali normal, Anda dapat mencetak ulang struk dari menu <strong>"History Transaksi"</strong>.
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden transition-all">
                            <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full flex items-center justify-between p-5 text-left font-black text-sm text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-850/20 hover:bg-gray-50 dark:hover:bg-gray-850">
                                <span>Bagaimana jika salah menginput produk yang dibeli?</span>
                                <svg class="w-4 h-4 transition-transform duration-300" :class="activeFaq === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="activeFaq === 2" class="p-5 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 leading-relaxed">
                                Jika pembayaran <strong>belum</strong> diselesaikan, Anda dapat langsung menghapus produk dari keranjang belanja dengan menekan tombol tempat sampah atau tombol kurangi (<strong>-</strong>) hingga kuantitas menjadi nol. Jika transaksi <strong>sudah</strong> disimpan, segera hubungi Pengelola Jurusan/Superadmin untuk melakukan void/pembatalan transaksi secara berwenang.
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden transition-all">
                            <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full flex items-center justify-between p-5 text-left font-black text-sm text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-850/20 hover:bg-gray-50 dark:hover:bg-gray-850">
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
</div>
