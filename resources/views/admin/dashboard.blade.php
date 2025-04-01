<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Tasks
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            {{ $totalTasks ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Completed Tasks
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            {{ $completedTasks ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Pending Tasks
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            {{ $pendingTasks ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Overdue Tasks
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            {{ $overdueTasks ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Tasks -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Tasks</h3>
                            <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:text-indigo-900">View All</a>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($recentTasks ?? [] as $task)
                                <div class="py-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $task->title }}
                                            </a>
                                            <p class="text-sm text-gray-500 mt-1">
                                                @if($task->due_date)
                                                    Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                                @else
                                                    No due date
                                                @endif
                                            </p>
                                        </div>
                                        <span class="{{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No tasks found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Time Tracking Overview -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Time Tracking</h3>
                            <a href="{{ route('time-entries.index') }}" class="text-indigo-600 hover:text-indigo-900">View All</a>
                        </div>
                        
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">This Week</h4>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $weeklyTime ?? '00:00' }}
                                <span class="text-sm font-normal text-gray-500">hours</span>
                            </div>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Recent Time Entries</h4>
                            @forelse($recentTimeEntries ?? [] as $timeEntry)
                                <div class="py-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <a href="{{ route('tasks.show', $timeEntry->task) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $timeEntry->task->title }}
                                            </a>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($timeEntry->start_time)->format('M d, Y H:i') }}
                                                @if($timeEntry->end_time)
                                                    - {{ \Carbon\Carbon::parse($timeEntry->end_time)->format('H:i') }}
                                                @else
                                                    <span class="text-green-600">(Running)</span>
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            @if($timeEntry->end_time)
                                                {{ \Carbon\Carbon::parse($timeEntry->start_time)->diff(\Carbon\Carbon::parse($timeEntry->end_time))->format('%H:%I') }}
                                            @else
                                                --:--
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No time entries found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
