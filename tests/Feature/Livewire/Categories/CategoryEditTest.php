<?php

namespace Tests\Feature\Livewire\Categories;

use App\Livewire\Categories\CategoryEdit;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryEditTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'color' => '#000000'
        ]);
    }

    /** @test */
    public function category_edit_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryEdit::class, ['categoryId' => $this->category->id])
            ->assertStatus(200)
            ->assertSet('name', 'Original Name')
            ->assertSet('color', '#000000');
    }

    /** @test */
    public function it_can_update_a_category()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryEdit::class, ['categoryId' => $this->category->id])
            ->set('name', 'Updated Name')
            ->set('color', '#ff0000')
            ->call('updateCategory');
            
        $this->assertDatabaseHas('categories', [
            'id' => $this->category->id,
            'name' => 'Updated Name',
            'color' => '#ff0000'
        ]);
    }
    
    /** @test */
    public function it_validates_required_fields()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryEdit::class, ['categoryId' => $this->category->id])
            ->set('name', '')
            ->set('color', '')
            ->call('updateCategory')
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
            ->test(CategoryEdit::class, ['categoryId' => $this->category->id])
            ->set('name', 'Existing Category')
            ->call('updateCategory')
            ->assertHasErrors(['name' => 'unique']);
    }
    
    /** @test */
    public function it_does_not_trigger_unique_validation_when_name_is_unchanged()
    {
        Livewire::actingAs($this->user)
            ->test(CategoryEdit::class, ['categoryId' => $this->category->id])
            ->set('color', '#ff00ff')
            ->call('updateCategory')
            ->assertHasNoErrors('name');
    }
    
    /** @test */
    public function it_prevents_editing_another_users_category()
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        $this->actingAs($this->user);
        
        $this->get(route('categories.edit', ['id' => $otherCategory->id]))
            ->assertStatus(403);
    }
} 