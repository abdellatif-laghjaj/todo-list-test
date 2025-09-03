import { defineStore } from "pinia";
import axios from "axios";

export const useTaskStore = defineStore("tasks", {
    state: () => ({
        tasks: [],
        currentTask: null,
        pagination: null,
        stats: null,
        isLoading: false,
        error: null,
        filters: {
            search: "",
            status: "",
            priority: "",
        },
    }),

    getters: {
        getAllTasks: (state) => state.tasks,
        getCurrentTask: (state) => state.currentTask,
        getPagination: (state) => state.pagination,
        getStats: (state) => state.stats,
        getFilters: (state) => state.filters,
        isLoading: (state) => state.isLoading,
        getError: (state) => state.error,
    },

    actions: {
        /**
         * Fetch tasks with optional filters
         */
        async fetchTasks(page = 1) {
            this.isLoading = true;
            this.error = null;

            try {
                const params = {
                    page,
                    ...this.filters,
                };

                const response = await axios.get("/tasks", { params });
                const data = response.data.data;

                this.tasks = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    per_page: data.per_page,
                    total: data.total,
                };

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to fetch tasks";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Fetch single task by ID
         */
        async fetchTask(id) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.get(`/tasks/${id}`);
                this.currentTask = response.data.data;
                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to fetch task";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Create new task
         */
        async createTask(taskData) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.post("/tasks", taskData);
                const newTask = response.data.data;

                // Add to beginning of tasks array
                this.tasks.unshift(newTask);

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to create task";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Update existing task
         */
        async updateTask(id, taskData) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.put(`/tasks/${id}`, taskData);
                const updatedTask = response.data.data;

                // Update task in tasks array
                const index = this.tasks.findIndex((task) => task.id === id);
                if (index !== -1) {
                    this.tasks[index] = updatedTask;
                }

                // Update current task if it's the same
                if (this.currentTask && this.currentTask.id === id) {
                    this.currentTask = updatedTask;
                }

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to update task";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Delete task
         */
        async deleteTask(id) {
            this.isLoading = true;
            this.error = null;

            try {
                const response = await axios.delete(`/tasks/${id}`);

                // Remove from tasks array
                this.tasks = this.tasks.filter((task) => task.id !== id);

                // Clear current task if it's the deleted one
                if (this.currentTask && this.currentTask.id === id) {
                    this.currentTask = null;
                }

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to delete task";
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Fetch task statistics
         */
        async fetchStats() {
            try {
                const response = await axios.get("/tasks-stats");
                this.stats = response.data.data;
                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to fetch stats";
                throw error;
            }
        },

        /**
         * Update filters
         */
        updateFilters(newFilters) {
            this.filters = { ...this.filters, ...newFilters };
        },

        /**
         * Clear filters
         */
        clearFilters() {
            this.filters = {
                search: "",
                status: "",
                priority: "",
            };
        },

        /**
         * Clear current task
         */
        clearCurrentTask() {
            this.currentTask = null;
        },

        /**
         * Clear error
         */
        clearError() {
            this.error = null;
        },
    },
});
