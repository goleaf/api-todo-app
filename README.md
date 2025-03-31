# Todo App

A modern task management application built with Laravel, Livewire, and Tailwind CSS.

## Features

- User authentication and registration
- Task creation and management
- Categories with customizable colors and icons
- Task prioritization, due dates, and reminders
- Progress tracking
- Tag support
- Dark mode support
- Mobile responsive design

## Recent Updates

### Todo to Task Migration (March 30, 2025)

We've completed the migration from the Todo model to the Task model to support enhanced task management capabilities. This update includes:

- Migrated data from `todos` table to a more feature-rich `tasks` table
- Updated all components to use the Task model
- Enhanced task functionality with improved progress tracking and prioritization
- Comprehensive test coverage for all task operations

For detailed information about the migration, see [Todo to Task Migration Guide](docs/TODO_TO_TASK_MIGRATION.md).

## Requirements

- PHP 8.1+
- Composer
- MySQL 5.7+ or compatible database
- Node.js and NPM

## Installation

1. Clone the repository
   ```bash
   git clone https://github.com/yourusername/todo-app.git
   cd todo-app
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Install JavaScript dependencies
   ```bash
   npm install
   ```

4. Create and configure environment file
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database in the `.env` file
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=todo_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Run database migrations and seeders
   ```bash
   php artisan migrate:fresh --seed
   ```

7. Build frontend assets
   ```bash
   npm run dev
   ```

8. Start the development server
   ```bash
   php artisan serve
   ```

9. Visit `http://localhost:8000` in your browser

## Default Credentials

After seeding the database, you can log in with these credentials:

- Admin User: admin@example.com / password
- Test User: test@example.com / password

## Testing

Run the tests to ensure everything is working properly:

```bash
php artisan test
```

Or run specific test suites:

```bash
php artisan test --filter=Feature
php artisan test --filter=Unit
```

## Troubleshooting

If you encounter any issues during setup or usage, refer to `.cursor/rules/main.mdc` for common problems and solutions.

Common fixes:

- Clear all caches: `php artisan optimize:clear`
- Restart the development server
- Ensure database credentials are correct
- Check storage directory permissions: `chmod -R 775 storage bootstrap/cache`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## API Documentation

The API documentation is available at `/api/documentation`.

## Testing

Our application uses a comprehensive testing approach with Livewire testing tools for components and PHPUnit for backend logic.

### Running Tests

Run all tests with:
```bash
php artisan test
```

### Test Structure

- **Feature Tests**: Test full HTTP requests and Livewire components
  - `tests/Feature/Livewire/`: Tests for Livewire components
  - `tests/Feature/Api/`: Tests for API endpoints
  - `tests/Feature/Auth/`: Tests for authentication flows

- **Unit Tests**: Test individual classes and methods
  - `tests/Unit/Models/`: Tests for model relationships and methods
  - `tests/Unit/Services/`: Tests for service classes

### Testing Helpers

We've created custom test helpers to simplify writing tests:

```php
// Create test data easily
[$user, $todos] = LivewireTestHelpers::createTestEnvironment(3);

// Test Livewire components with authentication
LivewireTestHelpers::testComponentAsUser(TaskList::class, $user)
    ->assertSee('Task List');

// Test form submissions with validation
LivewireTestHelpers::testFormSubmission(
    Login::class,
    'login',
    ['email' => 'test@example.com', 'password' => 'password123']
);
```

See the [Livewire Testing Guide](docs/livewire-testing-guide.md) for comprehensive documentation on testing Livewire components.

## Features

### Dashboard
- View a summary of tasks
- Quick access to recently updated and upcoming tasks
- Task completion statistics

### Tasks
- Create and manage tasks
- Filter tasks by status (all, pending, completed, overdue, upcoming)
- Search tasks by title or description
- View task details and edit tasks

### Calendar
- View tasks in a calendar format
- Monthly navigation
- View and manage tasks for specific dates

### Statistics
- View task completion rates
- Task distribution by category
- Recent activity timeline

### Profile
- Update user profile information
- Change password

## Vue to Livewire Migration

We have successfully completed the migration from Vue.js to Livewire 3. This project involved converting all frontend components, ensuring API compatibility, and maintaining test coverage.

