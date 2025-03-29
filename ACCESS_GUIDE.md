# Todo Application: Access Guide

This guide explains how to access and use the Todo application.

## Accessing the Application

### Production Environment

If the application is deployed to a production server, simply visit the domain where it's hosted:

```
https://todo.prus.dev/
```

### Development Environment

If you're running the application locally for development:

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Access the application in your browser:
   ```
   http://localhost:8000
   ```

## Using the Application

### Authentication

1. **Register a New Account**
   - Visit the registration page: `/register`
   - Fill in your name, email, and password
   - Click "Create account"

2. **Login**
   - Visit the login page: `/login`
   - Enter your email and password
   - Check "Remember me" if desired
   - Click "Sign in"

3. **Logout**
   - Click your user profile in the top-right corner
   - Select "Sign out" from the dropdown menu

### Task Management

1. **View Tasks**
   - After logging in, you'll be taken to the Dashboard
   - Click "Tasks" in the sidebar to view all tasks

2. **Create a New Task**
   - Click "Add Task" in the top-right corner of the Tasks page
   - Fill in the task details:
     - Title (required)
     - Description (optional)
     - Category
     - Due Date (optional)
     - Priority (Low, Medium, High)
   - Click "Save" to create the task

3. **Edit a Task**
   - Click on a task in the list to open the task details
   - Modify the task information
   - Click "Save" to update the task

4. **Complete a Task**
   - Click the checkbox next to a task in the list to mark it as complete
   - The task will be visually marked as completed

5. **Delete a Task**
   - Open the task details by clicking on the task
   - Click the "Delete" button
   - Confirm deletion in the modal dialog

### Filtering and Sorting

1. **Filter Tasks**
   - Use the filters at the top of the Tasks page:
     - Status (All, Completed, Pending, Overdue)
     - Category
     - Search (to find tasks by title or description)
     - Due Date

2. **Sort Tasks**
   - Click on column headers to sort by:
     - Title
     - Due Date
     - Priority

3. **Clear Filters**
   - Click "Clear Filters" to reset all filters

### Dashboard

The Dashboard provides an overview of your task status:

- Total Tasks
- Completed Tasks
- Pending Tasks  
- Overdue Tasks
- Recent Tasks (last 5 tasks)

### Dark Mode

- Toggle between light and dark mode by clicking the sun/moon icon in the top navigation bar

## Mobile Usage

The application is fully responsive and works on mobile devices:

1. Access the application using your mobile browser
2. Use the mobile-optimized interface for managing tasks on the go
3. Add the site to your home screen for a more app-like experience

## API Access

The application also provides a RESTful API for programmatic access:

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
- `POST /api/tasks/{id}/toggle-complete` - Toggle task completion status (requires authentication)

### Category Endpoints
- `GET /api/categories` - Get all categories (requires authentication)
- `POST /api/categories` - Create a new category (requires authentication)
- `GET /api/categories/{id}` - Get a specific category (requires authentication)
- `PUT /api/categories/{id}` - Update a category (requires authentication)
- `DELETE /api/categories/{id}` - Delete a category (requires authentication)

## Troubleshooting

If you encounter any issues:

1. **Login Problems**
   - Make sure you're using the correct email and password
   - Reset your password if necessary

2. **Page Not Loading**
   - Check your internet connection
   - Clear your browser cache
   - Try a different browser

3. **Task Changes Not Saving**
   - Verify that you're logged in
   - Check your internet connection
   - Try logging out and back in

4. **API Access Issues**
   - Ensure you're providing the correct authentication token
   - Check that your request is properly formatted

## Support

For additional help or to report issues, please contact support at support@example.com. 