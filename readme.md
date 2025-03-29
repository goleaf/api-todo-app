## Due Dates Functionality

Tasks now support due dates with the following features:

- Due dates are stored as timestamps in the database
- Due dates are displayed as "Month Day, Year" (e.g., "Jan 15, 2025") in orange text
- Overdue tasks are highlighted with red/orange styling
- Date picker is provided for input in the task creation and edit forms
- Due dates are optional - tasks can be created without them

### Tests

Tests for due date functionality are available in `tests/Feature/TaskTest.php`. To run these tests, you need to install PHPUnit:

```bash
composer require phpunit/phpunit --dev
./vendor/bin/phpunit --filter=TaskTest
``` 