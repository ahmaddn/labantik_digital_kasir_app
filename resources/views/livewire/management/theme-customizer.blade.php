<div class="p-6">
    <!-- Google Fonts Loader for Customizer -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;600;800&family=Syne:wght@400;600;800&display=swap" rel="stylesheet">

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-blue-light">Kustomisasi Tampilan</h1>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-[0.2em] italic">Desain Identitas Visual Unit TEFA</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
        <!-- Settings Panel -->
        <div class="xl:col-span-5 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-10 shadow-2xl shadow-blue-900/5 border border-gray-100 dark:border-gray-700">
                
                <!-- Jurusan Selection (Locked if not Superadmin) -->
                <div class="mb-8">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Unit TEFA yang Dikustomisasi</label>
                    @if(session('active_role_name') === 'superadmin' || session('active_role_name') === 'pengelola_jurusan')
                        <select wire:model.live="selectedJurusanId" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                            @foreach($jurusans as $jur)
                                <option value="{{ $jur->id }}">{{ is_null($jur->parent_id) ? 'Jurusan Utama: ' : 'Sub-Unit: ' }}TEFA {{ $jur->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <div class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-900/50 rounded-2xl font-black text-sm text-gray-600 dark:text-gray-400 uppercase tracking-tight italic">
                            TEFA {{ session('active_jurusan_name') }} (Terkunci)
                        </div>
                    @endif
                </div>

                <div class="h-px bg-gray-100 dark:bg-gray-700 my-8"></div>

                <form wire:submit.prevent="saveTheme" class="space-y-8">
                    
                    <!-- TEFA Settings Section -->
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2 italic">Identitas & Dokumen TEFA</label>
                        
                        <!-- TEFA Name -->
                        <div>
                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-2">Nama TEFA / Unit Usaha</label>
                            <input type="text" wire:model="tefaName" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white uppercase tracking-tight">
                        </div>

                        <!-- Document Prefix -->
                        <div>
                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-2">Prefiks Penomoran Invoice Supplier</label>
                            <input type="text" wire:model="docPrefixInvoice" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white uppercase tracking-wider">
                        </div>

                        <!-- Transaction Prefix -->
                        <div>
                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-2">Prefiks Penomoran Nota Kasir (Transaksi)</label>
                            <input type="text" wire:model="docPrefixTransaction" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-bold text-sm text-gray-800 dark:text-white uppercase tracking-wider">
                        </div>

                        <!-- TEFA Logo -->
                        <div>
                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-2">Logo TEFA</label>
                            <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl">
                                <div class="w-16 h-16 bg-white dark:bg-gray-850 rounded-xl border border-gray-200 dark:border-gray-800 flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($tefaLogo)
                                        <img src="{{ $tefaLogo->temporaryUrl() }}" class="w-full h-full object-contain p-1">
                                    @elseif ($existingTefaLogo)
                                        <img src="{{ asset('storage/' . $existingTefaLogo) }}" class="w-full h-full object-contain p-1">
                                    @else
                                        <span class="text-[9px] text-gray-400 font-bold uppercase text-center p-1">No Logo</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" wire:model="tefaLogo" class="hidden" id="tefa-logo-input">
                                    <label for="tefa-logo-input" class="inline-block px-4 py-2 bg-primary-blue text-white rounded-xl text-[10px] font-black uppercase tracking-wider cursor-pointer hover:bg-primary-blue-dark transition-all">
                                        Upload Logo
                                    </label>
                                    <div wire:loading wire:target="tefaLogo" class="text-[9px] text-primary-blue font-bold uppercase mt-1 block">Uploading...</div>
                                    @error('tefaLogo') <span class="text-xs text-primary-red block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-gray-700 my-6"></div>
                    
                    <!-- Color Customization -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2 italic">Skema Warna</label>
                        
                        <!-- Presets Grid -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            @foreach($colorPresets as $preset)
                                <button type="button" 
                                    wire:click="applyPreset('{{ $preset['primary'] }}', '{{ $preset['secondary'] }}')"
                                    class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800 flex items-center hover:border-gray-300 dark:hover:border-gray-700 transition-all text-left group">
                                    <div class="flex gap-1.5 mr-3">
                                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $preset['primary'] }};"></div>
                                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $preset['secondary'] }};"></div>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-white truncate">
                                        {{ $preset['name'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Manual Color Picker -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-2">Warna Utama</label>
                                <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-2xl px-4 py-2">
                                    <input type="color" wire:model.live="primaryColor" class="w-8 h-8 rounded-lg border-none cursor-pointer bg-transparent">
                                    <input type="text" wire:model.live="primaryColor" class="w-full bg-transparent border-none p-0 ml-3 text-xs font-black text-gray-700 dark:text-white uppercase tracking-wider">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-2">Warna Sekunder</label>
                                <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-2xl px-4 py-2">
                                    <input type="color" wire:model.live="secondaryColor" class="w-8 h-8 rounded-lg border-none cursor-pointer bg-transparent">
                                    <input type="text" wire:model.live="secondaryColor" class="w-full bg-transparent border-none p-0 ml-3 text-xs font-black text-gray-700 dark:text-white uppercase tracking-wider">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Font Customization -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2 italic">Tipografi (Font)</label>
                        <select wire:model.live="fontFamily" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-4 focus:ring-primary-blue/10 font-black text-sm text-gray-800 dark:text-white">
                            @foreach($fontPresets as $font)
                                <option value="{{ $font['value'] }}">{{ $font['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Visual Template Style -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2 italic">
                            {{ $isSubUnit ? 'Tipe Bisnis Sub-Unit (Tema Khusus)' : 'Gaya Visual Template' }}
                        </label>
                        <div class="space-y-3">
                            @foreach($availableStyles as $style)
                                <label class="flex p-5 bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] border-2 cursor-pointer transition-all {{ $themeStyle === $style['value'] ? 'border-primary-blue bg-blue-50/10 dark:bg-blue-900/5' : 'border-transparent hover:border-gray-200 dark:hover:border-gray-800' }}">
                                    <input type="radio" wire:model.live="themeStyle" value="{{ $style['value'] }}" class="sr-only">
                                    <div class="flex items-start">
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-700 flex items-center justify-center mr-4 mt-1 shrink-0">
                                            @if($themeStyle === $style['value'])
                                                <div class="w-2.5 h-2.5 rounded-full bg-primary-blue"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="block text-xs font-black uppercase tracking-wider text-gray-800 dark:text-white">{{ $style['name'] }}</span>
                                            <span class="block text-[10px] font-medium text-gray-400 mt-1 leading-normal">{{ $style['desc'] }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-5 bg-primary-blue text-white rounded-[2rem] shadow-2xl shadow-blue-900/20 font-black italic uppercase tracking-wider transform hover:-translate-y-1 transition-all">
                            Simpan & Terapkan Tema
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Live Preview Panel -->
        <div class="xl:col-span-7">
            <div class="sticky top-10 space-y-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-8 w-2 bg-primary-blue rounded-full"></div>
                    <h2 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 dark:text-white">Live Preview</h2>
                </div>

                <!-- Preview Area Wrapper -->
                <div class="p-8 md:p-12 bg-white dark:bg-gray-950 rounded-[4rem] border border-gray-100 dark:border-gray-800 shadow-2xl relative overflow-hidden min-h-[500px] flex flex-col justify-between" 
                    style="font-family: '{{ $fontFamily }}', sans-serif;">
                    
                    <!-- Top Bar Preview -->
                    <div class="flex items-center justify-between mb-10 pb-6 border-b border-gray-100 dark:border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black italic shadow-lg animate-pulse"
                                style="background-color: {{ $primaryColor }};">
                                TA
                            </div>
                            <div>
                                <span class="block text-xs font-black text-gray-950 dark:text-white uppercase tracking-tight italic">{{ $tefaName }}</span>
                                <span class="block text-[8px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-0.5">Sesi Penjualan Aktif</span>
                            </div>
                        </div>

                        <!-- Status Button -->
                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest"
                            style="background-color: {{ $primaryColor }}15; border-color: {{ $primaryColor }}30; color: {{ $primaryColor }};">
                            Online
                        </span>
                    </div>

                    <!-- Middle Card Preview -->
                    <div class="flex-1 flex flex-col justify-center">
                        @if($themeStyle === 'glassmorphism')
                            <!-- Glassmorphism Card -->
                            <div class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-md rounded-[3rem] p-8 border border-white/40 dark:border-white/10 shadow-2xl relative overflow-hidden">
                                <div class="absolute -right-8 -bottom-8 opacity-10">
                                    <svg class="w-48 h-48 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                </div>
                                <h3 class="text-gray-500 dark:text-white/60 text-[10px] font-black uppercase tracking-widest mb-2">Penjualan Hari Ini</h3>
                                <p class="text-4xl font-black text-gray-950 dark:text-white italic tracking-tighter mb-4">Rp2.450.000</p>
                                <span class="px-2.5 py-1 rounded-md text-[8px] font-black bg-white/20 dark:bg-white/10 text-gray-800 dark:text-white uppercase tracking-widest">
                                    +12.4% vs Kemarin
                                </span>
                            </div>
                        @elseif($themeStyle === 'neon-cyberpunk')
                            <!-- Neon Cyberpunk Card -->
                            <div class="bg-gray-950 dark:bg-black rounded-[3rem] p-8 border-2 shadow-[0_0_20px_rgba(0,0,0,0.05)] dark:shadow-[0_0_20px_rgba(255,255,255,0.05)] relative overflow-hidden"
                                style="border-color: {{ $primaryColor }}; box-shadow: 0 0 20px {{ $primaryColor }}20;">
                                <div class="absolute right-6 top-6 w-3 h-3 rounded-full animate-ping" style="background-color: {{ $secondaryColor }};"></div>
                                <h3 class="text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">System Performance</h3>
                                <p class="text-4xl font-black italic tracking-tighter mb-4" style="color: {{ $primaryColor }};">98.4% AUDIT</p>
                                <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase tracking-widest"
                                    style="background-color: {{ $secondaryColor }}20; color: {{ $secondaryColor }};">
                                    CRITICAL STOCKS OK
                                </span>
                            </div>
                        @elseif($themeStyle === 'restaurant-aesthetic')
                            <!-- Restoran & Warung Culinary Card -->
                            <div class="bg-[#fffaf0] dark:bg-stone-900 rounded-[2.5rem] p-8 border-2 border-dashed border-amber-500/30 dark:border-amber-500/10 shadow-xl relative overflow-hidden text-amber-950 dark:text-amber-100">
                                <h3 class="text-amber-800/80 dark:text-amber-400/60 text-[10px] font-black uppercase tracking-widest mb-2">Total Penjualan</h3>
                                <p class="text-4xl font-black text-amber-950 dark:text-white italic tracking-tighter mb-4">Rp1.850.000</p>
                                <span class="px-3 py-1 rounded-full text-[8px] font-black bg-amber-500 text-white uppercase tracking-widest">
                                    WARUNG STYLE ACTIVE
                                </span>
                            </div>
                        @elseif($themeStyle === 'retail-aesthetic')
                            <!-- Retail Minimarket Card -->
                            <div class="bg-white dark:bg-slate-900 rounded-lg p-8 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden text-slate-800 dark:text-slate-200">
                                <h3 class="text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Kas Masuk</h3>
                                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter mb-4">Rp720.000</p>
                                <span class="px-2.5 py-1 rounded-sm text-[8px] font-black bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 uppercase tracking-widest">
                                    GRID SYSTEM ACTIVE
                                </span>
                            </div>
                        @elseif($themeStyle === 'bank-aesthetic')
                            <!-- Bank Mini Fintech Card -->
                            <div class="bg-white dark:bg-slate-950 rounded-2xl p-8 border border-sky-100 dark:border-sky-950/30 shadow-md relative overflow-hidden text-sky-950 dark:text-sky-100">
                                <h3 class="text-sky-600 dark:text-sky-400 text-[10px] font-black uppercase tracking-widest mb-2">Total Simpanan</h3>
                                <p class="text-4xl font-black text-sky-950 dark:text-white italic tracking-tighter mb-4">Rp5.450.000</p>
                                <span class="px-2.5 py-1 rounded-lg text-[8px] font-black text-white uppercase tracking-widest"
                                    style="background-color: {{ $primaryColor }};">
                                    FINTECH STYLE ACTIVE
                                </span>
                            </div>
                        @else
                            <!-- Classic Premium Card -->
                            <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-8 border border-gray-100 dark:border-white/5 shadow-2xl relative overflow-hidden">
                                <h3 class="text-gray-500 dark:text-white/60 text-[10px] font-black uppercase tracking-widest mb-2">Laba Bersih</h3>
                                <p class="text-4xl font-black text-gray-950 dark:text-white italic tracking-tighter mb-4">Rp840.500</p>
                                <span class="px-2.5 py-1 rounded-md text-[8px] font-black text-white uppercase tracking-widest"
                                    style="background-color: {{ $primaryColor }};">
                                    Target Tercapai
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Buttons Preview -->
                    <div class="mt-10 pt-6 border-t border-gray-100 dark:border-white/5 flex gap-4">
                        <button type="button" class="flex-1 py-4 text-xs font-black uppercase tracking-wider italic text-white transition-all
                            {{ $themeStyle === 'restaurant-aesthetic' ? 'rounded-full' : ($themeStyle === 'retail-aesthetic' ? 'rounded-md' : ($themeStyle === 'bank-aesthetic' ? 'rounded-lg' : ($themeStyle === 'neon-cyberpunk' ? 'rounded-none border-2 border-[#ff007f]' : 'rounded-2xl'))) }}"
                            style="background-color: {{ $primaryColor }};">
                            Lanjutkan Transaksi
                        </button>
                        <button type="button" class="px-6 py-4 text-xs font-black uppercase tracking-wider italic text-gray-700 dark:text-white transition-all bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                            {{ $themeStyle === 'restaurant-aesthetic' ? 'rounded-full' : ($themeStyle === 'retail-aesthetic' ? 'rounded-md' : ($themeStyle === 'bank-aesthetic' ? 'rounded-lg' : ($themeStyle === 'neon-cyberpunk' ? 'rounded-none border-2 border-cyan-400' : 'rounded-2xl'))) }}">
                            Batal
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
