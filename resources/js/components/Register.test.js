import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import Register from './Register.vue';

// Create component for route
const Home = { template: '<div>Home Component</div>' };

// Mock router with proper component
const router = createRouter({
  history: createWebHistory(),
  routes: [{ path: '/', name: 'home', component: Home }]
});

// Mock store
const createVuexStore = (registerAction = vi.fn().mockResolvedValue()) => createStore({
  state: {
    user: null,
    loading: false,
    error: null
  },
  getters: {
    isAuthenticated: state => !!state.user
  },
  mutations: {
    setUser: vi.fn(),
    setLoading: vi.fn(),
    setError: vi.fn()
  },
  actions: {
    register: registerAction
  }
});

describe('Register.vue', () => {
  let wrapper;
  let store;
  let routerPush;

  beforeEach(() => {
    // Setup router mock
    routerPush = vi.fn();
    vi.spyOn(router, 'push').mockImplementation(routerPush);
    
    // Create store with mock register action
    store = createVuexStore();
    
    // Mount component
    wrapper = mount(Register, {
      global: {
        plugins: [store, router],
        stubs: {
          'router-link': true
        }
      }
    });
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  it('renders the registration form correctly', () => {
    expect(wrapper.find('h2').text()).toBe('Create Account');
    expect(wrapper.find('input[id="name"]').exists()).toBe(true);
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[id="password"]').exists()).toBe(true);
    expect(wrapper.find('input[id="password_confirmation"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').text()).toBe('Create Account');
  });

  it('updates the form data when inputs change', async () => {
    const nameInput = wrapper.find('input[id="name"]');
    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[id="password"]');
    const passwordConfirmationInput = wrapper.find('input[id="password_confirmation"]');
    
    await nameInput.setValue('Test User');
    await emailInput.setValue('test@example.com');
    await passwordInput.setValue('password');
    await passwordConfirmationInput.setValue('password');
    
    expect(wrapper.vm.form.name).toBe('Test User');
    expect(wrapper.vm.form.email).toBe('test@example.com');
    expect(wrapper.vm.form.password).toBe('password');
    expect(wrapper.vm.form.password_confirmation).toBe('password');
  });

  it('submits the form and calls register action', async () => {
    // Use fake timers to handle async operations
    vi.useFakeTimers();
    
    const registerAction = vi.fn().mockResolvedValue({});
    store = createVuexStore(registerAction);
    
    wrapper = mount(Register, {
      global: {
        plugins: [store, router],
        stubs: {
          'router-link': true
        }
      }
    });
    
    // Fill the form
    await wrapper.find('input[id="name"]').setValue('Test User');
    await wrapper.find('input[type="email"]').setValue('test@example.com');
    await wrapper.find('input[id="password"]').setValue('password');
    await wrapper.find('input[id="password_confirmation"]').setValue('password');
    
    // Submit the form
    await wrapper.find('form').trigger('submit');
    
    // Run timers to resolve promises
    await vi.runAllTimers();
    
    // Check if the register action was called
    expect(registerAction).toHaveBeenCalled();
    
    // Check the function was called with an object containing our form data
    const callArgs = registerAction.mock.calls[0];
    expect(callArgs[1]).toEqual(expect.objectContaining({
      name: 'Test User',
      email: 'test@example.com',
      password: 'password',
      password_confirmation: 'password'
    }));
    
    // Check if the router push was called
    expect(routerPush).toHaveBeenCalledWith('/');
    
    // Clean up timers
    vi.useRealTimers();
  });

  it('displays an error message when registration fails', async () => {
    const error = 'The email has already been taken.';
    const registerAction = vi.fn().mockRejectedValue({ 
      response: { 
        data: { 
          message: error,
          errors: {
            email: ['The email has already been taken.']
          }
        } 
      } 
    });
    
    store = createVuexStore(registerAction);
    
    wrapper = mount(Register, {
      global: {
        plugins: [store, router],
        stubs: {
          'router-link': true
        }
      }
    });
    
    // Fill the form with minimal data
    await wrapper.find('input[id="name"]').setValue('Test User');
    await wrapper.find('input[type="email"]').setValue('existing@example.com');
    
    // Submit the form
    await wrapper.find('form').trigger('submit');
    
    // Wait for the promise to reject and component to update
    await wrapper.vm.$nextTick();
    
    // Check for error message
    const errorElement = wrapper.find('.bg-red-100');
    expect(errorElement.exists()).toBe(true);
    expect(errorElement.text()).toBe(error);
    
    // Router push should not have been called
    expect(routerPush).not.toHaveBeenCalled();
  });

  it('disables the submit button while loading', async () => {
    // Make component think it's submitting
    wrapper.vm.loading = true;
    
    // Force component update
    await wrapper.vm.$nextTick();
    
    // Check if the button is disabled and shows loading text
    const submitButton = wrapper.find('button[type="submit"]');
    expect(submitButton.attributes('disabled')).toBeDefined();
    expect(submitButton.text()).toBe('Creating account...');
  });

  it('has a link to the login page', () => {
    // Only check that router-link exists with correct attributes
    const loginLink = wrapper.find('router-link-stub');
    expect(loginLink.exists()).toBe(true);
    expect(loginLink.attributes('to')).toBe('/login');
    
    // Check parent element exists
    const linkParent = wrapper.find('div.text-center');
    expect(linkParent.exists()).toBe(true);
  });

  it('requires password confirmation to match password', async () => {
    // Use fake timers
    vi.useFakeTimers();
    
    const registerAction = vi.fn().mockRejectedValue({ 
      response: { 
        data: { 
          message: 'The password field confirmation does not match.',
          errors: {
            password: ['The password field confirmation does not match.']
          }
        } 
      } 
    });
    
    store = createVuexStore(registerAction);
    
    wrapper = mount(Register, {
      global: {
        plugins: [store, router],
        stubs: {
          'router-link': true
        }
      }
    });
    
    // Fill the form with mismatched passwords
    await wrapper.find('input[id="name"]').setValue('Test User');
    await wrapper.find('input[type="email"]').setValue('test@example.com');
    await wrapper.find('input[id="password"]').setValue('password1');
    await wrapper.find('input[id="password_confirmation"]').setValue('password2');
    
    // Submit the form
    await wrapper.find('form').trigger('submit');
    
    // Run timers to resolve promises
    await vi.runAllTimers();
    
    // Error should be displayed
    const errorElement = wrapper.find('.bg-red-100');
    expect(errorElement.exists()).toBe(true);
    expect(errorElement.text()).toContain('confirmation does not match');
    
    // Clean up timers
    vi.useRealTimers();
  });
}); 