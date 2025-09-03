import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

// Import main component
import App from "./components/App.vue";

// Configure axios
axios.defaults.baseURL = "/api";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Set up axios interceptors for authentication
const token = localStorage.getItem("auth_token");
if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// Configure Echo for real-time broadcasting
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    auth: {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    },
});

// Create Pinia store
const pinia = createPinia();

// Create Vue app
const app = createApp(App);

// Use plugins
app.use(pinia);
app.use(router);

// Make axios available globally
app.config.globalProperties.$http = axios;

// Mount the app
app.mount("#app");
