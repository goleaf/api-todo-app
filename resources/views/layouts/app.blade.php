<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="A pure API-based task management application">
    
    <title>{{ config('app.name', 'API Task Manager') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
    
    <!-- Styles -->
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #8b5cf6;
            margin-bottom: 1rem;
        }
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4b5563;
            margin: 2rem 0 1rem;
            text-align: left;
        }
        p {
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #8b5cf6;
            color: #ffffff;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #7c3aed;
        }
        .endpoints {
            margin-top: 2rem;
            text-align: left;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        .endpoint-group {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .endpoint-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .endpoint {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #e5e7eb;
        }
        .endpoint:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .method {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 0.5rem;
        }
        .get {
            background-color: #10b981;
            color: #ffffff;
        }
        .post {
            background-color: #3b82f6;
            color: #ffffff;
        }
        .put {
            background-color: #f59e0b;
            color: #ffffff;
        }
        .delete {
            background-color: #ef4444;
            color: #ffffff;
        }
        .patch {
            background-color: #8b5cf6;
            color: #ffffff;
        }
        .path {
            font-family: monospace;
            font-weight: 500;
        }
        .description {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .architecture {
            text-align: left;
            margin: 2rem 0;
            padding: 1.5rem;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .architecture ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
    <x-rich-text::styles theme="richtextlaravel" data-turbo-track="false" />
</head>
<body>
    @yield('content')
    
    @if(!request()->is('admin*') && !request()->is('*error*'))
    <div class="container">
        <h1>{{ config('app.name', 'Task Manager') }} API</h1>
        <h2>Pure API-based Task Management Application</h2>
        <p>This application provides a RESTful API for task management. There is no frontend UI - all interactions must be performed through API calls.</p>
        <a href="/api/documentation" class="btn">API Documentation</a>
        
        <div class="architecture">
            <h3>API Architecture</h3>
            <p>This application is built as a 100% API-based solution with:</p>
            <ul>
                <li>Custom API Controllers without Laravel defaults</li>
                <li>Request validation via dedicated classes with custom messages</li>
                <li>JSON responses with consistent structure</li>
                <li>Token-based authentication with Laravel Sanctum</li>
                <li>Role-based access control (admin/user)</li>
                <li>Comprehensive test coverage</li>
            </ul>
        </div>

        <div class="endpoints">
            <h3>API Endpoints:</h3>
            
            <div class="endpoint-group">
                <h4>Authentication</h4>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/register</span>
                    <div class="description">Register a new user</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/login</span>
                    <div class="description">Login to get an API token</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/logout</span>
                    <div class="description">Invalidate the current API token</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/me</span>
                    <div class="description">Get authenticated user info</div>
                </div>
            </div>
            
            <div class="endpoint-group">
                <h4>Users</h4>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/users</span>
                    <div class="description">List all users (admin only)</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/users</span>
                    <div class="description">Create a new user (admin only)</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/users/{id}</span>
                    <div class="description">Get user details</div>
                </div>
                
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/api/users/{id}</span>
                    <div class="description">Update a user</div>
                </div>
                
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/api/users/{id}</span>
                    <div class="description">Delete a user</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/users/statistics</span>
                    <div class="description">Get user statistics</div>
                </div>
            </div>
            
            <div class="endpoint-group">
                <h4>Profile</h4>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/profile</span>
                    <div class="description">Get user profile</div>
                </div>
                
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/api/profile</span>
                    <div class="description">Update user profile</div>
                </div>
                
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/api/profile/password</span>
                    <div class="description">Update user password</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/profile/photo</span>
                    <div class="description">Upload profile photo</div>
                </div>
                
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/api/profile/photo</span>
                    <div class="description">Delete profile photo</div>
                </div>
            </div>
            
            <div class="endpoint-group">
                <h4>Tasks</h4>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/tasks</span>
                    <div class="description">List all tasks</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/tasks</span>
                    <div class="description">Create a new task</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/tasks/{id}</span>
                    <div class="description">Get task details</div>
                </div>
                
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/api/tasks/{id}</span>
                    <div class="description">Update a task</div>
                </div>
                
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/api/tasks/{id}</span>
                    <div class="description">Delete a task</div>
                </div>
                
                <div class="endpoint">
                    <span class="method patch">PATCH</span>
                    <span class="path">/api/tasks/{id}/toggle</span>
                    <div class="description">Toggle task completion status</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/tasks/statistics</span>
                    <div class="description">Get task statistics</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/tasks/due-today</span>
                    <div class="description">Get tasks due today</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/tasks/overdue</span>
                    <div class="description">Get overdue tasks</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/tasks/upcoming</span>
                    <div class="description">Get upcoming tasks</div>
                </div>
            </div>
            
            <div class="endpoint-group">
                <h4>Categories</h4>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/categories</span>
                    <div class="description">List all categories</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/categories</span>
                    <div class="description">Create a new category</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/categories/{id}</span>
                    <div class="description">Get category details</div>
                </div>
                
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/api/categories/{id}</span>
                    <div class="description">Update a category</div>
                </div>
                
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/api/categories/{id}</span>
                    <div class="description">Delete a category</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/categories/task-counts</span>
                    <div class="description">Get task counts by category</div>
                </div>
            </div>
            
            <div class="endpoint-group">
                <h4>Dashboard</h4>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/dashboard</span>
                    <div class="description">Get dashboard statistics</div>
                </div>
            </div>
            
            <div class="endpoint-group">
                <h4>Async Operations</h4>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/async/dashboard-stats</span>
                    <div class="description">Get dashboard statistics asynchronously</div>
                </div>
                
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/api/async/external-apis</span>
                    <div class="description">Fetch data from external APIs asynchronously</div>
                </div>
                
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/api/async/process-tasks</span>
                    <div class="description">Process tasks in bulk asynchronously</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</body>
</html> 