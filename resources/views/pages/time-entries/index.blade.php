@extends('layouts.app')

@section('title', 'Time Entries')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Time Entries</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Track and manage your time spent on tasks
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('time-entries.summary') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                </svg>
                View Summary
            </a>
            <a href="{{ route('time-entries.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Add Time Entry
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('time-entries.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:flex-wrap md:items-end md:gap-4">
            <div class="w-full md:w-64">
                <label for="filter_task" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Task</label>
                <select name="task_id" id="filter_task" class="form-select w-full">
                    <option value="">All Tasks</option>
                    @foreach($tasks as $task)
                        <option value="{{ $task->id }}" {{ request('task_id') == $task->id ? 'selected' : '' }}>
                            {{ $task->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-auto md:flex-1 md:flex md:space-x-4">
                <div class="w-full mb-4 md:mb-0 md:w-1/2">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-input w-full">
                </div>
                <div class="w-full md:w-1/2">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-input w-full">
                </div>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['task_id', 'start_date', 'end_date']))
                    <a href="{{ route('time-entries.index') }}" class="btn btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Time Entries List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                Time Entries
                @if($timeEntries->total() > 0)
                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                        ({{ $timeEntries->total() }} {{ Str::plural('entry', $timeEntries->total()) }})
                    </span>
                @endif
            </h2>
            
            <div class="flex items-center">
                <div class="mr-4">
                    <label for="sort_by" class="sr-only">Sort by</label>
                    <select id="sort_by" name="sort_by" class="form-select form-select-sm text-sm" 
                            onchange="window.location.href = this.value">
                        @php
                            $sortOptions = [
                                'date_desc' => 'Date (newest first)',
                                'date_asc' => 'Date (oldest first)',
                                'duration_desc' => 'Duration (longest first)',
                                'duration_asc' => 'Duration (shortest first)',
                            ];
                            $currentSort = request('sort', 'date_desc');
                        @endphp
                        
                        @foreach($sortOptions as $value => $label)
                            <option value="{{ route('time-entries.index', array_merge(request()->except('page', 'sort'), ['sort' => $value])) }}"
                                {{ $currentSort === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="button" id="export-csv" class="btn btn-sm btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Export
                </button>
            </div>
        </div>
        
        @if($timeEntries->isEmpty())
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No time entries found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['task_id', 'start_date', 'end_date']))
                        No entries match your current filters.
                        <a href="{{ route('time-entries.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Clear filters</a>
                    @else
                        Get started by creating your first time entry.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('time-entries.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Add Time Entry
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Task
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($timeEntries as $entry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $entry->started_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $entry->started_at->format('g:i A') }} - 
                                        @if($entry->ended_at)
                                            {{ $entry->ended_at->format('g:i A') }}
                                        @else
                                            <span class="text-indigo-500 dark:text-indigo-400">Running</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($entry->task->category)
                                            <span class="h-3 w-3 rounded-full mr-2" style="background-color: {{ $entry->task->category->color }}"></span>
                                        @endif
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('tasks.show', $entry->task_id) }}" class="hover:underline">
                                                {{ $entry->task->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        @if($entry->ended_at)
                                            @php
                                                $duration = $entry->ended_at->diffInSeconds($entry->started_at);
                                                $hours = floor($duration / 3600);
                                                $minutes = floor(($duration % 3600) / 60);
                                            @endphp
                                            {{ sprintf('%02d:%02d', $hours, $minutes) }}
                                        @else
                                            <div class="flex items-center">
                                                <span class="flex h-3 w-3 relative mr-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                                                </span>
                                                Running
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                        {{ $entry->description ?: 'No description' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @if(!$entry->ended_at)
                                            <form action="{{ route('time-entries.stop', $entry) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Stop timer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('time-entries.edit', $entry) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>

                                        <button type="button" 
                                                onclick="confirmDelete('{{ $entry->id }}')" 
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" 
                                                title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $timeEntries->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Confirm Delete</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">Are you sure you want to delete this time entry? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="cancelDelete()" class="btn btn-outline">Cancel</button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection 