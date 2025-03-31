<?php

namespace Tests\Feature\Events;

use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CategoryEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_category_created_event_when_creating_a_category()
    {
        Event::fake([CategoryCreated::class]);

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

        Event::fake([CategoryUpdated::class]);

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

        Event::fake([CategoryUpdated::class]);

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

        Event::fake([CategoryDeleted::class]);

        $category->delete();

        Event::assertDispatched(CategoryDeleted::class, function ($event) use ($categoryId) {
            return $event->category->id === $categoryId;
        });
    }
} 