@extends('layouts.app')

@section('title', __('tags.title'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('tags.title') }}</h1>
            <x-ui.primary-button href="{{ route('tags.create') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('tags.new_tag') }}
            </x-ui.primary-button>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            @if($tags->isEmpty())
                <div class="p-6 text-gray-500 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <p class="mb-2">{{ __('tags.no_tags_found') }}</p>
                    <a href="{{ route('tags.create') }}" class="text-indigo-600 hover:text-indigo-500">{{ __('tags.create_first_tag') }}</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('tags.name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('tags.tasks_count') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('tags.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tags as $tag)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $tag->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tag->tasks_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('tags.edit', $tag) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('tags.edit') }}
                                            </a>
                                            <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('tags.confirm_delete') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    {{ __('tags.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4">
                    {{ $tags->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 