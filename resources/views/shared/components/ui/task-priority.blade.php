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
        'low' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>',
        'medium' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>',
        'high' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 ' . $colors[$priority]]) }}>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icons[$priority] !!}
    </svg>
    <span>{{ $labels[$priority] }}</span>
</div> 