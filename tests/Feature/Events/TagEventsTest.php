<?php

namespace Tests\Feature\Events;

use App\Events\TagCreated;
use App\Events\TagDeleted;
use App\Events\TagUpdated;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TagEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_tag_created_event_when_creating_a_tag()
    {
        Event::fake([TagCreated::class]);

        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#ff0000',
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(TagCreated::class, function ($event) use ($tag) {
            return $event->tag->id === $tag->id;
        });
    }

    /** @test */
    public function it_dispatches_tag_updated_event_when_updating_a_tag()
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#ff0000',
            'user_id' => $user->id,
        ]);

        Event::fake([TagUpdated::class]);

        $tag->update(['name' => 'Updated Tag']);

        Event::assertDispatched(TagUpdated::class, function ($event) use ($tag) {
            return $event->tag->id === $tag->id && $event->tag->name === 'Updated Tag';
        });
    }

    /** @test */
    public function it_does_not_dispatch_tag_updated_event_when_no_changes_made()
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#ff0000',
            'user_id' => $user->id,
        ]);

        Event::fake([TagUpdated::class]);

        $tag->update(['name' => 'Test Tag']); // No actual change

        Event::assertNotDispatched(TagUpdated::class);
    }

    /** @test */
    public function it_dispatches_tag_deleted_event_when_deleting_a_tag()
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#ff0000',
            'user_id' => $user->id,
        ]);

        $tagId = $tag->id;
        $userId = $tag->user_id;

        Event::fake([TagDeleted::class]);

        $tag->delete();

        Event::assertDispatched(TagDeleted::class, function ($event) use ($tagId, $userId) {
            return $event->tagId === $tagId && $event->userId === $userId;
        });
    }

    /** @test */
    public function it_dispatches_tag_deleted_event_when_using_static_delete()
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#ff0000',
            'user_id' => $user->id,
        ]);

        $tagId = $tag->id;
        $userId = $tag->user_id;

        Event::fake([TagDeleted::class]);

        Tag::destroy($tag->id);

        Event::assertDispatched(TagDeleted::class, function ($event) use ($tagId, $userId) {
            return $event->tagId === $tagId && $event->userId === $userId;
        });
    }
} 