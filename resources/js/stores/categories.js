import { defineStore } from 'pinia';
import axios from 'axios';
import { useAppStore } from './app';

export const useCategoryStore = defineStore('categories', {
  state: () => ({
    categories: [],
    isLoading: false
  }),
  
  getters: {
    /**
     * Get all categories
     */
    getAllCategories: (state) => state.categories,
    
    /**
     * Get category by ID
     */
    getCategoryById: (state) => (id) => {
      return state.categories.find(category => category.id === id);
    },
    
    /**
     * Get category options for selects
     */
    getCategoryOptions: (state) => {
      return state.categories.map(category => ({
        value: category.id,
        label: category.name,
        color: category.color || '#4B5563' // Default gray if no color
      }));
    }
  },
  
  actions: {
    /**
     * Fetch all categories
     */
    async fetchCategories() {
      const appStore = useAppStore();
      this.isLoading = true;
      appStore.setLoading(true);
      
      try {
        const response = await axios.get('/categories');
        this.categories = response.data;
        return this.categories;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to fetch categories.'
        });
        throw error;
      } finally {
        this.isLoading = false;
        appStore.setLoading(false);
      }
    },
    
    /**
     * Create a new category
     */
    async createCategory(categoryData) {
      const appStore = useAppStore();
      this.isLoading = true;
      appStore.setLoading(true);
      
      try {
        const response = await axios.post('/categories', categoryData);
        
        // Add to categories list
        this.categories.push(response.data);
        
        appStore.addToast({
          type: 'success',
          message: 'Category created successfully!'
        });
        
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to create category.'
        });
        throw error;
      } finally {
        this.isLoading = false;
        appStore.setLoading(false);
      }
    },
    
    /**
     * Update a category
     */
    async updateCategory(id, categoryData) {
      const appStore = useAppStore();
      this.isLoading = true;
      appStore.setLoading(true);
      
      try {
        const response = await axios.put(`/categories/${id}`, categoryData);
        
        // Update in categories list
        const index = this.categories.findIndex(category => category.id === id);
        if (index !== -1) {
          this.categories[index] = response.data;
        }
        
        appStore.addToast({
          type: 'success',
          message: 'Category updated successfully!'
        });
        
        return response.data;
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to update category.'
        });
        throw error;
      } finally {
        this.isLoading = false;
        appStore.setLoading(false);
      }
    },
    
    /**
     * Delete a category
     */
    async deleteCategory(id) {
      const appStore = useAppStore();
      this.isLoading = true;
      appStore.setLoading(true);
      
      try {
        await axios.delete(`/categories/${id}`);
        
        // Remove from categories list
        this.categories = this.categories.filter(category => category.id !== id);
        
        appStore.addToast({
          type: 'success',
          message: 'Category deleted successfully!'
        });
      } catch (error) {
        appStore.addToast({
          type: 'error',
          message: error.response?.data?.message || 'Failed to delete category.'
        });
        throw error;
      } finally {
        this.isLoading = false;
        appStore.setLoading(false);
      }
    }
  }
}); 