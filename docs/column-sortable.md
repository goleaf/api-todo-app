# Laravel Column Sortable Integration

## Overview

The Laravel Column Sortable integration provides a clean, intuitive way to add sortable columns to your application's data tables. The package allows users to click on column headers to sort data in ascending or descending order, complete with directional indicators.

## Features

- **Blade Integration**: Adds the `@sortablelink` directive for easy implementation in views
- **Model Integration**: Adds the `Sortable` trait for models that need sorting functionality
- **Direction Indicators**: Visually indicates current sort direction with customizable icons
- **Query Parameter Management**: Automatically handles URL query parameters for sorting
- **Relationship Sorting**: Supports sorting by relationship columns
- **Customizable Styling**: Easily customizable appearance through configuration

## Installation

The integration uses the `kyslik/column-sortable` package:

```bash
composer require kyslik/column-sortable
```

The service provider is automatically registered through Laravel's package discovery.

## Configuration

The package configuration is published to `config/columnsortable.php`:

```bash
php artisan vendor:publish --provider="Kyslik\ColumnSortable\ColumnSortableServiceProvider" --tag="config"
```

### Key Configuration Options

- **Icon Configuration**: Customize the appearance of sorting indicators
- **Column Type Mapping**: Define different styles for specific column types
- **Default Direction**: Set the default sort direction
- **Relationship Handling**: Configure how relationship columns are handled

## Implementation

### 1. Model Setup

Add the `Sortable` trait to your models and define which columns are sortable:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Task extends Model
{
    use Sortable;

    public $sortable = [
        'id',
        'title',
        'due_date',
        'priority',
        'completed',
        'created_at',
        'updated_at'
    ];
    
    // ... rest of the model
}
```

### 2. Controller Implementation

In your controllers, use the `sortable()` method on your query builder:

```php
public function index(Request $request)
{
    $tasks = Task::sortable()->paginate(15);
    
    return view('tasks.index', compact('tasks'));
}
```

For more advanced implementations with custom query parameters:

```php
public function index(Request $request)
{
    $query = Task::query();
    
    // Apply other filters
    if ($request->has('search')) {
        $query->search($request->search);
    }
    
    // Apply sorting using the Sortable trait
    if ($request->has('sort') || $request->has('direction')) {
        $query = $query->sortable($request->only(['sort', 'direction']));
    } else {
        // Default sorting
        $query->orderBy('created_at', 'desc');
    }
    
    $tasks = $query->paginate(15);
    
    return view('tasks.index', compact('tasks'));
}
```

### 3. View Implementation

In your Blade views, use the `@sortablelink` directive to create sortable column headers:

```blade
<table class="table">
    <thead>
        <tr>
            <th>@sortablelink('id', 'ID')</th>
            <th>@sortablelink('title', 'Task Title')</th>
            <th>@sortablelink('due_date', 'Due Date')</th>
            <th>@sortablelink('created_at', 'Created')</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td>{{ $task->title }}</td>
                <td>{{ $task->due_date }}</td>
                <td>{{ $task->created_at }}</td>
                <td>
                    <!-- Actions -->
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Pagination with query parameters preserved -->
{!! $tasks->appends(\Request::except('page'))->render() !!}
```

The `@sortablelink` directive accepts several parameters:
- First parameter: column name
- Second parameter (optional): display text
- Third parameter (optional): default query parameters as array
- Fourth parameter (optional): HTML attributes as array

## Relationship Sorting

To enable sorting by relationship columns, configure both models correctly:

### Parent Model (e.g., Task)

```php
use Kyslik\ColumnSortable\Sortable;

class Task extends Model
{
    use Sortable;

    public $sortable = [
        'id',
        'title',
        // Define relationship column
        'category.name'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### Related Model (e.g., Category)

```php
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use Sortable;

    public $sortable = [
        'id',
        'name'
    ];
}
```

### View Implementation for Relationships

```blade
<th>@sortablelink('category.name', 'Category')</th>
```

## Advanced Customization

### Custom Sorting Logic

For custom sorting logic, define a method in your model:

```php
public function scopePrioritySortable($query, $direction)
{
    return $query->orderBy('priority', $direction);
}
```

Then in your view:

```blade
<th>@sortablelink('priority', 'Priority')</th>
```

### Custom Icons

Configure custom icons in `config/columnsortable.php`:

```php
'default_icon_set' => 'fa fa-sort',
'sortable_icon' => 'fa fa-sort',
'asc_suffix' => '-asc',
'desc_suffix' => '-desc',
```

## API Reference

### Sortable Trait Methods

- `sortable($defaultParameters = null)`: Apply sorting to the query
- `scopeSortable($query, $defaultParameters = null)`: Scope implementation of sorting

### Blade Directive

```blade
@sortablelink('column', 'Title', ['param' => 'value'], ['class' => 'custom-class'])
```

## Examples

We've included example views to demonstrate the integration:

- `resources/views/examples/sortable-tasks.blade.php`: Example for tasks
- `resources/views/examples/sortable-users.blade.php`: Example for users
- `resources/views/examples/sortable-categories.blade.php`: Example for categories

## Troubleshooting

### Common Issues

- **Sort Icons Not Displaying**: Ensure your front-end includes the required icon library (FontAwesome by default)
- **No Sorting Applied**: Check if your model includes the `Sortable` trait and has a `$sortable` array
- **Relationship Sorting Not Working**: Ensure both models are properly configured with the `Sortable` trait

### Debug Tips

- Check your browser network tab to see the generated URL parameters
- Use `dd()` to debug the query builder within the `sortable()` method

## CSS Styling

Example styling for sorting icons:

```css
.sortable-link {
    display: inline-flex;
    align-items: center;
}

.sortable-link i {
    margin-left: 5px;
}

.sortable-link-active {
    font-weight: bold;
}
``` 