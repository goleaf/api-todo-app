# Blade Template Organization

This document outlines the organization of blade templates in our application, following a controller-based architecture with clean separation of concerns.

## Directory Structure

```
resources/
├── views/
│   ├── components/          # Reusable UI components
│   │   ├── ui/              # Basic UI elements (buttons, cards, etc.)
│   │   └── shared/          # Feature-specific components (task cards, etc.)
│   └── controllers/         # Views organized by controllers
│       ├── layout.blade.php # Main layout template
│       ├── partials/        # Layout partials (navigation, footer, etc.)
│       │   ├── navigation.blade.php
│       │   ├── footer.blade.php
│       │   └── flash-messages.blade.php
│       └── frontend/        # Views for frontend controllers
│           ├── dashboard/   # Dashboard controller views
│           ├── task/        # Task controller views
│           ├── category/    # Category controller views
│           └── tag/         # Tag controller views
├── lang/                    # Language files for internationalization
│   ├── en/                  # English translations
│   └── es/                  # Spanish translations
└── js/                      # JavaScript files (extracted from blades)
```

## Key Principles

1. **Minimal Root Folders**
   - Only two main view folders: `components` and `controllers`
   - All controller-specific views are organized in the controllers directory

2. **Component-Based Architecture**
   - UI components: Basic UI elements like buttons, cards, inputs
   - Shared components: Feature-specific components that use UI components

3. **Internationalization**
   - No hardcoded strings in templates
   - All text extracted to language files
   - Using `{{ __('key') }}` syntax for translations

4. **Clean Separation of Concerns**
   - JavaScript extracted to dedicated JS files
   - PHP logic moved to controllers/models
   - No inline styles (using Tailwind CSS classes)

## Files and Naming Conventions

- **Views**: Follow controller/action pattern: `controllers/frontend/task/index.blade.php`
- **Components**: Use kebab-case: `components/shared/task-card.blade.php`
- **Language keys**: Use snake_case: `tasks.status.in_progress`
- **JavaScript files**: Follow feature-based naming: `task-form.js`, `dashboard.js`

## Component Usage

### UI Components

UI components are the basic building blocks of our interface:

```blade
<x-ui.button type="submit" color="primary">
    {{ __('common.save') }}
</x-ui.button>

<x-ui.card>
    <!-- Card content -->
</x-ui.card>

<x-ui.alert type="success" dismissible>
    {{ session('success') }}
</x-ui.alert>
```

### Shared Components

Shared components are feature-specific and often combine multiple UI components:

```blade
<x-shared.task-card :task="$task" />
<x-shared.task-status :status="$task->status" />
<x-shared.tag-badge :tag="$tag" />
```

## Internationalization

All text is extracted to language files:

```php
// resources/lang/en/tasks.php
return [
    'create' => 'Create Task',
    'status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress'
    ]
];
```

And used in templates with the translation helper:

```blade
<h1>{{ __('tasks.create') }}</h1>
<span>{{ __('tasks.status.pending') }}</span>
```

## JavaScript

JavaScript is extracted from blade files and imported where needed:

```blade
@push('scripts')
<script src="{{ asset('js/task-form.js') }}"></script>
@endpush
``` 