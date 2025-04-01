# Implementing Blast Storybook in Todo.prus.dev

This guide provides step-by-step instructions for implementing Blast Storybook UI in the Todo.prus.dev application.

## Implementation Steps

### 1. Installation

First, install Blast via Composer:

```bash
composer require area17/blast
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="A17\Blast\BlastServiceProvider" --tag="blast-config"
```

### 2. Configuration

Update the `config/blast.php` file with the project-specific settings:

```php
<?php

return [
    // Storybook assets
    'assets' => [
        'compiled' => [
            // Compiled CSS & JS files (Vite or Laravel Mix output)
            'css' => [
                '/css/app.css',
            ],
            'js' => [
                '/js/app.js',
            ],
        ],
        'cdn' => [
            // Any CDN assets, if applicable
            'css' => [
                // 'https://cdn.example.com/some-external-library.css',
            ],
            'js' => [
                // 'https://cdn.example.com/some-external-library.js',
            ],
        ],
    ],
    
    // Component directories to scan for auto-generating stories
    'component_directories' => [
        // Relative to resources/views
        'components',
        'components/admin',
        'components/tasks',
        'components/ui',
    ],
    
    // Stories directory (where Blast will generate stories)
    'stories_directory' => 'stories',
    
    // Tailwind configuration for tokens
    'tailwind_config' => base_path('tailwind.config.js'),
    
    // Component Status options
    'statuses' => [
        'wip' => 'WIP',
        'ready' => 'Ready',
        'deprecated' => 'Deprecated',
    ],
];
```

### 3. Create Directory Structure

Create the necessary directories for Blast:

```bash
mkdir -p resources/views/stories/components
mkdir -p resources/views/stories/data
mkdir -p resources/views/stories/pages
```

### 4. Sample Component Story

Create a sample story for a Button component:

File location: `resources/views/stories/components/ui/button.blade.php`

```blade
@storybook([
    'name' => 'Button',
    'status' => 'ready',
    'description' => 'Standard button component used throughout the application',
    'args' => [
        'type' => 'primary',
        'label' => 'Click me',
        'disabled' => false,
        'size' => 'md'
    ],
    'argTypes' => [
        'type' => [
            'control' => 'select',
            'options' => ['primary', 'secondary', 'danger', 'warning', 'success', 'info'],
            'description' => 'Button style variant'
        ],
        'disabled' => [
            'control' => 'boolean',
            'description' => 'Whether the button is disabled'
        ],
        'size' => [
            'control' => 'select',
            'options' => ['sm', 'md', 'lg', 'xl'],
            'description' => 'Button size'
        ]
    ],
])

<x-button
    :type="$type"
    :disabled="$disabled"
    :size="$size"
>
    {{ $label }}
</x-button>
```

### 5. Sample Task Card Component Story

Create a story for the Task Card component:

File location: `resources/views/stories/components/tasks/task-card.blade.php`

```blade
@storybook([
    'name' => 'Task Card',
    'status' => 'ready',
    'description' => 'Card displaying task information with action buttons',
    'args' => [
        'title' => 'Complete the project',
        'description' => 'Finish the new homepage with all requested features',
        'priority' => 3,
        'due_date' => '2025-04-15',
        'completed' => false,
        'tags' => ['UI', 'Frontend'],
    ],
    'argTypes' => [
        'title' => [
            'control' => 'text',
            'description' => 'Task title'
        ],
        'description' => [
            'control' => 'text',
            'description' => 'Task description'
        ],
        'priority' => [
            'control' => 'select',
            'options' => [1, 2, 3, 4],
            'description' => 'Task priority (1=Low, 2=Medium, 3=High, 4=Urgent)'
        ],
        'due_date' => [
            'control' => 'date',
            'description' => 'Task due date'
        ],
        'completed' => [
            'control' => 'boolean',
            'description' => 'Whether the task is completed'
        ],
        'tags' => [
            'control' => 'object',
            'description' => 'Array of tags associated with the task'
        ],
    ],
])

<!-- Use our actual task-card component -->
<x-tasks.task-card
    :title="$title"
    :description="$description"
    :priority="$priority"
    :due-date="$due_date"
    :completed="$completed"
    :tags="$tags"
/>
```

### 6. Documenting Design Tokens

Create a color tokens documentation page:

File location: `resources/views/stories/pages/design-tokens/colors.blade.php`

```blade
@storybook([
    'name' => 'Colors',
    'displayType' => 'tokens',
    'description' => 'Color palette used throughout the application',
])

@colorTokens([
    'colors' => [
        'primary' => [
            '50' => '#f0f9ff',
            '100' => '#e0f2fe',
            '200' => '#bae6fd',
            '300' => '#7dd3fc',
            '400' => '#38bdf8',
            '500' => '#0ea5e9',
            '600' => '#0284c7',
            '700' => '#0369a1',
            '800' => '#075985',
            '900' => '#0c4a6e',
            '950' => '#082f49',
        ],
        'danger' => [
            '50' => '#fef2f2',
            '100' => '#fee2e2',
            '200' => '#fecaca',
            '300' => '#fca5a5',
            '400' => '#f87171',
            '500' => '#ef4444',
            '600' => '#dc2626',
            '700' => '#b91c1c',
            '800' => '#991b1b',
            '900' => '#7f1d1d',
            '950' => '#450a0a',
        ],
        // Include other color tokens as needed
    ]
])
```

### 7. Launch Storybook

Start the Storybook instance:

```bash
php artisan blast:launch
```

This will:
1. Install necessary dependencies
2. Generate stories from your component directories
3. Start Storybook on http://localhost:6006

### 8. Continuous Integration

To integrate with CI/CD, add the following to your deployment script:

```bash
# Generate static Storybook site for production
php artisan blast:generate-stories
php artisan blast:build --static --output=public/storybook

# This will generate a static Storybook site in public/storybook
# which can be accessed at your-site.com/storybook
```

## Component Documentation Standards

When creating component stories, follow these standards:

1. **Clear Naming**: Use clear, descriptive names for all components
2. **Complete Props Documentation**: Document all available props
3. **Show Variants**: Demonstrate all component variants and states
4. **Accessibility Notes**: Include a11y considerations
5. **Component Status**: Mark development status (WIP, Ready, Deprecated)

## Benefits for Our Project

Implementing Blast Storybook in Todo.prus.dev provides:

1. **Consistent UI**: Ensure UI consistency across components
2. **Developer Onboarding**: Quicker onboarding for new developers
3. **Visual Regression Testing**: Easily spot UI regressions
4. **UI/UX Collaboration**: Easier collaboration between designers and developers
5. **Component Reuse**: Encourage reuse of existing components
6. **Documentation**: Self-documenting component library 