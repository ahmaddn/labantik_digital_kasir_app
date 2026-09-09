<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    class="min-h-screen font-outfit antialiased selection:bg-blue-600 selection:text-white transition-colors duration-300 bg-white dark:bg-slate-900 text-slate-900 dark:text-white"
>
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        <!-- Kolom KIRI: Panel Branding TEFA -->
        <div 
            class="relative flex flex-col items-center justify-center p-8 sm:p-12 lg:p-16 text-center text-white overflow-hidden"
            style="background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);"
        >
            <!-- Decorative Subtle Ambient Bubbles -->
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl opacity-30 pointer-events-none" style="background-color: #93c5fd;"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl opacity-30 pointer-events-none" style="background-color: #1e40af;"></div>

            <div class="relative z-10 max-w-md mx-auto flex flex-col items-center justify-center">
                <!-- Subtitle Badge -->
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase mb-6 shadow-md" style="background-color: rgba(251, 191, 36, 0.2); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.4);">
                    RPL X LABANTIK
                </div>

                <!-- Title -->
                <h1 class="text-4xl sm:text-5xl font-black tracking-tight mb-4 text-white drop-shadow-md uppercase italic">
                    SUPERAPPS TEFA
                </h1>

                <!-- Brief Description -->
                <p class="text-sm sm:text-base font-medium leading-relaxed max-w-sm" style="color: rgba(239, 246, 255, 0.95);">
                    Sistem Pengelolaan Kasir, Transaksi, &amp; Manajemen Keuangan Terpadu SMKN 1 Talaga.
                </p>
            </div>
        </div>

        <!-- Kolom KANAN: Form Login -->
        <div class="relative flex flex-col justify-between items-center p-6 sm:p-12 lg:p-16 min-h-screen bg-white dark:bg-slate-900 transition-colors duration-300 w-full">
            
            <!-- Dark / Light Mode Toggle Button (Pojok Kanan Atas) -->
            <div class="absolute top-6 right-6 z-20">
                <button 
                    @click="toggleTheme()" 
                    type="button"
                    title="Beralih Mode Gelap/Terang"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400 border-none shadow-sm hover:shadow-md hover:scale-105 active:scale-95 transition-all font-bold text-xs cursor-pointer"
                >
                    <!-- Sun Icon (Show in dark mode) -->
                    <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon Icon (Show in light mode) -->
                    <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>
            </div>

            <!-- Form Container (Perfectly Centered Vertically & Horizontally) -->
            <div class="w-full max-w-md my-auto py-8 flex flex-col justify-center">
                
                <!-- Heading -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border-none shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Selamat Datang!
                        </h2>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium pl-1">
                        Login Untuk Melanjutkan ke Dashboard Admin
                    </p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login.store') }}" class="space-y-5" x-data="{ loading: false }" x-on:submit="setTimeout(() => loading = true, 50)">
                    @csrf

                    <!-- Email Admin Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 ml-0.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 dark:text-slate-500 pointer-events-none flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                placeholder="admin@gmail.com" 
                                class="w-full pl-12 pr-4 py-4 bg-slate-100 dark:bg-slate-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all font-medium text-sm"
                                style="border: none; outline: none;"
                            >
                        </div>
                        @error('email')
                            <p class="text-xs text-rose-500 mt-2 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input dengan Toggle Show/Hide -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 ml-0.5">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div x-data="{ showPassword: false }" class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 dark:text-slate-500 pointer-events-none flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                required 
                                placeholder="••••••••" 
                                class="w-full pl-12 pr-12 py-4 bg-slate-100 dark:bg-slate-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all font-medium text-sm"
                                style="border: none; outline: none;"
                            >
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors focus:outline-none cursor-pointer"
                                tabindex="-1"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.022 10.022 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-500 mt-2 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Checkbox Ingat Saya -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center group cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-none bg-slate-100 dark:bg-slate-800 transition"
                            >
                            <span class="ml-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">
                                Ingat Saya
                            </span>
                        </label>
                    </div>

                    <!-- Tombol Masuk Sekarang -->
                    <button 
                        type="submit" 
                        class="w-full py-4 rounded-2xl font-black text-lg shadow-lg active:scale-[0.98] transition-all uppercase italic tracking-wider disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
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

            <!-- Footer -->
            <footer class="w-full text-center pb-2">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                    Developed for Labantik Jurusan &copy; 2026
                </p>
            </footer>

        </div>
    </div>
</body>
</html>
