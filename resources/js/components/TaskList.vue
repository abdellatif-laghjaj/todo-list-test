<template>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div
            class="card card-padding mb-8 fade-in"
            style="animation-delay: 0.1s"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        My Tasks
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Manage and organize your tasks efficiently
                    </p>
                    <div class="flex items-center gap-4 mt-3">
                        <div class="text-sm text-gray-500">
                            <span class="font-semibold text-gray-900">{{
                                filteredTasks.length
                            }}</span>
                            {{ filteredTasks.length === 1 ? "task" : "tasks" }}
                            {{
                                activeFiltersText
                                    ? `(${activeFiltersText})`
                                    : ""
                            }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        @click="clearAllFilters"
                        v-if="hasActiveFilters"
                        class="btn btn-secondary"
                    >
                        Clear Filters
                    </button>
                    <button
                        @click="showCreateForm = true"
                        class="btn btn-primary"
                    >
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Add Task
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div
            class="card card-padding mb-8 fade-in"
            style="animation-delay: 0.2s"
        >
            <div class="space-y-4">
                <!-- Status Filters -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                        Status
                    </h4>
                    <div class="filter-pills">
                        <button
                            v-for="status in statusFilters"
                            :key="status.value"
                            @click="selectedStatus = status.value"
                            class="filter-pill"
                            :class="{ active: selectedStatus === status.value }"
                        >
                            <span
                                class="status-dot"
                                :class="
                                    status.value === 'all'
                                        ? 'bg-gray-400'
                                        : status.value.replace('_', '-')
                                "
                                v-if="status.value !== 'all'"
                            ></span>
                            {{ status.label }}
                            <span
                                v-if="status.count !== undefined"
                                class="ml-1 text-xs opacity-75"
                                >({{ status.count }})</span
                            >
                        </button>
                    </div>
                </div>

                <!-- Priority Filters -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                        Priority
                    </h4>
                    <div class="filter-pills">
                        <button
                            v-for="priority in priorityFilters"
                            :key="priority.value"
                            @click="selectedPriority = priority.value"
                            class="filter-pill"
                            :class="{
                                active: selectedPriority === priority.value,
                            }"
                        >
                            <div
                                v-if="priority.value !== 'all'"
                                class="priority-badge"
                                :class="priority.value"
                            >
                                {{ priority.value }}
                            </div>
                            <span v-else>{{ priority.label }}</span>
                            <span
                                v-if="priority.count !== undefined"
                                class="ml-1 text-xs opacity-75"
                                >({{ priority.count }})</span
                            >
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                        Search
                    </h4>
                    <div class="relative max-w-md">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search tasks by title or description..."
                            class="form-input pr-10"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
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
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center py-12">
                <div class="loading-spinner"></div>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="filteredTasks.length === 0"
                class="card card-padding text-center py-12 fade-in"
                style="animation-delay: 0.3s"
            >
                <div class="text-gray-400 mb-4">
                    <CheckCircleIcon class="w-16 h-16 mx-auto" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ getEmptyStateTitle }}
                </h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    {{ getEmptyStateDescription }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button
                        v-if="hasActiveFilters"
                        @click="clearAllFilters"
                        class="btn btn-secondary"
                    >
                        Clear Filters
                    </button>
                    <button
                        @click="showCreateForm = true"
                        class="btn btn-primary"
                    >
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Create Task
                    </button>
                </div>
            </div>

            <!-- Tasks Grid -->
            <TransitionGroup v-else name="list" tag="div" class="grid gap-4">
                <TaskItem
                    v-for="(task, index) in paginatedTasks"
                    :key="task.id"
                    :task="task"
                    class="fade-in"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                    @edit="openEditForm"
                    @delete="handleDeleteTask"
                    @toggle-status="handleToggleStatus"
                />
            </TransitionGroup>
        </div>

        <!-- Pagination -->
        <div
            v-if="totalPages > 1"
            class="card card-padding mt-8 fade-in"
            style="animation-delay: 0.4s"
        >
            <div
                class="flex flex-col sm:flex-row items-center justify-between gap-4"
            >
                <div class="text-sm text-gray-600">
                    Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                    {{
                        Math.min(
                            currentPage * itemsPerPage,
                            filteredTasks.length
                        )
                    }}
                    of {{ filteredTasks.length }} results
                </div>

                <div class="flex items-center gap-2">
                    <button
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="btn btn-secondary px-3 py-2"
                        :class="{
                            'opacity-50 cursor-not-allowed': currentPage === 1,
                        }"
                    >
                        Previous
                    </button>

                    <div class="flex gap-1">
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            @click="currentPage = page"
                            class="btn px-3 py-2"
                            :class="
                                currentPage === page
                                    ? 'btn-primary'
                                    : 'btn-secondary'
                            "
                        >
                            {{ page }}
                        </button>
                    </div>

                    <button
                        @click="
                            currentPage = Math.min(totalPages, currentPage + 1)
                        "
                        :disabled="currentPage === totalPages"
                        class="btn btn-secondary px-3 py-2"
                        :class="{
                            'opacity-50 cursor-not-allowed':
                                currentPage === totalPages,
                        }"
                    >
                        Next
                    </button>
                </div>
            </div>
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

const statusFilters = computed(() => [
    { value: "all", label: "All", count: tasksStore.tasks.length },
    {
        value: "pending",
        label: "Pending",
        count: tasksStore.tasks.filter((t) => t.status === "pending").length,
    },
    {
        value: "in_progress",
        label: "In Progress",
        count: tasksStore.tasks.filter((t) => t.status === "in_progress")
            .length,
    },
    {
        value: "completed",
        label: "Completed",
        count: tasksStore.tasks.filter((t) => t.status === "completed").length,
    },
]);

const priorityFilters = computed(() => [
    { value: "all", label: "All Priority", count: tasksStore.tasks.length },
    {
        value: "low",
        label: "Low",
        count: tasksStore.tasks.filter((t) => t.priority === "low").length,
    },
    {
        value: "medium",
        label: "Medium",
        count: tasksStore.tasks.filter((t) => t.priority === "medium").length,
    },
    {
        value: "high",
        label: "High",
        count: tasksStore.tasks.filter((t) => t.priority === "high").length,
    },
]);

const hasActiveFilters = computed(() => {
    return (
        selectedStatus.value !== "all" ||
        selectedPriority.value !== "all" ||
        searchQuery.value.trim() !== ""
    );
});

const activeFiltersText = computed(() => {
    const filters = [];
    if (selectedStatus.value !== "all") {
        filters.push(
            statusFilters.value.find((s) => s.value === selectedStatus.value)
                ?.label
        );
    }
    if (selectedPriority.value !== "all") {
        filters.push(
            priorityFilters.value.find(
                (p) => p.value === selectedPriority.value
            )?.label
        );
    }
    if (searchQuery.value.trim()) {
        filters.push(`"${searchQuery.value}"`);
    }
    return filters.join(", ");
});

const getEmptyStateTitle = computed(() => {
    if (hasActiveFilters.value) {
        return "No tasks match your filters";
    }
    return "No tasks found";
});

const getEmptyStateDescription = computed(() => {
    if (hasActiveFilters.value) {
        return "Try adjusting your filters to see more results, or create a new task.";
    }
    return "Get started by creating your first task. Stay organized and productive!";
});

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

const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredTasks.value.slice(start, end);
});

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
const clearAllFilters = () => {
    selectedStatus.value = "all";
    selectedPriority.value = "all";
    searchQuery.value = "";
    currentPage.value = 1;
};

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
/* Enhanced animations */
.fade-in {
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

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
