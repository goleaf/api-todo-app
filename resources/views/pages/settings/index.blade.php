@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Settings</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Manage your account preferences and application settings
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
                            General Settings
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Update your general preferences and regional settings
                        </p>
                    </div>
                </div>

                <form action="{{ route('settings.general.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Language -->
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Language
                        </label>
                        <select id="language" name="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="en" {{ $user->getSetting('language', config('app.locale')) === 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ $user->getSetting('language', config('app.locale')) === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="es" {{ $user->getSetting('language', config('app.locale')) === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="de" {{ $user->getSetting('language', config('app.locale')) === 'de' ? 'selected' : '' }}>Deutsch</option>
                            <option value="it" {{ $user->getSetting('language', config('app.locale')) === 'it' ? 'selected' : '' }}>Italiano</option>
                        </select>
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Timezone
                        </label>
                        <select id="timezone" name="timezone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @php
                                $timezones = [
                                    'UTC' => 'UTC',
                                    'America/New_York' => 'Eastern Time (US & Canada)',
                                    'America/Chicago' => 'Central Time (US & Canada)',
                                    'America/Denver' => 'Mountain Time (US & Canada)',
                                    'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                    'Europe/London' => 'London',
                                    'Europe/Paris' => 'Paris',
                                    'Europe/Berlin' => 'Berlin',
                                    'Europe/Moscow' => 'Moscow',
                                    'Asia/Tokyo' => 'Tokyo',
                                    'Asia/Shanghai' => 'Shanghai',
                                    'Australia/Sydney' => 'Sydney',
                                ];
                                $currentTimezone = $user->getSetting('timezone', config('app.timezone'));
                            @endphp
                            
                            @foreach($timezones as $value => $label)
                                <option value="{{ $value }}" {{ $currentTimezone === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Format -->
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Date Format
                        </label>
                        <select id="date_format" name="date_format" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @php
                                $dateFormats = [
                                    'Y-m-d' => date('Y-m-d') . ' (YYYY-MM-DD)',
                                    'm/d/Y' => date('m/d/Y') . ' (MM/DD/YYYY)',
                                    'd/m/Y' => date('d/m/Y') . ' (DD/MM/YYYY)',
                                    'd.m.Y' => date('d.m.Y') . ' (DD.MM.YYYY)',
                                ];
                                $currentDateFormat = $user->getSetting('date_format', 'Y-m-d');
                            @endphp
                            
                            @foreach($dateFormats as $value => $label)
                                <option value="{{ $value }}" {{ $currentDateFormat === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Time Format -->
                    <div>
                        <label for="time_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Time Format
                        </label>
                        <select id="time_format" name="time_format" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @php
                                $timeFormats = [
                                    'H:i' => date('H:i') . ' (24-hour)',
                                    'h:i A' => date('h:i A') . ' (12-hour)',
                                ];
                                $currentTimeFormat = $user->getSetting('time_format', 'H:i');
                            @endphp
                            
                            @foreach($timeFormats as $value => $label)
                                <option value="{{ $value }}" {{ $currentTimeFormat === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 