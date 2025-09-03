<template>
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Welcome back,
                        {{ user?.display_name || user?.name || "User" }}!
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Here's an overview of your tasks and productivity.
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ currentDate }}</p>
                    <p class="text-sm text-gray-500">{{ currentTime }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Tasks -->
            <div
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ClipboardDocumentListIcon
                            class="h-8 w-8 text-blue-600"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">
                            Total Tasks
                        </p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.total }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ClockIcon class="h-8 w-8 text-yellow-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.pending }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- In Progress Tasks -->
            <div
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <PlayIcon class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">
                            In Progress
                        </p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.in_progress }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <CheckCircleIcon class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">
                            Completed
                        </p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.completed }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Overall Progress
                </h3>
                <span class="text-sm font-medium text-gray-600">
                    {{ completionPercentage }}% Complete
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                    class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-300"
                    :style="{ width: `${completionPercentage}%` }"
                ></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-2">
                <span>{{ stats.completed }} completed</span>
                <span>{{ stats.total }} total</span>
            </div>
        </div>

        <!-- Recent Tasks and Upcoming Deadlines -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Tasks -->
            <div
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Recent Tasks
                    </h3>
                    <router-link
                        to="/tasks"
                        class="text-sm text-blue-600 hover:text-blue-500 font-medium"
                    >
                        View all
                    </router-link>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="task in recentTasks"
                        :key="task.id"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                :class="[
                                    'w-3 h-3 rounded-full',
                                    statusColors[task.status],
                                ]"
                            ></div>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 truncate"
                                >
                                    {{ task.title }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ formatRelativeDate(task.created_at) }}
                                </p>
                            </div>
                        </div>
                        <span
                            :class="[
                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                priorityColors[task.priority],
                            ]"
                        >
                            {{ task.priority }}
                        </span>
                    </div>
                    <div
                        v-if="recentTasks.length === 0"
                        class="text-center py-4"
                    >
                        <p class="text-gray-500 text-sm">No recent tasks</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Upcoming Deadlines
                    </h3>
                    <CalendarIcon class="h-5 w-5 text-gray-400" />
                </div>
                <div class="space-y-3">
                    <div
                        v-for="task in upcomingTasks"
                        :key="task.id"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                :class="[
                                    'w-3 h-3 rounded-full',
                                    statusColors[task.status],
                                ]"
                            ></div>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 truncate"
                                >
                                    {{ task.title }}
                                </p>
                                <p
                                    :class="[
                                        'text-xs',
                                        isOverdue(task.due_date)
                                            ? 'text-red-600'
                                            : 'text-gray-500',
                                    ]"
                                >
                                    {{ formatDueDate(task.due_date) }}
                                </p>
                            </div>
                        </div>
                        <ExclamationTriangleIcon
                            v-if="isOverdue(task.due_date)"
                            class="h-4 w-4 text-red-500"
                        />
                    </div>
                    <div
                        v-if="upcomingTasks.length === 0"
                        class="text-center py-4"
                    >
                        <p class="text-gray-500 text-sm">
                            No upcoming deadlines
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Quick Actions
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button
                    @click="showCreateTask = true"
                    class="flex items-center justify-center p-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                >
                    <PlusIcon class="h-5 w-5 mr-2" />
                    New Task
                </button>
                <router-link
                    to="/tasks?status=pending"
                    class="flex items-center justify-center p-4 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors"
                >
                    <ClockIcon class="h-5 w-5 mr-2" />
                    View Pending
                </router-link>
                <router-link
                    to="/tasks?status=in_progress"
                    class="flex items-center justify-center p-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                >
                    <PlayIcon class="h-5 w-5 mr-2" />
                    In Progress
                </router-link>
                <router-link
                    to="/tasks?status=completed"
                    class="flex items-center justify-center p-4 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors"
                >
                    <CheckCircleIcon class="h-5 w-5 mr-2" />
                    Completed
                </router-link>
            </div>
        </div>

        <!-- Task Creation Modal -->
        <TaskForm
            v-if="showCreateTask"
            @close="showCreateTask = false"
            @created="handleTaskCreated"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useTaskStore } from "@/stores/tasks";
import { useNotificationStore } from "@/stores/notifications";
import TaskForm from "@/components/TaskForm.vue";
import {
    ClipboardDocumentListIcon,
    ClockIcon,
    PlayIcon,
    CheckCircleIcon,
    CalendarIcon,
    PlusIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";

// Stores
const authStore = useAuthStore();
const tasksStore = useTaskStore();
const notificationsStore = useNotificationStore();

// Reactive data
const showCreateTask = ref(false);
const currentTime = ref("");
const timeInterval = ref(null);

// Computed properties
const user = computed(() => authStore.user);

const currentDate = computed(() => {
    return new Date().toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

const stats = computed(() => {
    const tasks = tasksStore.tasks;
    return {
        total: tasks.length,
        pending: tasks.filter((task) => task.status === "pending").length,
        in_progress: tasks.filter((task) => task.status === "in_progress")
            .length,
        completed: tasks.filter((task) => task.status === "completed").length,
    };
});

const completionPercentage = computed(() => {
    if (stats.value.total === 0) return 0;
    return Math.round((stats.value.completed / stats.value.total) * 100);
});

const recentTasks = computed(() => {
    return tasksStore.tasks
        .slice()
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 5);
});

const upcomingTasks = computed(() => {
    const now = new Date();
    const weekFromNow = new Date();
    weekFromNow.setDate(weekFromNow.getDate() + 7);

    return tasksStore.tasks
        .filter((task) => task.due_date && task.status !== "completed")
        .sort((a, b) => new Date(a.due_date) - new Date(b.due_date))
        .slice(0, 5);
});

const statusColors = {
    pending: "bg-gray-400",
    in_progress: "bg-blue-500",
    completed: "bg-green-500",
};

const priorityColors = {
    low: "bg-gray-100 text-gray-800",
    medium: "bg-yellow-100 text-yellow-800",
    high: "bg-red-100 text-red-800",
};

// Methods
const updateTime = () => {
    currentTime.value = new Date().toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
};

const formatRelativeDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));

    if (diffInHours < 1) return "Just now";
    if (diffInHours < 24) return `${diffInHours}h ago`;

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}d ago`;

    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
    });
};

const formatDueDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInDays = Math.ceil((date - now) / (1000 * 60 * 60 * 24));

    if (diffInDays < 0) return `Overdue by ${Math.abs(diffInDays)} days`;
    if (diffInDays === 0) return "Due today";
    if (diffInDays === 1) return "Due tomorrow";
    if (diffInDays < 7) return `Due in ${diffInDays} days`;

    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
    });
};

const isOverdue = (dateString) => {
    return new Date(dateString) < new Date();
};

const handleTaskCreated = (task) => {
    showCreateTask.value = false;
    notificationsStore.addNotification({
        type: "success",
        title: "Task Created",
        message: `Task "${task.title}" has been created successfully.`,
    });
};

// Lifecycle
onMounted(() => {
    tasksStore.fetchTasks();
    updateTime();
    timeInterval.value = setInterval(updateTime, 1000);
});

onUnmounted(() => {
    if (timeInterval.value) {
        clearInterval(timeInterval.value);
    }
});
</script>
