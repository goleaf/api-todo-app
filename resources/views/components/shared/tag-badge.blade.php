@props(['tag', 'size' => 'md', 'clickable' => true])

@php
$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-xs',
    'lg' => 'px-3 py-1 text-sm',
];

$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
$route = $clickable ? route('frontend.tasks.index', ['tag_id' => $tag->id]) : null;
$dynamicClass = 'rounded-full font-medium';

if (!isset($tag->color) || !$tag->color) {
    $colorClass = 'bg-indigo-100 text-indigo-800 hover:bg-indigo-200';
} else {
    // Use the tag color but ensure text contrast
    $colorClass = "text-gray-900 hover:bg-opacity-75";
}
@endphp

@if($clickable)
    <a 
        href="{{ $route }}" 
        {{ $attributes->merge([
            'class' => $dynamicClass . ' ' . $sizeClass . ' ' . $colorClass . ' inline-flex items-center',
            'style' => isset($tag->color) ? "background-color: {$tag->color};" : '',
        ]) }}
    >
        {{ $tag->name }}
    </a>
@else
    <span 
        {{ $attributes->merge([
            'class' => $dynamicClass . ' ' . $sizeClass . ' ' . $colorClass . ' inline-flex items-center',
            'style' => isset($tag->color) ? "background-color: {$tag->color};" : '',
        ]) }}
    >
        {{ $tag->name }}
    </span>
@endif 