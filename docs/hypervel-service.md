# Hypervel Service Documentation

The Hypervel service provides a simple API for running operations concurrently in Laravel applications. It leverages PHP's Guzzle Promise library to enable efficient processing of I/O-bound operations, significantly improving performance in many scenarios.

## Installation

Hypervel is included in the application. No additional installation is required.

## Configuration

Configure Hypervel in the `config/hypervel.php` file or through environment variables:

```
HYPERVEL_CONCURRENCY_LIMIT=25
HYPERVEL_TIMEOUT=30
HYPERVEL_BATCH_SIZE=10
HYPERVEL_DEBUG=false
HYPERVEL_COLLECT_METRICS=false
HYPERVEL_SIMULATION_DELAY=100
```

## Core Methods

### 1. Running Concurrent Operations

```php
use App\Services\HypervelService;

public function __construct(HypervelService $hypervelService)
{
    $this->hypervelService = $hypervelService;
}

public function fetchDashboardData()
{
    $data = $this->hypervelService->runConcurrently([
        'stats' => fn() => $this->fetchStats(),
        'recentActivity' => fn() => $this->fetchRecentActivity(),
        'upcomingEvents' => fn() => $this->fetchUpcomingEvents(),
        'notifications' => fn() => $this->fetchNotifications(),
    ]);
    
    return [
        'stats' => $data['stats'],
        'recentActivity' => $data['recentActivity'],
        'upcomingEvents' => $data['upcomingEvents'],
        'notifications' => $data['notifications'],
    ];
}
```

### 2. Batch Processing

```php
public function processUsers(Collection $users)
{
    $results = $this->hypervelService->runBatch($users, function ($user) {
        return $this->processUserData($user);
    }, 10); // Process in batches of 10
    
    return $results;
}
```

### 3. Concurrent HTTP Requests

```php
public function fetchExternalData()
{
    $apis = [
        'weather' => 'https://api.weather.com/current',
        'news' => 'https://api.news.com/latest',
        'stocks' => 'https://api.stocks.com/market',
    ];
    
    return $this->hypervelService->runConcurrentHttpRequests($apis);
}
```

### 4. Operation With Retry

```php
public function importData()
{
    return $this->hypervelService->runWithRetry(
        fn() => $this->attemptImport(),
        3,     // 3 retries
        200    // 200ms delay between retries
    );
}
```

## Best Practices

### When to Use Hypervel

Hypervel is most effective for:

1. **I/O-bound operations** - Database queries, API calls, file system operations
2. **Independent operations** - Tasks that don't depend on each other's results
3. **Dashboard loading** - When multiple components need to load simultaneously

### When Not to Use Hypervel

Avoid using Hypervel for:

1. **CPU-bound operations** - Heavy calculations or processing
2. **Sequential operations** - Tasks that must be performed in a specific order
3. **Very fast operations** - The overhead of concurrency may outweigh the benefits

## Performance Considerations

- Use the `hypervel:benchmark` command to measure actual performance improvements
- Monitor memory usage when processing large batches
- Adjust the concurrency limit based on your server's resources

## Error Handling

Hypervel wraps exceptions in a `HypervelException` that provides additional context:

```php
try {
    $result = $hypervelService->runConcurrently([...]);
} catch (HypervelException $e) {
    Log::error('Concurrent operation failed', [
        'message' => $e->getMessage(),
        'context' => $e->getContext(),
    ]);
}
```

## Integration with Laravel

Hypervel integrates seamlessly with Laravel's dependency injection:

```php
class DashboardController extends Controller
{
    protected $hypervelService;
    
    public function __construct(HypervelService $hypervelService)
    {
        $this->hypervelService = $hypervelService;
    }
    
    public function index()
    {
        $dashboardData = $this->hypervelService->runConcurrently([
            'stats' => fn() => $this->fetchStats(),
            'activities' => fn() => $this->fetchActivities(),
        ]);
        
        return view('dashboard', $dashboardData);
    }
}
```

## Further Reading

- [Benchmark Documentation](../readme.md#hypervel-performance-benchmarking)
- [Guzzle Promises Documentation](https://github.com/guzzle/promises) 