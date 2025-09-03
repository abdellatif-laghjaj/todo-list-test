<template>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div
            class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
        >
            <!-- Backdrop -->
            <div
                @click="$emit('close')"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            ></div>

            <!-- Modal -->
            <div
                class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle bg-white shadow-xl rounded-xl transform transition-all sm:max-w-md"
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ isEditing ? "Edit Task" : "Create New Task" }}
                    </h3>
                    <button
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <XMarkIcon class="w-6 h-6" />
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleSubmit" class="space-y-5">
                    <!-- Title -->
                    <div>
                        <label
                            for="title"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Title *
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter task title"
                            :class="{ 'border-red-300': errors.title }"
                        />
                        <p
                            v-if="errors.title"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.title[0] }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter task description"
                            :class="{ 'border-red-300': errors.description }"
                        ></textarea>
                        <p
                            v-if="errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.description[0] }}
                        </p>
                    </div>

                    <!-- Priority and Status Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Priority -->
                        <div>
                            <label
                                for="priority"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Priority
                            </label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': errors.priority }"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <p
                                v-if="errors.priority"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.priority[0] }}
                            </p>
                        </div>

                        <!-- Status (only for editing) -->
                        <div v-if="isEditing">
                            <label
                                for="status"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Status
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': errors.status }"
                            >
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <p
                                v-if="errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.status[0] }}
                            </p>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label
                            for="due_date"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Due Date
                        </label>
                        <input
                            id="due_date"
                            v-model="form.due_date"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-300': errors.due_date }"
                            :min="today"
                        />
                        <p
                            v-if="errors.due_date"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.due_date[0] }}
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                        >
                            <div
                                v-if="isLoading"
                                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                            ></div>
                            <span>{{
                                isLoading
                                    ? "Saving..."
                                    : isEditing
                                    ? "Update Task"
                                    : "Create Task"
                            }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useTaskStore } from "@/stores/tasks";
import { XMarkIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    task: {
        type: Object,
        default: null,
    },
});

// Emits
const emit = defineEmits(["close", "created", "updated"]);

// Stores
const tasksStore = useTaskStore();

// Reactive data
const isLoading = ref(false);
const errors = ref({});

const form = ref({
    title: "",
    description: "",
    priority: "medium",
    status: "pending",
    due_date: "",
});

// Computed properties
const isEditing = computed(() => !!props.task);

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

// Methods
const handleSubmit = async () => {
    if (isLoading.value) return;

    isLoading.value = true;
    errors.value = {};

    try {
        if (isEditing.value) {
            const updatedTask = await tasksStore.updateTask(
                props.task.id,
                form.value
            );
            emit("updated", updatedTask);
        } else {
            const newTask = await tasksStore.createTask(form.value);
            emit("created", newTask);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
        } else {
            console.error("Form submission error:", error);
            // You could show a general error message here
        }
    } finally {
        isLoading.value = false;
    }
};

const resetForm = () => {
    form.value = {
        title: "",
        description: "",
        priority: "medium",
        status: "pending",
        due_date: "",
    };
};

// Lifecycle
onMounted(() => {
    if (isEditing.value && props.task) {
        form.value = {
            title: props.task.title || "",
            description: props.task.description || "",
            priority: props.task.priority || "medium",
            status: props.task.status || "pending",
            due_date: props.task.due_date || "",
        };
    } else {
        resetForm();
    }
});
</script>
