<template>
    <nav class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Navigation -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <router-link
                            to="/dashboard"
                            class="text-xl font-bold text-indigo-600"
                        >
                            TodoApp
                        </router-link>
                    </div>

                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <router-link
                            to="/dashboard"
                            class="nav-link"
                            :class="{
                                'border-indigo-500 text-gray-900':
                                    $route.name === 'Dashboard',
                            }"
                        >
                            Dashboard
                        </router-link>
                        <router-link
                            to="/tasks"
                            class="nav-link"
                            :class="{
                                'border-indigo-500 text-gray-900':
                                    $route.name === 'TaskList',
                            }"
                        >
                            Tasks
                        </router-link>
                        <router-link
                            to="/notifications"
                            class="nav-link relative"
                            :class="{
                                'border-indigo-500 text-gray-900':
                                    $route.name === 'NotificationCenter',
                            }"
                        >
                            Notifications
                            <span
                                v-if="notificationStore.unreadCount > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                            >
                                {{
                                    notificationStore.unreadCount > 9
                                        ? "9+"
                                        : notificationStore.unreadCount
                                }}
                            </span>
                        </router-link>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <div class="relative">
                        <button
                            @click="toggleUserMenu"
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <div
                                class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center"
                            >
                                <span class="text-white font-medium">
                                    {{
                                        authStore.user?.name
                                            ?.charAt(0)
                                            .toUpperCase()
                                    }}
                                </span>
                            </div>
                            <span class="ml-2 text-gray-700 hidden md:block">
                                {{ authStore.user?.name }}
                            </span>
                        </button>

                        <!-- User Dropdown -->
                        <transition name="fade">
                            <div
                                v-if="showUserMenu"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                @click="showUserMenu = false"
                            >
                                <div
                                    class="px-4 py-2 text-sm text-gray-700 border-b"
                                >
                                    <div class="font-medium">
                                        {{ authStore.user?.name }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ authStore.user?.email }}
                                    </div>
                                </div>
                                <router-link
                                    to="/profile"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Profile
                                </router-link>
                                <button
                                    @click="handleLogout"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Sign out
                                </button>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t">
                <router-link
                    to="/dashboard"
                    class="mobile-nav-link"
                    :class="{
                        'bg-indigo-50 border-indigo-500 text-indigo-700':
                            $route.name === 'Dashboard',
                    }"
                >
                    Dashboard
                </router-link>
                <router-link
                    to="/tasks"
                    class="mobile-nav-link"
                    :class="{
                        'bg-indigo-50 border-indigo-500 text-indigo-700':
                            $route.name === 'TaskList',
                    }"
                >
                    Tasks
                </router-link>
                <router-link
                    to="/notifications"
                    class="mobile-nav-link flex items-center justify-between"
                    :class="{
                        'bg-indigo-50 border-indigo-500 text-indigo-700':
                            $route.name === 'NotificationCenter',
                    }"
                >
                    <span>Notifications</span>
                    <span
                        v-if="notificationStore.unreadCount > 0"
                        class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                    >
                        {{
                            notificationStore.unreadCount > 9
                                ? "9+"
                                : notificationStore.unreadCount
                        }}
                    </span>
                </router-link>
            </div>
        </div>
    </nav>
</template>

<script>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { useNotificationStore } from "../../stores/notifications";

export default {
    name: "Navbar",
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();
        const notificationStore = useNotificationStore();
        const showUserMenu = ref(false);

        const toggleUserMenu = () => {
            showUserMenu.value = !showUserMenu.value;
        };

        const handleLogout = async () => {
            try {
                await authStore.logout();
                router.push("/login");
            } catch (error) {
                console.error("Logout error:", error);
                router.push("/login");
            }
        };

        return {
            authStore,
            notificationStore,
            showUserMenu,
            toggleUserMenu,
            handleLogout,
        };
    },
};
</script>

<style scoped>
.nav-link {
    @apply border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium;
}

.mobile-nav-link {
    @apply text-gray-600 hover:bg-gray-50 hover:text-gray-900 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium;
}
</style>
