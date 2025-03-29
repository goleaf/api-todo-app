import { describe, it, expect, vi, afterEach } from 'vitest';
import Login from './Login.vue';
import {
  setupComponentTest,
  fillForm,
  submitForm,
  createApiError,
  hasErrorMessage
} from '../tests/utils/testUtils';

describe('Login.vue (with test utilities)', () => {
  afterEach(() => {
    vi.resetAllMocks();
    vi.useRealTimers();
  });
  
  it('renders the login form', () => {
    // Setup the component with minimal configuration
    const { wrapper } = setupComponentTest(Login);
    
    // Verify that the form renders correctly
    expect(wrapper.find('form').exists()).toBe(true);
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });
  
  it('updates form data when inputs change', async () => {
    // Setup the component
    const { wrapper } = setupComponentTest(Login);
    
    // Fill the form using our utility
    await fillForm(wrapper, {
      'input[type="email"]': 'test@example.com',
      'input[type="password"]': 'password123'
    });
    
    // Verify data is updated
    expect(wrapper.vm.form.email).toBe('test@example.com');
    expect(wrapper.vm.form.password).toBe('password123');
  });
  
  it('dispatches login action when form is submitted', async () => {
    // Setup component with fake timers
    const { wrapper, store, runTimers } = setupComponentTest(Login, {
      useFakeTimers: true
    });
    
    // Fill and submit the form
    await fillForm(wrapper, {
      'input[type="email"]': 'test@example.com',
      'input[type="password"]': 'password123'
    });
    
    await submitForm(wrapper);
    
    // Check store actions
    expect(store.dispatch).toHaveBeenCalledWith('login', expect.objectContaining({
      email: 'test@example.com',
      password: 'password123'
    }));
  });
  
  it('redirects to home on successful login', async () => {
    // Setup component with custom store response
    const { wrapper, store, routerPush, runTimers } = setupComponentTest(Login, {
      useFakeTimers: true,
      storeOptions: {
        dispatchResponse: { id: 1, name: 'Test User' }
      }
    });
    
    // Fill and submit form
    await fillForm(wrapper, {
      'input[type="email"]': 'test@example.com',
      'input[type="password"]': 'password123'
    });
    
    await submitForm(wrapper);
    
    // Check that router.push was called with home route
    expect(routerPush).toHaveBeenCalledWith('/');
  });
  
  it('shows error message when login fails', async () => {
    // Create an API error
    const errorMessage = 'Invalid credentials';
    const apiError = createApiError(errorMessage);
    
    // Setup component with rejection
    const { wrapper, store, runTimers } = setupComponentTest(Login, {
      useFakeTimers: true,
      storeOptions: {
        dispatchResponse: Promise.reject(apiError)
      }
    });
    
    // Make store.dispatch reject with our error
    store.dispatch.mockRejectedValueOnce(apiError);
    
    // Fill and submit form
    await fillForm(wrapper, {
      'input[type="email"]': 'test@example.com',
      'input[type="password"]': 'wrong-password'
    });
    
    await submitForm(wrapper);
    
    // Check for error message
    expect(hasErrorMessage(wrapper, errorMessage)).toBe(true);
  });
  
  it('disables the form when loading', async () => {
    // Setup component
    const { wrapper } = setupComponentTest(Login);
    
    // Set loading state
    wrapper.vm.loading = true;
    await wrapper.vm.$nextTick();
    
    // Check disabled state
    expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
  });
  
  it('has a link to the registration page', () => {
    // Setup component
    const { wrapper } = setupComponentTest(Login);
    
    // Check for router link
    const registerLink = wrapper.find('router-link-stub');
    expect(registerLink.exists()).toBe(true);
    expect(registerLink.attributes('to')).toBe('/register');
  });
}); 