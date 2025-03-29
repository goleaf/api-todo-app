import axios from 'axios';

// Set the API base URL based on the environment
const API_BASE = import.meta.env.MODE === 'production' 
  ? 'https://todo.prus.dev/api' 
  : 'http://10.0.2.2:8000/api'; // Android emulator localhost

// Configure axios defaults
axios.defaults.withCredentials = true;
axios.defaults.baseURL = API_BASE;

// Add a request interceptor to add auth token to requests
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Add a response interceptor to handle common errors
axios.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    if (error.response && error.response.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem('auth_token');
      // Redirect to login page or trigger logout action
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth endpoints
  login(credentials) {
    return axios.post('/login', credentials);
  },
  register(userData) {
    return axios.post('/register', userData);
  },
  logout() {
    return axios.post('/logout');
  },
  getUser() {
    return axios.get('/user');
  },
  
  // Tasks endpoints
  getTasks() {
    return axios.get('/tasks');
  },
  getTask(id) {
    return axios.get(`/tasks/${id}`);
  },
  createTask(task) {
    return axios.post('/tasks', task);
  },
  updateTask(id, task) {
    return axios.put(`/tasks/${id}`, task);
  },
  deleteTask(id) {
    return axios.delete(`/tasks/${id}`);
  },
  toggleTaskComplete(id) {
    return axios.post(`/tasks/${id}/toggle-complete`);
  },
  
  // Categories endpoints
  getCategories() {
    return axios.get('/categories');
  },
  createCategory(category) {
    return axios.post('/categories', category);
  },
  updateCategory(id, category) {
    return axios.put(`/categories/${id}`, category);
  },
  deleteCategory(id) {
    return axios.delete(`/categories/${id}`);
  },
  
  // Stats endpoints
  getStatsOverview() {
    return axios.get('/stats/overview');
  },
  getStatsByCategory() {
    return axios.get('/stats/by-category');
  }
}; 