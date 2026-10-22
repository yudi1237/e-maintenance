<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important}</style>
        @if (request()->routeIs('login'))
            <style>
                .em-login-brand {
                    position: absolute;
                    top: 1rem;
                    left: 1rem;
                    z-index: 30;
                    display: inline-flex;
                    align-items: center;
                    border-radius: 0.75rem;
                    border: 1px solid rgba(255, 255, 255, 0.85);
                    background: #ffffff;
                    padding: 0.55rem 0.8rem;
                    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
                }
                .em-login-brand img {
                    display: block;
                    height: 3.25rem;
                    width: auto;
                }
                @media (min-width: 640px) {
                    .em-login-brand {
                        top: 1.25rem;
                        left: 1.25rem;
                    }
                    .em-login-brand img {
                        height: 3.5rem;
                    }
                }
            </style>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        @php $isLogin = request()->routeIs('login'); @endphp
        <div class="{{ $isLogin ? 'em-login-shell' : 'min-h-screen flex flex-col bg-gray-100 dark:bg-slate-950' }}">
            @if ($isLogin)
                <div class="em-login-photo" aria-hidden="true">
                    <img src="{{ asset('images/login-background.jpg') }}" alt="">
                </div>
                <a href="{{ route('login') }}" class="em-login-brand">
                    <img src="{{ asset('images/logo-dpp.png') }}?v=2" alt="PT Dharma Poliplast">
                </a>
            @endif
            <div class="absolute top-4 right-4 z-10">
                <x-theme-toggle />
            </div>
            <div class="{{ $isLogin ? 'em-login-main' : 'flex flex-1 flex-col items-center justify-center px-4 pt-6 sm:pt-0 pb-4' }}">
                <div class="{{ $isLogin ? 'em-login-card' : 'w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg dark:border dark:border-white/10 dark:bg-slate-900/80 dark:shadow-xl' }}">
                    {{ $slot }}
                </div>
            </div>

            <x-site-footer class="{{ $isLogin ? 'em-login-footer' : '' }}" />
        </div>
    </body>
</html>
