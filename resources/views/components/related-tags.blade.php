@props(['selectedTag' => null, 'relatedTags' => []])

@if(isset($relatedTags) && count($relatedTags) > 0 && isset($selectedTag))
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Related Tags') }}</h3>
        </div>
        <div class="text-sm text-gray-600 mb-2">
            Tags frequently used with "{{ $selectedTag->name }}"
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($relatedTags as $tag)
                <a href="{{ request()->routeIs('dashboard') 
                    ? route('dashboard', ['tag' => $tag->id]) 
                    : route('tasks.index', array_merge(request()->query(), ['tag' => $tag->id])) }}"
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-sm bg-blue-50 text-blue-700 hover:bg-blue-100">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    {{ $tag->name }}
                    <span class="ml-1 text-xs text-gray-500">({{ $tag->count }})</span>
                </a>
            @endforeach
        </div>
    </div>
@endif 