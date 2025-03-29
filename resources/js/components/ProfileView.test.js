import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import ProfileView from './ProfileView.vue';

// Mock vuex store
vi.mock('vuex', () => ({
  useStore: vi.fn()
}));

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn()
  }
}));

describe('ProfileView.vue', () => {
  let wrapper;
  let mockStore;
  const mockUser = {
    id: 1,
    name: 'Test User',
    email: 'test@example.com',
    created_at: '2023-07-15T12:30:00Z',
    photo_url: null
  };
  
  beforeEach(() => {
    // Reset mocks
    vi.clearAllMocks();
    
    // Setup mock store
    mockStore = {
      state: {
        user: mockUser,
        loading: false,
        error: null
      },
      dispatch: vi.fn().mockResolvedValue({}),
      commit: vi.fn()
    };
    
    vi.mocked(require('vuex').useStore).mockReturnValue(mockStore);
    
    // Mount component
    wrapper = mount(ProfileView);
  });
  
  it('renders the profile view title', () => {
    expect(wrapper.find('h1').text()).toBe('Your Profile');
  });
  
  it('displays the user information', () => {
    expect(wrapper.text()).toContain('Test User');
    expect(wrapper.text()).toContain('test@example.com');
  });
  
  it('shows account creation date', () => {
    // Should format the date
    expect(wrapper.text()).toContain('Member since');
  });
  
  it('shows user initials when no photo is available', () => {
    const avatarElement = wrapper.find('.profile-avatar');
    expect(avatarElement.exists()).toBe(true);
    
    // Should show initials (T for Test User)
    expect(avatarElement.text()).toBe('T');
  });
  
  it('shows user photo when available', async () => {
    // Update user with photo URL
    mockStore.state.user = {
      ...mockUser,
      photo_url: 'https://example.com/photo.jpg'
    };
    
    // Re-mount with updated store
    wrapper = mount(ProfileView);
    
    const avatarImg = wrapper.find('.profile-avatar img');
    expect(avatarImg.exists()).toBe(true);
    expect(avatarImg.attributes('src')).toBe('https://example.com/photo.jpg');
    expect(avatarImg.attributes('alt')).toBe('Test User');
  });
  
  it('allows editing profile information', async () => {
    // Find and click edit button
    const editButton = wrapper.find('.edit-profile-button');
    expect(editButton.exists()).toBe(true);
    
    await editButton.trigger('click');
    
    // Should show edit form
    const form = wrapper.find('form');
    expect(form.exists()).toBe(true);
    expect(form.isVisible()).toBe(true);
    
    // Fields should be pre-filled with current user data
    const nameInput = wrapper.find('input[id="name"]');
    const emailInput = wrapper.find('input[id="email"]');
    
    expect(nameInput.element.value).toBe('Test User');
    expect(emailInput.element.value).toBe('test@example.com');
  });
  
  it('validates profile update information', async () => {
    // Enter edit mode
    await wrapper.find('.edit-profile-button').trigger('click');
    
    // Set empty name (invalid)
    await wrapper.find('input[id="name"]').setValue('');
    
    // Try to submit
    await wrapper.find('form').trigger('submit.prevent');
    
    // Should show validation error
    expect(wrapper.text()).toContain('Name is required');
    
    // Should not dispatch update action
    expect(mockStore.dispatch).not.toHaveBeenCalled();
  });
  
  it('submits profile updates', async () => {
    // Enter edit mode
    await wrapper.find('.edit-profile-button').trigger('click');
    
    // Update name
    await wrapper.find('input[id="name"]').setValue('Updated Name');
    
    // Submit form
    await wrapper.find('form').trigger('submit.prevent');
    
    // Should dispatch update profile action
    expect(mockStore.dispatch).toHaveBeenCalledWith('updateProfile', expect.objectContaining({
      name: 'Updated Name',
      email: 'test@example.com'
    }));
  });
  
  it('shows loading state during profile update', async () => {
    // Set loading state
    mockStore.state.loading = true;
    
    // Enter edit mode
    await wrapper.find('.edit-profile-button').trigger('click');
    
    // Submit button should be disabled and show loading text
    const submitButton = wrapper.find('button[type="submit"]');
    expect(submitButton.attributes('disabled')).toBeDefined();
    expect(submitButton.text()).toContain('Saving');
  });
  
  it('shows error message when update fails', async () => {
    // Set error state
    mockStore.state.error = 'Failed to update profile';
    
    // Enter edit mode and then force re-render
    await wrapper.find('.edit-profile-button').trigger('click');
    await wrapper.vm.$nextTick();
    
    // Should display error message
    const errorElement = wrapper.find('.error-message');
    expect(errorElement.exists()).toBe(true);
    expect(errorElement.text()).toContain('Failed to update profile');
  });
  
  it('allows password change', async () => {
    // Find and click change password button
    const changePasswordButton = wrapper.find('.change-password-button');
    expect(changePasswordButton.exists()).toBe(true);
    
    await changePasswordButton.trigger('click');
    
    // Should show password change form
    const passwordForm = wrapper.find('.password-form');
    expect(passwordForm.exists()).toBe(true);
    expect(passwordForm.isVisible()).toBe(true);
  });
  
  it('validates password change', async () => {
    // Enter password change mode
    await wrapper.find('.change-password-button').trigger('click');
    
    // Set mismatched passwords
    await wrapper.find('input[id="new_password"]').setValue('newpassword');
    await wrapper.find('input[id="new_password_confirmation"]').setValue('different');
    
    // Try to submit
    await wrapper.find('.password-form').trigger('submit.prevent');
    
    // Should show validation error
    expect(wrapper.text()).toContain('Passwords do not match');
    
    // Should not dispatch change password action
    expect(mockStore.dispatch).not.toHaveBeenCalled();
  });
  
  it('submits password change', async () => {
    // Mock the dispatch for password change
    mockStore.dispatch.mockResolvedValue({});
    
    // Enter password change mode
    await wrapper.find('.change-password-button').trigger('click');
    
    // Set matching passwords
    await wrapper.find('input[id="current_password"]').setValue('currentpassword');
    await wrapper.find('input[id="new_password"]').setValue('newpassword');
    await wrapper.find('input[id="new_password_confirmation"]').setValue('newpassword');
    
    // Submit form
    await wrapper.find('.password-form').trigger('submit.prevent');
    
    // Should dispatch change password action
    expect(mockStore.dispatch).toHaveBeenCalledWith('changePassword', expect.objectContaining({
      current_password: 'currentpassword',
      password: 'newpassword',
      password_confirmation: 'newpassword'
    }));
  });
}); 