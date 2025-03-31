<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-800 dark:text-green-200 p-4 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <!-- Loading Indicator -->
        @if($loading)
            <div class="flex justify-center items-center h-64">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 dark:border-purple-400"></div>
            </div>
        @else
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 md:mb-0">
                            <i class="fas fa-tachometer-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                            Dashboard
                        </h1>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-200 active:bg-purple-600 disabled:opacity-25 transition">
                                <i class="fas fa-plus-circle mr-2"></i>
                                New Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Task Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-semibold mb-4">Task Statistics</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Total Tasks -->
                        <div class="bg-blue-50 p-4 rounded-lg shadow">
                            <h3 class="text-blue-700 text-lg font-medium">Total Tasks</h3>
                            <p class="text-3xl font-bold">{{ $taskStats['total'] }}</p>
                        </div>
                        
                        <!-- Completed Tasks -->
                        <div class="bg-green-50 p-4 rounded-lg shadow">
                            <h3 class="text-green-700 text-lg font-medium">Completed</h3>
                            <p class="text-3xl font-bold">{{ $taskStats['completed'] }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $taskStats['total'] > 0 
                                    ? round(($taskStats['completed'] / $taskStats['total']) * 100) 
                                    : 0 }}% of all tasks
                            </p>
                        </div>
                        
                        <!-- Pending Tasks -->
                        <div class="bg-yellow-50 p-4 rounded-lg shadow">
                            <h3 class="text-yellow-700 text-lg font-medium">Pending</h3>
                            <p class="text-3xl font-bold">{{ $taskStats['pending'] }}</p>
                        </div>
                        
                        <!-- Overdue Tasks -->
                        <div class="bg-red-50 p-4 rounded-lg shadow">
                            <h3 class="text-red-700 text-lg font-medium">Overdue</h3>
                            <p class="text-3xl font-bold">{{ $taskStats['overdue'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Completion Rate -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 mr-2"></i>
                            Completion Rate
                        </h2>
                        
                        <div class="mb-2 flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $completionRate }}% Complete</span>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $taskStats['completed'] }}/{{ $taskStats['total'] }}</span>
                        </div>
                        
                        <div class="relative pt-1">
                            <div class="overflow-hidden h-4 mb-4 text-xs flex rounded-lg bg-gray-200 dark:bg-gray-700">
                                <div style="width:{{ $completionRate }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-purple-600 dark:bg-purple-500 rounded-lg transition-all duration-500"></div>
                            </div>
                        </div>
                        
                        @if($taskStats['total'] === 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-4">
                                No tasks created yet. Create some tasks to see statistics.
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Tasks by Category -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-tags text-purple-600 dark:text-purple-400 mr-2"></i>
                            Tasks by Category
                        </h2>
                        
                        @if(empty($categoryStats))
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">
                                No categories with tasks found.
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach($categoryStats as $category)
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $category['name'] ?? 'Uncategorized' }}</span>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $category['task_count'] }}</span>
                                        </div>
                                        <div class="relative pt-1">
                                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-gray-200 dark:bg-gray-700">
                                                <div style="width:{{ ($category['task_count'] / $taskStats['total']) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-{{ $category['color'] ?? 'purple' }}-600 dark:bg-{{ $category['color'] ?? 'purple' }}-500 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-history text-purple-600 dark:text-purple-400 mr-2"></i>
                            Recent Activity
                        </h2>
                        
                        @if(empty($recentActivity))
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">
                                No recent activity found.
                            </p>
                        @else
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($recentActivity as $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full flex items-center justify-center
                                                            @if($activity['type'] === 'created') bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400
                                                            @elseif($activity['type'] === 'completed') bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400
                                                            @else bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400
                                                            @endif">
                                                            @if($activity['type'] === 'created')
                                                                <i class="fas fa-plus-circle"></i>
                                                            @elseif($activity['type'] === 'completed')
                                                                <i class="fas fa-check-circle"></i>
                                                            @else
                                                                <i class="fas fa-edit"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-900 dark:text-white">
                                                                @if($activity['type'] === 'created')
                                                                    Created task
                                                                @elseif($activity['type'] === 'completed')
                                                                    Completed task
                                                                @else
                                                                    Updated task
                                                                @endif
                                                                <a href="{{ route('tasks.show', $activity['id']) }}" class="font-medium text-purple-600 dark:text-purple-400 hover:underline">
                                                                    "{{ $activity['title'] }}"
                                                                </a>
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ $activity['date']->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Tasks -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-clock-rotate-left text-purple-600 dark:text-purple-400 mr-2"></i>
                            Recent Tasks
                        </h2>
                        
                        @if(count($recentTasks) > 0)
                            <div class="space-y-4">
                                @foreach($recentTasks as $task)
                                    <div class="border-l-4 {{ $task->completed ? 'border-green-500 dark:border-green-600' : 'border-yellow-500 dark:border-yellow-600' }} pl-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-r-lg transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <a href="{{ route('tasks.show', $task->id) }}" class="text-gray-900 dark:text-white font-medium hover:text-purple-600 dark:hover:text-purple-400">
                                                    {{ $task->title }}
                                                </a>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Added {{ $task->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button wire:click="toggleComplete({{ $task->id }})" class="text-sm p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 {{ $task->completed ? 'text-green-600 hover:text-green-800' : 'text-gray-400 hover:text-gray-600' }}">
                                                    <i class="fas {{ $task->completed ? 'fa-check-circle' : 'fa-circle' }} text-lg"></i>
                                                </button>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->completed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                                    {{ $task->completed ? 'Completed' : 'Active' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 text-right">
                                <a href="{{ route('tasks.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 inline-flex items-center">
                                    View all tasks
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-400 dark:text-gray-500 mb-3">
                                    <i class="fas fa-clipboard-list text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">No tasks yet</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first task</p>
                                <div class="mt-4">
                                    <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Task
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Upcoming Tasks -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-calendar text-purple-600 dark:text-purple-400 mr-2"></i>
                            Upcoming Tasks
                        </h2>
                        
                        @if(count($upcomingTasks) > 0)
                            <div class="space-y-4">
                                @foreach($upcomingTasks as $task)
                                    <div class="border-l-4 border-blue-500 dark:border-blue-600 pl-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-r-lg transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <a href="{{ route('tasks.show', $task->id) }}" class="text-gray-900 dark:text-white font-medium hover:text-purple-600 dark:hover:text-purple-400">
                                                    {{ $task->title }}
                                                </a>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    @if($task->due_date)
                                                        Due {{ $task->due_date->format('M d, Y') }}
                                                        <span class="font-medium {{ $task->due_date->isPast() ? 'text-red-600 dark:text-red-400' : '' }}">
                                                            ({{ $task->due_date->diffForHumans() }})
                                                        </span>
                                                    @else
                                                        No due date
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button wire:click="toggleComplete({{ $task->id }})" class="text-sm p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 {{ $task->completed ? 'text-green-600 hover:text-green-800' : 'text-gray-400 hover:text-gray-600' }}">
                                                    <i class="fas {{ $task->completed ? 'fa-check-circle' : 'fa-circle' }} text-lg"></i>
                                                </button>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $task->priority == 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $task->priority == 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $task->priority == 'low' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ !$task->priority ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                                ">
                                                    {{ $task->priority ? ucfirst($task->priority) : 'No Priority' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 text-right">
                                <a href="{{ route('tasks.index', ['due_date' => 'upcoming']) }}" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 inline-flex items-center">
                                    View all upcoming tasks
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-400 dark:text-gray-500 mb-3">
                                    <i class="fas fa-calendar-check text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">No upcoming tasks</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up</p>
                                <div class="mt-4">
                                    <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Task
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div> 
</div> 