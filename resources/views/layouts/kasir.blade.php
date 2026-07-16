<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Mode Kasir | LabAntik</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @php
            $activeJurusanId = session('active_jurusan_id');
            $themeSettings = null;
            if ($activeJurusanId) {
                $jurusanModel = \App\Models\Jurusan::find($activeJurusanId);
                if ($jurusanModel && $jurusanModel->theme_settings) {
                    $themeSettings = $jurusanModel->theme_settings;
                }
            }
            $primaryColor = $themeSettings['primary_color'] ?? '#2563EB'; 
            $secondaryColor = $themeSettings['secondary_color'] ?? '#EF4444'; 
            $fontFamily = $themeSettings['font_family'] ?? 'Outfit'; 
            $themeStyle = $themeSettings['theme_style'] ?? 'classic-premium'; 
        @endphp

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        @if($fontFamily !== 'Outfit')
            <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontFamily) }}:wght@300;400;550;600;700;800;900&display=swap" rel="stylesheet">
        @endif

        <style>
            :root {
                --color-primary-blue: {{ $primaryColor }} !important;
                --color-primary-blue-dark: {{ $primaryColor }}CC !important;
                --color-primary-red: {{ $secondaryColor }} !important;
                --color-primary-red-dark: {{ $secondaryColor }}CC !important;
                --font-outfit: '{{ $fontFamily }}', sans-serif !important;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <script>
            // Synchronize theme with Admin preference
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    <body class="h-full overflow-hidden antialiased bg-[#e0e0e0] dark:bg-black theme-{{ $themeStyle }}">
        <div class="h-full w-full">
            {{ $slot }}
        </div>
        @include('partials.toasts')
        @include('partials.global-loading')
        @livewireScripts
    </body>
</html>
