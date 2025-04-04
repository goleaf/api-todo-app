@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
    'iconLeft' => null,
    'iconRight' => null,
    'fullWidth' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none transition ease-in-out duration-150';
    
    $variantClasses = [
        'primary' => 'text-white bg-indigo-600 hover:bg-indigo-700 focus:border-indigo-700 focus:ring-indigo-500 active:bg-indigo-700',
        'secondary' => 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:border-indigo-300 focus:ring-indigo-500 active:bg-indigo-200',
        'success' => 'text-white bg-green-600 hover:bg-green-700 focus:border-green-700 focus:ring-green-500 active:bg-green-700',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 focus:border-red-700 focus:ring-red-500 active:bg-red-700',
        'white' => 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:border-indigo-300 focus:ring-indigo-500 active:bg-gray-100',
    ];
    
    $sizeClasses = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm leading-4',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
    ];
    
    $disabledClasses = 'opacity-50 cursor-not-allowed';
    $fullWidthClass = $fullWidth ? 'w-full' : '';
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $fullWidthClass . ($disabled ? ' ' . $disabledClasses : '');
@endphp

@if ($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($iconLeft)
            <span class="mr-2">
                {!! $iconLeft !!}
            </span>
        @endif
        <span>{{ $slot }}</span>
        @if ($iconRight)
            <span class="ml-2">
                {!! $iconRight !!}
            </span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if ($iconLeft)
            <span class="mr-2">
                {!! $iconLeft !!}
            </span>
        @endif
        <span>{{ $slot }}</span>
        @if ($iconRight)
            <span class="ml-2">
                {!! $iconRight !!}
            </span>
        @endif
    </button>
@endif 