import { defineStore } from 'pinia';
import axios from 'axios';
import { useAppStore } from './app';
import { format, isAfter } from 'date-fns';

export const useTaskStore = defineStore('tasks', {
  state: () => ({
    tasks: [],
    categories: [],
    task: null,
    filters: {
      status: 'all',
      category: '',
      search: '',
      dueDate: null,
      sort: 'created_at',
      order: 'desc'
    },
    pagination: {
      currentPage: 1,
      totalPages: 1,
      perPage: 10,
      total: 0
    },
    statistics: {
      total: 0,
      completed: 0,
      pending: 0,
      overdue: 0
    }
  }),
  
  getters: {
    /**
     * Get the tasks list
     */
    getAllTasks: (state) => state.tasks,
    
    /**
     * Get the current task
     */
    getCurrentTask: (state) => state.task,
    
    /**
     * Get pending tasks count
     */
    getPendingCount: (state) => state.tasks.filter(task => !task.completed).length,
    
    /**
     * Get completed tasks count
     */
    getCompletedCount: (state) => state.tasks.filter(task => task.completed).length,
    
    /**
     * Get tasks filtered by status
     */
    getFilteredTasks: (state) => {
      let filteredTasks = [...state.tasks];
      
      // Filter by status
      if (state.filters.status === 'pending') {
        filteredTasks = filteredTasks.filter(task => !task.completed);
      } else if (state.filters.status === 'completed') {
        filteredTasks = filteredTasks.filter(task => task.completed);
      }
      
      // Filter by category
      if (state.filters.category) {
        filteredTasks = filteredTasks.filter(task => task.category_id === parseInt(state.filters.category));
      }
      
      // Filter by search term
      if (state.filters.search) {
        const searchTerm = state.filters.search.toLowerCase();
        filteredTasks = filteredTasks.filter(task => 
          task.title.toLowerCase().includes(searchTerm) || 
          (task.description && task.description.toLowerCase().includes(searchTerm))
        );
      }
      
      // Filter by date
      if (state.filters.dueDate) {
        const filterDate = new Date(state.filters.dueDate).toISOString().split('T')[0];
        filteredTasks = filteredTasks.filter(task => {
          const taskDate = new Date(task.due_date).toISOString().split('T')[0];
          return taskDate === filterDate;
        });
      }
      
      return filteredTasks;
    },
    
    getTaskById: (state) => (id) => {
      return state.tasks.find(task => task.id === parseInt(id));
    },
    
    getStatistics: (state) => state.statistics,
    
    getOverdueTasks: (state) => {
      const today = new Date();
      return state.tasks.filter(task => 
        !task.completed && 
        task.due_date && 
        isAfter(today, new Date(task.due_date))
      );
    },
    
    getDueSoonTasks: (state) => {
      const today = new Date();
      const nextWeek = new Date();
      nextWeek.setDate(today.getDate() + 7);
      
      return state.tasks.filter(task => 
        !task.completed && 
        task.due_date && 
        isAfter(new Date(task.due_date), today) && 
        isAfter(nextWeek, new Date(task.due_date))
      );
    }
  },
  
  actions: {
    /**
     * Set filter
     */
    setFilter(filterName, value) {
      this.filters[filterName] = value;
      // Reset to page 1 when changing filters
      this.pagination.currentPage = 1;
      this.fetchTasks();
    },
    
    /**
     * Reset filters
     */
    resetFilters() {
      this.filters = {
        status: 'all',
        category: '',
        search: '',
        dueDate: null,
        sort: 'created_at',
        order: 'desc'
      };
      this.pagination.currentPage = 1;
      this.fetchTasks();
    },
    
    /**
     * Fetch tasks
     */
    async fetchTasks() {
      const appStore = useAppStore();
      appStore.setLoading(true);
      
      try {
        const params = {
          page: this.pagination.currentPage,
          per_page: this.pagination.perPage,
          status: this.filters.status !== 'all' ? this.filters.status : undefined,
          category: this.filters.category || undefined,
          search: this.filters.search || undefined,
          due_date: this.filters.dueDate || undefined,
          sort: this.filters.sort,
          order: this.filters.order
        };
        
        const response = await axios.get('/api/tasks', { params });
        
        this.tasks = response.data.data;
        this.pagination.currentPage = response.data.current_page;
        this.pagination.totalPages = response.data.last_page;
        this.pagination.total = response.data.total;
        
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to load tasks. Please try again.'
        });
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },
    
    /**
     * Fetch a task by ID
     */
    async fetchTask(id) {
      const appStore = useAppStore();
      appStore.setLoading(true);
      
      try {
        const response = await axios.get(`/api/tasks/${id}`);
        this.task = response.data;
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to load task details. Please try again.'
        });
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },
    
    /**
     * Create a new task
     */
    async createTask(taskData) {
      const appStore = useAppStore();
      appStore.setLoading(true);
      
      try {
        // Format the due_date if it exists
        if (taskData.due_date) {
          taskData.due_date = new Date(taskData.due_date).toISOString().split('T')[0];
        }
        
        const response = await axios.post('/api/tasks', taskData);
        
        // Update statistics
        this.fetchDashboardStats();
        
        appStore.addToast({
          type: 'success',
          message: 'Task created successfully!'
        });
        
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to create task. Please try again.'
        });
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },
    
    /**
     * Update an existing task
     */
    async updateTask(id, taskData) {
      const appStore = useAppStore();
      appStore.setLoading(true);
      
      try {
        // Format the due_date if it exists
        if (taskData.due_date) {
          taskData.due_date = new Date(taskData.due_date).toISOString().split('T')[0];
        }
        
        const response = await axios.put(`/api/tasks/${id}`, taskData);
        
        // Update local task if it exists in the list
        const index = this.tasks.findIndex(task => task.id === parseInt(id));
        if (index !== -1) {
          this.tasks[index] = response.data;
        }
        
        // Update task detail if this is the one being viewed
        if (this.task && this.task.id === parseInt(id)) {
          this.task = response.data;
        }
        
        // Update statistics
        this.fetchDashboardStats();
        
        appStore.addToast({
          type: 'success',
          message: 'Task updated successfully!'
        });
        
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to update task. Please try again.'
        });
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },
    
    /**
     * Toggle task completion
     */
    async toggleTaskCompletion(id, completed) {
      const appStore = useAppStore();
      
      try {
        const response = await axios.patch(`/api/tasks/${id}/toggle-completion`, {
          completed
        });
        
        // Update local task if it exists in the list
        const index = this.tasks.findIndex(task => task.id === parseInt(id));
        if (index !== -1) {
          this.tasks[index].completed = completed;
          this.tasks[index].completed_at = completed ? new Date().toISOString() : null;
        }
        
        // Update task detail if this is the one being viewed
        if (this.task && this.task.id === parseInt(id)) {
          this.task.completed = completed;
          this.task.completed_at = completed ? new Date().toISOString() : null;
        }
        
        // Update statistics
        this.fetchDashboardStats();
        
        appStore.addToast({
          type: 'success',
          message: completed ? 'Task marked as completed!' : 'Task marked as incomplete!'
        });
        
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to update task status. Please try again.'
        });
        throw error;
      }
    },
    
    /**
     * Delete a task
     */
    async deleteTask(id) {
      const appStore = useAppStore();
      appStore.setLoading(true);
      
      try {
        await axios.delete(`/api/tasks/${id}`);
        
        // Remove task from local list if it exists
        this.tasks = this.tasks.filter(task => task.id !== parseInt(id));
        
        // Clear current task if this is the one being viewed
        if (this.task && this.task.id === parseInt(id)) {
          this.task = null;
        }
        
        // Update statistics
        this.fetchDashboardStats();
        
        appStore.addToast({
          type: 'success',
          message: 'Task deleted successfully!'
        });
        
        return true;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to delete task. Please try again.'
        });
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },
    
    /**
     * Get pending tasks count
     */
    async fetchPendingCount() {
      try {
        const response = await axios.get('/tasks/pending-count');
        return response.data.count;
      } catch (error) {
        console.error('Failed to fetch pending count:', error);
        return 0;
      }
    },
    
    async fetchCategories() {
      const appStore = useAppStore();
      
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data;
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: 'Failed to load categories. Please try again.'
        });
        throw error;
      }
    },
    
    async fetchDashboardStats() {
      const appStore = useAppStore();
      
      try {
        const response = await axios.get('/api/dashboard/stats');
        this.statistics = response.data;
        return response.data;
      } catch (error) {
        console.error('Failed to load dashboard statistics:', error);
        // Don't show toast for this to avoid cluttering UI
        throw error;
      }
    },
    
    setSort(sort, order = 'desc') {
      this.filters.sort = sort;
      this.filters.order = order;
    },
    
    setPage(page) {
      this.pagination.currentPage = page;
    },
    
    // Format date in "Month Day, Year" format
    formatDate(dateString) {
      if (!dateString) return '';
      return format(new Date(dateString), 'MMM d, yyyy');
    },
    
    // Check if a task is overdue
    isOverdue(task) {
      if (!task.due_date || task.completed) return false;
      return isAfter(new Date(), new Date(task.due_date));
    },
    
    clearFilters() {
      this.filters = {
        status: 'all',
        category: '',
        search: '',
        dueDate: null,
        sort: 'created_at',
        order: 'desc'
      };
      this.pagination.currentPage = 1;
    }
  }
}); 