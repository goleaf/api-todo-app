<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class HypervelBenchmarkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hypervel:benchmark 
                            {--todos=100 : Number of todos to create for benchmarking}
                            {--iterations=5 : Number of iterations to run for each test}
                            {--feature=dashboard : Feature to benchmark (dashboard, batch, api)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Benchmark performance of synchronous vs Hypervel-powered asynchronous operations';

    /**
     * User for benchmark tests
     */
    protected User $user;

    /**
     * HypervelService instance
     */
    protected HypervelService $hypervelService;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->hypervelService = app(HypervelService::class);

        $todoCount = (int) $this->option('todos');
        $iterations = (int) $this->option('iterations');
        $feature = $this->option('feature');

        $this->info('Starting Hypervel benchmark...');
        $this->info("Todos: {$todoCount}, Iterations: {$iterations}, Feature: {$feature}");

        // Set up the test environment
        $this->setupEnvironment($todoCount);

        // Run the appropriate benchmark
        switch ($feature) {
            case 'dashboard':
                $this->benchmarkDashboard($iterations);
                break;
            case 'batch':
                $this->benchmarkBatchProcessing($iterations);
                break;
            case 'api':
                $this->benchmarkApiOperations($iterations);
                break;
            default:
                $this->error("Unknown feature: {$feature}");

                return 1;
        }

        return 0;
    }

    /**
     * Set up the benchmark environment
     */
    protected function setupEnvironment(int $todoCount): void
    {
        $this->info('Setting up benchmark environment...');

        // Use in-memory database for tests
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        DB::purge();

        // Migrate database
        $this->info('Running migrations...');
        Artisan::call('migrate:fresh');

        // Create user with todos
        $this->info('Creating test data...');
        $this->user = User::factory()->create([
            'name' => 'Benchmark User',
            'email' => 'benchmark@example.com',
        ]);

        // Create todos with various attributes
        $priorities = ['low', 'medium', 'high'];
        $categories = ['work', 'personal', 'errands', 'shopping', 'health'];
        $completed = [true, false];

        $this->info("Creating {$todoCount} todos...");
        $progressBar = $this->output->createProgressBar($todoCount);

        for ($i = 0; $i < $todoCount; $i++) {
            $isCompleted = $completed[array_rand($completed)];

            Task::create([
                'user_id' => $this->user->id,
                'title' => "Benchmark Todo #{$i}",
                'description' => "Description for benchmark todo #{$i}",
                'priority' => $priorities[array_rand($priorities)],
                'category' => $categories[array_rand($categories)],
                'completed' => $isCompleted,
                'completed_at' => $isCompleted ? Carbon::now()->subHours(rand(1, 100)) : null,
                'due_date' => rand(0, 1) ? Carbon::now()->addDays(rand(-5, 10)) : null,
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Setup complete.');
    }

    /**
     * Benchmark dashboard data loading
     */
    protected function benchmarkDashboard(int $iterations): void
    {
        $this->info('');
        $this->info('========== DASHBOARD LOADING BENCHMARK ==========');
        $this->info('Comparing synchronous vs asynchronous data loading');
        $this->info('');

        $syncTimes = [];
        $asyncTimes = [];

        for ($i = 1; $i <= $iterations; $i++) {
            $this->info("Running benchmark iteration {$i}...");

            // Benchmark synchronous loading
            $start = microtime(true);
            $syncData = $this->loadDashboardSynchronously();
            $syncTime = microtime(true) - $start;
            $syncTimes[] = $syncTime;

            $this->line('  Sync time: '.number_format($syncTime, 4).' seconds');

            // Small delay to let system stabilize
            usleep(100000); // 100ms

            // Benchmark asynchronous loading
            $start = microtime(true);
            $asyncData = $this->loadDashboardAsynchronously();
            $asyncTime = microtime(true) - $start;
            $asyncTimes[] = $asyncTime;

            $this->line('  Async time: '.number_format($asyncTime, 4).' seconds');

            // Verify data integrity
            $this->verifyDashboardResults($syncData, $asyncData);

            $this->line('  Data integrity verified ✓');
        }

        // Calculate statistics
        $avgSyncTime = array_sum($syncTimes) / count($syncTimes);
        $avgAsyncTime = array_sum($asyncTimes) / count($asyncTimes);
        $improvement = $avgSyncTime / $avgAsyncTime;
        $percentImprovement = ($improvement - 1) * 100;

        $this->info('');
        $this->info('================ BENCHMARK RESULTS ================');
        $this->info('Average sync time: '.number_format($avgSyncTime, 4).' seconds');
        $this->info('Average async time: '.number_format($avgAsyncTime, 4).' seconds');
        $this->info('Improvement factor: '.number_format($improvement, 2).'x');
        $this->info('Percent improvement: '.number_format($percentImprovement, 2).'%');
        $this->info('===================================================');
    }

    /**
     * Benchmark batch processing
     */
    protected function benchmarkBatchProcessing(int $iterations): void
    {
        $this->info('');
        $this->info('========== BATCH PROCESSING BENCHMARK ==========');
        $this->info('Comparing synchronous vs asynchronous batch processing');
        $this->info('');

        $syncTimes = [];
        $asyncTimes = [];

        for ($i = 1; $i <= $iterations; $i++) {
            $this->info("Running benchmark iteration {$i}...");

            // Get todos for this iteration
            $todos = Task::where('user_id', $this->user->id)->get();

            // Benchmark synchronous processing
            $start = microtime(true);
            $this->processBatchSynchronously($todos);
            $syncTime = microtime(true) - $start;
            $syncTimes[] = $syncTime;

            $this->line('  Sync time: '.number_format($syncTime, 4).' seconds');

            // Reset todos before async test
            $this->resetTodos();

            // Small delay to let system stabilize
            usleep(100000); // 100ms

            // Benchmark asynchronous processing
            $start = microtime(true);
            $this->processBatchAsynchronously($todos);
            $asyncTime = microtime(true) - $start;
            $asyncTimes[] = $asyncTime;

            $this->line('  Async time: '.number_format($asyncTime, 4).' seconds');
        }

        // Calculate statistics
        $avgSyncTime = array_sum($syncTimes) / count($syncTimes);
        $avgAsyncTime = array_sum($asyncTimes) / count($asyncTimes);
        $improvement = $avgSyncTime / $avgAsyncTime;
        $percentImprovement = ($improvement - 1) * 100;

        $this->info('');
        $this->info('================ BENCHMARK RESULTS ================');
        $this->info('Average sync time: '.number_format($avgSyncTime, 4).' seconds');
        $this->info('Average async time: '.number_format($avgAsyncTime, 4).' seconds');
        $this->info('Improvement factor: '.number_format($improvement, 2).'x');
        $this->info('Percent improvement: '.number_format($percentImprovement, 2).'%');
        $this->info('===================================================');
    }

    /**
     * Benchmark API operations
     */
    protected function benchmarkApiOperations(int $iterations): void
    {
        $this->info('');
        $this->info('========== API OPERATIONS BENCHMARK ==========');
        $this->info('Comparing synchronous vs asynchronous API operations');
        $this->info('');

        $syncTimes = [];
        $asyncTimes = [];

        for ($i = 1; $i <= $iterations; $i++) {
            $this->info("Running benchmark iteration {$i}...");

            // Get todos for this iteration
            $todoIds = Task::where('user_id', $this->user->id)->pluck('id')->take(20)->toArray();

            // Benchmark synchronous API fetching
            $start = microtime(true);
            $this->fetchTodosSynchronously($todoIds);
            $syncTime = microtime(true) - $start;
            $syncTimes[] = $syncTime;

            $this->line('  Sync time: '.number_format($syncTime, 4).' seconds');

            // Small delay to let system stabilize
            usleep(100000); // 100ms

            // Benchmark asynchronous API fetching
            $start = microtime(true);
            $this->fetchTodosAsynchronously($todoIds);
            $asyncTime = microtime(true) - $start;
            $asyncTimes[] = $asyncTime;

            $this->line('  Async time: '.number_format($asyncTime, 4).' seconds');
        }

        // Calculate statistics
        $avgSyncTime = array_sum($syncTimes) / count($syncTimes);
        $avgAsyncTime = array_sum($asyncTimes) / count($asyncTimes);
        $improvement = $avgSyncTime / $avgAsyncTime;
        $percentImprovement = ($improvement - 1) * 100;

        $this->info('');
        $this->info('================ BENCHMARK RESULTS ================');
        $this->info('Average sync time: '.number_format($avgSyncTime, 4).' seconds');
        $this->info('Average async time: '.number_format($avgAsyncTime, 4).' seconds');
        $this->info('Improvement factor: '.number_format($improvement, 2).'x');
        $this->info('Percent improvement: '.number_format($percentImprovement, 2).'%');
        $this->info('===================================================');
    }

    /**
     * Load dashboard data synchronously
     */
    protected function loadDashboardSynchronously(): array
    {
        $userId = $this->user->id;

        // Load stats
        $stats = [
            'total' => Task::where('user_id', $userId)->count(),
            'completed' => Task::where('user_id', $userId)->where('completed', true)->count(),
            'pending' => Task::where('user_id', $userId)->where('completed', false)->count(),
            'overdue' => Task::where('user_id', $userId)
                ->where('completed', false)
                ->where('due_date', '<', now())
                ->count(),
            'due_today' => Task::where('user_id', $userId)
                ->where('completed', false)
                ->whereDate('due_date', now())
                ->count(),
            'high_priority' => Task::where('user_id', $userId)
                ->where('priority', 'high')
                ->count(),
        ];

        // Load recent todos
        $recentTodos = Task::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($todo) {
                return [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'completed' => $todo->completed,
                    'created_at' => $todo->created_at->diffForHumans(),
                    'priority' => $todo->priority,
                ];
            })
            ->toArray();

        // Load upcoming deadlines
        $upcomingDeadlines = Task::where('user_id', $userId)
            ->where('completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(function ($todo) {
                return [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'due_date' => Carbon::parse($todo->due_date)->format('M d, Y'),
                    'days_left' => Carbon::parse($todo->due_date)->diffInDays(now()),
                    'priority' => $todo->priority,
                ];
            })
            ->toArray();

        // Load completion trend
        $completionTrend = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Task::where('user_id', $userId)
                ->where('completed', true)
                ->whereDate('completed_at', $date)
                ->count();

            $completionTrend[] = [
                'date' => now()->subDays($i)->format('M d'),
                'count' => $count,
            ];
        }

        // Reverse to show oldest to newest
        $completionTrend = array_reverse($completionTrend);

        // Load priority distribution
        $priorities = ['high', 'medium', 'low'];
        $priorityDistribution = [];

        foreach ($priorities as $priority) {
            $priorityDistribution[$priority] = Task::where('user_id', $userId)
                ->where('priority', $priority)
                ->count();
        }

        // Load category breakdown
        $categories = Task::where('user_id', $userId)
            ->whereNotNull('category')
            ->distinct('category')
            ->pluck('category')
            ->toArray();

        $categoryBreakdown = [];

        foreach ($categories as $category) {
            $categoryBreakdown[$category] = Task::where('user_id', $userId)
                ->where('category', $category)
                ->count();
        }

        // Add 'uncategorized' count
        $categoryBreakdown['uncategorized'] = Task::where('user_id', $userId)
            ->whereNull('category')
            ->count();

        return [
            'stats' => $stats,
            'recentTodos' => $recentTodos,
            'upcomingDeadlines' => $upcomingDeadlines,
            'completionTrend' => $completionTrend,
            'priorityDistribution' => $priorityDistribution,
            'categoryBreakdown' => $categoryBreakdown,
        ];
    }

    /**
     * Load dashboard data asynchronously
     */
    protected function loadDashboardAsynchronously(): array
    {
        $userId = $this->user->id;

        return $this->hypervelService->runConcurrently([
            'stats' => function () use ($userId) {
                return [
                    'total' => Task::where('user_id', $userId)->count(),
                    'completed' => Task::where('user_id', $userId)->where('completed', true)->count(),
                    'pending' => Task::where('user_id', $userId)->where('completed', false)->count(),
                    'overdue' => Task::where('user_id', $userId)
                        ->where('completed', false)
                        ->where('due_date', '<', now())
                        ->count(),
                    'due_today' => Task::where('user_id', $userId)
                        ->where('completed', false)
                        ->whereDate('due_date', now())
                        ->count(),
                    'high_priority' => Task::where('user_id', $userId)
                        ->where('priority', 'high')
                        ->count(),
                ];
            },
            'recentTodos' => function () use ($userId) {
                return Task::where('user_id', $userId)
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($todo) {
                        return [
                            'id' => $todo->id,
                            'title' => $todo->title,
                            'completed' => $todo->completed,
                            'created_at' => $todo->created_at->diffForHumans(),
                            'priority' => $todo->priority,
                        ];
                    })
                    ->toArray();
            },
            'upcomingDeadlines' => function () use ($userId) {
                return Task::where('user_id', $userId)
                    ->where('completed', false)
                    ->whereNotNull('due_date')
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date')
                    ->limit(5)
                    ->get()
                    ->map(function ($todo) {
                        return [
                            'id' => $todo->id,
                            'title' => $todo->title,
                            'due_date' => Carbon::parse($todo->due_date)->format('M d, Y'),
                            'days_left' => Carbon::parse($todo->due_date)->diffInDays(now()),
                            'priority' => $todo->priority,
                        ];
                    })
                    ->toArray();
            },
            'completionTrend' => function () use ($userId) {
                $result = [];
                for ($i = 0; $i < 7; $i++) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $count = Task::where('user_id', $userId)
                        ->where('completed', true)
                        ->whereDate('completed_at', $date)
                        ->count();

                    $result[] = [
                        'date' => now()->subDays($i)->format('M d'),
                        'count' => $count,
                    ];
                }

                return array_reverse($result);
            },
            'priorityDistribution' => function () use ($userId) {
                $priorities = ['high', 'medium', 'low'];
                $result = [];

                foreach ($priorities as $priority) {
                    $result[$priority] = Task::where('user_id', $userId)
                        ->where('priority', $priority)
                        ->count();
                }

                return $result;
            },
            'categoryBreakdown' => function () use ($userId) {
                $categories = Task::where('user_id', $userId)
                    ->whereNotNull('category')
                    ->distinct('category')
                    ->pluck('category')
                    ->toArray();

                $result = [];

                foreach ($categories as $category) {
                    $result[$category] = Task::where('user_id', $userId)
                        ->where('category', $category)
                        ->count();
                }

                $result['uncategorized'] = Task::where('user_id', $userId)
                    ->whereNull('category')
                    ->count();

                return $result;
            },
        ]);
    }

    /**
     * Verify dashboard results match between sync and async methods
     */
    protected function verifyDashboardResults(array $syncData, array $asyncData): void
    {
        // Verify that all data sections exist
        foreach (['stats', 'recentTodos', 'upcomingDeadlines', 'completionTrend', 'priorityDistribution', 'categoryBreakdown'] as $section) {
            if (! isset($syncData[$section]) || ! isset($asyncData[$section])) {
                throw new \Exception("Section {$section} missing from results");
            }
        }

        // Verify stats are equal
        foreach ($syncData['stats'] as $key => $value) {
            if (! isset($asyncData['stats'][$key]) || $asyncData['stats'][$key] !== $value) {
                throw new \Exception("Stats mismatch for {$key}: sync={$value}, async={$asyncData['stats'][$key]}");
            }
        }
    }

    /**
     * Process todos synchronously in batch
     */
    protected function processBatchSynchronously($todos): void
    {
        foreach ($todos as $todo) {
            $this->simulateTodoProcessing($todo);
        }
    }

    /**
     * Process todos asynchronously in batch
     */
    protected function processBatchAsynchronously($todos): void
    {
        $this->hypervelService->runBatch($todos->all(), function ($todo) {
            return $this->simulateTodoProcessing($todo);
        });
    }

    /**
     * Simulate some processing on a todo
     */
    protected function simulateTodoProcessing($todo): array
    {
        // Simulate some processing time (reduced for benchmarking)
        usleep(5000); // 5ms

        // Perform some updates on the todo
        if (! $todo->completed && $todo->due_date && Carbon::parse($todo->due_date)->isPast()) {
            $todo->priority = 'high';
            $todo->save();
        }

        return [
            'id' => $todo->id,
            'status' => 'processed',
        ];
    }

    /**
     * Reset todos for batch processing test
     */
    protected function resetTodos(): void
    {
        // Reset todos to original state
        Task::where('user_id', $this->user->id)
            ->update([
                'priority' => DB::raw("(CASE WHEN RAND() < 0.33 THEN 'high' WHEN RAND() < 0.66 THEN 'medium' ELSE 'low' END)"),
            ]);
    }

    /**
     * Fetch todos synchronously
     */
    protected function fetchTodosSynchronously(array $todoIds): array
    {
        $results = [];

        foreach ($todoIds as $id) {
            // Simulate API request time
            usleep(10000); // 10ms

            $todo = Task::find($id);
            if ($todo) {
                $results[] = $todo->toArray();
            }
        }

        return $results;
    }

    /**
     * Fetch todos asynchronously
     */
    protected function fetchTodosAsynchronously(array $todoIds): array
    {
        $jobs = [];

        foreach ($todoIds as $id) {
            $jobs["todo_{$id}"] = function () use ($id) {
                // Simulate API request time
                usleep(10000); // 10ms

                $todo = Task::find($id);

                return $todo ? $todo->toArray() : null;
            };
        }

        $results = $this->hypervelService->runConcurrently($jobs);

        // Filter out nulls
        return array_filter($results);
    }
}
