# Blast Storybook UI for Laravel Blade

## What is Blast?

Blast is a low-maintenance component library tool that integrates Storybook Server with Laravel Blade. Created by AREA 17, Blast allows you to render examples of your app's components using the Blade templating engine within your Laravel application. It provides an efficient way to develop, test, and document your UI components.

## Key Features

- **Low Maintenance Setup**: Quick installation (less than 90s in existing projects)
- **Blade Integration**: Native support for Laravel Blade templates
- **Controls Tab**: Test components with different props and configurations
- **Accessibility Testing**: Built-in a11y testing tools
- **Design Documentation**: Document design systems and Tailwind tokens
- **Component Status Tracking**: Track component development status
- **Documentation Pages**: Create global documentation for your component library

## Installation

1. Install Blast via Composer:
```bash
composer require area17/blast
```

2. Publish the configuration file:
```bash
php artisan vendor:publish --provider="A17\Blast\BlastServiceProvider" --tag="blast-config"
```

3. Configure your app's assets in `config/blast.php` as needed.

## Configuration

The `config/blast.php` file allows you to customize:

- Asset paths and compilations
- Story generation settings
- Component directories
- Tailwind configuration
- Component statuses

## Starting Storybook

To start Blast Storybook:

```bash
php artisan blast:launch
```

This command will:
- Install all required dependencies
- Generate stories based on your existing components
- Start a Storybook instance
- Start a watch task to update stories when files change

### Launch Options

- `--install`: Force install dependencies
- `--noGenerate`: Skip auto-generating stories from existing components
- `--port`: Specify a port to run Storybook (default: 6006)

## Generating Stories

You can also generate stories separately:

```bash
php artisan blast:generate-stories
```

### Generation Options

- `--watch`: Watch story files and regenerate on changes

## Directory Structure

Blast uses the following directory structure:

```
resources/
└── views/
    └── stories/
        ├── data/            # PHP files for story data
        ├── components/      # Component stories
        └── pages/           # Documentation pages
```

## Creating Component Stories

To create a story for a component, create a Blade file in the appropriate directory with the `@storybook` directive:

```blade
@storybook([
    'name' => 'Button',
    'args' => [
        'type' => 'primary',
        'label' => 'Click me',
        'disabled' => false
    ],
    'argTypes' => [
        'type' => [
            'control' => 'select',
            'options' => ['primary', 'secondary', 'danger'],
            'description' => 'Button style variant'
        ],
        'disabled' => [
            'control' => 'boolean',
            'description' => 'Whether the button is disabled'
        ]
    ],
])

<x-button
    :type="$type"
    :disabled="$disabled"
>
    {{ $label }}
</x-button>
```

## Documenting Tailwind Tokens

Blast can automatically visualize your Tailwind tokens for documentation:

```blade
@storybook([
    'name' => 'Colors',
    'displayType' => 'tokens'
])

@colorTokens([
    'colors' => [
        'primary' => [
            '50' => '#f0f9ff',
            '100' => '#e0f2fe',
            // ... other color values
        ],
        'secondary' => [
            // ... color values
        ]
    ]
])
```

## Best Practices

1. **Create Stories for All Components**: Document every reusable component
2. **Include Variants**: Show different states and configurations
3. **Document Props**: Use the `argTypes` to document all component props
4. **Add Accessibility Notes**: Include a11y considerations for each component
5. **Use Consistent Naming**: Follow a consistent naming pattern for stories

## Integration with CI/CD

You can integrate Blast Storybook into your CI/CD pipeline:

```bash
# Example CI script
php artisan blast:generate-stories
php artisan blast:build --static
```

## Working with Existing Blade Components

For existing projects, Blast can auto-detect components in your specified directories. To include them:

1. Ensure your component directories are properly set in `config/blast.php`
2. Run `php artisan blast:launch` to generate stories automatically

## Resources

- [Official GitHub Repository](https://github.com/area17/blast)
- [Getting Started with Blast – Storybook for Laravel Blade](https://dev.to/area17/getting-started-with-blast-storybook-for-laravel-blade-c5c)
- [Auto-visualizing Tailwind Tokens and Documenting Design Systems Props with Blast](https://dev.to/area17)

## Troubleshooting

### Common Issues

1. **Storybook not starting**: Check Node.js version (requires v14+)
2. **Components not rendering**: Verify asset paths in `config/blast.php`
3. **CSS not loading**: Ensure your asset compilation is configured correctly
4. **Control panel not working**: Check for JavaScript errors in browser console

Contact the Blast team via GitHub issues for additional support. 