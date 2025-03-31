<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="A pure API-based task management application">

    <title>{{ config('app.name', 'API Task Manager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        body {
            font-family: 'Figtree', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #8b5cf6;
            margin: 0;
        }
        .api-badge {
            background-color: #8b5cf6;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .api-link {
            color: #8b5cf6;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .api-link:hover {
            text-decoration: underline;
        }
        .api-icon {
            width: 20px;
            height: 20px;
            display: inline-block;
        }
        .content {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .message {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }
        .message p {
            margin-bottom: 1.5rem;
        }
        .message .btn {
            display: inline-block;
            background-color: #8b5cf6;
            color: #ffffff;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .message .btn:hover {
            background-color: #7c3aed;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'Task Manager') }} <span class="api-badge">API</span></h1>
            <a href="/api/documentation" class="api-link">
                <svg class="api-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                API Documentation
            </a>
        </div>
        
        <!-- Page Content -->
        <div class="content">
            @if(isset($slot))
                {{ $slot }}
            @else
                <div class="message">
                    <p>This is an API-only application. Please use the API endpoints to interact with the system.</p>
                    <a href="/api/documentation" class="btn">View API Documentation</a>
                </div>
            @endif
        </div>
    </div>
</body>
</html> 