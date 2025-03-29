import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import Task from './Task.vue';

// Mock vuex store
vi.mock('vuex', () => ({
  useStore: vi.fn()
}));

// Mock vue-router
vi.mock('vue-router', () => ({
  useRouter: vi.fn()
}));

describe('Task.vue', () => {
  let wrapper;
  let mockStore;
  let mockRouter;
  
  const mockTask = {
    id: 1,
    title: 'Test Task',
    description: 'This is a test task',
    completed: false,
    due_date: new Date().toISOString(),
    priority: 2,
    category_id: 1,
    progress: 25
  };
  
  const mockCategories = [
    { id: 1, name: 'Work', color: 'blue' },
    { id: 2, name: 'Personal', color: 'green' }
  ];
  
  beforeEach(() => {
    mockStore = {
      state: {
        categories: mockCategories
      },
      getters: {
        getCategories: mockCategories
      },
      dispatch: vi.fn()
    };
    
    mockRouter = {
      push: vi.fn()
    };
    
    vi.mocked(require('vuex').useStore).mockReturnValue(mockStore);
    vi.mocked(require('vue-router').useRouter).mockReturnValue(mockRouter);
    
    wrapper = mount(Task, {
      props: {
        task: mockTask
      }
    });
  });
  
  it('renders the task title', () => {
    expect(wrapper.text()).toContain('Test Task');
  });
  
  it('displays task description', () => {
    expect(wrapper.text()).toContain('This is a test task');
  });
  
  it('shows the correct progress percentage', () => {
    expect(wrapper.text()).toContain('25%');
  });
  
  it('displays high priority indicator for high priority tasks', () => {
    // Our mock task has priority 2 (high)
    const priorityBadge = wrapper.find('.bg-red-100');
    expect(priorityBadge.exists()).toBe(true);
    expect(priorityBadge.text()).toContain('High');
  });
  
  it('displays correct category name', () => {
    const categoryBadge = wrapper.find('.category-badge');
    expect(categoryBadge.exists()).toBe(true);
    expect(categoryBadge.text()).toContain('Work');
  });
  
  it('properly formats the due date', () => {
    // Since we're using today's date, look for common date elements
    const dueDateElement = wrapper.find('.task-due-date');
    expect(dueDateElement.exists()).toBe(true);
    
    // Due date should contain elements like dates, time, or "Today"
    const dueDateText = dueDateElement.text();
    expect(dueDateText.includes('Today') || 
           /\d{1,2}(st|nd|rd|th)/.test(dueDateText) ||
           /\d{1,2}:\d{2}/.test(dueDateText)).toBe(true);
  });
  
  it('shows "Overdue" styling for past due dates', async () => {
    // Create a new wrapper with an overdue task
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    
    const overdueTask = {
      ...mockTask,
      due_date: yesterday.toISOString()
    };
    
    const overdueWrapper = mount(Task, {
      props: {
        task: overdueTask
      }
    });
    
    const dueDateElement = overdueWrapper.find('.task-due-date');
    expect(dueDateElement.classes()).toContain('text-red-600');
  });
  
  it('emits complete-task event when checkbox is clicked', async () => {
    const checkbox = wrapper.find('input[type="checkbox"]');
    await checkbox.setValue(true);
    
    expect(wrapper.emitted('complete-task')).toBeTruthy();
    expect(wrapper.emitted('complete-task')[0]).toEqual([mockTask.id]);
  });
  
  it('emits delete-task event when delete button is clicked', async () => {
    const deleteButton = wrapper.find('.delete-button');
    await deleteButton.trigger('click');
    
    expect(wrapper.emitted('delete-task')).toBeTruthy();
    expect(wrapper.emitted('delete-task')[0]).toEqual([mockTask.id]);
  });
  
  it('emits edit-task event when edit button is clicked', async () => {
    const editButton = wrapper.find('.edit-button');
    await editButton.trigger('click');
    
    expect(wrapper.emitted('edit-task')).toBeTruthy();
    expect(wrapper.emitted('edit-task')[0]).toEqual([mockTask.id]);
  });
  
  it('shows completed styling when task is complete', async () => {
    // Create a new wrapper with a completed task
    const completedTask = {
      ...mockTask,
      completed: true
    };
    
    const completedWrapper = mount(Task, {
      props: {
        task: completedTask
      }
    });
    
    // Check for completed styling (strikethrough, opacity, etc.)
    const taskTitle = completedWrapper.find('.task-title');
    expect(taskTitle.classes()).toContain('line-through');
  });
  
  it('shows progress bar with correct width', () => {
    const progressBar = wrapper.find('.progress-bar-fill');
    expect(progressBar.attributes('style')).toContain('width: 25%');
  });
}); 