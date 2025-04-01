<?php

namespace Tests\Feature\Events;

use App\Events\TagCreated;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use App\Events\CategoryCreated;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class EventBroadcastingTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
    }

    /** @test */
    public function task_created_event_broadcasts_on_the_correct_channel()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $event = new TaskCreated($task);
        
        $this->assertEquals(['private-user.' . $user->id], array_map(function ($channel) {
            return $channel->name;
        }, $event->broadcastOn()));
        
        $this->assertEquals('task.created', $event->broadcastAs());
        
        $this->assertArrayHasKey('task', $event->broadcastWith());
    }

    /** @test */
    public function category_created_event_broadcasts_on_the_correct_channel()
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'folder',
            'user_id' => $user->id,
        ]);

        $event = new CategoryCreated($category);
        
        $this->assertEquals(['private-user.' . $user->id], array_map(function ($channel) {
            return $channel->name;
        }, $event->broadcastOn()));
        
        $this->assertEquals('category.created', $event->broadcastAs());
        
        $this->assertArrayHasKey('category', $event->broadcastWith());
    }

    /** @test */
    public function tag_created_event_broadcasts_on_the_correct_channel()
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#ff0000',
            'user_id' => $user->id,
        ]);

        $event = new TagCreated($tag);
        
        $this->assertEquals(['private-user.' . $user->id], array_map(function ($channel) {
            return $channel->name;
        }, $event->broadcastOn()));
        
        $this->assertEquals('tag.created', $event->broadcastAs());
        
        $this->assertArrayHasKey('tag', $event->broadcastWith());
    }
} 