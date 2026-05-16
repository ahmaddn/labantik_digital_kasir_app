<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Kasir LabAntik') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body x-data="{
    sidebarOpen: localStorage.getItem('sidebar-open') !== 'false',
    darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebar-open', this.sidebarOpen);
    },
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
}" x-init="if (darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
    class="bg-bone-white dark:bg-dark-soft text-slate-900 dark:text-bone-white antialiased transition-colors duration-300">
    @livewire('global-search')
    <div class="flex h-screen overflow-hidden">
        
        @include('partials.layout.sidebar')

        <!-- Main Content -->
        <main
            class="flex-1 overflow-y-auto relative no-scrollbar bg-bone-white dark:bg-dark-bg transition-all duration-500">
            
            @include('partials.layout.header')

            <div class="w-full px-6 md:px-10 min-h-screen pb-20 transition-all duration-500">
                {{ $slot }}
            </div>

            @include('partials.toasts')
        </main>
    </div>

    @include('partials.global-loading')
    @livewireScripts
</body>

</html>
