<template>
    <div
        :class="[
            'bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-200 hover:shadow-md',
            task.status === 'completed' ? 'opacity-75' : '',
        ]"
    >
        <div class="flex items-start justify-between">
            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-3 mb-2">
                    <!-- Status Toggle -->
                    <button
                        @click="$emit('toggle-status', task)"
                        :class="[
                            'flex-shrink-0 w-5 h-5 rounded-full border-2 transition-colors',
                            task.status === 'completed'
                                ? 'bg-green-500 border-green-500'
                                : 'border-gray-300 hover:border-green-400',
                        ]"
                    >
                        <CheckIcon
                            v-if="task.status === 'completed'"
                            class="w-3 h-3 text-white m-auto"
                        />
                    </button>

                    <!-- Title -->
                    <h3
                        :class="[
                            'text-lg font-semibold truncate',
                            task.status === 'completed'
                                ? 'line-through text-gray-500'
                                : 'text-gray-900',
                        ]"
                    >
                        {{ task.title }}
                    </h3>

                    <!-- Priority Badge -->
                    <span
                        :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            priorityClasses[task.priority],
                        ]"
                    >
                        {{
                            task.priority.charAt(0).toUpperCase() +
                            task.priority.slice(1)
                        }}
                    </span>
                </div>

                <!-- Description -->
                <p
                    v-if="task.description"
                    :class="[
                        'text-gray-600 mb-3 line-clamp-2',
                        task.status === 'completed' ? 'line-through' : '',
                    ]"
                >
                    {{ task.description }}
                </p>

                <!-- Meta Information -->
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <!-- Status -->
                    <div class="flex items-center space-x-1">
                        <div
                            :class="[
                                'w-2 h-2 rounded-full',
                                statusClasses[task.status],
                            ]"
                        ></div>
                        <span>{{ formatStatus(task.status) }}</span>
                    </div>

                    <!-- Due Date -->
                    <div
                        v-if="task.due_date"
                        class="flex items-center space-x-1"
                    >
                        <CalendarIcon class="w-4 h-4" />
                        <span :class="{ 'text-red-600': isOverdue }">
                            {{ formatDate(task.due_date) }}
                        </span>
                    </div>

                    <!-- Created Date -->
                    <div class="flex items-center space-x-1">
                        <ClockIcon class="w-4 h-4" />
                        <span>{{ formatRelativeDate(task.created_at) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 ml-4">
                <button
                    @click="$emit('edit', task)"
                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="Edit task"
                >
                    <PencilIcon class="w-4 h-4" />
                </button>
                <button
                    @click="$emit('delete', task)"
                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete task"
                >
                    <TrashIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Overdue Warning -->
        <div
            v-if="isOverdue && task.status !== 'completed'"
            class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg"
        >
            <div class="flex items-center space-x-2">
                <ExclamationTriangleIcon class="w-5 h-5 text-red-500" />
                <span class="text-red-700 font-medium"
                    >This task is overdue</span
                >
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
    in_progress: "bg-blue-500",
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
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
