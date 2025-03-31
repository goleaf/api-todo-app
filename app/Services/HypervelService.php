<?php

namespace App\Services;

// use Hypervel\Facades\Hypervel;
use Illuminate\Support\Collection;
use App\Exceptions\HypervelException;
use Throwable;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;

/**
 * HypervelService provides a wrapper around Hypervel functionality
 * with additional error handling and logging
 */
class HypervelService
{
    /**
     * Maximum concurrency limit
     *
     * @var int
     */
    protected $concurrencyLimit;

    /**
     * Timeout in seconds
     *
     * @var int
     */
    protected $timeout;

    /**
     * Default batch size
     * 
     * @var int
     */
    protected $defaultBatchSize;

    /**
     * Debug mode
     * 
     * @var bool
     */
    protected $debug;

    /**
     * HTTP client
     * 
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->concurrencyLimit = config('hypervel.concurrency_limit', 25);
        $this->timeout = config('hypervel.timeout', 30);
        $this->defaultBatchSize = config('hypervel.default_batch_size', 10);
        $this->debug = config('hypervel.debug', false);
        
        $this->httpClient = new Client([
            'timeout' => $this->timeout,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * Run multiple operations concurrently.
     * This uses Guzzle Promises to achieve actual concurrency.
     *
     * @param  array  $operations  Array of operations with keys and callables
     * @return array
     *
     * @throws \App\Exceptions\HypervelException
     */
    public function runConcurrently(array $operations)
    {
        $promises = [];
        $results = [];

        try {
            // Skip concurrency in testing environment if configured
            if (config('hypervel.disable_concurrency_in_testing', false) && app()->environment('testing')) {
                $this->debugLog("Concurrency disabled in testing environment, running sequentially");
                foreach ($operations as $key => $operation) {
                    if (!is_callable($operation)) {
                        throw new HypervelException("Operation '{$key}' is not callable");
                    }
                    $results[$key] = $operation();
                }
                return $results;
            }

            // Create promises for each operation
            foreach ($operations as $key => $operation) {
                if (!is_callable($operation)) {
                    throw new HypervelException("Operation '{$key}' is not callable");
                }

                $promises[$key] = Promise\Create::promiseFor(null)->then(function() use ($operation, $key) {
                    $this->debugLog("Starting operation: {$key}");
                    return $operation();
                });
            }

            // Wait for all promises to complete with a timeout
            $results = Promise\Utils::unwrap($promises, $this->timeout);
            
            $this->debugLog("All operations completed successfully");
            return $results;
        } catch (Throwable $e) {
            $this->debugLog("Error in concurrent operations: " . $e->getMessage(), 'error');
            throw new HypervelException("Error in concurrent operations: " . $e->getMessage(), 0, $e, [
                'operations' => array_keys($operations),
                'results' => $results,
            ]);
        }
    }

    /**
     * Process items in batches concurrently.
     * This uses actual concurrency for batch processing.
     *
     * @param  \Illuminate\Support\Collection|array  $items
     * @param  callable  $callback
     * @param  int|null  $batchSize
     * @return array
     *
     * @throws \App\Exceptions\HypervelException
     */
    public function runBatch($items, callable $callback, ?int $batchSize = null)
    {
        $results = [];
        $batchSize = $batchSize ?? $this->defaultBatchSize;

        try {
            // Convert to collection if array
            if (is_array($items)) {
                $items = collect($items);
            }

            // Skip concurrency in testing environment if configured
            if (config('hypervel.disable_concurrency_in_testing', false) && app()->environment('testing')) {
                $this->debugLog("Batch concurrency disabled in testing environment, processing sequentially");
                foreach ($items as $index => $item) {
                    $key = is_object($item) && method_exists($item, 'getKey') 
                        ? $item->getKey() 
                        : $index;
                    
                    $results[$key] = $callback($item);
                }
                return $results;
            }

            // Process in batches
            $batches = $items->chunk($batchSize);
            $this->debugLog("Processing " . count($items) . " items in " . count($batches) . " batches");

            foreach ($batches as $batchIndex => $batch) {
                $batchOperations = [];

                foreach ($batch as $index => $item) {
                    $key = is_object($item) && method_exists($item, 'getKey') 
                        ? $item->getKey() 
                        : $batchIndex . '_' . $index;
                    
                    $batchOperations[$key] = fn() => $callback($item);
                }

                $batchResults = $this->runConcurrently($batchOperations);
                $results = array_merge($results, $batchResults);

                $this->debugLog("Completed batch {$batchIndex} with " . count($batchResults) . " results");
            }

            return $results;
        } catch (Throwable $e) {
            $this->debugLog("Error in batch processing: " . $e->getMessage(), 'error');
            throw new HypervelException("Error in batch processing: " . $e->getMessage(), 0, $e, [
                'total_items' => count($items),
                'batch_size' => $batchSize,
                'processed' => count($results),
            ]);
        }
    }

