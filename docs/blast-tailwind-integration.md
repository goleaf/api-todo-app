# Integrating Blast Storybook with TailwindCSS

This guide explains how to integrate Blast Storybook UI with TailwindCSS in the Todo.prus.dev project, allowing you to document and visualize your Tailwind tokens and classes.

## Prerequisites

- Blast installed and configured
- TailwindCSS installed and configured with Vite or Laravel Mix

## Configuration

### 1. Update Blast Configuration

First, ensure your `config/blast.php` file is properly configured to use your Tailwind configuration file:

```php
<?php

return [
    // ... other configuration
    
    // Path to your Tailwind config file
    'tailwind_config' => base_path('tailwind.config.js'),
    
    // ... rest of configuration
];
```

### 2. Configure Vite for Blast

Update your `vite.config.js` file to ensure Blast can access your compiled CSS:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // Make sure to build CSS when running Blast
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
    },
});
```

## Documenting Tailwind Tokens

### 1. Create Color Tokens Documentation

Create a file for documenting your color tokens:

File: `resources/views/stories/pages/design-tokens/colors.blade.php`

```blade
@storybook([
    'name' => 'Color Tokens',
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
        'secondary' => [
            // Secondary color values
        ],
        'neutral' => [
            // Neutral color values
        ],
        // Add other color categories
    ]
])
```

### 2. Create Typography Tokens Documentation

Create a file for documenting your typography settings:

File: `resources/views/stories/pages/design-tokens/typography.blade.php`

```blade
@storybook([
    'name' => 'Typography',
    'displayType' => 'tokens',
    'description' => 'Typography system used throughout the application',
])

@typographyTokens([
    'fontFamily' => [
        'sans' => ['Roboto', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
        'serif' => ['ui-serif', 'Georgia', 'serif'],
        'mono' => ['ui-monospace', 'SFMono-Regular', 'monospace'],
    ],
    'fontSize' => [
        'xs' => '0.75rem',
        'sm' => '0.875rem',
        'base' => '1rem',
        'lg' => '1.125rem',
        'xl' => '1.25rem',
        '2xl' => '1.5rem',
        '3xl' => '1.875rem',
        '4xl' => '2.25rem',
        '5xl' => '3rem',
    ],
    'fontWeight' => [
        'thin' => '100',
        'extralight' => '200',
        'light' => '300',
        'normal' => '400',
        'medium' => '500',
        'semibold' => '600',
        'bold' => '700',
        'extrabold' => '800',
        'black' => '900',
    ],
    'lineHeight' => [
        'none' => '1',
        'tight' => '1.25',
        'snug' => '1.375',
        'normal' => '1.5',
        'relaxed' => '1.625',
        'loose' => '2',
    ],
])
```

### 3. Create Spacing Tokens Documentation

Document your spacing values:

File: `resources/views/stories/pages/design-tokens/spacing.blade.php`

```blade
@storybook([
    'name' => 'Spacing',
    'displayType' => 'tokens',
    'description' => 'Spacing values used for margins, padding, and gaps',
])

@spacingTokens([
    'spacing' => [
        '0' => '0px',
        '1' => '0.25rem',
        '2' => '0.5rem',
        '3' => '0.75rem',
        '4' => '1rem',
        '5' => '1.25rem',
        '6' => '1.5rem',
        '8' => '2rem',
        '10' => '2.5rem',
        '12' => '3rem',
        '16' => '4rem',
        '20' => '5rem',
        '24' => '6rem',
        '32' => '8rem',
        '40' => '10rem',
        '48' => '12rem',
        '56' => '14rem',
        '64' => '16rem',
    ],
])
```

## Creating Tailwind Component Examples

### 1. Button Component with Tailwind Classes

File: `resources/views/stories/components/ui/button-tailwind.blade.php`

```blade
@storybook([
    'name' => 'Button (Tailwind)',
    'status' => 'ready',
    'description' => 'Button component styled with Tailwind classes',
    'args' => [
        'variant' => 'primary',
        'size' => 'md',
        'rounded' => true,
        'label' => 'Click me',
        'disabled' => false,
    ],
    'argTypes' => [
        'variant' => [
            'control' => 'select',
            'options' => ['primary', 'secondary', 'danger', 'warning', 'success'],
            'description' => 'Button color variant'
        ],
        'size' => [
            'control' => 'select',
            'options' => ['sm', 'md', 'lg', 'xl'],
            'description' => 'Button size'
        ],
        'rounded' => [
            'control' => 'boolean',
            'description' => 'Whether to use rounded corners'
        ],
        'disabled' => [
            'control' => 'boolean',
            'description' => 'Whether the button is disabled'
        ],
    ],
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

$variantClasses = [
    'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
    'secondary' => 'bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'warning' => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-400',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
];

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-base',
    'lg' => 'px-5 py-2.5 text-lg',
    'xl' => 'px-6 py-3 text-xl',
];

$roundedClasses = $rounded ? 'rounded-md' : 'rounded-none';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';

$classes = $baseClasses . ' ' . 
           $variantClasses[$variant] . ' ' . 
           $sizeClasses[$size] . ' ' . 
           $roundedClasses . ' ' . 
           $disabledClasses;
@endphp

<button 
    type="button" 
    class="{{ $classes }}"
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $label }}
</button>
```

### 2. Card Component with Tailwind Classes

File: `resources/views/stories/components/ui/card-tailwind.blade.php`

```blade
@storybook([
    'name' => 'Card (Tailwind)',
    'status' => 'ready',
    'description' => 'Card component styled with Tailwind classes',
    'args' => [
        'title' => 'Card Title',
        'content' => 'This is the card content that demonstrates a simple card component using Tailwind CSS classes.',
        'hasFooter' => true,
        'elevation' => 'md',
        'padding' => 'normal',
    ],
    'argTypes' => [
        'title' => [
            'control' => 'text',
            'description' => 'Card title text'
        ],
        'content' => [
            'control' => 'text',
            'description' => 'Card content text'
        ],
        'hasFooter' => [
            'control' => 'boolean',
            'description' => 'Whether to show a footer section'
        ],
        'elevation' => [
            'control' => 'select',
            'options' => ['none', 'sm', 'md', 'lg', 'xl'],
            'description' => 'Card shadow elevation'
        ],
        'padding' => [
            'control' => 'select',
            'options' => ['tight', 'normal', 'loose'],
            'description' => 'Card padding'
        ],
    ],
])

