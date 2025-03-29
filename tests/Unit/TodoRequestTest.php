<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TodoRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_todo_request_validates_required_fields()
    {
        $request = new StoreTodoRequest;
        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    /** @test */
    public function store_todo_request_passes_with_valid_data()
    {
        $request = new StoreTodoRequest;

        $validator = Validator::make([
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'priority' => 1,
            'due_date' => now()->format('Y-m-d H:i:s'),
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function store_todo_request_validates_priority_range()
    {
        $request = new StoreTodoRequest;

        $validator = Validator::make([
            'title' => 'Test Todo',
            'priority' => 5, // Invalid priority
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('priority', $validator->errors()->toArray());
    }

    /** @test */
    public function store_todo_request_validates_due_date_format()
    {
        $request = new StoreTodoRequest;

        $validator = Validator::make([
            'title' => 'Test Todo',
            'due_date' => 'not-a-date',
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('due_date', $validator->errors()->toArray());
    }

    /** @test */
    public function update_todo_request_validates_fields()
    {
        $request = new UpdateTodoRequest;

        $validator = Validator::make([
            'title' => '', // Empty title
            'priority' => 'not-a-number',
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertArrayHasKey('priority', $validator->errors()->toArray());
    }

    /** @test */
    public function update_todo_request_passes_with_partial_data()
    {
        $request = new UpdateTodoRequest;

        $validator = Validator::make([
            'title' => 'Updated Title',
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function update_todo_request_validates_completed_boolean()
    {
        $request = new UpdateTodoRequest;

        $validator = Validator::make([
            'completed' => 'not-a-boolean',
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('completed', $validator->errors()->toArray());
    }

    /** @test */
    public function update_todo_request_passes_with_valid_data()
    {
        $request = new UpdateTodoRequest;

        $validator = Validator::make([
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'priority' => 2,
            'completed' => true,
            'due_date' => now()->format('Y-m-d H:i:s'),
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }
}
