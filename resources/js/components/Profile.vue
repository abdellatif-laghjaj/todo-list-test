<template>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 mx-auto">
            <div>
                <h2
                    class="mt-6 text-center text-3xl font-extrabold text-gray-900"
                >
                    Profile
                </h2>
            </div>

            <!-- Update Profile Form -->
            <form class="mt-8 space-y-6" @submit.prevent="updateProfile">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="name" class="sr-only">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            v-model="profileForm.name"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Name"
                        />
                    </div>
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            v-model="profileForm.email"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Email address"
                        />
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
                                ? "Updating..."
                                : "Update Profile"
                        }}
                    </button>
                </div>
            </form>

            <div class="border-t border-gray-200"></div>

            <!-- Change Password Form -->
            <form class="mt-8 space-y-6" @submit.prevent="changePassword">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="current_password" class="sr-only"
                            >Current Password</label
                        >
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            v-model="passwordForm.current_password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Current Password"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only"
                            >New Password</label
                        >
                        <input
                            id="password"
                            name="password"
                            type="password"
                            v-model="passwordForm.password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="New Password"
                        />
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only"
                            >Confirm New Password</label
                        >
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            v-model="passwordForm.password_confirmation"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Confirm New Password"
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
                                ? "Changing..."
                                : "Change Password"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const profileForm = ref({
    name: "",
    email: "",
});

const passwordForm = ref({
    current_password: "",
    password: "",
    password_confirmation: "",
});

onMounted(() => {
    if (authStore.user) {
        profileForm.value.name = authStore.user.name;
        profileForm.value.email = authStore.user.email;
    }
});

const updateProfile = async () => {
    try {
        await authStore.updateProfile(profileForm.value);
        alert("Profile updated successfully!");
    } catch (error) {
        // Error is handled by the store
    }
};

const changePassword = async () => {
    try {
        await authStore.changePassword(passwordForm.value);
        alert(
            "Password changed successfully! Please log in again with your new password.",
        );
        await authStore.logout();
        router.push("/login");
    } catch (error) {
        // Error is handled by the store
    }
};
</script>
