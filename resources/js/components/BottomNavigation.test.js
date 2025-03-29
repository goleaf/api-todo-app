import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createRouter, createWebHistory } from 'vue-router';
import BottomNavigation from './BottomNavigation.vue';

// Create mock components for routes
const Home = { template: '<div>Home Component</div>' };
const Calendar = { template: '<div>Calendar Component</div>' };
const Stats = { template: '<div>Stats Component</div>' };
const Profile = { template: '<div>Profile Component</div>' };

// Create router with proper components
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/calendar', name: 'calendar', component: Calendar },
    { path: '/stats', name: 'stats', component: Stats },
    { path: '/profile', name: 'profile', component: Profile }
  ]
});

describe('BottomNavigation.vue', () => {
  // Set up router for each test
  beforeEach(async () => {
    router.push('/');
    await router.isReady();
  });

  it('renders all navigation items', async () => {
    const wrapper = mount(BottomNavigation, {
      global: {
        plugins: [router],
        stubs: {
          'font-awesome-icon': true
        }
      }
    });
    
    await router.isReady();
    
    const navItems = wrapper.findAll('.nav-item');
    expect(navItems.length).toBe(4); // Home, Calendar, Stats, Profile
  });
  
  it('highlights the active route', async () => {
    // Start at home route
    await router.push('/');
    await router.isReady();
    
    const wrapper = mount(BottomNavigation, {
      global: {
        plugins: [router],
        stubs: {
          'font-awesome-icon': true
        }
      }
    });
    
    // Home should be active initially
    const homeSpan = wrapper.find('[data-testid="home-nav"] span');
    expect(homeSpan.classes()).toContain('text-purple-600');
    
    // Navigate to calendar
    await router.push('/calendar');
    await wrapper.vm.$nextTick();
    
    // Calendar should now be active
    const calendarSpan = wrapper.find('[data-testid="calendar-nav"] span');
    expect(calendarSpan.classes()).toContain('text-purple-600');
  });
  
  it('navigates to the correct route when clicked', async () => {
    const wrapper = mount(BottomNavigation, {
      global: {
        plugins: [router],
        stubs: {
          'font-awesome-icon': true
        }
      }
    });
    
    await router.isReady();
    
    // Click on the calendar item
    const calendarItem = wrapper.findAll('.nav-item')[1];
    await calendarItem.trigger('click');
    
    // Check if router path changed
    expect(router.currentRoute.value.path).toBe('/calendar');
  });
  
  it('has accessible aria labels', async () => {
    const wrapper = mount(BottomNavigation, {
      global: {
        plugins: [router],
        stubs: {
          'font-awesome-icon': true
        }
      }
    });
    
    await router.isReady();
    
    // Check if nav has aria-label
    expect(wrapper.find('nav').attributes('aria-label')).toBeDefined();
  });
}); 