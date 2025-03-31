<?php

namespace Tests\Feature\Api;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AsyncApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /**
     * Test batch tag operation on multiple tasks asynchronously.
     */
    public function test_batch_tag_operation(): void
    {
        // Create several tasks for the test
        $tasks = Task::factory(5)->create([
            'user_id' => $this->user->id,
        ]);
        
        // Create a few tags to use in the test
        $existingTag = Tag::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'existing-tag',
        ]);
        
        // Attach the existing tag to some tasks
        $tasks->take(2)->each(function ($task) use ($existingTag) {
            $task->tags()->attach($existingTag->id);
        });
        
        // Prepare the request data for adding tags
        $addRequest = [
            'task_ids' => $tasks->pluck('id')->toArray(),
            'tags' => ['new-tag-1', 'new-tag-2', 'existing-tag'],
            'operation' => 'add',
        ];
        
        // Test adding tags
        $response = $this->postJson('/api/async/batch-tag-operation', $addRequest);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'processed',
                    'total',
                    'success_count',
                    'operation',
                    'tags',
                ],
            ]);
            
        // Verify that all tasks have the specified tags
        foreach ($tasks as $task) {
            // Each task should have all tags now
            $this->assertEquals(3, $task->fresh()->tags()->count());
            
            foreach (['new-tag-1', 'new-tag-2', 'existing-tag'] as $tagName) {
                $tag = Tag::where('name', $tagName)
                    ->where('user_id', $this->user->id)
                    ->first();
                    
                $this->assertNotNull($tag);
                $this->assertTrue($task->fresh()->hasTag($tag->id));
            }
        }
        
        // Now test removing tags
        $removeRequest = [
            'task_ids' => $tasks->pluck('id')->toArray(),
            'tags' => ['new-tag-1', 'new-tag-2'],
            'operation' => 'remove',
        ];
        
        $response = $this->postJson('/api/async/batch-tag-operation', $removeRequest);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'processed',
                    'total',
                    'success_count',
                    'operation',
                    'tags',
                ],
            ]);
            
        // Verify that all tasks have only the existing tag
        foreach ($tasks as $task) {
            // Each task should have only 1 tag now
            $this->assertEquals(1, $task->fresh()->tags()->count());
            
            // They should have the existing tag
            $this->assertTrue($task->fresh()->hasTag($existingTag->id));
            
            // They should not have the removed tags
            foreach (['new-tag-1', 'new-tag-2'] as $tagName) {
                $tag = Tag::where('name', $tagName)
                    ->where('user_id', $this->user->id)
                    ->first();
                    
                $this->assertFalse($task->fresh()->hasTag($tag->id));
            }
        }
    }
    
    /**
     * Test batch tag operation with some invalid tasks.
     */
    public function test_batch_tag_operation_with_invalid_tasks(): void
    {
        // Create some valid tasks
        $tasks = Task::factory(3)->create([
            'user_id' => $this->user->id,
        ]);
        
        // Include some invalid task IDs
        $taskIds = array_merge(
            $tasks->pluck('id')->toArray(),
            [999, 1000] // Non-existent task IDs
        );
        
        $request = [
            'task_ids' => $taskIds,
            'tags' => ['test-tag'],
            'operation' => 'add',
        ];
        
        $response = $this->postJson('/api/async/batch-tag-operation', $request);
        
        // Expect a validation error since the task IDs don't exist
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'task_ids.3',
                    'task_ids.4',
                ]
            ]);
            
        // The operation should not have been performed
        foreach ($tasks as $task) {
            $this->assertEquals(0, $task->fresh()->tags()->count());
        }
    }
} 