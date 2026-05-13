<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | LabAntik POS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .text-outline {
            -webkit-text-stroke: 2px rgba(226, 232, 240, 0.3);
            color: transparent;
        }
        .dark .text-outline {
            -webkit-text-stroke: 2px rgba(30, 41, 59, 0.3);
            color: transparent;
        }
    </style>
</head>
<body class="bg-bone-white dark:bg-dark-bg min-h-screen flex items-center justify-center p-4 relative overflow-hidden transition-colors duration-500">
    
    @php
        $errorCode = trim($__env->yieldContent('code'));
        
        $brandColor = 'primary-blue'; // Default for 404
        $accentColor = '#3b82f6'; // Blue
        
        if (in_array($errorCode, ['401', '403', '419'])) {
            $brandColor = 'amber-500'; // Warning
            $accentColor = '#f59e0b';
        } elseif (str_starts_with($errorCode, '5') || $errorCode == '405') {
            $brandColor = 'primary-red'; // Danger
            $accentColor = '#ef4444';
        }
    @endphp

    <!-- Subtle Background Gradient -->
    <div class="absolute inset-0 pointer-events-none -z-10">
        <div class="absolute inset-0 bg-gradient-to-tr from-gray-100 to-white dark:from-[#05070a] dark:to-[#0a0d14]"></div>
        <div class="absolute top-0 right-0 w-[50vw] h-[50vw] rounded-full blur-[150px] opacity-20" style="background-color: {{ $accentColor }}"></div>
    </div>

    <div class="max-w-xl w-full relative z-10 animate-in fade-in zoom-in-95 duration-700">
        <!-- Brand Identity -->
        <div class="flex flex-col items-center mb-10">
            <div class="w-20 h-20 bg-white dark:bg-gray-900 rounded-[1.8rem] p-4 shadow-2xl border-4 border-white dark:border-gray-800 rotate-3 hover:rotate-0 transition-transform duration-500">
                <img src="{{ asset('favicon.png') }}" class="w-full h-full object-contain">
            </div>
            <div class="mt-4 text-center">
                <h1 class="text-xl font-black tracking-tighter leading-none text-primary-blue dark:text-primary-blue-light uppercase italic">LabAntik</h1>
                <span class="text-[8px] font-black text-primary-red tracking-[0.3em] uppercase italic">Digital System</span>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-3xl rounded-[3rem] p-10 md:p-14 border border-white/20 dark:border-gray-800/50 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] relative overflow-hidden text-center">
            
            <!-- Background Error Code -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.15] dark:opacity-[0.2]">
                <span class="text-[12rem] md:text-[16rem] font-black italic text-outline select-none leading-none">@yield('code')</span>
            </div>

            <!-- Content -->
            <div class="relative z-10">
                <div class="inline-block px-5 py-2 bg-{{ $brandColor }}/10 rounded-full mb-6 border border-{{ $brandColor }}/20">
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-{{ $brandColor }}">ERROR @yield('code') &bull; System Notification</p>
                </div>

                <h2 class="text-4xl md:text-5xl font-black italic uppercase tracking-tighter text-gray-900 dark:text-white leading-none mb-6">
                    @yield('title')
                </h2>

                <p class="text-sm md:text-base font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest italic leading-relaxed max-w-sm mx-auto">
                    @yield('message')
                </p>

                <div class="mt-12 flex flex-col gap-4">
                    <a href="{{ url('/') }}" class="w-full py-5 bg-{{ $brandColor }} text-white rounded-[2rem] shadow-2xl shadow-{{ $brandColor }}/40 font-black italic uppercase tracking-[0.3em] text-xs hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Ke Dashboard Utama
                    </a>
                    <button onclick="window.history.back()" class="w-full py-5 bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-800 font-black italic uppercase tracking-[0.3em] text-xs hover:text-{{ $brandColor }} transition-all">
                        Kembali
                    </button>
                </div>
            </div>
        </div>

        <!-- System Footer -->
        <div class="mt-10 flex flex-col items-center gap-2 opacity-30 text-center">
            <p class="text-[8px] font-black uppercase tracking-[0.5em]">LabAntik Protocol &bull; Error Protection</p>
            <p class="text-[7px] font-black uppercase tracking-[0.2em] italic">Protecting your data with digital integrity</p>
        </div>
    </div>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</body>
</html>
