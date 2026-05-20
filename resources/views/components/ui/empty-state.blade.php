@props(['icon' => 'ti-mood-empty', 'title' => 'No data', 'message' => null])

<div {{ $attributes->merge(['class' => 'cc-empty']) }}>
    <i class="ti {{ $icon }}"></i>
    <p class="fw-semibold mb-1">{{ $title }}</p>
    @if($message)<p class="small mb-0">{{ $message }}</p>@endif
    @if($slot->isNotEmpty())<div class="mt-3">{{ $slot }}</div>@endif
</div>
