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
                try {
                    var f11 = sessionStorage.getItem('em-fs-f11') === '1' && window.innerHeight >= screen.availHeight - 8;
                    if (window.frameElement || f11) document.documentElement.classList.add('em-fs-persist');
                } catch (e) {}
            })();
        </script>
        <style>
            html.em-fs-persist, html.em-fs-persist body { height: 100%; overflow: hidden; }
            html.em-fs-persist nav, html.em-fs-persist .em-site-footer { display: none !important; }
            html.em-fs-persist .min-h-screen { min-height: 0 !important; height: 100%; }
            html.em-fs-persist main { height: 100%; overflow: auto; }
            #em-fs-exit { display: none; }
            html.em-fs-persist #em-fs-exit {
                display: inline-flex;
                position: fixed;
                top: 0.75rem;
                right: 0.75rem;
                z-index: 70;
                align-items: center;
                border: 0;
                border-radius: 0.75rem;
                background: #0891b2;
                color: #fff;
                font-weight: 700;
                padding: 0.55rem 0.9rem;
                cursor: pointer;
            }
            html.em-fs-persist:has(#dashboard-fullscreen-btn) #em-fs-exit { display: none; }
        </style>

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
            @if (! Auth::user()->canAccessAdmin() && ! Auth::user()->isOperator())
                <x-technician.pic-pin-modal :technicians="$picTechnicians" />
            @endif
        @endauth
        <button type="button" id="em-fs-exit">Keluar</button>
        <script>
            (function () {
                function removeShell() {
                    var shell = document.getElementById('em-fs-shell');
                    if (shell) shell.remove();
                }
                function exitPersist() {
                    try { sessionStorage.removeItem('em-fs-f11'); } catch (e) {}
                    document.documentElement.classList.remove('em-fs-persist');
                    if (window.frameElement && window.parent) {
                        window.parent.postMessage({ type: 'em-fs-exit' }, window.location.origin);
                        return;
                    }
                    removeShell();
                    if (document.fullscreenElement && document.exitFullscreen) document.exitFullscreen();
                    else if (document.webkitFullscreenElement && document.webkitExitFullscreen) document.webkitExitFullscreen();
                }
                window.emExitFullscreen = exitPersist;
                window.emEnterFullscreenShell = function () {
                    if (window.frameElement) {
                        exitPersist();
                        return;
                    }
                    var shell = document.getElementById('em-fs-shell');
                    if (shell && (document.fullscreenElement === shell || document.webkitFullscreenElement === shell)) {
                        exitPersist();
                        return;
                    }
                    if (!shell) {
                        shell = document.createElement('div');
                        shell.id = 'em-fs-shell';
                        shell.setAttribute('style', 'position:fixed;inset:0;z-index:80;background:#020617;');
                        var frame = document.createElement('iframe');
                        frame.setAttribute('style', 'width:100%;height:100%;border:0;background:#020617;');
                        frame.src = window.location.href;
                        shell.appendChild(frame);
                        document.body.appendChild(shell);
                    }
                    var req = shell.requestFullscreen || shell.webkitRequestFullscreen;
                    if (req) req.call(shell);
                };
                document.getElementById('em-fs-exit')?.addEventListener('click', exitPersist);
                window.addEventListener('message', function (event) {
                    if (event.origin !== window.location.origin) return;
                    if (!event.data || event.data.type !== 'em-fs-exit') return;
                    removeShell();
                    if (document.fullscreenElement && document.exitFullscreen) document.exitFullscreen();
                });
                document.addEventListener('fullscreenchange', function () {
                    if (!document.fullscreenElement) removeShell();
                });
                document.addEventListener('submit', function (event) {
                    var form = event.target;
                    if (!form || !form.action || form.action.indexOf('/logout') === -1) return;
                    try { sessionStorage.removeItem('em-fs-f11'); } catch (e) {}
                    if (window.frameElement) form.target = '_top';
                }, true);
            })();
        </script>
        @stack('scripts')
        <script>
            (function () {
                var key = 'em-keep-scroll';
                document.addEventListener('click', function (event) {
                    var link = event.target.closest('nav[aria-label="Navigasi halaman"] a[href]');
                    if (!link) return;
                    try { sessionStorage.setItem(key, String(window.scrollY)); } catch (e) {}
                }, true);
                function restore() {
                    var raw;
                    try { raw = sessionStorage.getItem(key); } catch (e) { return; }
                    if (raw === null) return;
                    try { sessionStorage.removeItem(key); } catch (e) {}
                    var y = Number(raw);
                    if (!Number.isFinite(y)) return;
                    var apply = function () { window.scrollTo(0, y); };
                    apply();
                    requestAnimationFrame(apply);
                    setTimeout(apply, 60);
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', restore);
                } else {
                    restore();
                }
            })();
        </script>
    </body>
</html>
