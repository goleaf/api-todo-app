@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center">
            <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Category</h1>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-ui.input-label for="name" value="Category Name" />
                            <x-ui.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name)" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-ui.input-label for="color" value="Category Color" />
                            <div class="flex mt-1">
                                <input type="color" id="color" name="color" value="{{ old('color', $category->color) }}" class="h-10 w-10 rounded">
                                <x-ui.text-input type="text" id="color_text" value="{{ old('color', $category->color) }}" class="ml-2 block w-full" readonly />
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Choose a color for this category</p>
                            @error('color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-ui.input-label for="description" value="Description (Optional)" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-ui.secondary-button href="{{ route('categories.index') }}" class="mr-3">
                            Cancel
                        </x-ui.secondary-button>
                        <x-ui.primary-button type="submit">
                            Update Category
                        </x-ui.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 