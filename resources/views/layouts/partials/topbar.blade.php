<header class="cc-topbar">
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" class="cc-hamburger" @click="$store.sidebar.toggleMobile()" aria-label="Open menu" title="Menu">
            <i class="ti ti-menu-2 fs-4"></i>
        </button>
        <button type="button" class="cc-hamburger d-none d-lg-inline-flex" @click="$store.sidebar.toggleCollapse()" aria-label="Toggle sidebar" title="Collapse sidebar">
            <i class="ti ti-layout-sidebar-left-collapse fs-4"></i>
        </button>
        <div class="d-none d-md-block ms-1">
            <div class="small text-muted lh-1">@yield('title', 'Dashboard')</div>
            <div class="fw-bold lh-sm">ClinicCare HMS</div>
        </div>
    </div>

    @if(in_array(auth()->user()->roles->first()?->name, ['receptionist', 'nurse', 'super-admin', 'doctor', 'pharmacist']))
        <form action="{{ route('reception.patients') }}" method="GET" class="cc-search flex-grow-1 d-none d-md-block" style="max-width:420px">
            <i class="ti ti-search"></i>
            <input type="search" name="q" placeholder="Search patient name, mobile, ID..." value="{{ request('q') }}" autocomplete="off">
        </form>
    @endif

    <div class="d-flex align-items-center gap-2 ms-auto">
        <span class="badge bg-success-lt d-none d-sm-inline-flex align-items-center gap-1 px-2 py-1">
            <span class="live-dot"></span> Live
        </span>
        <button type="button"
                class="cc-theme-toggle"
                @click="$store.theme.toggle()"
                :title="$store.theme.isDark ? 'Light mode' : 'Dark mode'">

            <i class="fs-4"
            :class="$store.theme.isDark ? 'ti ti-sun' : 'ti ti-moon'">
            </i>

        </button>
        @include('layouts.partials.notifications')
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-reset text-decoration-none px-2 py-1 rounded-pill cc-profile-pill" data-bs-toggle="dropdown">
                <span class="avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center" style="background:var(--cc-gradient);color:#fff;font-weight:700;font-size:0.85rem">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <span class="d-none d-lg-inline small fw-semibold">{{ auth()->user()->name }}</span>
                <i class="ti ti-chevron-down small opacity-50 d-none d-lg-inline"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 overflow-hidden" style="border-radius:14px;min-width:220px">
                <div class="px-3 py-3" style="background:var(--cc-primary-light)">
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <div class="small text-muted text-capitalize">{{ str_replace('-', ' ', auth()->user()->roles->first()?->name ?? '') }}</div>
                </div>
                <div class="p-2">
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="dropdown-item rounded text-danger"><i class="ti ti-logout me-2"></i>Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