### Migration Summary
- ✅ Removed all Vue.js dependencies and components
- ✅ Created equivalent Livewire components with the same functionality
- ✅ Updated API endpoints to work seamlessly with Livewire
- ✅ Moved from JavaScript tests to PHPUnit tests for components
- ✅ Created new test helpers specifically for Livewire
- ✅ Updated documentation with Livewire testing guide
- ✅ Maintained backward compatibility for API consumers
- ✅ Applied code style standards with Laravel Pint

### Migration Benefits
- **Improved Server-Side Rendering**: Livewire provides server-side rendering which reduces client-side processing
- **Reduced JavaScript Footprint**: Much smaller JS bundle size with Livewire compared to Vue
- **Simplified Component Lifecycle**: Easier to understand and manage component state
- **Better Integration with Laravel**: Native Laravel eco-system integration
- **Increased Development Speed**: Faster development with less context-switching
- **Enhanced Testing**: Simpler testing with PHPUnit rather than JavaScript testing tools
- **Real-Time Updates**: Added real-time task notifications using Livewire's event system and Laravel Echo

### Livewire Component Structure
The application now uses the following Livewire components:

| Component | Description | File |
|-----------|-------------|------|
| `Auth.Login` | User login form | `app/Livewire/Auth/Login.php` |
| `Auth.Register` | User registration form | `app/Livewire/Auth/Register.php` |
| `Tasks.TaskList` | Task listing and management | `app/Livewire/Tasks/TaskList.php` |
| `Tasks.TaskShow` | Task detail view | `app/Livewire/Tasks/TaskShow.php` |
| `Dashboard` | Main dashboard | `app/Livewire/Dashboard.php` |
| `Stats` | Statistics and metrics | `app/Livewire/Stats.php` |
| `Calendar` | Task calendar view | `app/Livewire/Calendar.php` |
| `Profile` | User profile management | `app/Livewire/Profile.php` |
| `Notifications.TaskNotifications` | Real-time task notifications | `app/Livewire/Notifications/TaskNotifications.php` |

### API Endpoints
All existing API endpoints remain functional for mobile apps and other API consumers. The API structure follows RESTful conventions with the following main endpoints:

- `/api/v1/auth/*` - Authentication endpoints
- `/api/v1/tasks/*` - Task management endpoints
- `/api/v1/users/*` - User management endpoints
- `/api/v1/stats/*` - Statistics endpoints

For complete API documentation, visit `/api/documentation`.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Documentation

Additional documentation is available in the `docs` directory:

- [Livewire Testing Guide](docs/livewire-testing-guide.md): Complete guide to testing Livewire components
- [Real-Time Notifications](docs/real-time-notifications.md): Detailed information on setting up and using the real-time notification system
- [Vue to Livewire Migration](docs/vue-to-livewire-migration-checklist.md): Checklist used for migrating from Vue to Livewire

## Credits

- Design inspiration from various Tailwind UI components
- Icons from Heroicons 

## Hypervel Integration

This project uses Hypervel for asynchronous and concurrent operations to improve performance. Key features include:

- **Dashboard with Concurrent Data Loading**: The dashboard loads multiple data sets concurrently, resulting in significantly faster page loads.
- **Batch Processing for Todos**: Perform operations on multiple todos simultaneously with the bulk processor.
- **Performant API Endpoints**: API endpoints use Hypervel to run queries concurrently.
- **Benchmark Command**: Run performance tests to measure Hypervel improvements.

### Performance Improvements

Benchmark tests show significant performance improvements when using Hypervel:

- Dashboard data loading is 2-3x faster with concurrent requests
- Batch operations complete in a fraction of the time
- API response times are reduced by up to 60%

### Running Benchmarks

You can run performance benchmarks to compare synchronous vs asynchronous approaches:

```bash
# Benchmark dashboard data loading
php artisan hypervel:benchmark --feature=dashboard

# Benchmark batch processing
php artisan hypervel:benchmark --feature=batch --todos=200

# Benchmark API operations
php artisan hypervel:benchmark --feature=api

# Configure benchmark parameters
php artisan hypervel:benchmark --todos=100 --iterations=5 --feature=dashboard
```

### Documentation

For more details on the Hypervel integration, see:

