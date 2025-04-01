import { defineStore } from 'pinia';
import axios from 'axios';
import { useAppStore } from './app';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    isLoading: false,
    error: null
  }),
  
  getters: {
    currentUser: (state) => state.user,
    userInitials: (state) => {
      if (!state.user || !state.user.name) return '';
      return state.user.name
        .split(' ')
        .map(name => name[0])
        .join('')
        .toUpperCase();
    },
    userPhotoUrl: (state) => state.user?.photo_url || null
  },
  
  actions: {
    /**
     * Fetch the current user's profile
     */
    async fetchUserProfile() {
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/users');
        if (response.data.success) {
          this.user = response.data.data;
        }
        return this.user;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch user profile';
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
     * Update the user's profile
     */
    async updateProfile(profileData) {
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await axios.put('/api/users/profile', profileData);
        if (response.data.success) {
          this.user = response.data.data;
          appStore.addToast({
            type: 'success',
            message: response.data.message || 'Profile updated successfully'
          });
        }
        return this.user;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update profile';
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
     * Update the user's password
     */
    async updatePassword(passwordData) {
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await axios.put('/api/users/password', passwordData);
        if (response.data.success) {
          appStore.addToast({
            type: 'success',
            message: response.data.message || 'Password updated successfully'
          });
        }
        return true;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update password';
        appStore.addToast({
          type: 'error',
          message: this.error,
          errors: error.response?.data?.errors
        });
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Upload a profile photo
     */
    async uploadProfilePhoto(photoFile) {
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      const formData = new FormData();
      formData.append('photo', photoFile);
      
      try {
        const response = await axios.post('/api/users/photo', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        
        if (response.data.success) {
          this.user = response.data.data;
          appStore.addToast({
            type: 'success',
            message: response.data.message || 'Profile photo uploaded successfully'
          });
        }
        return this.user;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to upload profile photo';
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
     * Delete the user's profile photo
     */
    async deleteProfilePhoto() {
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await axios.delete('/api/users/photo');
        if (response.data.success) {
          this.user = {...this.user, photo_url: null, photo_path: null};
          appStore.addToast({
            type: 'success',
            message: response.data.message || 'Profile photo deleted successfully'
          });
        }
        return true;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete profile photo';
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
     * Get user statistics
     */
    async fetchUserStatistics() {
      const appStore = useAppStore();
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/users/statistics');
        if (response.data.success) {
          return response.data.data;
        }
        return null;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch user statistics';
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
     * Set the user data
     */
    setUser(userData) {
      this.user = userData;
    },
    
    /**
     * Clear user data
     */
    clearUser() {
      this.user = null;
    }
  }
}); 