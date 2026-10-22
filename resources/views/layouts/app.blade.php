<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
    <body class="font-sans antialiased em-body {{ $bodyClass }}" style="font-family: 'DM Sans', Figtree, ui-sans-serif, system-ui, sans-serif;">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-slate-200 bg-white dark:border-white/5 dark:bg-slate-900/60">
                    <div class="em-shell py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-site-footer />
        </div>
        @auth
            @if (! Auth::user()->canAccessAdmin())
                <x-technician.pic-pin-modal :technicians="$picTechnicians" />
            @endif
        @endauth
        @stack('scripts')
    </body>
</html>
