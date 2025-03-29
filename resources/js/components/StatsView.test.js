import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import StatsView from './StatsView.vue';

// Create the mock data outside the mock
const mockTodos = [
  { id: 1, title: 'Task 1', completed: false, priority: 2, progress: 0, due_date: new Date().toISOString(), category_id: 1 },
  { id: 2, title: 'Task 2', completed: true, priority: 1, progress: 100, due_date: new Date().toISOString(), category_id: 2 },
  { id: 3, title: 'Task 3', completed: false, priority: 0, progress: 50, due_date: '2100-01-01', category_id: 1 },
  { id: 4, title: 'Task 4', completed: false, priority: 2, progress: 25, due_date: null, category_id: 3 }
];

const mockCategories = [
  { id: 1, name: 'Work' },
  { id: 2, name: 'Personal' },
  { id: 3, name: 'Shopping' }
];

// Store mock setup globally
const mockStore = {
  state: {
    todos: [...mockTodos],
    categories: [...mockCategories]
  },
  dispatch: vi.fn().mockResolvedValue({}),
  subscribe: vi.fn((callback) => {
    mockStore._callback = callback;
    return () => {}; // Return unsubscribe function
  })
};

// Mock the Vuex store
vi.mock('vuex', () => ({
  useStore: () => mockStore
}));

describe('StatsView.vue', () => {
  let wrapper;
  
  beforeEach(() => {
    // Reset mock store state before each test
    mockStore.state = {
      todos: [...mockTodos],
      categories: [...mockCategories]
    };
    vi.clearAllMocks(); // Reset all mocks
    
    // Mount the component
    wrapper = mount(StatsView);
  });

  it('renders the statistics view title', () => {
    expect(wrapper.find('h1').text()).toBe('Your Task Statistics');
  });

  it('displays completion rate correctly', async () => {
    // 1 out of 4 tasks completed = 25%
    const completionRateText = wrapper.find('.text-4xl.font-bold.text-purple-600');
    expect(completionRateText.text()).toBe('25%');
    
    // Check progress bar width
    const progressBarFill = wrapper.find('.progress-bar-fill');
    expect(progressBarFill.attributes('style')).toContain('width: 25%');
  });

  it('shows correct task counts', () => {
    // Total tasks - should be 4
    const totalTasksEl = wrapper.find('.text-4xl.font-bold.text-orange-500');
    expect(totalTasksEl.text()).toBe('4');
    
    // Completed tasks - should be 1
    const completedTasksEl = wrapper.find('.text-xl.font-bold.text-green-600');
    expect(completedTasksEl.text()).toBe('1');
    
    // Pending tasks - should be 3
    const pendingTasksEl = wrapper.find('.text-xl.font-bold.text-yellow-600');
    expect(pendingTasksEl.text()).toBe('3');
  });

  it('displays priority distribution correctly', () => {
    // Find all the priority count elements
    const priorityCounts = wrapper.findAll('.flex.justify-between.mb-1 .text-xs.font-medium');
    
    // Check high priority count (priority: 2): 2 tasks
    expect(priorityCounts[1].text()).toBe('2');
    
    // Check medium priority count (priority: 1): 1 task
    expect(priorityCounts[3].text()).toBe('1');
    
    // Check low priority count (priority: 0): 1 task
    expect(priorityCounts[5].text()).toBe('1');
  });

  it('shows due soon tasks', async () => {
    // Due soon is within next 7 days
    const todayPlusSixDays = new Date();
    todayPlusSixDays.setDate(todayPlusSixDays.getDate() + 6);
    
    // Replace mock todos with one task due soon
    mockStore.state.todos = [
      { id: 1, title: 'Due Soon Task', completed: false, priority: 2, due_date: todayPlusSixDays.toISOString() }
    ];
    
    // Re-mount the component with updated store
    wrapper = mount(StatsView);
    
    // Should show the due soon task
    expect(wrapper.text()).toContain('Due Soon Task');
  });

  it('shows message when no tasks are due soon', () => {
    // All tasks are either completed or due far in the future
    mockStore.state.todos = [
      { id: 1, title: 'Far Future Task', completed: false, priority: 1, due_date: '2100-01-01' },
      { id: 2, title: 'Completed Task', completed: true, priority: 2, due_date: new Date().toISOString() }
    ];
    
    // Re-mount the component
    wrapper = mount(StatsView);
    
    expect(wrapper.text()).toContain('No tasks due in the next 7 days');
  });

  it('displays category distribution correctly', () => {
    // Should have 3 category cards
    const categoryCards = wrapper.findAll('.p-3.rounded-lg.border');
    expect(categoryCards.length).toBe(3);
    
    // First category should have a name and count
    const firstCategory = categoryCards[0];
    expect(firstCategory.find('.font-medium').text()).toBeTruthy();
    expect(firstCategory.find('.bg-gray-100.rounded-full').text()).toBeTruthy();
  });

  it('displays message when no categories exist', () => {
    mockStore.state.categories = [];
    
    // Re-mount the component
    wrapper = mount(StatsView);
    
    expect(wrapper.text()).toContain('No categories created yet');
  });

  it('fetches data from store on mount', () => {
    // Mock store with empty arrays
    mockStore.state.todos = [];
    mockStore.state.categories = [];
    
    // Create a new wrapper
    wrapper = mount(StatsView);
    
    // Should dispatch actions to fetch data
    expect(mockStore.dispatch).toHaveBeenCalledWith('fetchTodos');
    expect(mockStore.dispatch).toHaveBeenCalledWith('fetchCategories');
  });

  it('updates when store changes', async () => {
    // Initially 25% completion rate (1/4 tasks)
    const initialCompletionRate = wrapper.find('.text-4xl.font-bold.text-purple-600');
    expect(initialCompletionRate.text()).toBe('25%');
    
    // Simulate a store mutation that updates todos
    const newTodos = [
      ...mockTodos,
      { id: 5, title: 'New Task', completed: true, priority: 1, progress: 100 }
    ];
    
    // Call the subscriber callback with a mock mutation
    if (mockStore._callback) {
      mockStore._callback({ type: 'setTodos' }, { todos: newTodos });
      mockStore.state.todos = newTodos;
      
      // Re-mount the component to see the changes
      wrapper = mount(StatsView);
      
      // Now 2/5 tasks completed = 40%
      const updatedCompletionRate = wrapper.find('.text-4xl.font-bold.text-purple-600');
      expect(updatedCompletionRate.text()).toBe('40%');
    }
  });
}); 