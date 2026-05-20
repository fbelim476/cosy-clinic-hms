<div class="dropdown" x-data="{ open: false }">
    <button type="button" class="btn btn-ghost-secondary btn-icon position-relative" data-bs-toggle="dropdown">
        <i class="ti ti-bell"></i>
        <span class="badge bg-danger badge-notification" x-show="$store.notifications.unread > 0"
              x-text="$store.notifications.unread" style="position:absolute;top:2px;right:2px;font-size:0.6rem"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width:320px;max-height:400px;overflow-y:auto">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <span>Notifications</span>
            <button type="button" class="btn btn-sm btn-ghost-primary" @click="$store.notifications.markAllRead()">Mark read</button>
        </div>
        <template x-if="$store.notifications.items.length === 0">
            <div class="dropdown-item text-muted small">No notifications yet</div>
        </template>
        <template x-for="item in $store.notifications.items" :key="item.id">
            <div class="dropdown-item py-2" :class="{ 'bg-light': !item.read }">
                <div class="fw-semibold small" x-text="item.title"></div>
                <div class="text-muted small" x-text="item.message"></div>
            </div>
        </template>
    </div>
</div>
