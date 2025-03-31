<?php

namespace Tests\Feature\Hypervel;

use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Hypervel\Facades\Hypervel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HypervelPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected HypervelService $hypervelService;
    protected int $taskCount = 50;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user and tasks
        $this->user = User::factory()->create();
        Task::factory()->count($this->taskCount)->create([
            'user_id' => $this->user->id,
        ]);

        $this->hypervelService = app(HypervelService::class);
    }

    /** @test */
    public function it_loads_dashboard_data_faster_with_hypervel()
    {
        // Skip in CI environments
        if (env('CI', false)) {
            $this->markTestSkipped('Performance tests are skipped in CI environments');
        }

        // Test synchronous loading
        $startSync = microtime(true);
        $syncData = $this->loadDashboardDataSynchronously();
        $syncTime = microtime(true) - $startSync;

        // Test asynchronous loading with Hypervel
        $startAsync = microtime(true);
        $asyncData = $this->loadDashboardDataAsynchronously();
        $asyncTime = microtime(true) - $startAsync;

        // Compare performance
        $speedup = $syncTime / $asyncTime;
        
        // Verify data integrity
        $this->assertEquals(count($syncData['stats']), count($asyncData['stats']));
        $this->assertEquals($syncData['stats']['total'], $asyncData['stats']['total']);
        
        // Assert performance improvement
        $this->assertGreaterThan(1.5, $speedup, "Hypervel should provide at least 1.5x speedup");
        
        // Output results
        fwrite(STDERR, sprintf(
            "\nDashboard loading: Sync: %.4fs, Async: %.4fs, Speedup: %.2fx\n",
            $syncTime,
            $asyncTime,
            $speedup
        ));
    }

    /** @test */
    public function it_processes_batch_operations_faster_with_hypervel()
    {
        // Skip in CI environments
        if (env('CI', false)) {
            $this->markTestSkipped('Performance tests are skipped in CI environments');
        }

        $tasks = Task::where('user_id', $this->user->id)->get();

        // Test synchronous processing
        $startSync = microtime(true);
        foreach ($tasks as $task) {
            $this->processTaskSync($task);
        }
        $syncTime = microtime(true) - $startSync;

        // Reset tasks
        Task::where('user_id', $this->user->id)->update(['completed' => false]);

        // Test asynchronous processing with Hypervel
        $startAsync = microtime(true);
        $this->hypervelService->runBatch($tasks->all(), function($task) {
            return $this->processTaskSync($task, true);
        });
        $asyncTime = microtime(true) - $startAsync;

        // Compare performance
        $speedup = $syncTime / $asyncTime;
        
        // Assert performance improvement
        $this->assertGreaterThan(1.5, $speedup, "Hypervel should provide at least 1.5x speedup for batch operations");
        
        // Output results
        fwrite(STDERR, sprintf(
            "\nBatch processing: Sync: %.4fs, Async: %.4fs, Speedup: %.2fx\n",
            $syncTime,
            $asyncTime,
            $speedup
        ));
    }

    /** @test */
    public function it_handles_errors_properly_in_concurrent_operations()
    {
        // Create operations that will succeed and fail
        $operations = [
            'success1' => fn() => 'success1',
            'success2' => fn() => 'success2',
            'error' => function() {
                throw new \Exception('Test exception');
                return 'never reached';
            },
            'success3' => fn() => 'success3',
        ];

        // Run operations and catch the exception
        try {
            $this->hypervelService->runConcurrently($operations);
            $this->fail('Exception should have been thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Test exception', $e->getMessage());
            
            // Verify that Hypervel handled the error properly
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /** @test */
    public function it_runs_with_retry_logic_for_flaky_operations()
    {
        // Skip if configuration doesn't allow retries
        if (config('hypervel.default_retries', 3) < 2) {
            $this->markTestSkipped('Retry test requires at least 2 retries in configuration');
        }
        
        // Counter to track retry attempts
        $attempts = 0;
        
        // Create a flaky operation that fails on first attempt
        $result = $this->hypervelService->runWithRetry(function() use (&$attempts) {
            $attempts++;
            if ($attempts === 1) {
                throw new \Exception('First attempt failed');
            }
            return 'success after retry';
        });
        
        // Verify retry worked
        $this->assertEquals('success after retry', $result);
        $this->assertEquals(2, $attempts, 'Operation should have been retried once');
    }

    /** @test */
    public function it_respects_concurrency_limits()
    {
        // Get current concurrency limit
        $originalLimit = Hypervel::getConcurrencyLimit() ?? 25;
        
        // Set a very low limit for testing
        Hypervel::setConcurrencyLimit(2);
        
        // Create more operations than the limit allows
        $operations = [];
        for ($i = 0; $i < 5; $i++) {
            $operations["op{$i}"] = function() use ($i) {
                // Add a small delay to simulate work
                usleep(10000);
                return "result{$i}";
            };
        }
        
        // Run operations and verify we don't hit concurrency issues
        $results = $this->hypervelService->runConcurrently($operations);
        
        // Verify all operations completed
        $this->assertCount(5, $results);
        $this->assertEquals('result0', $results['op0']);
        $this->assertEquals('result4', $results['op4']);
        
        // Restore original limit
        Hypervel::setConcurrencyLimit($originalLimit);
    }

    /**
     * Load dashboard data synchronously
     */
    private function loadDashboardDataSynchronously(): array
    {
        $userId = $this->user->id;
        
        $stats = [
            'total' => Task::where('user_id', $userId)->count(),
            'completed' => Task::where('user_id', $userId)->where('completed', true)->count(),
            'pending' => Task::where('user_id', $userId)->where('completed', false)->count(),
        ];
        
        $recentTasks = Task::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->toArray();
            
        $upcomingDeadlines = Task::where('user_id', $userId)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->toArray();
            
        return [
            'stats' => $stats,
            'recentTasks' => $recentTasks,
            'upcomingDeadlines' => $upcomingDeadlines,
        ];
    }
    
    /**
     * Load dashboard data asynchronously
     */
    private function loadDashboardDataAsynchronously(): array
    {
        $userId = $this->user->id;
        
        return $this->hypervelService->runConcurrently([
            'stats' => function() use ($userId) {
                return [
                    'total' => Task::where('user_id', $userId)->count(),
                    'completed' => Task::where('user_id', $userId)->where('completed', true)->count(),
                    'pending' => Task::where('user_id', $userId)->where('completed', false)->count(),
                ];
            },
            'recentTasks' => function() use ($userId) {
                return Task::where('user_id', $userId)
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->toArray();
            },
            'upcomingDeadlines' => function() use ($userId) {
                return Task::where('user_id', $userId)
                    ->whereNotNull('due_date')
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date')
                    ->limit(5)
                    ->get()
                    ->toArray();
            }
        ]);
    }
    
    /**
     * Process a task synchronously
     */
    private function processTaskSync(Task $task, bool $isAsync = false): array
    {
        // Simulate complex processing
        usleep(10000); // 10ms delay
        
        // Update the task
        $task->completed = true;
        $task->save();
        
        // Simulate more processing
        usleep(5000); // 5ms delay
        
        // Return the processed result
        return [
            'id' => $task->id,
            'title' => $task->title,
            'completed' => $task->completed,
            'processed_async' => $isAsync,
        ];
    }
} 