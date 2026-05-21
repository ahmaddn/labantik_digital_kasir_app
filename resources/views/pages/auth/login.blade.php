<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | LabAntik Kasir</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
        </style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="h-full bg-bone-white dark:bg-dark-soft flex items-center justify-center p-6 font-sans">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-10">
                <img src="{{ asset('favicon.png') }}" alt="LabAntik Logo" class="inline-flex w-20 h-20 mb-4 drop-shadow-2xl">
                <h1 class="text-3xl font-black italic uppercase tracking-tighter text-primary-blue dark:text-primary-yellow">LabAntik</h1>
                <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mt-1">Sistem Kasir Digital Jurusan</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl shadow-blue-900/10 p-10 border border-gray-100 dark:border-gray-700">
                <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-2">Selamat Datang!</h2>
                <p class="text-gray-400 text-sm mb-8 font-medium">Silakan masuk untuk mulai mencatat transaksi.</p>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-6" x-data="{ loading: false }" x-on:submit="setTimeout(() => loading = true, 50)">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Email Admin</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@gmail.com" class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all">
                        </div>
                        @error('email') <p class="text-xs text-primary-red mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-primary-blue dark:text-white transition-all">
                        </div>
                        @error('password') <p class="text-xs text-primary-red mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded-lg text-primary-blue focus:ring-primary-blue border-gray-200 dark:bg-gray-900">
                            <span class="ml-2 text-xs font-bold text-gray-400 group-hover:text-gray-600 transition">Ingat Saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-5 bg-primary-blue hover:bg-blue-900 text-primary-yellow rounded-2xl font-black text-lg shadow-xl shadow-blue-900/20 active:scale-95 transition-all uppercase italic tracking-wider disabled:opacity-50 disabled:cursor-not-allowed">
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
            
            <p class="text-center mt-8 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                Developed for LabAntik Jurusan &copy; 2026
            </p>
        </div>
    </body>
</html>
