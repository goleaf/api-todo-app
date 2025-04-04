@props(['task'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <div class="p-4">
        <div class="flex items-start gap-4">
            <!-- Checkbox -->
            <div class="flex-none pt-1">
                <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors {{ $task->completed ? 'bg-blue-500 border-blue-500' : '' }}">
                        @if($task->completed)
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </button>
                </form>
            </div>

            <!-- Task Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium {{ $task->completed ? 'line-through text-gray-500' : 'text-gray-900' }}">
                        <a href="{{ route('tasks.show', $task) }}" class="hover:text-blue-600">
                            {{ $task->title }}
                        </a>
                    </h3>
                    <div class="flex items-center gap-2">
                        <x-tasks.task-priority :priority="$task->priority_text" />
                        <x-tasks.task-status :status="$task->status" />
                    </div>
                </div>

                @if($task->description)
                    <p class="mt-1 text-sm text-gray-500">{{ Str::limit($task->description, 100) }}</p>
                @endif

                <div class="mt-3 flex flex-wrap gap-3">
                    <x-tasks.task-category :category="$task->category" />
                    <x-tasks.task-tags :tags="$task->tags" />
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <x-tasks.due-date :date="$task->due_date" />

                    <div class="flex items-center gap-2">
                        <a href="{{ route('tasks.show', $task) }}" class="text-gray-400 hover:text-blue-500" title="{{ __('View Task') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('tasks.edit', $task) }}" class="text-gray-400 hover:text-blue-500" title="{{ __('Edit Task') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this task?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500" title="{{ __('Delete Task') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 