<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="A beautiful task management application">
    
    <title>{{ config('app.name', 'Todo App') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    @inertiaHead
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    @inertia
</body>
</html> 