<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserCrudTest extends DuskTestCase
{
    /**
     * Test user listing page.
     */
    public function testUserListingPage()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/users')
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/users')
                    ->clickLink('Create New User')
                    ->assertPathIs('/admin/users/create')
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $testUserEmail = 'test_' . time() . '@example.com';

        $this->browse(function (Browser $browser) use ($admin, $testUserEmail) {
            $browser->loginAs($admin)
                    ->visit('/admin/users/create')
                    ->type('name', 'Test User')
                    ->type('email', $testUserEmail)
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->select('role', 'user')
                    ->check('active')
                    ->press('Save')
                    ->waitForLocation('/admin/users')
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
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit('/admin/users')
                    ->click('@edit-user-' . $user->id)
                    ->assertPathIs('/admin/users/' . $user->id . '/edit')
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
        $admin = User::where('role', 'admin')->first();
        
        // Create a test user that we can modify
        $user = User::create([
            'name' => 'Test Update User',
            'email' => 'test_update_' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'active' => true,
        ]);

        if (!$admin) {
            $this->markTestSkipped('Admin not found in the database');
            $user->delete();
        }

        $newName = 'Updated User ' . time();

        $this->browse(function (Browser $browser) use ($admin, $user, $newName) {
            $browser->loginAs($admin)
                    ->visit('/admin/users/' . $user->id . '/edit')
                    ->clear('name')
                    ->type('name', $newName)
                    ->press('Save')
                    ->waitForLocation('/admin/users')
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
        $admin = User::where('role', 'admin')->first();
        
        // Create a test user that we can delete
        $user = User::create([
            'name' => 'Test Delete User',
            'email' => 'test_delete_' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'active' => true,
        ]);

        if (!$admin) {
            $this->markTestSkipped('Admin not found in the database');
            $user->delete();
        }

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit('/admin/users')
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