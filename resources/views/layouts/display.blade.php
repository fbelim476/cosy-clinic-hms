<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Token Display — ClinicCare HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        window.__CC_REVERB__ = {
            key: @json(config('broadcasting.connections.reverb.key')),
            host: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
            port: @json(config('broadcasting.connections.reverb.options.port', 8080)),
            scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'http')),
        };
    </script>
    <script src="{{ asset('js/hms-realtime.js') }}"></script>
    @livewireStyles
    <style>
        body { font-family: Inter, sans-serif; background: linear-gradient(160deg, #0c1929 0%, #0f2744 50%, #0a1628 100%); color: #fff; min-height: 100vh; margin: 0; padding: 2rem; }
        .display-header { text-align: center; margin-bottom: 2rem; }
        .display-header h1 { font-size: 2rem; background: linear-gradient(90deg, #38bdf8, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .display-label { font-size: 1.25rem; letter-spacing: 0.25em; opacity: 0.6; text-transform: uppercase; }
        .display-token-hero { font-size: clamp(6rem, 18vw, 12rem); font-weight: 900; line-height: 1; background: linear-gradient(180deg, #fff, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; transition: all 0.4s ease; }
        .display-patient-name { font-size: clamp(1.5rem, 4vw, 2.5rem); margin-top: 1rem; font-weight: 600; }
        .next-token-box { background: rgba(255,255,255,0.08); border-radius: 16px; padding: 1.25rem; border: 1px solid rgba(56,189,248,0.2); transition: transform 0.3s; }
        .next-token-box.emergency { background: rgba(239,68,68,0.25); border-color: #ef4444; animation: emergencyPulse 1.5s infinite; }
        .next-num { font-size: 2rem; font-weight: 800; color: #38bdf8; }
        .next-name { font-size: 0.85rem; opacity: 0.8; margin-top: 0.25rem; }
        @keyframes emergencyPulse { 50% { box-shadow: 0 0 30px rgba(239,68,68,0.5); } }
    </style>
</head>
<body>
    <div class="display-header">
        <h1>ClinicCare OPD</h1>
        <div style="opacity:0.5">{{ now()->format('l, d F Y — h:i A') }}</div>
        <span class="live-dot" style="margin-top:0.5rem"></span> <small>LIVE</small>
    </div>
    {{ $slot }}
    @livewireScripts
    <script>document.addEventListener('DOMContentLoaded',()=>window.initHmsRealtime?.(0));</script>
</body>
</html>
