<template>
    <div
        class="task-card"
        :class="{
            'task-completed': task.status === 'completed',
            'task-high-priority': task.priority === 'high',
            'task-medium-priority': task.priority === 'medium',
            'task-low-priority': task.priority === 'low',
        }"
    >
        <div class="flex items-start justify-between gap-4">
            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <!-- Header with Status Toggle and Title -->
                <div class="flex items-start gap-4 mb-3">
                    <!-- Status Toggle -->
                    <button
                        @click="$emit('toggle-status', task)"
                        class="task-status-toggle"
                        :class="{
                            completed: task.status === 'completed',
                            pending: task.status === 'pending',
                            'in-progress': task.status === 'in_progress',
                        }"
                        :aria-label="`Mark task as ${
                            task.status === 'completed'
                                ? 'incomplete'
                                : 'complete'
                        }`"
                    >
                        <CheckIcon
                            v-if="task.status === 'completed'"
                            class="w-3 h-3 text-white"
                        />
                    </button>

                    <!-- Title and Priority -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h3
                                class="task-title"
                                :class="{
                                    'line-through text-gray-500':
                                        task.status === 'completed',
                                    'text-gray-900':
                                        task.status !== 'completed',
                                }"
                            >
                                {{ task.title }}
                            </h3>
                            <div class="priority-badge" :class="task.priority">
                                {{ task.priority }}
                            </div>
                        </div>

                        <!-- Description -->
                        <p
                            v-if="task.description"
                            class="task-description"
                            :class="{
                                'line-through text-gray-400':
                                    task.status === 'completed',
                                'text-gray-600': task.status !== 'completed',
                            }"
                        >
                            {{ task.description }}
                        </p>
                    </div>
                </div>

                <!-- Meta Information -->
                <div
                    class="flex flex-wrap items-center gap-4 text-sm text-gray-500"
                >
                    <!-- Status -->
                    <div class="flex items-center gap-2">
                        <div
                            class="status-dot"
                            :class="task.status.replace('_', '-')"
                        ></div>
                        <span class="font-medium">{{
                            formatStatus(task.status)
                        }}</span>
                    </div>

                    <!-- Due Date -->
                    <div
                        v-if="task.due_date"
                        class="flex items-center gap-2"
                        :class="{ 'text-red-600 font-medium': isOverdue }"
                    >
                        <CalendarIcon class="w-4 h-4" />
                        <span>{{ formatDate(task.due_date) }}</span>
                        <ExclamationTriangleIcon
                            v-if="isOverdue"
                            class="w-4 h-4 text-red-500"
                        />
                    </div>

                    <!-- Created Date -->
                    <div class="flex items-center gap-2">
                        <ClockIcon class="w-4 h-4" />
                        <span>{{ formatRelativeDate(task.created_at) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-1">
                <button
                    @click="$emit('edit', task)"
                    class="action-btn action-btn-edit"
                    title="Edit task"
                >
                    <PencilIcon class="w-4 h-4" />
                </button>
                <button
                    @click="$emit('delete', task)"
                    class="action-btn action-btn-delete"
                    title="Delete task"
                >
                    <TrashIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Overdue Warning -->
        <div
            v-if="isOverdue && task.status !== 'completed'"
            class="overdue-warning"
        >
            <div class="flex items-center gap-3">
                <ExclamationTriangleIcon
                    class="w-5 h-5 text-red-500 flex-shrink-0"
                />
                <div>
                    <span class="text-red-700 font-semibold"
                        >This task is overdue</span
                    >
                    <p class="text-red-600 text-sm mt-1">
                        Due {{ formatRelativeDate(task.due_date) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    CheckIcon,
    PencilIcon,
    TrashIcon,
    CalendarIcon,
    ClockIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

// Emits
defineEmits(["edit", "delete", "toggle-status"]);

// Computed properties
const priorityClasses = {
    low: "bg-gray-100 text-gray-800",
    medium: "bg-yellow-100 text-yellow-800",
    high: "bg-red-100 text-red-800",
};

const statusClasses = {
    pending: "bg-gray-400",
    in_progress: "bg-indigo-500",
    completed: "bg-green-500",
};

const isOverdue = computed(() => {
    if (!props.task.due_date || props.task.status === "completed") return false;
    return new Date(props.task.due_date) < new Date();
});

// Methods
const formatStatus = (status) => {
    const statusMap = {
        pending: "Pending",
        in_progress: "In Progress",
        completed: "Completed",
    };
    return statusMap[status] || status;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
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

    const diffInWeeks = Math.floor(diffInDays / 7);
    if (diffInWeeks < 4) return `${diffInWeeks}w ago`;

    return formatDate(dateString);
};
</script>

<style scoped>
/* Task Status Toggle */
.task-status-toggle {
    @apply flex-shrink-0 w-6 h-6 rounded-full border-2 transition-all duration-200 flex items-center justify-center;
}

.task-status-toggle.pending {
    @apply border-gray-300 hover:border-green-400 hover:bg-green-50;
}

.task-status-toggle.in-progress {
    @apply border-indigo-400 bg-indigo-50;
}

.task-status-toggle.completed {
    @apply bg-green-500 border-green-500 hover:bg-green-600;
}

/* Task Title */
.task-title {
    @apply text-lg font-semibold leading-tight;
    word-break: break-word;
}

/* Task Description */
.task-description {
    @apply text-sm leading-relaxed mb-3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Action Buttons */
.action-btn {
    @apply p-2 rounded-lg transition-all duration-200 text-gray-400;
}

.action-btn-edit:hover {
    @apply text-indigo-600 bg-indigo-50;
}

.action-btn-delete:hover {
    @apply text-red-600 bg-red-50;
}

/* Overdue Warning */
.overdue-warning {
    @apply mt-4 p-4 bg-red-50 border border-red-200 rounded-lg;
}

/* Enhanced hover states */
@media (min-width: 768px) {
    .action-btn {
        opacity: 0.7;
    }
}

/* Focus styles for accessibility */
.task-status-toggle:focus-visible,
.action-btn:focus-visible {
    outline: 2px solid rgb(99 102 241);
    outline-offset: 2px;
}

/* Status-specific animations */
.task-status-toggle:active {
    transform: scale(0.95);
}

/* Text truncation improvements */
@supports (-webkit-line-clamp: 2) {
    .task-description {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: initial;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
}
</style>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
