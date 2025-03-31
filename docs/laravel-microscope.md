# Laravel Microscope Integration

## Overview

Laravel Microscope is a powerful static analysis tool for Laravel applications. It helps detect and fix potential errors, bad practices, and other issues that might be overlooked during development. This package performs smart checks that understand Laravel's runtime and magic, making it more effective than traditional IDEs and static analyzers.

## Features

- **Static Code Analysis**: Detects errors before they appear in production
- **Laravel-specific Checks**: Understanding Laravel conventions and magic methods
- **Automated Fixes**: Can automatically fix certain issues
- **Performance Optimized**: Built from scratch to be as fast as possible
- **Early Returns Refactoring**: Can refactor code by applying early returns pattern

## Implemented Checks

The following checks have been integrated into our application:

1. **PSR-4 Validation**: Ensures classes are in the correct namespace
2. **Route Checking**: Validates routes, controllers, and identifies duplicated routes
3. **Dead Controller Detection**: Identifies controllers without any associated routes
4. **Bad Practices Detection**: Finds anti-patterns like `env()` calls outside of config files
5. **Blade Queries Analysis**: Detects database queries in Blade templates
6. **Model Listing**: Lists all models in the application
7. **Unused View Variables**: Detects variables passed to views but not used
8. **Generic DocBlocks Removal**: Removes Laravel's auto-generated DocBlocks
9. **Helper Function Enforcement**: Converts Laravel facades to helper functions

## Usage

Laravel Microscope is set up as a development dependency and provides various Artisan commands:

### Essential Commands

```bash
# Check for PSR-4 compatibility issues
php artisan check:psr4

# Check routes for issues
php artisan check:routes

# Find controllers with no routes
php artisan check:dead_controllers

# List all models
php artisan list:models

# Check for bad coding practices
php artisan check:bad_practices

# Check for database queries in Blade templates
php artisan check:blade_queries

# Apply early returns refactoring
php artisan check:early_returns
```

### Additional Commands

```bash
# Check imports
php artisan check:imports

# Check and validate event listeners
php artisan check:events

# Check views for errors
php artisan check:views

# Check for global function calls
php artisan check:global_functions
```

## Configuration

Laravel Microscope's behavior can be configured through the `config/microscope.php` file:

```php
return [
    // Enable or disable microscope
    'is_enabled' => env('MICROSCOPE_ENABLED', true),

    // Prevent automatic fixing
    'no_fix' => false,
    
    // Patterns to ignore
    'ignore' => [
        // 'nova*'
    ],
    
    // Enable logging of unused view variables
    'log_unused_view_vars' => true,
    
    // Namespaces to ignore during scanning
    'ignored_namespaces' => [
        // 'Laravel\\Nova\\'
    ],
];
```

## Integration with CI/CD

Laravel Microscope can be integrated into your CI/CD pipeline to prevent code with potential issues from being deployed:

```yaml
# Example for GitHub Actions
steps:
  - name: Check PSR-4
    run: php artisan check:psr4 --no-interaction
    
  - name: Check Routes
    run: php artisan check:routes --no-interaction
    
  - name: Check Bad Practices
    run: php artisan check:bad_practices --no-interaction
```

## Example Identified Issues

During integration, Laravel Microscope detected the following issues in our application:

1. Route duplications for `/api/documentation` (conflict between application routes and Swagger documentation)
2. Missing controller `App\Http\Controllers\Api\CommentController` referenced in routes
3. Undefined route names being used: `dashboard`, `onboarding.skip`
4. `env()` function calls in the Service layer, which should be moved to config files
5. Controller actions without properly defined routes in `OnboardingController`

## Conclusion

Laravel Microscope has been successfully integrated into the project, helping identify several issues that could lead to errors in the application. By running these checks regularly, we can maintain high code quality and catch potential problems before they affect users.

For more detailed information on all commands and features, refer to the [official Laravel Microscope documentation](https://github.com/imanghafoori1/laravel-microscope). 