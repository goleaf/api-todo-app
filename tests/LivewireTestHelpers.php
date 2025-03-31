<?php

namespace Tests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

/**
 * Test helpers for Livewire component testing
 *
 * This class provides utility methods to simplify testing Livewire components,
 * especially for components that replaced Vue.js components during migration.
 */
class LivewireTestHelpers
{
    /**
     * Generate a unique email for testing
     */
    public static function uniqueEmail(string $prefix = 'test'): string
    {
        return $prefix.'_'.time().'_'.Str::random(8).'@example.com';
    }

    /**
     * Create a user with a guaranteed unique email
     */
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => self::uniqueEmail(),
            'password' => bcrypt('password'),
        ], $attributes));
    }

    /**
     * Create a task with an associated user
     */
    public static function createTaskWithUser(array $taskAttributes = [], ?User $user = null): Task
    {
        $user = $user ?? self::createUserWithUniqueEmail();

        return Task::factory()->create(array_merge([
            'title' => 'Test Task',
            'description' => 'This is a test task item',
            'completed' => false,
            'due_date' => now()->addDays(3),
            'user_id' => $user->id,
        ], $taskAttributes));
    }

    /**
     * Create multiple tasks for a user
     */
    public static function createTasksForUser(int $count = 3, ?User $user = null): array
    {
        $user = $user ?? self::createUserWithUniqueEmail();

        $tasks = [];
        for ($i = 0; $i < $count; $i++) {
            $tasks[] = self::createTaskWithUser([
                'title' => "Test Task {$i}",
                'completed' => $i % 2 === 0,
                'due_date' => now()->addDays($i + 1),
            ], $user);
        }

        return [$user, collect($tasks)];
    }

    /**
     * Set up a complete test environment with a user and tasks
     */
    public static function createTestEnvironment(int $taskCount = 3, array $userAttributes = []): array
    {
        $user = self::createUserWithUniqueEmail($userAttributes);
        [$_, $tasks] = self::createTasksForUser($taskCount, $user);

        return [$user, $tasks];
    }

    /**
     * For backward compatibility - Create a todo with an associated user (alias for createTaskWithUser)
     *
     * @deprecated Use createTaskWithUser instead
     */
    public static function createTodoWithUser(array $todoAttributes = [], ?User $user = null): Task
    {
        return self::createTaskWithUser($todoAttributes, $user);
    }

    /**
     * For backward compatibility - Create multiple todos for a user (alias for createTasksForUser)
     *
     * @deprecated Use createTasksForUser instead
     */
    public static function createTodosForUser(int $count = 3, ?User $user = null): array
    {
        return self::createTasksForUser($count, $user);
    }

    /**
     * Test a Livewire component with authentication
     */
    public static function testComponentAsUser($component, $user = null, $params = [])
    {
        $user = $user ?? self::createUserWithUniqueEmail();

        return Livewire::actingAs($user)->test($component, $params);
    }

    /**
     * Test form submission with validation
     */
    public static function testFormSubmission($component, $method, $formData, $validationRules = [])
    {
        $test = Livewire::test($component);

        // Set form data
        foreach ($formData as $field => $value) {
            $test->set($field, $value);
        }

        // Call the submit method
        $test = $test->call($method);

        // Check for validation errors if rules are provided
        if (! empty($validationRules)) {
            foreach ($validationRules as $field => $rule) {
                if (isset($formData[$field]) && ! $rule($formData[$field])) {
                    $test->assertHasErrors([$field]);
                } else {
                    $test->assertHasNoErrors([$field]);
                }
            }
        }

        return $test;
    }

    /**
     * Test component state after specific actions
     */
    public static function testComponentState($component, $initialState, $action, $expectedState)
    {
        $test = Livewire::test($component);

        // Set initial state
        foreach ($initialState as $property => $value) {
            $test->set($property, $value);
        }

        // Perform action (method call or event)
        if (is_array($action)) {
            $method = $action[0];
            $params = $action[1] ?? [];
            $test->call($method, ...$params);
        } else {
            $test->call($action);
        }

        // Assert expected state
        foreach ($expectedState as $property => $value) {
            $test->assertSet($property, $value);
        }

        return $test;
    }

    /**
     * Test component event emissions
     */
    public static function testComponentEvents($component, $action, $expectedEvents)
    {
        $test = Livewire::test($component);

        // Perform action
        if (is_array($action)) {
            $method = $action[0];
            $params = $action[1] ?? [];
            $test->call($method, ...$params);
        } else {
            $test->call($action);
        }

        // Assert expected events
        foreach ($expectedEvents as $event => $params) {
            if (is_numeric($event)) {
                $test->assertEmitted($params);
            } else {
                $test->assertEmitted($event, ...(array) $params);
            }
        }

        return $test;
    }

    /**
     * Test a component's response to events
     */
    public static function testComponentResponseToEvent($component, $event, $params, $expectedState)
    {
        $test = Livewire::test($component);

        // Emit event to the component
        if (is_array($params)) {
            $test->emit($event, ...$params);
        } else {
            $test->emit($event, $params);
        }

        // Assert expected state after event
        foreach ($expectedState as $property => $value) {
            $test->assertSet($property, $value);
        }

        return $test;
    }

    /**
     * Create API authentication headers for a user
     */
    public static function getApiAuthHeaders(User $user, string $tokenName = 'test-token'): array
    {
        $token = $user->createToken($tokenName)->plainTextToken;

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }
}
