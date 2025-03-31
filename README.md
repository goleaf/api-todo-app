# Task Manager API

A comprehensive API-only task management application built with Laravel. This application implements a full service-based architecture with thin controllers and robust request validation.

## Features

- **100% API-Based**: All functionality exposed through well-designed REST API endpoints
- **Service-Based Architecture**: Complete separation of business logic from controllers
- **Request Validation**: Dedicated request classes with custom error messages
- **Task Management**: Create, read, update, and delete tasks with features for due dates, priorities, and categories
- **Category Management**: Organize tasks with custom categories
- **User Management**: Complete user profile and authentication system
- **Dashboard API**: Get an overview of task statistics and recent activities

## Tech Stack

- **Laravel**: Backend PHP framework
- **Laravel Sanctum**: API authentication
- **MySQL**: Database system

## Core Components

- **Services Layer**: Where all business logic lives
- **Request Classes**: Handle validation with custom error messages
- **Thin Controllers**: Simply delegate to services
- **Language Files**: Store localized messages for responses
- **Standardized API Responses**: Consistent JSON format for all endpoints

## API Documentation

API documentation is available at `/api/documentation` when the application is running.

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/goleaf/api-todo-app.git
   cd api-todo-app
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Copy the environment file and set up your database:
   ```
   cp .env.example .env
   ```

4. Generate application key:
   ```
   php artisan key:generate
   ```

5. Run migrations and seed the database:
   ```
   php artisan migrate --seed
   ```

6. Start the server:
   ```
   php artisan serve
   ```

## API Usage

### Authentication

The API uses Laravel Sanctum for authentication.

```bash
# Register
POST /api/register
Content-Type: application/json

{
  "name": "Example User",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}

# Login
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

# Response contains token
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Example User",
      "email": "user@example.com"
    },
    "token": "YOUR_API_TOKEN"
  }
}
```

### Tasks

```bash
# List all tasks
GET /api/tasks
Authorization: Bearer YOUR_API_TOKEN

# Create a task
POST /api/tasks
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "title": "Complete project",
  "description": "Finish all required features",
  "due_date": "2025-04-15",
  "priority": 2,
  "category_id": 1
}

# Update a task
PUT /api/tasks/1
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "title": "Complete project phase 1",
  "priority": 1
}

# Delete a task
DELETE /api/tasks/1
Authorization: Bearer YOUR_API_TOKEN

# Toggle task completion
PATCH /api/tasks/1/toggle
Authorization: Bearer YOUR_API_TOKEN
```

### Categories

```bash
# List all categories
GET /api/categories
Authorization: Bearer YOUR_API_TOKEN

# Create a category
POST /api/categories
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "name": "Work",
  "color": "#ff5722",
  "icon": "briefcase"
}
```

### User & Profile

```bash
# Get current user profile
GET /api/profile
Authorization: Bearer YOUR_API_TOKEN

# Update profile
PUT /api/profile
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "name": "Updated Name",
  "email": "updated@example.com"
}

# Change password
PUT /api/profile/password
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "current_password": "current_password",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

## Testing

Run tests with PHPUnit:
```
php artisan test
```

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 