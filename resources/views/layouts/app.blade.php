<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="A beautiful task management application">
    
    <title>{{ $title ?? config('app.name', 'Task Manager') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    @stack('scripts')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo and Nav Links -->
                    <div class="flex">
                        <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center">
                            <img class="h-8 w-auto" src="{{ asset('logo.svg') }}" alt="Task Manager Logo">
                            <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">Task Manager</span>
                        </a>
                        
                        <!-- Desktop Navigation -->
                        @auth
                        <div class="hidden space-x-8 ml-10 sm:flex">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-purple-600 dark:text-purple-400' : '' }}">
                                <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                            </a>
                            <a href="{{ route('tasks.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 text-sm font-medium {{ request()->routeIs('tasks.*') ? 'text-purple-600 dark:text-purple-400' : '' }}">
                                <i class="fas fa-tasks mr-1"></i> Tasks
                            </a>
                            <a href="{{ route('calendar') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 text-sm font-medium {{ request()->routeIs('calendar') ? 'text-purple-600 dark:text-purple-400' : '' }}">
                                <i class="fas fa-calendar-alt mr-1"></i> Calendar
                            </a>
                        </div>
                        @endauth
                    </div>
                    
                    <!-- User Menu -->
                    <div class="flex items-center">
                        @auth
                        <!-- Notifications Component -->
                        <div class="mr-2">
                            <livewire:notifications.task-notifications />
                        </div>
                        @endauth
                        
                        <!-- Dark Mode Toggle -->
                        <button id="theme-toggle" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 ml-2">
                            <i class="fas fa-sun text-yellow-500 dark:hidden"></i>
                            <i class="fas fa-moon text-blue-500 hidden dark:block"></i>
                        </button>
                        
                        @auth
                        <!-- Profile Dropdown -->
                        <div class="ml-3 relative">
                            <div>
                                <button type="button" id="user-menu-button" class="bg-white dark:bg-gray-800 p-1 rounded-full text-gray-400 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-purple-600 dark:bg-purple-500 text-white dark:text-gray-200 flex items-center justify-center text-sm font-medium">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                </button>
                            </div>
                            
                            <!-- User Dropdown Menu -->
                            <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="px-4 py-2 text-sm text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700">
                                    <div class="font-medium">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-user mr-1"></i> Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <!-- Login / Register Links -->
                        <div class="space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login
                            </a>
                            <a href="{{ route('register') }}" class="text-white bg-purple-600 hover:bg-purple-700 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-user-plus mr-1"></i> Register
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Menu (hidden by default) -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @auth
                <a href="{{ route('dashboard') }}" class="block text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
                <a href="{{ route('tasks.index') }}" class="block text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tasks.*') ? 'text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-tasks mr-1"></i> Tasks
                </a>
                <a href="{{ route('calendar') }}" class="block text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('calendar') ? 'text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-calendar-alt mr-1"></i> Calendar
                </a>
                @endauth
            </div>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow">
            <!-- Notification Flash Messages -->
            @if (session('success'))
            <div id="notification" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('notification').remove()">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif
            
            @if (session('error'))
            <div id="notification" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('notification').remove()">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Page Content -->
            {{ $slot }}
        </main>
        
        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} Task Manager. All rights reserved.
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-4">
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- User Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User menu toggle
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
            
            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Dark mode toggle
            const themeToggleBtn = document.getElementById('theme-toggle');
            
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    document.documentElement.classList.toggle('dark');
                    
                    // Save preference to localStorage
                    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
                    
                    // Dispatch custom event for Livewire components to listen to
                    document.dispatchEvent(new CustomEvent('dark-mode-toggle'));
                });
            }
        });
    </script>
</body>
</html> 