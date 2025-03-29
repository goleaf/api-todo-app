# Todo Application

A modern task management application built with Laravel and Vue.js.

## Features

- User authentication (register, login, logout)
- Task management (create, read, update, delete)
- Task categorization with custom categories
- Task prioritization (low, medium, high)
- Due date tracking with overdue indicators
- Task completion tracking
- Dark/light mode toggle
- Responsive design for all devices

## Tech Stack

### Backend
- Laravel 10
- MySQL/SQLite database
- Laravel Sanctum for API authentication
- Repository pattern for data access

### Frontend
- Vue.js 3 with Composition API
- Vue Router for navigation
- Pinia for state management
- Tailwind CSS for styling
- Axios for API requests
- date-fns for date manipulation

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or SQLite

### Setup Steps

1. Clone the repository:
```bash
git clone https://github.com/yourusername/todo-app.git
cd todo-app
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the environment file and configure it:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Set up the database:
```bash
php artisan migrate
```

6. (Optional) Seed the database with test data:
```bash
php artisan db:seed
```

7. Install frontend dependencies:
```bash
npm install
```

8. Build the frontend assets:
```bash
npm run build
```

9. Start the development server:
```bash
php artisan serve
```

## Usage

After installation, you can access the application at `http://localhost:8000`.

### User Registration and Login
1. Create a new account using the registration form
2. Login with your credentials
3. You will be redirected to the dashboard

### Task Management
1. View all your tasks on the Tasks page
2. Create new tasks using the "Add Task" button
3. Edit tasks by clicking on them in the list
4. Mark tasks as complete using the checkbox
5. Filter tasks by status, category, or due date
6. Sort tasks by different criteria

## API Documentation

The application exposes a RESTful API for easy integration with other systems.

### Authentication Endpoints
- `POST /api/register` - Register a new user
- `POST /api/login` - Login a user
- `POST /api/logout` - Logout the current user (requires authentication)

### Task Endpoints
- `GET /api/tasks` - Get all tasks (requires authentication)
- `POST /api/tasks` - Create a new task (requires authentication)
- `GET /api/tasks/{id}` - Get a specific task (requires authentication)
- `PUT /api/tasks/{id}` - Update a task (requires authentication)
- `DELETE /api/tasks/{id}` - Delete a task (requires authentication)
- `POST /api/tasks/{id}/toggle-completion` - Toggle task completion status (requires authentication)

### Category Endpoints
- `GET /api/categories` - Get all categories (requires authentication)
- `POST /api/categories` - Create a new category (requires authentication)
- `GET /api/categories/{id}` - Get a specific category (requires authentication)
- `PUT /api/categories/{id}` - Update a category (requires authentication)
- `DELETE /api/categories/{id}` - Delete a category (requires authentication)

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

- Design inspiration from various Tailwind UI components
- Icons from Heroicons 