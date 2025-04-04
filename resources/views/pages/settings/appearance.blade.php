@extends('layouts.app')

@section('title', 'Appearance Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Appearance Settings</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Customize how the application looks and feels
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Settings Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="p-4 font-medium text-gray-700 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700">
                    Settings
                </div>
                <nav class="p-2">
                    <a href="{{ route('settings.index') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('settings.index') ? 'bg-gray-100 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        General
                    </a>
                    <a href="{{ route('settings.profile.edit') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('settings.profile.*') ? 'bg-gray-100 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile
                    </a>
                    <a href="{{ route('settings.notifications') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('settings.notifications') ? 'bg-gray-100 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notifications
                    </a>
                    <a href="{{ route('settings.appearance') }}" 
                       class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('settings.appearance') ? 'bg-gray-100 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        Appearance
                    </a>
                </nav>
            </div>
        </div>

        <div class="col-span-1 md:col-span-3">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            Appearance Settings
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Customize how the application looks and feels for your account
                        </p>
                    </div>
                </div>

                <form action="{{ route('settings.appearance.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Theme Settings -->
                    <div class="space-y-4">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Theme</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="relative rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                                <input type="radio" name="theme" id="theme_light" value="light" 
                                    {{ $user->getSetting('theme', 'system') === 'light' ? 'checked' : '' }}
                                    class="sr-only">
                                <label for="theme_light" class="block cursor-pointer">
                                    <div class="aspect-w-16 aspect-h-9 bg-white">
                                        <div class="p-2">
                                            <div class="h-3 w-24 bg-gray-200 rounded mb-2"></div>
                                            <div class="h-3 w-32 bg-gray-200 rounded mb-2"></div>
                                            <div class="h-2 w-full bg-gray-100 rounded mt-4 mb-1"></div>
                                            <div class="h-2 w-full bg-gray-100 rounded mb-1"></div>
                                            <div class="h-2 w-3/4 bg-gray-100 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-200 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">Light</span>
                                            <div class="theme-check-icon {{ $user->getSetting('theme', 'system') === 'light' ? 'opacity-100' : 'opacity-0' }}">
                                                <svg class="h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="relative rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                                <input type="radio" name="theme" id="theme_dark" value="dark" 
                                    {{ $user->getSetting('theme', 'system') === 'dark' ? 'checked' : '' }}
                                    class="sr-only">
                                <label for="theme_dark" class="block cursor-pointer">
                                    <div class="aspect-w-16 aspect-h-9 bg-gray-900">
                                        <div class="p-2">
                                            <div class="h-3 w-24 bg-gray-700 rounded mb-2"></div>
                                            <div class="h-3 w-32 bg-gray-700 rounded mb-2"></div>
                                            <div class="h-2 w-full bg-gray-800 rounded mt-4 mb-1"></div>
                                            <div class="h-2 w-full bg-gray-800 rounded mb-1"></div>
                                            <div class="h-2 w-3/4 bg-gray-800 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-700 bg-gray-800">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-white">Dark</span>
                                            <div class="theme-check-icon {{ $user->getSetting('theme', 'system') === 'dark' ? 'opacity-100' : 'opacity-0' }}">
                                                <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="relative rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                                <input type="radio" name="theme" id="theme_system" value="system" 
                                    {{ $user->getSetting('theme', 'system') === 'system' ? 'checked' : '' }}
                                    class="sr-only">
                                <label for="theme_system" class="block cursor-pointer">
                                    <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-white to-gray-900">
                                        <div class="flex h-full">
                                            <div class="w-1/2 p-2">
                                                <div class="h-3 w-12 bg-gray-200 rounded mb-2"></div>
                                                <div class="h-3 w-16 bg-gray-200 rounded mb-2"></div>
                                            </div>
                                            <div class="w-1/2 p-2">
                                                <div class="h-3 w-12 bg-gray-700 rounded mb-2"></div>
                                                <div class="h-3 w-16 bg-gray-700 rounded mb-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">System</span>
                                            <div class="theme-check-icon {{ $user->getSetting('theme', 'system') === 'system' ? 'opacity-100' : 'opacity-0' }}">
                                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Choose a theme for the application. The "System" option will automatically switch between light and dark mode based on your device settings.
                        </p>
                    </div>
                    
                    <!-- Accent Color -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Accent Color</h3>
                        
                        <div class="grid grid-cols-6 sm:grid-cols-8 gap-3">
                            @php
                                $accentColors = [
                                    'indigo' => '#4f46e5',
                                    'purple' => '#9333ea',
                                    'pink' => '#db2777',
                                    'rose' => '#e11d48',
                                    'red' => '#dc2626',
                                    'orange' => '#ea580c',
                                    'amber' => '#d97706', 
                                    'yellow' => '#ca8a04',
                                    'lime' => '#65a30d',
                                    'green' => '#16a34a',
                                    'emerald' => '#059669',
                                    'teal' => '#0d9488',
                                    'cyan' => '#0891b2',
                                    'sky' => '#0284c7',
                                    'blue' => '#2563eb',
                                    'violet' => '#7c3aed'
                                ];
                                $currentAccentColor = $user->getSetting('accent_color', 'indigo');
                            @endphp
                            
                            @foreach($accentColors as $name => $hex)
                                <div>
                                    <input type="radio" name="accent_color" id="accent_{{ $name }}" value="{{ $name }}" 
                                        {{ $currentAccentColor === $name ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <label for="accent_{{ $name }}" 
                                        class="flex items-center justify-center w-full h-10 rounded-full cursor-pointer border-2 border-transparent peer-checked:border-gray-900 dark:peer-checked:border-white"
                                        style="background-color: {{ $hex }};">
                                        @if($currentAccentColor === $name)
                                            <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Display Preferences -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Display Preferences</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="tasks_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tasks per page
                                </label>
                                <select id="tasks_per_page" name="tasks_per_page" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach([10, 15, 25, 50, 100] as $value)
                                        <option value="{{ $value }}" {{ $user->getSetting('tasks_per_page', 15) == $value ? 'selected' : '' }}>
                                            {{ $value }} tasks
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="default_view" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Default task view
                                </label>
                                <select id="default_view" name="default_view" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="list" {{ $user->getSetting('default_view', 'list') === 'list' ? 'selected' : '' }}>List view</option>
                                    <option value="board" {{ $user->getSetting('default_view', 'list') === 'board' ? 'selected' : '' }}>Board view</option>
                                    <option value="calendar" {{ $user->getSetting('default_view', 'list') === 'calendar' ? 'selected' : '' }}>Calendar view</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="default_task_sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Default task sorting
                                </label>
                                <select id="default_task_sort" name="default_task_sort" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="created_at|desc" {{ $user->getSetting('default_task_sort', 'created_at|desc') === 'created_at|desc' ? 'selected' : '' }}>Created date (newest first)</option>
                                    <option value="created_at|asc" {{ $user->getSetting('default_task_sort', 'created_at|desc') === 'created_at|asc' ? 'selected' : '' }}>Created date (oldest first)</option>
                                    <option value="due_date|asc" {{ $user->getSetting('default_task_sort', 'created_at|desc') === 'due_date|asc' ? 'selected' : '' }}>Due date (soonest first)</option>
                                    <option value="due_date|desc" {{ $user->getSetting('default_task_sort', 'created_at|desc') === 'due_date|desc' ? 'selected' : '' }}>Due date (latest first)</option>
                                    <option value="priority|desc" {{ $user->getSetting('default_task_sort', 'created_at|desc') === 'priority|desc' ? 'selected' : '' }}>Priority (highest first)</option>
                                    <option value="title|asc" {{ $user->getSetting('default_task_sort', 'created_at|desc') === 'title|asc' ? 'selected' : '' }}>Title (A-Z)</option>
                                </select>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="enable_animations" name="enable_animations" type="checkbox" 
                                        {{ $user->getSetting('enable_animations', true) ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_animations" class="font-medium text-gray-700 dark:text-gray-300">Enable animations</label>
                                    <p class="text-gray-500 dark:text-gray-400">Use animations for a more dynamic experience</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="compact_mode" name="compact_mode" type="checkbox" 
                                        {{ $user->getSetting('compact_mode', false) ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="compact_mode" class="font-medium text-gray-700 dark:text-gray-300">Compact mode</label>
                                    <p class="text-gray-500 dark:text-gray-400">Use less spacing to show more content at once</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="btn btn-primary">
                            Save Appearance Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 