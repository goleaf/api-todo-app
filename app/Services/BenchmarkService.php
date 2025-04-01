<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
// use Squeaky\Squeaky; // Commented out temporarily

class BenchmarkService
{
    // protected Squeaky $squeaky;
    protected bool $enabled;
    protected array $startTimes = [];
    protected array $benchmarks = [];

    /**
     * Create a new BenchmarkService instance.
     */
    public function __construct()
    {
        // $this->squeaky = new Squeaky();
        $this->enabled = config('app.debug', false);
    }

    /**
     * Start measuring a segment of code.
     *
     * @param string $name
     * @return void
     */
    public function start(string $name): void
    {
        if (!$this->enabled) {
            return;
        }

        // $this->squeaky->start($name);
        $this->startTimes[$name] = microtime(true);
    }

    /**
     * End measuring a segment of code and log the results.
     *
     * @param string $name
     * @param bool $logResult
     * @return float|null Duration in milliseconds
     */
    public function end(string $name, bool $logResult = true): ?float
    {
        if (!$this->enabled || !isset($this->startTimes[$name])) {
            return null;
        }

        // $duration = $this->squeaky->end($name);
        $endTime = microtime(true);
        $duration = ($endTime - $this->startTimes[$name]) * 1000; // Convert to milliseconds
        $this->benchmarks[$name] = $duration;
        
        if ($logResult) {
            Log::debug("BENCHMARK: {$name} took {$duration}ms");
        }
        
        return $duration;
    }

    /**
     * Measure the execution time of a callback.
     *
     * @param string $name
     * @param callable $callback
     * @param bool $logResult
     * @return mixed The result of the callback
     */
    public function measure(string $name, callable $callback, bool $logResult = true)
    {
        if (!$this->enabled) {
            return $callback();
        }

        $this->start($name);
        $result = $callback();
        $this->end($name, $logResult);
        
        return $result;
    }

    /**
     * Get all benchmarks.
     *
     * @return array
     */
    public function getAllBenchmarks(): array
    {
        if (!$this->enabled) {
            return [];
        }

        // return $this->squeaky->getAllBenchmarks();
        return $this->benchmarks;
    }

    /**
     * Reset all benchmarks.
     *
     * @return void
     */
    public function reset(): void
    {
        if (!$this->enabled) {
            return;
        }

        // $this->squeaky->reset();
        $this->startTimes = [];
        $this->benchmarks = [];
    }
} 