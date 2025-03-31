@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="text-9xl font-bold text-purple-600 dark:text-purple-400">429</div>
                    <h1 class="mt-4 text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Too Many Requests</h1>
                    <p class="mt-4 text-base text-gray-500 dark:text-gray-400">Sorry, you are making too many requests. Please try again later.</p>
                    <div class="mt-6">
                        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
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