<template>
    <nav class="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Navigation -->
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <router-link
                            to="/dashboard"
                            class="text-xl font-bold text-indigo-600 hover:text-indigo-700 transition-colors"
                        >
                            TodoApp
                        </router-link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex md:space-x-1">
                        <router-link
                            to="/dashboard"
                            class="nav-link"
                            :class="{
                                active: $route.name === 'Dashboard',
                            }"
                        >
                            Dashboard
                        </router-link>
                        <router-link
                            to="/tasks"
                            class="nav-link"
                            :class="{
                                active: $route.name === 'Tasks',
                            }"
                        >
                            Tasks
                        </router-link>
                        <router-link
                            to="/notifications"
                            class="nav-link relative"
                            :class="{
                                active: $route.name === 'Notifications',
                            }"
                        >
                            Notifications
                            <!-- Notification Badge -->
                            <span
                                v-if="notificationStore.unreadCount > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold animate-pulse"
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
                    <!-- Mobile Menu Button -->
                    <button
                        @click="toggleMobileMenu"
                        class="md:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all"
                        aria-label="Toggle mobile menu"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                v-if="!showMobileMenu"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown" ref="userDropdown">
                        <button
                            @click="toggleUserMenu"
                            class="dropdown-trigger ml-2 focus:outline-none"
                            aria-haspopup="true"
                            :aria-expanded="showUserMenu"
                        >
                            <!-- User Avatar -->
                            <div
                                class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md"
                            >
                                <span class="text-white font-semibold text-sm">
                                    {{
                                        authStore.user?.name
                                            ?.charAt(0)
                                            .toUpperCase() || "U"
                                    }}
                                </span>
                            </div>
                            <!-- User Name (Desktop) -->
                            <span
                                class="ml-3 text-gray-700 font-medium hidden md:block"
                            >
                                {{ authStore.user?.name || "User" }}
                            </span>
                            <!-- Chevron Icon -->
                            <svg
                                class="ml-2 h-4 w-4 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': showUserMenu }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            class="dropdown-menu"
                            :class="{ open: showUserMenu }"
                            role="menu"
                            aria-orientation="vertical"
                        >
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="font-semibold text-gray-900">
                                    {{ authStore.user?.name || "User" }}
                                </div>
                                <div class="text-sm text-gray-500 truncate">
                                    {{
                                        authStore.user?.email ||
                                        "user@example.com"
                                    }}
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <button
                                @click="handleLogout"
                                class="dropdown-item w-full text-left text-red-600 hover:bg-red-50 flex items-center"
                                role="menuitem"
                            >
                                <svg
                                    class="w-4 h-4 mr-3"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>
                                Sign out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <transition name="slide-down">
            <div
                v-if="showMobileMenu"
                class="md:hidden border-t border-gray-200 bg-white"
            >
                <div class="px-4 py-3 space-y-2">
                    <router-link
                        to="/dashboard"
                        class="mobile-nav-link"
                        :class="{
                            'bg-indigo-50 border-indigo-500 text-indigo-700':
                                $route.name === 'Dashboard',
                        }"
                        @click="closeMobileMenu"
                    >
                        Dashboard
                    </router-link>
                    <router-link
                        to="/tasks"
                        class="mobile-nav-link"
                        :class="{
                            'bg-indigo-50 border-indigo-500 text-indigo-700':
                                $route.name === 'Tasks',
                        }"
                        @click="closeMobileMenu"
                    >
                        Tasks
                    </router-link>
                    <router-link
                        to="/notifications"
                        class="mobile-nav-link flex items-center justify-between"
                        :class="{
                            'bg-indigo-50 border-indigo-500 text-indigo-700':
                                $route.name === 'Notifications',
                        }"
                        @click="closeMobileMenu"
                    >
                        <span>Notifications</span>
                        <span
                            v-if="notificationStore.unreadCount > 0"
                            class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold"
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
        </transition>
    </nav>
</template>

<script>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { useNotificationStore } from "../../stores/notifications";

