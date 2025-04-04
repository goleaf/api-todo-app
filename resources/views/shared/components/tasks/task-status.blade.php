@props(['status'])

@php
    $colors = [
        'pending' => 'text-yellow-600 bg-yellow-50',
        'in_progress' => 'text-blue-600 bg-blue-50',
        'completed' => 'text-green-600 bg-green-50'
    ];
    
    $labels = [
        'pending' => __('Pending'),
        'in_progress' => __('In Progress'),
        'completed' => __('Completed')
    ];
    
    $icons = [
        'pending' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'in_progress' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
        'completed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . ($colors[$status] ?? 'text-gray-500 bg-gray-50')]) }}>
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icons[$status] ?? $icons['pending'] !!}
    </svg>
    <span>{{ $labels[$status] ?? $status }}</span>
</span> 