<!-- Navbar -->
<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ url('/') }}">
                {{ config('app.name', 'Task Manager') }}
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-home"></i>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('tasks.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-checkbox"></i>
                            </span>
                            <span class="nav-link-title">Tasks</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('categories.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-folder"></i>
                            </span>
                            <span class="nav-link-title">Categories</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="nav-link-title">Profile</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-home"></i>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('tasks.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-checkbox"></i>
                            </span>
                            <span class="nav-link-title">Tasks</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('categories.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-folder"></i>
                            </span>
                            <span class="nav-link-title">Categories</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="nav-link-title">Profile</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> 