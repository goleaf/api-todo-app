# Blast Storybook Quick Start Guide

This guide provides a quick way to get started with Blast Storybook in the Todo.prus.dev project.

## Prerequisites

- PHP 8.1+
- Composer
- Node.js 14+
- npm or Yarn

## Quick Installation

```bash
# Install Blast
composer require area17/blast

# Publish configuration
php artisan vendor:publish --provider="A17\Blast\BlastServiceProvider" --tag="blast-config"

# Create initial directories
mkdir -p resources/views/stories/{components,data,pages}

# Launch Storybook
php artisan blast:launch
```

## Creating Your First Component Story

1. Create a new file: `resources/views/stories/components/my-component.blade.php`

2. Add the following content:

```blade
@storybook([
    'name' => 'My First Component',
    'status' => 'wip',
    'args' => [
        'title' => 'Hello World',
        'showIcon' => true
    ],
    'argTypes' => [
        'title' => [
            'control' => 'text',
            'description' => 'The component title'
        ],
        'showIcon' => [
            'control' => 'boolean',
            'description' => 'Whether to show the icon'
        ]
    ],
])

<div class="bg-white p-4 rounded shadow">
    <h3 class="text-lg font-semibold">{{ $title }}</h3>
    @if($showIcon)
        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
    @endif
</div>
```

3. Save the file and Blast will automatically detect the changes and update the Storybook.

## Available Story Controls

Blast supports the following control types:

- `text`: For text input
- `number`: For numeric input
- `boolean`: For true/false toggle
- `select`: For dropdown selection
- `radio`: For radio button selection
- `date`: For date input
- `color`: For color selection
- `object`: For JSON object/array input

## Documentation Pages

Create a documentation page:

```blade
@storybook([
    'name' => 'Getting Started',
    'displayType' => 'page',
])

<h1 class="text-3xl font-bold">Getting Started</h1>

<p class="my-4">
    This is a documentation page for our component library. You can include:
</p>

<ul class="list-disc ml-5 my-4">
    <li>Usage guidelines</li>
    <li>Best practices</li>
    <li>Design principles</li>
    <li>Code examples</li>
</ul>

<pre class="bg-gray-100 p-4 rounded">
<code>
// Example code
const example = "This is an example";
console.log(example);
</code>
</pre>
```

## Component Status

Mark components with appropriate status:

- `wip`: Work in progress, not ready for production
- `ready`: Ready for use in production
- `deprecated`: Still functional but scheduled for removal

## Common Commands

```bash
# Start Storybook server
php artisan blast:launch

# Generate stories only
php artisan blast:generate-stories

# Generate stories and watch for changes
php artisan blast:generate-stories --watch

# Build a static Storybook
php artisan blast:build --static --output=public/storybook
```

## Troubleshooting

1. **Component not appearing in Storybook**:
   - Check that the file is in the correct location
   - Verify that the `@storybook` directive is properly formatted
   - Restart Blast with `php artisan blast:launch`

2. **CSS not loading**:
   - Verify the CSS paths in `config/blast.php`
   - Make sure Vite/Laravel Mix is compiling your assets correctly
   - Check browser console for errors

3. **Errors in story code**:
   - Check the browser console for JavaScript errors
   - Verify that all component props have valid values
   - Make sure referenced components exist

## Next Steps

1. Read the full [Blast Documentation](docs/blast-storybook.md)
2. Review the [Implementation Guide](docs/blast-implementation.md)
3. Explore the official [Blast GitHub Repository](https://github.com/area17/blast) 