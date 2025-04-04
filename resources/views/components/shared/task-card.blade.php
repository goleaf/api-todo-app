@props(['task'])

<x-ui.card {{ $attributes }}>
    <div class="space-y-3">
        <div class="flex justify-between items-start">
            <h3 class="text-lg font-medium text-gray-900">
                <a href="{{ route('frontend.tasks.show', $task) }}" class="hover:text-indigo-600">
                    {{ $task->title }}
                </a>
            </h3>
            <div class="flex space-x-1">
                <x-shared.task-priority :priority="$task->priority" />
                <x-shared.task-status :status="$task->status" />
            </div>
        </div>
        
        @if($task->description)
            <p class="text-gray-600 text-sm line-clamp-2">
                {{ Str::limit($task->description, 150) }}
            </p>
        @endif
        
        @if($task->due_date)
            <div class="text-sm {{ $task->is_overdue ? 'text-red-600' : 'text-gray-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ __('tasks.due') }}: {{ $task->due_date->format('M d, Y') }}
                @if($task->is_overdue)
                    <span class="font-medium">{{ __('tasks.overdue') }}</span>
                @endif
            </div>
        @endif
        
        <div class="flex justify-between items-center pt-2">
            <div class="flex flex-wrap gap-1">
                @foreach($task->tags as $tag)
                    <x-shared.tag-badge :tag="$tag" size="sm" />
                @endforeach
            </div>
            
            <div class="text-sm text-gray-500">
                @if($task->category)
                    <span class="inline-flex items-center">
                        <span class="h-2 w-2 rounded-full mr-1" style="background-color: {{ $task->category->color }}"></span>
                        {{ $task->category->name }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</x-ui.card> 