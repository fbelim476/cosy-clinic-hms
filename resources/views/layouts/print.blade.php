<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Print') — ClinicCare HMS</title>
    @stack('print-styles')
</head>
<body class="@yield('body-class')">
    @yield('content')
    @stack('print-scripts')
</body>
</html>
