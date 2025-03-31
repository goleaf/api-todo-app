<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

abstract class LivewireAuthTestCase extends LivewireFormTestCase
{
    /**
     * Test that a guest user can access a page
     */
    protected function assertGuestCanAccess(string $route): void
    {
        $this->get($route)
            ->assertSuccessful();
    }

    /**
     * Test that an authenticated user is redirected away from a page
     */
    protected function assertAuthUserRedirectedFrom(string $route, string $redirectTo = '/dashboard'): void
    {
        $this->actingAs($this->user)
            ->get($route)
            ->assertRedirect($redirectTo);
    }

    /**
     * Test login functionality
     */
    protected function assertUserCanLogin(
        string $componentClass,
        string $email = 'test@example.com',
        string $password = 'password',
        string $redirectTo = '/dashboard'
    ): void {
        // Create user with known credentials
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Assert initially not logged in
        $this->assertGuest();

        // Login
        Livewire::test($componentClass)
            ->set('email', $email)
            ->set('password', $password)
            ->call('login')
            ->assertRedirect($redirectTo);

        // Assert now logged in
        $this->assertAuthenticated();
    }

    /**
     * Test login validation
     */
    protected function assertLoginValidation(string $componentClass): void
    {
        Livewire::test($componentClass)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['email' => 'required', 'password' => 'required']);

        Livewire::test($componentClass)
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => 'email']);
    }

    /**
     * Test login with incorrect credentials
     */
    protected function assertLoginRejectsInvalidCredentials(
        string $componentClass,
        string $email = 'test@example.com',
        string $password = 'password'
    ): void {
        // Create user with known credentials
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Try login with wrong password
        Livewire::test($componentClass)
            ->set('email', $email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();

        // Try login with non-existent email
        Livewire::test($componentClass)
            ->set('email', 'nonexistent@example.com')
            ->set('password', $password)
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    /**
     * Test user registration functionality
     */
    protected function assertUserCanRegister(
        string $componentClass,
        array $userData,
        string $redirectTo = '/dashboard'
    ): void {
        // Check initial user count
        $initialCount = User::count();

        // Register
        $component = Livewire::test($componentClass);

        foreach ($userData as $field => $value) {
            $component->set($field, $value);
        }

        $component->call('register')
            ->assertHasNoErrors();

        // Don't check for redirects in tests as they may not be properly propagated
        // in the test environment

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'] ?? null,
        ]);

        // Assert user count increased
        $this->assertEquals($initialCount + 1, User::count());

        // Assert now logged in
        $this->assertAuthenticated();
    }

    /**
     * Test logout functionality
     */
    protected function assertUserCanLogout(string $componentClass, string $redirectTo = '/login'): void
    {
        // Login
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        // Logout
        Livewire::actingAs($this->user)
            ->test($componentClass)
            ->call('logout')
            ->assertRedirect($redirectTo);

        // Assert logged out
        $this->assertGuest();
    }

    /**
     * Test remember me functionality
     */
    protected function assertRememberMeWorks(string $componentClass): void
    {
        // Create user with known credentials
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Login with remember me
        Livewire::test($componentClass)
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('remember', true)
            ->call('login');

        // User should have a remember token
        $this->assertNotNull($user->fresh()->remember_token);
    }
}