export default {
    name: "Navbar",
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();
        const notificationStore = useNotificationStore();

        // Reactive data
        const showUserMenu = ref(false);
        const showMobileMenu = ref(false);
        const userDropdown = ref(null);

        // Methods
        const toggleUserMenu = () => {
            showUserMenu.value = !showUserMenu.value;
            // Close mobile menu if open
            if (showUserMenu.value) {
                showMobileMenu.value = false;
            }
        };

        const toggleMobileMenu = () => {
            showMobileMenu.value = !showMobileMenu.value;
            // Close user menu if open
            if (showMobileMenu.value) {
                showUserMenu.value = false;
            }
        };

        const closeUserMenu = () => {
            showUserMenu.value = false;
        };

        const closeMobileMenu = () => {
            showMobileMenu.value = false;
        };

        const closeAllMenus = () => {
            showUserMenu.value = false;
            showMobileMenu.value = false;
        };

        const handleLogout = async () => {
            closeAllMenus();
            try {
                await authStore.logout();
                router.push("/login");
            } catch (error) {
                console.error("Logout error:", error);
                router.push("/login");
            }
        };

        // Click outside handler
        const handleClickOutside = (event) => {
            // Close user dropdown if clicking outside
            if (
                userDropdown.value &&
                !userDropdown.value.contains(event.target)
            ) {
                showUserMenu.value = false;
            }

            // Close mobile menu if clicking outside (for tablet/desktop views)
            if (showMobileMenu.value && !event.target.closest(".navbar")) {
                showMobileMenu.value = false;
            }
        };

        // Handle escape key
        const handleEscapeKey = (event) => {
            if (event.key === "Escape") {
                closeAllMenus();
            }
        };

        // Handle route changes
        const handleRouteChange = () => {
            closeAllMenus();
        };

        // Lifecycle hooks
        onMounted(() => {
            document.addEventListener("click", handleClickOutside);
            document.addEventListener("keydown", handleEscapeKey);
            router.afterEach(handleRouteChange);
        });

        onUnmounted(() => {
            document.removeEventListener("click", handleClickOutside);
            document.removeEventListener("keydown", handleEscapeKey);
        });

        return {
            authStore,
            notificationStore,
            showUserMenu,
            showMobileMenu,
            userDropdown,
            toggleUserMenu,
            toggleMobileMenu,
            closeUserMenu,
            closeMobileMenu,
            handleLogout,
        };
    },
};
</script>

<style scoped>
/* Mobile Navigation Link Styles */
.mobile-nav-link {
    @apply text-gray-600 hover:bg-gray-50 hover:text-gray-900 block pl-3 pr-4 py-3 border-l-4 border-transparent text-base font-medium transition-all duration-200;
}

/* Mobile menu slide animation */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease-in-out;
    transform-origin: top;
}

.slide-down-enter-from {
    opacity: 0;
    transform: scaleY(0.95) translateY(-10px);
}

.slide-down-leave-to {
    opacity: 0;
    transform: scaleY(0.95) translateY(-10px);
}

/* Ensure proper z-index for dropdowns */
.dropdown-menu {
    z-index: var(--z-dropdown, 1000);
}

/* Smooth rotation for chevron */
.dropdown-trigger svg {
    transition: transform var(--transition-normal, 0.25s ease-in-out);
}

/* Focus styles for accessibility */
.dropdown-trigger:focus-visible {
    outline: 2px solid rgb(99 102 241);
    outline-offset: 2px;
}

/* Enhanced hover effects */
.dropdown-trigger:hover {
    background-color: rgba(243, 244, 246, 0.8);
}

/* Mobile menu button animation */
.md\:hidden button svg {
    transition: all 0.2s ease-in-out;
}

/* Notification badge pulse animation */
@keyframes pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Enhanced user avatar gradient */
.gradient-avatar {
    background: linear-gradient(
        135deg,
        rgb(99 102 241) 0%,
        rgb(139 92 246) 100%
    );
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

/* Backdrop blur for navbar */
.navbar {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .navbar {
        padding: 0 1rem;
    }
}
</style>
