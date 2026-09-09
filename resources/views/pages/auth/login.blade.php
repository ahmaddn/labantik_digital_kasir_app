<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Login | Superapps TEFA SMKN 1 Talaga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
        input:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body 
    x-data="{
        darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }
        }
    }"
    class="h-screen h-[100dvh] overflow-hidden font-outfit antialiased selection:bg-blue-600 selection:text-white transition-colors duration-300 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white"
>
    <div class="h-full min-h-full grid grid-cols-1 lg:grid-cols-2 overflow-hidden">
        
        <!-- Kolom KIRI: Branding Panel TEFA (DISEMBUNYIKAN DI MOBILE: hidden lg:flex) -->
        <div 
            class="hidden lg:flex relative flex-col items-center justify-center p-12 lg:p-16 text-center text-white overflow-hidden z-10"
            style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1d4ed8 100%);"
        >
            <!-- Ambient Glow Effects -->
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl opacity-30 pointer-events-none" style="background-color: #60a5fa;"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl opacity-30 pointer-events-none" style="background-color: #3b82f6;"></div>

            <div class="relative z-10 max-w-md mx-auto flex flex-col items-center justify-center">
                <!-- Subtitle Badge -->
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase mb-6 shadow-sm" style="background-color: rgba(251, 191, 36, 0.2); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.4);">
                    RPL X LABANTIK
                </div>

                <!-- Title -->
                <h1 class="text-4xl lg:text-5xl font-black tracking-tight mb-4 text-white drop-shadow-md uppercase italic">
                    SUPERAPPS TEFA
                </h1>

                <!-- Brief Description -->
                <p class="text-sm lg:text-base font-medium leading-relaxed max-w-sm" style="color: rgba(239, 246, 255, 0.95);">
                    Sistem Pengelolaan Kasir, Transaksi, &amp; Manajemen Keuangan Terpadu SMKN 1 Talaga.
                </p>
            </div>
        </div>

        <!-- Kolom KANAN: Form Login (Presisi Ditengah Vertikal & Horizontal) -->
        <div class="relative h-full flex flex-col items-center justify-center p-6 sm:p-10 lg:p-16 bg-slate-50 dark:bg-slate-900 transition-colors duration-300 overflow-hidden">
            
            <!-- Dark / Light Mode Toggle Button (Pojok Kanan Atas) -->
            <div class="absolute top-4 sm:top-6 right-4 sm:right-6 z-20">
                <button 
                    @click="toggleTheme()" 
                    type="button"
                    title="Beralih Mode Gelap/Terang"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-amber-400 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:scale-105 active:scale-95 transition-all font-bold text-xs cursor-pointer"
                >
                    <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>
            </div>

            <!-- Form Wrapper Utama: Tepat di titik tengah matematis layar (Vertical & Horizontal Center) -->
            <div class="w-full max-w-sm sm:max-w-md flex flex-col justify-center">
                <!-- Heading -->
                <div class="mb-6 sm:mb-8 text-left">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Selamat Datang!</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1.5 font-medium">Silakan masuk untuk mulai mencatat transaksi.</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login.store') }}" class="space-y-4 sm:space-y-6" x-data="{ loading: false }" x-on:submit="setTimeout(() => loading = true, 50)">
                    @csrf

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1">
                            Email Admin
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 dark:text-slate-500 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                                </svg>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                placeholder="admin@gmail.com" 
                                class="w-full pl-12 pr-4 py-3.5 sm:py-4 bg-gray-100 dark:bg-slate-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 font-medium text-sm transition-all"
                                style="border: none; outline: none;"
                            >
                        </div>
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1">
                            Password
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 dark:text-slate-500 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </span>
                            <input 
                                type="password" 
                                name="password" 
                                required 
                                placeholder="••••••••" 
                                class="w-full pl-12 pr-4 py-3.5 sm:py-4 bg-gray-100 dark:bg-slate-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 font-medium text-sm transition-all"
                                style="border: none; outline: none;"
                            >
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center group cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="w-4 h-4 rounded text-blue-600 border-none bg-gray-200 dark:bg-slate-800 transition"
                            >
                            <span class="ml-2.5 text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors">
                                Ingat Saya
                            </span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-4 sm:py-5 rounded-2xl font-black text-base sm:text-lg shadow-xl active:scale-95 transition-all uppercase italic tracking-wider disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        style="background-color: #2563eb; color: #fbbf24;"
                    >
                        <span x-show="!loading">Masuk Sekarang</span>
                        <span x-show="loading" x-cloak class="flex items-center justify-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Footer Posisikan Secara Absolut di Bawah Layar -->
            <footer class="absolute bottom-4 left-0 right-0 text-center pointer-events-none z-10">
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest pointer-events-auto">
                    Developed for Labantik Jurusan &copy; 2026
                </p>
            </footer>

        </div>
    </div>
</body>
</html>
