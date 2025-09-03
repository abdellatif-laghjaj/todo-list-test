<template>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="card card-padding mb-8 slide-up">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Notifications
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Stay updated with your task activities
                    </p>
                    <div class="flex items-center gap-4 mt-3">
                        <div class="text-sm text-gray-500">
                            <span class="font-semibold text-gray-900">{{
                                notificationStore.notifications.length
                            }}</span>
                            {{
                                notificationStore.notifications.length === 1
                                    ? "notification"
                                    : "notifications"
                            }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        @click="markAllAsRead"
                        v-if="unreadCount > 0"
                        class="btn btn-secondary"
                    >
                        Mark All as Read
                    </button>
                    <button
                        @click="clearAll"
                        v-if="notificationStore.notifications.length > 0"
                        class="btn btn-ghost text-red-600 hover:bg-red-50"
                    >
                        Clear All
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center py-12">
                <div class="loading-spinner"></div>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="notificationStore.notifications.length === 0"
                class="card card-padding text-center py-12"
            >
                <div class="text-gray-400 mb-4">
                    <BellIcon class="w-16 h-16 mx-auto" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    No notifications yet
                </h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    When you have task activities, notifications will appear
                    here.
                </p>
                <router-link to="/tasks" class="btn btn-primary">
                    Go to Tasks
                </router-link>
            </div>

            <!-- Notifications Grid -->
            <TransitionGroup v-else name="list" tag="div" class="space-y-4">
                <div
                    v-for="(notification, index) in sortedNotifications"
                    :key="notification.id"
                    class="notification-card"
                    :class="{
                        unread: !notification.read,
                        read: notification.read,
                    }"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                >
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                            :class="getNotificationIconClass(notification.type)"
                        >
                            <component
                                :is="getNotificationIcon(notification.type)"
                                class="w-5 h-5"
                            />
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4
                                        class="font-semibold text-gray-900 mb-1"
                                    >
                                        {{ notification.title }}
                                    </h4>
                                    <p
                                        v-if="notification.message"
                                        class="text-gray-600 text-sm mb-2"
                                    >
                                        {{ notification.message }}
                                    </p>
                                    <div
                                        class="flex items-center gap-4 text-xs text-gray-500"
                                    >
                                        <span>{{
                                            formatRelativeDate(
                                                notification.created_at
                                            )
                                        }}</span>
                                        <span
                                            v-if="!notification.read"
                                            class="flex items-center gap-1 text-blue-600 font-medium"
                                        >
                                            <div
                                                class="w-2 h-2 bg-blue-500 rounded-full"
                                            ></div>
                                            New
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-1 ml-4">
                                    <button
                                        v-if="!notification.read"
                                        @click="markAsRead(notification.id)"
                                        class="action-btn text-gray-400 hover:text-blue-600"
                                        title="Mark as read"
                                    >
                                        <CheckIcon class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="
                                            removeNotification(notification.id)
                                        "
                                        class="action-btn text-gray-400 hover:text-red-600"
                                        title="Remove notification"
                                    >
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "@/stores/notifications";
import {
    BellIcon,
    CheckIcon,
    XMarkIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";

// Store
const notificationStore = useNotificationStore();

// Reactive data
const isLoading = ref(false);

// Computed properties
const unreadCount = computed(
    () => notificationStore.notifications.filter((n) => !n.read).length
);

const sortedNotifications = computed(() =>
    [...notificationStore.notifications].sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
    )
);

// Methods
const getNotificationIcon = (type) => {
    const icons = {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        error: XCircleIcon,
        info: InformationCircleIcon,
    };
    return icons[type] || InformationCircleIcon;
};

const getNotificationIconClass = (type) => {
    const classes = {
        success: "bg-green-100 text-green-600",
        warning: "bg-yellow-100 text-yellow-600",
        error: "bg-red-100 text-red-600",
        info: "bg-blue-100 text-blue-600",
    };
    return classes[type] || "bg-blue-100 text-blue-600";
};

const markAsRead = (id) => {
    notificationStore.markAsRead(id);
};

const markAllAsRead = () => {
    notificationStore.notifications.forEach((notification) => {
        if (!notification.read) {
            notificationStore.markAsRead(notification.id);
        }
    });
};

const removeNotification = (id) => {
    notificationStore.removeNotification(id);
};

const clearAll = () => {
    if (confirm("Are you sure you want to clear all notifications?")) {
        notificationStore.clearAll();
    }
};

const formatRelativeDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "Just now";

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}h ago`;

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}d ago`;

    return date.toLocaleDateString();
};

// Lifecycle
onMounted(() => {
    // You can add any initialization logic here
});
</script>

<style scoped>
.notification-card {
    @apply bg-white border border-gray-200 rounded-lg p-4 transition-all duration-200 hover:shadow-md;
}

.notification-card.unread {
    @apply border-l-4 border-l-blue-500 bg-blue-50/30;
}

.notification-card.read {
    @apply opacity-75;
}

.action-btn {
    @apply p-2 rounded-lg transition-all duration-200;
}

.action-btn:hover {
    @apply bg-gray-100 transform scale-110;
}

.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}

.list-leave-to {
    opacity: 0;
    transform: translateX(20px);
}

.list-move {
    transition: transform 0.3s ease;
}
</style>
