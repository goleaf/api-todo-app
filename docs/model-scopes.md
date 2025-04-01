# Laravel Model Scopes Generator

This package provides a convenient way to generate query scopes for your Laravel models.

## Overview

Laravel's query scopes allow you to encapsulate common query constraints that you can easily re-use throughout your application. This generator creates a trait with pre-defined scopes based on your model's fields, saving you time and providing a consistent interface for filtering data.

## Installation

The Model Scopes Generator is already installed in this application. It is registered in the `ScopesServiceProvider` and ready to use.

## Usage

### Basic Usage

```bash
php artisan generate:scopes {model}
```

Where `{model}` is the name of your model class (e.g., User, Post, etc.).

### Options

| Option | Description |
|--------|-------------|
| `--all` | Generate scopes for all properties including common fields like id, timestamps, etc. |
| `--fields=field1,field2` | Comma-separated list of specific fields to create scopes for |
| `--force` | Overwrite existing scopes if they already exist |

### Examples

Generate scopes for the User model:
```bash
php artisan generate:scopes User
```

Generate scopes for specific fields:
```bash
php artisan generate:scopes Post --fields=title,content,status
```

Generate scopes for all fields including common ones:
```bash
php artisan generate:scopes Product --all
```

Force overwrite of existing scopes:
```bash
php artisan generate:scopes Order --force
```

## Generated Scopes

The command will generate a trait file in your model's namespace under a `Scopes` directory. For example, if your model is `App\Models\User`, the trait will be created at `App\Models\Scopes\UserScopes.php`.

### Types of Scopes Generated

Depending on the field type (which is automatically detected), different types of scopes will be generated:

#### String Fields
- `scope{Field}()` - Exact match
- `scope{Field}Like()` - LIKE pattern match
- `scope{Field}Contains()` - Contains substring
- `scope{Field}StartsWith()` - Starts with substring
- `scope{Field}EndsWith()` - Ends with substring

#### Integer Fields
- `scope{Field}()` - Exact match
- `scope{Field}GreaterThan()` - Greater than value
- `scope{Field}LessThan()` - Less than value
- `scope{Field}Between()` - Between min and max values

#### Boolean Fields
- `scope{Field}()` - Where field is true
- `scopeNot{Field}()` - Where field is false

#### DateTime Fields
- `scope{Field}Before()` - Before given date
- `scope{Field}After()` - After given date
- `scope{Field}Between()` - Between two dates
- `scope{Field}Date()` - Matching date (ignoring time)

#### JSON Fields
- `scope{Field}Contains()` - JSON contains key or key/value pair

#### Default (fallback for all other types)
- `scope{Field}()` - Exact match
- `scope{Field}Not()` - Not equal to value
- `scope{Field}In()` - In array of values
- `scope{Field}NotIn()` - Not in array of values
- `scope{Field}Null()` - Is null
- `scope{Field}NotNull()` - Is not null

## Using Generated Scopes

After generating the scopes, you need to use the trait in your model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserScopes;

class User extends Model
{
    use UserScopes;
    
    // ... rest of your model
}
```

Then you can use the scopes in your queries:

```php
// Find users with name containing "John"
$users = User::nameContains('John')->get();

// Find users created after a certain date
$users = User::createdAtAfter('2023-01-01')->get();

// Find active users
$users = User::active()->get();

// Combined scopes
$users = User::nameContains('John')
    ->createdAtAfter('2023-01-01')
    ->active()
    ->get();
```

## Field Type Detection

The generator automatically detects field types based on field names:

- **DateTime**: Fields ending with `_at` or `_date` and common fields like `created_at`
- **Boolean**: Fields like `is_active`, `active`, `published`, etc.
- **Integer**: Fields ending with `_id`, `_count`, etc. and common fields like `id`
- **JSON**: Fields containing `json`, `data`, `meta`, etc.
- **Default**: All other fields are treated as strings

## Tips

- Use `--all` when you need scopes for timestamp fields
- Use `--fields` to generate only specific scopes for clarity
- After generating, you may want to customize some scopes to fit your specific needs 