import './echo';
import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;
    if (!Alpine) return;
    Alpine.store('notifications', {
        items: [],
        unread: 0,
        add(payload) {
            const item = {
                id: Date.now(),
                title: payload.title,
                message: payload.message,
                type: payload.type || 'info',
                icon: payload.icon || 'ti-bell',
                read: false,
                at: payload.at || new Date().toISOString(),
            };
            this.items.unshift(item);
            this.unread++;
            if (this.items.length > 30) this.items.pop();
            window.dispatchEvent(new CustomEvent('hms-toast', { detail: item }));
            if (payload.sound !== 'none' && window.playNotificationSound) {
                window.playNotificationSound();
            }
        },
        markAllRead() {
            this.items.forEach(i => (i.read = true));
            this.unread = 0;
        },
    });

});

window.playNotificationSound = () => {
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

window.initHmsRealtime = (userId) => {
    if (!window.Echo) return;

    window.Echo.channel('CosyClinic-queue')
        .listen('.visit-queue-updated', (e) => {
            Livewire.dispatch('queue-updated', e);
        });

    window.Echo.channel('CosyClinic-dashboard')
        .listen('.dashboard-stats-updated', (e) => {
            Livewire.dispatch('dashboard-updated', e.stats || {});
        })
        .listen('.visit-queue-updated', (e) => {
            Livewire.dispatch('queue-updated', e);
        });

    if (userId) {
        window.Echo.private(`App.Models.User.${userId}`)
            .listen('.hms-notification', (e) => {
                Alpine.store('notifications').add(e);
            });
    }
};

window.showToast = (title, message, type = 'info') => {
    window.dispatchEvent(new CustomEvent('hms-toast', {
        detail: { title, message, type, id: Date.now() },
    }));
};

document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, content }) => {
            if (status === 419) window.showToast('Session Expired', 'Please refresh the page.', 'danger');
        });
    });
});
