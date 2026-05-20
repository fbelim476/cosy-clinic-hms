/* ClinicCare HMS — Real-time + UI helpers (CDN-compatible) */
(function () {
    const cfg = window.__CC_REVERB__ || {};
    if (typeof Pusher !== 'undefined' && cfg.key) {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: cfg.key,
            wsHost: cfg.host || location.hostname,
            wsPort: cfg.port || 8080,
            wssPort: cfg.port || 8080,
            forceTLS: cfg.scheme === 'https',
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
        });
    }

    window.playNotificationSound = function () {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            gain.gain.value = 0.05;
            osc.start();
            osc.stop(ctx.currentTime + 0.12);
        } catch (_) {}
    };

    window.showToast = function (title, message, type) {
        window.dispatchEvent(new CustomEvent('hms-toast', {
            detail: { id: Date.now(), title, message, type: type || 'info' },
        }));
    };

    window.initHmsRealtime = function (userId) {
        if (!window.Echo) return;
        window.Echo.channel('cliniccare-queue').listen('.visit-queue-updated', function (e) {
            if (window.Livewire) Livewire.dispatch('queue-updated', e);
        });
        window.Echo.channel('cliniccare-dashboard')
            .listen('.dashboard-stats-updated', function (e) {
                if (window.Livewire) Livewire.dispatch('dashboard-updated', e);
            })
            .listen('.visit-queue-updated', function (e) {
                if (window.Livewire) Livewire.dispatch('queue-updated', e);
            });
        if (userId) {
            window.Echo.private('App.Models.User.' + userId).listen('.hms-notification', function (e) {
                if (window.Alpine && Alpine.store('notifications')) Alpine.store('notifications').add(e);
            });
        }
    };

    document.addEventListener('alpine:init', function () {
        var Alpine = window.Alpine;
        if (!Alpine) return;
        Alpine.store('notifications', {
            items: [],
            unread: 0,
            add: function (payload) {
                var item = {
                    id: Date.now(),
                    title: payload.title,
                    message: payload.message,
                    type: payload.type || 'info',
                    read: false,
                    at: payload.at || new Date().toISOString(),
                };
                this.items.unshift(item);
                this.unread++;
                window.dispatchEvent(new CustomEvent('hms-toast', { detail: item }));
                if (payload.sound !== 'none') window.playNotificationSound();
            },
            markAllRead: function () {
                this.items.forEach(function (i) { i.read = true; });
                this.unread = 0;
            },
        });
        Alpine.store('theme', {
            mode: localStorage.getItem('cc-theme') || 'light',
            toggle: function () {
                this.mode = this.mode === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', this.mode);
                localStorage.setItem('cc-theme', this.mode);
            },
            init: function () {
                document.documentElement.setAttribute('data-bs-theme', this.mode);
            },
        });
    });
})();
