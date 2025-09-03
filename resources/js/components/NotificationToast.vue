<template>
    <div
        :class="[
            'rounded-lg shadow-lg border border-gray-200 bg-white p-4 max-w-sm w-full',
            'transition-all duration-300 transform',
            typeClasses[notification.type],
        ]"
    >
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <component
                    :is="iconComponent"
                    :class="['h-6 w-6', iconClasses[notification.type]]"
                />
            </div>
            <div class="ml-3 flex-1">
                <p
                    :class="[
                        'text-sm font-medium',
                        titleClasses[notification.type],
                    ]"
                >
                    {{ notification.title }}
                </p>
                <p
                    v-if="notification.message"
                    class="mt-1 text-sm text-gray-600"
                >
                    {{ notification.message }}
                </p>
                <div
                    v-if="notification.actions && notification.actions.length"
                    class="mt-3 flex space-x-2"
                >
                    <button
                        v-for="action in notification.actions"
                        :key="action.label"
                        @click="handleAction(action)"
                        :class="[
                            'text-xs font-medium px-3 py-1 rounded-md transition-colors',
                            action.primary
                                ? `bg-${
                                      notification.type === 'error'
                                          ? 'red'
                                          : notification.type === 'warning'
                                          ? 'yellow'
                                          : notification.type === 'success'
                                          ? 'green'
                                          : 'blue'
                                  }-600 text-white hover:bg-${
                                      notification.type === 'error'
                                          ? 'red'
                                          : notification.type === 'warning'
                                          ? 'yellow'
                                          : notification.type === 'success'
                                          ? 'green'
                                          : 'blue'
                                  }-700`
                                : 'text-gray-600 hover:text-gray-800 border border-gray-300 hover:border-gray-400',
                        ]"
                    >
                        {{ action.label }}
                    </button>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button
                    @click="dismiss"
                    class="rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>
        </div>

        <!-- Progress bar for auto-dismiss -->
        <div
            v-if="notification.autoRemove !== false && !isHovered"
            class="mt-3 w-full bg-gray-200 rounded-full h-1"
        >
            <div
                :class="[
                    'h-1 rounded-full transition-all ease-linear',
                    progressBarClasses[notification.type],
                ]"
                :style="{
                    width: `${progressPercentage}%`,
                    transitionDuration: `${remainingTime}ms`,
                }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
    InformationCircleIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    notification: {
        type: Object,
        required: true,
    },
});

// Emits
const emit = defineEmits(["dismiss"]);

// Reactive data
const isHovered = ref(false);
const startTime = ref(Date.now());
const remainingTime = ref(props.notification.duration || 5000);
const progressPercentage = ref(100);
const timer = ref(null);
const progressTimer = ref(null);

// Computed properties
const iconComponent = computed(() => {
    const icons = {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        error: XCircleIcon,
        info: InformationCircleIcon,
    };
    return icons[props.notification.type] || InformationCircleIcon;
});

const typeClasses = {
    success: "border-l-4 border-l-green-500",
    warning: "border-l-4 border-l-yellow-500",
    error: "border-l-4 border-l-red-500",
    info: "border-l-4 border-l-indigo-500",
};

const iconClasses = {
    success: "text-green-500",
    warning: "text-yellow-500",
    error: "text-red-500",
    info: "text-indigo-500",
};

const titleClasses = {
    success: "text-green-800",
    warning: "text-yellow-800",
    error: "text-red-800",
    info: "text-indigo-800",
};

const progressBarClasses = {
    success: "bg-green-500",
    warning: "bg-yellow-500",
    error: "bg-red-500",
    info: "bg-indigo-500",
};

// Methods
const dismiss = () => {
    emit("dismiss", props.notification.id);
};

const handleAction = (action) => {
    if (action.handler) {
        action.handler();
    }
    if (action.dismissOnClick !== false) {
        dismiss();
    }
};

const startAutoRemove = () => {
    if (props.notification.autoRemove === false) return;

    const duration = props.notification.duration || 5000;
    startTime.value = Date.now();

    // Main timer for dismissal
    timer.value = setTimeout(() => {
        dismiss();
    }, duration);

    // Progress bar animation
    const updateProgress = () => {
        const elapsed = Date.now() - startTime.value;
        const remaining = duration - elapsed;
        remainingTime.value = remaining;
        progressPercentage.value = Math.max(0, (remaining / duration) * 100);

        if (remaining > 0 && !isHovered.value) {
            progressTimer.value = requestAnimationFrame(updateProgress);
        }
    };

    updateProgress();
};

const pauseAutoRemove = () => {
    if (timer.value) {
        clearTimeout(timer.value);
        timer.value = null;
    }
    if (progressTimer.value) {
        cancelAnimationFrame(progressTimer.value);
        progressTimer.value = null;
    }
    isHovered.value = true;
};

const resumeAutoRemove = () => {
    isHovered.value = false;
    if (props.notification.autoRemove !== false && remainingTime.value > 0) {
        startAutoRemove();
    }
};

// Lifecycle
onMounted(() => {
    startAutoRemove();

    // Add mouse event listeners
    const element = document.currentScript?.parentElement;
    if (element) {
        element.addEventListener("mouseenter", pauseAutoRemove);
        element.addEventListener("mouseleave", resumeAutoRemove);
    }
});

onUnmounted(() => {
    if (timer.value) {
        clearTimeout(timer.value);
    }
    if (progressTimer.value) {
        cancelAnimationFrame(progressTimer.value);
    }
});
</script>
