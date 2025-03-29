import { describe, it, expect, vi, beforeEach } from 'vitest';
import { createRouter, createWebHistory } from 'vue-router';
import TodoList from '../components/TodoList.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import TodoDetail from '../components/TodoDetail.vue';
import CalendarView from '../components/CalendarView.vue';
import StatsView from '../components/StatsView.vue';
import ProfileView from '../components/ProfileView.vue';
import WelcomeView from '../components/WelcomeView.vue';

// Mock components
vi.mock('../components/TodoList.vue', () => ({
  default: { name: 'TodoList' }
}));
vi.mock('../components/Login.vue', () => ({
  default: { name: 'Login' }
}));
vi.mock('../components/Register.vue', () => ({
  default: { name: 'Register' }
}));
vi.mock('../components/TodoDetail.vue', () => ({
  default: { name: 'TodoDetail' }
}));
vi.mock('../components/CalendarView.vue', () => ({
  default: { name: 'CalendarView' }
}));
vi.mock('../components/StatsView.vue', () => ({
  default: { name: 'StatsView' }
}));
vi.mock('../components/ProfileView.vue', () => ({
  default: { name: 'ProfileView' }
}));
vi.mock('../components/WelcomeView.vue', () => ({
  default: { name: 'WelcomeView' }
}));

// Mock localStorage
const localStorageMock = (() => {
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
    })
  };
})();

Object.defineProperty(window, 'localStorage', {
  value: localStorageMock,
  writable: true
});

describe('Router Configuration', () => {
  let router;
  
  beforeEach(() => {
    vi.clearAllMocks();
    localStorageMock.clear();
    
    // Define routes similar to those in app.js
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
    router = createRouter({
      history: createWebHistory(),
      routes
    });
    
    // Add navigation guard
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
  });

  it('redirects unauthenticated users from protected routes to login', async () => {
    // No token in localStorage
    await router.push('/');
    expect(router.currentRoute.value.path).toBe('/login');
    
    await router.push('/calendar');
    expect(router.currentRoute.value.path).toBe('/login');
    
    await router.push('/stats');
    expect(router.currentRoute.value.path).toBe('/login');
    
    await router.push('/profile');
    expect(router.currentRoute.value.path).toBe('/login');
    
    await router.push('/todos/1');
    expect(router.currentRoute.value.path).toBe('/login');
    
    await router.push('/todos/new');
    expect(router.currentRoute.value.path).toBe('/login');
  });

  it('allows unauthenticated users to access guest routes', async () => {
    // No token in localStorage
    await router.push('/login');
    expect(router.currentRoute.value.path).toBe('/login');
    
    await router.push('/register');
    expect(router.currentRoute.value.path).toBe('/register');
    
    await router.push('/welcome');
    expect(router.currentRoute.value.path).toBe('/welcome');
  });

  it('allows authenticated users to access protected routes', async () => {
    // Set token in localStorage
    localStorage.setItem('auth_token', 'fake-token');
    
    await router.push('/');
    expect(router.currentRoute.value.path).toBe('/');
    
    await router.push('/calendar');
    expect(router.currentRoute.value.path).toBe('/calendar');
    
    await router.push('/stats');
    expect(router.currentRoute.value.path).toBe('/stats');
    
    await router.push('/profile');
    expect(router.currentRoute.value.path).toBe('/profile');
    
    await router.push('/todos/1');
    expect(router.currentRoute.value.path).toBe('/todos/1');
    
    await router.push('/todos/new');
    expect(router.currentRoute.value.path).toBe('/todos/new');
  });

  it('redirects authenticated users from guest routes to home', async () => {
    // Set token in localStorage
    localStorage.setItem('auth_token', 'fake-token');
    
    await router.push('/login');
    expect(router.currentRoute.value.path).toBe('/');
    
    await router.push('/register');
    expect(router.currentRoute.value.path).toBe('/');
    
    await router.push('/welcome');
    expect(router.currentRoute.value.path).toBe('/');
  });

  it('navigates to the todo detail page with the correct ID', async () => {
    // Set token in localStorage
    localStorage.setItem('auth_token', 'fake-token');
    
    await router.push('/todos/42');
    expect(router.currentRoute.value.path).toBe('/todos/42');
    expect(router.currentRoute.value.params.id).toBe('42');
  });

  it('passes isNew prop when navigating to new todo route', async () => {
    // Set token in localStorage
    localStorage.setItem('auth_token', 'fake-token');
    
    await router.push('/todos/new');
    expect(router.currentRoute.value.path).toBe('/todos/new');
    
    // The route should have isNew: true in props
    const matchedRoute = router.currentRoute.value.matched[0];
    expect(matchedRoute.props.default).toEqual({ isNew: true });
  });
}); 