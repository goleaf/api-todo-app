@props(['tags' => []])

@if(count($tags) > 0)
    <div class="flex flex-wrap gap-1.5">
        @foreach($tags as $tag)
            <a href="{{ request()->routeIs('tasks.*') 
                ? route('tasks.index', ['tag' => $tag->id]) 
                : route('dashboard', ['tag' => $tag->id]) }}"
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition"
            >
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                {{ $tag->name }}
            </a>
        @endforeach
    </div>
@endif 