<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="user-logged-in" content="true">
        @endauth

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @livewireStyles
    </head>
    <body class="bg-light">
        <div class="container">
            <header class="py-3">
                <div class="row">
                    <div class="col-6">
                        <a href="/" class="text-decoration-none">
                            <h1 class="h4 text-dark">{{ config('app.name', 'Laravel') }}</h1>
                        </a>
                    </div>
                    <div class="col-6">
                        <livewire:user-header />
                    </div>
                </div>
            </header>

            <main class="py-4">
                {{ $slot }}
            </main>

            <footer class="py-3 mt-5 border-top">
                <div class="text-center text-muted">
                    © {{ date('Y') }} {{ config('app.name', 'Laravel') }}
                </div>
            </footer>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        @livewireScripts
    </body>
</html>