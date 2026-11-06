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
                try {
                    var f11 = sessionStorage.getItem('em-fs-f11') === '1' && window.innerHeight >= screen.availHeight - 8;
                    if (window.frameElement || f11) document.documentElement.classList.add('em-fs-persist');
                } catch (e) {}
            })();
        </script>
        <style>
            html.em-fs-persist nav { display: none !important; }
            #em-fs-exit { display: none; }
            html.em-fs-persist #em-fs-exit {
                display: inline-flex;
                position: fixed;
                top: 0.75rem;
                right: 0.75rem;
                z-index: 70;
                border: 0;
                border-radius: 0.75rem;
                background: #0891b2;
                color: #fff;
                font-weight: 700;
                padding: 0.55rem 0.9rem;
                cursor: pointer;
            }
        </style>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important}</style>
    </head>
    <body class="font-sans antialiased em-body" style="font-family: 'DM Sans', Figtree, ui-sans-serif, system-ui, sans-serif;">
        <div class="min-h-screen flex flex-col">
            <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-700/80 dark:bg-slate-900/95">
                <div class="em-shell">
                    <div class="flex items-center justify-between gap-3" style="min-height:4rem;padding-top:0.5rem;padding-bottom:0.5rem;">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">e-Maintenance</p>
                            <p class="text-xs text-slate-500 dark:text-slate-300">PT Dharma Poliplast · Scan QR</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $scanBackUrl = null;
                                if (request()->routeIs('scan.machines.show') && auth()->check()) {
                                    $scanBackUrl = route(auth()->user()->homeRouteName());
                                } elseif (request()->routeIs('scan.operator-reports.create')) {
                                    $scanBackUrl = auth()->check()
                                        ? route(auth()->user()->homeRouteName())
                                        : route('scan.machines.show', [request()->route('machineNumber'), request()->route('token')]);
                                }
                                $scanBackStyle = 'display:inline-flex;align-items:center;justify-content:center;gap:0.35rem;min-height:44px;padding:0.55rem 1rem;border-radius:0.75rem;background:#0891b2;color:#ffffff;font-weight:700;font-size:0.95rem;line-height:1;text-decoration:none;border:2px solid #0e7490;box-shadow:0 4px 12px rgba(8,145,178,.35);cursor:pointer;';
                            @endphp
                            @if (request()->routeIs('scan.machines.show', 'scan.operator-reports.create'))
                                @if ($scanBackUrl)
                                    <a href="{{ $scanBackUrl }}" style="{{ $scanBackStyle }}">{{ auth()->check() && request()->routeIs('scan.operator-reports.create') ? 'Keluar' : '← Kembali' }}</a>
                                @else
                                    <button type="button" onclick="if (window.history.length > 1) window.history.back();" style="{{ $scanBackStyle }}">← Kembali</button>
                                @endif
                            @endif
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
        <button type="button" id="em-fs-exit">Keluar</button>
        <script>
            document.getElementById('em-fs-exit')?.addEventListener('click', function () {
                try { sessionStorage.removeItem('em-fs-f11'); } catch (e) {}
                @if (auth()->user()?->isOperator())
                window.location.href = @json(route('operator.home'));
                return;
                @endif
                if (window.frameElement && window.parent) {
                    window.parent.postMessage({ type: 'em-fs-exit' }, window.location.origin);
                }
            });
        </script>
    </body>
</html>
