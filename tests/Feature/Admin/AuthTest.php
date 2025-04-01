<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_login_page()
    {
        $response = $this->get(route('admin.login'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.auth.login');
    }
    
    public function test_admin_can_login_with_correct_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'test@admin.com',
            'password' => bcrypt('password123'),
        ]);
        
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'test@admin.com',
            'password' => 'password123',
        ]);
        
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }
    
    public function test_admin_cannot_login_with_incorrect_password()
    {
        $admin = Admin::factory()->create([
            'email' => 'test@admin.com',
            'password' => bcrypt('password123'),
        ]);
        
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'test@admin.com',
            'password' => 'wrong-password',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }
    
    public function test_admin_can_logout()
    {
        $admin = Admin::factory()->create();
        
        $this->actingAs($admin, 'admin');
        $this->assertAuthenticatedAs($admin, 'admin');
        
        $response = $this->post(route('admin.logout'));
        
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }
    
    public function test_authenticated_admin_cannot_access_login_page()
    {
        $admin = Admin::factory()->create();
        
        $this->actingAs($admin, 'admin');
        
        $response = $this->get(route('admin.login'));
        
        $response->assertRedirect();
    }
} 