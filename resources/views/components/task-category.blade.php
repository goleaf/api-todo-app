@props(['category'])

@if($category)
    <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium" style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
        </svg>
        {{ $category->name }}
    </span>
@endif 