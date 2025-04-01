<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Task Manager'))</title>

    <!-- CSS -->
    <link href="{{ asset('vendor/tabler/dist/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendor/tabler/dist/css/tabler-icons.min.css') }}" rel="stylesheet"/>

    @stack('styles')
</head>
<body>
<div class="page">
    <!-- Navbar -->
    @include('frontend.layouts.navigation')

    <div class="page-wrapper">
        @yield('content')

        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                <a href="https://github.com/yourusername/task-manager" target="_blank" class="link-secondary" rel="noopener">
                                    Source code
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Copyright &copy; {{ date('Y') }}
                                <a href="." class="link-secondary">{{ config('app.name', 'Task Manager') }}</a>.
                                All rights reserved.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Core JS -->
<script src="{{ asset('vendor/tabler/dist/js/tabler.min.js') }}" defer></script>

@stack('scripts')
</body>
</html>
