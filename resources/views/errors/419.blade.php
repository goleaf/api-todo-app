@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="text-9xl font-bold text-purple-600 dark:text-purple-400">419</div>
                    <h1 class="mt-4 text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Page Expired</h1>
                    <p class="mt-4 text-base text-gray-500 dark:text-gray-400">Sorry, your session has expired. Please refresh the page and try again.</p>
                    <div class="mt-6">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Refresh Page
                        </a>
                        <a href="{{ route('dashboard') }}" class="ml-4 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-home mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 