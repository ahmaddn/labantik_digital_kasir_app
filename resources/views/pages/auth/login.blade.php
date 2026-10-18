<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Login Petugas | TEFA SMKN 1 Talaga</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

<body x-data="{
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
    class="min-h-screen min-h-[100dvh] font-outfit antialiased selection:bg-blue-600 selection:text-white bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-150">

    <div class="min-h-screen min-h-[100dvh] grid grid-cols-1 lg:grid-cols-12">
        <!-- Sisi Kiri: Panel Institusional TEFA SMKN 1 Talaga (Tampil di Desktop lg:) -->
        <!-- Sisi Kiri: Panel Institusional Minimalis (Hanya Tampil di Desktop lg:) -->
        <div class="hidden lg:flex lg:col-span-5 xl:col-span-5 flex-col justify-between p-12 xl:p-16 bg-blue-700 text-white">
            <!-- Header Institusi -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white text-blue-700 font-bold flex items-center justify-center text-sm shadow-xs">
                    LA
                </div>
                <div>
                    <span class="block font-bold text-sm tracking-tight text-white leading-tight">SMK Negeri 1 Talaga</span>
                    <span class="block text-xs text-blue-100">Teaching Factory</span>
                </div>
            </div>

            <!-- Konten Tengah: Ringkas & Lapang -->
            <div class="my-auto max-w-sm">
                <h2 class="text-3xl xl:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    LabAntik Kasir
                </h2>
                <p class="mt-3 text-sm text-blue-100/90 leading-relaxed font-normal">
                    Sistem pencatatan transaksi kasir POS dan pembukuan unit usaha sekolah terpadu.
                </p>
            </div>

            <!-- Footer Sisi Kiri -->
            <div class="text-xs text-blue-200/80">
                <span>Unit Produksi &amp; Bisnis Sekolah</span>
            </div>
        </div>

        <!-- Sisi Kanan: Formulir Masuk Petugas -->
        <div class="lg:col-span-7 xl:col-span-7 flex flex-col justify-between p-6 sm:p-10 lg:p-14 bg-white dark:bg-slate-900 transition-colors">
            <!-- Bar Atas: Branding Mobile & Pengatur Tema -->
            <div class="w-full max-w-md mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2.5 lg:invisible">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center text-xs">
                        LA
                    </div>
                    <div>
                        <span class="block font-bold text-xs text-slate-900 dark:text-white leading-tight">LabAntik Kasir</span>
                        <span class="block text-[10px] text-slate-500 dark:text-slate-400">SMKN 1 Talaga</span>
                    </div>
                </div>

                <button @click="toggleTheme()" type="button"
                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-medium cursor-pointer transition-colors"
                    title="Ganti Mode Tampilan">
                    <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>
            </div>

            <!-- Kontainer Form -->
            <div class="w-full max-w-md mx-auto my-auto py-8">
                <div class="mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Masuk ke Sistem
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Masukkan email dan kata sandi akun petugas yang telah terdaftar di sistem sekolah.
                    </p>
                </div>

                <!-- Notifikasi Error Sesi -->
                @if (session('error'))
                    <div class="mb-5 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-4"
                    x-data="{ loading: false, showPassword: false }"
                    x-on:submit="setTimeout(() => loading = true, 50)">
                    @csrf

                    <!-- Input Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Email Akun
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@smkn1talaga.sch.id" autocomplete="email"
                            class="w-full px-3.5 py-2.5 sm:py-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:border-blue-600 focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-blue-600 outline-none transition-colors">
                        @error('email')
                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 font-medium">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                placeholder="Masukkan kata sandi akun" autocomplete="current-password"
                                class="w-full pl-3.5 pr-11 py-2.5 sm:py-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:border-blue-600 focus:bg-white dark:focus:bg-slate-900 focus:ring-1 focus:ring-blue-600 outline-none transition-colors">
                            
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer transition-colors"
                                title="Tampilkan / Sembunyikan Kata Sandi">
                                <!-- Eye Open (Saat kata sandi disembunyikan) -->
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <!-- Eye Slash (Saat kata sandi terlihat) -->
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 font-medium">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded text-blue-600 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 focus:ring-blue-500">
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                Ingat sesi saya di komputer ini
                            </span>
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit" :disabled="loading"
                        class="w-full mt-2 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                        <span x-show="!loading">Masuk ke Sistem</span>
                        <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memeriksa kredensial...</span>
                        </span>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Lupa kata sandi atau kendala akses? Hubungi Administrator Jurusan.
                    </p>
                </div>
            </div>

            <!-- Footer Sisi Kanan -->
            <footer class="w-full max-w-md mx-auto text-center pt-4">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    Teaching Factory &copy; {{ date('Y') }} SMKN 1 Talaga
                </p>
            </footer>
        </div>
    </div>

</body>

</html>
