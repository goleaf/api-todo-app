import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Import components
import App from './components/App.vue';
import TodoList from './components/TodoList.vue';
import Todo from './components/Todo.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Tasks from './pages/Tasks.vue';
import TodoDetail from './components/TodoDetail.vue';
import CalendarView from './components/CalendarView.vue';
import StatsView from './components/StatsView.vue';
import ProfileView from './components/ProfileView.vue';
import WelcomeView from './components/WelcomeView.vue';

// Axios setup
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

// Get token from local storage and set as default authorization header
const token = localStorage.getItem('auth_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Pusher setup
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Define routes
const routes = [
    { 
        path: '/', 
        component: TodoList, 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/calendar', 
        component: CalendarView, 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/stats', 
        component: StatsView, 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/profile', 
        component: ProfileView, 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/todos/:id', 
        component: TodoDetail, 
        meta: { requiresAuth: true },
        props: true
    },
    {
        path: '/todos/new',
        component: TodoDetail,
        meta: { requiresAuth: true },
        props: { isNew: true }
    },
    { 
        path: '/login', 
        component: Login, 
        meta: { guest: true } 
    },
    { 
        path: '/register', 
        component: Register, 
        meta: { guest: true } 
    },
    { 
        path: '/welcome', 
        component: WelcomeView, 
        meta: { guest: true } 
    }
];

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const isLoggedIn = !!localStorage.getItem('auth_token');
    
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!isLoggedIn) {
            next('/login');
            return;
        }
    }
    
    if (to.matched.some(record => record.meta.guest)) {
        if (isLoggedIn) {
            next('/');
            return;
        }
    }
    
    next();
});

