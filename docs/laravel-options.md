# Laravel Options Integration

This application integrates the Laravel Options package to provide a global key-value store in the database. This allows you to store application-wide settings, configuration, and other data that needs to be easily accessible throughout the application.

## Features

- Store and retrieve global configuration values
- Consistent API for accessing options
- Helper functions, Blade directives, and Artisan commands
- API endpoints for managing options programmatically

## PHP Usage

### Basic Usage

You can use the `option()` helper function to get and set option values:

```php
// Get an option
$value = option('site.title');

// Get an option with a default value
$theme = option('site.theme', 'default');

// Set an option
option(['site.title' => 'My Awesome Site']);

// Remove an option
option()->remove('site.title');

// Check if an option exists
if (option_exists('site.title')) {
    // Do something
}
```

### Using the Facade

You can also use the Option facade:

```php
use Appstract\Options\Option;

// Get all options
$options = Option::all();

// Check if an option exists
$exists = Option::exists('site.title');
```

## Blade Component

A blade component is available for displaying option values in your views:

```blade
<x-app-options key="site.title" default="My Site" />

<x-app-options key="site.description" default="A Laravel application">
    <!-- Additional content -->
</x-app-options>
```

## Blade Directives

The package provides Blade directives for working with options:

```blade
<!-- Display an option -->
@option('site.title')

<!-- Display an option with a default value -->
@option('site.theme', 'default')

<!-- Check if an option exists -->
@optionExists('site.maintenance_mode')
    <div class="alert alert-warning">
        The site is in maintenance mode.
    </div>
@endif
```

## Artisan Commands

The application includes a custom Artisan command for managing options:

```bash
# List all options
php artisan app:options list

# Get a specific option
php artisan app:options get site.title

# Set an option
php artisan app:options set site.title "My Site"

# Remove an option
php artisan app:options remove site.title
```

The package also provides its own command for setting options:

```bash
php artisan option:set site.title "My Site"
```

## API Endpoints

The following API endpoints are available for managing options:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/options` | Get all options |
| GET | `/api/options?key=site.title` | Get a specific option |
| POST | `/api/options` | Create a new option |
| PUT | `/api/options/{key}` | Update an option |
| DELETE | `/api/options/{key}` | Delete an option |
| GET | `/api/options/{key}/exists` | Check if an option exists |

### Examples

#### Get all options

```http
GET /api/options
```

Response:
```json
{
  "site.title": "My Awesome Site",
  "site.description": "A Laravel application",
  "maintenance_mode": false
}
```

#### Get a specific option

```http
GET /api/options?key=site.title
```

Response:
```json
{
  "key": "site.title",
  "value": "My Awesome Site"
}
```

#### Create an option

```http
POST /api/options
Content-Type: application/json

{
  "key": "site.color",
  "value": "#3490dc"
}
```

Response:
```json
{
  "message": "Option saved successfully",
  "key": "site.color",
  "value": "#3490dc"
}
```

#### Update an option

```http
PUT /api/options/site.color
Content-Type: application/json

{
  "value": "#38c172"
}
```

Response:
```json
{
  "message": "Option updated successfully",
  "key": "site.color",
  "value": "#38c172"
}
```

#### Delete an option

```http
DELETE /api/options/site.color
```

Response:
```json
{
  "message": "Option removed successfully",
  "key": "site.color"
}
```

#### Check if an option exists

```http
GET /api/options/site.color/exists
```

Response:
```json
{
  "key": "site.color",
  "exists": true
}
```

## Important Notes

- Options are stored in the database, so they persist across application deployments.
- Values can be of any type that can be serialized to JSON (strings, numbers, booleans, arrays, and objects).
- For sensitive data, consider using environment variables or Laravel's built-in encryption. 