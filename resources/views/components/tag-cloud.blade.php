@props(['tags' => []])

@if(count($tags) > 0)
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Tag Cloud') }}</h3>
    </div>
    
    <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
            @php
                // Calculate size based on task count (min size 70%, max size 130%)
                $size = 80 + min(50, $tag->tasks_count * 5);
                
                // Calculate color intensity based on task count (lighter to darker)
                $blue = max(30, 70 - min(40, $tag->tasks_count * 4));
                $fontWeight = $tag->tasks_count > 3 ? 'font-medium' : 'font-normal';
            @endphp
            
            <a href="{{ request()->routeIs('dashboard') 
                ? route('dashboard', ['tag' => $tag->id]) 
                : route('tasks.index', array_merge(request()->query(), ['tag' => $tag->id])) }}"
                class="inline-flex items-center px-2.5 py-1 rounded-md text-sm hover:bg-blue-100 transition-all duration-200 {{ $fontWeight }}"
                style="font-size: {{ $size }}%; background-color: rgba(59, 130, 246, 0.{{ $blue }}); color: rgb({{ 255 - $blue * 2 }}, {{ 255 - $blue * 2 }}, {{ 255 - $blue }});">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                {{ $tag->name }}
                <span class="ml-1 text-xs">({{ $tag->tasks_count }})</span>
            </a>
        @endforeach
    </div>
</div>
@endif 