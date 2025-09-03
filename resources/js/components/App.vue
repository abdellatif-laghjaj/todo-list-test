<template>
    <div id="app" class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <Navbar v-if="authStore.isAuthenticated"></Navbar>

        <!-- Main Content -->
        <main :class="{ 'pt-16': authStore.isAuthenticated }">
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
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}

.slide-up-enter-from {
    transform: translateY(10px);
    opacity: 0;
}

.slide-up-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>
