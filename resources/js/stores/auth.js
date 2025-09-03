import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("auth_token"),
        isLoading: false,
        error: null,
        isInitialized: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        getUser: (state) => state.user,
        getError: (state) => state.error,
        isLoading: (state) => state.isLoading,
    },

    actions: {
        /**
         * Login user with email and password
         */
        async login(credentials) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.post("/auth/login", credentials);
                const { user, token } = response.data.data;

                this.user = user;
                this.token = token;

                // Store token in localStorage
                localStorage.setItem("auth_token", token);

                // Set default authorization header
                axios.defaults.headers.common["Authorization"] =
                    `Bearer ${token}`;

                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || "Login failed";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Register new user
         */
        async register(userData) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.post("/auth/register", userData);
                const { user, token } = response.data.data;

                this.user = user;
                this.token = token;

                // Store token in localStorage
                localStorage.setItem("auth_token", token);

                // Set default authorization header
                axios.defaults.headers.common["Authorization"] =
                    `Bearer ${token}`;

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Registration failed";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Logout user
         */
        async logout() {
            try {
                await axios.post("/auth/logout");
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.user = null;
                this.token = null;
                this.error = null;
                this.isInitialized = false;

                // Remove token from localStorage
                localStorage.removeItem("auth_token");

                // Remove authorization header
                delete axios.defaults.headers.common["Authorization"];
            }
        },

        /**
         * Get user profile
         */
        async getProfile() {
            if (!this.token) return;

            try {
                const response = await axios.get("/auth/profile");
                this.user = response.data.data;
            } catch (error) {
                console.error("Failed to get profile:", error);
                this.logout();
            }
        },

        /**
         * Refresh token
         */
        async refreshToken() {
            try {
                const response = await axios.post("/auth/refresh");
                const { token } = response.data.data;

                this.token = token;
                localStorage.setItem("auth_token", token);
                axios.defaults.headers.common["Authorization"] =
                    `Bearer ${token}`;

                return response.data;
            } catch (error) {
                this.logout();
                throw error;
            }
        },

        /**
         * Initialize auth state from localStorage
         */
        initializeAuth() {
            if (this.isInitialized) return;

            const token = localStorage.getItem("auth_token");
            if (token) {
                this.token = token;
                axios.defaults.headers.common["Authorization"] =
                    `Bearer ${token}`;
                this.getProfile();
            }
            this.isInitialized = true;
        },

        async updateProfile(data) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.put("/profile", data);
                this.user = response.data;
                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Profile update failed";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        async changePassword(data) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.put("/profile/password", data);
                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Password change failed";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },
    },
});
