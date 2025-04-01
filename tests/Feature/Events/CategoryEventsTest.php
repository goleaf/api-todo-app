<?php

namespace Tests\Feature\Events;

use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class CategoryEventsTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        Event::fake([
            CategoryCreated::class,
            CategoryUpdated::class,
            CategoryDeleted::class,
        ]);
    }

    /** @test */
    public function it_dispatches_category_created_event_when_creating_a_category()
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'folder',
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(CategoryCreated::class, function ($event) use ($category) {
            return $event->category->id === $category->id;
        });
    }

    /** @test */
    public function it_dispatches_category_updated_event_when_updating_a_category()
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'folder',
            'user_id' => $user->id,
        ]);

        $category->update(['name' => 'Updated Category']);

        Event::assertDispatched(CategoryUpdated::class, function ($event) use ($category) {
            return $event->category->id === $category->id && $event->category->name === 'Updated Category';
        });
    }

    /** @test */
    public function it_does_not_dispatch_category_updated_event_when_no_changes_made()
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'folder',
            'user_id' => $user->id,
        ]);

        $category->update(['name' => 'Test Category']); // No actual change

        Event::assertNotDispatched(CategoryUpdated::class);
    }

    /** @test */
    public function it_dispatches_category_deleted_event_when_deleting_a_category()
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'folder',
            'user_id' => $user->id,
        ]);

        $categoryId = $category->id;
        $userId = $category->user_id;

        $category->delete();

        Event::assertDispatched(CategoryDeleted::class, function ($event) use ($categoryId, $userId) {
            return $event->categoryId === $categoryId && $event->userId === $userId;
        });
    }
} 