- [Hypervel Integration Guide](docs/hypervel-integration.md)
- [API Documentation](api/documentation)

### Testing

The project includes comprehensive tests for Hypervel components:

```bash
# Run Hypervel integration tests
php artisan test --filter=TaskManagementTest
```

## Hypervel Performance Benchmark

The application includes a performance benchmarking tool to measure the benefits of using Hypervel for concurrent operations. This benchmark compares sequential processing versus concurrent execution to help you identify where Hypervel can provide the most significant performance improvements.

### Running the Benchmark

```bash
# Run the complete benchmark
php artisan hypervel:benchmark

# Run with custom settings
php artisan hypervel:benchmark --todos=100 --iterations=10 --delay=50 --feature=dashboard
```

### Command Options

- `--todos=50` - Number of todo items to use in the benchmark (default: 50)
- `--iterations=5` - Number of iterations for each test (default: 5)
- `--feature=all` - Specific feature to benchmark: `dashboard`, `batch`, `api`, or `all` (default: all)
- `--delay=100` - Artificial delay in milliseconds to simulate I/O operations (default: 100)

### Features Benchmarked

1. **Dashboard Loading**: Simulates loading multiple components of a dashboard concurrently vs. sequentially
2. **Batch Processing**: Compares processing collections of items in batches concurrently vs. one at a time
3. **API Requests**: Measures performance improvements when making multiple HTTP requests in parallel

### Benchmark Results

The command outputs detailed results showing:

- Average execution time (with standard deviation) for both regular and Hypervel approaches
- Percentage improvement when using Hypervel
- Specific recommendations based on measured improvements

### When to Use Hypervel

Based on benchmark results, the command provides recommendations:

- **Strongly Recommended** (≥50% improvement) - Significant performance benefits
- **Recommended** (≥30% improvement) - Notable performance improvements
- **Consider Using** (≥10% improvement) - Moderate benefits that may be worthwhile
- **Minimal Benefit** (<10% improvement) - The overhead may outweigh the benefits

### Example Output

```
Benchmark Results:
+------------------+----------------+----------------+-------------+---------------------+
| Feature          | Regular (ms)   | Hypervel (ms)  | Improvement | Recommendation      |
+------------------+----------------+----------------+-------------+---------------------+
| Dashboard Loading| 402.34 (±5.21) | 108.67 (±3.45) | 73.0%       | Strongly Recommended|
| Batch Processing | 305.12 (±4.87) | 89.34 (±4.12)  | 70.7%       | Strongly Recommended|
| API Requests     | 512.45 (±6.34) | 125.67 (±5.23) | 75.5%       | Strongly Recommended|
+------------------+----------------+----------------+-------------+---------------------+

Overall Recommendations:
Hypervel provides significant performance improvements (73.1%). Recommended for production use.
```

For more information, see the [Hypervel Service Documentation](docs/hypervel-service.md).

## TodoMVC Implementation

The application includes a TodoMVC implementation built with Laravel Livewire. This demonstrates how to implement the classic [TodoMVC](http://todomvc.com) reference application in Laravel.

Features:
- Fully functional TodoMVC implementation with Livewire
- Real-time updates without page refreshes
- Filter by All/Active/Completed/Due Today/Overdue/Upcoming
- URL routing with hash fragments (#/, #/active, #/completed, #/due-today, #/overdue, #/upcoming)
- Double-click to edit todos
- Keyboard shortcuts for editing (Enter to save, Esc to cancel)
- Clear completed functionality
- Complete test coverage
- Persistent filters across page refreshes
- Smooth animations for item transitions
- Due dates for todos with color coding by status (overdue, due today)
- Visual filtering based on due dates
- Toggle visibility of due dates
- Performance optimizations using Hypervel for:
  - Bulk operations (Clear completed, Toggle all)
  - Debounced user interactions
- Loading indicators for better UX
- Performance monitoring to track component load times

To access the TodoMVC feature:
1. Log in to the application
2. Click on the "TodoMVC" link in the navigation menu
3. Use direct URLs: `/todomvc`, `/todomvc/active`, `/todomvc/completed`, `/todomvc/due-today`, `/todomvc/overdue`, `/todomvc/upcoming` 