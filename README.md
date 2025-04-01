# Task Manager Application

A modern task management application built with Laravel and Tabler.io UI.

## Features

- User Authentication
  - Login
  - Registration
  - Password Reset
  - Email Verification
- Task Management
  - Create, Read, Update, Delete Tasks
  - Task Categories
  - Task Tags
  - Time Tracking
- User Settings
  - Profile Management
  - Appearance Settings
  - Password Change
- Admin Panel
  - User Management
  - Category Management
  - Tag Management
  - Statistics Dashboard

## Installation

1. Clone the repository
```bash
git clone <repository-url>
cd task-manager
```

2. Install dependencies
```bash
composer install
npm install
```

3. Copy environment file and set up your environment variables
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Run migrations and seed the database
```bash
php artisan migrate:fresh --seed
```

6. Build assets
```bash
npm run build
```

7. Start the development server
```bash
php artisan serve
```

## Default Users

After seeding the database, you can log in with these default accounts:

- Admin User:
  - Email: admin@example.com
  - Password: adminpassword

- Demo User:
  - Email: demo@example.com
  - Password: demopassword

## Technologies Used

- Laravel 10.x
- Tabler.io UI
- MySQL
- Node.js & NPM
- Vite

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
