@props(['task'])

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 sm:p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <h3 class="text-lg font-medium text-gray-900">
                        <a href="{{ route('tasks.show', $task) }}" class="hover:underline">
                            {{ $task->title }}
                        </a>
                    </h3>
                    @if($task->priority)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                              ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    @endif
                </div>

                <div class="mt-2 text-sm text-gray-600 line-clamp-2">
                    {{ $task->description }}
                </div>

                <div class="mt-4 flex items-center text-sm">
                    @if($task->category)
                        <div class="mr-4">
                            <span class="font-medium text-gray-600">{{ __('Category:') }}</span>
                            <span class="ml-1">{{ $task->category->name }}</span>
                        </div>
                    @endif

                    @if($task->due_date)
                        <div class="mr-4">
                            <span class="font-medium text-gray-600">{{ __('Due:') }}</span>
                            <span class="ml-1 {{ $task->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                {{ $task->due_date->format('M d, Y') }}
                            </span>
                        </div>
                    @endif

                    <div>
                        <span class="font-medium text-gray-600">{{ __('Status:') }}</span>
                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $task->completed ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $task->completed ? __('Completed') : __('Pending') }}
                        </span>
                    </div>
                </div>

                <div class="mt-3">
                    <x-components.tags.task-tags :tags="$task->tags" />
                </div>
            </div>

            <div class="ml-4 flex flex-col items-end space-y-2">
                <div class="flex space-x-1">
                    <form action="{{ route('tasks.toggle-completion', $task) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="p-1 rounded-full hover:bg-gray-100">
                            <svg class="h-5 w-5 {{ $task->completed ? 'text-green-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </form>
                    <a href="{{ route('tasks.edit', $task) }}" class="p-1 rounded-full hover:bg-gray-100">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this task?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 rounded-full hover:bg-gray-100">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
                <span class="text-xs text-gray-500">
                    {{ __('Created:') }} {{ $task->created_at->format('M d, Y') }}
                </span>
            </div>
        </div>
    </div>
</div> 