@props([
    'label',
    'value',
    'icon' => 'ti-chart-bar',
    'variant' => 'soft', // soft | gradient
    'prefix' => '',
    'danger' => false,
])

@if($variant === 'gradient')
    <div {{ $attributes->merge(['class' => 'stat-gradient premium-card p-3 interactive']) }}>
        <i class="ti {{ $icon }} opacity-50"></i>
        <div class="small opacity-75 mt-2">{{ $label }}</div>
        <div class="stat-value">{{ $prefix }}{{ $value }}</div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'stat-soft' . ($danger ? ' border border-danger border-opacity-25' : '')]) }}>
        <i class="ti {{ $icon }} text-primary opacity-75"></i>
        <div class="stat-label mt-2">{{ $label }}</div>
        <div class="stat-value {{ $danger ? 'text-danger' : '' }}">{{ $prefix }}{{ $value }}</div>
    </div>
@endif
