# Laravel Slower Integration

## Introduction

This document outlines the integration of [Laravel Slower](https://github.com/halilcosdu/laravel-slower) into our application. Laravel Slower is a powerful package designed to identify and log slow database queries in Laravel applications. It can significantly help improve application performance by highlighting performance bottlenecks in database operations.

## Key Features

- **Slow Query Detection**: Automatically detects and logs database queries that exceed a configurable time threshold
- **Detailed Query Information**: Captures SQL statements, bindings, execution time, and connection details
- **AI Recommendations**: Optional AI-powered recommendations for query optimization (using OpenAI)
- **Admin Interface**: Provides a simple interface to review slow queries and their details

## Installation

The package has been installed via Composer:

```bash
composer require halilcosdu/laravel-slower
```

## Configuration

The package configuration has been published to `config/slower.php` and the following settings have been configured in the `.env` file:

```dotenv
# Laravel Slower Configuration
SLOWER_ENABLED=true
SLOWER_THRESHOLD=1000
SLOWER_AI_RECOMMENDATION=false
SLOWER_IGNORE_EXPLAIN_QUERIES=true
SLOWER_IGNORE_INSERT_QUERIES=true
```

### Key Configuration Options

- **SLOWER_ENABLED**: Enables or disables the package globally
- **SLOWER_THRESHOLD**: Time threshold in milliseconds to consider a query as slow (default: 1000ms)
- **SLOWER_AI_RECOMMENDATION**: Disabled by default to avoid OpenAI API costs. Can be enabled if needed.
- **SLOWER_IGNORE_EXPLAIN_QUERIES**: Ignores EXPLAIN queries to prevent recursive logging
- **SLOWER_IGNORE_INSERT_QUERIES**: Ignores INSERT queries as they're typically less important to optimize

## Database Structure

The package creates a table called `slow_logs` with the following structure:

- `id`: Auto-increment primary key
- `is_analyzed`: Boolean indicating if the query has been analyzed
- `bindings`: Query bindings
- `sql`: The SQL statement
- `time`: Execution time in milliseconds
- `connection`: The database connection used
- `connection_name`: Name of the database connection
- `raw_sql`: The raw SQL with bindings applied
- `recommendation`: Optimization recommendations (if AI is enabled)
- `created_at` and `updated_at`: Timestamps

## Usage

### Viewing Slow Queries

Slow queries are automatically logged to the database. You can view them by querying the `slow_logs` table:

```php
use HalilCosdu\Slower\Models\SlowLog;

// Get all slow queries
$slowQueries = SlowLog::all();

// Get slow queries from the last 24 hours
$recentSlowQueries = SlowLog::where('created_at', '>=', now()->subDay())->get();
```

### Creating a Simple Admin Interface

You can create a simple admin interface to view slow queries. Add a controller and blade views to display the slow query data.

Example controller:

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use HalilCosdu\Slower\Models\SlowLog;
use Illuminate\Http\Request;

class SlowQueryController extends Controller
{
    public function index()
    {
        $slowQueries = SlowLog::latest()->paginate(20);
        
        return view('admin.slow-queries.index', compact('slowQueries'));
    }
    
    public function show(SlowLog $slowQuery)
    {
        return view('admin.slow-queries.show', compact('slowQuery'));
    }
}
```

### Analyzing Results

When reviewing slow queries, look for:

1. **Repeated Queries**: If the same query appears multiple times, it may indicate a larger issue
2. **Missing Indexes**: Queries performing table scans on large tables
3. **N+1 Query Problems**: Multiple similar queries executed in loops
4. **Complex Joins**: Queries joining many tables or with complex conditions

## Best Practices

1. **Set an Appropriate Threshold**: Start with a higher threshold (e.g., 1000ms) and gradually lower it as you optimize your application
2. **Regularly Review Logs**: Schedule a weekly review of slow queries
3. **Prioritize High-Impact Queries**: Focus on queries that affect user-facing functionality first
4. **Test Optimizations**: Always test optimization changes in a staging environment first

## Troubleshooting

If you encounter issues with Laravel Slower:

- **Logs Filling Up**: If your logs are filling up too quickly, increase the threshold
- **No Slow Queries Detected**: Lower the threshold temporarily to confirm the package is working
- **Performance Impact**: If the package itself is impacting performance, consider disabling it in production environments and only enabling it periodically for monitoring

## Conclusion

Laravel Slower provides valuable insights into database performance bottlenecks, allowing developers to identify and fix slow queries before they impact users. By regularly reviewing the slow query logs and implementing optimizations, the application's overall performance and user experience can be significantly improved.

## Resources

- [GitHub Repository](https://github.com/halilcosdu/laravel-slower)
- [Laravel Slower Official Website](https://laravel-slower.com) 