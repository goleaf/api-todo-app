import { defineStore } from 'pinia';
import axios from 'axios';
import { useAppStore } from './app';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    authError: null,
    authStatus: 'idle', // 'idle', 'authenticating', 'success', 'error'
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    currentUser: (state) => state.user,
    isAuthLoading: (state) => state.authStatus === 'authenticating',
    authFailed: (state) => state.authStatus === 'error',
  },

  actions: {
    async init() {
      if (this.token) {
        try {
          await this.fetchUser();
        } catch (error) {
          console.error('Auth initialization failed:', error);
          this.clearAuth();
        }
      }
    },

    async login(credentials) {
      const appStore = useAppStore();
      this.authStatus = 'authenticating';
      this.authError = null;
      appStore.setLoading(true);

      try {
        const response = await axios.post('/api/login', {
          ...credentials,
          device_name: credentials.device_name || navigator.userAgent
        });
        
        if (response.data && response.data.token) {
          this.setToken(response.data.token);
          await this.fetchUser();
          
          this.authStatus = 'success';
          appStore.addToast({
            type: 'success',
            message: 'Logged in successfully'
          });
          
          return response.data;
        } else {
          throw new Error('Invalid response from server');
        }
      } catch (error) {
        this.authStatus = 'error';
        this.authError = this.formatErrorMessage(error);
        this.clearAuth();
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },

    async register(userData) {
      const appStore = useAppStore();
      this.authStatus = 'authenticating';
      this.authError = null;
      appStore.setLoading(true);

      try {
        // Ensure device_name is included in registration data
        const registrationData = {
          ...userData,
          device_name: userData.device_name || navigator.userAgent
        };
        
        const response = await axios.post('/api/register', registrationData);
        
        if (response.data && response.data.data && response.data.data.token) {
          this.setToken(response.data.data.token);
          
          if (response.data.data.user) {
            this.setUser(response.data.data.user);
          } else {
            await this.fetchUser();
          }
          
          this.authStatus = 'success';
          appStore.addToast({
            type: 'success',
            message: 'Account created successfully'
          });
          
          return response.data;
        } else if (response.data && response.data.message) {
          // API returned an error message but not in the expected error format
          throw new Error(response.data.message);
        } else {
          throw new Error('Invalid response from server');
        }
      } catch (error) {
        this.authStatus = 'error';
        console.error('Registration error:', error);
        
        // Detailed error handling
        if (error.response) {
          // Extract validation errors if present
          if (error.response.status === 422 && error.response.data.errors) {
            console.log('Validation errors:', error.response.data.errors);
            // Return the specific validation errors
            return { 
              success: false, 
              validationErrors: error.response.data.errors 
            };
          }
          
          // Handle other status codes
          if (error.response.status === 429) {
            this.authError = 'Too many attempts. Please try again later.';
          } else if (error.response.status === 500) {
            this.authError = 'Server error. Our team has been notified.';
          } else if (error.response.data && error.response.data.message) {
            this.authError = error.response.data.message;
          } else {
            this.authError = this.formatErrorMessage(error);
          }
        } else if (error.request) {
          // Network error
          this.authError = 'Network error. Please check your connection.';
        } else {
          // Other errors
          this.authError = error.message || 'An error occurred during registration. Please try again.';
        }
        
        // Don't clear auth on validation errors
        if (!error.response || error.response.status !== 422) {
          this.clearAuth();
        }
        
        throw error;
      } finally {
        appStore.setLoading(false);
      }
    },

    async fetchUser() {
      if (!this.token) {
        throw new Error('No authentication token found');
      }
      
      try {
        const response = await axios.get('/api/user');
        
        if (response.data) {
          this.setUser(response.data);
          return response.data;
        } else {
          throw new Error('Invalid user data received');
        }
      } catch (error) {
        if (error.response && error.response.status === 401) {
          // Token is invalid or expired
          this.clearAuth();
          const appStore = useAppStore();
          appStore.addToast({
            type: 'error',
            message: 'Your session has expired. Please login again.'
          });
        }
        throw error;
      }
    },

    async logout() {
      const appStore = useAppStore();
      appStore.setLoading(true);

      try {
        if (this.token) {
          await axios.post('/api/logout');
        }
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.clearAuth();
        this.authStatus = 'idle';
        appStore.setLoading(false);
        appStore.addToast({
          type: 'info',
          message: 'Logged out successfully'
        });
      }
    },

    setUser(user) {
      this.user = user;
    },

    setToken(token) {
      this.token = token;
      localStorage.setItem('token', token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },

    clearAuth() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    },
    
    formatErrorMessage(error) {
      if (error.response) {
        // Server responded with an error status
        if (error.response.data && error.response.data.message) {
          return error.response.data.message;
        } else if (error.response.status === 401) {
          return 'Authentication failed. Please check your credentials.';
        } else if (error.response.status === 422) {
          return 'Validation error. Please check your input.';
        } else if (error.response.status >= 500) {
          return 'Server error. Please try again later.';
        }
      } else if (error.request) {
        // Request was made but no response received
        return 'No response from server. Please check your internet connection.';
      }
      
      // Something else happened while setting up the request
      return error.message || 'An unknown error occurred';
    }
  }
}); 