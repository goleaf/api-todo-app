<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected string $baseUrl = '/api/categories';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function a_user_can_get_all_categories()
    {
        Sanctum::actingAs($this->user);

        Category::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'color',
                        'icon',
                        'user_id',
                        'created_at',
                        'updated_at',
                        'task_count',
                        'completed_task_count',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function a_user_can_create_a_category()
    {
        Sanctum::actingAs($this->user);

        $data = [
            'name' => $this->faker->word(),
            'color' => '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
            'icon' => 'folder',
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $data['name'],
                    'color' => $data['color'],
                    'icon' => $data['icon'],
                    'user_id' => $this->user->id,
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => $data['name'],
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function a_user_can_show_a_category()
    {
        Sanctum::actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("{$this->baseUrl}/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'user_id' => $this->user->id,
                ],
            ]);
    }

    /** @test */
    public function a_user_can_update_a_category()
    {
        Sanctum::actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'name' => 'Updated Category Name',
            'color' => '#FF5733',
            'icon' => 'edit',
        ];

        $response = $this->putJson("{$this->baseUrl}/{$category->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => $data['name'],
                    'color' => $data['color'],
                    'icon' => $data['icon'],
                    'user_id' => $this->user->id,
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $data['name'],
            'color' => $data['color'],
            'icon' => $data['icon'],
        ]);
    }

    /** @test */
    public function a_user_can_delete_a_category()
    {
        Sanctum::actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/{$category->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function a_user_can_get_task_counts_by_category()
    {
        Sanctum::actingAs($this->user);

        $categories = Category::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("{$this->baseUrl}/task-counts");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'color',
                        'icon',
                        'user_id',
                        'tasks_count',
                        'completed_tasks_count',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_user_cannot_access_categories_from_another_user()
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->getJson("{$this->baseUrl}/{$category->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function validation_fails_when_creating_category_with_invalid_data()
    {
        Sanctum::actingAs($this->user);

        $data = [
            'name' => '',
            'color' => 'invalid-color',
            'icon' => '',
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'color', 'icon']);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_categories()
    {
        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(401);
    }
}
