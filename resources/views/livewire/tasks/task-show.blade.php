<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($task)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 sm:mb-0">
                            <i class="fas fa-clipboard-list text-purple-600 dark:text-purple-400 mr-2"></i>
                            Task Details
                        </h1>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-200 active:bg-purple-600 disabled:opacity-25 transition">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Task
                            </a>
                            
                            <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to List
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 overflow-hidden shadow rounded-lg divide-y divide-gray-200 dark:divide-gray-600">
                        <div class="px-4 py-5 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    {{ $task->title }}
                                </h3>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $task->priority == 5 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $task->priority == 4 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                    {{ $task->priority == 3 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $task->priority == 2 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $task->priority == 1 ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                ">
                                    Priority: {{ $task->priority }}
                                </span>
                            </div>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                Created on {{ $task->created_at->format('M d, Y') }}
                                @if($task->due_date)
                                    · Due {{ $task->due_date->format('M d, Y') }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-600 px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Status
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->completed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                            {{ $task->completed ? 'Completed' : 'Active' }}
                                        </span>
                                        @if($task->completed && $task->completed_at)
                                            <span class="text-gray-500 dark:text-gray-400 text-xs ml-2">
                                                Completed {{ $task->completed_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Description
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($task->description)
                                            {{ $task->description }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 italic">No description provided</span>
                                        @endif
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Category
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($task->category)
                                            {{ $task->category->name }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 italic">No category</span>
                                        @endif
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Created By
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $task->user->name }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div class="px-4 py-4 sm:px-6 flex justify-between items-center">
                            <button wire:click="toggleStatus" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm {{ $task->completed ? 'text-white bg-yellow-600 hover:bg-yellow-700' : 'text-white bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $task->completed ? 'focus:ring-yellow-500' : 'focus:ring-green-500' }}">
                                @if($task->completed)
                                    <i class="fas fa-undo mr-2"></i> Mark as Incomplete
                                @else
                                    <i class="fas fa-check mr-2"></i> Mark as Complete
                                @endif
                            </button>
                            
                            <button wire:click="deleteTask" wire:confirm="Are you sure you want to delete this task?" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Task
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-9xl font-bold text-purple-600 dark:text-purple-400">404</div>
                        <h1 class="mt-4 text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Task Not Found</h1>
                        <p class="mt-4 text-base text-gray-500 dark:text-gray-400">Sorry, the task you are looking for doesn't exist or has been deleted.</p>
                        <div class="mt-6">
                            <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Back to Task List
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 