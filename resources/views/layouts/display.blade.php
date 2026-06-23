<!DOCTYPE html>
<html lang="en" class="token-display-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OPD Token Display — CosyClinic HMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    {{-- Do NOT use @vite here — manifest may exist without token-display.css and throws ViteException --}}
    <link rel="stylesheet" href="{{ asset('css/token-display.css') }}?v={{ file_exists(public_path('css/token-display.css')) ? filemtime(public_path('css/token-display.css')) : time() }}">

    @livewireStyles

    {{-- Inline fallback: guarantees styling even if css/token-display.css 404s --}}
    @include('layouts.partials.token-display-critical-css')

    <script>
        window.__CC_REVERB__ = {
            key: @json(config('broadcasting.connections.reverb.key')),
            host: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
            port: @json(config('broadcasting.connections.reverb.options.port', 8080)),
            scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'http')),
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="{{ url('js/hms-realtime.js') }}"></script>
</head>
<body class="token-display-screen td-theme-night" id="tdBody">
    <header class="td-header">
        <div>
            <div class="td-brand"><i class="ti ti-building-hospital"></i> CosyClinic OPD</div>
            <div class="td-clock" id="tdClock">{{ now()->format('l, d F Y — h:i A') }}</div>
        </div>
        <div class="td-header-actions">
            <button type="button" class="td-theme-btn" id="tdThemeBtn" title="Toggle Day / Night mode" aria-label="Toggle theme">
                <i class="ti ti-moon" id="tdThemeIcon"></i>
            </button>
            <div class="td-live-badge">
                <span class="td-live-dot" aria-hidden="true"></span>
                Live Queue
            </div>
        </div>
    </header>

    <main class="token-display-main">
        @yield('content')
    </main>

    <footer class="td-footer">CosyClinic Hospital Management System</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.initHmsRealtime?.(0);

            const body = document.getElementById('tdBody');
            const btn = document.getElementById('tdThemeBtn');
            const icon = document.getElementById('tdThemeIcon');
            const STORAGE_KEY = 'cc-token-display-theme';

            const applyTheme = (theme) => {
                body.classList.remove('td-theme-day', 'td-theme-night');
                body.classList.add(theme === 'day' ? 'td-theme-day' : 'td-theme-night');
                if (icon) {
                    icon.className = theme === 'day' ? 'ti ti-sun' : 'ti ti-moon';
                }
                localStorage.setItem(STORAGE_KEY, theme);
            };

            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved === 'day' || saved === 'night') {
                applyTheme(saved);
            } else {
                const hour = new Date().getHours();
                applyTheme(hour >= 7 && hour < 19 ? 'day' : 'night');
            }

            btn?.addEventListener('click', () => {
                const isDay = body.classList.contains('td-theme-day');
                applyTheme(isDay ? 'night' : 'day');
            });

            const tick = () => {
                const el = document.getElementById('tdClock');
                if (!el) return;
                el.textContent = new Date().toLocaleString('en-IN', {
                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            };
            tick();
            setInterval(tick, 30000);
        });
    </script>
    @stack('scripts')
</body>
</html>
