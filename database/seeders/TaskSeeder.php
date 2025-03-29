<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // Create predefined tasks for the test user
        $testUser = User::where('email', 'test@example.com')->first();

        if ($testUser) {
            $tasks = [
                [
                    'user_id' => $testUser->id,
                    'title' => 'Complete project documentation',
                    'description' => 'Finish writing all documentation for the current project',
                    'status' => 'in_progress',
                    'completed' => false,
                    'priority' => 2, // High priority
                    'due_date' => now()->addDays(2),
                ],
                [
                    'user_id' => $testUser->id,
                    'title' => 'Set up CI/CD pipeline',
                    'description' => 'Configure GitHub Actions for continuous integration and deployment',
                    'status' => 'pending',
                    'completed' => false,
                    'priority' => 1, // Medium priority
                    'due_date' => now()->addDays(5),
                ],
                [
                    'user_id' => $testUser->id,
                    'title' => 'Review pull requests',
                    'description' => 'Review and merge pending pull requests from the team',
                    'status' => 'completed',
                    'completed' => true,
                    'priority' => 0, // Low priority
                    'due_date' => now()->subDays(2),
                ],
                [
                    'user_id' => $testUser->id,
                    'title' => 'Fix critical security bug',
                    'description' => 'Patch the authentication vulnerability identified in the security audit',
                    'status' => 'pending',
                    'completed' => false,
                    'priority' => 2, // High priority
                    'due_date' => now()->addDay(),
                ],
                [
                    'user_id' => $testUser->id,
                    'title' => 'Prepare for team meeting',
                    'description' => 'Create slides and talking points for the weekly status meeting',
                    'status' => 'pending',
                    'completed' => false,
                    'priority' => 1, // Medium priority
                    'due_date' => now()->addDays(3),
                ],
            ];

            foreach ($tasks as $task) {
                Task::create($task);
            }
        }

        // Create random tasks for each user
        foreach ($users as $user) {
            // Create tasks with specific priorities to ensure a good distribution
            Task::factory()->highPriority()->count(rand(1, 2))->create(['user_id' => $user->id]);
            Task::factory()->mediumPriority()->count(rand(1, 2))->create(['user_id' => $user->id]);
            Task::factory()->lowPriority()->count(rand(1, 2))->create(['user_id' => $user->id]);

            // Create some completed tasks
            Task::factory()->completed()->count(rand(2, 4))->create(['user_id' => $user->id]);

            // Create additional random tasks
            Task::factory()->count(rand(0, 3))->create(['user_id' => $user->id]);
        }
    }
}
