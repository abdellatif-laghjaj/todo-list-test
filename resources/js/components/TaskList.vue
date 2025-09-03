<template>
    <div class="space-y-6">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Tasks</h1>
                <p class="text-gray-600">
                    {{ filteredTasks.length }}
                    {{ filteredTasks.length === 1 ? "task" : "tasks" }}
                </p>
            </div>
            <button
                @click="showCreateForm = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
            >
                <PlusIcon class="w-4 h-4" />
                <span>Add Task</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex space-x-2">
                <button
                    v-for="status in statusFilters"
                    :key="status.value"
                    @click="selectedStatus = status.value"
                    :class="[
                        'px-3 py-1 rounded-full text-sm font-medium transition-colors',
                        selectedStatus === status.value
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                    ]"
                >
                    {{ status.label }}
                </button>
            </div>
            <div class="flex space-x-2">
                <button
                    v-for="priority in priorityFilters"
                    :key="priority.value"
                    @click="selectedPriority = priority.value"
                    :class="[
                        'px-3 py-1 rounded-full text-sm font-medium transition-colors',
                        selectedPriority === priority.value
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                    ]"
                >
                    {{ priority.label }}
                </button>
            </div>
            <div class="flex-1 max-w-md">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search tasks..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>
        </div>

        <!-- Task Creation Form -->
        <TaskForm
            v-if="showCreateForm"
            @close="showCreateForm = false"
            @created="handleTaskCreated"
        />

        <!-- Task Edit Form -->
        <TaskForm
            v-if="showEditForm && editingTask"
            :task="editingTask"
            @close="closeEditForm"
            @updated="handleTaskUpdated"
        />

        <!-- Tasks List -->
        <div class="space-y-4">
            <div v-if="isLoading" class="flex justify-center py-8">
                <div
                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                ></div>
            </div>

            <div
                v-else-if="filteredTasks.length === 0"
                class="text-center py-8"
            >
                <div class="text-gray-400 mb-2">
                    <CheckCircleIcon class="w-16 h-16 mx-auto" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">
                    No tasks found
                </h3>
                <p class="text-gray-600">
                    {{
                        searchQuery ||
                        selectedStatus !== "all" ||
                        selectedPriority !== "all"
                            ? "Try adjusting your filters"
                            : "Create your first task to get started"
                    }}
                </p>
            </div>

            <TransitionGroup v-else name="list" tag="div" class="space-y-4">
                <TaskItem
                    v-for="task in filteredTasks"
                    :key="task.id"
                    :task="task"
                    @edit="openEditForm"
                    @delete="handleDeleteTask"
                    @toggle-status="handleToggleStatus"
                />
            </TransitionGroup>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center space-x-2">
            <button
                v-for="page in visiblePages"
                :key="page"
                @click="currentPage = page"
                :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium',
                    currentPage === page
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                ]"
            >
                {{ page }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useTaskStore } from "@/stores/tasks";
import { useNotificationStore } from "@/stores/notifications";
import TaskForm from "@/components/TaskForm.vue";
import TaskItem from "@/components/TaskItem.vue";
import { PlusIcon, CheckCircleIcon } from "@heroicons/vue/24/outline";

// Stores
const tasksStore = useTaskStore();
const notificationsStore = useNotificationStore();

// Reactive data
const showCreateForm = ref(false);
const showEditForm = ref(false);
const editingTask = ref(null);
const searchQuery = ref("");
const selectedStatus = ref("all");
const selectedPriority = ref("all");
const currentPage = ref(1);
const itemsPerPage = 10;

// Computed properties
const isLoading = computed(() => tasksStore.isLoading);

const statusFilters = [
    { value: "all", label: "All" },
    { value: "pending", label: "Pending" },
    { value: "in_progress", label: "In Progress" },
    { value: "completed", label: "Completed" },
];

const priorityFilters = [
    { value: "all", label: "All Priority" },
    { value: "low", label: "Low" },
    { value: "medium", label: "Medium" },
    { value: "high", label: "High" },
];

const filteredTasks = computed(() => {
    let tasks = tasksStore.tasks;

    // Filter by status
    if (selectedStatus.value !== "all") {
        tasks = tasks.filter((task) => task.status === selectedStatus.value);
    }

    // Filter by priority
    if (selectedPriority.value !== "all") {
        tasks = tasks.filter(
            (task) => task.priority === selectedPriority.value
        );
    }

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        tasks = tasks.filter(
            (task) =>
                task.title.toLowerCase().includes(query) ||
                task.description.toLowerCase().includes(query)
        );
    }

    // Sort by created_at (newest first)
    return tasks.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
    );
});

const totalPages = computed(() =>
    Math.ceil(filteredTasks.value.length / itemsPerPage)
);

const visiblePages = computed(() => {
    const pages = [];
    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(totalPages.value, start + 4);

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});

// Methods
const openEditForm = (task) => {
    editingTask.value = task;
    showEditForm.value = true;
};

const closeEditForm = () => {
    showEditForm.value = false;
    editingTask.value = null;
};

const handleTaskCreated = (task) => {
    showCreateForm.value = false;
    notificationsStore.addNotification({
        type: "success",
        title: "Task Created",
        message: `Task "${task.title}" has been created successfully.`,
    });
};

const handleTaskUpdated = (task) => {
    closeEditForm();
    notificationsStore.addNotification({
        type: "success",
        title: "Task Updated",
        message: `Task "${task.title}" has been updated successfully.`,
    });
};

const handleDeleteTask = async (task) => {
    if (confirm(`Are you sure you want to delete "${task.title}"?`)) {
        try {
            await tasksStore.deleteTask(task.id);
            notificationsStore.addNotification({
                type: "success",
                title: "Task Deleted",
                message: `Task "${task.title}" has been deleted.`,
            });
        } catch (error) {
            notificationsStore.addNotification({
                type: "error",
                title: "Delete Failed",
                message: error.message || "Failed to delete task.",
            });
        }
    }
};

const handleToggleStatus = async (task) => {
    try {
        const newStatus = task.status === "completed" ? "pending" : "completed";
        await tasksStore.updateTask(task.id, { status: newStatus });

        notificationsStore.addNotification({
            type: "success",
            title: "Task Updated",
            message: `Task marked as ${
                newStatus === "completed" ? "completed" : "pending"
            }.`,
        });
    } catch (error) {
        notificationsStore.addNotification({
            type: "error",
            title: "Update Failed",
            message: error.message || "Failed to update task status.",
        });
    }
};

// Watch for filter changes and reset pagination
watch([selectedStatus, selectedPriority, searchQuery], () => {
    currentPage.value = 1;
});

// Lifecycle
onMounted(() => {
    tasksStore.fetchTasks();
});
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}

.list-leave-to {
    opacity: 0;
    transform: translateX(20px);
}

.list-move {
    transition: transform 0.3s ease;
}
</style>
