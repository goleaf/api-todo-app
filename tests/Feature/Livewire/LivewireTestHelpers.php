<?php

namespace Tests\Feature\Livewire;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;

class LivewireTestHelpers
{
    /**
     * Create a test environment with a user and optional tasks
     *
     * @param  int  $taskCount  Number of tasks to create
     * @return array Array containing user and tasks collection
     */
    public static function createTestEnvironment(int $taskCount = 0): array
    {
        $user = User::factory()->create([
            'email' => 'test_'.uniqid().'@example.com',
        ]);

        $tasks = new Collection;

        if ($taskCount > 0) {
            $tasks = Task::factory()->count($taskCount)->create([
                'user_id' => $user->id,
            ]);
        }

        return [$user, $tasks];
    }

    /**
     * Test a Livewire component as a specific user
     *
     * @param  string  $componentClass  The Livewire component class
     * @param  User  $user  The user to authenticate as
     * @param  array  $componentParams  Component parameters
     * @return \Livewire\Testing\TestableLivewire
     */
    public static function testComponentAsUser(string $componentClass, User $user, array $componentParams = [])
    {
        return Livewire::actingAs($user)->test($componentClass, $componentParams);
    }

    /**
     * Test a form submission with validation
     *
     * @param  string  $componentClass  The Livewire component class
     * @param  string  $submitMethod  The method to call for submission
     * @param  array  $formData  Form data to set
     * @param  array  $expectedErrors  Expected validation errors (if any)
     * @return \Livewire\Testing\TestableLivewire
     */
    public static function testFormSubmission(
        string $componentClass,
        string $submitMethod,
        array $formData,
        array $expectedErrors = []
    ) {
        $component = Livewire::test($componentClass);

        // Set form values
        foreach ($formData as $field => $value) {
            $component->set($field, $value);
        }

        // Call submit method
        $component->call($submitMethod);

        // Assert errors if expected
        if (! empty($expectedErrors)) {
            $component->assertHasErrors($expectedErrors);
        } else {
            $component->assertHasNoErrors();
        }

        return $component;
    }

    /**
     * Test component state changes before and after method call
     *
     * @param  string  $componentClass  The Livewire component class
     * @param  array  $initialProps  Initial properties
     * @param  string  $methodToCall  Method to call
     * @param  array  $expectedProps  Expected properties after call
     * @param  array  $methodParams  Method parameters
     * @return \Livewire\Testing\TestableLivewire
     */
    public static function testComponentState(
        string $componentClass,
        array $initialProps,
        string $methodToCall,
        array $expectedProps,
        array $methodParams = []
    ) {
        $component = Livewire::test($componentClass);

        // Set initial properties
        foreach ($initialProps as $prop => $value) {
            $component->set($prop, $value);
        }

        // Verify initial state if needed
        foreach ($initialProps as $prop => $value) {
            $component->assertSet($prop, $value);
        }

        // Call the method
        $component->call($methodToCall, ...$methodParams);

        // Verify resulting state
        foreach ($expectedProps as $prop => $value) {
            $component->assertSet($prop, $value);
        }

        return $component;
    }

    /**
     * Test a component's event emission
     *
     * @param  string  $componentClass  The Livewire component class
     * @param  string  $methodToCall  Method to call
     * @param  string  $eventName  Expected event name
     * @param  array  $eventParams  Expected event parameters
     * @param  array  $methodParams  Method parameters
     * @return \Livewire\Testing\TestableLivewire
     */
    public static function testComponentEvent(
        string $componentClass,
        string $methodToCall,
        string $eventName,
        array $eventParams = [],
        array $methodParams = []
    ) {
        $component = Livewire::test($componentClass);

        // Call the method
        $component->call($methodToCall, ...$methodParams);

        // Verify event was emitted
        if (empty($eventParams)) {
            $component->assertEmitted($eventName);
        } else {
            $component->assertEmitted($eventName, ...$eventParams);
        }

        return $component;
    }
}
