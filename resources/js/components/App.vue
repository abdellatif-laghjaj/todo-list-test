<template>
    <div id="app" class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <Navbar v-if="authStore.isAuthenticated"></Navbar>

        <!-- Main Content -->
        <main
            class="main-content"
            :class="{ 'pt-16': authStore.isAuthenticated }"
        >
            <router-view></router-view>
        </main>

        <!-- Notification Center -->
        <NotificationCenter></NotificationCenter>
    </div>
</template>

<script>
import { onMounted } from "vue";
import { useAuthStore } from "../stores/auth";
import Navbar from "./layout/Navbar.vue";
import NotificationCenter from "./NotificationCenter.vue";
import { useNotificationStore } from "../stores/notifications";
export default {
    name: "App",
    components: {
        Navbar,
        NotificationCenter,
    },
    setup() {
        const authStore = useAuthStore();
        const notificationsStore = useNotificationStore();

        onMounted(() => {
            // Initialize auth from localStorage
            authStore.initializeAuth();

            // Request notification permission
            if ("Notification" in window) {
                Notification.requestPermission();
            }

            // Set up Echo listeners for real-time notifications
            if (authStore.isAuthenticated) {
                setupEchoListeners();
            }
        });

        const setupEchoListeners = () => {
            const user = authStore.user;
            if (!user || !window.Echo) return;

            // Listen for task events
            window.Echo.private(`tasks.${user.id}`)
                .listen(".task.created", (data) => {
                    notificationsStore.addNotification({
                        type: "success",
                        title: "Task Created",
                        message: data.message || "A new task has been created.",
                        data: data.task,
                    });
                })
                .listen(".task.updated", (data) => {
                    notificationsStore.addNotification({
                        type: "info",
                        title: "Task Updated",
                        message: data.message || "A task has been updated.",
                        data: data.task,
                    });
                })
                .listen(".task.deleted", (data) => {
                    notificationsStore.addNotification({
                        type: "warning",
                        title: "Task Deleted",
                        message: data.message || "A task has been deleted.",
                        data: { id: data.task_id },
                    });
                });
        };

        return {
            authStore,
        };
    },
};
</script>

<style>
/* Global styles */
* {
    box-sizing: border-box;
}

body {
    font-family: "Inter", sans-serif;
    margin: 0;
    padding: 0;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Main content area improvements */
.main-content {
    min-height: calc(100vh - 4rem);
    transition: padding-top var(--transition-normal, 0.25s ease-in-out);
}

/* Enhanced fade transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity var(--transition-normal, 0.3s ease-in-out);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Improved slide-up animations */
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all var(--transition-normal, 0.3s ease-out);
}

.slide-up-enter-from {
    transform: translateY(20px);
    opacity: 0;
}

.slide-up-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}

/* List transition improvements */
.list-enter-active,
.list-leave-active {
    transition: all var(--transition-normal, 0.3s ease-in-out);
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}

.list-leave-to {
    opacity: 0;
    transform: translateX(20px) scale(0.95);
}

.list-move {
    transition: transform var(--transition-normal, 0.3s ease-in-out);
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: rgb(243 244 246);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: rgb(156 163 175);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgb(107 114 128);
}

/* Focus-visible improvements for better accessibility */
button:focus-visible,
a:focus-visible,
input:focus-visible,
textarea:focus-visible,
select:focus-visible {
    outline: 2px solid rgb(99 102 241);
    outline-offset: 2px;
}

/* Text selection styling */
::selection {
    background-color: rgb(99 102 241 / 0.2);
    color: rgb(55 65 81);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .card {
        border-color: rgb(0 0 0);
        border-width: 2px;
    }

    .btn {
        border-width: 2px;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Print styles */
@media print {
    .navbar,
    .btn,
    .action-btn {
        display: none !important;
    }

    .main-content {
        padding-top: 0 !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
}
</style>
