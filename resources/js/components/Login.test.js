import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import Login from './Login.vue';

// Initialize mocks at the module level
let mockStore;
let mockRouter;

// Mock dependencies
vi.mock('vuex', () => ({
  useStore: () => mockStore
}));

vi.mock('vue-router', () => ({
  useRouter: () => mockRouter,
  useRoute: () => ({ query: {} })
}));

describe('Login.vue', () => {
  let wrapper;
  
  beforeEach(() => {
    // Use fake timers
    vi.useFakeTimers();
    
    // Reset mocks
    vi.clearAllMocks();
    
    // Configure mocks
    mockStore = {
      dispatch: vi.fn().mockResolvedValue({}),
      state: {
        loading: false,
        error: null
      }
    };
    
    mockRouter = {
      push: vi.fn()
    };
    
    // Mount component
    wrapper = mount(Login, {
      global: {
        stubs: {
          'router-link': true
        }
      }
    });
  });
  
  afterEach(() => {
    // Clean up fake timers
    vi.restoreAllMocks();
    vi.useRealTimers();
  });
  
  it('renders the login form', () => {
    // Should have a form
    expect(wrapper.find('form').exists()).toBe(true);
    
    // Should have email and password inputs
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    
    // Should have a submit button
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });
  
  it('updates email and password on input', async () => {
    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');
    
    await emailInput.setValue('test@example.com');
    await passwordInput.setValue('password123');
    
    // Check that the form data is updated
    expect(wrapper.vm.form.email).toBe('test@example.com');
    expect(wrapper.vm.form.password).toBe('password123');
  });
  
  it('dispatches login action when form is submitted', async () => {
    const loginData = {
      email: 'test@example.com',
      password: 'password123'
    };
    
    // Set form values
    await wrapper.find('input[type="email"]').setValue(loginData.email);
    await wrapper.find('input[type="password"]').setValue(loginData.password);
    
    // Submit form
    await wrapper.find('form').trigger('submit');
    
    // Login action should be dispatched with login data
    expect(mockStore.dispatch).toHaveBeenCalledWith('login', expect.objectContaining(loginData));
  });
  
  it('redirects to home on successful login', async () => {
    // Mock successful login
    mockStore.dispatch.mockResolvedValue({ id: 1, name: 'Test User' });
    
    // Set form values
    await wrapper.find('input[type="email"]').setValue('test@example.com');
    await wrapper.find('input[type="password"]').setValue('password123');
    
    // Submit form
    await wrapper.find('form').trigger('submit');
    
    // Wait for async operations
    await vi.runAllTimers();
    
    // Should redirect to home page
    expect(mockRouter.push).toHaveBeenCalledWith('/');
  });
  
  it('shows error message when login fails', async () => {
    // Mock failed login
    const errorResponse = { 
      response: { 
        data: { 
          message: 'Invalid credentials' 
        } 
      } 
    };
    mockStore.dispatch.mockRejectedValue(errorResponse);
    
    // Set form values
    await wrapper.find('input[type="email"]').setValue('test@example.com');
    await wrapper.find('input[type="password"]').setValue('wrong-password');
    
    // Submit form
    await wrapper.find('form').trigger('submit');
    
    // Wait for promises to resolve
    await vi.runAllTimers();
    
    // Should display the error message in the component
    expect(wrapper.find('.bg-red-100').exists()).toBe(true);
    expect(wrapper.find('.bg-red-100').text()).toContain('Invalid credentials');
  });
  
  it('disables the form when loading', async () => {
    // Set loading state
    wrapper.vm.loading = true;
    
    // Force a re-render
    await wrapper.vm.$nextTick();
    
    // Submit button should be disabled
    expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
  });
  
  it('shows loading indicator when loading', async () => {
    // Set loading state
    wrapper.vm.loading = true;
    
    // Force a re-render
    await wrapper.vm.$nextTick();
    
    // Should show loading text
    expect(wrapper.find('button[type="submit"]').text()).toContain('Signing in...');
  });
  
  it('has a link to the registration page', () => {
    // Only check that router-link exists with correct attributes
    const registerLink = wrapper.find('router-link-stub');
    expect(registerLink.exists()).toBe(true);
    expect(registerLink.attributes('to')).toBe('/register');
    
    // Check parent element exists
    const linkParent = wrapper.find('div.text-center');
    expect(linkParent.exists()).toBe(true);
  });
}); 