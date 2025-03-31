<?php

namespace Tests\Browser\Admin;

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\LoginPage;
use Tests\Browser\Pages\Admin\UserFormPage;
use Tests\Browser\Pages\Admin\UsersPage;
use Tests\DuskTestCase;

class AdminUsersTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test users page loads
     *
     * @return void
     */
    public function testUsersPageLoads()
    {
        $admin = Admin::factory()->create();
        User::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new UsersPage)
                    ->assertSee('Users List')
                    ->assertSee('Create User');
        });
    }

    /**
     * Test creating a new user
     *
     * @return void
     */
    public function testCreateUser()
    {
        $admin = Admin::factory()->create();
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser' . time() . '@example.com',
            'password' => 'password123',
            'role' => UserRole::USER->value,
        ];

        $this->browse(function (Browser $browser) use ($admin, $userData) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new UsersPage)
                    ->navigateToCreate()
                    ->on(new UserFormPage)
                    ->fillForm($userData)
                    ->submit()
                    ->on(new UsersPage)
                    ->assertSee('User created successfully')
                    ->assertSee($userData['name']);
        });
    }

    /**
     * Test viewing a user
     *
     * @return void
     */
    public function testViewUser()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new UsersPage)
                    ->viewFirstUser()
                    ->assertPathContains('/admin/users')
                    ->assertSee($user->name)
                    ->assertSee($user->email);
        });
    }

    /**
     * Test editing a user
     *
     * @return void
     */
    public function testEditUser()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $newName = 'Updated User Name';

        $this->browse(function (Browser $browser) use ($admin, $user, $newName) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new UsersPage)
                    ->editFirstUser()
                    ->on(new UserFormPage)
                    ->type('@name-input', $newName)
                    ->submit()
                    ->on(new UsersPage)
                    ->assertSee('User updated successfully')
                    ->assertSee($newName);
        });
    }

    /**
     * Test deleting a user
     *
     * @return void
     */
    public function testDeleteUser()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create([
            'name' => 'User To Delete',
            'email' => 'delete' . time() . '@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new UsersPage)
                    ->search($user->email)
                    ->assertSee($user->name)
                    ->deleteFirstUser()
                    ->assertSee('User deleted successfully')
                    ->search($user->email)
                    ->assertDontSee($user->name);
        });
    }

    /**
     * Test searching for users
     *
     * @return void
     */
    public function testSearchUsers()
    {
        $admin = Admin::factory()->create();
        $uniqueName = 'Unique User ' . time();
        $user = User::factory()->create(['name' => $uniqueName]);
        User::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($admin, $uniqueName) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new UsersPage)
                    ->search($uniqueName)
                    ->assertSee($uniqueName)
                    ->assertDontSee('No users found');
        });
    }
} 