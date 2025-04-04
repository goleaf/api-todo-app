@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <!-- Tasks Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tasks Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Tasks</span>
                        <span class="font-semibold">{{ $totalTasks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Completed Tasks</span>
                        <span class="font-semibold text-green-600">{{ $completedTasks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pending Tasks</span>
                        <span class="font-semibold text-yellow-600">{{ $pendingTasks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Overdue Tasks</span>
                        <span class="font-semibold text-red-600">{{ $overdueTasks }}</span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                        View All Tasks
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Due Today -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Due Today</h3>
                @if($dueTodayTasks->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($dueTodayTasks as $task)
                            <li class="py-3">
                                <a href="{{ route('tasks.show', $task) }}" class="hover:text-indigo-600">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ Str::limit($task->title, 30) }}</span>
                                        <x-tasks.priority :priority="$task->priority" />
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('tasks.due-today') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                @else
                    <p class="text-gray-500">No tasks due today</p>
                @endif
            </div>

            <!-- Recently Added -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recently Added</h3>
                @if($recentTasks->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentTasks as $task)
                            <li class="py-3">
                                <a href="{{ route('tasks.show', $task) }}" class="hover:text-indigo-600">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ Str::limit($task->title, 30) }}</span>
                                        <span class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">No recent tasks</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('tasks.create') }}" class="block w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-center transition">
                        Create New Task
                    </a>
                    <a href="{{ route('categories.index') }}" class="block w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-center transition">
                        Manage Categories
                    </a>
                    <a href="{{ route('tags.index') }}" class="block w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-md text-center transition">
                        Manage Tags
                    </a>
                    <a href="{{ route('tasks.completed') }}" class="block w-full py-2 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-center transition">
                        View Completed Tasks
                    </a>
                </div>
            </div>
        </div>

        <!-- Task Activity -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Task Activity</h3>
                @if($taskActivity->count() > 0)
                    <div class="overflow-hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Task
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($taskActivity as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('tasks.show', $activity->task_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ Str::limit($activity->task_title, 40) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $activity->action }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->created_at->format('M d, Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 