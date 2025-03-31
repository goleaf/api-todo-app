<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSearchTest extends DuskTestCase
{
    /**
     * Test user search functionality.
     *
     * @return void
     */
    public function testUserSearch()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        // Create a test user with a unique name
        $testUser = User::create([
            'name' => 'SearchableTestUser' . time(),
            'email' => 'searchtest' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'active' => true,
        ]);

        try {
            $this->browse(function (Browser $browser) use ($admin, $testUser) {
                // Search for the test user
                $browser->loginAs($admin)
                        ->visit('/admin/users')
                        ->type('search', $testUser->name)
                        ->press('Search')
                        ->assertSee($testUser->name)
                        ->assertSee($testUser->email);

                // Search with no results
                $browser->visit('/admin/users')
                        ->type('search', 'NonExistentUser' . time())
                        ->press('Search')
                        ->assertSee('No users found');
            });
        } finally {
            // Clean up test data
            $testUser->delete();
        }
    }

    /**
     * Test category search functionality.
     *
     * @return void
     */
    public function testCategorySearch()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test category with a unique name
        $testCategory = Category::create([
            'name' => 'SearchableTestCategory' . time(),
            'user_id' => $user->id,
            'type' => 'work',
            'color' => '#FF5733'
        ]);

        try {
            $this->browse(function (Browser $browser) use ($admin, $testCategory) {
                // Search for the test category
                $browser->loginAs($admin)
                        ->visit('/admin/categories')
                        ->type('search', $testCategory->name)
                        ->press('Search')
                        ->assertSee($testCategory->name);

                // Test filter by user
                $browser->visit('/admin/categories')
                        ->select('user_id', $testCategory->user_id)
                        ->waitForReload()
                        ->assertSee($testCategory->name);
            });
        } finally {
            // Clean up test data
            $testCategory->delete();
        }
    }

    /**
     * Test tag search functionality.
     *
     * @return void
     */
    public function testTagSearch()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test tag with a unique name
        $testTag = Tag::create([
            'name' => 'SearchableTestTag' . time(),
            'user_id' => $user->id,
            'color' => '#3355FF'
        ]);

        try {
            $this->browse(function (Browser $browser) use ($admin, $testTag) {
                // Search for the test tag
                $browser->loginAs($admin)
                        ->visit('/admin/tags')
                        ->type('search', $testTag->name)
                        ->press('Search')
                        ->assertSee($testTag->name);

                // Test filter by user
                $browser->visit('/admin/tags')
                        ->select('user_id', $testTag->user_id)
                        ->waitForReload()
                        ->assertSee($testTag->name);
            });
        } finally {
            // Clean up test data
            $testTag->delete();
        }
    }

    /**
     * Test task search functionality.
     *
     * @return void
     */
    public function testTaskSearch()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test task with a unique title
        $testTask = Task::create([
            'title' => 'SearchableTestTask' . time(),
            'description' => 'This is a test task for search',
            'user_id' => $user->id,
            'priority' => 'medium',
            'progress' => 50,
            'completed' => false
        ]);

        try {
            $this->browse(function (Browser $browser) use ($admin, $testTask, $user) {
                // Search for the test task by title
                $browser->loginAs($admin)
                        ->visit('/admin/tasks')
                        ->type('search', $testTask->title)
                        ->press('Search')
                        ->assertSee($testTask->title);

                // Filter by user
                $browser->visit('/admin/tasks')
                        ->select('user_id', $user->id)
                        ->waitForReload()
                        ->assertSee($testTask->title);

                // Filter by status (incomplete)
                $browser->visit('/admin/tasks')
                        ->select('status', 'incomplete')
                        ->waitForReload()
                        ->assertSee($testTask->title);

                // Filter by priority
                $browser->visit('/admin/tasks')
                        ->select('priority', 'medium')
                        ->waitForReload()
                        ->assertSee($testTask->title);
            });
        } finally {
            // Clean up test data
            $testTask->delete();
        }
    }
} 