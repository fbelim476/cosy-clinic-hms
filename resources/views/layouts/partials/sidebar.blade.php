@php $role = auth()->user()->roles->first()?->name; @endphp
<aside class="cc-sidebar" id="ccSidebar" :class="{ 'open': sidebarOpen }">
    <div class="cc-sidebar-brand">
        <div class="cc-brand-icon"><i class="ti ti-building-hospital"></i></div>
        <div class="cc-brand-text">
            <div>ClinicCare</div>
            <div class="cc-brand-sub">Enterprise HMS</div>
        </div>
    </div>

    <nav class="flex-fill py-2 overflow-auto">
        @if($role === 'super-admin')
            <div class="cc-nav-section">Administration</div>
            <a href="{{ route('admin.dashboard') }}" class="cc-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                <i class="ti ti-layout-dashboard"></i><span class="cc-nav-label">Analytics</span>
            </a>
            <a href="{{ route('admin.users') }}" class="cc-nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="ti ti-users"></i><span class="cc-nav-label">Users</span>
            </a>
            <a href="{{ route('admin.medicines') }}" class="cc-nav-link {{ request()->routeIs('admin.medicines*') ? 'active' : '' }}">
                <i class="ti ti-pill"></i><span class="cc-nav-label">Medicines</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="cc-nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="ti ti-settings"></i><span class="cc-nav-label">Settings</span>
            </a>
        @endif

        @if(in_array($role, ['receptionist', 'nurse', 'super-admin']))
            <div class="cc-nav-section">Reception</div>
            <a href="{{ route('reception.dashboard') }}" class="cc-nav-link {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-list"></i><span class="cc-nav-label">Queue Board</span>
            </a>
            <a href="{{ route('reception.register') }}" class="cc-nav-link {{ request()->routeIs('reception.register') ? 'active' : '' }}">
                <i class="ti ti-user-plus"></i><span class="cc-nav-label">Registration</span>
            </a>
            <a href="{{ route('reception.patients') }}" class="cc-nav-link {{ request()->routeIs('reception.patients*') ? 'active' : '' }}">
                <i class="ti ti-search"></i><span class="cc-nav-label">Patients</span>
            </a>
        @endif

        @if(in_array($role, ['doctor', 'super-admin']))
            <div class="cc-nav-section">Clinical</div>
            <a href="{{ route('doctor.dashboard') }}" class="cc-nav-link {{ request()->routeIs('doctor.*') ? 'active' : '' }}">
                <i class="ti ti-stethoscope"></i><span class="cc-nav-label">Consultation</span>
            </a>
        @endif

        @if(in_array($role, ['pharmacist', 'super-admin']))
            <div class="cc-nav-section">Pharmacy</div>
            <a href="{{ route('pharmacy.dashboard') }}" class="cc-nav-link {{ request()->routeIs('pharmacy.*') ? 'active' : '' }}">
                <i class="ti ti-shopping-cart"></i><span class="cc-nav-label">POS Billing</span>
            </a>
        @endif

        @if(in_array($role, ['accountant', 'super-admin']))
            <div class="cc-nav-section">Finance</div>
            <a href="{{ route('billing.dashboard') }}" class="cc-nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                <i class="ti ti-receipt-2"></i><span class="cc-nav-label">Billing</span>
            </a>
        @endif

        @if(in_array($role, ['lab-technician', 'super-admin']))
            <a href="{{ route('lab.dashboard') }}" class="cc-nav-link {{ request()->routeIs('lab.*') ? 'active' : '' }}">
                <i class="ti ti-test-pipe-2"></i><span class="cc-nav-label">Laboratory</span>
            </a>
        @endif

        <div class="cc-nav-section">Display</div>
        <a href="{{ route('queue.display') }}" target="_blank" class="cc-nav-link">
            <i class="ti ti-device-tv"></i><span class="cc-nav-label">Token Screen</span>
        </a>
    </nav>

    <div class="cc-sidebar-footer">
        <button type="button" class="cc-nav-link w-100 border-0 bg-transparent mb-1" onclick="toggleSidebarCollapse()">
            <i class="ti ti-layout-sidebar-left-collapse"></i><span class="cc-nav-label">Collapse</span>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="cc-nav-link w-100 border-0 bg-transparent text-danger">
                <i class="ti ti-logout"></i><span class="cc-nav-label">Logout</span>
            </button>
        </form>
    </div>
</aside>
