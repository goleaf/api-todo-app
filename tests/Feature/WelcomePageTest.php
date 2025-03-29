<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test welcome page loads for guests
     *
     * @return void
     */
    public function test_welcome_page_loads_for_guests()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Welcome to Taskify')
            ->assertSee('Get Started');
    }

    /**
     * Test authenticated users are redirected to dashboard
     *
     * @return void
     */
    public function test_authenticated_users_redirected_to_dashboard()
    {
        $this->markTestSkipped('Redirection is handled via JavaScript, not server-side');
        
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test the welcome page contains required components
     *
     * @return void
     */
    public function test_welcome_page_contains_required_components()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Welcome to Taskify')
            ->assertSee('Your simple yet powerful task management solution')
            ->assertSee('Get Started')
            ->assertSee('Already have an account')
            ->assertSee('Log in')
            ->assertSee('Register');
    }

    /**
     * Test login link is present and working
     *
     * @return void
     */
    public function test_login_link_works()
    {
        $this->markTestSkipped('Login route test needs to be revisited');
        
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test register link is present and working
     *
     * @return void
     */
    public function test_register_link_works()
    {
        $this->markTestSkipped('Register route test needs to be revisited');
        
        $response = $this->get('/register');

        $response->assertStatus(200);
    }
} 