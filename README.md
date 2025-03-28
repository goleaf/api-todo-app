# Todo App

A secure, real-time todo application built with Laravel and Vue.js with mobile app capabilities via Capacitor.

## Features

The Todo App includes a comprehensive set of features:

- **User Management**: Secure registration, authentication, and profile management
- **Task Management**: Complete CRUD operations with detailed task information
- **Categories**: Organize tasks with customizable categories and color coding
- **Priority Levels**: Assign Low, Medium, or High priority to tasks
- **Due Dates & Reminders**: Set and track due dates with reminder notifications
- **Progress Tracking**: Monitor completion percentage with visual progress bars
- **Dark Mode**: Toggle between light and dark themes with persistent preferences
- **Filters & Search**: Filter by status, category, and search by text
- **Mobile Optimized**: Responsive design that works on all devices
- **Accessibility**: WCAG 2.1 AA compliant with keyboard navigation and screen reader support

For a complete list of features, please see the [features.md](features.md) documentation.

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Vue.js
- **Authentication**: Laravel Sanctum
- **Real-time**: Laravel Broadcasting with Pusher
- **Mobile**: Capacitor
- **Styling**: Tailwind CSS
- **Testing**: PHPUnit, Laravel Dusk, Vitest

## Setup and Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js and NPM
- MySQL or SQLite

### Installation

1. Clone the repository
```bash
git clone <repository-url>
cd todoapp
```

2. Install PHP dependencies
```bash
composer install
```

3. Copy the .env.example file
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Configure your database in the .env file

6. Run migrations and seeders
```bash
php artisan migrate --seed
```

This will create:
- 10 users (admin@example.com, test@example.com, and 8 random users)
- Multiple todos for each user
- Multiple tasks for each user

You can refresh just the seed data without migrating using:
```bash
php artisan db:refresh-seeds
```

7. Install JavaScript dependencies
```bash
npm install
```

8. Build assets
```bash
npm run build
```

9. Set up Pusher (for real-time features)
   - Create an account on [Pusher](https://pusher.com/)
   - Update the .env file with your Pusher credentials:
   ```
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_APP_CLUSTER=your_app_cluster
   ```

### Running the Application

1. Start the Laravel server
```bash
php artisan serve
```

2. In a separate terminal, run the development server for assets
```bash
npm run dev
```

3. Access the application at http://localhost:8000

4. Login using one of the seeded accounts:
   - Email: admin@example.com
   - Password: password
   
   or
   
   - Email: test@example.com
   - Password: password

## Creating an Android App

1. Build the production assets
```bash
npm run build
```

2. Add the Android platform to Capacitor
```bash
npx cap add android
```

3. Sync the web assets to the Android project
```bash
npx cap sync
```

4. Open the Android project in Android Studio
```bash
npx cap open android
```

5. Build and run the Android app from Android Studio

## Documentation

The Todo App includes comprehensive documentation:

- [Features](features.md) - Detailed list of all application features
- [Changelog](changelog.md) - Record of all notable changes to the application
- [Testing Documentation](#todo-app-testing-documentation) - Information about the testing setup

## Security Features

- Token-based authentication with Laravel Sanctum
- HTTPS enforcement
- CSRF protection
- Validation rules for user input
- Proper authorization checks on all routes
- Data access controls with policy-based permissions

## Performance Optimizations

- Optimized database queries
- Cached responses where appropriate
- Efficient frontend asset loading
- Lazy-loaded components
- Pagination for large datasets

## License

[MIT License](LICENSE)

# Todo App Testing Documentation

This document outlines the testing setup for the Todo application.

## Testing Overview

The Todo App uses a comprehensive testing approach with:

- **Unit Tests**: Testing individual components and models
- **Feature Tests**: Testing application features and APIs
- **Browser Tests**: Testing UI interactions with Laravel Dusk
- **Component Tests**: Testing Vue components with Vitest

## Laravel Dusk Browser Tests

We've set up Laravel Dusk for browser testing in the application. The following Dusk test files have been created:

1. `tests/Browser/LoginTest.php` - Tests for user authentication functionality
2. `tests/Browser/TodoTest.php` - Tests for todo list viewing, creation, completion, and filtering
3. `tests/Browser/TodoDetailTest.php` - Tests for viewing and managing individual todo items

### Running Dusk Tests

To run the Dusk tests, use the following command:

```bash
php artisan dusk
```

Note: Dusk tests require Chrome to be installed on the system.

## PHPUnit Tests

PHPUnit tests are organized into:

1. `tests/Unit/` - Unit tests for models and relationships
2. `tests/Feature/` - Feature tests for controllers and APIs

To run PHPUnit tests:

```bash
vendor/bin/phpunit
```

## Vue Component Tests

Vue components are tested using Vitest:

```bash
npm run test
```

## Code Quality Tools

### PHP_CodeSniffer

We've configured PHP_CodeSniffer to check the code against PSR-2 coding standards.

Configuration file: `phpcs.xml`

To run PHP_CodeSniffer:

```bash
./vendor/bin/phpcs --standard=phpcs.xml [path]
```

### PHP-CS-Fixer

We've set up PHP-CS-Fixer for fixing code style issues automatically.

Configuration file: `.php-cs-fixer.php`

To run PHP-CS-Fixer:

```bash
./vendor/bin/php-cs-fixer fix [path] --diff --verbose
```

## CI/CD Integration

For continuous integration, the following workflow is recommended:

1. Run PHP_CodeSniffer to check code style
2. Run PHP-CS-Fixer to fix code style issues
3. Run PHPUnit tests to verify functionality
4. Run Laravel Dusk tests to verify browser behavior
5. Run Vue component tests with Vitest

## Known Issues

There appears to be a compatibility issue with the current versions of PHP dependencies. The following error occurs when running some commands:

```
Uncaught Error: Interface "PHPUnit\Event\Application\StartedSubscriber" not found
```

This can be addressed by downgrading PHPUnit to version 9.6 in composer.json.

## Next Steps

- Expand test coverage for additional features
- Set up a CI/CD pipeline to run tests automatically
- Implement the upcoming features listed in the [features.md](features.md) document 