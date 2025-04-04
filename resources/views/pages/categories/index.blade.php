@extends('layouts.app')

@section('title', __('Categories'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('Categories') }}
            </h1>
            <div class="flex space-x-2">
                <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('New Category') }}
                </a>
            </div>
        </div>

        @if($categories->isEmpty())
            <x-ui.card class="p-6 text-gray-500 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <p class="mb-2">{{ __('No categories found') }}</p>
                <a href="{{ route('categories.create') }}" class="text-blue-600 hover:text-blue-500">{{ __('Create your first category') }}</a>
            </x-ui.card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <x-categories.category-card :category="$category" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 