import { defineStore } from 'pinia';
import axios from 'axios';
import { useAppStore } from './app';

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    stats: {
      total: 0,
      completed: 0,
      pending: 0,
      overdue: 0,
      completion_rate: 0
    },
    categories: [],
    recentTasks: [],
    upcomingDeadlines: [],
    isLoading: false,
    error: null,
    lastFetched: null
  }),
  
  getters: {
    /**
     * Get dashboard statistics
     */
    getStats: (state) => state.stats,
    
    /**
     * Get recent tasks
     */
    getRecentTasks: (state) => state.recentTasks,
    
    /**
     * Get upcoming deadlines
     */
    getUpcomingDeadlines: (state) => state.upcomingDeadlines,
    
    /**
     * Get categories with task counts
     */
    getCategories: (state) => state.categories,
    
    /**
     * Check if data is stale (older than 5 minutes)
     */
    isDataStale: (state) => {
      if (!state.lastFetched) return true;
      const fiveMinutesAgo = new Date();
      fiveMinutesAgo.setMinutes(fiveMinutesAgo.getMinutes() - 5);
      return state.lastFetched < fiveMinutesAgo;
    }
  },
  
  actions: {
    /**
     * Fetch dashboard data from API
     */
    async fetchDashboardData(force = false) {
      // Don't fetch if data is fresh unless forced
      if (!force && !this.isDataStale && this.lastFetched) {
        return {
          stats: this.stats,
          categories: this.categories,
          recentTasks: this.recentTasks,
          upcomingDeadlines: this.upcomingDeadlines
        };
      }
      
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/dashboard');
        
        if (response.data.success) {
          const data = response.data.data;
          
          this.stats = data.stats;
          this.categories = data.categories;
          this.recentTasks = data.recentTasks;
          this.upcomingDeadlines = data.upcomingDeadlines;
          this.lastFetched = new Date();
        }
        
        return {
          stats: this.stats,
          categories: this.categories,
          recentTasks: this.recentTasks,
          upcomingDeadlines: this.upcomingDeadlines
        };
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load dashboard data';
        appStore.addToast({
          type: 'error',
          message: this.error
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Reset dashboard data
     */
    resetDashboardData() {
      this.stats = {
        total: 0,
        completed: 0,
        pending: 0,
        overdue: 0,
        completion_rate: 0
      };
      this.categories = [];
      this.recentTasks = [];
      this.upcomingDeadlines = [];
      this.lastFetched = null;
    }
  }
}); 