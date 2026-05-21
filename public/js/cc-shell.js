/**
 * ClinicCare HMS — Shell: sidebar, theme, tooltips
 */
(function () {
    function applyTheme(mode) {
        document.documentElement.setAttribute('data-bs-theme', mode);
        document.documentElement.classList.toggle('cc-dark', mode === 'dark');
    }

    // Prevent flash — run before paint if inline in head; also on load
    var savedTheme = localStorage.getItem('cc-theme');
    if (savedTheme) applyTheme(savedTheme);

    var savedCollapsed = localStorage.getItem('cc-sidebar-collapsed') === '1';
    if (savedCollapsed) document.body.classList.add('sidebar-collapsed');

    window.toggleSidebarCollapse = function () {
        document.body.classList.toggle('sidebar-collapsed');
        var collapsed = document.body.classList.contains('sidebar-collapsed');
        localStorage.setItem('cc-sidebar-collapsed', collapsed ? '1' : '0');
        window.dispatchEvent(new CustomEvent('cc-sidebar-toggle', { detail: { collapsed } }));
    };

    window.toggleMobileSidebar = function (open) {
        document.body.classList.toggle('sidebar-mobile-open', open);
    };

    document.addEventListener('alpine:init', function () {
        var Alpine = window.Alpine;
        if (!Alpine) return;

        Alpine.store('sidebar', {
            mobileOpen: false,
            collapsed: document.body.classList.contains('sidebar-collapsed'),
            toggleMobile() {
                this.mobileOpen = !this.mobileOpen;
                window.toggleMobileSidebar(this.mobileOpen);
            },
            closeMobile() {
                this.mobileOpen = false;
                window.toggleMobileSidebar(false);
            },
            toggleCollapse() {
                window.toggleSidebarCollapse();
                this.collapsed = document.body.classList.contains('sidebar-collapsed');
            },
        });

        Alpine.store('theme', {
            mode: localStorage.getItem('cc-theme') || 'light',
            get isDark() { return this.mode === 'dark'; },
            icon() { return this.mode === 'dark' ? 'ti-sun' : 'ti-moon'; },
            toggle() {
                this.mode = this.mode === 'dark' ? 'light' : 'dark';
                applyTheme(this.mode);
                localStorage.setItem('cc-theme', this.mode);
            },
            init() {
                applyTheme(this.mode);
            },
        });
    });
})();
