# Coverage Comparison Guide

This guide explains how to use the Coverage Comparison Tool to track your migration progress from Vue.js to Livewire components.

## What This Tool Does

The Coverage Comparison Tool helps you:

1. Compare test coverage between Vue.js and Livewire components
2. Visualize the migration progress with charts and tables
3. Identify components that need more testing
4. Track improvements in test coverage

## Prerequisites

Before using this tool, you'll need:

1. A Vue.js test coverage report (generated with Jest)
2. A Livewire test coverage report (generated with PHPUnit)

## Generating Coverage Reports

### Vue.js Coverage Report

To generate a Vue.js test coverage report:

```bash
# Using Jest (typically in package.json scripts)
npm run test:coverage

# Or directly with Jest
npx jest --coverage
```

This will create a coverage report in the `coverage-vue` directory.

### Livewire Coverage Report

To generate a Livewire test coverage report:

```bash
# Generate coverage for all Livewire tests
php artisan test --filter=Livewire --coverage-html coverage

# Or for all tests
php artisan test --coverage-html coverage
```

This will create a coverage report in the `coverage` directory.

## Running the Comparison Tool

Once you have both coverage reports, run the comparison tool:

```bash
php scripts/coverage-compare.php [vue-coverage-path] [livewire-coverage-path]
```

The default paths are:
- Vue.js coverage: `coverage-vue/coverage-summary.json`
- Livewire coverage: `coverage/coverage-summary.json`

If your coverage reports are in different locations, specify the paths as arguments.

## Understanding the Report

The generated report (`coverage-comparison.html`) includes:

1. **Summary Statistics**: Overall coverage percentages for Vue.js and Livewire
2. **Coverage Details**: Breakdown by lines, statements, functions, and branches
3. **Component Coverage**: Visual comparison of coverage for each component group
4. **Charts**: Visual representation of the coverage data
5. **Recommendations**: Suggestions for improving coverage

## Interpreting Results

### Coverage Metrics

- **Lines**: Percentage of code lines executed during tests
- **Statements**: Percentage of statements executed
- **Functions**: Percentage of functions/methods called
- **Branches**: Percentage of conditional branches executed (if/else, switch, etc.)

### Component Status

Components are marked with one of the following statuses:

- **Completed**: Livewire tests are implemented and coverage is at or above Vue.js level
- **In Progress**: Livewire tests are partially implemented
- **Pending**: Livewire tests have not been implemented yet

### Recommendations

The report includes specific recommendations based on your coverage data, such as:

- Which components to focus on next
- How to improve overall coverage
- Strategies for writing better tests

## Regular Usage During Migration

For best results, run this tool regularly during your migration process:

1. Generate coverage reports for both Vue.js and Livewire components
2. Run the comparison tool
3. Analyze the results and prioritize your work accordingly
4. Implement more Livewire tests for components with low coverage
5. Repeat until all components have been migrated and adequately tested

## Troubleshooting

### Missing Coverage Reports

If the coverage reports are not found, the tool will use placeholder data. Make sure:

1. You've correctly generated the coverage reports
2. The paths to the coverage reports are correct

### Chart Display Issues

If the charts don't display correctly:

1. Ensure you're viewing the HTML file in a modern browser
2. Check that your browser allows loading scripts from CDN (Chart.js)
3. Verify there are no JavaScript errors in the browser console

## Command Reference

| Command | Description |
|---------|-------------|
| `php scripts/coverage-compare.php` | Generate a comparison report with default paths |
| `php scripts/coverage-compare.php path/to/vue-coverage.json path/to/livewire-coverage.json` | Generate a report with custom paths |

## Example Workflow

```bash
# Step 1: Generate Vue.js coverage
npm run test:coverage

# Step 2: Generate Livewire coverage
php artisan test --filter=Livewire --coverage-html coverage

# Step 3: Run the comparison tool
php scripts/coverage-compare.php

# Step 4: Open the report
open coverage-comparison.html
```

## Next Steps

After reviewing the coverage comparison:

1. Update your test implementation priorities based on coverage gaps
2. Focus on components with 0% Livewire coverage
3. Update the migration checklist based on your progress
4. Share the coverage report with your team to track migration progress 