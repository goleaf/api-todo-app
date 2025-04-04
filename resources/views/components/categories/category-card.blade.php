@props(['category'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $category->color }}"></div>
                <h3 class="text-lg font-medium text-gray-900">{{ $category->name }}</h3>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('categories.edit', $category) }}" class="text-gray-400 hover:text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        
        @if($category->description)
            <p class="mt-2 text-sm text-gray-500">{{ $category->description }}</p>
        @endif
        
        <div class="mt-4">
            <span class="text-xs text-gray-500">{{ $category->tasks_count ?? 0 }} {{ __('tasks') }}</span>
        </div>
    </div>
</div> 