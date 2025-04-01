<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Task management application">
    
    <title>{{ config('app.name', 'Task Manager') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
    
    <!-- Tabler Core -->
    <link rel="stylesheet" href="{{ asset('vendor/tabler/dist/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/tabler/dist/css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/tabler/dist/css/tabler-vendors.min.css') }}">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <x-rich-text::styles theme="richtextlaravel" data-turbo-track="false" />
</head>
<body class="antialiased">
    <div id="app">
        @yield('content')
    </div>

    <!-- Tabler Core -->
    <script src="{{ asset('vendor/tabler/dist/js/tabler.min.js') }}"></script>
    
    <!-- Custom Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html> 