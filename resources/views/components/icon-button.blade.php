@props(['color' => 'blue'])

@php
    $colors = [
        'blue' => 'text-blue-600 hover:text-blue-800',
        'red' => 'text-red-600 hover:text-red-800',
        'green' => 'text-green-600 hover:text-green-800',
        'gray' => 'text-gray-600 hover:text-gray-800',
    ];
@endphp

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ' . ($colors[$color] ?? '')]) }}>
    {{ $icon ?? '' }}
</button> 