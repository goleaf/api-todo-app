<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserCrudTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * Test user listing page.
     */
    public function testUserListingPage()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.index'))
                    ->assertSee('Users')
                    ->assertSee('Create New User')
                    ->assertSee('ID')
                    ->assertSee('Name')
                    ->assertSee('Email')
                    ->assertSee('Role')
                    ->assertSee('Actions');
        });
    }

    /**
     * Test create user form.
     */
    public function testCreateUserForm()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.index'))
                    ->clickLink('Create New User')
                    ->assertRouteIs('admin.users.create')
                    ->assertSee('Create New User')
                    ->assertSee('Name')
                    ->assertSee('Email')
                    ->assertSee('Password')
                    ->assertSee('Role');
        });
    }

    /**
     * Test user creation.
     */
    public function testUserCreation()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $testUserEmail = 'test_' . time() . '@example.com';

        $this->browse(function (Browser $browser) use ($admin, $testUserEmail) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.create'))
                    ->type('name', 'Test User')
                    ->type('email', $testUserEmail)
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->select('role', 'user')
                    ->check('active')
                    ->press('Save')
                    ->waitForRoute('admin.users.index')
                    ->assertSee('User created successfully')
                    ->assertSee('Test User')
                    ->assertSee($testUserEmail);
        });

        // Clean up
        User::where('email', $testUserEmail)->delete();
    }

    /**
     * Test edit user form.
     */
    public function testEditUserForm()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Create a test user
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.index'))
                    ->click('@edit-user-' . $user->id)
                    ->assertRouteIs('admin.users.edit', [$user->id])
                    ->assertSee('Edit User')
                    ->assertInputValue('name', $user->name)
                    ->assertInputValue('email', $user->email);
        });
    }

    /**
     * Test user update.
     */
    public function testUserUpdate()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Create a test user that we can modify
        $user = User::factory()->create([
            'name' => 'Test Update User',
            'email' => 'test_update_' . time() . '@example.com',
            'role' => 'user',
            'active' => true,
        ]);

        $newName = 'Updated User ' . time();

        $this->browse(function (Browser $browser) use ($admin, $user, $newName) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.edit', $user->id))
                    ->clear('name')
                    ->type('name', $newName)
                    ->press('Save')
                    ->waitForRoute('admin.users.index')
                    ->assertSee('User updated successfully')
                    ->assertSee($newName);
        });

        // Clean up
        $user->delete();
    }

    /**
     * Test user deletion.
     */
    public function testUserDeletion()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Create a test user that we can delete
        $user = User::factory()->create([
            'name' => 'Test Delete User',
            'email' => 'test_delete_' . time() . '@example.com',
            'role' => 'user',
            'active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.index'))
                    ->assertSee($user->name)
                    ->click('@delete-user-' . $user->id)
                    ->waitForDialog()
                    ->acceptDialog()
                    ->waitForText('User deleted successfully')
                    ->assertSee('User deleted successfully')
                    ->assertDontSee($user->name);
        });

        // No need to clean up as the user should have been deleted by the test
    }
} 