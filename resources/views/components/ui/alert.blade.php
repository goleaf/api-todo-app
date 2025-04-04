@props([
    'type' => 'info',
    'dismissible' => false,
    'title' => null
])

@php
$typeClasses = [
    'info' => 'bg-blue-50 border-blue-300 text-blue-800',
    'success' => 'bg-green-50 border-green-300 text-green-800',
    'warning' => 'bg-yellow-50 border-yellow-300 text-yellow-800',
    'error' => 'bg-red-50 border-red-300 text-red-800',
];

$iconClasses = [
    'info' => 'text-blue-400',
    'success' => 'text-green-400',
    'warning' => 'text-yellow-400',
    'error' => 'text-red-400',
];

$icons = [
    'info' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    'success' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    'warning' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
    'error' => '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
];

$classes = 'border px-4 py-3 rounded relative ' . ($typeClasses[$type] ?? $typeClasses['info']);
$iconClass = $iconClasses[$type] ?? $iconClasses['info'];
$icon = $icons[$type] ?? $icons['info'];
@endphp

<div {{ $attributes->merge(['class' => $classes, 'role' => 'alert']) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <div class="{{ $iconClass }}">
                {!! $icon !!}
            </div>
        </div>
        <div class="ml-3">
            @if($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
            @endif
            <div class="text-sm">{{ $slot }}</div>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $iconClass }}" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div> 