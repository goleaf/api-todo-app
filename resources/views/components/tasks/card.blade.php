@props(['task'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition duration-300 h-full">
    <div class="p-4 h-full flex flex-col">
        <div class="flex justify-between items-start">
            <a href="{{ route('tasks.show', $task) }}" class="text-lg font-semibold mb-2 text-gray-900 hover:text-indigo-700">
                {{ $task->title }}
            </a>
            <span>
                <x-tasks.status :status="$task->status" />
            </span>
        </div>

        <div class="text-sm text-gray-600 mb-4 line-clamp-3">
            {{ Str::limit(strip_tags($task->description), 150) }}
        </div>

        <div class="mt-auto">
            <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    @if($task->due_date)
                        <span class="{{ $task->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                            {{ $task->due_date->format('M d, Y') }}
                        </span>
                    @else
                        <span>No due date</span>
                    @endif
                </div>

                <div>
                    <x-tasks.priority :priority="$task->priority" />
                </div>
            </div>

            @if($task->category)
                <div class="mb-2">
                    <x-tasks.category :category="$task->category" />
                </div>
            @endif

            @if($task->tags && $task->tags->count() > 0)
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($task->tags->take(3) as $tag)
                        <x-tasks.tag :tag="$tag" />
                    @endforeach
                    
                    @if($task->tags->count() > 3)
                        <span class="inline-flex text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                            +{{ $task->tags->count() - 3 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div> 