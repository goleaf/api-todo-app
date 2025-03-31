# Livewire Testing Guide

This guide provides comprehensive documentation on testing Livewire components in our Todo application, which has been migrated from Vue.js to Livewire.

## Table of Contents

- [Introduction](#introduction)
- [Testing Approach](#testing-approach)
- [Test Helpers](#test-helpers)
- [Component Testing](#component-testing)
- [Form Testing](#form-testing)
- [Event Testing](#event-testing)
- [Authentication Testing](#authentication-testing)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

## Introduction

After migrating from Vue.js to Livewire, our testing approach has significantly changed. Instead of JavaScript-based testing using Vitest and Vue Test Utils, we now use PHPUnit with Livewire's built-in testing tools.

This shift brings several advantages:
- Testing is closer to the actual server-side implementation
- Fewer dependencies to maintain
- More straightforward database and authentication testing
- Better integration with Laravel's testing ecosystem

## Testing Approach

Our Livewire testing strategy follows these principles:

1. **Component-Focused**: Each Livewire component should have its own test file.
2. **Database-Aware**: Use `RefreshDatabase` to test database interactions.
3. **User-Centric**: Test components from the user's perspective.
4. **Coverage-Oriented**: Aim for high test coverage of all component features.
5. **Helper-Assisted**: Use custom test helpers to reduce boilerplate code.

## Test Helpers

We've created custom test helpers in the `Tests\LivewireTestHelpers` class to simplify testing and provide consistent testing patterns.

### Basic Helpers

```php
// Create a user with a guaranteed unique email
$user = LivewireTestHelpers::createUserWithUniqueEmail([
    'name' => 'Test User',
]);

// Create a todo with an associated user
$todo = LivewireTestHelpers::createTodoWithUser([
    'title' => 'Test Todo',
]);

// Create multiple todos for a user
[$user, $todos] = LivewireTestHelpers::createTodosForUser(3);

// Set up a complete test environment
[$user, $todos] = LivewireTestHelpers::createTestEnvironment();
```

### Component Testing Helpers

```php
// Test a Livewire component as an authenticated user
$test = LivewireTestHelpers::testComponentAsUser(
    TaskList::class, 
    $user
);

// Test component state changes
$test = LivewireTestHelpers::testComponentState(
    TaskList::class,
    ['filter' => 'all'], // Initial state
    ['filterTasks', ['status' => 'completed']], // Action
    ['filter' => 'completed'] // Expected state
);

// Test component event emissions
$test = LivewireTestHelpers::testComponentEvents(
    TaskList::class,
    ['toggleTaskCompleted', [$todo->id]], // Action
    ['task-updated' => $todo->id] // Expected events
);

// Test component's response to events
$test = LivewireTestHelpers::testComponentResponseToEvent(
    TaskList::class,
    'task-deleted', // Event to emit
    $todo->id, // Event parameter
    ['refreshRequired' => true] // Expected state
);
```

### Form Testing Helpers

```php
// Test form submission with validation
$test = LivewireTestHelpers::testFormSubmission(
    Login::class,
    'login', // Method to call
    [
        'email' => 'test@example.com',
        'password' => 'password123',
    ], // Form data
    [
        // Optional validation rules as closures
        'email' => fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL),
    ]
);
```

### Test Case Classes

We've created specialized test case classes to provide a more structured approach to testing Livewire components:

#### LivewireTestCase

This is the base test case for all Livewire component tests:

```php
use Tests\Feature\Livewire\LivewireTestCase;

class YourComponentTest extends LivewireTestCase
{
    /** @test */
    public function component_can_render()
    {
        $this->assertLivewireCanSee(YourComponent::class, 'Expected Text');
    }
}
```

Key methods:
- `assertLivewireCanSee(string $componentClass, string $text, array $params = [])`
- `assertLivewireCannotSee(string $componentClass, string $text, array $params = [])`
- `assertLivewirePropertyUpdates(string $componentClass, string $propertyName, mixed $value, array $params = [])`
- `assertLivewireMethodWorks(string $componentClass, string $methodName, array $expectedChanges = [], array $params = [], array $methodParams = [])`

#### LivewireFormTestCase

This test case extends `LivewireTestCase` and adds methods specifically for testing forms:

```php
use Tests\Feature\Livewire\LivewireFormTestCase;

class YourFormTest extends LivewireFormTestCase
{
    /** @test */
    public function form_validates_input()
    {
        $this->assertFormFieldIsRequired(YourForm::class, 'name');
        $this->assertEmailValidation(YourForm::class, 'email');
    }
    
    /** @test */
    public function form_submits_successfully()
    {
        $this->assertFormSubmitsSuccessfully(
            YourForm::class,
            [
                'name' => 'Test User',
                'email' => 'test@example.com'
            ],
            [
                'redirect' => '/success',
                'event' => 'form-submitted',
                'database' => [
                    'users' => ['email' => 'test@example.com']
                ]
            ]
        );
    }
}
```

Key methods:
- `assertFormValidationRule(string $componentClass, string $fieldName, mixed $invalidValue, string $validationRule, array $params = [])`
- `assertFormFieldIsRequired(string $componentClass, string $fieldName, array $params = [])`
- `assertEmailValidation(string $componentClass, string $fieldName, array $params = [])`
- `assertPasswordMinLength(string $componentClass, string $fieldName, int $minLength = 8, array $params = [])`
- `assertPasswordConfirmation(string $componentClass, string $passwordField = 'password', string $confirmationField = 'password_confirmation', array $params = [])`
- `assertFormSubmitsSuccessfully(string $componentClass, array $formData, array $expectedOutcome, string $submitMethod = 'submit', array $params = [])`
- `assertFormResetsAfterSubmission(string $componentClass, array $formData, array $fieldsToCheck, string $submitMethod = 'submit', array $params = [])`

#### LivewireAuthTestCase

This test case extends `LivewireFormTestCase` and adds methods specifically for testing authentication components:

```php
use Tests\Feature\Livewire\LivewireAuthTestCase;

class LoginTest extends LivewireAuthTestCase
{
    /** @test */
    public function login_page_contains_livewire_component()
    {
        $this->assertGuestCanAccess('/login');
        $this->get('/login')->assertSeeLivewire('auth.login');
    }
    
    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $this->assertUserCanLogin(Login::class);
    }
    
    /** @test */
    public function remember_me_functionality_works()
    {
        $this->assertRememberMeWorks(Login::class);
    }
}
```

Key methods:
- `assertGuestCanAccess(string $route)`
- `assertAuthUserRedirectedFrom(string $route, string $redirectTo = '/dashboard')`
- `assertUserCanLogin(string $componentClass, string $email = 'test@example.com', string $password = 'password', string $redirectTo = '/dashboard')`
- `assertLoginValidation(string $componentClass)`
- `assertLoginRejectsInvalidCredentials(string $componentClass, string $email = 'test@example.com', string $password = 'password')`
- `assertUserCanRegister(string $componentClass, array $userData, string $redirectTo = '/dashboard')`
- `assertUserCanLogout(string $componentClass, string $redirectTo = '/login')`
- `assertRememberMeWorks(string $componentClass)`

Using these test case classes provides several benefits:
1. **Reduces boilerplate code** in your tests
2. **Standardizes testing methods** across your application
3. **Provides readable, descriptive test methods** that clearly indicate what's being tested
4. **Makes tests more maintainable** by centralizing common testing logic
5. **Simplifies complex testing scenarios** with ready-to-use helper methods

## Component Testing

### Basic Component Testing

Test if a component renders correctly:

```php
/** @test */
public function it_can_render_component()
{
    Livewire::test(TaskList::class)
        ->assertViewIs('livewire.tasks.task-list')
        ->assertSee('Task List');
}
```

### Testing Component Properties

```php
/** @test */
public function it_initializes_with_correct_properties()
{
    Livewire::test(TaskList::class)
        ->assertSet('filter', 'all')
        ->assertSet('searchQuery', '')
        ->assertSet('isLoading', false);
}
```

### Testing Component Methods

```php
/** @test */
public function it_can_filter_tasks()
{
    Livewire::test(TaskList::class)
        ->call('filterTasks', 'completed')
        ->assertSet('filter', 'completed');
}
```

### Testing Computed Properties

```php
/** @test */
public function it_computes_filtered_tasks_correctly()
{
    [$user, $todos] = LivewireTestHelpers::createTestEnvironment(5);
    $completedCount = $todos->where('completed', true)->count();

    Livewire::actingAs($user)
        ->test(TaskList::class)
        ->call('filterTasks', 'completed')
        ->assertCount('filteredTasks', $completedCount);
}
```

## Form Testing

### Testing Form Inputs

```php
/** @test */
public function it_updates_form_inputs()
{
    Livewire::test(TaskCreate::class)
        ->set('form.title', 'New Task')
        ->set('form.description', 'Task Description')
        ->assertSet('form.title', 'New Task')
        ->assertSet('form.description', 'Task Description');
}
```

### Testing Form Validation

```php
/** @test */
public function it_validates_form_inputs()
{
    Livewire::test(TaskCreate::class)
        ->set('form.title', '')
        ->call('save')
        ->assertHasErrors(['form.title' => 'required']);
}
```

### Testing Form Submission

```php
/** @test */
public function it_can_create_task()
{
    $user = LivewireTestHelpers::createUserWithUniqueEmail();

    Livewire::actingAs($user)
        ->test(TaskCreate::class)
        ->set('form.title', 'New Task')
        ->set('form.description', 'Task Description')
        ->call('save')
        ->assertEmitted('task-created')
        ->assertSet('form.title', '');

    $this->assertDatabaseHas('todos', [
        'title' => 'New Task',
        'description' => 'Task Description',
        'user_id' => $user->id,
    ]);
}
```

## Event Testing

### Testing Event Emissions

```php
/** @test */
public function it_emits_events_when_task_is_updated()
{
    $todo = LivewireTestHelpers::createTodoWithUser();
    $user = User::find($todo->user_id);

    Livewire::actingAs($user)
        ->test(TaskShow::class, ['taskId' => $todo->id])
        ->call('updateTask')
        ->assertEmitted('task-updated', $todo->id);
}
```

### Testing Event Listeners

```php
/** @test */
public function it_responds_to_events()
{
    Livewire::test(TaskList::class)
        ->emit('task-created')
        ->assertSet('refreshRequired', true);
}
```

### Testing Parent-Child Component Interaction

```php
/** @test */
public function parent_component_can_communicate_with_child()
{
    Livewire::test('tasks.task-list')
        ->assertSeeLivewire('tasks.task-item')
        ->emit('task-selected', 1)
        ->assertEmitted('task-details-requested', 1);
}
```

## Authentication Testing

### Testing Login

```php
/** @test */
public function it_can_log_in_user()
{
    $user = LivewireTestHelpers::createUserWithUniqueEmail([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect('/dashboard');

    $this->assertTrue(Auth::check());
}
```

### Testing Protected Components

```php
/** @test */
public function it_redirects_unauthenticated_users()
{
    $this->get('/tasks')
        ->assertRedirect('/login');
}

/** @test */
public function it_allows_authenticated_users()
{
    $user = LivewireTestHelpers::createUserWithUniqueEmail();

    $this->actingAs($user)
        ->get('/tasks')
        ->assertSuccessful()
        ->assertSeeLivewire('tasks.task-list');
}
```

## Best Practices

1. **Isolation**: Test one feature per test method.
2. **Naming**: Use descriptive test method names that explain what's being tested.
3. **Arrangement**: Follow the Arrange-Act-Assert pattern.
4. **Readability**: Keep test code clean and easy to understand.
5. **Data Creation**: Use test helpers to create test data consistently.
6. **Minimal Assertions**: Keep assertions focused on what's being tested.
7. **Database Cleanup**: Always use `RefreshDatabase` to reset between tests.
8. **Component Testing**: Test components in isolation when possible.

## Troubleshooting

### Common Issues

1. **Authentication Issues**:
   - If tests fail due to authentication, ensure you're using `Livewire::actingAs($user)`.

2. **Database Errors**:
   - If you encounter database errors, check that `RefreshDatabase` is used.
   - Ensure unique emails are used for test users.

3. **Component Not Found**:
   - If component isn't found, check namespace and registration in `Livewire\Livewire::component()`.

4. **Event Assertions Failing**:
   - Ensure events are properly emitted with `$this->emit()` in components.
   - Check for correct event names and parameters.

5. **Validation Errors**:
   - Verify that validation rules in the component match what's being tested.

### Debugging Tips

1. Use `dump()` or `dd()` in tests to inspect values.
2. Check component state with `->dump()` method:
   ```php
   Livewire::test(TaskList::class)->dump();
   ```
3. For complex issues, use Laravel's error handling to catch exceptions:
   ```php
   try {
       // Test code
   } catch (\Exception $e) {
       dd($e->getMessage());
   }
   ```

## Example: Complete Component Test

```php
<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tasks\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\LivewireTestHelpers;
use Tests\TestCase;

class TaskListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_tasks_for_authenticated_user()
    {
        // Arrange: Create user and tasks
        [$user, $todos] = LivewireTestHelpers::createTestEnvironment(3);

        // Act & Assert: Test component rendering
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->assertViewIs('livewire.tasks.task-list')
            ->assertSee($todos->first()->title)
            ->assertCount('tasks', 3);
    }

    /** @test */
    public function it_can_create_new_task()
    {
        // Arrange: Create user
        $user = LivewireTestHelpers::createUserWithUniqueEmail();

        // Act: Create task through component
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->set('newTask.title', 'New Test Task')
            ->set('newTask.description', 'Test Description')
            ->call('createTask');

        // Assert: Task was created in database
        $this->assertDatabaseHas('todos', [
            'title' => 'New Test Task',
            'description' => 'Test Description',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_filter_tasks()
    {
        // Arrange: Create user with mixed completed/incomplete tasks
        $user = LivewireTestHelpers::createUserWithUniqueEmail();
        $completedTodo = LivewireTestHelpers::createTodoWithUser([
            'completed' => true
        ], $user);
        $incompleteTodo = LivewireTestHelpers::createTodoWithUser([
            'completed' => false
        ], $user);

        // Act & Assert: Filter to completed tasks only
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('filterTasks', 'completed')
            ->assertSee($completedTodo->title)
            ->assertDontSee($incompleteTodo->title);
    }

    /** @test */
    public function it_can_mark_task_as_completed()
    {
        // Arrange: Create incomplete task
        $todo = LivewireTestHelpers::createTodoWithUser(['completed' => false]);
        $user = User::find($todo->user_id);

        // Act: Toggle task completion
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('toggleTaskCompleted', $todo->id);

        // Assert: Task is now completed
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => true,
        ]);
    }
} 