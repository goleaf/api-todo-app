@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Notification Settings</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Manage how you receive notifications from the application
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
                            Notification Preferences
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Choose how and when you want to be notified
                        </p>
                    </div>
                </div>

                <form action="{{ route('settings.notifications.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Email Notifications Section -->
                    <div class="space-y-4">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Email Notifications</h3>
                        
                        <div class="mt-4 space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="email_enabled" name="email_enabled" type="checkbox" 
                                        {{ $user->getSetting('email_enabled', true) ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="email_enabled" class="font-medium text-gray-700 dark:text-gray-300">Enable email notifications</label>
                                    <p class="text-gray-500 dark:text-gray-400">Receive notifications via email</p>
                                </div>
                            </div>
                            
                            <div class="ml-7 space-y-3">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="email_task_due" name="email_task_due" type="checkbox" 
                                            {{ $user->getSetting('email_task_due', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="email_task_due" class="font-medium text-gray-700 dark:text-gray-300">Task Due Reminders</label>
                                        <p class="text-gray-500 dark:text-gray-400">Receive notifications when tasks are due soon</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="email_task_assigned" name="email_task_assigned" type="checkbox" 
                                            {{ $user->getSetting('email_task_assigned', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="email_task_assigned" class="font-medium text-gray-700 dark:text-gray-300">Task Assignments</label>
                                        <p class="text-gray-500 dark:text-gray-400">Receive notifications when a task is assigned to you</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="email_task_comment" name="email_task_comment" type="checkbox" 
                                            {{ $user->getSetting('email_task_comment', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="email_task_comment" class="font-medium text-gray-700 dark:text-gray-300">Task Comments</label>
                                        <p class="text-gray-500 dark:text-gray-400">Receive notifications when someone comments on your tasks</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="email_weekly_summary" name="email_weekly_summary" type="checkbox" 
                                            {{ $user->getSetting('email_weekly_summary', false) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="email_weekly_summary" class="font-medium text-gray-700 dark:text-gray-300">Weekly Summary</label>
                                        <p class="text-gray-500 dark:text-gray-400">Receive a weekly summary of your tasks and progress</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- In-App Notifications Section -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">In-App Notifications</h3>
                        
                        <div class="mt-4 space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="inapp_enabled" name="inapp_enabled" type="checkbox" 
                                        {{ $user->getSetting('inapp_enabled', true) ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="inapp_enabled" class="font-medium text-gray-700 dark:text-gray-300">Enable in-app notifications</label>
                                    <p class="text-gray-500 dark:text-gray-400">Receive notifications inside the application</p>
                                </div>
                            </div>
                            
                            <div class="ml-7 space-y-3">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="inapp_task_due" name="inapp_task_due" type="checkbox" 
                                            {{ $user->getSetting('inapp_task_due', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="inapp_task_due" class="font-medium text-gray-700 dark:text-gray-300">Task Due Reminders</label>
                                        <p class="text-gray-500 dark:text-gray-400">Show notifications when tasks are due soon</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="inapp_task_assigned" name="inapp_task_assigned" type="checkbox" 
                                            {{ $user->getSetting('inapp_task_assigned', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="inapp_task_assigned" class="font-medium text-gray-700 dark:text-gray-300">Task Assignments</label>
                                        <p class="text-gray-500 dark:text-gray-400">Show notifications when a task is assigned to you</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="inapp_task_comment" name="inapp_task_comment" type="checkbox" 
                                            {{ $user->getSetting('inapp_task_comment', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="inapp_task_comment" class="font-medium text-gray-700 dark:text-gray-300">Task Comments</label>
                                        <p class="text-gray-500 dark:text-gray-400">Show notifications when someone comments on your tasks</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Push Notifications Section -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Browser Push Notifications</h3>
                        
                        <div class="mt-4 space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="push_enabled" name="push_enabled" type="checkbox" 
                                        {{ $user->getSetting('push_enabled', false) ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="push_enabled" class="font-medium text-gray-700 dark:text-gray-300">Enable browser push notifications</label>
                                    <p class="text-gray-500 dark:text-gray-400">Receive notifications even when the app is closed</p>
                                </div>
                            </div>
                            
                            <div id="push-permissions-warning" class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-md text-sm {{ $user->getSetting('push_enabled', false) ? '' : 'hidden' }}">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Browser Permission Required</h3>
                                        <div class="mt-2 text-yellow-700 dark:text-yellow-300">
                                            <p>
                                                After saving, you'll need to allow notifications when prompted by your browser.
                                                If you previously denied permission, you'll need to update your browser settings.
                                            </p>
                                        </div>
                                        <button type="button" id="request-permission-btn" class="mt-2 px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-100 dark:hover:bg-yellow-700">
                                            Request Permission
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ml-7 space-y-3 {{ $user->getSetting('push_enabled', false) ? '' : 'opacity-50' }}">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="push_task_due" name="push_task_due" type="checkbox" 
                                            {{ $user->getSetting('push_task_due', false) ? 'checked' : '' }}
                                            {{ $user->getSetting('push_enabled', false) ? '' : 'disabled' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="push_task_due" class="font-medium text-gray-700 dark:text-gray-300">Task Due Reminders</label>
                                        <p class="text-gray-500 dark:text-gray-400">Receive browser notifications when tasks are due soon</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="push_task_assigned" name="push_task_assigned" type="checkbox" 
                                            {{ $user->getSetting('push_task_assigned', false) ? 'checked' : '' }}
                                            {{ $user->getSetting('push_enabled', false) ? '' : 'disabled' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="push_task_assigned" class="font-medium text-gray-700 dark:text-gray-300">Task Assignments</label>
                                        <p class="text-gray-500 dark:text-gray-400">Receive browser notifications when a task is assigned to you</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="btn btn-primary">
                            Save Notification Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 