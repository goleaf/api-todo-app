<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class HypervelBenchmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hypervel:benchmark
                            {--todos=50 : Number of todos to use in benchmark}
                            {--iterations=5 : Number of iterations to run for each test}
                            {--feature=all : Specific feature to benchmark (dashboard, batch, api, all)}
                            {--delay=100 : Artificial delay in milliseconds to simulate I/O operations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run performance tests to measure Hypervel improvements';

    /**
     * The Hypervel service.
     *
     * @var \App\Services\HypervelService
     */
    protected $hypervelService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(HypervelService $hypervelService)
    {
        parent::__construct();
        $this->hypervelService = $hypervelService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $numTodos = (int) $this->option('todos');
        $iterations = (int) $this->option('iterations');
        $feature = $this->option('feature');
        $delay = (int) $this->option('delay');

        $this->info("Running Hypervel benchmark with {$numTodos} todos, {$iterations} iterations, and {$delay}ms simulated delay");

        // Create test data
        $this->info('Preparing test data...');
        $user = $this->prepareTestData($numTodos);

        // Run benchmarks
        $results = [];

        if ($feature === 'all' || $feature === 'dashboard') {
            $this->info('Benchmarking dashboard data loading...');
            $dashboardResults = $this->benchmarkDashboardLoading($user, $iterations, $delay);
            $results['Dashboard Loading'] = $dashboardResults;
        }

        if ($feature === 'all' || $feature === 'batch') {
            $this->info('Benchmarking batch processing...');
            $batchResults = $this->benchmarkBatchProcessing($user, $iterations, $delay);
            $results['Batch Processing'] = $batchResults;
        }

        if ($feature === 'all' || $feature === 'api') {
            $this->info('Benchmarking API requests...');
            $apiResults = $this->benchmarkApiRequests($iterations, $delay);
            $results['API Requests'] = $apiResults;
        }

        if ($feature === 'all' || $feature === 'todomvc') {
            $this->info('Benchmarking TodoMVC...');
            $todomvcResults = $this->benchmarkTodoMvc();
            $results['TodoMVC'] = $todomvcResults;
        }

        // Display results
        $this->displayResults($results);

        // Clean up test data
        $this->info('Cleaning up test data...');
        Task::where('user_id', $user->id)->delete();
        $user->delete();

        return 0;
    }

    /**
     * Prepare test data for benchmarking.
     *
     * @return \App\Models\User
     */
    protected function prepareTestData(int $numTodos)
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Benchmark Test User',
            'email' => 'benchmark_'.Str::random(8).'@example.com',
        ]);

        // Create todos for the user
        Task::factory()->count($numTodos)->create([
            'user_id' => $user->id,
            'status' => collect(['pending', 'in_progress', 'completed', 'cancelled'])->random(),
        ]);

        return $user;
    }

    /**
     * Benchmark dashboard data loading.
     *
     * @return array
     */
    protected function benchmarkDashboardLoading(User $user, int $iterations, int $delay)
    {
        $regularTimes = [];
        $hypervelTimes = [];

        for ($i = 0; $i < $iterations; $i++) {
            // Regular sequential loading
            $start = microtime(true);

            // Simulate loading different dashboard components sequentially
            $this->simulateDataFetch('task-stats', $delay);
            $this->simulateDataFetch('recent-activity', $delay);
            $this->simulateDataFetch('calendar-events', $delay);
            $this->simulateDataFetch('notifications', $delay);

            $regularTimes[] = (microtime(true) - $start) * 1000; // Convert to ms

            // Hypervel concurrent loading
            $start = microtime(true);

            $operations = [
                'task-stats' => fn () => $this->simulateDataFetch('task-stats', $delay),
                'recent-activity' => fn () => $this->simulateDataFetch('recent-activity', $delay),
                'calendar-events' => fn () => $this->simulateDataFetch('calendar-events', $delay),
                'notifications' => fn () => $this->simulateDataFetch('notifications', $delay),
            ];

            $this->hypervelService->runConcurrently($operations);

            $hypervelTimes[] = (microtime(true) - $start) * 1000; // Convert to ms
        }

        return [
            'regular' => $regularTimes,
            'hypervel' => $hypervelTimes,
        ];
    }

    /**
     * Benchmark batch processing.
     *
     * @return array
     */
    protected function benchmarkBatchProcessing(User $user, int $iterations, int $delay)
    {
        $regularTimes = [];
        $hypervelTimes = [];

        for ($i = 0; $i < $iterations; $i++) {
            $tasks = Task::where('user_id', $user->id)->limit(20)->get();

            // Regular sequential processing
            $start = microtime(true);

            foreach ($tasks as $task) {
                $this->simulateProcessTask($task, $delay);
            }

            $regularTimes[] = (microtime(true) - $start) * 1000; // Convert to ms

            // Hypervel batch processing
            $start = microtime(true);

            $this->hypervelService->runBatch($tasks, fn ($task) => $this->simulateProcessTask($task, $delay), 5);

            $hypervelTimes[] = (microtime(true) - $start) * 1000; // Convert to ms
        }

        return [
            'regular' => $regularTimes,
            'hypervel' => $hypervelTimes,
        ];
    }

    /**
     * Benchmark API requests.
     *
     * @return array
     */
    protected function benchmarkApiRequests(int $iterations, int $delay)
    {
        $regularTimes = [];
        $hypervelTimes = [];

        $urls = [
            'todos' => '/api/mock/todos',
            'users' => '/api/mock/users',
            'stats' => '/api/mock/stats',
            'notifications' => '/api/mock/notifications',
            'settings' => '/api/mock/settings',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Regular sequential API requests
            $start = microtime(true);

            foreach ($urls as $key => $url) {
                $this->simulateApiRequest($url, $delay);
            }

            $regularTimes[] = (microtime(true) - $start) * 1000; // Convert to ms

            // Hypervel concurrent API requests
            $start = microtime(true);

            $this->hypervelService->runConcurrentHttpRequests($urls, $delay);

            $hypervelTimes[] = (microtime(true) - $start) * 1000; // Convert to ms
        }

        return [
            'regular' => $regularTimes,
            'hypervel' => $hypervelTimes,
        ];
    }

    /**
     * Simulate fetching data with an artificial delay.
     *
     * @return array
     */
    protected function simulateDataFetch(string $component, int $delay)
    {
        usleep($delay * 1000); // Convert to microseconds

        return [
            'component' => $component,
            'timestamp' => now()->toDateTimeString(),
            'data' => ['sample' => 'data'],
        ];
    }

    /**
     * Simulate processing a task with an artificial delay.
     *
     * @return \App\Models\Task
     */
    protected function simulateProcessTask(Task $task, int $delay)
    {
        usleep($delay * 1000); // Convert to microseconds

        // No actual changes, just simulating processing
        return $task;
    }

    /**
     * Simulate making an API request with an artificial delay.
     *
     * @return array
     */
    protected function simulateApiRequest(string $url, int $delay)
    {
        usleep($delay * 1000); // Convert to microseconds

        return [
            'url' => $url,
            'status' => 200,
            'data' => ['sample' => 'data'],
        ];
    }

    /**
     * Display benchmark results.
     *
     * @return void
     */
    protected function displayResults(array $results)
    {
        $this->info("\nBenchmark Results:");
        $headers = ['Feature', 'Regular (ms)', 'Hypervel (ms)', 'Improvement', 'Recommendation'];
        $rows = [];

        foreach ($results as $feature => $data) {
            $regularStats = $this->calculateStats($data['regular']);
            $hypervelStats = $this->calculateStats($data['hypervel']);

            $improvement = round((1 - ($hypervelStats['avg'] / $regularStats['avg'])) * 100, 2);

            $recommendation = match (true) {
                $improvement >= 50 => '<fg=green>Strongly Recommended</>',
                $improvement >= 30 => '<fg=green>Recommended</>',
                $improvement >= 10 => '<fg=yellow>Consider Using</>',
                default => '<fg=red>Minimal Benefit</>'
            };

            $rows[] = [
                $feature,
                "{$regularStats['avg']} (±{$regularStats['std']})",
                "{$hypervelStats['avg']} (±{$hypervelStats['std']})",
                "{$improvement}%",
                $recommendation,
            ];
        }

        $this->table($headers, $rows);

        // Overall recommendation
        $this->info("\nOverall Recommendations:");

        $totalRegular = array_reduce($results, function ($carry, $item) {
            return $carry + array_sum($item['regular']) / count($item['regular']);
        }, 0);

        $totalHypervel = array_reduce($results, function ($carry, $item) {
            return $carry + array_sum($item['hypervel']) / count($item['hypervel']);
        }, 0);

        $totalImprovement = round((1 - ($totalHypervel / $totalRegular)) * 100, 2);

        if ($totalImprovement >= 40) {
            $this->comment("Hypervel provides significant performance improvements ({$totalImprovement}%). Recommended for production use.");
        } elseif ($totalImprovement >= 20) {
            $this->comment("Hypervel provides good performance improvements ({$totalImprovement}%). Consider using in I/O-bound operations.");
        } else {
            $this->comment("Hypervel provides limited performance improvements ({$totalImprovement}%). Consider using only for specific use cases.");
        }
    }

    /**
     * Calculate statistics for timing results.
     *
     * @return array
     */
    protected function calculateStats(array $times)
    {
        $count = count($times);

        if ($count === 0) {
            return ['avg' => 0, 'min' => 0, 'max' => 0, 'std' => 0];
        }

        $avg = array_sum($times) / $count;
        $min = min($times);
        $max = max($times);

        // Calculate standard deviation
        $sumSquaredDiff = array_reduce($times, function ($carry, $time) use ($avg) {
            return $carry + pow($time - $avg, 2);
        }, 0);

        $std = round(sqrt($sumSquaredDiff / $count), 2);

        return [
            'avg' => round($avg, 2),
            'min' => round($min, 2),
            'max' => round($max, 2),
            'std' => $std,
        ];
    }

    protected function benchmarkTodoMvc()
    {
        $this->logInfo('Running TodoMVC benchmark...');

        // Create test user and todos for benchmark
        $user = User::factory()->create();

        // Create a large number of todos for testing
        $todoCount = $this->option('todos');
        $this->logInfo("Creating {$todoCount} todos for benchmark...");

        Task::factory()->count($todoCount)->create([
            'user_id' => $user->id,
        ]);

        $iterations = $this->option('iterations');
        $this->logInfo("Running benchmark with {$iterations} iterations...");

        // Benchmark clearCompleted
        $regularTimes = [];
        $hypervelTimes = [];

        // Regular approach
        for ($i = 0; $i < $iterations; $i++) {
            // Make half of the todos completed for the test
            Task::where('user_id', $user->id)
                ->update(['status' => ($i % 2 === 0 ? 'completed' : 'pending')]);

            $startTime = microtime(true);

            // Traditional approach
            Task::where('user_id', $user->id)
                ->where('status', 'completed')
                ->delete();

            $endTime = microtime(true);
            $regularTimes[] = ($endTime - $startTime) * 1000; // Convert to ms
        }

        // Hypervel approach
        for ($i = 0; $i < $iterations; $i++) {
            // Make half of the todos completed for the test
            Task::where('user_id', $user->id)
                ->update(['status' => ($i % 2 === 0 ? 'completed' : 'pending')]);

            $startTime = microtime(true);

            // Hypervel approach
            $tasks = Task::where('user_id', $user->id)
                ->where('status', 'completed')
                ->get();

            $hypervel = app(HypervelService::class);
            $hypervel->runBatch($tasks, function ($task) {
                $task->delete();
            });

            $endTime = microtime(true);
            $hypervelTimes[] = ($endTime - $startTime) * 1000; // Convert to ms
        }

        // Calculate average times
        $regularAvg = $this->calculateAverage($regularTimes);
        $hypervelAvg = $this->calculateAverage($hypervelTimes);
        $improvement = $this->calculateImprovement($regularAvg, $hypervelAvg);

        // Add results to the table
        $this->results[] = [
            'feature' => 'TodoMVC Clear Completed',
            'regular' => $this->formatTime($regularAvg, $this->calculateStdDev($regularTimes)),
            'hypervel' => $this->formatTime($hypervelAvg, $this->calculateStdDev($hypervelTimes)),
            'improvement' => $this->formatImprovement($improvement),
            'recommendation' => $this->getRecommendation($improvement),
        ];

        // Cleanup
        Task::where('user_id', $user->id)->delete();
        $user->delete();
    }

    protected function getFeaturesToBenchmark()
    {
        $feature = $this->option('feature');

        if ($feature === 'all') {
            return ['dashboard', 'batch', 'api', 'todomvc'];
        }

        if (in_array($feature, ['dashboard', 'batch', 'api', 'todomvc'])) {
            return [$feature];
        }

        return ['dashboard', 'batch', 'api', 'todomvc'];
    }

    protected function runBenchmarks()
    {
        foreach ($this->getFeaturesToBenchmark() as $feature) {
            switch ($feature) {
                case 'dashboard':
                    $this->benchmarkDashboard();
                    break;
                case 'batch':
                    $this->benchmarkBatch();
                    break;
                case 'api':
                    $this->benchmarkApi();
                    break;
                case 'todomvc':
                    $this->benchmarkTodoMvc();
                    break;
            }
        }
    }
}
