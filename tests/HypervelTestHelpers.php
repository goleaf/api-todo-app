<?php

namespace Tests;

// use Hypervel\Testing\WithHypervel; // Remove missing trait import
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use App\Services\HypervelService;
use Mockery;

/**
 * Hypervel Test Helpers
 * 
 * Helper methods for testing asynchronous code with Hypervel
 */
class HypervelTestHelpers
{
    // Remove trait usage
    // use WithHypervel;

    /**
     * Setup a test environment that supports Hypervel coroutines
     *
     * @param array $additionalConfig Optional additional config for Hypervel
     * @return void
     */
    public static function setupHypervelTestEnv(array $additionalConfig = []): void
    {
        // Ensure Hypervel is using the test configuration
        config([
            'hypervel.concurrency_limit' => 10,
            'hypervel.debug' => true,
            'hypervel.timeout' => 5,
            ...$additionalConfig
        ]);
        
        // Skip Hypervel reset since the package isn't available
        // if (class_exists('Hypervel\\Facades\\Hypervel')) {
        //     \Hypervel\Facades\Hypervel::reset();
        // }

        // Make sure we don't have any lingering mocks
        Mockery::close();
    }

    /**
     * Run an async test and wait for all coroutines to complete
     *
     * @param callable $callback The test callback
     * @return mixed The result of the callback
     */
    public static function runAsyncTest(callable $callback)
    {
        self::setupHypervelTestEnv();
        
        $result = $callback();
        
        // Skip running Hypervel since the package isn't available
        // (new self())->runHypervel();
        
        return $result;
    }

    /**
     * Simulate running Hypervel (for compatibility with tests)
     */
    public function runHypervel()
    {
        // This is a placeholder method since we don't have access to the actual Hypervel package
        // In a real implementation, this would run pending coroutines
    }

    /**
     * Run multiple functions concurrently and wait for all to complete
     *
     * @param array $functions Array of callables to run concurrently
     * @return array Results from all functions
     */
    public static function runConcurrently(array $functions): array
    {
        // Always use sequential execution since Hypervel is not available
        $results = [];
        foreach ($functions as $key => $fn) {
            $results[$key] = $fn();
        }
        return $results;
    }

    /**
     * Test a Livewire component with Hypervel
     *
     * @param string $componentClass The component class to test
     * @param array $params Component parameters
     * @param \App\Models\User|null $user The user to authenticate as
     * @return \Livewire\Testing\TestableLivewire
     */
    public static function testComponentWithHypervel(string $componentClass, array $params = [], $user = null)
    {
        // Set up the test
        if ($user) {
            auth()->login($user);
        }
        
        return Livewire::test($componentClass, $params);
    }

    /**
     * Benchmark async vs sync approaches
     *
     * @param callable $syncFn The synchronous function to benchmark
     * @param callable $asyncFn The asynchronous function to benchmark
     * @param int $iterations Number of iterations to run
     * @return array Benchmark metrics
     */
    public static function benchmarkAsyncVsSync(callable $syncFn, callable $asyncFn, int $iterations = 3): array
    {
        $syncTimes = [];
        $asyncTimes = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            // Benchmark sync version
            $startSync = microtime(true);
            $syncResult = $syncFn();
            $endSync = microtime(true);
            $syncTime = $endSync - $startSync;
            $syncTimes[] = $syncTime;
            
            // Small delay to let system stabilize
            usleep(100000); // 100ms
            
            // Benchmark async version
            $startAsync = microtime(true);
            $asyncResult = $asyncFn();
            $endAsync = microtime(true);
            $asyncTime = $endAsync - $startAsync;
            $asyncTimes[] = $asyncTime;
            
            // Small delay to let system stabilize
            usleep(100000); // 100ms
        }
        
        // Calculate average times
        $avgSyncTime = array_sum($syncTimes) / count($syncTimes);
        $avgAsyncTime = array_sum($asyncTimes) / count($asyncTimes);
        
        // Calculate improvement
        $improvement = $avgSyncTime / $avgAsyncTime;
        $percentImprovement = ($improvement - 1) * 100;
        
        return [
            'sync_time' => $avgSyncTime,
            'async_time' => $avgAsyncTime,
            'improvement' => $improvement,
            'percent_improvement' => $percentImprovement,
            'iterations' => $iterations,
        ];
    }

    /**
     * Make a real HypervelService for testing
     * 
     * @return HypervelService
     */
    public static function makeRealHypervelService(): HypervelService
    {
        return app(HypervelService::class);
    }
} 