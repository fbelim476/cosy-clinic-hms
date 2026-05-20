<!DOCTYPE html>
<html lang="en" x-data x-init="$store.theme.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ClinicCare HMS') — Enterprise Medical ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/premium.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    @livewireStyles
    @stack('styles')
</head>
<body class="cc-app" x-data="{ sidebarOpen: false }" @keydown.escape="sidebarOpen = false">
@auth
    <div class="cc-sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>
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
            ClinicCare HMS &mdash; Enterprise Hospital Management
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
<script src="{{ asset('js/hms-realtime.js') }}"></script>
@livewireScripts
@auth
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.initHmsRealtime?.({{ auth()->id() }});
        const collapsed = localStorage.getItem('cc-sidebar-collapsed') === '1';
        if (collapsed) document.body.classList.add('sidebar-collapsed');
    });
    function toggleSidebarCollapse() {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('cc-sidebar-collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
    }
</script>
@endauth
@stack('scripts')
</body>
</html>
