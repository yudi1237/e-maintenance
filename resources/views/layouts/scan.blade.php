<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">

        <title>{{ $title ?? config('app.name', 'e-Maintenance') }}</title>

        <script>
            (function () {
                try {
                    var theme = localStorage.getItem('em-theme') || 'dark';
                    if (theme === 'dark') document.documentElement.classList.add('dark');
                    document.documentElement.style.colorScheme = theme === 'dark' ? 'dark' : 'light';
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important}</style>
    </head>
    <body class="font-sans antialiased em-body" style="font-family: 'DM Sans', Figtree, ui-sans-serif, system-ui, sans-serif;">
        <div class="min-h-screen flex flex-col">
            <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-700/80 dark:bg-slate-900/95">
                <div class="em-shell">
                    <div class="flex h-16 items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">e-Maintenance</p>
                            <p class="text-xs text-slate-500 dark:text-slate-300">PT Dharma Poliplast · Scan QR</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-theme-toggle />
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-site-footer />
        </div>
    </body>
</html>
