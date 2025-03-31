<?php

namespace Tests\Feature\Livewire\Categories;

use App\Livewire\Categories\CategoryList;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function category_list_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryList::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_can_display_categories()
    {
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Work',
            'color' => '#ff0000',
        ]);

        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Personal',
            'color' => '#00ff00',
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryList::class)
            ->assertSee('Work')
            ->assertSee('Personal');
    }

    /** @test */
    public function it_only_shows_categories_for_authenticated_user()
    {
        $otherUser = User::factory()->create();
        
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'My Category',
        ]);

        Category::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Category',
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryList::class)
            ->assertSee('My Category')
            ->assertDontSee('Other User Category');
    }

    /** @test */
    public function it_can_delete_category()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'To Delete',
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryList::class)
            ->call('confirmDelete', $category->id)
            ->assertSet('categoryToDelete', $category->id)
            ->call('deleteCategory');
            
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }
} 