<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the profile edit page can be rendered.
     */
    public function test_profile_edit_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
        $response->assertViewHas('user', $user);
    }

    /**
     * Test that the profile information can be updated.
     */
    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User Updated',
                'email' => 'test-updated@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $user->refresh();
        $this->assertEquals('Test User Updated', $user->name);
        $this->assertEquals('test-updated@example.com', $user->email);
    }

    /**
     * Test that email must be unique when updating profile.
     */
    public function test_email_must_be_unique_when_updating_profile(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this
            ->actingAs($user1)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => $user2->email,
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test that user password is required for deleting account.
     */
    public function test_user_password_is_required_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors('password');
        $this->assertNotNull($user->fresh());
    }

    /**
     * Test that user can delete their account with correct password.
     */
    public function test_user_can_delete_their_account_with_correct_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => $password,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}
