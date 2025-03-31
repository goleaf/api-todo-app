<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 md:mb-0">
                        <i class="fas fa-tasks text-purple-600 dark:text-purple-400 mr-2"></i>
                        Task List
                    </h1>
                    
                    <div>
                        <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-200 active:bg-purple-600 disabled:opacity-25 transition">
                            <i class="fas fa-plus mr-2"></i>
                            New Task
                        </a>
                    </div>
                </div>
                
                <!-- Filters Section -->
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search Box -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search Tasks</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input wire:model.live.debounce.300ms="search" type="text" id="search" class="pl-10 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Search by title or description...">
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select wire:model.live="statusFilter" id="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="all">All Tasks</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <label for="categoryFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <select wire:model.live="categoryFilter" id="categoryFilter" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Date Filter -->
                        <div>
                            <label for="dateFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                            <select wire:model.live="dateFilter" id="dateFilter" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="">All Dates</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <button wire:click="resetFilters" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Filters
                        </button>
                    </div>
                </div>
                
                <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-md">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('message') }}</span>
                        </div>
                    </div>
                @endif
                
                <!-- Tasks Table -->
                <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
                    @if ($tasks->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('completed')" class="text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider flex items-center focus:outline-none">
                                            Status
                                            @if ($sortField === 'completed')
                                                <span class="ml-1 text-xs">
                                                    @if ($sortDirection === 'asc')
                                                        <i class="fas fa-sort-up"></i>
                                                    @else
                                                        <i class="fas fa-sort-down"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('title')" class="text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider flex items-center focus:outline-none">
                                            Title
                                            @if ($sortField === 'title')
                                                <span class="ml-1 text-xs">
                                                    @if ($sortDirection === 'asc')
                                                        <i class="fas fa-sort-up"></i>
                                                    @else
                                                        <i class="fas fa-sort-down"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('priority')" class="text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider flex items-center focus:outline-none">
                                            Priority
                                            @if ($sortField === 'priority')
                                                <span class="ml-1 text-xs">
                                                    @if ($sortDirection === 'asc')
                                                        <i class="fas fa-sort-up"></i>
                                                    @else
                                                        <i class="fas fa-sort-down"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('due_date')" class="text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider flex items-center focus:outline-none">
                                            Due Date
                                            @if ($sortField === 'due_date')
                                                <span class="ml-1 text-xs">
                                                    @if ($sortDirection === 'asc')
                                                        <i class="fas fa-sort-up"></i>
                                                    @else
                                                        <i class="fas fa-sort-down"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('created_at')" class="text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider flex items-center focus:outline-none">
                                            Created
                                            @if ($sortField === 'created_at')
                                                <span class="ml-1 text-xs">
                                                    @if ($sortDirection === 'asc')
                                                        <i class="fas fa-sort-up"></i>
                                                    @else
                                                        <i class="fas fa-sort-down"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($tasks as $task)
                                    <tr class="{{ $task->completed ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button wire:click="toggleStatus({{ $task->id }})" class="flex items-center justify-center h-6 w-6 rounded-md border {{ $task->completed ? 'border-green-500 bg-green-100 dark:bg-green-900 dark:border-green-600' : 'border-gray-300 dark:border-gray-600' }}">
                                                @if ($task->completed)
                                                    <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-1">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white {{ $task->completed ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                                                        {{ $task->title }}
                                                    </div>
                                                    @if ($task->description)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 {{ $task->completed ? 'line-through' : '' }}">
                                                            {{ Str::limit($task->description, 50) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $task->priority == 5 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                {{ $task->priority == 4 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                {{ $task->priority == 3 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $task->priority == 2 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $task->priority == 1 ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                            ">
                                                {{ $task->priority }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if ($task->due_date)
                                                <span class="{{ $task->due_date < now() && !$task->completed ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                                    {{ $task->due_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">No due date</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $task->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Are you sure you want to delete this task?" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        <div class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-tasks text-gray-400 dark:text-gray-600 text-5xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No tasks found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new task.</p>
                            <div class="mt-6">
                                <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    New Task
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
