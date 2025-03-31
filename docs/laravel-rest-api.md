# Laravel REST API Package Integration

This document provides information about the Laravel REST API package (`lomkit/laravel-rest-api`) integrated into our application.

## About Laravel REST API Package

Laravel REST API is a powerful package that allows us to quickly generate and expose a standardized API for our models. It takes advantage of Laravel's ecosystem including Policies, Controllers, Eloquent, and other features to provide a robust and comprehensive API interface.

## Installation

The package has been installed via Composer:

```bash
composer require lomkit/laravel-rest-api
```

## Configuration

### Resources

The package organizes API endpoints around Resources, which are abstractions of our Eloquent models. We have created the following resources:

- `UserResource`: Exposes the User model
- `TaskResource`: Exposes the Task model
- `CategoryResource`: Exposes the Category model
- `TagResource`: Exposes the Tag model

Each resource defines which fields, relations, and scopes are available through the API.

### Controllers

Controllers provide the interface between HTTP requests and our resources. We've created the following controllers:

- `UsersController`: Handles requests for User resources
- `TasksController`: Handles requests for Task resources
- `CategoriesController`: Handles requests for Category resources
- `TagsController`: Handles requests for Tag resources

### Routes

The REST API routes are defined in `routes/api.php` and are prefixed with `/api/rest`. All routes (except for documentation) require authentication via Sanctum.

```php
// Public routes
Route::prefix('rest')->name('rest.')->group(function () {
    // Documentation - using a regular route
    Route::get('docs', function () {
        return response()->json([
            'title' => 'Todo API Documentation',
            'description' => 'API documentation for the Todo application',
            'version' => '1.0.0',
            'resources' => [
                'users' => '/api/rest/users',
                'tasks' => '/api/rest/tasks',
                'categories' => '/api/rest/categories',
                'tags' => '/api/rest/tags',
            ]
        ]);
    })->name('docs');
});

// Protected routes
Route::middleware('auth:sanctum')->prefix('rest')->name('rest.')->group(function () {
    // Users (admin only)
    Rest::resource('users', \App\Rest\Controllers\UsersController::class)
        ->middleware('admin.api');

    // Tasks
    Rest::resource('tasks', \App\Rest\Controllers\TasksController::class);

    // Categories
    Rest::resource('categories', \App\Rest\Controllers\CategoriesController::class);

    // Tags
    Rest::resource('tags', \App\Rest\Controllers\TagsController::class);
});
```

## Available Endpoints

For each resource, the following endpoints are available:

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/rest/{resource}` | Get resource details |
| POST | `/api/rest/{resource}/search` | Search for resources with filters and includes |
| POST | `/api/rest/{resource}/actions/{action}` | Execute operations on resources |
| POST | `/api/rest/{resource}/mutate` | Create, update, or delete resources in bulk |
| DELETE | `/api/rest/{resource}` | Delete resources by primary keys |

## Usage Examples

### Authentication

Before using the API, you must authenticate using Laravel Sanctum. Obtain a token via the existing `/api/login` endpoint:

```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Use the token in the Authorization header for subsequent requests:

```http
Authorization: Bearer your-token-here
```

### Searching for Resources

To search for resources with filtering, sorting, and including relations:

```http
POST /api/rest/tasks/search
Content-Type: application/json

{
    "filters": [
        {
            "field": "completed",
            "operator": "=",
            "value": false
        }
    ],
    "includes": ["category", "tags"],
    "sorts": [
        {
            "field": "due_date",
            "direction": "asc"
        }
    ],
    "limit": 10
}
```

### Creating Resources

To create new resources:

```http
POST /api/rest/tasks/mutate
Content-Type: application/json

{
    "create": [
        {
            "title": "New Task",
            "description": "Task description here",
            "category_id": 1,
            "due_date": "2023-12-31"
        }
    ]
}
```

### Updating Resources

To update existing resources:

```http
POST /api/rest/tasks/mutate
Content-Type: application/json

{
    "update": [
        {
            "id": 123,
            "title": "Updated Task Title",
            "completed": true
        }
    ]
}
```

### Deleting Resources

To delete resources:

```http
POST /api/rest/tasks/mutate
Content-Type: application/json

{
    "delete": [123, 456, 789]
}
```

Alternatively, you can use the DELETE endpoint:

```http
DELETE /api/rest/tasks
Content-Type: application/json

{
    "primaryKeys": [123, 456, 789]
}
```

## Using Scopes

Each resource exposes its model's scopes that can be used for filtering in search queries:

```http
POST /api/rest/tasks/search
Content-Type: application/json

{
    "scopes": [
        {
            "name": "dueToday"
        },
        {
            "name": "forUser",
            "parameters": [1]
        }
    ]
}
```

## Using Relations

Relations can be included in responses:

```http
POST /api/rest/tasks/search
Content-Type: application/json

{
    "includes": ["user", "category", "tags"]
}
```

## Using Aggregates

You can use aggregates to get summary statistics:

```http
POST /api/rest/tasks/search
Content-Type: application/json

{
    "aggregates": [
        {
            "relation": "tags",
            "type": "count"
        }
    ]
}
```

## API Documentation

The API documentation is available at `/api/rest/docs`, which provides a comprehensive overview of all available resources, fields, and operations.

## Testing

We've created tests for the REST API in `tests/Feature/Api/RestApiTest.php`. Run them using:

```bash
php artisan test --filter=RestApiTest
```

## Security Considerations

- All API endpoints (except documentation) require authentication
- The `users` resource is only accessible to admins
- Each resource respects the permissions and scopes defined in its configuration

## Further Reading

- [Laravel REST API GitHub Repository](https://github.com/Lomkit/laravel-rest-api)
- [Laravel REST API Documentation](https://laravel-rest-api.lomkit.com/) 