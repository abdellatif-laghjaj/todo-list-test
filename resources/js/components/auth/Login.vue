<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2
                    class="mt-6 text-center text-3xl font-extrabold text-gray-900"
                >
                    Sign in to your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <router-link
                        to="/register"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        create a new account
                    </router-link>
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            v-model="form.email"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Email address"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            v-model="form.password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Password"
                        />
                    </div>
                </div>

                <!-- Error Message -->
                <div
                    v-if="authStore.error"
                    class="bg-red-50 border border-red-200 rounded-md p-4"
                >
                    <div class="text-sm text-red-800">
                        {{ authStore.error }}
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="authStore.isLoading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span
                            v-if="authStore.isLoading"
                            class="absolute left-0 inset-y-0 flex items-center pl-3"
                        >
                            <svg
                                class="animate-spin h-5 w-5 text-indigo-300"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </span>
                        {{ authStore.isLoading ? "Signing in..." : "Sign in" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";

export default {
    name: "Login",
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();

        const form = reactive({
            email: "",
            password: "",
        });

        const handleLogin = async () => {
            try {
                await authStore.login(form);
                router.push("/dashboard");
            } catch (error) {
                console.error("Login failed:", error);
            }
        };

        return {
            form,
            authStore,
            handleLogin,
        };
    },
};
</script>
