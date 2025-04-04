@extends('layouts.app')

@section('title', 'Create Time Entry')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create Time Entry</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Log time spent on a task
            </p>
        </div>
        <a href="{{ route('time-entries.index') }}" class="btn btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Time Entries
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('time-entries.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Task Selection -->
            <div>
                <label for="task_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Task</label>
                <select id="task_id" name="task_id" class="form-select w-full" required>
                    <option value="">Select a task</option>
                    @foreach($tasks as $task)
                        <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                            {{ $task->title }}
                        </option>
                    @endforeach
                </select>
                @error('task_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-input w-full" required>
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Time -->
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" class="form-input w-full" required>
                    @error('start_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Time -->
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" class="form-input w-full" required>
                    @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                <textarea id="description" name="description" rows="3" class="form-textarea w-full">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration Preview -->
            <div class="p-4 rounded-md bg-gray-50 dark:bg-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration Preview</h3>
                <div id="duration-preview" class="text-xl font-semibold text-gray-900 dark:text-white">--:--</div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Time in hours:minutes</p>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="reset" class="btn btn-outline">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Create Time Entry
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 