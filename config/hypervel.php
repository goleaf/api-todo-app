<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hypervel Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for Hypervel, the package
    | that enables asynchronous and concurrent operations in Laravel.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Hypervel Concurrency Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the concurrency behavior of Hypervel operations.
    |
    */

    /**
     * Maximum number of concurrent operations allowed
     */
    'concurrency_limit' => env('HYPERVEL_CONCURRENCY_LIMIT', 25),

    /**
     * Maximum time (in seconds) to wait for operations to complete
     */
    'timeout' => env('HYPERVEL_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Batch Processing Settings
    |--------------------------------------------------------------------------
    |
    | These settings control how batch processing works with Hypervel.
    |
    */

    /**
     * Default batch size for processing large collections
     */
    'default_batch_size' => env('HYPERVEL_BATCH_SIZE', 10),

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Settings for error handling and retry behavior.
    |
    */

    /*
    | Default number of retries for failed operations
    */
    'default_retries' => env('HYPERVEL_DEFAULT_RETRIES', 3),

    /*
    | Default delay in milliseconds between retries
    */
    'retry_delay' => env('HYPERVEL_RETRY_DELAY', 100),

    /*
    |--------------------------------------------------------------------------
    | Debugging and Monitoring
    |--------------------------------------------------------------------------
    |
    | Settings for debugging and monitoring Hypervel operations.
    |
    */

    /**
     * Enable debug logging for Hypervel operations
     */
    'debug' => env('HYPERVEL_DEBUG', false),

    /**
     * Enable performance metrics collection
     */
    'collect_metrics' => env('HYPERVEL_COLLECT_METRICS', false),

    /*
    |--------------------------------------------------------------------------
    | Simulation Settings
    |--------------------------------------------------------------------------
    |
    | Settings for simulating I/O delays in development or testing environments.
    |
    */

    /**
     * Default simulated delay for I/O operations in milliseconds.
     * Only used in development/testing to simulate real I/O delays.
     */
    'simulation_delay' => env('HYPERVEL_SIMULATION_DELAY', 100),

    /*
    |--------------------------------------------------------------------------
    | Testing
    |--------------------------------------------------------------------------
    |
    | Settings for testing with Hypervel.
    |
    */

    /*
    | Whether to skip performance tests in CI environments
    */
    'skip_performance_tests' => env('SKIP_PERFORMANCE_TESTS', false),

    /*
    | Maximum time in milliseconds that a performance test should aim to complete in
    */
    'performance_test_threshold' => env('HYPERVEL_PERFORMANCE_TEST_THRESHOLD', 500),

    /*
    |--------------------------------------------------------------------------
    | Memory Limit
    |--------------------------------------------------------------------------
    |
    | The maximum amount of memory (in MB) that Hypervel can use for coroutines.
    | This helps prevent memory leaks from causing application crashes.
    |
    */
    'memory_limit' => env('HYPERVEL_MEMORY_LIMIT', 512),

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | The log channel to use for Hypervel-related log messages. This can be
    | any channel defined in your logging configuration.
    |
    */
    'log_channel' => env('HYPERVEL_LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | If you want to use a specific database connection for Hypervel operations,
    | you can specify it here. This is useful for isolating long-running
    | queries from your main application connection pool.
    |
    */
    'database_connection' => env('HYPERVEL_DB_CONNECTION', env('DB_CONNECTION')),

    /*
    |--------------------------------------------------------------------------
    | Auto-Configure Services
    |--------------------------------------------------------------------------
    |
    | When enabled, Hypervel will attempt to automatically configure common
    | Laravel services like HTTP clients and database connections to work
    | optimally with coroutines.
    |
    */
    'auto_configure_services' => env('HYPERVEL_AUTO_CONFIGURE', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Integration
    |--------------------------------------------------------------------------
    |
    | Configure how Hypervel integrates with Laravel's queue system.
    | When enabled, Hypervel can run multiple queue jobs concurrently.
    |
    */
    'queue' => [
        'enabled' => env('HYPERVEL_QUEUE_ENABLED', false),
        'max_concurrent_jobs' => env('HYPERVEL_MAX_CONCURRENT_JOBS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Settings
    |--------------------------------------------------------------------------
    |
    | Settings for controlling behavior in test environments.
    |
    */

    /**
     * Disable actual concurrency in testing environments.
     * When true, operations will be executed sequentially during tests.
     */
    'disable_concurrency_in_testing' => env('HYPERVEL_DISABLE_CONCURRENCY_IN_TESTING', true),
];
