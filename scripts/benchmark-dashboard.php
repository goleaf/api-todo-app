<?php

require __DIR__.'/../vendor/autoload.php';

use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

/**
 * Benchmark dashboard performance
 *
 * This script compares synchronous vs asynchronous data loading for the dashboard
 */
class DashboardBenchmark
{
    protected User $user;

    protected int $iterations = 5;

    protected int $taskCount = 100;

    protected HypervelService $hypervelService;

    public function __construct()
    {
        $this->hypervelService = app(HypervelService::class);

        echo "Setting up benchmark environment...\n";
        $this->setupEnvironment();
    }

    protected function setupEnvironment(): void
    {
        // Use in-memory database for tests
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        DB::purge();

        // Migrate database
        echo "Running migrations...\n";
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh');

        // Create user with tasks
        echo "Creating test data...\n";
        $this->user = User::factory()->create([
            'name' => 'Benchmark User',
            'email' => 'benchmark@example.com',
        ]);

        // Create tasks with various attributes
        $priorities = ['low', 'medium', 'high'];
        $categories = ['work', 'personal', 'errands', 'shopping', 'health'];
        $completed = [true, false];

        echo "Creating {$this->taskCount} tasks...\n";

        for ($i = 0; $i < $this->taskCount; $i++) {
            $isCompleted = $completed[array_rand($completed)];

            Task::create([
                'user_id' => $this->user->id,
                'title' => "Benchmark Task #{$i}",
                'description' => "Description for benchmark task #{$i}",
                'priority' => $priorities[array_rand($priorities)],
                'category' => $categories[array_rand($categories)],
                'completed' => $isCompleted,
                'completed_at' => $isCompleted ? Carbon::now()->subHours(rand(1, 100)) : null,
                'due_date' => rand(0, 1) ? Carbon::now()->addDays(rand(-5, 10)) : null,
            ]);
        }

        echo "Setup complete.\n";
    }

    public function runBenchmark(): void
    {
        echo "\n========== DASHBOARD LOADING BENCHMARK ==========\n";
        echo "Comparing synchronous vs asynchronous data loading\n";
        echo "Iterations: {$this->iterations}\n";
        echo "Task count: {$this->taskCount}\n";
        echo "================================================\n\n";

        $syncTimes = [];
        $asyncTimes = [];

        for ($i = 1; $i <= $this->iterations; $i++) {
            echo "Running benchmark iteration {$i}...\n";

            // Benchmark synchronous loading
            $start = microtime(true);
            $syncData = $this->loadSynchronously();
            $syncTime = microtime(true) - $start;
            $syncTimes[] = $syncTime;

            echo '  Sync time: '.number_format($syncTime, 4)." seconds\n";

            // Small delay to let system stabilize
            usleep(100000); // 100ms

            // Benchmark asynchronous loading
            $start = microtime(true);
            $asyncData = $this->loadAsynchronously();
            $asyncTime = microtime(true) - $start;
            $asyncTimes[] = $asyncTime;

            echo '  Async time: '.number_format($asyncTime, 4)." seconds\n";

            // Verify data integrity
            $this->verifyResults($syncData, $asyncData);

            echo "  Data integrity verified ✓\n";
        }

        // Calculate statistics
        $avgSyncTime = array_sum($syncTimes) / count($syncTimes);
        $avgAsyncTime = array_sum($asyncTimes) / count($asyncTimes);
        $improvement = $avgSyncTime / $avgAsyncTime;
        $percentImprovement = ($improvement - 1) * 100;

        echo "\n================ BENCHMARK RESULTS ================\n";
        echo 'Average sync time: '.number_format($avgSyncTime, 4)." seconds\n";
        echo 'Average async time: '.number_format($avgAsyncTime, 4)." seconds\n";
        echo 'Improvement factor: '.number_format($improvement, 2)."x\n";
        echo 'Percent improvement: '.number_format($percentImprovement, 2)."%\n";
        echo "===================================================\n";
    }

    protected function loadSynchronously(): array
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

        // Load recent tasks
        $recentTasks = Task::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'completed' => $task->completed,
                    'created_at' => $task->created_at->diffForHumans(),
                    'priority' => $task->priority,
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
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => Carbon::parse($task->due_date)->format('M d, Y'),
                    'days_left' => Carbon::parse($task->due_date)->diffInDays(now()),
                    'priority' => $task->priority,
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
            'recentTasks' => $recentTasks,
            'upcomingDeadlines' => $upcomingDeadlines,
            'completionTrend' => $completionTrend,
            'priorityDistribution' => $priorityDistribution,
            'categoryBreakdown' => $categoryBreakdown,
        ];
    }

    protected function loadAsynchronously(): array
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
            'recentTasks' => function () use ($userId) {
                return Task::where('user_id', $userId)
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'completed' => $task->completed,
                            'created_at' => $task->created_at->diffForHumans(),
                            'priority' => $task->priority,
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
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'due_date' => Carbon::parse($task->due_date)->format('M d, Y'),
                            'days_left' => Carbon::parse($task->due_date)->diffInDays(now()),
                            'priority' => $task->priority,
                        ];
                    })
                    ->toArray();
            },
            'completionTrend' => function () use ($userId) {
                $days = 7;
                $result = [];

                for ($i = 0; $i < $days; $i++) {
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

                // Reverse to show oldest to newest
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

                // Add 'uncategorized' count
                $result['uncategorized'] = Task::where('user_id', $userId)
                    ->whereNull('category')
                    ->count();

                return $result;
            },
        ]);
    }

    protected function verifyResults(array $syncData, array $asyncData): void
    {
        // Verify that all data sections exist
        foreach (['stats', 'recentTasks', 'upcomingDeadlines', 'completionTrend', 'priorityDistribution', 'categoryBreakdown'] as $section) {
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

        // Verify record counts match
        $sections = [
            'recentTasks',
            'upcomingDeadlines',
            'completionTrend',
        ];

        foreach ($sections as $section) {
            if (count($syncData[$section]) !== count($asyncData[$section])) {
                throw new \Exception("{$section} count mismatch: sync=".count($syncData[$section]).', async='.count($asyncData[$section]));
            }
        }

        // Verify priority distributions match
        foreach ($syncData['priorityDistribution'] as $priority => $count) {
            if (! isset($asyncData['priorityDistribution'][$priority]) || $asyncData['priorityDistribution'][$priority] !== $count) {
                throw new \Exception("Priority distribution mismatch for {$priority}: sync={$count}, async={$asyncData['priorityDistribution'][$priority]}");
            }
        }

        // Verify category breakdowns match
        foreach ($syncData['categoryBreakdown'] as $category => $count) {
            if (! isset($asyncData['categoryBreakdown'][$category]) || $asyncData['categoryBreakdown'][$category] !== $count) {
                throw new \Exception("Category breakdown mismatch for {$category}: sync={$count}, async={$asyncData['categoryBreakdown'][$category]}");
            }
        }
    }
}

// Run the benchmark
$benchmark = new DashboardBenchmark;
$benchmark->runBenchmark();
