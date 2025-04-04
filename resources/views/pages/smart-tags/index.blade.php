@extends('layouts.app')

@section('title', __('smart_tags.smart_tags'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('smart_tags.smart_tags') }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                {{ __('smart_tags.dynamic_collections') }}
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('smart-tags.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ __('smart_tags.create_smart_tag') }}
            </a>
        </div>
    </div>

    <!-- Smart Tags Grid -->
    @if($smartTags->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('smart_tags.no_smart_tags_found') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('smart_tags.get_started') }}</p>
            <div class="mt-6">
                <a href="{{ route('smart-tags.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('smart_tags.create_smart_tag') }}
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($smartTags as $tag)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between"
                         style="background-color: {{ $tag->color }}15; border-left: 4px solid {{ $tag->color }};">
                        <div class="flex items-center space-x-2">
                            <span class="h-3 w-3 rounded-full" style="background-color: {{ $tag->color }};"></span>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                {{ $tag->name }}
                            </h2>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300">
                                {{ $tag->tasks_count }} {{ Str::plural(__('smart_tags.task'), $tag->tasks_count) }}
                            </span>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('smart-tags.edit', $tag) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('common.edit') }}
                                    </a>
                                    <a href="{{ route('tasks.index', ['smart_tag' => $tag->id]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('smart_tags.view_tasks') }}
                                    </a>
                                    <button onclick="confirmDelete('{{ $tag->id }}', '{{ $tag->name }}')" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 flex-grow">
                        @if($tag->description)
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
                                {{ $tag->description }}
                            </p>
                        @endif
                        
                        <div class="space-y-3">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('smart_tags.filter_criteria') }}</h3>
                            
                            <!-- Due Date Filter -->
                            @if($tag->filter_by_due_date)
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ __('smart_tags.due_date') }}</span>
                                        @switch($tag->due_date_operator)
                                            @case('before')
                                                {{ __('smart_tags.before') }} {{ \Carbon\Carbon::parse($tag->due_date_value)->format('M j, Y') }}
                                                @break
                                            @case('after')
                                                {{ __('smart_tags.after') }} {{ \Carbon\Carbon::parse($tag->due_date_value)->format('M j, Y') }}
                                                @break
                                            @case('between')
                                                {{ __('smart_tags.between') }} {{ \Carbon\Carbon::parse($tag->due_date_value)->format('M j, Y') }}
                                                {{ __('smart_tags.and') }} {{ \Carbon\Carbon::parse($tag->due_date_end_value)->format('M j, Y') }}
                                                @break
                                            @case('overdue')
                                                {{ __('smart_tags.overdue') }}
                                                @break
                                            @case('today')
                                                {{ __('smart_tags.due_today') }}
                                                @break
                                            @case('next_7_days')
                                                {{ __('smart_tags.due_next_7_days') }}
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Priority Filter -->
                            @if($tag->filter_by_priority)
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ __('smart_tags.priority') }}</span>
                                        @php
                                            $priorities = [];
                                            if($tag->priority_high) $priorities[] = __('smart_tags.high');
                                            if($tag->priority_medium) $priorities[] = __('smart_tags.medium');
                                            if($tag->priority_low) $priorities[] = __('smart_tags.low');
                                        @endphp
                                        {{ implode(', ', $priorities) }}
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Status Filter -->
                            @if($tag->filter_by_status)
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ __('smart_tags.status') }}</span>
                                        @php
                                            $statuses = [];
                                            if($tag->status_pending) $statuses[] = __('smart_tags.pending');
                                            if($tag->status_in_progress) $statuses[] = __('smart_tags.in_progress');
                                            if($tag->status_completed) $statuses[] = __('smart_tags.done');
                                        @endphp
                                        {{ implode(', ', $statuses) }}
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Category Filter -->
                            @if($tag->filter_by_category && $tag->categories->count() > 0)
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ __('smart_tags.categories') }}</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($tag->categories as $category)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                                                      style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                                    <span class="h-1.5 w-1.5 rounded-full mr-1" style="background-color: {{ $category->color }};"></span>
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!$tag->filter_by_due_date && !$tag->filter_by_priority && !$tag->filter_by_status && !($tag->filter_by_category && $tag->categories->count() > 0))
                                <p class="text-sm italic text-gray-500 dark:text-gray-400">{{ __('smart_tags.no_filter_criteria') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                        <a href="{{ route('tasks.index', ['smart_tag' => $tag->id]) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium flex items-center">
                            <span>{{ __('smart_tags.view_matching_tasks') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $smartTags->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" 
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden p-4"
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="deleteModalTitle" 
         tabindex="-1"
         data-modal-bg {{-- For closing on bg click --}}
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 id="deleteModalTitle" class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('smart_tags.delete_smart_tag') }}</h3>
            <p id="deleteModalMessage" class="text-sm text-gray-600 dark:text-gray-400 mb-6"></p>
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                        data-modal-cancel
                >
                    {{ __('common.cancel') }}
                </button>
                <form id="deleteForm" method="POST" action="" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            data-modal-confirm
                    >
                        {{ __('common.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- Script moved to resources/js/pages/smart-tags-index.js --}}
{{-- @push('scripts')
<script>
    // ... removed script content ...
</script>
@endpush --}}
@endsection 