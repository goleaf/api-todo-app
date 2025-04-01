<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Tag;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class TagTest extends TestCase
{
    use WithFaker;

    protected User $adminUser;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);
    }

    public function test_admin_can_view_tags_index()
    {
        // Create test data
        $user = User::factory()->create();
        $tags = Tag::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tags.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.index');
        $response->assertViewHas('tags');
        
        // Check that all tags are in the view
        foreach ($tags as $tag) {
            $response->assertSee($tag->name);
        }
    }

    public function test_admin_can_create_a_tag()
    {
        // Create test data
        $user = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tags.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.form');

        // Submit the form to create a tag
        $tagData = [
            'name' => 'Test Tag',
            'user_id' => $user->id,
            'color' => '#ff0000',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.tags.store'), $tagData);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        // Check that the tag was created in the database
        $this->assertDatabaseHas('tags', [
            'name' => 'Test Tag',
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_view_a_tag()
    {
        // Create test data
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Tag',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tags.show', $tag));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.show');
        $response->assertSee('Test Tag');
    }

    public function test_admin_can_update_a_tag()
    {
        // Create test data
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tags.edit', $tag));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.form');

        // Submit the form to update the tag
        $updatedData = [
            'name' => 'Updated Name',
            'user_id' => $user->id,
            'color' => '#00ff00',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.tags.update', $tag), $updatedData);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        // Check that the tag was updated in the database
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_a_tag()
    {
        // Create test data
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        // Check that the tag was deleted from the database
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    public function test_admin_can_delete_tag_with_tasks()
    {
        // Create test data
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create a task and attach the tag
        $task = \App\Models\Task::factory()->create([
            'user_id' => $user->id,
        ]);
        $task->tags()->attach($tag->id);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        // Check that the tag was deleted from the database
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
        
        // Check that the relationship was removed
        $this->assertDatabaseMissing('task_tag', [
            'tag_id' => $tag->id,
        ]);
    }
} 