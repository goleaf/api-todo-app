<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-edit text-purple-600 dark:text-purple-400 mr-2"></i>
                        Edit Task
                    </h1>
                    
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                </div>
                
                <form wire:submit.prevent="updateTask" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <div class="mt-1">
                            <input type="text" wire:model="title" id="title" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Enter task title">
                        </div>
                        @error('title') <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <div class="mt-1">
                            <textarea wire:model="description" id="description" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Enter task details"></textarea>
                        </div>
                        @error('description') <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category (Optional)</label>
                            <div class="mt-1">
                                <select wire:model="category_id" id="category_id" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id') <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date (Optional)</label>
                            <div class="mt-1">
                                <input type="date" wire:model="due_date" id="due_date" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            </div>
                            @error('due_date') <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <div class="mt-1">
                                <select wire:model="priority" id="priority" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                    <option value="1">Low</option>
                                    <option value="2">Normal</option>
                                    <option value="3">Medium</option>
                                    <option value="4">High</option>
                                    <option value="5">Critical</option>
                                </select>
                            </div>
                            @error('priority') <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="completed" id="completed" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="completed" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Mark as completed</label>
                        </div>
                        @error('completed') <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-5">
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-save mr-2"></i>
                            Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 