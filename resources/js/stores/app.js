import { defineStore } from 'pinia';

export const useAppStore = defineStore('app', {
  state: () => ({
    isLoading: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    toasts: [],
    sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', // Default to true
  }),

  getters: {
    isDarkMode: (state) => state.darkMode,
    isSidebarOpen: (state) => state.sidebarOpen,
  },

  actions: {
    /**
     * Set loading state
     */
    setLoading(status) {
      this.isLoading = status;
    },
    
    /**
     * Toggle dark mode
     */
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('darkMode', this.darkMode);
      
      // Apply dark mode to document
      if (this.darkMode) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    },
    
    /**
     * Initialize dark mode
     */
    initDarkMode() {
      // Initialize dark mode from system preference if not set in localStorage
      if (localStorage.getItem('darkMode') === null) {
        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        localStorage.setItem('darkMode', this.darkMode);
      }
      
      // Apply current setting to document
      if (this.darkMode) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    },
    
    /**
     * Toggle sidebar
     */
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
      localStorage.setItem('sidebarOpen', this.sidebarOpen);
    },
    
    /**
     * Add a toast notification
     */
    addToast({ type = 'info', message, timeout = 5000 }) {
      const id = Date.now();
      
      this.toasts.push({
        id,
        type,
        message,
        timeout,
      });
      
      // Auto remove toast after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.removeToast(id);
        }, timeout);
      }
      
      return id;
    },
    
    /**
     * Remove a toast by ID
     */
    removeToast(id) {
      const index = this.toasts.findIndex(toast => toast.id === id);
      if (index !== -1) {
        this.toasts.splice(index, 1);
      }
    },
    
    /**
     * Clear all toasts
     */
    clearToasts() {
      this.toasts = [];
    }
  }
}); 