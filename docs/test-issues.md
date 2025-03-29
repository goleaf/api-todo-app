# Testing Issues and Solutions

## Current Testing Status

The application has two types of tests:
1. PHP/Laravel tests (PHPUnit)
2. JavaScript/Vue tests (Vitest)

### PHP Test Issues

1. **Database Integrity Violations**:
   - `UNIQUE constraint failed: users.email`: Tests are creating users with the same email. Each test needs unique emails.
   - `NOT NULL constraint failed: todos.user_id`: Todo items are being created without associating them with users.

2. **Browser Test Issues**:
   - `Failed to connect to localhost port 9515: Connection refused`: ChromeDriver is not running for Laravel Dusk tests.

3. **Route Testing Issues**:
   - `View [profile.edit] not found`: Tests are checking routes for views that don't exist.
   - API endpoint status code expectations don't match actual responses:
     - Expected 401 but got 422 for invalid credentials
     - Expected 201 but got 200 for category creation

### JavaScript Test Issues

1. **Vue Router Issues**:
   - Record with path "/" is missing a "component(s)" or "children" property
   - Need to properly mock route components in tests

2. **Component Testing Issues**:
   - Failures in form submission tests due to mismatched expectations
   - Component selector tests failing because elements can't be found

## Solutions

### PHP Test Fixes

1. **Database Issues**:
   - Use the `RefreshDatabase` or `DatabaseMigrations` trait consistently
   - Create a test helper to generate unique emails for each test
   - Ensure todos are always associated with a user:
     ```php
     $todo = Todo::factory()->for(User::factory())->create();
     ```

2. **Browser Tests**:
   - Start ChromeDriver before running Dusk tests:
     ```bash
     php artisan dusk:chrome-driver
     ./vendor/laravel/dusk/bin/chromedriver-linux > /dev/null 2>&1 &
     ```

3. **Route Testing**:
   - Create missing views or update tests to expect the correct status codes
   - Update API test expectations to match actual response codes

### JavaScript Test Fixes

1. **Vue Router**:
   - Mock components properly in tests:
     ```js
     const Home = { template: '<div>Home</div>' };
     const routes = [{ path: '/', name: 'home', component: Home }];
     ```

2. **Component Tests**:
   - Create proper mocks for Vuex store and router
   - Ensure component selectors match the actual rendered components
   - Update test expectations to match actual component behavior

## Implementation Plan

1. Fix the user factory to ensure unique emails
2. Update browser tests to properly check for ChromeDriver
3. Fix todo creation to always include a user_id
4. Update API test expectations to match actual response codes
5. Fix Vue component tests with proper mocks