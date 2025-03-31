# Task Manager

A comprehensive task management application built with Laravel and Livewire.

## Features

- **Task Management**: Create, read, update, and delete tasks with features for due dates, priorities, and categories
- **TaskMVC Component**: Implementation of the classic TodoMVC reference application
- **Bulk Task Processing**: Process multiple tasks at once with our Hypervel-powered bulk processor
- **Calendar View**: Visualize tasks in a calendar interface
- **Dashboard**: Get an overview of task statistics and recent activities
- **Responsive Design**: Works on desktop and mobile devices
- **Real-time Updates**: Powered by Livewire for a dynamic user experience
- **Async Processing**: Uses Hypervel for high-performance asynchronous operations

## Tech Stack

- **Laravel**: Backend PHP framework
- **Livewire**: Full-stack framework for dynamic interfaces without writing JavaScript
- **Hypervel**: Asynchronous processing library for Laravel
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework
- **MySQL**: Database system

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/your-username/task-manager.git
   cd task-manager
   ```

2. Install dependencies:
   ```
   composer install
   npm install
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

6. Build assets:
   ```
   npm run dev
   ```

7. Start the server:
   ```
   php artisan serve
   ```

## Usage

### Task Management

Create, view, edit, and delete tasks through an intuitive interface. Tasks can be assigned priorities, due dates, and categories.

### TaskMVC

Experience the classic TodoMVC reference application implemented with Laravel Livewire at `/taskmvc`.

### Bulk Task Processing

Process multiple tasks at once with our high-performance bulk processor powered by Hypervel at `/tasks/bulk`.

### Calendar View

Visualize your tasks in a calendar interface at `/calendar`.

## Documentation

- [Migration from Todo to Task](docs/TODO_TO_TASK_MIGRATION.md)
- [Hypervel Integration](docs/hypervel-integration.md)
- [Livewire Testing Guide](docs/livewire-testing-guide.md)
- [Real-time Notifications](docs/real-time-notifications.md)

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