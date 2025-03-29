import { defineStore } from 'pinia';
import axios from 'axios';
import { useAppStore } from './app';

export const useStatsStore = defineStore('stats', {
  state: () => ({
    overview: {
      total: 0,
      completed: 0,
      pending: 0,
      overdue: 0,
      completion_rate: 0
    },
    completionRate: {
      daily: [],
      weekly: [],
      monthly: []
    },
    byCategory: [],
    byPriority: [],
    byDate: {
      daily: [],
      weekly: [],
      monthly: []
    },
    completionTime: {
      average: 0,
      tasks: []
    },
    isLoading: false,
    dateRange: {
      start: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0], // 30 days ago
      end: new Date().toISOString().split('T')[0] // today
    }
  }),
  
  getters: {
    /**
     * Get statistics overview
     */
    getOverview: (state) => state.overview,
    
    /**
     * Get completion rate statistics
     */
    getCompletionRate: (state) => state.completionRate,
    
    /**
     * Get statistics by category
     */
    getStatsByCategory: (state) => state.byCategory,
    
    /**
     * Get statistics by priority
     */
    getStatsByPriority: (state) => state.byPriority,
    
    /**
     * Get statistics by date
     */
    getStatsByDate: (state) => state.byDate,
    
    /**
     * Get completion time statistics
     */
    getCompletionTime: (state) => state.completionTime
  },
  
  actions: {
    /**
     * Set date range for statistics
     */
    setDateRange(startDate, endDate) {
      this.dateRange.start = startDate;
      this.dateRange.end = endDate;
    },
    
    /**
     * Fetch statistics overview
     */
    async fetchOverview() {
      const appStore = useAppStore();
      this.isLoading = true;
      appStore.setLoading(true);
      
      try {
        const url = `/stats/overview?start_date=${this.dateRange.start}&end_date=${this.dateRange.end}`;
        const response = await axios.get(url);
        this.overview = response.data;
        return this.overview;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch statistics overview.'
        });
        throw error;
      } finally {
        this.isLoading = false;
        appStore.setLoading(false);
      }
    },
    
    /**
     * Fetch completion rate statistics
     */
    async fetchCompletionRate(period = 'weekly') {
      const appStore = useAppStore();
      this.isLoading = true;
      
      try {
        const url = `/stats/completion-rate?period=${period}&start_date=${this.dateRange.start}&end_date=${this.dateRange.end}`;
        const response = await axios.get(url);
        this.completionRate[period] = response.data;
        return this.completionRate[period];
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch completion rate statistics.'
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Fetch statistics by category
     */
    async fetchStatsByCategory() {
      const appStore = useAppStore();
      this.isLoading = true;
      
      try {
        const url = `/stats/by-category?start_date=${this.dateRange.start}&end_date=${this.dateRange.end}`;
        const response = await axios.get(url);
        this.byCategory = response.data;
        return this.byCategory;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch category statistics.'
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Fetch statistics by priority
     */
    async fetchStatsByPriority() {
      const appStore = useAppStore();
      this.isLoading = true;
      
      try {
        const url = `/stats/by-priority?start_date=${this.dateRange.start}&end_date=${this.dateRange.end}`;
        const response = await axios.get(url);
        this.byPriority = response.data;
        return this.byPriority;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch priority statistics.'
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Fetch statistics by date
     */
    async fetchStatsByDate(period = 'weekly') {
      const appStore = useAppStore();
      this.isLoading = true;
      
      try {
        const url = `/stats/by-date?period=${period}&start_date=${this.dateRange.start}&end_date=${this.dateRange.end}`;
        const response = await axios.get(url);
        this.byDate[period] = response.data;
        return this.byDate[period];
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch date statistics.'
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Fetch completion time statistics
     */
    async fetchCompletionTime() {
      const appStore = useAppStore();
      this.isLoading = true;
      
      try {
        const url = `/stats/completion-time?start_date=${this.dateRange.start}&end_date=${this.dateRange.end}`;
        const response = await axios.get(url);
        this.completionTime = response.data;
        return this.completionTime;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch completion time statistics.'
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Fetch all statistics for dashboard
     */
    async fetchAllStats() {
      const appStore = useAppStore();
      appStore.setLoading(true);
      
      try {
        await Promise.all([
          this.fetchOverview(),
          this.fetchCompletionRate('weekly'),
          this.fetchStatsByCategory(),
          this.fetchStatsByPriority(),
          this.fetchStatsByDate('weekly')
        ]);
      } catch (error) {
        console.error('Failed to fetch all statistics:', error);
      } finally {
        appStore.setLoading(false);
      }
    }
  }
}); 