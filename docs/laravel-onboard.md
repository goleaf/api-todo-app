# Laravel Onboard Integration

## Overview

This application integrates the Laravel Onboard package by Spatie to provide a structured onboarding experience for new users. The package helps track the completion of various onboarding steps and can direct users through a guided setup process.

## Features

- Track user progress through multiple onboarding steps
- Automatically redirect users to uncompleted steps
- Display visual progress indicators
- Configure multiple onboarding steps with conditions for completion
- Customize onboarding for different user types

## Implementation

### User Model Integration

The `User` model has been enhanced with the `GetsOnboarded` trait and implements the `Onboardable` interface:

```php
use Spatie\Onboard\Concerns\GetsOnboarded;
use Spatie\Onboard\Concerns\Onboardable;

class User extends Authenticatable implements Onboardable
{
    use HasApiTokens, HasFactory, Notifiable, GetsOnboarded;

    // ...rest of the User model...
}
```

### Onboarding Steps

The onboarding steps are configured in the `App\Providers\OnboardServiceProvider`. Each step includes:

1. A title 
2. A link to the step's page
3. A call-to-action text
4. A completion condition

```php
// Step 1: Complete Profile
Onboard::addStep('Complete Profile')
    ->link('/profile')
    ->cta('Complete')
    ->completeIf(function (User $model) {
        return $model->photo_path !== null;
    });

// Step 2: Create First Task
Onboard::addStep('Create Your First Task')
    ->link('/api/tasks')
    ->cta('Create Task')
    ->completeIf(function (User $model) {
        return $model->tasks()->count() > 0;
    });

// Additional steps...
```

### Middleware

A middleware named `RedirectToUnfinishedOnboardingStep` is available to automatically redirect users to their next uncompleted onboarding step:

```php
// Kernel.php
protected $middlewareAliases = [
    // ...
    'onboarding' => \App\Http\Middleware\RedirectToUnfinishedOnboardingStep::class,
];
```

Apply this middleware to routes where you want to enforce onboarding completion:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'onboarding'])->name('dashboard');
```

### Blade Component

The onboarding UI is implemented as a Blade component (`resources/views/components/onboarding.blade.php`) that displays:

- Progress bar showing overall completion
- List of onboarding steps with completion status
- Call-to-action buttons for incomplete steps
- Option to skip onboarding

### Routes

The application includes both API and web routes for onboarding:

#### API Routes

```php
Route::middleware('auth:sanctum')->prefix('onboarding')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('api.onboarding.index');
    Route::post('/skip', [OnboardingController::class, 'skip'])->name('api.onboarding.skip');
});
```

#### Web Routes

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');
});
```

## Usage Examples

### Checking Onboarding Status

```php
$user = Auth::user();

// Check if onboarding is in progress
if ($user->onboarding()->inProgress()) {
    // User has not completed all steps
}

// Get completion percentage
$percentage = $user->onboarding()->percentageCompleted();

// Check if onboarding is finished
if ($user->onboarding()->finished()) {
    // User has completed all steps
}
```

### Getting Onboarding Steps

```php
// Get all steps
$steps = $user->onboarding()->steps();

// Loop through steps
foreach ($steps as $step) {
    $title = $step->title;
    $link = $step->link;
    $cta = $step->cta;
    $isComplete = $step->complete();
    $isIncomplete = $step->incomplete();
}

// Get next unfinished step
$nextStep = $user->onboarding()->nextUnfinishedStep();
```

### Displaying in Templates

```blade
<x-onboarding :user="$user" />
```

## Advanced Configuration

### Excluding Steps

Steps can be excluded based on conditions:

```php
Onboard::addStep('Admin Setup')
    ->excludeIf(function (User $model) {
        return !$model->isAdmin();
    });
```

### User-Specific Steps

Steps can be limited to specific model classes:

```php
Onboard::addStep('Team Setup', Team::class)
    ->link('/teams/create');
```

### Custom Attributes

Steps can include custom attributes:

```php
Onboard::addStep('Custom Step')
    ->attributes([
        'priority' => 'high',
        'category' => 'account',
    ]);
```

## Conclusion

The Laravel Onboard integration provides a complete onboarding solution that guides users through their initial setup process. It's flexible enough to accommodate different types of users and can be customized to fit the specific needs of the application. 