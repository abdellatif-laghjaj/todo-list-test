import { defineStore } from "pinia";

export const useNotificationStore = defineStore("notifications", {
    state: () => ({
        notifications: [],
        unreadCount: 0,
    }),

    getters: {
        getAllNotifications: (state) => state.notifications,
        getUnreadCount: (state) => state.unreadCount,
        getRecentNotifications: (state) => state.notifications.slice(0, 5),
    },

    actions: {
        /**
         * Add new notification
         */
        addNotification(notification) {
            const newNotification = {
                id: Date.now(),
                timestamp: new Date().toISOString(),
                read: false,
                ...notification,
            };

            this.notifications.unshift(newNotification);
            this.unreadCount++;

            // Show browser notification if permission granted
            this.showBrowserNotification(newNotification);
        },

        /**
         * Mark notification as read
         */
        markAsRead(id) {
            const notification = this.notifications.find((n) => n.id === id);
            if (notification && !notification.read) {
                notification.read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        },

        /**
         * Mark all notifications as read
         */
        markAllAsRead() {
            this.notifications.forEach((notification) => {
                notification.read = true;
            });
            this.unreadCount = 0;
        },

        /**
         * Remove notification
         */
        removeNotification(id) {
            const index = this.notifications.findIndex((n) => n.id === id);
            if (index !== -1) {
                const notification = this.notifications[index];
                if (!notification.read) {
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
                this.notifications.splice(index, 1);
            }
        },

        /**
         * Clear all notifications
         */
        clearAll() {
            this.notifications = [];
            this.unreadCount = 0;
        },

        /**
         * Show browser notification
         */
        showBrowserNotification(notification) {
            if (Notification.permission === "granted") {
                new Notification("Todo App", {
                    body: notification.message,
                    icon: "/favicon.ico",
                });
            }
        },

        /**
         * Request browser notification permission
         */
        async requestNotificationPermission() {
            if ("Notification" in window) {
                const permission = await Notification.requestPermission();
                return permission === "granted";
            }
            return false;
        },
    },
});
