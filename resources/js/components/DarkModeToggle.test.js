import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import DarkModeToggle from './DarkModeToggle.vue';

// Mock for the store
const mockStore = {
  getters: {
    isDarkMode: false
  },
  dispatch: vi.fn()
};

// Mock vuex
vi.mock('vuex', () => ({
  useStore: () => mockStore
}));

describe('DarkModeToggle.vue', () => {
  beforeEach(() => {
    // Reset mock store to default values before each test
    mockStore.getters.isDarkMode = false;
    mockStore.dispatch = vi.fn();
  });

  it('renders the light mode icon by default', () => {
    const wrapper = mount(DarkModeToggle);
    
    // Should show the moon icon in light mode
    expect(wrapper.find('svg path[d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"]').exists()).toBe(true);
    
    // Should not show the sun icon
    expect(wrapper.find('svg path[d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"]').exists()).toBe(false);
  });

  it('renders the dark mode icon when in dark mode', () => {
    // Set dark mode to true
    mockStore.getters.isDarkMode = true;
    
    const wrapper = mount(DarkModeToggle);
    
    // Should show the sun icon in dark mode
    expect(wrapper.find('svg path[d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"]').exists()).toBe(true);
    
    // Should not show the moon icon
    expect(wrapper.find('svg path[d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"]').exists()).toBe(false);
  });

  it('toggles dark mode when clicked', async () => {
    // Reset the mock
    mockStore.dispatch = vi.fn();
    
    const wrapper = mount(DarkModeToggle);
    
    // Click the toggle button
    await wrapper.find('button').trigger('click');
    
    // Should dispatch the toggleDarkMode action
    expect(mockStore.dispatch).toHaveBeenCalledWith('toggleDarkMode');
  });

  it('has the correct title attribute', () => {
    // Light mode
    mockStore.getters.isDarkMode = false;
    
    let wrapper = mount(DarkModeToggle);
    expect(wrapper.attributes('title')).toBe('Switch to dark mode');
    
    // Dark mode
    mockStore.getters.isDarkMode = true;
    
    wrapper = mount(DarkModeToggle);
    expect(wrapper.attributes('title')).toBe('Switch to light mode');
  });
}); 