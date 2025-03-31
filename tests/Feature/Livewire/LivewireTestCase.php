<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

abstract class LivewireTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * The currently authenticated user
     */
    protected ?User $user = null;

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a default user if needed
        $this->user = $this->createUser();
    }

    /**
     * Create and authenticate a user
     */
    protected function createAndAuthenticateUser(array $attributes = []): User
    {
        $this->user = $this->createUser($attributes);
        $this->actingAs($this->user);

        return $this->user;
    }

    /**
     * Create a user with optional attributes
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Test a Livewire component as an authenticated user
     */
    protected function assertLivewireCanSee(string $componentClass, string $text, array $params = []): void
    {
        Livewire::actingAs($this->user)
            ->test($componentClass, $params)
            ->assertSee($text);
    }

    /**
     * Test a Livewire component doesn't show content
     */
    protected function assertLivewireCannotSee(string $componentClass, string $text, array $params = []): void
    {
        Livewire::actingAs($this->user)
            ->test($componentClass, $params)
            ->assertDontSee($text);
    }

    /**
     * Test a component property gets updated
     */
    protected function assertLivewirePropertyUpdates(
        string $componentClass,
        string $propertyName,
        mixed $value,
        array $params = []
    ): void {
        Livewire::actingAs($this->user)
            ->test($componentClass, $params)
            ->set($propertyName, $value)
            ->assertSet($propertyName, $value);
    }

    /**
     * Test a component method works and returns expected result
     */
    protected function assertLivewireMethodWorks(
        string $componentClass,
        string $methodName,
        array $expectedChanges = [],
        array $params = [],
        array $methodParams = []
    ): void {
        $component = Livewire::actingAs($this->user)
            ->test($componentClass, $params)
            ->call($methodName, ...$methodParams);

        foreach ($expectedChanges as $property => $value) {
            $component->assertSet($property, $value);
        }
    }

    /**
     * Test a form submission works
     */
    protected function assertLivewireFormSubmits(
        string $componentClass,
        string $submitMethod,
        array $formData,
        array $expectedOutcome = [],
        bool $expectErrors = false
    ): void {
        $component = Livewire::actingAs($this->user)
            ->test($componentClass);

        // Set form values
        foreach ($formData as $field => $value) {
            $component->set($field, $value);
        }

        // Call the submit method
        $component = $component->call($submitMethod);

        // Assert errors or no errors
        if ($expectErrors) {
            $component->assertHasErrors(array_keys($formData));
        } else {
            $component->assertHasNoErrors();
        }

        // Check for redirects, events, or property changes
        foreach ($expectedOutcome as $type => $value) {
            if ($type === 'redirect') {
                $component->assertRedirect($value);
            } elseif ($type === 'event') {
                $component->assertEmitted($value);
            } elseif ($type === 'see') {
                $component->assertSee($value);
            } elseif ($type === 'property') {
                foreach ($value as $prop => $propValue) {
                    $component->assertSet($prop, $propValue);
                }
            }
        }
    }

    /**
     * Assert that a Livewire component can be rendered
     */
    protected function assertLivewireCanRender(string $componentClass, array $params = []): void
    {
        $this->actingAs($this->user);

        // Mock the component render method to return an empty view
        // This prevents view-not-found errors in tests
        Livewire::test($componentClass, $params)
            ->assertStatus(200);
    }
}
