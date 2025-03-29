import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import CalendarView from './CalendarView.vue';
import { format, startOfMonth, endOfMonth, eachDayOfInterval } from 'date-fns';

// Mock dependencies
vi.mock('vuex', () => ({
  useStore: vi.fn()
}));

vi.mock('vue-router', () => ({
  useRouter: vi.fn()
}));

describe('CalendarView.vue', () => {
  let wrapper;
  let mockStore;
  
  const today = new Date();
  const firstDayOfMonth = startOfMonth(today);
  const lastDayOfMonth = endOfMonth(today);
  const daysInMonth = eachDayOfInterval({ start: firstDayOfMonth, end: lastDayOfMonth });
  
  const mockTodos = [
    { 
      id: 1, 
      title: 'Task due today', 
      due_date: format(today, 'yyyy-MM-dd'),
      completed: false,
      priority: 2,
      category_id: 1
    },
    { 
      id: 2, 
      title: 'Completed task', 
      due_date: format(today, 'yyyy-MM-dd'),
      completed: true,
      priority: 1,
      category_id: 2
    },
    { 
      id: 3, 
      title: 'Future task', 
      due_date: format(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 5), 'yyyy-MM-dd'),
      completed: false,
      priority: 0,
      category_id: 1
    }
  ];
  
  const mockCategories = [
    { id: 1, name: 'Work', color: 'blue' },
    { id: 2, name: 'Personal', color: 'green' }
  ];
  
  beforeEach(() => {
    // Reset mocks
    vi.clearAllMocks();
    
    // Setup mock store
    mockStore = {
      state: {
        todos: mockTodos,
        categories: mockCategories,
        loading: false
      },
      dispatch: vi.fn().mockResolvedValue({}),
      getters: {
        getCategories: () => mockCategories
      }
    };
    
    vi.mocked(require('vuex').useStore).mockReturnValue(mockStore);
    
    // Mount component
    wrapper = mount(CalendarView);
  });
  
  it('renders the calendar title with current month and year', () => {
    const currentMonthYear = format(today, 'MMMM yyyy');
    expect(wrapper.find('h1').text()).toContain(currentMonthYear);
  });
  
  it('renders a grid with the correct number of days', () => {
    const dayElements = wrapper.findAll('.calendar-day');
    
    // The calendar should show at least the days in the month
    // (may include some days from prev/next month to fill the grid)
    expect(dayElements.length).toBeGreaterThanOrEqual(daysInMonth.length);
  });
  
  it('highlights the current day', () => {
    const currentDay = format(today, 'd');
    const currentDayElements = wrapper.findAll('.calendar-day.current-month');
    
    // Find the element with the current day number
    const todayElement = Array.from(currentDayElements).find(
      el => el.text().includes(currentDay)
    );
    
    expect(todayElement).toBeDefined();
    expect(todayElement.classes()).toContain('today');
  });
  
  it('shows tasks on their due dates', () => {
    const currentDay = format(today, 'd');
    const currentDayElements = wrapper.findAll('.calendar-day.current-month');
    
    // Find today's element in the calendar
    const todayElement = Array.from(currentDayElements).find(
      el => el.text().includes(currentDay)
    );
    
    // Today's element should show tasks
    const taskElements = todayElement.findAll('.calendar-task');
    expect(taskElements.length).toBe(2); // We have 2 tasks due today in our mock
    
    // Task titles should be visible
    expect(todayElement.text()).toContain('Task due today');
    expect(todayElement.text()).toContain('Completed task');
  });
  
  it('displays completed tasks with strikethrough', () => {
    const currentDay = format(today, 'd');
    const currentDayElements = wrapper.findAll('.calendar-day.current-month');
    
    // Find today's element in the calendar
    const todayElement = Array.from(currentDayElements).find(
      el => el.text().includes(currentDay)
    );
    
    // Find the completed task
    const completedTaskElement = todayElement.findAll('.calendar-task')[1]; // Second task is completed
    expect(completedTaskElement.classes()).toContain('completed');
  });
  
  it('navigates to the previous month when previous button is clicked', async () => {
    const prevButton = wrapper.find('.prev-month-button');
    expect(prevButton.exists()).toBe(true);
    
    // Current month/year text
    const initialMonthYear = wrapper.find('h1').text();
    
    // Click to go to previous month
    await prevButton.trigger('click');
    
    // Month/year text should be different
    const newMonthYear = wrapper.find('h1').text();
    expect(newMonthYear).not.toBe(initialMonthYear);
  });
  
  it('navigates to the next month when next button is clicked', async () => {
    const nextButton = wrapper.find('.next-month-button');
    expect(nextButton.exists()).toBe(true);
    
    // Current month/year text
    const initialMonthYear = wrapper.find('h1').text();
    
    // Click to go to next month
    await nextButton.trigger('click');
    
    // Month/year text should be different
    const newMonthYear = wrapper.find('h1').text();
    expect(newMonthYear).not.toBe(initialMonthYear);
  });
  
  it('shows "Today" button that returns to current month', async () => {
    const todayButton = wrapper.find('.today-button');
    expect(todayButton.exists()).toBe(true);
    
    // Go to next month first
    await wrapper.find('.next-month-button').trigger('click');
    
    // Then click "Today" button
    await todayButton.trigger('click');
    
    // Should show current month again
    const currentMonthYear = format(today, 'MMMM yyyy');
    expect(wrapper.find('h1').text()).toContain(currentMonthYear);
  });
  
  it('fetches todos when the component is mounted', () => {
    expect(mockStore.dispatch).toHaveBeenCalledWith('fetchTodos');
  });
  
  it('displays a loading indicator when loading', async () => {
    // Set loading state
    mockStore.state.loading = true;
    
    // Force re-render
    await wrapper.vm.$nextTick();
    
    // Should show loading indicator
    const loadingIndicator = wrapper.find('.loading-indicator');
    expect(loadingIndicator.exists()).toBe(true);
    expect(loadingIndicator.isVisible()).toBe(true);
  });
  
  it('opens task details when a task is clicked', async () => {
    const currentDay = format(today, 'd');
    const currentDayElements = wrapper.findAll('.calendar-day.current-month');
    
    // Find today's element in the calendar
    const todayElement = Array.from(currentDayElements).find(
      el => el.text().includes(currentDay)
    );
    
    // Find and click the first task
    const firstTaskElement = todayElement.find('.calendar-task');
    await firstTaskElement.trigger('click');
    
    // Should emit an event or change the view to show task details
    expect(wrapper.emitted('view-task')).toBeTruthy();
    expect(wrapper.emitted('view-task')[0]).toEqual([1]); // First task has ID 1
  });
}); 