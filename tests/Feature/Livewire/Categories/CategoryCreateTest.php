<?php

namespace Tests\Feature\Livewire\Categories;

use App\Livewire\Categories\CategoryCreate;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function category_create_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryCreate::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryCreate::class)
            ->set('name', 'New Category')
            ->set('color', '#ff0000')
            ->call('createCategory');

        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'New Category',
            'color' => '#ff0000',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryCreate::class)
            ->set('name', '')
            ->set('color', '')
            ->call('createCategory')
            ->assertHasErrors(['name', 'color']);
    }

    /** @test */
    public function it_validates_unique_name_per_user()
    {
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Existing Category',
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryCreate::class)
            ->set('name', 'Existing Category')
            ->set('color', '#ff0000')
            ->call('createCategory')
            ->assertHasErrors(['name' => 'unique']);
    }

    /** @test */
    public function it_allows_same_name_for_different_users()
    {
        $otherUser = User::factory()->create();

        Category::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Shared Name',
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryCreate::class)
            ->set('name', 'Shared Name')
            ->set('color', '#ff0000')
            ->call('createCategory');

        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'Shared Name',
        ]);
    }
}
