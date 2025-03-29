import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Todo from './Todo.vue';

// Create a mock store
vi.mock('vuex', () => ({
  useStore: () => ({
    dispatch: vi.fn().mockResolvedValue({}),
    state: {
      categories: [
        { id: 1, name: 'Work' },
        { id: 2, name: 'Personal' }
      ]
    }
  })
}));

// Create a mock router
vi.mock('vue-router', () => ({
  useRouter: () => ({
    push: vi.fn()
  })
}));

describe('Todo.vue', () => {
  const createWrapper = (props = {}) => {
    return mount(Todo, {
      props: {
        todo: {
          id: 1,
          title: 'Test Todo',
          description: 'This is a test todo',
          completed: false,
          priority: 1,
          progress: 25,
          due_date: new Date().toISOString(),
          category_id: 1,
          category: { id: 1, name: 'Work' },
          ...props
        }
      },
      global: {
        stubs: {
          'router-link': {
            template: '<div><slot /></div>'
          }
        }
      }
    });
  };
  
  it('renders todo title correctly', () => {
    const wrapper = createWrapper();
    expect(wrapper.find('label').text()).toContain('Test Todo');
  });
  
  it('displays description when provided', () => {
    const wrapper = createWrapper();
    const description = wrapper.find('.text-sm');
    expect(description.text()).toContain('This is a test todo');
  });
  
  it('shows the proper priority badge', () => {
    // High priority
    const wrapperHigh = createWrapper({ priority: 2 });
    expect(wrapperHigh.find('.border-red-500').exists()).toBe(true);
    
    // Medium priority
    const wrapperMedium = createWrapper({ priority: 1 });
    expect(wrapperMedium.find('.border-yellow-500').exists()).toBe(true);
    
    // Low priority
    const wrapperLow = createWrapper({ priority: 0 });
    expect(wrapperLow.find('.border-purple-500').exists()).toBe(true);
  });
  
  it('displays progress bar with correct percentage', () => {
    const wrapper = createWrapper({ progress: 75 });
    const progressBar = wrapper.find('.h-full.rounded-full');
    expect(progressBar.attributes('style')).toContain('width: 75%');
  });
  
  it('shows completed status correctly', async () => {
    const wrapper = createWrapper({ completed: true });
    expect(wrapper.find('label').classes()).toContain('line-through');
  });
  
  it('emits toggle event when checkbox is clicked', async () => {
    const wrapper = createWrapper();
    await wrapper.find('input[type="checkbox"]').trigger('change');
    
    expect(wrapper.emitted()).toHaveProperty('toggle');
    expect(wrapper.emitted().toggle[0]).toBeTruthy();
  });
  
  it('shows the delete confirmation dialog when delete is clicked', async () => {
    const wrapper = createWrapper();
    
    // Before clicking
    expect(wrapper.find('.fixed.inset-0').exists()).toBe(false);
    
    // Click delete button
    await wrapper.find('button[title="Delete todo"]').trigger('click');
    
    // Dialog should now be visible
    expect(wrapper.find('.fixed.inset-0').exists()).toBe(true);
    expect(wrapper.find('h3').text()).toBe('Delete Todo');
  });
  
  it('emits delete event when confirmed', async () => {
    const wrapper = createWrapper();
    
    // Click delete button
    await wrapper.find('button[title="Delete todo"]').trigger('click');
    
    // Click confirm button
    await wrapper.find('.bg-red-600').trigger('click');
    
    expect(wrapper.emitted()).toHaveProperty('delete');
    expect(wrapper.emitted().delete[0]).toEqual([1]); // The todo ID
  });
}); 