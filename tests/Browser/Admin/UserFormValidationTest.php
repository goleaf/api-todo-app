<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserFormValidationTest extends DuskTestCase
{
    /**
     * Test validation for required fields.
     *
     * @return void
     */
    public function testRequiredFieldsValidation()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.create'))
                    ->press('Save')
                    ->assertSee('The name field is required')
                    ->assertSee('The email field is required')
                    ->assertSee('The password field is required');
        });
    }

    /**
     * Test email validation.
     *
     * @return void
     */
    public function testEmailValidation()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.create'))
                    ->type('name', 'Test User')
                    ->type('email', 'invalid-email')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('Save')
                    ->assertSee('The email must be a valid email address');
        });
    }

    /**
     * Test password confirmation validation.
     *
     * @return void
     */
    public function testPasswordConfirmationValidation()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.create'))
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'different-password')
                    ->press('Save')
                    ->assertSee('The password confirmation does not match');
        });
    }

    /**
     * Test email uniqueness validation.
     *
     * @return void
     */
    public function testEmailUniquenessValidation()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.users.create'))
                    ->type('name', 'Test User')
                    ->type('email', $admin->email) // Use an existing email
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('Save')
                    ->assertSee('The email has already been taken');
        });
    }
} 