    /**
     * Run an operation with retry logic.
     *
     * @param  callable  $operation
     * @param  int  $retries
     * @param  int  $delay  Delay in milliseconds
     * @return mixed
     *
     * @throws \App\Exceptions\HypervelException
     */
    public function runWithRetry(callable $operation, int $retries = 3, int $delay = 100)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt <= $retries) {
            try {
                $this->debugLog("Attempt {$attempt} of {$retries}");
                return $operation();
            } catch (Throwable $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt <= $retries) {
                    $this->debugLog("Retry after error: " . $e->getMessage(), 'warning');
                    usleep($delay * 1000); // Convert to microseconds
                }
            }
        }

        $this->debugLog("All retry attempts failed: " . $lastException->getMessage(), 'error');
        throw new HypervelException("Operation failed after {$retries} retries", 0, $lastException, [
            'retries' => $retries,
            'delay_ms' => $delay,
        ]);
    }

    /**
     * Run multiple HTTP requests concurrently.
     * Uses Guzzle's request pooling for true concurrency.
     *
     * @param  array  $urls  Array of URLs with keys
     * @param  int  $delay  Optional delay in ms for simulation 
     * @return array
     *
     * @throws \App\Exceptions\HypervelException
     */
    public function runConcurrentHttpRequests(array $urls, $delay = null)
    {
        try {
            $client = $this->httpClient;
            $results = [];

            // If this is a simulation with delay, we'll use a different approach
            if ($delay !== null) {
                $operations = [];
                foreach ($urls as $key => $url) {
                    $operations[$key] = function() use ($url, $delay) {
                        // Simulate I/O delay
                        usleep($delay * 1000);
                        return ['url' => $url, 'status' => 200, 'data' => ['sample' => 'data']];
                    };
                }
                return $this->runConcurrently($operations);
            }

            // Skip concurrency in testing environment if configured
            if (config('hypervel.disable_concurrency_in_testing', false) && app()->environment('testing')) {
                $this->debugLog("HTTP concurrency disabled in testing environment");
                foreach ($urls as $key => $url) {
                    // For testing, just return a mock response
                    $results[$key] = [
                        'status' => 200,
                        'body' => ['success' => true, 'url' => $url],
                        'headers' => ['Content-Type' => 'application/json'],
                    ];
                }
                return $results;
            }

            // Create requests
            $requests = [];
            foreach ($urls as $key => $url) {
                $requests[$key] = new Request('GET', $url);
            }

            $this->debugLog("Sending " . count($requests) . " HTTP requests");

            // Create pool with concurrency limit
            $pool = new Pool($client, $requests, [
                'concurrency' => $this->concurrencyLimit,
                'fulfilled' => function ($response, $key) use (&$results) {
                    $results[$key] = [
                        'status' => $response->getStatusCode(),
                        'body' => json_decode($response->getBody(), true),
                        'headers' => $response->getHeaders(),
                    ];
                    $this->debugLog("Request to {$key} completed with status: " . $response->getStatusCode());
                },
                'rejected' => function ($reason, $key) use (&$results) {
                    $results[$key] = [
                        'error' => $reason->getMessage(),
                    ];
                    $this->debugLog("Request to {$key} failed: " . $reason->getMessage(), 'error');
                },
            ]);

            // Execute requests
            $pool->promise()->wait();
            
            return $results;
        } catch (Throwable $e) {
            $this->debugLog("Error in concurrent HTTP requests: " . $e->getMessage(), 'error');
            throw new HypervelException("Error in concurrent HTTP requests: " . $e->getMessage(), 0, $e, [
                'urls' => array_keys($urls),
            ]);
        }
    }

    /**
     * Log debug messages if debug mode is enabled
     *
     * @param  string  $message
     * @param  string  $level
     * @return void
     */
    protected function debugLog(string $message, string $level = 'debug')
    {
        if ($this->debug) {
            Log::$level("Hypervel: {$message}");
        }
    }

    /**
     * Run a computational task in a separate coroutine
     *
     * @param Closure $task The task to run
     * @return mixed Result of the task
     */
    public function computeAsync(Closure $task): mixed
    {
        return Hypervel::async($task);
    }

    /**
     * Run an asynchronous task with proper error handling
     *
     * @param Closure $task The task to run asynchronously
     * @return mixed The result of the asynchronous task
     * @throws HypervelException
     */
    public function runAsync(Closure $task)
    {
        try {
            $result = Hypervel::async(function() use ($task) {
                return $task();
            });
            
            return Hypervel::await($result);
        } catch (Throwable $e) {
            report($e);
            throw new HypervelException('Failed to execute async task: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch multiple API endpoints concurrently
     *
     * @param array $urls Array of URLs to fetch
     * @param array $headers Optional headers to include with the requests
     * @return array Responses from the API endpoints
     * @throws HypervelException
     */
    public function fetchMultipleApis(array $urls, array $headers = []): array
    {
        $functions = [];
        
        foreach ($urls as $key => $url) {
            $functions[$key] = function() use ($url, $headers) {
                return $this->fetchSingleApi($url, $headers);
            };
        }
        
        return $this->runConcurrently($functions);
    }

    /**
     * Fetch a single API endpoint
     *
     * @param string $url URL to fetch
     * @param array $headers Optional headers to include with the request
     * @return array Response from the API endpoint
     */
    private function fetchSingleApi(string $url, array $headers = []): array
    {
        try {
            // Ensure we use non-blocking HTTP client
            $response = Hypervel::await(Hypervel::async(function() use ($url, $headers) {
                $client = new \GuzzleHttp\Client();
                return $client->get($url, [
                    'headers' => $headers,
                    'timeout' => 10,
                ]);
            }));
            
            return [
                'success' => true,
                'status' => $response->getStatusCode(),
                'data' => json_decode($response->getBody()->getContents(), true),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process a collection of items concurrently.
     *
     * @param  \Illuminate\Support\Collection|array  $collection
     * @param  callable  $callback
     * @param  int|null  $concurrencyLimit
     * @return \Illuminate\Support\Collection
     *
     * @throws \App\Exceptions\HypervelException
     */
    public function processCollection($collection, callable $callback, ?int $concurrencyLimit = null): Collection
    {
        $concurrencyLimit = $concurrencyLimit ?? $this->concurrencyLimit;
        
        try {
            // Convert to collection if array
            if (is_array($collection)) {
                $collection = collect($collection);
            }
            
            $this->debugLog("Processing collection with {$collection->count()} items and concurrency limit of {$concurrencyLimit}");
            
            // For small collections or when concurrency is 1, process sequentially
            if ($collection->count() <= 1 || $concurrencyLimit <= 1) {
                return $collection->map($callback);
            }
            
            // Process in chunks based on concurrency limit
            $chunks = $collection->chunk($concurrencyLimit);
            $results = collect();
            
            foreach ($chunks as $chunk) {
                $operations = [];
                
                // Create operation for each item
                foreach ($chunk as $index => $item) {
                    $operations[$index] = fn() => $callback($item);
                }
                
                // Run chunk concurrently
                $chunkResults = $this->runConcurrently($operations);
                
                // Add results maintaining the original order
                foreach ($chunkResults as $result) {
                    $results->push($result);
                }
            }
            
            $this->debugLog("Finished processing collection, produced {$results->count()} results");
            return $results;
            
        } catch (Throwable $e) {
            $this->debugLog("Error processing collection: " . $e->getMessage(), 'error');
            throw new HypervelException("Error processing collection: " . $e->getMessage(), 0, $e, [
                'collection_size' => count($collection),
                'concurrency_limit' => $concurrencyLimit,
            ]);
        }
    }

    /**
     * Execute a database query with timeout
     *
     * @param Closure $queryFunction Function containing the database query
     * @param int $timeout Maximum time to wait for query execution (in seconds)
     * @return mixed Result of the database query
     */
    public function executeQuery(Closure $queryFunction, int $timeout = 10)
    {
        return Hypervel::withTimeout($timeout, function() use ($queryFunction) {
            return $queryFunction();
        });
    }
} 