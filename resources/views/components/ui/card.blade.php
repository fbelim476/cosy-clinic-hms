@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'interactive' => false,
])

<div {{ $attributes->merge(['class' => 'cc-card premium-card' . ($interactive ? ' interactive' : '')]) }}>
    @if($title || isset($header))
        <div class="cc-card-header">
            <div>
                @if($title)<h3 class="h5 mb-0 fw-bold">{{ $title }}</h3>@endif
                @if($subtitle)<div class="small text-muted">{{ $subtitle }}</div>@endif
            </div>
            @isset($header){{ $header }}@endisset
        </div>
    @endif
    <div @class(['cc-card-body' => $padding])>{{ $slot }}</div>
    @isset($footer)<div class="cc-card-footer">{{ $footer }}</div>@endisset
</div>
