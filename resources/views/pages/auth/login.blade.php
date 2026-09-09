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
    class="min-h-screen font-outfit antialiased selection:bg-blue-500 selection:text-white transition-colors duration-300"
>
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        <!-- Kolom KIRI: Branding Panel -->
        <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-900 text-white flex flex-col items-center justify-center p-8 lg:p-16 overflow-hidden">
            <!-- Background Decorative Glows -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-sky-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-lg text-center flex flex-col items-center">
                <!-- Subtitle Badge -->
                <span class="inline-flex items-center px-4 py-1.5 bg-amber-400/20 text-amber-300 border border-amber-400/30 rounded-full text-xs font-black tracking-widest uppercase mb-4 shadow-sm backdrop-blur-sm">
                    RPL X LABANTIK
                </span>

                <!-- Title -->
                <h1 class="text-4xl lg:text-5xl font-black tracking-tight text-white mb-4 drop-shadow-sm">
                    SUPERAPPS TEFA
                </h1>

                <!-- Brief Description -->
                <p class="text-blue-100/90 text-sm lg:text-base font-medium leading-relaxed max-w-md">
                    Sistem Pengelolaan Kasir, Transaksi, &amp; Manajemen Keuangan Terpadu SMKN 1 Talaga.
                </p>
            </div>
        </div>

        <!-- Kolom KANAN: Login Form -->
        <div class="relative bg-slate-50 dark:bg-slate-900 transition-colors duration-300 flex flex-col justify-between p-6 sm:p-10 lg:p-16 min-h-screen">
            
            <!-- Dark / Light Mode Toggle Button (Pojok Kanan Atas) -->
            <div class="absolute top-6 right-6 z-20">
                <button 
                    @click="toggleTheme()" 
                    type="button"
                    title="Beralih Mode Gelap/Terang"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-amber-400 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:scale-105 active:scale-95 transition-all font-semibold text-xs cursor-pointer"
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

            <!-- Main Form Card Container (Centered Vertically) -->
            <div class="w-full max-w-md mx-auto my-auto py-8">
                
                <!-- Heading Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">
                        Selamat Datang!
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-medium transition-colors">
                        Silakan masuk untuk mulai mencatat transaksi.
                    </p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login.store') }}" class="space-y-6" x-data="{ loading: false }" x-on:submit="setTimeout(() => loading = true, 50)">
                    @csrf

                    <!-- Email Admin Input -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1 transition-colors">
                            Email Admin
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 dark:text-slate-500 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                placeholder="admin@gmail.com" 
                                class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 transition-all font-medium text-sm shadow-sm"
                            >
                        </div>
                        @error('email')
                            <p class="text-xs text-rose-500 mt-2 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1 transition-colors">
                            Password
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 dark:text-slate-500 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input 
                                type="password" 
                                name="password" 
                                required 
                                placeholder="••••••••" 
                                class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 transition-all font-medium text-sm shadow-sm"
                            >
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
                                class="w-4 h-4 rounded-md text-blue-600 focus:ring-blue-600 border-slate-300 dark:border-slate-700 dark:bg-slate-800 transition"
                            >
                            <span class="ml-2.5 text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors">
                                Ingat Saya
                            </span>
                        </label>
                    </div>

                    <!-- Tombol Submit "Masuk Sekarang" -->
                    <button 
                        type="submit" 
                        class="w-full py-4 bg-[#2563eb] hover:bg-blue-700 text-[#fbbf24] dark:text-amber-300 rounded-xl font-black text-lg shadow-lg shadow-blue-600/25 active:scale-[0.98] transition-all uppercase italic tracking-wider disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
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
            <footer class="mt-8 text-center">
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors">
                    Developed for Labantik Jurusan &copy; 2026
                </p>
            </footer>

        </div>
    </div>
</body>
</html>
