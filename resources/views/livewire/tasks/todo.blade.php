<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Task Management</h1>
    
    <!-- Filters and Search -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-wrap gap-2">
            <button wire:click="applyFilter('all')" 
                class="px-3 py-1 rounded text-sm {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                All
            </button>
            <button wire:click="applyFilter('completed')" 
                class="px-3 py-1 rounded text-sm {{ $filter === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                Completed
            </button>
            <button wire:click="applyFilter('incomplete')" 
                class="px-3 py-1 rounded text-sm {{ $filter === 'incomplete' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                Incomplete
            </button>
            <button wire:click="applyFilter('today')" 
                class="px-3 py-1 rounded text-sm {{ $filter === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                Today
            </button>
            <button wire:click="applyFilter('overdue')" 
                class="px-3 py-1 rounded text-sm {{ $filter === 'overdue' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                Overdue
            </button>
            <button wire:click="applyFilter('this-week')" 
                class="px-3 py-1 rounded text-sm {{ $filter === 'this-week' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                This Week
            </button>
        </div>
        
        <!-- Category Filters -->
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $category)
                <button wire:click="applyFilter({{ $category->id }})" 
                    class="px-3 py-1 rounded text-sm {{ $filter == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
        
        <!-- Search -->
        <div class="flex items-center">
            <input wire:model.live.debounce.300ms="search" type="text" 
                placeholder="Search tasks..." 
                class="border rounded px-3 py-1 text-sm">
        </div>
    </div>
    
    <!-- Create Button -->
    <div class="mb-4">
        <button wire:click="showModal" class="bg-blue-600 text-white px-4 py-2 rounded">
            Create New Task
        </button>
    </div>
    
    <!-- Task List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tasks as $task)
                    <tr>
                        <td class="px-6 py-4">
                            <button wire:click="toggleComplete({{ $task->id }})" class="text-sm">
                                @if($task->completed)
                                    <span class="text-green-500">✓</span>
                                @else
                                    <span class="text-gray-400">○</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $task->completed ? 'line-through text-gray-400' : '' }}">
                                {{ $task->title }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $task->category->color }}; color: white;">
                                    {{ $task->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($task->due_date)
                                <span class="{{ now()->isAfter($task->due_date) && !$task->completed ? 'text-red-500' : '' }}">
                                    {{ $task->due_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">No date</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($task->priority === 2)
                                <span class="text-red-500">High</span>
                            @elseif($task->priority === 1)
                                <span class="text-yellow-500">Medium</span>
                            @else
                                <span class="text-green-500">Low</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="editTask({{ $task->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                Edit
                            </button>
                            <button wire:click="deleteTask({{ $task->id }})" 
                                wire:confirm="Are you sure you want to delete this task?"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No tasks found. Start by creating a new task!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
    
    <!-- Task Modal -->
    @if($showTaskModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-medium mb-4">
                    {{ $isEditing ? 'Edit Task' : 'Create New Task' }}
                </h3>
                
                <form wire:submit.prevent="{{ $isEditing ? 'updateTask' : 'createTask' }}">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Title
                        </label>
                        <input 
                            type="text" 
                            id="title" 
                            wire:model.defer="title" 
                            class="w-full px-3 py-2 border rounded-md @error('title') border-red-500 @enderror"
                        >
                        @error('title') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea 
                            id="description" 
                            wire:model.defer="description" 
                            rows="3" 
                            class="w-full px-3 py-2 border rounded-md"
                        ></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Due Date
                        </label>
                        <input 
                            type="date" 
                            id="due_date" 
                            wire:model.defer="due_date" 
                            class="w-full px-3 py-2 border rounded-md"
                        >
                    </div>
                    
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Priority
                        </label>
                        <select 
                            id="priority" 
                            wire:model.defer="priority" 
                            class="w-full px-3 py-2 border rounded-md"
                        >
                            <option value="0">Low</option>
                            <option value="1">Medium</option>
                            <option value="2">High</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <select 
                            id="category_id" 
                            wire:model.defer="category_id" 
                            class="w-full px-3 py-2 border rounded-md"
                        >
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="px-4 py-2 border rounded-md"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md"
                        >
                            {{ $isEditing ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
