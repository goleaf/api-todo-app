@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Welcome to {{ config('app.name') }}</h1>
        
        <!-- Onboarding Component -->
        <x-onboarding :user="auth()->user()" />
        
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">What's Next?</h2>
            <p class="mb-4">
                After completing the onboarding steps, you'll have access to all the features of our application.
                The onboarding process helps you set up your account for the best experience.
            </p>
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Go to dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 