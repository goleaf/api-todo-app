/**
 * Example test file demonstrating testUtils.js usage.
 * 
 * This file shows how to use the test helper utilities to simplify testing Vue components.
 * The tests use a mock component defined in this file, but the same techniques work for
 * real Vue components imported from .vue files.
 * 
 * @see /docs/test-helpers-guide.md for more examples and documentation
 */
import { describe, it, expect, afterEach, vi } from 'vitest';
import { 
  setupComponentTest, 
  fillForm, 
  submitForm, 
  hasErrorMessage, 
  createApiError,
  waitForElement,
  wasEventEmitted
} from '../tests/utils/testUtils';

// Example component (this would normally be imported from a .vue file)
const ExampleForm = {
  template: `
    <div>
      <h1>Example Form</h1>
      <div v-if="error" class="error-message">{{ error }}</div>
      <form @submit.prevent="submitForm">
        <div>
          <label for="name">Name</label>
          <input type="text" id="name" v-model="form.name" />
        </div>
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" v-model="form.email" />
        </div>
        <div>
          <label for="role">Role</label>
          <select id="role" v-model="form.role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" :disabled="loading">Submit</button>
      </form>
      <div v-if="success" class="success-message">Form submitted successfully!</div>
    </div>
  `,
  data() {
    return {
      form: {
        name: '',
        email: '',
        role: 'user'
      },
      loading: false,
      error: null,
      success: false
    };
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.error = null;
      
      try {
        // This would normally call an API endpoint
        const response = await this.$store.dispatch('submitForm', this.form);
        this.success = true;
        this.$emit('form-submitted', response);
        
        // If user is authorized, redirect to dashboard
        if (this.$store.getters.isAuthenticated) {
          this.$router.push('/dashboard');
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred';
      } finally {
        this.loading = false;
      }
    }
  }
};

describe('ExampleForm', () => {
  // Clean up after each test
  afterEach(() => {
    vi.restoreAllMocks();
    vi.useRealTimers();
  });
  
  it('renders the form correctly', () => {
    // Setup component with minimal configuration
    const { wrapper } = setupComponentTest(ExampleForm);
    
    // Verify all form elements exist
    expect(wrapper.find('h1').text()).toBe('Example Form');
    expect(wrapper.find('input#name').exists()).toBe(true);
    expect(wrapper.find('input#email').exists()).toBe(true);
    expect(wrapper.find('select#role').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });
  
  it('fills the form and submits it successfully', async () => {
    // Setup component with fake timers and store configured
    const { wrapper, store, routerPush } = setupComponentTest(ExampleForm, {
      useFakeTimers: true,
      storeOptions: {
        // Mock successful response from store.dispatch
        dispatchResponse: { id: 1, status: 'success' },
        // Set authenticated state
        isAuthenticated: true
      }
    });
    
    // Fill the form with a single function call
    await fillForm(wrapper, {
      'input#name': 'John Doe',
      'input#email': 'john@example.com',
      'select#role': 'admin'
    });
    
    // Submit the form and run timers to handle async operations
    await submitForm(wrapper);
    
    // Verify store dispatch was called with correct data
    expect(store.dispatch).toHaveBeenCalledWith('submitForm', {
      name: 'John Doe',
      email: 'john@example.com',
      role: 'admin'
    });
    
    // Verify success message is shown
    expect(wrapper.find('.success-message').exists()).toBe(true);
    
    // Verify event was emitted
    expect(wasEventEmitted(wrapper, 'form-submitted')).toBe(true);
    
    // Verify router redirect for authenticated users
    expect(routerPush).toHaveBeenCalledWith('/dashboard');
  });
  
  it('displays an error message when submission fails', async () => {
    // Setup component with store configured to return an error
    const { wrapper, store } = setupComponentTest(ExampleForm, {
      useFakeTimers: true
    });
    
    // Configure store to reject with an error
    store.dispatch.mockRejectedValueOnce(
      createApiError('Invalid input data', {
        email: ['Email is required']
      }, 422)
    );
    
    // Fill partial form data
    await fillForm(wrapper, {
      'input#name': 'Jane Doe'
      // Intentionally missing email
    });
    
    // Submit form
    await submitForm(wrapper);
    
    // Verify error message is displayed
    expect(hasErrorMessage(wrapper, 'Invalid input data')).toBe(true);
    
    // Verify button is re-enabled after error
    expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeUndefined();
  });
  
  it('disables the submit button during loading state', async () => {
    // Setup component
    const { wrapper } = setupComponentTest(ExampleForm);
    
    // Manually set loading state
    wrapper.vm.loading = true;
    await wrapper.vm.$nextTick();
    
    // Verify button is disabled when loading is true
    expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBe('');
    
    // Change loading state
    wrapper.vm.loading = false;
    await wrapper.vm.$nextTick();
    
    // Verify button is enabled when loading is false
    expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeUndefined();
  });
}); 