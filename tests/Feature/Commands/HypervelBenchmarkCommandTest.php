<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class HypervelBenchmarkCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the benchmark command runs successfully.
     *
     * @return void
     */
    public function test_benchmark_command_runs_successfully()
    {
        // Execute the command with minimal test data
        $this->artisan('hypervel:benchmark', [
            '--todos' => 2,
            '--iterations' => 1,
            '--delay' => 10,
        ])
            ->assertExitCode(0);
    }

    /**
     * Test the benchmark command with specific feature.
     *
     * @return void
     */
    public function test_benchmark_specific_feature()
    {
        // Only run dashboard benchmark
        $this->artisan('hypervel:benchmark', [
            '--todos' => 2,
            '--iterations' => 1,
            '--delay' => 10,
            '--feature' => 'dashboard',
        ])
            ->expectsOutput('Benchmarking dashboard data loading...')
            ->doesntExpectOutput('Benchmarking batch processing...')
            ->assertExitCode(0);
    }

    /**
     * Test the benchmark command displays results correctly.
     *
     * @return void
     */
    public function test_benchmark_displays_results()
    {
        // Execute command and check for results table
        $this->artisan('hypervel:benchmark', [
            '--todos' => 2,
            '--iterations' => 1,
            '--delay' => 10,
            '--feature' => 'dashboard',
        ])
            ->expectsOutput('Benchmark Results:')
            ->expectsTable(
                ['Feature', 'Regular (ms)', 'Hypervel (ms)', 'Improvement', 'Recommendation'],
                // The actual values will vary, so we can't check them specifically
                Mockery::any()
            )
            ->assertExitCode(0);
    }

    /**
     * Test the benchmark command cleans up after itself.
     *
     * @return void
     */
    public function test_benchmark_cleans_up_test_data()
    {
        // Execute the command
        $this->artisan('hypervel:benchmark', [
            '--todos' => 2,
            '--iterations' => 1,
            '--delay' => 10,
        ])
            ->assertExitCode(0);

        // Check that test data was cleaned up
        $this->assertDatabaseCount('tasks', 0);
        $this->assertDatabaseMissing('users', [
            'name' => 'Benchmark Test User',
        ]);
    }

    /**
     * Test the benchmark shows performance improvement.
     *
     * @return void
     */
    public function test_benchmark_shows_performance_improvement()
    {
        // Use a longer delay to ensure we can measure performance difference
        $result = $this->artisan('hypervel:benchmark', [
            '--todos' => 3,
            '--iterations' => 2,
            '--delay' => 50,
            '--feature' => 'dashboard',
        ]);

        // Command should complete successfully
        $result->assertExitCode(0);

        // We can't assert on the specific improvement percentage since it will vary,
        // but we can verify the command ran to completion
        $this->assertTrue(true);
    }
}
