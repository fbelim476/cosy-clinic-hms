<header class="cc-topbar">
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" class="btn btn-ghost-primary d-lg-none btn-icon" @click="sidebarOpen = true" aria-label="Menu">
            <i class="ti ti-menu-2"></i>
        </button>
        <div class="d-none d-md-block">
            <div class="small text-muted lh-1">@yield('title', 'Dashboard')</div>
            <div class="fw-bold lh-sm">ClinicCare HMS</div>
        </div>
    </div>

    @if(in_array(auth()->user()->roles->first()?->name, ['receptionist', 'nurse', 'super-admin', 'doctor']))
        <form action="{{ route('reception.patients') }}" method="GET" class="cc-search d-none d-md-block">
            <i class="ti ti-search"></i>
            <input type="search" name="q" placeholder="Search patients, mobile, ID..." value="{{ request('q') }}" autocomplete="off">
        </form>
    @endif

    <div class="d-flex align-items-center gap-1 ms-auto">
        <span class="d-none d-sm-inline-flex align-items-center gap-1 small text-muted me-1">
            <span class="live-dot"></span> Live
        </span>
        <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" @click="$store.theme.toggle()" title="Toggle theme">
            <i class="ti ti-moon"></i>
        </button>
        @include('layouts.partials.notifications')
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-reset text-decoration-none px-2 py-1 rounded-pill"
               style="background:var(--cc-gray-50)" data-bs-toggle="dropdown">
                <span class="avatar avatar-sm rounded-circle" style="background:var(--cc-gradient);color:#fff;font-weight:700">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <span class="d-none d-lg-inline small fw-semibold">{{ auth()->user()->name }}</span>
                <i class="ti ti-chevron-down small opacity-50 d-none d-lg-inline"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius:12px">
                <div class="px-3 py-2">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="small text-muted text-capitalize">{{ str_replace('-', ' ', auth()->user()->roles->first()?->name ?? '') }}</div>
                </div>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i>Sign out</button>
                </form>
            </div>
        </div>
    </div>
</header>
