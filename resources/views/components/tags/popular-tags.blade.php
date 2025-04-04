@props(['popularTags' => []])

@if(count($popularTags) > 0)
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Popular Tags') }}</h3>
            <a href="{{ route('tags.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                {{ __('Manage Tags') }}
            </a>
        </div>
        
        <div class="flex flex-wrap gap-2">
            @foreach($popularTags as $tag)
                <a 
                    href="{{ request()->routeIs('tasks.*') 
                        ? route('tasks.index', ['tag' => $tag->id]) 
                        : route('dashboard', ['tag' => $tag->id]) }}"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition"
                >
                    <span>{{ $tag->name }}</span>
                    <span class="ml-1 text-xs bg-blue-200 text-blue-800 px-1.5 py-0.5 rounded-full">{{ $tag->tasks_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif 