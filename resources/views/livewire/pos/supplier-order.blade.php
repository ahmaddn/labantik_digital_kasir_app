<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6" x-data="{
    openWhatsApp(url) {
        window.open(url, '_blank');
    }
}" x-on:open-whatsapp.window="openWhatsApp($event.detail.url)">

    @if ($isUnauthorizedKasir)
        <!-- Halaman Error Khusus: Tidak Terjadwal Piket Hari Ini (Sesuai Desain LabAntik) -->
        <div class="min-h-[65vh] flex items-center justify-center p-4">
            <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 text-center shadow-lg space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 border border-amber-200 dark:border-amber-800/80 flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>

                <div class="space-y-2">
                    <span class="inline-block px-3 py-1 bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 rounded-full text-[10px] font-black uppercase tracking-wider">
                        Akses Terkunci
                    </span>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Tidak Ada Jadwal Piket Hari Ini
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Fitur pemesanan produk via WhatsApp untuk kasir hanya dapat diakses oleh kasir yang sedang bertugas/terjadwal pada hari ini.
                    </p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-2.5 justify-center">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                        <span>Kembali ke Dashboard</span>
                    </a>
                    <a href="{{ route('schedules') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        <span>Lihat Jadwal Kasir</span>
                    </a>
                </div>
            </div>
        </div>
    @else

    <!-- Header Halaman Bersih & Bernafas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <span>Pesan Produk ke Seller</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                    WhatsApp Order
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pilih atau cari produk dari katalog, tentukan jumlah (pack/dus/pcs), dan langsung buat pesan WhatsApp otomatis.
            </p>
        </div>

        <!-- Segmented Tab Switcher -->
        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold self-start sm:self-auto">
            <button type="button" wire:click="$set('activeTab', 'order')"
                class="px-4 py-2 rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'order' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Order Baru</span>
            </button>
            <button type="button" wire:click="$set('activeTab', 'history')"
                class="px-4 py-2 rounded-lg transition-all flex items-center gap-1.5 {{ $activeTab === 'history' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Order</span>
            </button>
        </div>
    </div>

    <!-- Alert Sukses Flash -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-medium">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    @if ($activeTab === 'order')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- FORMULIR INPUT -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Section 1: Kontak Seller -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tujuan Seller / Toko</h2>
                            <p class="text-xs text-slate-500">Nama toko atau perorangan yang akan menerima pesanan via WhatsApp</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Nama Seller / Toko <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model.live="seller_name" placeholder="Misal: Bu Siti Grosir / Agen Roti Aoka"
                                class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                            @error('seller_name') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Nomor WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model.live="seller_phone" placeholder="08... / +62... / 628..."
                                class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-mono text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                            <span class="text-[10px] text-slate-400 mt-1 block">Bisa diawali 08, 62, atau +62</span>
                            @error('seller_phone') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Daftar Item Pesanan -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Produk yang Dipesan</h2>
                                <p class="text-xs text-slate-500">Cari produk di database atau ketik produk baru langsung di kolom yang sama</p>
                            </div>
                        </div>

                        <button type="button" wire:click="addItem"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-xl transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Item</span>
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        @foreach($items as $index => $item)
                            <div class="p-4 bg-slate-50/80 dark:bg-slate-800/40 rounded-xl border border-slate-200/90 dark:border-slate-700/80 space-y-3" 
                                wire:key="item-box-{{ $index }}">
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        Item #{{ $index + 1 }}
                                    </span>
                                    @if(count($items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                            class="text-xs text-slate-400 hover:text-rose-500 flex items-center gap-1 transition-colors cursor-pointer" title="Hapus Item">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                                    
                                    <!-- SEARCHABLE COMBOBOX WITH DARK/LIGHT MODE STYLING -->
                                    <div class="sm:col-span-6 relative" 
                                        x-data="{
                                            open: false,
                                            search: '{{ addslashes($item['name']) }}',
                                            products: {{ Js::from($productsList) }},
                                            get filteredProducts() {
                                                if (!this.search) return this.products.slice(0, 15);
                                                return this.products.filter(p => p.name.toLowerCase().includes(this.search.toLowerCase())).slice(0, 15);
                                            },
                                            selectProduct(prod) {
                                                this.search = prod.name;
                                                this.open = false;
                                                $wire.setItemProduct({{ $index }}, prod.id, prod.name);
                                            },
                                            useCustomName() {
                                                this.open = false;
                                                $wire.setItemProduct({{ $index }}, null, this.search);
                                            }
                                        }"
                                        @click.outside="open = false">
                                        
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                            Nama Produk / Barang <span class="text-rose-500">*</span>
                                        </label>

                                        <div class="relative">
                                            <input type="text"
                                                x-model="search"
                                                @focus="open = true"
                                                @input="open = true; $wire.setItemProduct({{ $index }}, null, search)"
                                                placeholder="Cari atau ketik produk baru..."
                                                class="w-full min-h-[44px] pl-3.5 pr-8 py-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                            
                                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </div>
                                        </div>

                                        <!-- Dropdown List yang Selaras Tema Gelap/Terang -->
                                        <div x-show="open" x-cloak
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="absolute left-0 right-0 mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-700">
                                            
                                            <!-- Option Gunakan Teks Custom Jika Belum Ada di DB -->
                                            <div x-show="search.trim().length > 0 && !filteredProducts.some(p => p.name.toLowerCase() === search.toLowerCase())"
                                                @click="useCustomName()"
                                                class="p-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400 font-semibold transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>Gunakan: "<span x-text="search"></span>" (Produk Baru)</span>
                                            </div>

                                            <!-- Opsi dari Database -->
                                            <template x-for="prod in filteredProducts" :key="prod.id">
                                                <div @click="selectProduct(prod)"
                                                    class="px-3.5 py-2.5 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer flex items-center justify-between text-xs text-slate-800 dark:text-slate-100 transition-colors">
                                                    <span x-text="prod.name" class="font-medium"></span>
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-300 bg-slate-100 dark:bg-slate-900 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">Database</span>
                                                </div>
                                            </template>

                                            <!-- State Kosong -->
                                            <div x-show="filteredProducts.length === 0 && search.trim().length === 0"
                                                class="p-3 text-center text-xs text-slate-400">
                                                Ketik nama produk untuk mencari...
                                            </div>
                                        </div>

                                        @error("items.{$index}.name") <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- JUMLAH (QTY) & SATUAN (Mobile 2 Kolom Berdampingan) -->
                                    <div class="sm:col-span-6 grid grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                                Jumlah (Qty) <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="number" min="1" wire:model.live="items.{{ $index }}.quantity" placeholder="1"
                                                class="w-full min-h-[44px] px-3 py-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-center text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                                Satuan <span class="text-rose-500">*</span>
                                            </label>
                                            <select wire:model.live="items.{{ $index }}.unit"
                                                class="w-full min-h-[44px] px-3 py-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                                                <option value="pcs">Pcs</option>
                                                <option value="pack">Pack</option>
                                                <option value="dus">Dus / Karton</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- CATATAN KHUSUS ITEM -->
                                    <div class="sm:col-span-12">
                                        <input type="text" wire:model.live="items.{{ $index }}.notes" placeholder="Catatan rasa, kemasan, atau varian (contoh: rasa keju / vanila - opsional)"
                                            class="w-full min-h-[40px] px-3 py-2 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-700 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-500 italic focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Catatan Tambahan Global -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan Pesanan Tambahan <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" wire:model.live="order_notes" placeholder="Misal: Mohon dikirim besok pagi sebelum jam istirahat ya kak..."
                            class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                </div>

            </div>

            <!-- PREVIEW & TOMBOL KIRIM (KANAN) -->
            <div class="lg:col-span-5 sticky top-6 space-y-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs space-y-4">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">Pratinjau Pesan WhatsApp</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">Format Pesan</span>
                    </div>

                    <!-- Chat Bubble WhatsApp Terformat Rapi -->
                    <div class="p-4 bg-slate-100 dark:bg-slate-950 rounded-xl border border-slate-200/80 dark:border-slate-800 font-mono text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-wrap select-all">
{!! nl2br(e($this->whatsapp_message)) !!}
                    </div>

                    <!-- Target Nomor & Tombol Kirim -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Tujuan Nomor:</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                {{ $seller_phone ? \App\Models\SupplierOrder::formatWhatsAppNumber($seller_phone) : '(Belum diisi)' }}
                            </span>
                        </div>

                        <button type="button" wire:click="submitAndOpenWhatsApp"
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2.5 shadow-md shadow-emerald-600/20 transition-all cursor-pointer disabled:opacity-50">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>Buka & Kirim WhatsApp</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    @else
        <!-- TABEL RIWAYAT PEMESANAN -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
            
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <input type="text" wire:model.live.debounce.300ms="historySearch" placeholder="Cari seller atau nomor..."
                    class="w-full sm:w-64 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:border-blue-500">

                <select wire:model.live="historyStatus"
                    class="w-full sm:w-auto px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:border-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="ordered">Sudah Dipesan</option>
                    <option value="received">Diterima</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            @if (session()->has('history_message'))
                <div class="px-4 py-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-medium border-b border-blue-100 dark:border-blue-900">
                    {{ session('history_message') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Seller</th>
                            <th class="py-3 px-4">Item Dipesan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($historyOrders as $order)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">{{ $order->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $order->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $order->supplier_name }}</div>
                                    <div class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400">+{{ $order->supplier_phone }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="space-y-1">
                                        @foreach($order->items as $it)
                                            <div class="text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block"></span>
                                                <span class="font-semibold">{{ $it->product_name }}</span>
                                                <span class="text-slate-400 font-mono text-[11px]">({{ $it->quantity }} {{ strtoupper($it->unit) }})</span>
                                                @if($it->notes)
                                                    <span class="text-[10px] text-slate-400 italic">[{{ $it->notes }}]</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($order->notes)
                                        <div class="text-[11px] text-slate-400 mt-1.5 italic">
                                            Catatan: "{{ $order->notes }}"
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <select wire:change="updateOrderStatus('{{ $order->id }}', $event.target.value)"
                                        class="text-xs font-semibold rounded-lg px-2.5 py-1 border cursor-pointer
                                            @if($order->status === 'ordered') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800
                                            @elseif($order->status === 'received') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800
                                            @elseif($order->status === 'cancelled') bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-800
                                            @else bg-slate-100 text-slate-700 border-slate-200 @endif">
                                        <option value="ordered" {{ $order->status === 'ordered' ? 'selected' : '' }}>Sudah Dipesan</option>
                                        <option value="received" {{ $order->status === 'received' ? 'selected' : '' }}>Diterima</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <button type="button" wire:click="resendWhatsApp('{{ $order->id }}')"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-emerald-400 rounded-lg text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Kirim Ulang
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Belum ada data riwayat pemesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $historyOrders->links() }}
            </div>

        </div>
    @endif
    @endif

</div>
