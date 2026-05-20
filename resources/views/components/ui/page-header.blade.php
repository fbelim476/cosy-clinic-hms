@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'live' => false,
])

<div {{ $attributes->merge(['class' => 'cc-page-header']) }}>
    <div>
        @if(isset($breadcrumb))
            <ol class="cc-breadcrumb mb-2">{{ $breadcrumb }}</ol>
        @endif
        <h1 class="cc-page-title">
            @if($icon)<i class="ti {{ $icon }} text-primary me-2"></i>@endif
            {{ $title }}
        </h1>
        @if($subtitle)
            <p class="cc-page-subtitle">
                @if($live)<span class="live-dot me-1"></span>@endif
                {{ $subtitle }}
            </p>
        @endif
    </div>
    @if(isset($actions))
        <div class="d-flex flex-wrap gap-2 align-items-center">{{ $actions }}</div>
    @endif
</div>
