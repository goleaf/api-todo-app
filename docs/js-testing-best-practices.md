# JavaScript Testing Best Practices

This document outlines best practices for JavaScript/Vue testing in the Todo application based on our experience fixing and improving the test suite.

## Vue Component Testing

### Mounting Components

Always properly set up the test environment when mounting components:

```js
// Mount a component with proper stubs
const wrapper = mount(ComponentName, {
  global: {
    plugins: [store, router],
    stubs: {
      'router-link': true
    }
  }
});
```

### Mocking Dependencies

Mock Vuex and Vue Router correctly to avoid test errors:

```js
// Mock store
const mockStore = {
  dispatch: vi.fn().mockResolvedValue({}),
  state: {
    loading: false,
    error: null
  }
};

// Mock router
const mockRouter = {
  push: vi.fn()
};

// Mock dependencies at the module level
vi.mock('vuex', () => ({
  useStore: () => mockStore
}));

vi.mock('vue-router', () => ({
  useRouter: () => mockRouter,
  useRoute: () => ({ query: {} })
}));
```

### Router Testing

When using Vue Router in tests, always provide components for routes:

```js
// Create components for routes
const Home = { template: '<div>Home Component</div>' };
const Calendar = { template: '<div>Calendar Component</div>' };

// Create router with proper components
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/calendar', name: 'calendar', component: Calendar }
  ]
});
```

### Async Tests

When testing asynchronous operations, use fake timers:

```js
// In beforeEach or test setup
vi.useFakeTimers();

// When testing async operations
await wrapper.find('form').trigger('submit');
await vi.runAllTimers();

// Clean up in afterEach or at end of test
vi.useRealTimers();
```

### Form Tests

When testing forms, verify the correct data is being stored:

```js
// Set input values
await emailInput.setValue('test@example.com');
await passwordInput.setValue('password123');

// Verify component data
expect(wrapper.vm.form.email).toBe('test@example.com');
expect(wrapper.vm.form.password).toBe('password123');
```

### Action Dispatch Tests

Use more flexible assertions when testing Vuex action dispatches:

```js
// Instead of exact argument matching:
expect(store.dispatch).toHaveBeenCalledWith('login', exactObject);

// Use a more flexible approach:
expect(store.dispatch).toHaveBeenCalled();
const callArgs = store.dispatch.mock.calls[0];
expect(callArgs[1]).toEqual(expect.objectContaining({
  email: 'test@example.com',
  password: 'password123'
}));
```

### Router Link Testing

When testing router-link components that are stubbed, don't expect the link text to be available:

```js
// Check for the router-link stub with correct attributes
const registerLink = wrapper.find('router-link-stub');
expect(registerLink.exists()).toBe(true);
expect(registerLink.attributes('to')).toBe('/register');
```

### Mocking Axios

For API tests, properly mock axios responses:

```js
// Mock axios
vi.mock('axios');

// In test
axios.post.mockResolvedValueOnce({
  data: {
    token: 'test-token',
    user: { id: 1, name: 'Test User', email: 'test@example.com' }
  }
});
```

## Known Issues and Solutions

1. **Vue Router Warning**: "Record with path '/' is missing a component(s) or children property"
   - Solution: Always provide a component for each route in the test router

2. **Empty Component Stubs**: When using `stubs`, the component content will be empty
   - Solution: Test for the existence of elements and their attributes instead of content

3. **Timers in Async Tests**: Unresolved promises causing test timeouts
   - Solution: Use `vi.useFakeTimers()` and `vi.runAllTimers()` to control async execution

4. **Component Data Access**: Unable to access deeply nested reactive data
   - Solution: Access data through `wrapper.vm` for form values and component state 