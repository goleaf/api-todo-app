<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            margin: 0.2rem 0;
        }
        .sidebar .nav-link:hover {
            color: rgba(255, 255, 255, 1);
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .content {
            padding: 1.5rem;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @auth('admin')
                <!-- Sidebar -->
                <div class="col-md-2 sidebar p-0">
                    <div class="d-flex flex-column p-3">
                        <h5 class="mb-4 text-center">Todo Admin</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tags.index') }}" class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">Tags</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">Tasks</a>
                            </li>
                            <li class="nav-item mt-3">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Main Content -->
                <div class="col-md-10 content">
                    <div class="mb-4">
                        <h3>@yield('title', 'Admin Dashboard')</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            @else
                <div class="col-md-12 content">
                    @yield('content')
                </div>
            @endauth
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 