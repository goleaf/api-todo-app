<?php

namespace Tests\Hypervel;

use App\Services\HypervelService;
use Hypervel\Facades\Hypervel;

trait TestHypervelConcurrency
{
    /**
     * Prepare the test environment for Hypervel concurrency tests
     */
    protected function setUpHypervel(): void
    {
        // Ensure the Hypervel is initialized
        Hypervel::reset();

        // Use memory database to isolate test data
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        // Ensure the hypervel service is ready
        app()->forgetInstance(HypervelService::class);
        app()->singleton(HypervelService::class, function () {
            return new HypervelService;
        });
    }

    /**
     * Run a Hypervel test with benchmarking capabilities
     *
     * @param  callable  $testCallback  The test function to run
     * @param  int  $iterations  Optional number of iterations for benchmarking
     * @return array Performance metrics if benchmarking, null otherwise
     */
    protected function runHypervelTest(callable $testCallback, int $iterations = 1): ?array
    {
        $this->setUpHypervel();

        if ($iterations === 1) {
            $testCallback();

            return null;
        }

        // Benchmarking mode
        $times = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $testCallback();
            $end = microtime(true);
            $times[] = $end - $start;
        }

        return [
            'total_iterations' => $iterations,
            'avg_time' => array_sum($times) / count($times),
            'min_time' => min($times),
            'max_time' => max($times),
            'times' => $times,
        ];
    }

    /**
     * Compare the performance of synchronized vs concurrent code
     *
     * @param  callable  $syncCode  The synchronous code to test
     * @param  callable  $asyncCode  The concurrent code using Hypervel
     * @param  int  $iterations  Number of times to run each test
     * @return array The performance comparison results
     */
    protected function comparePerformance(callable $syncCode, callable $asyncCode, int $iterations = 3): array
    {
        $this->setUpHypervel();

        // Test synchronous performance
        $syncTimes = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $syncCode();
            $end = microtime(true);
            $syncTimes[] = $end - $start;
        }

        // Test asynchronous performance
        $asyncTimes = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $asyncCode();
            $end = microtime(true);
            $asyncTimes[] = $end - $start;
        }

        $syncAvg = array_sum($syncTimes) / count($syncTimes);
        $asyncAvg = array_sum($asyncTimes) / count($asyncTimes);
        $improvement = $syncAvg / max(0.001, $asyncAvg); // Avoid division by zero

        return [
            'sync' => [
                'avg' => $syncAvg,
                'min' => min($syncTimes),
                'max' => max($syncTimes),
                'times' => $syncTimes,
            ],
            'async' => [
                'avg' => $asyncAvg,
                'min' => min($asyncTimes),
                'max' => max($asyncTimes),
                'times' => $asyncTimes,
            ],
            'improvement' => $improvement,
            'percent_faster' => ($improvement - 1) * 100,
            'iterations' => $iterations,
        ];
    }

    /**
     * Run a simulated load test for Hypervel performance
     *
     * @param  callable  $setupCallback  Function to set up the test data
     * @param  callable  $syncCallback  Function with synchronous code to test
     * @param  callable  $asyncCallback  Function with asynchronous code to test
     * @param  int  $iterations  Number of iterations
     * @return array Performance metrics
     */
    protected function runLoadTest(
        callable $setupCallback,
        callable $syncCallback,
        callable $asyncCallback,
        int $iterations = 3
    ): array {
        $this->setUpHypervel();

        // Set up the test data
        $setupCallback();

        // Run synchronous test
        $syncStart = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $syncCallback();
        }
        $syncTime = microtime(true) - $syncStart;

        // Run asynchronous test
        $asyncStart = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $asyncCallback();
        }
        $asyncTime = microtime(true) - $asyncStart;

        // Calculate improvement
        $improvement = $syncTime / max(0.001, $asyncTime); // Avoid division by zero

        return [
            'sync_time' => $syncTime,
            'async_time' => $asyncTime,
            'improvement' => $improvement,
            'percent_faster' => ($improvement - 1) * 100,
            'iterations' => $iterations,
        ];
    }
}
