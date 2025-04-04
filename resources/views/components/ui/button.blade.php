@props([
    'href' => null,
    'type' => 'button', // button, submit, link
    'variant' => 'primary', // primary, secondary, danger, etc.
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left' // left, right
])

@php
    $baseClasses = 'inline-flex items-center border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25';

    $variantClasses = match ($variant) {
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:ring-indigo-500',
        'secondary' => 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:ring-red-500',
        // Add more variants as needed
        default => 'bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 focus:ring-indigo-500',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-6 py-3 text-base',
        'md' => 'px-4 py-2 text-xs', // Default size from original button
        default => 'px-4 py-2 text-xs',
    };

    $iconSizeClasses = match ($size) {
        'sm' => 'h-4 w-4',
        'lg' => 'h-6 w-6',
        'md' => 'h-5 w-5', // Default size from original button
        default => 'h-5 w-5',
    };

    $iconMarginClasses = match ($iconPosition) {
        'left' => $size === 'sm' ? 'mr-1' : 'mr-1.5',
        'right' => $size === 'sm' ? 'ml-1' : 'ml-1.5',
        default => 'mr-1.5',
    };

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses]) }}
    @if($tag === 'a')
        href="{{ $href }}"
@else
        type="{{ $type }}" 
    @endif
>
    @if($icon && $iconPosition === 'left')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSizeClasses }} {{ $iconMarginClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            {!! $icon !!} {{-- Expecting SVG path data --}}
        </svg>
    @endif

        {{ $slot }}

    @if($icon && $iconPosition === 'right')
         <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSizeClasses }} {{ $iconMarginClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
             {!! $icon !!} {{-- Expecting SVG path data --}}
         </svg>
@endif 
</{{ $tag }}> 