# Hypervel Integration

This application integrates [Hypervel](https://github.com/hypervel) to enable asynchronous and concurrent operations in Laravel. This document provides an overview of the integration and how to use the Hypervel service in your application.

## Overview

Hypervel is a package that enables running multiple operations concurrently in Laravel applications, improving performance for I/O-bound operations. This integration includes:

- `HypervelService` - A service that provides methods for running concurrent operations
- Configuration settings in `config/hypervel.php` 
- Exception handling with `HypervelException`
- Livewire components that leverage Hypervel
- API endpoints that use concurrent processing
- Benchmarking tools

## Installation and Setup

The Hypervel integration is ready to use out of the box. The `HypervelServiceProvider` automatically registers the service and configures it based on the settings in `config/hypervel.php`.

## Configuration

Configuration options are available in `config/hypervel.php`. You can publish the configuration file to your own config directory with:

```bash
php artisan vendor:publish --tag=hypervel-config
```

Key configuration options:

- `concurrency_limit`: Maximum number of concurrent operations (default: 25)
- `timeout`: Maximum time for operations in seconds (default: 30)
- `default_batch_size`: Default batch size for processing collections (default: 10)
- `debug`: Enable debug mode for additional logging (default: false)

## Using the HypervelService

The `HypervelService` provides several methods for running operations concurrently:

### Run Multiple Operations Concurrently

```php
use App\Services\HypervelService;

public function __construct(protected HypervelService $hypervelService) {}

public function getDashboardData()
{
    return $this->hypervelService->runConcurrently([
        'stats' => fn() => $this->getStats(),
        'recentTasks' => fn() => $this->getRecentTasks(),
        'upcomingDeadlines' => fn() => $this->getUpcomingDeadlines(),
    ]);
}
```

### Process Items in Batches

```php
$results = $this->hypervelService->runBatch($todos, function($todo) {
    return $this->processTask($todo);
}, $batchSize = 10);
```

### Run Operations with Retry Logic

```php
$result = $this->hypervelService->runWithRetry(function() {
    return $this->apiClient->fetchData();
}, $retries = 3, $delay = 100);
```

### Run HTTP Requests Concurrently

```php
$results = $this->hypervelService->runConcurrentHttpRequests([
    'users' => 'https://api.example.com/users',
    'posts' => 'https://api.example.com/posts',
]);
```

## Livewire Integration

The integration includes Livewire components that leverage Hypervel:

- `TodoBulkProcessor`: Process multiple todos concurrently
- `HypervelDashboard`: Load dashboard data concurrently

### Using in Livewire Components

```php
use App\Services\HypervelService;
use Livewire\Component;

class YourComponent extends Component
{
    public function __construct(protected HypervelService $hypervelService) {}
    
    public function loadData()
    {
        $this->data = $this->hypervelService->runConcurrently([
            'section1' => fn() => $this->loadSection1(),
            'section2' => fn() => $this->loadSection2(),
        ]);
    }
}
```

## Error Handling

Use `HypervelException` to handle errors from concurrent operations:

```php
use App\Exceptions\HypervelException;

try {
    $results = $this->hypervelService->runConcurrently([
        'data' => fn() => $this->fetchData(),
    ]);
} catch (HypervelException $e) {
    Log::error('Hypervel operation failed', [
        'message' => $e->getMessage(),
        'context' => $e->getContext(),
    ]);
    
    if ($e->getOriginalException()) {
        report($e->getOriginalException());
    }
}
```

## Benchmarking

The integration includes a benchmarking command to measure performance improvements:

```bash
php artisan hypervel:benchmark --todos=100 --iterations=5 --feature=dashboard
```

Available features to benchmark:
- `dashboard`: Dashboard data loading
- `batch`: Batch todo processing
- `api`: API operations

## Performance Considerations

- Hypervel works best for I/O-bound operations (database queries, API calls, file operations)
- For CPU-bound tasks, consider using queues instead
- Set an appropriate concurrency limit to avoid overloading the system
- Use benchmarks to measure actual performance improvements in your specific use case

## Testing

The integration includes tests for Hypervel functionality:

```bash
php artisan test --filter=TaskManagementTest
```

For more information on the underlying Hypervel package, refer to the [official documentation](https://github.com/hypervel).

This guide explains how to use Hyperscript with Livewire components (Hypervel) to add lightweight client-side interactivity during your Vue to Livewire migration.

## What is Hypervel?

"Hypervel" refers to the integration of Hyperscript with Laravel Livewire components. This combination provides:

- Lightweight client-side interactivity without the need for a full JavaScript framework
- Simple syntax for adding behaviors directly in your HTML
- Smooth transition path for teams moving from Vue.js to Livewire

## Why Use Hyperscript with Livewire?

When migrating from Vue.js to Livewire, you might encounter scenarios where:

1. You need simple client-side interactivity that doesn't warrant a full AJAX request
2. You want to avoid adding Alpine.js as another dependency
3. You prefer a declarative syntax similar to Vue's v-directives

Hyperscript provides a lightweight solution for these scenarios, with a syntax that feels familiar to developers used to Vue directives.

## Installation

### Step 1: Install Hyperscript

Add Hyperscript to your project using npm:

```bash
npm install hyperscript --save
```

Or include it directly in your HTML:

```html
<script src="https://unpkg.com/hyperscript.org@0.9.7"></script>
```

### Step 2: Configure Laravel Mix or Vite

If you're using Laravel Mix, update your `webpack.mix.js` file:

```javascript
mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       //
   ]);
```

Ensure that Hyperscript is imported in your `resources/js/app.js` file:

```javascript
import 'hyperscript.org';
```

If using Vite, update your `vite.config.js` file:

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

## Basic Usage with Livewire

### Example 1: Toggle Visibility

**Vue Component (Before):**
```html
<template>
  <div>
    <button @click="showDetails = !showDetails">Toggle Details</button>
    <div v-if="showDetails" class="details">
      {{ detailsText }}
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showDetails: false,
      detailsText: 'Here are the details...'
    }
  }
}
</script>
```

**Livewire Component with Hyperscript (After):**
```php
// TaskDetails.php
class TaskDetails extends Component
{
    public $detailsText = 'Here are the details...';
    
    public function render()
    {
        return view('livewire.task-details');
    }
}
```

```html
<!-- task-details.blade.php -->
<div>
    <button _="on click toggle .hidden on the next <div/>">Toggle Details</button>
    <div class="details hidden">
        {{ $detailsText }}
    </div>
</div>
```

### Example 2: Form Validation

**Vue Component (Before):**
```html
<template>
  <div>
    <input v-model="email" type="email" @blur="validateEmail">
    <p v-if="emailError" class="error">{{ emailError }}</p>
    <button @click="submitForm" :disabled="!!emailError">Submit</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      emailError: ''
    }
  },
  methods: {
    validateEmail() {
      if (!this.email.includes('@')) {
        this.emailError = 'Please enter a valid email';
      } else {
        this.emailError = '';
      }
    },
    submitForm() {
      // Submit form logic
    }
  }
}
</script>
```

**Livewire Component with Hyperscript (After):**
```php
// ContactForm.php
class ContactForm extends Component
{
    public $email = '';
    public $emailError = '';
    
    public function submitForm()
    {
        $this->validate([
            'email' => 'required|email',
        ]);
        
        // Submit form logic
    }
    
    public function render()
    {
        return view('livewire.contact-form');
    }
}
```

```html
<!-- contact-form.blade.php -->
<div>
    <input 
        wire:model.defer="email" 
        type="email" 
        _="on blur if !my.value.includes('@') 
            put 'Please enter a valid email' into #emailError.innerHTML
            add .text-red-500 to #emailError
            add @disabled to #submitBtn
           else
            put '' into #emailError.innerHTML
            remove .text-red-500 from #emailError
            remove @disabled from #submitBtn"
    >
    <p id="emailError" class="error"></p>
    <button id="submitBtn" wire:click="submitForm">Submit</button>
</div>
```

## Advanced Techniques

### Working with Livewire Events

You can use Hyperscript to dispatch Livewire events:

```html
<button _="on click call $wire.emit('refreshList')">Refresh List</button>
```

### Handling Livewire Loading States

Hyperscript can be used to enhance Livewire loading states:

```html
<button 
    wire:click="saveData"
    _="on click
        add .opacity-50 to me
        put 'Saving...' into my.innerHTML
        on wire:loading remove .opacity-50 from me
        on wire:loading:remove put 'Save' into my.innerHTML"
>
    Save
</button>
```

### Animations and Transitions

Hyperscript excels at adding smooth animations:

```html
<div 
    _="on wire:model.change(tasks)
        for <li/> in me that is new
            add .highlight to it
            wait 2s
            remove .highlight from it"
>
    @foreach($tasks as $task)
        <li>{{ $task->name }}</li>
    @endforeach
</div>
```

## Migration Patterns

### Pattern 1: Replace v-if/v-show with Hyperscript Toggle

**Vue:**
```html
<div v-if="isVisible">Content</div>
```

**Hypervel:**
```html
<div _="on load if !{{ $isVisible }} add .hidden to me">Content</div>
```

### Pattern 2: Replace Vue Methods with Hyperscript

**Vue:**
```html
<button @click="doSomething">Click Me</button>
```

**Hypervel:**
```html
<button _="on click call someFunction()">Click Me</button>
```

### Pattern 3: Replace Simple Computed Properties

**Vue:**
```html
<div>{{ firstName + ' ' + lastName }}</div>
```

**Hypervel:**
```html
<div _="on load put {{ $firstName }} + ' ' + {{ $lastName }} into my.innerHTML"></div>
```

## Best Practices

1. **Use Livewire for Data Management**: Let Livewire handle state management and server communication.

2. **Use Hyperscript for UI Behavior**: Use Hyperscript for animations, transitions, and simple DOM manipulations.

3. **Keep Scripts Simple**: If your Hyperscript becomes complex, consider using a Livewire method instead.

4. **Combine with Alpine.js When Needed**: For more complex client-side logic, use Alpine.js alongside Hyperscript.

5. **Leverage Hypermedia-Driven Approach**: Embrace the hypermedia approach where the server sends HTML that already includes the behavior it needs.

## Common Gotchas and Solutions

### 1. Scope Issues

**Problem:** Accessing variables across different elements.

**Solution:** Use Hyperscript's `get`, `set`, and `send` to communicate between elements:

```html
<button _="on click set global element to me then send showRelated to #related-items">Show Related</button>
<div id="related-items" _="on showRelated add .active to me"></div>
```

### 2. Event Handling Conflicts

**Problem:** Conflicts between Livewire and Hyperscript event handling.

**Solution:** Be specific about event targets and use Livewire's `wire:ignore` when needed:

```html
<div wire:ignore>
    <button _="on click call doSomething()">Action</button>
</div>
```

### 3. DOM Updates

**Problem:** Livewire rerenders may destroy Hyperscript behaviors.

**Solution:** Use `init` or `load` events to reattach behaviors after Livewire updates:

```html
<div _="on load init my behaviors">
    <!-- Content that may be re-rendered by Livewire -->
</div>
```

## Performance Considerations

- Hyperscript is lightweight (7kb minified and gzipped)
- It has minimal impact on initial page load
- For very complex applications, consider using Alpine.js instead

## Examples Repository

We've created a repository of common Hypervel patterns to help your migration:

[Hypervel Examples Repository](https://github.com/example/hypervel-examples)

## Conclusion

The Hypervel approach (Hyperscript + Livewire) provides a lightweight, elegant solution for adding client-side interactivity to your Livewire components. It's particularly useful during the migration from Vue.js, allowing you to address simple interactive behaviors without reaching for a heavier solution.

## Resources

- [Hyperscript Documentation](https://hyperscript.org/docs/)
- [Laravel Livewire Documentation](https://laravel-livewire.com/docs/)
- [Vue to Livewire Migration Guide](./vue-to-livewire-test-migration.md)
- [Livewire Testing Guide](./running-livewire-tests.md) 