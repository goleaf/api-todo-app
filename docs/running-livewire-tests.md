# Running Livewire Tests

This guide explains how to run the Livewire tests both locally and in the CI/CD pipeline.

## Running Tests Locally

### Prerequisites

- PHP 8.1 or higher
- Composer
- SQLite (for testing)

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/your-repo.git
   cd your-repo
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate an application key:
   ```bash
   php artisan key:generate
   ```

5. Configure the testing database in `.env.testing`:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=:memory:
   ```

### Running All Tests

To run all tests:

```bash
php artisan test
```

### Running Only Livewire Tests

To run only Livewire tests:

```bash
php artisan test --filter=Livewire
```

### Running Tests for a Specific Component

To run tests for a specific Livewire component:

```bash
# For example, to run TaskList component tests:
php artisan test --filter=TaskList

# Or to run all tests in the Tasks namespace:
php artisan test --filter=Tasks
```

### Getting Test Coverage Reports

To generate a code coverage report:

```bash
php artisan test --coverage-html coverage
```

This will create a coverage report in the `coverage` directory. Open `coverage/index.html` in your browser to view it.

## Understanding Our GitHub Actions Workflow

Our CI/CD pipeline uses GitHub Actions to automatically run tests on every push and pull request to main, master, and develop branches.

### Workflow File

The workflow is defined in `.github/workflows/run-tests.yml`.

### What It Does:

1. **Setup Environment**:
   - Sets up PHP 8.1 with necessary extensions
   - Uses SQLite for the database

2. **Caching**:
   - Caches Composer dependencies
   - Caches npm dependencies

3. **Dependencies**:
   - Installs Composer dependencies
   - Installs npm dependencies
   - Builds frontend assets

4. **Database**:
   - Creates a SQLite database
   - Runs migrations and seeds

5. **Run Tests**:
   - Runs all Livewire tests with `--filter=Livewire`
   - Runs all feature tests

6. **Coverage**:
   - Generates a code coverage report
   - Uploads the report as an artifact

### Viewing Test Results

After the workflow runs, you can:

1. See the test results in the GitHub Actions tab of your repository
2. Download the coverage report artifact from the workflow run

### Common Issues

1. **Tests Failing in CI but Passing Locally**:
   - Check if there are environment-specific issues
   - Ensure all required environment variables are set in the workflow
   - Check if there are path or permission issues

2. **Slow Test Suite**:
   - Consider using database transactions (`use RefreshDatabase;`)
   - Use the `--parallel` flag for parallel testing

3. **Coverage Issues**:
   - Ensure Xdebug is enabled in the workflow
   - Check if all test files are being discovered correctly

## Best Practices for Writing Livewire Tests

1. **Use Base Test Classes**: Extend from specialized test cases like `LivewireTestCase`, `LivewireFormTestCase`, or `LivewireAuthTestCase`.

2. **Use Helper Methods**: Leverage the helper methods provided by the base test cases to reduce boilerplate code.

3. **Test Real Database Operations**: Livewire tests work best when testing actual database operations rather than mocking them.

4. **Test Component Interactions**: Test that components properly emit and listen for events.

5. **Test Authorization**: Ensure components check permissions correctly before performing actions.

6. **Test Form Validation**: Thoroughly test form validation rules.

7. **Test File Uploads**: Use the techniques shown in `FileUploadTest.php` to test file uploads.

8. **Test Real-Time Features**: Test that components properly handle WebSocket events.

## Migrating More Tests

If you're still migrating tests from Vue to Livewire, refer to the `docs/vue-to-livewire-test-migration.md` file for a comprehensive guide on how to convert your tests. 