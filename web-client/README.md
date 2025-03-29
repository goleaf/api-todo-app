# Todo App Web Client

This is a simple standalone HTML/JavaScript frontend that demonstrates how to interact with the Todo API without requiring the full Vue.js application.

## Features

- User authentication (registration, login, logout)
- View tasks with filtering options
- Create, edit, and delete tasks
- Toggle task completion status
- Category support
- Due date and priority management
- Responsive design

## Usage

1. Simply open the `index.html` file in a web browser
2. Register a new account or log in with existing credentials
3. Start managing your tasks

## API Integration

This web client demonstrates how to:

- Authenticate with the Todo API
- Fetch, create, update, and delete tasks
- Toggle task completion
- Filter tasks by various criteria
- Manage task metadata (categories, due dates, priorities)

## Development

This is a single HTML file with embedded JavaScript and uses Tailwind CSS via CDN. No build process is required. Feel free to modify and enhance it as needed.

## API Endpoints

The web client interacts with the following API endpoints:

- Authentication:
  - `POST /api/register` - Register a new user
  - `POST /api/login` - Login
  - `POST /api/logout` - Logout
  - `GET /api/user` - Get current user

- Tasks:
  - `GET /api/tasks` - Get all tasks (with optional filters)
  - `POST /api/tasks` - Create a new task
  - `GET /api/tasks/{id}` - Get a specific task
  - `PUT /api/tasks/{id}` - Update a task
  - `DELETE /api/tasks/{id}` - Delete a task
  - `POST /api/tasks/{id}/toggle-complete` - Toggle task completion

- Categories:
  - `GET /api/categories` - Get all categories

## Default API URL

By default, this client connects to `https://todo.prus.dev/api` - modify the `API_URL` variable in the script if you need to connect to a different endpoint. 