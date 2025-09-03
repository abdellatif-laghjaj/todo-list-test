<template>
    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="card card-padding mb-8 slide-up">
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
            >
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Welcome back,
                        {{ user?.display_name || user?.name || "User" }}!
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Here's an overview of your tasks and productivity.
                    </p>
                </div>
                <div
                    class="flex flex-col sm:flex-row sm:items-center gap-4 text-right"
                >
                    <div
                        class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-4 py-3 rounded-lg border border-indigo-100"
                    >
                        <p class="text-sm font-medium text-gray-700">
                            {{ currentDate }}
                        </p>
                        <p class="text-2xl font-bold text-gray-600">
                            {{ currentTime }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid mb-8">
            <!-- Total Tasks -->
            <div
                class="card card-padding-sm transition-all duration-300 fade-in"
                style="animation-delay: 0.1s"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center"
                            >
                                <ClipboardDocumentListIcon
                                    class="h-6 w-6 text-indigo-600"
                                />
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Total Tasks
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.total }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">All Time</div>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div
                class="card card-padding-sm transition-all duration-300 fade-in"
                style="animation-delay: 0.2s"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center"
                            >
                                <ClockIcon class="h-6 w-6 text-yellow-600" />
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Pending
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.pending }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-yellow-600 font-medium">
                            {{ pendingPercentage }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- In Progress Tasks -->
            <div
                class="card card-padding-sm transition-all duration-300 fade-in"
                style="animation-delay: 0.3s"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center"
                            >
                                <PlayIcon class="h-6 w-6 text-indigo-600" />
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                In Progress
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.in_progress }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-indigo-600 font-medium">
                            {{ inProgressPercentage }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div
                class="card card-padding-sm transition-all duration-300 fade-in"
                style="animation-delay: 0.4s"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center"
                            >
                                <CheckCircleIcon
                                    class="h-6 w-6 text-green-600"
                                />
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Completed
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.completed }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-green-600 font-medium">
                            {{ completionPercentage }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Section -->
        <div
            class="card card-padding mb-8 fade-in"
            style="animation-delay: 0.5s"
        >
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-1">
                        Overall Progress
                    </h3>
                    <p class="text-gray-600">Track your productivity journey</p>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-bold text-indigo-600">
                        {{ completionPercentage }}%
                    </span>
                    <p class="text-sm text-gray-500">Complete</p>
                </div>
            </div>

            <div class="progress-bar mb-4">
                <div
                    class="progress-fill"
                    :style="{ width: `${completionPercentage}%` }"
                ></div>
            </div>

            <div class="flex justify-between items-center text-sm">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-600"
                            >{{ stats.completed }} completed</span
                        >
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                        <span class="text-gray-600"
                            >{{ stats.in_progress }} in progress</span
                        >
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                        <span class="text-gray-600"
                            >{{ stats.pending }} pending</span
                        >
                    </div>
                </div>
                <span class="font-medium text-gray-700"
                    >{{ stats.total }} total</span
                >
            </div>
        </div>

        <!-- Recent Tasks and Upcoming Deadlines -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Recent Tasks -->
            <div
                class="card card-padding fade-in"
                style="animation-delay: 0.6s"
            >
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-1">
                            Recent Tasks
                        </h3>
                        <p class="text-gray-600">Your latest activity</p>
                    </div>
                    <router-link
                        to="/tasks"
                        class="btn btn-ghost text-sm font-medium"
                    >
                        View all →
                    </router-link>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(task, index) in recentTasks"
                        :key="task.id"
                        class="task-card p-4 transition-all duration-200"
                        :class="{
                            'task-completed': task.status === 'completed',
                            'task-high-priority': task.priority === 'high',
                            'task-medium-priority': task.priority === 'medium',
                            'task-low-priority': task.priority === 'low',
                        }"
                        :style="{ animationDelay: `${0.7 + index * 0.1}s` }"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="flex items-center space-x-3 flex-1 min-w-0"
                            >
                                <div
                                    class="status-dot"
                                    :class="task.status"
                                ></div>
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-sm font-semibold text-gray-900 truncate"
                                    >
                                        {{ task.title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{
                                            formatRelativeDate(task.created_at)
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="priority-badge" :class="task.priority">
                                {{ task.priority }}
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="recentTasks.length === 0"
                        class="text-center py-8"
                    >
                        <div class="text-gray-400 mb-3">
                            <ClipboardDocumentListIcon
                                class="w-12 h-12 mx-auto"
                            />
                        </div>
                        <p class="text-gray-500">No recent tasks</p>
                        <button
                            @click="showCreateTask = true"
                            class="btn btn-primary mt-3"
                        >
                            Create your first task
                        </button>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div
                class="card card-padding fade-in"
                style="animation-delay: 0.7s"
            >
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-1">
                            Upcoming Deadlines
                        </h3>
                        <p class="text-gray-600">Tasks due soon</p>
                    </div>
                    <CalendarIcon class="h-6 w-6 text-gray-400" />
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(task, index) in upcomingTasks"
                        :key="task.id"
                        class="task-card p-4 transition-all duration-200"
                        :class="{
                            'border-red-200 bg-red-50': isOverdue(
                                task.due_date
                            ),
                            'border-yellow-200 bg-yellow-50': isDueSoon(
                                task.due_date
                            ),
                        }"
                        :style="{ animationDelay: `${0.8 + index * 0.1}s` }"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="flex items-center space-x-3 flex-1 min-w-0"
                            >
                                <div
                                    class="status-dot"
                                    :class="task.status"
                                ></div>
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-sm font-semibold text-gray-900 truncate"
                                    >
                                        {{ task.title }}
                                    </h4>
                                    <p
                                        class="text-xs mt-1 font-medium"
                                        :class="
                                            isOverdue(task.due_date)
                                                ? 'text-red-600'
                                                : isDueSoon(task.due_date)
                                                ? 'text-yellow-600'
                                                : 'text-gray-500'
                                        "
                                    >
                                        {{ formatDueDate(task.due_date) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <ExclamationTriangleIcon
                                    v-if="isOverdue(task.due_date)"
                                    class="h-5 w-5 text-red-500"
                                />
                                <div
                                    class="priority-badge"
                                    :class="task.priority"
                                >
                                    {{ task.priority }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="upcomingTasks.length === 0"
                        class="text-center py-8"
                    >
                        <div class="text-gray-400 mb-3">
                            <CalendarIcon class="w-12 h-12 mx-auto" />
                        </div>
                        <p class="text-gray-500">No upcoming deadlines</p>
                        <p class="text-xs text-gray-400 mt-1">
                            Great job staying on top of things!
                        </p>
                    </div>
                </div>
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

const pendingPercentage = computed(() => {
    if (stats.value.total === 0) return 0;
    return Math.round((stats.value.pending / stats.value.total) * 100);
});

const inProgressPercentage = computed(() => {
    if (stats.value.total === 0) return 0;
    return Math.round((stats.value.in_progress / stats.value.total) * 100);
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
    in_progress: "bg-indigo-500",
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

const isDueSoon = (dateString) => {
    const now = new Date();
    const dueDate = new Date(dateString);
    const diffInDays = Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24));
    return diffInDays <= 2 && diffInDays >= 0;
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

<style scoped>
/* Enhanced animations */
.fade-in {
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
}

.slide-up {
    opacity: 0;
    transform: translateY(20px);
    animation: slideUpFade 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUpFade {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card hover effects */

/* Responsive design improvements */
@media (max-width: 640px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Status indicator improvements */
.status-dot.pending {
    background: rgb(156 163 175);
    box-shadow: 0 0 0 2px rgba(156, 163, 175, 0.2);
}

.status-dot.in-progress {
    background: rgb(99 102 241);
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

.status-dot.completed {
    background: rgb(34 197 94);
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}
</style>
