# Todo App Test Suite

This document outlines the test suite structure and instructions for running tests.

## Test Structure

The test suite is organized as follows:

```
tests/
├── Feature/                 # Feature tests for API endpoints and controllers
│   ├── AuthTest.php         # Tests for authentication functions
│   └── TodoApiTest.php      # Tests for Todo API endpoints
├── Unit/                    # Unit tests for models and isolated components  
│   ├── BottomNavigationTest.php  # Tests for bottom navigation 
│   ├── TodoRequestTest.php  # Tests for form requests validation
│   ├── TodoTest.php         # Tests for Todo model
│   └── VueComponentTest.php # Tests for Vue components using Dusk
└── DuskTestCase.php         # Base class for browser tests
```

## Testing Tools

- **PHPUnit**: For PHP unit and feature testing
- **Laravel Dusk**: For browser testing
- **Vitest**: For Vue component testing
- **Laravel Pint**: For code style checking

## Running Tests

### PHP Tests

Run all PHP tests:
```bash
composer test
# or
php artisan test
```

Run only unit tests:
```bash
composer test:unit
# or
php artisan test --testsuite=Unit
```

Run only feature tests:
```bash
composer test:feature
# or
php artisan test --testsuite=Feature
```

### Browser Tests with Dusk

```bash
composer test:dusk
# or
php artisan dusk
```

### JavaScript Component Tests

```bash
npm test
```

### Code Style Checks

```bash
composer lint:check
# or
php ./vendor/bin/pint --test
```

To automatically fix code style issues:
```bash
composer lint
# or
php ./vendor/bin/pint
```

## Running All Tests

To run all tests at once:

```bash
composer test:all
```

## Continuous Integration

The test suite is set up to run automatically on each commit in GitHub Actions. 
The workflow includes:

1. Setting up the PHP environment
2. Installing dependencies
3. Setting up the database
4. Running migrations
5. Running PHP tests
6. Running Laravel Dusk browser tests
7. Running JavaScript tests with Vitest
8. Code style checking with Laravel Pint

## Writing New Tests

### PHP Tests

1. Create a new test file in the appropriate directory (Unit or Feature)
2. Extend the base TestCase class
3. Add test methods with prefixes like `test_` or use the `/** @test */` docblock
4. Run the tests to verify

### Vue Component Tests

1. Create a new test file with `.test.js` or `.spec.js` suffix
2. Use Vitest and Vue Test Utils for testing
3. Follow the examples in existing component tests
4. Run the tests to verify

## Test Coverage

To generate a test coverage report:

```bash
php artisan test --coverage
```

For a more detailed HTML report:

```bash
php artisan test --coverage-html=reports/coverage
``` 