# Hypervel Integration Guide

## Introduction

Hypervel provides two key features to enhance your Laravel application:

1. **Server-side Concurrency**: A service for running I/O-bound operations concurrently to improve performance
2. **Client-side Interactivity**: Integration of Hyperscript with Livewire for lightweight client-side interactions

## Part 1: Hypervel Service for Concurrent Operations

The Hypervel service allows you to run multiple I/O-bound operations concurrently, significantly improving performance for dashboard loading, batch processing, and API requests.

### Key Benefits

- **Improved Performance**: Run multiple operations in parallel rather than sequentially
- **Simple API**: Intuitive methods for common concurrency patterns
- **Error Handling**: Consistent error handling for concurrent operations
- **Batch Processing**: Process collections of items with controlled concurrency

### Installation

The Hypervel service is already installed in the application. You can use it by injecting it into your controllers or services:

```php
use App\Services\HypervelService;

class DashboardController extends Controller
{
    protected $hypervelService;
    
    public function __construct(HypervelService $hypervelService)
    {
        $this->hypervelService = $hypervelService;
    }
    
    // Controller methods...
}
```

### Basic Usage

#### Running Operations Concurrently

```php
$data = $this->hypervelService->runConcurrently([
    'stats' => fn() => $this->fetchStats(),
    'recentActivity' => fn() => $this->fetchRecentActivity(),
    'upcomingEvents' => fn() => $this->fetchUpcomingEvents(),
    'notifications' => fn() => $this->fetchNotifications(),
]);
```

#### Batch Processing

```php
$results = $this->hypervelService->runBatch($users, function ($user) {
    return $this->processUserData($user);
}, 10); // Process in batches of 10
```

#### Concurrent HTTP Requests

```php
$responses = $this->hypervelService->runConcurrentHttpRequests([
    'weather' => 'https://api.weather.com/current',
    'news' => 'https://api.news.com/latest',
    'stocks' => 'https://api.stocks.com/market',
]);
```

#### Retrying Operations

```php
$result = $this->hypervelService->runWithRetry(
    fn() => $this->attemptImport(),
    3,    // 3 retries
    200   // 200ms delay between retries
);
```

### Benchmarking

Use the benchmark command to measure performance improvements:

```bash
php artisan hypervel:benchmark
```

For detailed information about the Hypervel service, see the [Hypervel Service Documentation](docs/hypervel-service.md).

### Interactive Demo Dashboard

To see Hypervel in action, you can visit the interactive demo dashboard at `/hypervel-demo`. This dashboard demonstrates the performance benefits of using Hypervel for concurrent operations.

#### Features of the Demo Dashboard

- **Real-time Performance Comparison**: Toggle between concurrent and sequential loading to see the actual performance difference
- **Visual Performance Metrics**: See how much faster Hypervel makes your application
- **Task Statistics**: Load multiple data sets concurrently 
- **Artificially Delayed Operations**: The demo uses artificial delays to simulate complex database queries or API calls

#### How to Access the Demo

1. Log in to the application
2. Click on "Hypervel Demo" in the main navigation
3. Use the "Compare Performance" button to see the performance difference
4. Click "Refresh" to reload the dashboard data

#### Implementation Details

The demo is implemented using a Livewire component that uses the HypervelService to load multiple data sets concurrently:

```php
// Load dashboard data concurrently using Hypervel
$startTime = microtime(true);

$this->dashboardData = $this->hypervelService->runConcurrently([
    'tasks' => fn() => $this->getTasks(),
    'taskStats' => fn() => $this->getTaskStats(),
    'recentActivity' => fn() => $this->getRecentActivity(),
    'upcomingTasks' => fn() => $this->getUpcomingTasks(),
    'popularCategories' => fn() => $this->getPopularCategories(),
]);

$this->loadTime = round((microtime(true) - $startTime) * 1000);
```

For comparison, the same data is loaded sequentially:

```php
// Load the same data sequentially to compare performance
$startTime = microtime(true);

$sequentialData = [
    'tasks' => $this->getTasks(),
    'taskStats' => $this->getTaskStats(),
    'recentActivity' => $this->getRecentActivity(),
    'upcomingTasks' => $this->getUpcomingTasks(),
    'popularCategories' => $this->getPopularCategories(),
];

$this->comparisonTime = round((microtime(true) - $startTime) * 1000);
```

This demo provides a practical example of how to implement Hypervel in your Livewire components to improve performance.

---

## Part 2: Hyperscript Integration with Livewire

// ... existing Hyperscript content continues here ... 

## Technical Implementation Details

### How Hypervel Achieves Concurrency

The HypervelService utilizes the Promise pattern implemented by Guzzle Promises to achieve true concurrency in PHP:

1. **Creating Promises**: Each operation is converted into a Promise that will be executed asynchronously
   ```php
   $promises[$key] = Promise\Create::promiseFor(null)->then(function() use ($operation, $key) {
       return $operation();
   });
   ```

2. **Unwrapping Results**: All Promises are unwrapped simultaneously with a timeout
   ```php
   $results = Promise\Utils::unwrap($promises, $this->timeout);
   ```

3. **HTTP Request Pooling**: For HTTP requests, Guzzle's Pool is used to manage concurrent requests
   ```php
   $pool = new Pool($client, $requests, [
       'concurrency' => $this->concurrencyLimit,
       // ... handlers for success and failure
   ]);
   ```

### Performance Considerations

While PHP is single-threaded, I/O operations can run concurrently because PHP releases the thread during I/O wait time. This makes Hypervel particularly effective for:

- **Database Queries**: Multiple independent queries can be executed concurrently
- **API Calls**: External API requests are perfect for concurrency since they spend most time waiting
- **File Operations**: Reading/writing files can be parallelized
- **Email Sending**: Multiple emails can be sent concurrently

For CPU-bound operations, Hypervel will provide less benefit since PHP cannot execute multiple CPU operations truly in parallel without multiple processes.

### Testing vs. Production

Hypervel has special handling for testing environments:

```php
// Skip concurrency in testing environment if configured
if (config('hypervel.disable_concurrency_in_testing', false) && app()->environment('testing')) {
    // Process operations sequentially for predictable test results
}
```

This makes tests more predictable while still allowing the full benefits of concurrency in production.

### Error Handling

The service provides comprehensive error handling:

```php
try {
    // Concurrent operations
} catch (Throwable $e) {
    throw new HypervelException("Error in concurrent operations: " . $e->getMessage(), 0, $e, [
        'operations' => array_keys($operations),
        'results' => $results,
    ]);
}
```

The `HypervelException` class includes detailed context about what was happening when the error occurred, making debugging easier. 