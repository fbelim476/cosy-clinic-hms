<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function(){var t=localStorage.getItem('cc-theme');if(t)document.documentElement.setAttribute('data-bs-theme',t);if(localStorage.getItem('cc-sidebar-collapsed')==='1')document.documentElement.classList.add('sidebar-collapsed-pending');})();
    </script>
    <title>@yield('title', 'CosyClinic HMS') — Enterprise Medical ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/premium.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    @livewireStyles
    @stack('styles')
    <style>html.sidebar-collapsed-pending body.cc-app{margin-left:0}</style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body class="cc-app" x-data x-init="$store.theme.init(); if(document.documentElement.classList.contains('sidebar-collapsed-pending')){document.body.classList.add('sidebar-collapsed');document.documentElement.classList.remove('sidebar-collapsed-pending');}">
@auth
    <div class="cc-sidebar-overlay" @click="$store.sidebar.closeMobile()"></div>
    @include('layouts.partials.sidebar')
    <div class="cc-main">
        @include('layouts.partials.topbar')
        <div class="cc-content">
            @hasSection('breadcrumb')
                <ol class="cc-breadcrumb mb-3">@yield('breadcrumb')</ol>
            @endif
            @yield('content')
        </div>
        <footer class="text-center text-muted py-3 small opacity-75 d-none d-lg-block">
            CosyClinic HMS &mdash; Enterprise Hospital Management
        </footer>
    </div>
    @include('layouts.partials.mobile-nav')
@else
    <main>@yield('content')</main>
@endauth

@include('layouts.partials.toasts')

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
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
<script src="{{ asset('js/cc-shell.js') }}"></script>
<script src="{{ asset('js/hms-realtime.js') }}"></script>
@livewireScripts
@auth
<script>document.addEventListener('DOMContentLoaded',()=>window.initHmsRealtime?.({{ auth()->id() }}));</script>
@endauth
@stack('scripts')
</body>
</html>
