<?php

namespace Tests;

use App\Models\User;
use App\Models\Todo;
use Illuminate\Support\Str;

/**
 * Test helper methods for Laravel tests
 */
class TestHelpers
{
    /**
     * Generate a unique email to avoid database conflicts in tests
     * 
     * @param string $prefix Optional prefix for the email
     * @return string The unique email
     */
    public static function uniqueEmail(string $prefix = 'test'): string
    {
        // Generate a unique string based on time and random value
        $uniqueId = time() . '_' . Str::random(8);
        
        return "{$prefix}_{$uniqueId}@example.com";
    }

    /**
     * Create a user with guaranteed unique email to avoid database conflicts
     * 
     * @param array $attributes Additional attributes to set on the user
     * @return User The created user
     */
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => self::uniqueEmail(),
            'password' => bcrypt('password'),
        ], $attributes));
    }

    /**
     * Create a todo with an associated user
     * 
     * @param array $todoAttributes Todo attributes
     * @param User|null $user User to associate with the todo, or null to create a new user
     * @return Todo The created todo
     */
    public static function createTodoWithUser(array $todoAttributes = [], User $user = null): Todo
    {
        // Create user if not provided
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        // Merge with default attributes
        $attributes = array_merge([
            'title' => 'Test Todo',
            'description' => 'This is a test todo item created for testing purposes',
            'completed' => false,
            'due_date' => now()->addDays(3),
        ], $todoAttributes);

        // Create and return todo
        return Todo::factory()->create(array_merge($attributes, [
            'user_id' => $user->id,
        ]));
    }

    /**
     * Create multiple todos for a user
     * 
     * @param int $count Number of todos to create
     * @param User|null $user User to associate todos with, or null to create a new user
     * @return array Array containing the user and collection of created todos
     */
    public static function createTodosForUser(int $count = 3, User $user = null): array
    {
        // Create user if not provided
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        // Create todos
        $todos = [];
        for ($i = 0; $i < $count; $i++) {
            $todos[] = self::createTodoWithUser([
                'title' => "Test Todo {$i}",
                'completed' => $i % 2 === 0, // Alternate completed status
                'due_date' => now()->addDays($i + 1),
            ], $user);
        }

        return [$user, collect($todos)];
    }

    /**
     * Set up a complete test environment with a user and todos
     * 
     * @param int $todoCount Number of todos to create
     * @param array $userAttributes Additional attributes for the user
     * @return array Array containing the user and collection of created todos
     */
    public static function createTestEnvironment(int $todoCount = 3, array $userAttributes = []): array
    {
        // Create user with attributes
        $user = self::createUserWithUniqueEmail($userAttributes);
        
        // Create todos for the user
        [, $todos] = self::createTodosForUser($todoCount, $user);
        
        return [$user, $todos];
    }

    /**
     * Set up API authentication headers for a user
     * 
     * @param User $user The user to create token for
     * @param string $tokenName Name for the token
     * @return array Headers array with Bearer token
     */
    public static function getApiAuthHeaders(User $user, string $tokenName = 'test-token'): array
    {
        $token = $user->createToken($tokenName)->plainTextToken;
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create a sample API response for mocking
     * 
     * @param array $data Response data
     * @param int $status HTTP status code
     * @param array $headers Additional headers
     * @return array Structured API response
     */
    public static function createApiResponse(array $data, int $status = 200, array $headers = []): array
    {
        return [
            'data' => $data,
            'status' => $status,
            'headers' => array_merge([
                'Content-Type' => 'application/json',
            ], $headers),
        ];
    }

    /**
     * Create a validation error response for testing API validation
     * 
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return array Structured validation error response
     */
    public static function createValidationErrorResponse(array $errors, string $message = 'The given data was invalid.'): array
    {
        return [
            'message' => $message,
            'errors' => $errors,
        ];
    }
} 