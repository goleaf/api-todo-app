<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ValidateTodoTaskMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:validate-todo-task-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate that code has been properly migrated from the Todo model to the Task model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Validating Todo to Task migration...');

        $patternsToCheck = [
            'new Todo\(',
            'use App\\\\Models\\\\Todo',
            'Todo::',
            'todos table',
        ];

        // These are partial strings that will match allowed patterns
        $allowedReferences = [
            // Intentionally preserved Todo components, routes, and tests
            'app/Livewire/Tasks/Todo.php',
            'Route::get(\'/todo\', Todo::class)', // Todo component route
            'Route::get(\'/todomvc\', App\\Livewire\\TodoMvc::class)', // TodoMvc route
            'Route::get(\'/todomvc/{filter?}\', App\\Livewire\\TodoMvc::class)', // TodoMvc filter route
            'tests/Feature/Livewire/Tasks/TodoTest.php', // Tests for Todo component
            'app/Livewire/TodoMvc.php', // TodoMvc component

            // Migration documentation and validation command itself
            'database/migrations/2025_03_30_184612_add_missing_fields_to_tasks_table.php', // Migration mentioning todos table
            'database/migrations/2025_03_30_184713_drop_todos_table.php', // Migration to drop todos table
            'app/Console/Commands/ValidateTodoTaskMigration.php', // This command
        ];

        $criticalIssuesFound = false;

        foreach ($patternsToCheck as $pattern) {
            $this->newLine();
            $this->info("Checking for pattern: {$pattern}");

            $process = Process::fromShellCommandline("grep -r --include='*.php' '{$pattern}' .");
            $process->setWorkingDirectory(base_path());
            $process->run();

            $output = $process->getOutput();

            if (empty($output)) {
                $this->info('No issues found.');

                continue;
            }

            // Filter out allowed references
            $issues = [];
            $lines = explode("\n", trim($output));

            foreach ($lines as $line) {
                $shouldExclude = false;

                foreach ($allowedReferences as $allowedRef) {
                    if (strpos($line, $allowedRef) !== false) {
                        $shouldExclude = true;
                        break;
                    }
                }

                if (! $shouldExclude) {
                    $issues[] = $line;
                    $criticalIssuesFound = true;
                }
            }

            if (empty($issues)) {
                $this->info('No issues found after filtering allowed references.');
            } else {
                $this->error('Found issues:');
                foreach ($issues as $issue) {
                    $this->line($issue);
                }
            }
        }

        if ($criticalIssuesFound) {
            $this->newLine();
            $this->error('Critical issues were found that need to be fixed!');
            $this->info('Please update the code to use the Task model instead of Todo.');

            return 1;
        }
            $this->newLine();
            $this->info('✅ All checks passed! The codebase appears to be fully migrated to use the Task model.');
            $this->info("Note: Some references to 'Todo' remain in component names and route names as documented in 'docs/TODO_TO_TASK_MIGRATION.md'.");

            return 0;
        
    }
}