@php
$elevationClasses = [
    'none' => '',
    'sm' => 'shadow-sm',
    'md' => 'shadow-md',
    'lg' => 'shadow-lg',
    'xl' => 'shadow-xl',
];

$paddingClasses = [
    'tight' => 'p-3',
    'normal' => 'p-5',
    'loose' => 'p-8',
];

$cardClasses = 'bg-white rounded-lg border border-gray-200 ' . 
               $elevationClasses[$elevation];

$bodyClasses = $paddingClasses[$padding];
$headerClasses = 'border-b border-gray-200 mb-4 pb-2';
$footerClasses = 'border-t border-gray-200 mt-4 pt-4 flex justify-end';
@endphp

<div class="{{ $cardClasses }}">
    <div class="{{ $bodyClasses }}">
        <div class="{{ $headerClasses }}">
            <h3 class="text-xl font-semibold text-gray-800">
                {{ $title }}
            </h3>
        </div>
        
        <div class="text-gray-600">
            {{ $content }}
        </div>
        
        @if($hasFooter)
        <div class="{{ $footerClasses }}">
            <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors mr-2">
                Cancel
            </button>
            <button class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                Save
            </button>
        </div>
        @endif
    </div>
</div>
```

## Auto-Documenting Tailwind Configuration

To auto-document your Tailwind configuration, create a script that reads your Tailwind config file and generates documentation:

File: `resources/views/stories/data/tailwind-config.php`

```php
<?php

// This file reads the Tailwind config and provides data for Storybook

$tailwindConfig = json_decode(file_get_contents(base_path('tailwind.config.js')), true);

return [
    'theme' => $tailwindConfig['theme'] ?? [],
    'colors' => $tailwindConfig['theme']['colors'] ?? [],
    'spacing' => $tailwindConfig['theme']['spacing'] ?? [],
    'fontFamily' => $tailwindConfig['theme']['fontFamily'] ?? [],
    'fontSize' => $tailwindConfig['theme']['fontSize'] ?? [],
    'borderRadius' => $tailwindConfig['theme']['borderRadius'] ?? [],
    'boxShadow' => $tailwindConfig['theme']['boxShadow'] ?? [],
];
```

## Showcasing Responsive Design

Create stories that demonstrate responsive behavior:

File: `resources/views/stories/components/ui/responsive-container.blade.php`

```blade
@storybook([
    'name' => 'Responsive Container',
    'status' => 'ready',
    'description' => 'Container that demonstrates responsive behavior with Tailwind breakpoints',
    'args' => [
        'content' => 'This container changes its appearance at different screen sizes. Resize the viewport to see the changes.',
    ],
])

<div class="mx-auto max-w-7xl">
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 sm:p-6 md:p-8">
            <!-- Responsive padding -->
            
            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4">
                <!-- Responsive typography -->
                Responsive Component
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Responsive grid -->
                <div class="bg-blue-100 p-4 rounded">Item 1</div>
                <div class="bg-blue-100 p-4 rounded">Item 2</div>
                <div class="bg-blue-100 p-4 rounded md:col-span-2 lg:col-span-1">Item 3</div>
                <div class="bg-blue-100 p-4 rounded">Item 4</div>
            </div>
            
            <p class="mt-4 text-sm sm:text-base text-gray-700">
                {{ $content }}
            </p>
            
            <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                <!-- Responsive layout -->
                <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Submit</button>
            </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200">
            <p class="text-xs sm:text-sm text-gray-500">
                <!-- Responsive footer text -->
                Screen size indicators: 
                <span class="inline sm:hidden md:hidden lg:hidden xl:hidden">XS (default)</span>
                <span class="hidden sm:inline md:hidden lg:hidden xl:hidden">SM</span>
                <span class="hidden sm:hidden md:inline lg:hidden xl:hidden">MD</span>
                <span class="hidden sm:hidden md:hidden lg:inline xl:hidden">LG</span>
                <span class="hidden sm:hidden md:hidden lg:hidden xl:inline">XL</span>
            </p>
        </div>
    </div>
</div>
```

## Building and Deploying

To build a static version of your Storybook with Tailwind:

1. Build your CSS assets:

```bash
npm run build
# or 
yarn build
```

2. Build Blast Storybook:

```bash
php artisan blast:build --static --output=public/storybook
```

This will create a static version of your Storybook with all Tailwind styles applied, which you can deploy to any static hosting. 