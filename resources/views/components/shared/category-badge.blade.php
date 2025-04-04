@props(['category'])
 
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
    style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
    {{ $category->name }}
</span> 