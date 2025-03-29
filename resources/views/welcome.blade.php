<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-light">
        <div class="container">
            <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center" style="max-width: 500px;">
                    <!-- Logo/Icon -->
                    <div class="mb-4">
                        <div class="rounded-circle bg-purple text-white d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 80px; height: 80px; background-color: #6f42c1;">
                            <i class="bi bi-check2-all fs-1"></i>
                        </div>
                    </div>

                    <!-- Heading -->
                    <h1 class="fw-bold mb-3">Welcome to Taskify</h1>

                    <!-- Subheading -->
                    <p class="text-muted mb-4">
                        Your simple yet powerful task management solution. Stay organized, boost productivity, and never miss a deadline again.
                    </p>

                    <!-- Features -->
                    <div class="mb-5">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light">
                                    <i class="bi bi-list-check text-primary fs-4 mb-2"></i>
                                    <p class="mb-0 small">Track all your tasks</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light">
                                    <i class="bi bi-alarm text-warning fs-4 mb-2"></i>
                                    <p class="mb-0 small">Set priorities</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light">
                                    <i class="bi bi-graph-up text-success fs-4 mb-2"></i>
                                    <p class="mb-0 small">Track progress</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-lg" style="background-color: #6f42c1; color: white;">
                            Get Started
                        </a>
                        <p class="text-muted small mt-3">
                            Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Log in</a>
                            or <a href="{{ route('register') }}" class="text-decoration-none">Register</a>
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-4 text-muted small">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Check for token and redirect if already logged in -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check for token in localStorage
                const token = localStorage.getItem('auth_token');
                const urlParams = new URLSearchParams(window.location.search);
                const forceWelcome = urlParams.get('welcome');
                
                // If token exists and we're not forcing the welcome page, redirect to dashboard
                if (token && forceWelcome !== 'true') {
                    window.location.href = '/dashboard';
                }
            });
        </script>
    </body>
</html>
