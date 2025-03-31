<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Task List</h2>
        <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            New Task
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 flex flex-col md:flex-row gap-4 border-b">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" wire:model.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search tasks..."
                />
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row md:space-x-4 space-y-2 md:space-y-0">
                <!-- Category Filter -->
                <select wire:model="categoryFilter" class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select wire:model="filter" class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Tasks</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="overdue">Overdue</option>
                    <option value="today">Due Today</option>
                    <option value="upcoming">Upcoming</option>
                </select>

                <!-- Priority Filter -->
                <select wire:model="priorityFilter" class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>
        
        <!-- Loading Indicator -->
        <div wire:loading class="px-4 py-2 bg-gray-50 text-gray-500">
            Loading tasks...
        </div>

        <!-- Pagination Summary -->
        <div wire:loading.remove class="px-4 py-2 bg-gray-50 text-gray-500 border-b text-sm">
            @if($todos->total() > 0)
                Showing {{ $todos->firstItem() }} to {{ $todos->lastItem() }} of {{ $todos->total() }} tasks
            @else
                No tasks found
            @endif
        </div>

        <!-- Task Actions -->
        <div wire:loading.remove class="px-4 py-2 bg-gray-50 border-b flex justify-end space-x-2">
            <button wire:click="markAllCompleted" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Mark All Completed
            </button>
        </div>

        <!-- Task List -->
        <div wire:loading.remove>
            @if($todos->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($todos as $todo)
                        <li class="p-4 hover:bg-gray-50 transition-colors duration-150 flex items-start">
                            <!-- Checkbox -->
                            <div class="mr-4 mt-1">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                    wire:click="toggleComplete({{ $todo->id }})"
                                    @if($todo->completed) checked @endif
                                >
                            </div>
                            <div class="flex-1">
                                <p class="font-medium @if($todo->completed) line-through text-gray-400 @endif">
                                    {{ $todo->title }}
                                </p>
                                @if($todo->due_date)
                                    <p class="text-sm text-gray-500 mt-1">
                                        Due: {{ \Carbon\Carbon::parse($todo->due_date)->format('M d, Y') }}
                                    </p>
                                @endif
                                @if($todo->priority === 'high')
                                    <span class="inline-block px-2 py-1 mt-2 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                        High Priority
                                    </span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('tasks.edit', $todo->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p>No tasks found matching your criteria.</p>
                    <button wire:click="resetFilters" class="mt-2 text-blue-500 hover:text-blue-700">
                        Reset Filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 bg-gray-50 border-t">
            {{ $todos->links() }}
        </div>
    </div>
</div> 