<template>
    <div class="fixed top-20 right-4 z-[9999] w-80 max-w-sm">
        <TransitionGroup name="notification" tag="div" class="space-y-2">
            <NotificationToast
                v-for="notification in toasts"
                :key="notification.id"
                :notification="notification"
                @dismiss="removeToast"
            />
        </TransitionGroup>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useNotificationStore } from "@/stores/notifications";
import NotificationToast from "@/components/NotificationToast.vue";

// Store
const notificationsStore = useNotificationStore();

// Computed properties
const toasts = computed(() => notificationsStore.toasts);

// Methods
const removeToast = (id) => {
    notificationsStore.removeToast(id);
};
</script>

<style scoped>
.notification-enter-active {
    transition: all 0.3s ease-out;
}

.notification-leave-active {
    transition: all 0.3s ease-in;
}

.notification-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.notification-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.notification-move {
    transition: transform 0.3s ease;
}
</style>
