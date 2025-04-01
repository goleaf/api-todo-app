<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;

class UserTest extends AdminTestCase
{
    use WithFaker;

    /**
     * Test if admin can view users list
     *
     * @return void
     */
    public function test_admin_can_view_users_list()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create([
            'name' => 'Test Regular User',
            'email' => 'user@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAdmin($browser)
                ->clickLink('Users')
                ->assertPathIs('/admin/users')
                ->assertSee('User Management')
                ->assertSee('Test Regular User')
                ->assertSee('user@example.com');
        });
    }

    /**
     * Test if admin can view user details
     *
     * @return void
     */
    public function test_admin_can_view_user_details()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create([
            'name' => 'Test User Details',
            'email' => 'details@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $this->loginAdmin($browser)
                ->clickLink('Users')
                ->assertPathIs('/admin/users')
                ->click('@view-user-' . $user->id)
                ->assertPathIs('/admin/users/' . $user->id)
                ->assertSee('Test User Details')
                ->assertSee('details@example.com');
        });
    }

    /**
     * Test if admin can create a new user
     *
     * @return void
     */
    public function test_admin_can_create_user()
    {
        $admin = $this->createAdminUser();
        
        $userName = 'New User ' . $this->faker->name;
        $userEmail = $this->faker->unique()->safeEmail;

        $this->browse(function (Browser $browser) use ($admin, $userName, $userEmail) {
            $this->loginAdmin($browser)
                ->clickLink('Users')
                ->assertPathIs('/admin/users')
                ->clickLink('Create User')
                ->assertPathIs('/admin/users/create')
                ->type('name', $userName)
                ->type('email', $userEmail)
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->select('role', 'user')
                ->press('Save')
                ->waitForLocation('/admin/users')
                ->assertSee($userName)
                ->assertSee($userEmail);
        });
    }

    /**
     * Test if admin can edit a user
     *
     * @return void
     */
    public function test_admin_can_edit_user()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create([
            'name' => 'User to Edit',
            'email' => 'edit@example.com',
        ]);
        
        $updatedName = 'Updated User ' . $this->faker->name;

        $this->browse(function (Browser $browser) use ($admin, $user, $updatedName) {
            $this->loginAdmin($browser)
                ->clickLink('Users')
                ->assertPathIs('/admin/users')
                ->click('@edit-user-' . $user->id)
                ->assertPathIs('/admin/users/' . $user->id . '/edit')
                ->assertInputValue('name', 'User to Edit')
                ->type('name', $updatedName)
                ->press('Save')
                ->waitForLocation('/admin/users')
                ->assertSee($updatedName);
        });
    }

    /**
     * Test if admin can delete a user
     *
     * @return void
     */
    public function test_admin_can_delete_user()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create([
            'name' => 'User to Delete',
            'email' => 'delete@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $this->loginAdmin($browser)
                ->clickLink('Users')
                ->assertPathIs('/admin/users')
                ->assertSee('User to Delete')
                ->click('@delete-user-' . $user->id)
                ->waitForDialog()
                ->acceptDialog()
                ->waitUntilMissing('@delete-user-' . $user->id)
                ->assertDontSee('User to Delete');
        });
    }
} 