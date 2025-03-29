# Test Helpers Guide

This guide explains how to use the test helpers provided in the Todo application. These utilities are designed to simplify test setup, reduce code duplication, and prevent common test errors.

## JavaScript Test Utilities

Located at: `resources/js/tests/utils/testUtils.js`

### Component Testing Utilities

#### `setupComponentTest(Component, options)`

Sets up a component for testing with mocked dependencies.

```js
const { wrapper, store, router, routerPush, runTimers, cleanupTimers } = 
  setupComponentTest(MyComponent, {
    // Use fake timers for async tests
    useFakeTimers: true,
    
    // Configure store options
    storeOptions: {
      // Mock the response from store.dispatch
      dispatchResponse: { id: 1, name: 'User' },
      
      // Set initial state
      user: { id: 1 },
      loading: false,
      error: null,
      
      // Set getters
      isAuthenticated: true,
      
      // Additional state properties
      state: {
        customProp: 'value'
      },
      
      // Additional getters
      getters: {
        customGetter: 'value'
      }
    },
    
    // Custom routes (in addition to default home route)
    routes: [
      { path: '/profile', name: 'profile', component: { template: '<div>Profile</div>' } }
    ],
    
    // Use plugins when mounting (store and router)
    usePlugins: true,
    
    // Additional stubs
    stubs: {
      'custom-component': true
    },
    
    // Additional mounting options for vue-test-utils
    mountOptions: {
      attachTo: document.body
    },
    
    // Component props
    props: {
      initialValue: 'test'
    }
  });
```

#### `createTestRouter(routes)`

Creates a Vue Router instance with mock components:

```js
const router = createTestRouter([
  { path: '/profile', name: 'profile', component: { template: '<div>Profile</div>' } },
  { path: '/settings', name: 'settings', component: { template: '<div>Settings</div>' } }
]);
```

#### `createMockStore(options)`

Creates a mock Vuex store:

```js
const store = createMockStore({
  dispatchResponse: { success: true },
  isAuthenticated: true,
  user: { id: 1, name: 'User' },
  loading: false,
  error: null,
  darkMode: true,
  state: {
    customState: 'value'
  },
  getters: {
    customGetter: 'value'
  }
});
```

### Form Testing Utilities

#### `fillForm(wrapper, fieldMap)`

Fills form inputs with values:

```js
await fillForm(wrapper, {
  'input[type="email"]': 'test@example.com',
  'input[type="password"]': 'password123',
  'input[name="name"]': 'Test User',
  'select[name="role"]': 'admin'
});
```

#### `submitForm(wrapper, formSelector, runTimers)`

Submits a form and optionally runs timers:

```js
// Submit default form and run timers
await submitForm(wrapper);

// Submit a specific form without running timers
await submitForm(wrapper, '#login-form', false);
```

### Error Testing Utilities

#### `hasErrorMessage(wrapper, errorMessage, errorSelector)`

Checks if an error message is displayed:

```js
// Check for error message
expect(hasErrorMessage(wrapper, 'Invalid credentials')).toBe(true);

// Check for error in a specific element
expect(hasErrorMessage(wrapper, 'Required field', '.validation-error')).toBe(true);
```

#### `createApiError(message, errors, status)`

Creates a standardized API error response:

```js
// Create a simple error
const error = createApiError('Invalid credentials');

// Create a validation error
const validationError = createApiError('Validation failed', {
  email: ['Email is required', 'Email must be valid'],
  password: ['Password is too short']
}, 422);

// Use in tests
store.dispatch.mockRejectedValueOnce(error);
```

## PHP Test Helpers

Located at: `tests/TestHelpers.php`

### User Testing Utilities

#### `uniqueEmail(prefix)`

Generates a guaranteed unique email:

```php
// Generate a unique email
$email = TestHelpers::uniqueEmail();

// Generate a unique email with a custom prefix
$adminEmail = TestHelpers::uniqueEmail('admin');
```

#### `createUserWithUniqueEmail(attributes)`

Creates a user with a guaranteed unique email:

```php
// Create a user with default attributes
$user = TestHelpers::createUserWithUniqueEmail();

// Create a user with custom attributes
$admin = TestHelpers::createUserWithUniqueEmail([
    'name' => 'Admin User',
    'is_admin' => true
]);
```

### Todo Testing Utilities

#### `createTodoWithUser(todoAttributes, user)`

Creates a todo associated with a user:

```php
// Create a todo for an existing user
$todo = TestHelpers::createTodoWithUser(['title' => 'Test Todo'], $user);

// Create a todo with a new user automatically
$todo = TestHelpers::createTodoWithUser([
    'title' => 'Test Todo',
    'description' => 'Description',
    'completed' => true
]);
```

#### `createTodosForUser(count, user)`

Creates multiple todos for a user:

```php
// Create 3 todos for an existing user
$todos = TestHelpers::createTodosForUser(3, $user);

// Create 5 todos for a new user
$todos = TestHelpers::createTodosForUser(5);
```

#### `createTestEnvironment(todoCount, userAttributes)`

Sets up a complete test environment:

```php
// Basic setup - creates a user and 3 todos
[$user, $todos] = TestHelpers::createTestEnvironment();

// Create 5 todos for an admin user
[$admin, $adminTodos] = TestHelpers::createTestEnvironment(5, [
    'name' => 'Admin User',
    'is_admin' => true
]);
```

## Example Usage

### JavaScript Component Test Example

```js
import { describe, it, expect, afterEach, vi } from 'vitest';
import { setupComponentTest, fillForm, submitForm, createApiError } from '../tests/utils/testUtils';
import LoginComponent from './Login.vue';

describe('Login.vue', () => {
  afterEach(() => {
    vi.restoreAllMocks();
    vi.useRealTimers();
  });
  
  it('logs in successfully and redirects', async () => {
    // Setup with one line
    const { wrapper, store, routerPush, runTimers } = setupComponentTest(LoginComponent, {
      useFakeTimers: true
    });
    
    // Fill form with one line
    await fillForm(wrapper, {
      'input[type="email"]': 'test@example.com',
      'input[type="password"]': 'password123'
    });
    
    // Submit form
    await submitForm(wrapper);
    
    // Verify store dispatch
    expect(store.dispatch).toHaveBeenCalledWith('login', expect.objectContaining({
      email: 'test@example.com',
      password: 'password123'
    }));
    
    // Verify redirect
    expect(routerPush).toHaveBeenCalledWith('/');
  });
});
```

### PHP API Test Example

```php
<?php

use Tests\TestCase;
use Tests\TestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_can_see_only_their_todos()
    {
        // Create test data with one line
        [$user, $todos] = TestHelpers::createTestEnvironment(3);
        [$otherUser, $otherTodos] = TestHelpers::createTestEnvironment(2);
        
        // Test API response
        $response = $this->actingAs($user)->getJson('/api/todos');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
            
        // Verify correct todos are returned
        foreach ($todos as $todo) {
            $response->assertJsonFragment(['id' => $todo->id]);
        }
        
        // Verify other todos are not returned
        foreach ($otherTodos as $todo) {
            $response->assertJsonMissing(['id' => $todo->id]);
        }
    }
}
``` 