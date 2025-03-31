<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskFormValidationTest extends DuskTestCase
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
                    ->visit('/admin/tasks/create')
                    ->press('Save')
                    ->assertSee('The title field is required')
                    ->assertSee('The user id field is required');
        });
    }

    /**
     * Test date validation.
     *
     * @return void
     */
    public function testDateValidation()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit('/admin/tasks/create')
                    ->type('title', 'Test Task')
                    ->select('user_id', $user->id)
                    ->type('due_date', 'invalid-date')
                    ->press('Save')
                    ->assertSee('The due date is not a valid date');
        });
    }

    /**
     * Test progress validation.
     *
     * @return void
     */
    public function testProgressValidation()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin, $user) {
            // Test value below minimum
            $browser->loginAs($admin)
                    ->visit('/admin/tasks/create')
                    ->type('title', 'Test Task')
                    ->select('user_id', $user->id)
                    ->type('progress', '-10')
                    ->press('Save')
                    ->assertSee('The progress must be at least 0');

            // Test value above maximum
            $browser->visit('/admin/tasks/create')
                    ->type('title', 'Test Task')
                    ->select('user_id', $user->id)
                    ->type('progress', '110')
                    ->press('Save')
                    ->assertSee('The progress must not be greater than 100');
        });
    }

    /**
     * Test priority validation.
     *
     * @return void
     */
    public function testPriorityValidation()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit('/admin/tasks/create')
                    ->type('title', 'Test Task')
                    ->select('user_id', $user->id)
                    ->select('priority', 'invalid-priority')
                    ->press('Save')
                    ->assertSee('The selected priority is invalid');
        });
    }
} 