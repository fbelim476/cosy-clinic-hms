@props(['sticky' => true])

<div {{ $attributes->merge(['class' => 'cc-table-wrap']) }}>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0">
            @if(isset($head))
                <thead>{{ $head }}</thead>
            @endif
            <tbody>{{ $slot }}</tbody>
            @isset($foot)<tfoot>{{ $foot }}</tfoot>@endisset
        </table>
    </div>
</div>
