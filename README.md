# Taskify - Task Management Application

Taskify is a modern, feature-rich task management application built with Laravel 12, Vue.js, and Livewire. It helps users organize, track, and manage their tasks efficiently with an intuitive and responsive interface.

## Features

### User Experience
- **User Greeting and Profile**: Personalized welcome with user name and avatar
- **Task List with Cards**: Interactive task cards with complete information
- **Task Categorization**: Organize tasks by custom categories
- **Progress Indicators**: Track completion percentage of tasks
- **Priority Badges**: Color-coded priority levels (Low, Medium, High)
- **Due Dates**: Set and track task deadlines
- **Task Completion Checkboxes**: Mark tasks as complete/incomplete
- **Floating Action Button (FAB)**: Quick access to add new tasks
- **Bottom Navigation**: Easy access to Home, Calendar, Stats, and Profile
- **Calendar Integration**: Visual calendar view of tasks by due date
- **Statistics Dashboard**: Track task completion metrics
- **Task Filtering**: Filter by category, status, and search terms
- **Minimalist Design**: Clean, modern interface with ample white space

### Technical Features
- **Real-time Updates**: Changes sync across devices via Pusher
- **Dark Mode Support**: Toggle between light and dark themes
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Authentication**: Secure user accounts and data
- **API Integration**: RESTful API for data access
- **Offline Support**: Progressive Web App capabilities
- **Performance Optimized**: Fast loading and responsive interactions
- **Test Helpers**: Standardized utilities for consistent testing

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js and NPM
- SQLite (for development)

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/taskify.git
   cd taskify
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy environment file and generate app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Set up the database:
   ```bash
   php artisan migrate --seed
   ```

6. Build frontend assets:
   ```bash
   npm run build
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

8. Visit `http://localhost:8000` in your browser

## Development

### Front-end Development
The front-end is built with Vue.js 3 and Tailwind CSS:

- Run watch mode during development:
  ```bash
  npm run dev
  ```

- Build for production:
  ```bash
  npm run build
  ```

### Testing
Run tests with:
```bash
php artisan test
```

For JavaScript tests:
```bash
npm run test
```

#### Test Helpers

The application includes dedicated test helper utilities to simplify testing and ensure consistency:

##### JavaScript Test Utilities
Located at `resources/js/tests/utils/testUtils.js`, these helpers simplify Vue component testing:

```js
// Setup component with all dependencies mocked in one line
const { wrapper, store, router } = setupComponentTest(MyComponent, {
  storeOptions: { isAuthenticated: true }
});

// Fill form inputs with a single function call
await fillForm(wrapper, {
  'input[type="email"]': 'test@example.com',
  'input[type="password"]': 'password123'
});
```

##### PHP Test Helpers
Located at `tests/TestHelpers.php`, these utilities provide consistent test data creation:

```php
// Create a test environment with one line
[$user, $todos] = TestHelpers::createTestEnvironment(3);

// Create a user with a guaranteed unique email
$user = TestHelpers::createUserWithUniqueEmail([
  'name' => 'Test User'
]);
```

Detailed documentation is available in `docs/test-helpers-guide.md`.

## Technologies Used

- **Backend**: Laravel 12, PHP 8.1+
- **Frontend**: Vue.js 3, Tailwind CSS
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Testing**: PHPUnit, Vitest
- **Realtime**: Pusher
- **Authentication**: Laravel Sanctum

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request 