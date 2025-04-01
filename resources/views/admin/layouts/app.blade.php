<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') | {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Rich Text Editor Styles -->
    <x-rich-text::styles theme="richtextlaravel" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.2rem 0;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar .nav-link:hover {
            color: rgba(255, 255, 255, 1);
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 500;
        }
        .content {
            padding: 1.5rem;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 1rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 500;
        }
        .table th {
            font-weight: 600;
        }
        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-weight: bold;
        }
        .btn-logout {
            color: #dc3545 !important;
            transition: all 0.3s;
        }
        .btn-logout:hover {
            background-color: rgba(220, 53, 69, 0.2) !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @auth('admin')
                <!-- Sidebar -->
                <div class="col-md-2 sidebar p-0">
                    <div class="d-flex flex-column p-3">
                        <div class="d-flex align-items-center mb-4 justify-content-center">
                            <i class="fas fa-tasks fa-2x me-2"></i>
                            <h5 class="mb-0">Todo Admin</h5>
                        </div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    <i class="fas fa-folder"></i> Categories
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tags.index') }}" class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                                    <i class="fas fa-tags"></i> Tags
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list"></i> Tasks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.translations.index') }}" class="nav-link {{ request()->routeIs('admin.translations.*') ? 'active' : '' }}">
                                    <i class="fas fa-language"></i> Translations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.slow-queries.index') }}" class="nav-link {{ request()->routeIs('admin.slow-queries.*') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt"></i> Slow Queries
                                </a>
                            </li>
                            <li class="nav-item mt-3">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link w-100 btn-logout text-start border-0 bg-transparent">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Main Content -->
                <div class="col-md-10 content">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3>@yield('title', 'Admin Dashboard')</h3>
                            <div>
                                <span class="text-muted me-2">Logged in as: {{ Auth::guard('admin')->user()->email }}</span>
                            </div>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
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
    
    @stack('scripts')
</body>
</html> 