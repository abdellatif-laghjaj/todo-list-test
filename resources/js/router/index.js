import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Import components
import Login from "../components/auth/Login.vue";
import Register from "../components/auth/Register.vue";
import Dashboard from "../components/Dashboard.vue";
import TaskList from "../components/TaskList.vue";
import NotificationPage from "../components/NotificationPage.vue";

const routes = [
    {
        path: "/",
        redirect: (to) => {
            // Check if user is authenticated
            const token = localStorage.getItem("auth_token");
            return token ? "/dashboard" : "/login";
        },
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
        meta: {
            requiresGuest: true,
            title: "Login",
        },
    },
    {
        path: "/register",
        name: "Register",
        component: Register,
        meta: {
            requiresGuest: true,
            title: "Register",
        },
    },
    {
        path: "/dashboard",
        name: "Dashboard",
        component: Dashboard,
        meta: {
            requiresAuth: true,
            title: "Dashboard",
        },
    },
    {
        path: "/tasks",
        name: "Tasks",
        component: TaskList,
        meta: {
            requiresAuth: true,
            title: "Tasks",
        },
    },
    {
        path: "/notifications",
        name: "Notifications",
        component: NotificationPage,
        meta: {
            requiresAuth: true,
            title: "Notifications",
        },
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/dashboard",
    },
];

const router = createRouter({
    history: createWebHistory("/app"),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    // Initialize auth store if not already done
    if (!authStore.isInitialized) {
        authStore.initializeAuth();
    }

    // Set page title
    document.title = to.meta.title
        ? `${to.meta.title} - To-Do List`
        : "To-Do List";

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next("/login");
    } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next("/dashboard");
    } else {
        next();
    }
});

export default router;
