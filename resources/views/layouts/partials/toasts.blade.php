<div class="cc-toast-container" x-data="toastManager()" @hms-toast.window="add($event.detail)">
    <template x-for="t in toasts" :key="t.id">
        <div class="cc-toast" :class="t.type" x-show="t.visible" x-transition>
            <i class="ti" :class="t.icon || 'ti-bell'"></i>
            <div>
                <div class="fw-semibold small" x-text="t.title"></div>
                <div class="small text-muted" x-text="t.message"></div>
            </div>
            <button type="button" class="btn-close btn-close-sm ms-auto" @click="remove(t.id)"></button>
        </div>
    </template>
</div>
<script>
function toastManager() {
    return {
        toasts: [],
        add(detail) {
            const t = { ...detail, visible: true, icon: detail.type === 'success' ? 'ti-check' : detail.type === 'danger' ? 'ti-alert-circle' : 'ti-info-circle' };
            this.toasts.push(t);
            setTimeout(() => this.remove(t.id), 5000);
        },
        remove(id) {
            const i = this.toasts.findIndex(t => t.id === id);
            if (i >= 0) { this.toasts[i].visible = false; setTimeout(() => this.toasts.splice(i, 1), 300); }
        }
    };
}
</script>
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => window.showToast?.('Success', @json(session('success')), 'success'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded', () => window.showToast?.('Error', @json(session('error')), 'danger'));</script>
@endif
