import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import WelcomeView from './WelcomeView.vue';

// Mock vue-router
vi.mock('vue-router', () => ({
  useRouter: vi.fn(),
  useRoute: vi.fn()
}));

describe('WelcomeView.vue', () => {
  beforeEach(() => {
    // Clear all mocks before each test
    vi.clearAllMocks();
    
    // Mock localStorage
    vi.spyOn(Storage.prototype, 'getItem');
  });

  it('renders the app name and tagline', () => {
    const wrapper = mount(WelcomeView, {
      global: {
        stubs: ['router-link']
      }
    });
    
    expect(wrapper.text()).toContain('Welcome to Taskify');
    expect(wrapper.text()).toContain('The smarter way to manage your tasks');
  });

  it('displays the app logo/icon', () => {
    const wrapper = mount(WelcomeView, {
      global: {
        stubs: ['router-link']
      }
    });
    
    const logo = wrapper.find('.h-24.w-24.rounded-full.bg-purple-600');
    expect(logo.exists()).toBe(true);
  });

  it('shows all feature sections', () => {
    const wrapper = mount(WelcomeView, {
      global: {
        stubs: ['router-link']
      }
    });
    
    // Find all feature titles
    const featureTitles = wrapper.findAll('.text-lg.font-medium');
    
    expect(featureTitles.length).toBe(3);
    expect(featureTitles[0].text()).toBe('Organize with Categories');
    expect(featureTitles[1].text()).toBe('Calendar Integration');
    expect(featureTitles[2].text()).toBe('Track Your Progress');
  });

  it('displays action buttons', () => {
    const wrapper = mount(WelcomeView, {
      global: {
        stubs: ['router-link']
      }
    });
    
    const startButton = wrapper.find('.bg-purple-600');
    expect(startButton.exists()).toBe(true);
    expect(startButton.text()).toBe('Get Started');
    
    const signInLink = wrapper.find('.text-purple-600');
    expect(signInLink.exists()).toBe(true);
    expect(signInLink.text()).toBe('Sign In');
  });

  it('shows version information', () => {
    const wrapper = mount(WelcomeView, {
      global: {
        stubs: ['router-link']
      }
    });
    
    expect(wrapper.text()).toContain('Version 1.0.0');
  });

  it('checks auth status through localStorage', () => {
    // Mount the component - this should trigger the beforeRouteEnter hook
    mount(WelcomeView, {
      global: {
        stubs: ['router-link']
      }
    });
    
    // Check if localStorage.getItem was called with 'auth_token'
    expect(localStorage.getItem).toHaveBeenCalledWith('auth_token');
  });

  it('redirects to home if already logged in', () => {
    // Mock localStorage to return a token
    Storage.prototype.getItem.mockReturnValueOnce('fake-token');
    
    const mockNext = vi.fn();
    
    // Call the beforeRouteEnter hook directly
    WelcomeView.beforeRouteEnter.call(null, null, null, mockNext);
    
    // Should redirect to '/'
    expect(mockNext).toHaveBeenCalledWith('/');
  });

  it('proceeds normally if not logged in', () => {
    // Mock localStorage to return null (not logged in)
    Storage.prototype.getItem.mockReturnValueOnce(null);
    
    const mockNext = vi.fn();
    
    // Call the beforeRouteEnter hook directly
    WelcomeView.beforeRouteEnter.call(null, null, null, mockNext);
    
    // Should call next() with no arguments
    expect(mockNext).toHaveBeenCalledWith();
  });
}); 