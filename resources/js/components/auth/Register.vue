<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2
                    class="mt-6 text-center text-3xl font-extrabold text-gray-900"
                >
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <router-link
                        to="/login"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        sign in to your existing account
                    </router-link>
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
                <div class="space-y-4">
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700"
                            >Full Name</label
                        >
                        <input
                            id="name"
                            name="name"
                            type="text"
                            v-model="form.name"
                            required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter your full name"
                        />
                    </div>

                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700"
                            >Email Address</label
                        >
                        <input
                            id="email"
                            name="email"
                            type="email"
                            v-model="form.email"
                            required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter your email address"
                        />
                    </div>

                    <div>
                        <label
                            for="phone_number"
                            class="block text-sm font-medium text-gray-700"
                            >Phone Number</label
                        >
                        <input
                            id="phone_number"
                            name="phone_number"
                            type="tel"
                            v-model="form.phone_number"
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter your phone number (optional)"
                        />
                    </div>

                    <div>
                        <label
                            for="address"
                            class="block text-sm font-medium text-gray-700"
                            >Address</label
                        >
                        <textarea
                            id="address"
                            name="address"
                            v-model="form.address"
                            rows="2"
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter your address (optional)"
                        ></textarea>
                    </div>

                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700"
                            >Password</label
                        >
                        <input
                            id="password"
                            name="password"
                            type="password"
                            v-model="form.password"
                            required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Create a password"
                        />
                    </div>

                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-medium text-gray-700"
                            >Confirm Password</label
                        >
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Confirm your password"
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
                        {{
                            authStore.isLoading
                                ? "Creating account..."
                                : "Create account"
                        }}
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
    name: "Register",
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();

        const form = reactive({
            name: "",
            email: "",
            phone_number: "",
            address: "",
            password: "",
            password_confirmation: "",
        });

        const handleRegister = async () => {
            try {
                await authStore.register(form);
                router.push("/dashboard");
            } catch (error) {
                console.error("Registration failed:", error);
            }
        };

        return {
            form,
            authStore,
            handleRegister,
        };
    },
};
</script>
