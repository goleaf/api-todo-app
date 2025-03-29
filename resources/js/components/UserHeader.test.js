import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import UserHeader from './UserHeader.vue';

// Mock vuex store
vi.mock('vuex', () => ({
  useStore: vi.fn()
}));

// Mock vue-router
vi.mock('vue-router', () => ({
  useRouter: vi.fn()
}));

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn()
  }
}));

describe('UserHeader.vue', () => {
  const mockUser = {
    name: 'Test User',
    email: 'test@example.com',
    photo_url: null
  };
  
  const mockTodos = [
    { id: 1, title: 'Task 1', completed: false },
    { id: 2, title: 'Task 2', completed: true },
    { id: 3, title: 'Task 3', completed: false }
  ];
  
  let mockStore;
  
  beforeEach(() => {
    mockStore = {
      state: {
        user: mockUser,
        todos: mockTodos
      },
      commit: vi.fn(),
      dispatch: vi.fn()
    };
    
    vi.mocked(require('vuex').useStore).mockReturnValue(mockStore);
  });

  it('renders the user greeting correctly', () => {
    const wrapper = mount(UserHeader);
    expect(wrapper.text()).toContain('Hello, Test User!');
  });

  it('displays pending task count correctly', () => {
    const wrapper = mount(UserHeader);
    expect(wrapper.text()).toContain('2 tasks pending');
  });

  it('displays singular task text when only one task is pending', () => {
    mockStore.state.todos = [
      { id: 1, title: 'Task 1', completed: false },
      { id: 2, title: 'Task 2', completed: true }
    ];
    
    const wrapper = mount(UserHeader);
    expect(wrapper.text()).toContain('1 task pending');
  });

  it('shows user initials when no photo_url is available', () => {
    const wrapper = mount(UserHeader);
    const initialElement = wrapper.find('.rounded-full.bg-purple-600');
    
    expect(initialElement.exists()).toBe(true);
    expect(initialElement.text()).toBe('T');
  });

  it('shows user photo when photo_url is available', () => {
    mockStore.state.user = {
      ...mockUser,
      photo_url: 'https://example.com/photo.jpg'
    };
    
    const wrapper = mount(UserHeader);
    const photoElement = wrapper.find('img');
    
    expect(photoElement.exists()).toBe(true);
    expect(photoElement.attributes('src')).toBe('https://example.com/photo.jpg');
    expect(photoElement.attributes('alt')).toBe('Test User');
  });

  it('opens and closes dropdown when clicked', async () => {
    const wrapper = mount(UserHeader);
    
    // Dropdown should be closed initially
    expect(wrapper.find('.absolute.right-0.mt-2').isVisible()).toBe(false);
    
    // Click to open dropdown
    await wrapper.find('button').trigger('click');
    expect(wrapper.find('.absolute.right-0.mt-2').isVisible()).toBe(true);
    
    // Click again to close
    await wrapper.find('button').trigger('click');
    expect(wrapper.find('.absolute.right-0.mt-2').isVisible()).toBe(false);
  });

  it('shows correct user email in dropdown', async () => {
    const wrapper = mount(UserHeader);
    
    // Open dropdown
    await wrapper.find('button').trigger('click');
    
    expect(wrapper.text()).toContain('Signed in as test@example.com');
  });

  it('calls logout method when logout button is clicked', async () => {
    mockStore.dispatch.mockResolvedValue();
    
    // Mock window.location
    const originalLocation = window.location;
    delete window.location;
    window.location = { href: '' };
    
    const wrapper = mount(UserHeader);
    
    // Open dropdown
    await wrapper.find('button').trigger('click');
    
    // Find and click logout button
    const form = wrapper.find('form');
    await form.trigger('submit');
    
    expect(mockStore.dispatch).toHaveBeenCalledWith('logout');
    expect(window.location.href).toBe('/login');
    
    // Restore original location
    window.location = originalLocation;
  });
}); 