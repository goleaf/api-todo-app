@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $task->title }}</h1>
            </div>
            <div class="flex space-x-2">
                <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ $task->completed ? __('Mark Incomplete') : __('Mark Complete') }}
                    </button>
                </form>
                <x-ui.secondary-button href="{{ route('tasks.edit', $task) }}">
                    {{ __('Edit') }}
                </x-ui.secondary-button>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this task?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-tasks.status :status="$task->status" />
                    <x-tasks.priority :priority="$task->priority" />
                    @if($task->category)
                        <x-tasks.category :category="$task->category" />
                    @endif
                    
                    @if($task->due_date)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $task->is_overdue ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ __('Due') }}: {{ $task->due_date->format('M d, Y') }}
                        </span>
                    @endif
                </div>

                <div class="prose max-w-none mt-4">
                    {!! $task->description !!}
                </div>

                @if($task->tags && $task->tags->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Tags') }}</h3>
                        <div class="flex flex-wrap gap-1">
                            @foreach($task->tags as $tag)
                                <x-tasks.tag :tag="$tag" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <hr class="my-6" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Attachments -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Attachments') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($task->attachments && $task->attachments->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($task->attachments as $attachment)
                                        <li class="py-3 flex justify-between items-center">
                                            <div class="flex items-center">
                                                <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span>{{ $attachment->original_filename }}</span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('attachments.download', $attachment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Download') }}
                                                </a>
                                                <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500">{{ __('No attachments yet') }}</p>
                            @endif

                            <form action="{{ route('tasks.attachments.store', $task) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <div class="flex items-center">
                                    <input type="file" name="attachment" id="attachment" class="text-sm">
                                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Upload') }}
                                    </button>
                                </div>
                                @error('attachment')
                                    <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </form>
                        </div>
                    </div>

                    <!-- Time Entries -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Time Tracking') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="mb-4 flex justify-between">
                                <span>{{ __('Total time spent') }}: {{ $task->formatted_total_time ?? '0h 0m' }}</span>
                                @if(!$task->active_time_entry)
                                    <form action="{{ route('time-entries.start', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Start Timer') }}
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('time-entries.stop', $task->active_time_entry) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Stop Timer') }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if($task->time_entries && $task->time_entries->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($task->time_entries->sortByDesc('started_at') as $entry)
                                        <li class="py-3 flex justify-between">
                                            <div>
                                                <span class="block">{{ $entry->started_at->format('M d, Y - H:i') }}</span>
                                                <span class="block text-sm text-gray-500">
                                                    @if($entry->ended_at)
                                                        {{ __('to') }} {{ $entry->ended_at->format('H:i') }}
                                                    @else
                                                        <span class="text-green-600">{{ __('Running') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                @if($entry->ended_at)
                                                    <span>{{ $entry->formatted_duration }}</span>
                                                @else
                                                    <span class="animate-pulse text-green-600">
                                                        {{ __('Running...') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500">{{ __('No time entries yet') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 