# Laravel Model Info Integration

This package provides tools to inspect Laravel Eloquent models, their attributes, and relationships. The integration allows developers to easily obtain detailed information about models in the application through both a command-line interface and API endpoints.

## Overview

Laravel Model Info provides capabilities to:

- Discover all models in the application
- Extract model attributes and their types
- Identify model relationships
- Retrieve model metadata (table name, file path, etc.)

## Command-Line Interface

The package includes a command-line interface for quickly obtaining model information.

### Basic Usage

```bash
php artisan model:info
```

This command lists all models in the application with their respective table names and file paths.

### View Model Details

```bash
php artisan model:info User
```

This shows basic information about the User model.

### View Model Attributes

```bash
php artisan model:info User --attributes
```

Lists all attributes (columns) for the User model, including their types and casts.

### View Model Relations

```bash
php artisan model:info User --relations
```

Lists all relations defined on the User model, including their type and related model.

### View Detailed Information

```bash
php artisan model:info User --detail
```

Shows comprehensive information about the User model, including attributes and relations.

### Other Options

- `--table-only` - Show only model names and their corresponding table names

## API Endpoints

The package also provides API endpoints for accessing model information programmatically.

### List All Models

**Endpoint:** `GET /api/models`

**Authentication Required:** Yes

**Response Example:**
```json
{
  "success": true,
  "message": "Models retrieved successfully",
  "data": {
    "count": 5,
    "models": [
      {
        "name": "User",
        "class": "App\\Models\\User",
        "table": "users",
        "file_path": "/app/Models/User.php",
        "attribute_count": 12,
        "relation_count": 3
      },
      // More models...
    ]
  }
}
```

### Get Model Details

**Endpoint:** `GET /api/models/{model}`

**Parameters:**
- `model` - The model name (e.g., "User") or fully qualified class name (e.g., "App\\Models\\User")

**Authentication Required:** Yes

**Response Example:**
```json
{
  "success": true,
  "message": "Model information retrieved successfully",
  "data": {
    "name": "User",
    "class": "App\\Models\\User",
    "table": "users",
    "file_path": "/app/Models/User.php",
    "attributes": [
      {
        "name": "id",
        "type": "integer",
        "cast": null
      },
      {
        "name": "name",
        "type": "string",
        "cast": null
      },
      // More attributes...
    ],
    "relations": [
      {
        "name": "tasks",
        "type": "HasMany",
        "related": "App\\Models\\Task"
      },
      // More relations...
    ]
  }
}
```

### Get Model Attributes

**Endpoint:** `GET /api/models/{model}/attributes`

**Parameters:**
- `model` - The model name or fully qualified class name

**Authentication Required:** Yes

**Response Example:**
```json
{
  "success": true,
  "message": "Model attributes retrieved successfully",
  "data": {
    "model": "User",
    "attributes": [
      {
        "name": "id",
        "type": "integer",
        "cast": null
      },
      {
        "name": "email",
        "type": "string",
        "cast": null
      },
      // More attributes...
    ]
  }
}
```

### Get Model Relations

**Endpoint:** `GET /api/models/{model}/relations`

**Parameters:**
- `model` - The model name or fully qualified class name

**Authentication Required:** Yes

**Response Example:**
```json
{
  "success": true,
  "message": "Model relations retrieved successfully",
  "data": {
    "model": "User",
    "relations": [
      {
        "name": "tasks",
        "type": "HasMany",
        "related": "App\\Models\\Task"
      },
      {
        "name": "categories",
        "type": "HasMany",
        "related": "App\\Models\\Category"
      },
      // More relations...
    ]
  }
}
```

## Programmatic Usage

The package can also be used directly in your PHP code:

```php
use Spatie\ModelInfo\ModelFinder;
use Spatie\ModelInfo\ModelInfo;

// Get all models
$models = ModelFinder::all();

// Get info for a specific model
$modelInfo = ModelInfo::forModel(User::class);

// Access model details
$tableName = $modelInfo->tableName;
$fileName = $modelInfo->fileName;

// Access model attributes
$attributes = $modelInfo->attributes;

// Access model relations
$relations = $modelInfo->relations;
```

## Security Considerations

- The API endpoints are protected by Laravel Sanctum authentication
- Only authenticated users can access model information through the API
- Consider adding additional middleware for extra security if needed

## Troubleshooting

If you encounter issues:

1. Ensure the model class exists and follows Laravel conventions
2. Verify the model has a corresponding table in the database
3. Check for any custom configurations that might affect model discovery
4. Make sure your database connection is properly configured

## Additional Resources

- [Spatie Laravel Model Info Documentation](https://github.com/spatie/laravel-model-info)
- [Laravel Eloquent Documentation](https://laravel.com/docs/eloquent) 