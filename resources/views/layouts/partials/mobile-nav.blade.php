@php
    $role = auth()->user()->roles->first()?->name;
    $home = auth()->user()->dashboardRoute();
@endphp
<nav class="cc-mobile-nav">
    <a href="{{ $home }}" class="{{ request()->routeIs('*.dashboard') && !request()->routeIs('reception.register') ? 'active' : '' }}">
        <i class="ti ti-home"></i> Home
    </a>
    @if(in_array($role, ['receptionist', 'nurse', 'super-admin']))
        <a href="{{ route('reception.register') }}" class="{{ request()->routeIs('reception.register') ? 'active' : '' }}">
            <i class="ti ti-user-plus"></i> Register
        </a>
        <a href="{{ route('reception.dashboard') }}" class="{{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
            <i class="ti ti-list-numbers"></i> Queue
        </a>
    @endif
    @if(in_array($role, ['doctor', 'super-admin']))
        <a href="{{ route('doctor.dashboard') }}" class="{{ request()->routeIs('doctor.*') ? 'active' : '' }}">
            <i class="ti ti-stethoscope"></i> Doctor
        </a>
    @endif
    @if(in_array($role, ['pharmacist', 'super-admin']))
        <a href="{{ route('pharmacy.dashboard') }}" class="{{ request()->routeIs('pharmacy.*') ? 'active' : '' }}">
            <i class="ti ti-shopping-cart"></i> POS
        </a>
    @endif
    @if(in_array($role, ['accountant', 'super-admin']))
        <a href="{{ route('billing.dashboard') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">
            <i class="ti ti-receipt"></i> Bills
        </a>
    @endif
</nav>
