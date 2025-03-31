<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin login page loads properly.
     */
    public function test_login_page_loads(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.auth.login');
    }

    /**
     * Test successful admin login.
     */
    public function test_admin_can_login(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'test@admin.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'test@admin.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    /**
     * Test admin login with invalid credentials.
     */
    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        Admin::factory()->create([
            'email' => 'test@admin.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'test@admin.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    /**
     * Test admin logout.
     */
    public function test_admin_can_logout(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.logout'));
        
        $response->assertRedirect();
        $this->assertGuest('admin');
    }

    /**
     * Test unauthenticated admin cannot access protected routes.
     */
    public function test_unauthenticated_admin_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(302);
    }
} 