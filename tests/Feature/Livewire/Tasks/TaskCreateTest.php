<?php

namespace Tests\Feature\Livewire\Tasks;

use App\Livewire\Tasks\TaskCreate;
use App\Models\Task;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireFormTestCase;

class TaskCreateTest extends LivewireFormTestCase
{
    /** @test */
    public function task_create_component_can_render()
    {
        $this->assertLivewireCanSee(TaskCreate::class, 'Create Task');
    }
    
    /** @test */
    public function it_requires_a_title()
    {
        $this->assertFormFieldIsRequired(TaskCreate::class, 'form.title');
    }
    
    /** @test */
    public function it_validates_title_length()
    {
        Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', str_repeat('a', 256)) // More than 255 characters
            ->call('save')
            ->assertHasErrors(['form.title' => 'max']);
    }
    
    /** @test */
    public function it_validates_description_length()
    {
        Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', 'Valid Title')
            ->set('form.description', str_repeat('a', 1001)) // More than 1000 characters
            ->call('save')
            ->assertHasErrors(['form.description' => 'max']);
    }
    
    /** @test */
    public function it_can_create_a_task()
    {
        // Set up form data
        $formData = [
            'form.title' => 'Test Task',
            'form.description' => 'Test Description',
            'form.due_date' => now()->addDays(7)->format('Y-m-d'),
        ];
        
        // Set up expected outcome
        $expectedOutcome = [
            'event' => 'task-created',
            'database' => [
                'tasks' => [
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'user_id' => $this->user->id,
                ]
            ]
        ];
        
        // Test form submission
        $this->assertFormSubmitsSuccessfully(
            TaskCreate::class,
            $formData,
            $expectedOutcome,
            'save' // Method name to call
        );
    }
    
    /** @test */
    public function it_resets_form_after_successful_submission()
    {
        $this->assertFormResetsAfterSubmission(
            TaskCreate::class,
            [
                'form.title' => 'Test Task',
                'form.description' => 'Test Description',
            ],
            ['form.title', 'form.description'],
            'save'
        );
    }
    
    /** @test */
    public function it_allows_creating_task_without_description()
    {
        // Set up form data with only required fields
        $formData = [
            'form.title' => 'Test Task Without Description',
        ];
        
        // Test form submission
        $component = Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', 'Test Task Without Description')
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted('task-created');
            
        // Verify task was created
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task Without Description',
            'user_id' => $this->user->id,
        ]);
    }
    
    /** @test */
    public function it_shows_success_message_after_creation()
    {
        Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', 'Test Task')
            ->call('save')
            ->assertSee('Task created successfully');
    }
    
    /** @test */
    public function it_prevents_unauthorized_users_from_creating_tasks()
    {
        // Log out user
        auth()->logout();
        
        // Try to create task as guest
        $response = $this->get('/tasks/create');
        
        // Verify redirect to login
        $response->assertRedirect('/login');
    }
    
    /** @test */
    public function it_handles_validation_errors_correctly()
    {
        // This tests all the standard validation error handling in one test
        Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', '')
            ->set('form.description', str_repeat('a', 1001))
            ->call('save')
            ->assertHasErrors(['form.title', 'form.description']);
    }
} 