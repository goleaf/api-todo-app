import { describe, it, expect, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';

// Create a simple component for router-view
const Home = {
  name: 'Home',
  template: '<div class="home">Home Page</div>'
};

// Create mock components to avoid importing all components
vi.mock('./UserHeader.vue', () => ({
  default: {
    name: 'UserHeader',
    template: '<div class="user-header">User Header</div>'
  }
}));

vi.mock('./DarkModeToggle.vue', () => ({
  default: {
    name: 'DarkModeToggle',
    template: '<div class="dark-mode-toggle">Dark Mode Toggle</div>'
  }
}));

vi.mock('./BottomNavigation.vue', () => ({
  default: {
    name: 'BottomNavigation',
    template: '<div class="bottom-navigation">Bottom Navigation</div>'
  }
}));

vi.mock('./WelcomeView.vue', () => ({
  default: {
    name: 'WelcomeView',
    template: '<div class="welcome-view">Welcome View</div>'
  }
}));

// Mock router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: Home }
  ]
});

// Mock store
const createMockStore = (isAuthenticated = false) => createStore({
  state: {
    user: isAuthenticated ? { id: 1, name: 'Test User' } : null,
    darkMode: false
  },
  getters: {
    isAuthenticated: state => !!state.user
  },
  actions: {
    logout: vi.fn()
  }
});

describe('App.vue', () => {
  it('renders the welcome view for unauthenticated users at welcome path', async () => {
    const store = createMockStore(false);
    
    // Mock the window.location.pathname
    Object.defineProperty(window, 'location', {
      value: { pathname: '/welcome' }
    });
    
    const wrapper = mount(App, {
      global: {
        plugins: [store, router],
        stubs: {
          'router-view': true,
          'router-link': true
        }
      }
    });
    
    await flushPromises();
    
    expect(wrapper.html()).toContain('welcome-view');
    expect(wrapper.html()).not.toContain('bottom-navigation');
  });
  
  it('renders the app shell with bottom navigation for authenticated users', async () => {
    const store = createMockStore(true);
    
    // Mock the window.location.pathname
    Object.defineProperty(window, 'location', {
      value: { pathname: '/' }
    });
    
    const wrapper = mount(App, {
      global: {
        plugins: [store, router],
        stubs: {
          'router-view': true,
          'router-link': true
        }
      }
    });
    
    await flushPromises();
    
    expect(wrapper.html()).toContain('bottom-navigation');
    expect(wrapper.html()).toContain('user-header');
    expect(wrapper.html()).toContain('dark-mode-toggle');
    expect(wrapper.html()).not.toContain('welcome-view');
  });
}); 