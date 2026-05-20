@php
    $role = auth()->user()->roles->first()?->name;
@endphp

@if($role === 'super-admin')
    <li class="nav-item"><a class="nav-link sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="ti ti-dashboard me-2"></i>Dashboard</a></li>
    <li class="nav-item"><a class="nav-link sidebar-link" href="{{ route('admin.users') }}"><i class="ti ti-users me-2"></i>Users</a></li>
    <li class="nav-item"><a class="nav-link sidebar-link" href="{{ route('admin.medicines') }}"><i class="ti ti-pill me-2"></i>Medicines</a></li>
    <li class="nav-item"><a class="nav-link sidebar-link" href="{{ route('admin.settings') }}"><i class="ti ti-settings me-2"></i>Settings</a></li>
@endif

@if(in_array($role, ['receptionist', 'nurse', 'super-admin']))
    <li class="nav-item"><a class="nav-link sidebar-link {{ request()->routeIs('reception.*') ? 'active' : '' }}" href="{{ route('reception.dashboard') }}"><i class="ti ti-clipboard-plus me-2"></i>Reception</a></li>
    <li class="nav-item"><a class="nav-link sidebar-link" href="{{ route('reception.register') }}"><i class="ti ti-user-plus me-2"></i>Register Patient</a></li>
    <li class="nav-item"><a class="nav-link sidebar-link" href="{{ route('reception.patients') }}"><i class="ti ti-search me-2"></i>Patient Search</a></li>
@endif

@if(in_array($role, ['doctor', 'super-admin']))
    <li class="nav-item"><a class="nav-link sidebar-link {{ request()->routeIs('doctor.*') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}"><i class="ti ti-stethoscope me-2"></i>Doctor Panel</a></li>
@endif

@if(in_array($role, ['pharmacist', 'super-admin']))
    <li class="nav-item"><a class="nav-link sidebar-link {{ request()->routeIs('pharmacy.*') ? 'active' : '' }}" href="{{ route('pharmacy.dashboard') }}"><i class="ti ti-vaccine me-2"></i>Pharmacy</a></li>
@endif

@if(in_array($role, ['accountant', 'super-admin']))
    <li class="nav-item"><a class="nav-link sidebar-link {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.dashboard') }}"><i class="ti ti-receipt me-2"></i>Billing</a></li>
@endif

@if(in_array($role, ['lab-technician', 'super-admin']))
    <li class="nav-item"><a class="nav-link sidebar-link {{ request()->routeIs('lab.*') ? 'active' : '' }}" href="{{ route('lab.dashboard') }}"><i class="ti ti-test-pipe me-2"></i>Lab</a></li>
@endif

<li class="nav-item"><a class="nav-link sidebar-link" href="{{ route('queue.display') }}" target="_blank"><i class="ti ti-device-tv me-2"></i>Token Display</a></li>