// Vuex Store
const store = createStore({
    state: {
        user: JSON.parse(localStorage.getItem('user')) || null,
        todos: [],
        categories: [],
        loading: false,
        error: null,
        darkMode: localStorage.getItem('darkMode') === 'true'
    },
    getters: {
        isAuthenticated: state => !!state.user,
        getUser: state => state.user,
        getTodos: state => state.todos,
        getCategories: state => state.categories,
        isLoading: state => state.loading,
        getError: state => state.error,
        isDarkMode: state => state.darkMode
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        clearUser(state) {
            state.user = null;
            localStorage.removeItem('user');
            localStorage.removeItem('auth_token');
        },
        setTodos(state, todos) {
            state.todos = todos;
        },
        addTodo(state, todo) {
            state.todos.unshift(todo);
        },
        updateTodo(state, updatedTodo) {
            const index = state.todos.findIndex(t => t.id === updatedTodo.id);
            if (index !== -1) {
                state.todos.splice(index, 1, updatedTodo);
            }
        },
        removeTodo(state, id) {
            state.todos = state.todos.filter(t => t.id !== id);
        },
        setCategories(state, categories) {
            state.categories = categories;
        },
        addCategory(state, category) {
            state.categories.push(category);
        },
        removeCategory(state, id) {
            state.categories = state.categories.filter(c => c.id !== id);
        },
        setLoading(state, status) {
            state.loading = status;
        },
        setError(state, error) {
            state.error = error;
        },
        toggleDarkMode(state) {
            state.darkMode = !state.darkMode;
            localStorage.setItem('darkMode', state.darkMode);
        }
    },
    actions: {
        // Auth actions
        async login({ commit }, credentials) {
            commit('setLoading', true);
            commit('setError', null);
            try {
                const response = await axios.post('/login', credentials);
                const token = response.data.token;
                const user = response.data.user;
                
                localStorage.setItem('auth_token', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                commit('setUser', user);
                
                if (user) {
                    // Set up Echo private channel
                    this.dispatch('setupEcho');
                }
                
                return user;
            } catch (error) {
                commit('setError', error.response?.data?.message || 'Login failed');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        async register({ commit }, userData) {
            commit('setLoading', true);
            commit('setError', null);
            try {
                const response = await axios.post('/register', userData);
                const token = response.data.token;
                const user = response.data.user;
                
                localStorage.setItem('auth_token', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                commit('setUser', user);
                
                if (user) {
                    // Set up Echo private channel
                    this.dispatch('setupEcho');
                }
                
                return user;
            } catch (error) {
                commit('setError', error.response?.data?.message || 'Registration failed');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        logout({ commit }) {
            commit('clearUser');
            axios.defaults.headers.common['Authorization'] = '';
            
            // Unsubscribe from Echo channels
            if (window.Echo) {
                window.Echo.leave(`user.${this.state.user.id}`);
            }
        },
        
        // Echo setup
        setupEcho({ commit, state }) {
            if (state.user && window.Echo) {
                window.Echo.private(`user.${state.user.id}`)
                    .listen('TodoCreated', (e) => {
                        commit('addTodo', e.todo);
                    })
                    .listen('TodoUpdated', (e) => {
                        commit('updateTodo', e.todo);
                    })
                    .listen('TodoDeleted', (e) => {
                        commit('removeTodo', e.todo.id);
                    });
            }
        },
        
        // Todo actions
        async fetchTodos({ commit }, params = {}) {
            commit('setLoading', true);
            try {
                // Build query string based on params
                let queryParams = [];
                
                if (params.category) {
                    queryParams.push(`category=${params.category}`);
                }
                
                if (params.status) {
                    queryParams.push(`status=${params.status}`);
                }
                
                if (params.search) {
                    queryParams.push(`search=${encodeURIComponent(params.search)}`);
                }
                
                // Construct URL with query parameters
                const url = '/todos' + (queryParams.length > 0 ? `?${queryParams.join('&')}` : '');
                
                const response = await axios.get(url);
                commit('setTodos', response.data);
                return response.data;
            } catch (error) {
                console.error('Fetch todos error:', error);
                commit('setError', 'Failed to fetch todos');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        async createTodo({ commit }, todo) {
            commit('setLoading', true);
            try {
                const response = await axios.post('/todos', todo);
                commit('addTodo', response.data);
                return response.data;
            } catch (error) {
                console.error('Create todo error:', error);
                commit('setError', 'Failed to create todo');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        async updateTodo({ commit }, todo) {
            commit('setLoading', true);
            try {
                const response = await axios.put(`/todos/${todo.id}`, todo);
                commit('updateTodo', response.data);
                return response.data;
            } catch (error) {
                console.error('Update todo error:', error);
                commit('setError', 'Failed to update todo');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        async deleteTodo({ commit }, id) {
            commit('setLoading', true);
            try {
                await axios.delete(`/todos/${id}`);
                commit('removeTodo', id);
            } catch (error) {
                console.error('Delete todo error:', error);
                commit('setError', 'Failed to delete todo');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        // Category actions
        async fetchCategories({ commit }) {
            commit('setLoading', true);
            try {
                const response = await axios.get('/categories');
                commit('setCategories', response.data);
                return response.data;
            } catch (error) {
                console.error('Fetch categories error:', error);
                commit('setError', 'Failed to fetch categories');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        async createCategory({ commit }, category) {
            commit('setLoading', true);
            try {
                const response = await axios.post('/categories', category);
                commit('addCategory', response.data);
                return response.data;
            } catch (error) {
                console.error('Create category error:', error);
                commit('setError', 'Failed to create category');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        async deleteCategory({ commit }, id) {
            commit('setLoading', true);
            try {
                await axios.delete(`/categories/${id}`);
                commit('removeCategory', id);
            } catch (error) {
                console.error('Delete category error:', error);
                commit('setError', 'Failed to delete category');
                throw error;
            } finally {
                commit('setLoading', false);
            }
        },
        
        // Toggle dark mode
        toggleDarkMode({ commit }) {
            commit('toggleDarkMode');
            document.documentElement.classList.toggle('dark');
        }
    }
});

// Initialize dark mode
if (store.state.darkMode) {
    document.documentElement.classList.add('dark');
}

// Create and mount the Vue application
const app = createApp(App);
app.use(store);
app.use(router);

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Echo if user exists
    if (store.state.user) {
        store.dispatch('setupEcho');
    }
    
    app.mount('#app');
});

// Handle authentication state
document.addEventListener('DOMContentLoaded', function() {
    // If we're not on the welcome page specifically, check auth status
    if (window.location.pathname !== '/') {
        checkAuthState();
    }
});

// Function to check authentication state and redirect if needed
function checkAuthState() {
    const token = localStorage.getItem('auth_token');
    const publicRoutes = ['/', '/login', '/register', '/forgot-password', '/reset-password'];
    
    // If no token and not on a public route, redirect to welcome page
    if (!token && !publicRoutes.includes(window.location.pathname)) {
        window.location.href = '/';
    }
    
    // If token exists, validate it
    if (token) {
        axios.get('/api/user')
            .catch(() => {
                // If token is invalid, remove it and redirect to welcome
                localStorage.removeItem('auth_token');
                if (!publicRoutes.includes(window.location.pathname)) {
                    window.location.href = '/';
                }
            });
    }
}
