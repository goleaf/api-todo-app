<?php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivewireHttpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function livewire_components_are_loaded_properly()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSuccessful()
            ->assertSeeLivewire('dashboard.dashboard');
    }

    /** @test */
    public function livewire_assets_are_loaded()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSuccessful()
            ->assertSee('livewire:scripts')
            ->assertSee('livewire:styles');
    }

    /** @test */
    public function guest_users_cannot_access_protected_livewire_routes()
    {
        // Route that requires authentication
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        // Route that requires authentication
        $response = $this->get('/tasks');
        $response->assertRedirect('/login');

        // Route that requires authentication
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_can_access_protected_livewire_routes()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSuccessful();

        $this->actingAs($user)
            ->get('/tasks')
            ->assertSuccessful();

        $this->actingAs($user)
            ->get('/profile')
            ->assertSuccessful();
    }

    /** @test */
    public function livewire_component_update_endpoints_require_authentication()
    {
        // Attempt to make a Livewire component update request
        $response = $this->post('/livewire/message/tasks.task-list', [
            'fingerprint' => [
                'id' => 'some-id',
                'name' => 'tasks.task-list',
                'method' => 'toggleTask',
            ],
            'data' => [],
        ]);

        // Non-authenticated users should be redirected or receive unauthorized
        $response->assertStatus(401);
    }

    /** @test */
    public function livewire_routes_have_csrf_protection()
    {
        // Make a Livewire update request without CSRF token
        $response = $this->post('/livewire/message/tasks.task-list');

        // Should receive a 419 CSRF token mismatch error
        $response->assertStatus(419);
    }

    /** @test */
    public function livewire_components_have_proper_mount_parameters()
    {
        $user = User::factory()->create();

        // Test a route with parameters
        $this->actingAs($user)
            ->get('/tasks/1/edit')
            ->assertSuccessful()
            ->assertSeeLivewire('tasks.task-edit');
    }
}
