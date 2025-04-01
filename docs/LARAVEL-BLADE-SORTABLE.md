# Laravel Blade Sortable Integration

## Introduction

This document explains how the Laravel Blade Sortable package has been integrated into our project. The package provides an elegant way to implement column sorting in your Blade templates with minimal effort.

## Installation

The package has been installed via Composer:

```bash
composer require kyslik/column-sortable
```

## Configuration

The package configuration has been published to `config/columnsortable.php`. The following key configurations have been set:

- **Default sort direction**: Ascending
- **URI relation params**: Enabled (allows sorting related columns)
- **Icons**: Bootstrap-compatible icons have been configured
- **Default order by**: When no sort is specified, falls back to 'id', 'desc'

## Model Integration

### Adding Sortable Trait

We've integrated the `Sortable` trait into the following models:

- `Task`
- `User`
- `Category`
- `Tag`

Example implementation in the Task model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Task extends Model
{
    use Sortable;

    // Define which columns can be sorted
    public $sortable = [
        'id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'progress',
        'created_at',
        'updated_at',
    ];

    // Other model code...
}
```

### Sorting Relations

For sorting by related columns (e.g., sorting tasks by user name), relationship sorting has been configured:

```php
// In Task model
public $sortableAs = ['user_name', 'category_name'];

// Define relationships
public function user()
{
    return $this->belongsTo(User::class);
}

public function category()
{
    return $this->belongsTo(Category::class);
}
```

## Controller Implementation

Controllers have been updated to support sortable queries. For example, in the TaskController:

```php
public function index()
{
    $tasks = Task::sortable()->with(['user', 'category'])->paginate(10);
    
    return view('admin.tasks.index', compact('tasks'));
}
```

## View Implementation

The sorting functionality has been implemented in the Blade templates using the `@sortablelink` directive:

```blade
<table class="table">
    <thead>
        <tr>
            <th>@sortablelink('id', 'ID')</th>
            <th>@sortablelink('title')</th>
            <th>@sortablelink('user.name', 'User')</th>
            <th>@sortablelink('category.name', 'Category')</th>
            <th>@sortablelink('due_date', 'Due Date')</th>
            <th>@sortablelink('status')</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td>{{ $task->title }}</td>
                <td>{{ $task->user->name }}</td>
                <td>{{ $task->category->name }}</td>
                <td>{{ $task->due_date->format('Y-m-d') }}</td>
                <td>
                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($task->status) }}
                    </span>
                </td>
                <td>
                    <!-- Action buttons -->
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Pagination with Sort State Preservation

To maintain the sort state when navigating between pages, pagination links have been updated:

```blade
{{ $tasks->appends(request()->except('page'))->links() }}
```

## Custom Styling

Custom CSS has been added to enhance the appearance of sort indicators:

```css
.sortable-link {
    display: inline-flex;
    align-items: center;
}

.sortable-link i {
    margin-left: 5px;
    font-size: 0.8em;
}
```

## Testing

Testing for the sortable functionality has been implemented in browser tests. Key test cases include:

- Verifying sort direction changes when clicking on a sortable column
- Testing relationship sorting
- Ensuring pagination preserves sort order

## Troubleshooting

If sorting is not working as expected, check the following:

1. Ensure the column is defined in the model's `$sortable` array
2. For relationship sorting, verify that the relation method exists and is correctly defined
3. Check that the view is using the `@sortablelink` directive correctly
4. Confirm that the controller is calling the `sortable()` method on the query

## Conclusion

The Laravel Blade Sortable package has been fully integrated into our application, providing a robust and user-friendly way to sort data in our Blade templates. The implementation follows best practices and maintains consistent behavior across all sortable tables. 