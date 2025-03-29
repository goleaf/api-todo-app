import { describe, it, expect, vi, beforeEach } from 'vitest';

// Mock dependencies
vi.mock('axios', () => ({
  default: {
    post: vi.fn(),
    get: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    defaults: {
      headers: {
        common: {}
      }
    }
  }
}));

vi.mock('laravel-echo', () => {
  return function() {
    return {
      private: () => ({
        listen: () => ({
          listen: () => ({
            listen: () => ({})
          })
        })
      }),
      leave: vi.fn()
    };
  };
});

// Mock localStorage with proper JSON stringification
const localStorageMock = (function() {
  let store = {};
  return {
    getItem: vi.fn(key => store[key] || null),
    setItem: vi.fn((key, value) => {
      store[key] = String(value);
    }),
    removeItem: vi.fn(key => {
      delete store[key];
    }),
    clear: vi.fn(() => {
      store = {};
    }),
    getAllItems: () => store
  };
})();

Object.defineProperty(window, 'localStorage', {
  value: localStorageMock,
  writable: true
});

// Import the store from app.js
import axios from 'axios';
import { createStore } from 'vuex';

describe('Vuex Store', () => {
  let store;

  beforeEach(() => {
    // Reset mocks
    vi.clearAllMocks();
    localStorageMock.clear();
    
    // Create a fresh store for each test
    store = createStore({
      state: {
        user: null,
        todos: [],
        categories: [],
        loading: false,
        error: null,
        darkMode: false
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
        },
        
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
            commit('setError', 'Failed to delete todo');
            throw error;
          } finally {
            commit('setLoading', false);
          }
        },
        
        async fetchCategories({ commit }) {
          commit('setLoading', true);
          try {
            const response = await axios.get('/categories');
            commit('setCategories', response.data);
            return response.data;
          } catch (error) {
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
            commit('setError', 'Failed to delete category');
            throw error;
          } finally {
            commit('setLoading', false);
          }
        },
        
        toggleDarkMode({ commit }) {
          commit('toggleDarkMode');
        }
      }
    });
  });

  describe('mutations', () => {
    it('sets user in state and localStorage', () => {
      const user = { id: 1, name: 'Test User', email: 'test@example.com' };
      store.commit('setUser', user);
      
      expect(store.state.user).toEqual(user);
      expect(localStorage.setItem).toHaveBeenCalledWith('user', JSON.stringify(user));
    });

    it('clears user from state and localStorage', () => {
      // Set user first
      store.state.user = { id: 1, name: 'Test User' };
      
      // Then clear it
      store.commit('clearUser');
      
      expect(store.state.user).toBeNull();
      expect(localStorage.removeItem).toHaveBeenCalledWith('user');
      expect(localStorage.removeItem).toHaveBeenCalledWith('auth_token');
    });

    it('sets todos', () => {
      const todos = [
        { id: 1, title: 'Test Todo 1' },
        { id: 2, title: 'Test Todo 2' }
      ];
      
      store.commit('setTodos', todos);
      expect(store.state.todos).toEqual(todos);
    });

    it('adds a new todo to the start of the list', () => {
      // Start with initial todos
      store.state.todos = [
        { id: 1, title: 'Existing Todo' }
      ];
      
      const newTodo = { id: 2, title: 'New Todo' };
      store.commit('addTodo', newTodo);
      
      expect(store.state.todos.length).toBe(2);
      expect(store.state.todos[0]).toEqual(newTodo);
    });

    it('updates an existing todo', () => {
      // Set initial todos
      store.state.todos = [
        { id: 1, title: 'Todo 1', completed: false },
        { id: 2, title: 'Todo 2', completed: false }
      ];
      
      const updatedTodo = { id: 1, title: 'Updated Todo', completed: true };
      store.commit('updateTodo', updatedTodo);
      
      expect(store.state.todos[0]).toEqual(updatedTodo);
      expect(store.state.todos.length).toBe(2);
    });

    it('removes a todo by id', () => {
      // Set initial todos
      store.state.todos = [
        { id: 1, title: 'Todo 1' },
        { id: 2, title: 'Todo 2' }
      ];
      
      store.commit('removeTodo', 1);
      
      expect(store.state.todos.length).toBe(1);
      expect(store.state.todos[0].id).toBe(2);
    });

    it('toggles dark mode', () => {
      // Dark mode should initially be false
      expect(store.state.darkMode).toBe(false);
      
      // First toggle to true
      store.commit('toggleDarkMode');
      
      // Check state and localStorage call
      expect(store.state.darkMode).toBe(true);
      expect(localStorage.setItem).toHaveBeenCalledWith('darkMode', true);
      
      // Reset mock calls to check second toggle
      vi.clearAllMocks();
      
      // Second toggle back to false
      store.commit('toggleDarkMode');
      
      // Check state and localStorage call
      expect(store.state.darkMode).toBe(false);
      expect(localStorage.setItem).toHaveBeenCalledWith('darkMode', false);
    });
  });

  describe('actions', () => {
    it('login - successful', async () => {
      const userData = { id: 1, name: 'Test User' };
      const token = 'fake-token';
      
      axios.post.mockResolvedValue({
        data: { user: userData, token }
      });
      
      await store.dispatch('login', { email: 'test@example.com', password: 'password' });
      
      expect(store.state.user).toEqual(userData);
      expect(store.state.loading).toBe(false);
      expect(store.state.error).toBeNull();
      expect(localStorage.setItem).toHaveBeenCalledWith('auth_token', token);
      expect(axios.defaults.headers.common['Authorization']).toBe(`Bearer ${token}`);
    });

    it('login - failure', async () => {
      axios.post.mockRejectedValue({
        response: { data: { message: 'Invalid credentials' } }
      });
      
      try {
        await store.dispatch('login', { email: 'test@example.com', password: 'wrong' });
        // Should not reach here
        expect(true).toBe(false);
      } catch (error) {
        expect(store.state.user).toBeNull();
        expect(store.state.loading).toBe(false);
        expect(store.state.error).toBe('Invalid credentials');
      }
    });

    it('logout clears user data', async () => {
      // Setup initial state with user and token
      store.state.user = { id: 1, name: 'Test User' };
      
      await store.dispatch('logout');
      
      expect(store.state.user).toBeNull();
      expect(localStorage.removeItem).toHaveBeenCalledWith('user');
      expect(localStorage.removeItem).toHaveBeenCalledWith('auth_token');
      expect(axios.defaults.headers.common['Authorization']).toBe('');
    });

    it('fetchTodos successfully gets todos', async () => {
      const todos = [
        { id: 1, title: 'Todo 1' },
        { id: 2, title: 'Todo 2' }
      ];
      
      axios.get.mockResolvedValue({ data: todos });
      
      await store.dispatch('fetchTodos');
      
      expect(axios.get).toHaveBeenCalledWith('/todos');
      expect(store.state.todos).toEqual(todos);
      expect(store.state.loading).toBe(false);
    });

    it('fetchTodos with params builds correct query string', async () => {
      axios.get.mockResolvedValue({ data: [] });
      
      await store.dispatch('fetchTodos', {
        category: 1,
        status: 'active',
        search: 'test query'
      });
      
      expect(axios.get).toHaveBeenCalledWith('/todos?category=1&status=active&search=test%20query');
    });

    it('createTodo adds a new todo', async () => {
      const newTodo = { title: 'New Todo', description: 'Description' };
      const savedTodo = { id: 1, ...newTodo };
      
      axios.post.mockResolvedValue({ data: savedTodo });
      
      await store.dispatch('createTodo', newTodo);
      
      expect(axios.post).toHaveBeenCalledWith('/todos', newTodo);
      expect(store.state.todos[0]).toEqual(savedTodo);
    });

    it('updateTodo updates an existing todo', async () => {
      store.state.todos = [
        { id: 1, title: 'Old Title', completed: false }
      ];
      
      const updatedTodo = { id: 1, title: 'New Title', completed: true };
      
      axios.put.mockResolvedValue({ data: updatedTodo });
      
      await store.dispatch('updateTodo', updatedTodo);
      
      expect(axios.put).toHaveBeenCalledWith('/todos/1', updatedTodo);
      expect(store.state.todos[0]).toEqual(updatedTodo);
    });

    it('deleteTodo removes a todo', async () => {
      store.state.todos = [
        { id: 1, title: 'Todo 1' },
        { id: 2, title: 'Todo 2' }
      ];
      
      axios.delete.mockResolvedValue({});
      
      await store.dispatch('deleteTodo', 1);
      
      expect(axios.delete).toHaveBeenCalledWith('/todos/1');
      expect(store.state.todos.length).toBe(1);
      expect(store.state.todos[0].id).toBe(2);
    });

    it('toggleDarkMode commits the toggle mutation', async () => {
      await store.dispatch('toggleDarkMode');
      
      expect(store.state.darkMode).toBe(true);
      expect(localStorage.setItem).toHaveBeenCalledWith('darkMode', true);
    });
  });

  describe('getters', () => {
    it('isAuthenticated returns true when user exists', () => {
      store.state.user = { id: 1, name: 'Test User' };
      expect(store.getters.isAuthenticated).toBe(true);
    });

    it('isAuthenticated returns false when user is null', () => {
      store.state.user = null;
      expect(store.getters.isAuthenticated).toBe(false);
    });

    it('isDarkMode returns the darkMode state', () => {
      store.state.darkMode = true;
      expect(store.getters.isDarkMode).toBe(true);
      
      store.state.darkMode = false;
      expect(store.getters.isDarkMode).toBe(false);
    });
  });
}); 