@props(['priority'])

@php
    $colors = [
        'low' => 'text-green-600',
        'medium' => 'text-yellow-600',
        'high' => 'text-red-600'
    ];
    
    $labels = [
        'low' => __('Low'),
        'medium' => __('Medium'),
        'high' => __('High')
    ];
    
    $icons = [
        'low' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />',
        'medium' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 12h8M8 17h8" />',
        'high' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ' . $colors[$priority]]) }} title="{{ $labels[$priority] }}">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icons[$priority] !!}
    </svg>
    <span>{{ $labels[$priority] }}</span>
</div> 