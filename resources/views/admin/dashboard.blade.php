<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    Admin Dashboard
                </h1>
            </div>
        </header>

        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="text-2xl font-semibold mb-4">Welcome to the Todo App Admin Panel</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-blue-100 dark:bg-blue-900 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-xl font-semibold mb-2">Tasks</h3>
                                    <p class="text-3xl font-bold">12</p>
                                    <p class="text-sm mt-2">Total tasks in the system</p>
                                </div>
                                
                                <div class="bg-green-100 dark:bg-green-900 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-xl font-semibold mb-2">Users</h3>
                                    <p class="text-3xl font-bold">5</p>
                                    <p class="text-sm mt-2">Registered users</p>
                                </div>
                                
                                <div class="bg-purple-100 dark:bg-purple-900 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-xl font-semibold mb-2">Categories</h3>
                                    <p class="text-3xl font-bold">8</p>
                                    <p class="text-sm mt-2">Task categories</p>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <h3 class="text-xl font-semibold mb-4">Recent Activity</h3>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                                        <li class="py-3">User John created a new task "Complete project"</li>
                                        <li class="py-3">User Mary completed task "Write documentation"</li>
                                        <li class="py-3">User Admin created a new category "Urgent"</li>
                                        <li class="py-3">User Lisa edited task "Fix bugs"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
