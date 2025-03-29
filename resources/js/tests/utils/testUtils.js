/**
 * Test utilities for Vue components
 * 
 * This file contains helper functions to simplify testing Vue components,
 * particularly those using Vuex and Vue Router.
 */
import { vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';

/**
 * Create a mocked Vue Router instance
 * @param {Array} additionalRoutes - Additional routes to add
 * @returns {Object} - A configured router instance
 */
export function createTestRouter(additionalRoutes = []) {
  const Home = { template: '<div>Home Component</div>' };
  
  const routes = [
    { path: '/', name: 'home', component: Home },
    ...additionalRoutes
  ];
  
  const router = createRouter({
    history: createWebHistory(),
    routes
  });
  
  // Mock router push/replace methods
  router.push = vi.fn();
  router.replace = vi.fn();
  
  return router;
}

/**
 * Create a mocked Vuex store
 * @param {Object} options - Store configuration options
 * @returns {Object} - A configured Vuex store
 */
export function createMockStore(options = {}) {
  const {
    dispatchResponse = {},
    isAuthenticated = false,
    user = null,
    loading = false,
    error = null,
    darkMode = false,
    state = {},
    getters = {}
  } = options;
  
  const store = createStore({
    state: {
      user,
      loading,
      error,
      darkMode,
      ...state
    },
    getters: {
      isAuthenticated: () => isAuthenticated,
      ...getters
    },
    actions: {},
    mutations: {}
  });
  
  // Mock dispatch to return a configurable response
  store.dispatch = vi.fn().mockResolvedValue(dispatchResponse);
  
  return store;
}

/**
 * Setup a component test with all dependencies mocked
 * @param {Object} Component - Vue component to test
 * @param {Object} options - Test configuration options
 * @returns {Object} - Test environment with wrapper, store, router and helpers
 */
export function setupComponentTest(Component, options = {}) {
  const {
    useFakeTimers = false,
    storeOptions = {},
    routes = [],
    usePlugins = true,
    stubs = {},
    mountOptions = {},
    props = {}
  } = options;
  
  // Setup fake timers if requested
  if (useFakeTimers) {
    vi.useFakeTimers();
  }
  
  // Create store and router
  const store = createMockStore(storeOptions);
  const router = createTestRouter(routes);
  
  // Capture router.push calls for easy testing
  const routerPush = router.push;
  
  // Setup mounting options
  const baseOptions = {
    global: {
      stubs: {
        transition: false,
        'router-link': true,
        'router-view': true,
        ...stubs
      },
      ...mountOptions.global
    },
    props,
    ...mountOptions
  };
  
  // Add plugins if requested
  if (usePlugins) {
    baseOptions.global.plugins = [store, router];
  }
  
  // Mount component
  const wrapper = mount(Component, baseOptions);
  
  // Helper for running timers
  const runTimers = async (ms = 0) => {
    if (useFakeTimers) {
      vi.advanceTimersByTime(ms);
      await wrapper.vm.$nextTick();
    }
  };
  
  // Helper for cleaning up timers
  const cleanupTimers = () => {
    if (useFakeTimers) {
      vi.useRealTimers();
    }
  };
  
  return {
    wrapper,
    store,
    router,
    routerPush,
    runTimers,
    cleanupTimers
  };
}

/**
 * Fill form inputs with values
 * @param {Object} wrapper - Vue test wrapper
 * @param {Object} fieldMap - Map of selectors to values
 * @returns {Promise} - Promise resolving when inputs are filled
 */
export async function fillForm(wrapper, fieldMap) {
  for (const [selector, value] of Object.entries(fieldMap)) {
    const input = wrapper.find(selector);
    await input.setValue(value);
  }
  
  // Allow Vue to update
  await wrapper.vm.$nextTick();
}

/**
 * Submit a form and optionally run timers
 * @param {Object} wrapper - Vue test wrapper
 * @param {String} formSelector - Selector for the form
 * @param {Boolean} shouldRunTimers - Whether to run timers after submit
 * @returns {Promise} - Promise resolving when form is submitted
 */
export async function submitForm(wrapper, formSelector = 'form', shouldRunTimers = true) {
  const form = wrapper.find(formSelector);
  await form.trigger('submit.prevent');
  
  // Allow Vue to update
  await wrapper.vm.$nextTick();
  
  // Run timers if needed (for async operations)
  if (shouldRunTimers) {
    vi.runAllTimers();
    await wrapper.vm.$nextTick();
  }
}

/**
 * Check if an error message is displayed
 * @param {Object} wrapper - Vue test wrapper
 * @param {String} errorMessage - Error message to check for
 * @param {String} errorSelector - Selector for error elements
 * @returns {Boolean} - Whether the error message is displayed
 */
export function hasErrorMessage(wrapper, errorMessage, errorSelector = '.error-message') {
  const errorElements = wrapper.findAll(errorSelector);
  
  for (const element of errorElements) {
    if (element.text().includes(errorMessage)) {
      return true;
    }
  }
  
  return false;
}

/**
 * Create a standardized API error response
 * @param {String} message - Main error message
 * @param {Object} errors - Validation errors object
 * @param {Number} status - HTTP status code
 * @returns {Object} - Structured error response
 */
export function createApiError(message = 'An error occurred', errors = {}, status = 400) {
  return {
    response: {
      data: {
        message,
        errors
      },
      status
    }
  };
}

/**
 * Wait for an element to appear in the DOM
 * @param {Object} wrapper - Vue test wrapper
 * @param {String} selector - Element selector
 * @param {Number} timeout - Maximum time to wait in ms
 * @returns {Promise} - Promise resolving when element is found
 */
export async function waitForElement(wrapper, selector, timeout = 1000) {
  const startTime = Date.now();
  
  while (Date.now() - startTime < timeout) {
    if (wrapper.find(selector).exists()) {
      return wrapper.find(selector);
    }
    
    await new Promise(resolve => setTimeout(resolve, 50));
    await wrapper.vm.$nextTick();
  }
  
  throw new Error(`Element ${selector} not found within ${timeout}ms`);
}

/**
 * Check if component emitted an event
 * @param {Object} wrapper - Vue test wrapper
 * @param {String} eventName - Name of the event
 * @param {Array} expectedArgs - Expected event arguments
 * @returns {Boolean} - Whether the event was emitted with expected args
 */
export function wasEventEmitted(wrapper, eventName, expectedArgs = null) {
  const emitted = wrapper.emitted(eventName);
  
  if (!emitted) {
    return false;
  }
  
  if (expectedArgs === null) {
    return true;
  }
  
  // Check if any emission matches the expected args
  return emitted.some(emission => {
    return JSON.stringify(emission) === JSON.stringify([expectedArgs]);
  });
}

/**
 * Mock a component globally
 * @param {Object} app - Vue app instance
 * @param {String} componentName - Component name to mock
 * @param {Object} implementation - Mock implementation
 * @returns {Function} - Function to restore original component
 */
export function mockComponent(app, componentName, implementation = {}) {
  const original = app.component(componentName);
  
  app.component(componentName, {
    template: '<div data-testid="mocked-component"></div>',
    ...implementation
  });
  
  return () => {
    app.component(componentName, original);
  };
} 