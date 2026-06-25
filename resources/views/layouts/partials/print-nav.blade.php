@php
    $items = [
        ['route' => 'admin.print.dashboard', 'icon' => 'ti-layout-dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.print.templates', 'icon' => 'ti-template', 'label' => 'Print Templates', 'match' => 'admin.print.templates*'],
        ['route' => 'admin.print.paper-sizes', 'icon' => 'ti-dimensions', 'label' => 'Paper Sizes'],
        ['route' => 'admin.print.printer-profiles', 'icon' => 'ti-printer', 'label' => 'Printer Profiles'],
        ['route' => 'admin.print.branding', 'icon' => 'ti-building-hospital', 'label' => 'Hospital Branding'],
        ['route' => 'admin.print.header', 'icon' => 'ti-layout-navbar', 'label' => 'Header Builder'],
        ['route' => 'admin.print.footer', 'icon' => 'ti-layout-bottombar', 'label' => 'Footer Builder'],
        ['route' => 'admin.print.fonts', 'icon' => 'ti-typography', 'label' => 'Font Library'],
        ['route' => 'admin.print.qr-barcode', 'icon' => 'ti-qrcode', 'label' => 'QR & Barcode'],
        ['route' => 'admin.print.variables', 'icon' => 'ti-variable', 'label' => 'Variables'],
        ['route' => 'admin.print.preview', 'icon' => 'ti-eye', 'label' => 'Preview'],
        ['route' => 'admin.print.import-export', 'icon' => 'ti-file-import', 'label' => 'Import / Export'],
        ['route' => 'admin.print.settings', 'icon' => 'ti-settings', 'label' => 'Settings'],
    ];
@endphp
<nav class="cc-print-nav mb-4">
    <div class="cc-print-nav-scroll">
        @foreach($items as $item)
            <a href="{{ route($item['route']) }}"
               class="cc-print-nav-link {{ request()->routeIs($item['match'] ?? $item['route']) ? 'active' : '' }}">
                <i class="ti {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
