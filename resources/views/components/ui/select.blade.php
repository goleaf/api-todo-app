@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'error' => false,
    'helper' => '',
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'size' => 1,
])

@php
    $id = $id ?? $name;
    $hasError = $error !== false;
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    <select 
        name="{{ $name }}"
        id="{{ $id }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        size="{{ $size }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-md shadow-sm ' . 
                      ($hasError ? 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500') .
                      ($disabled ? ' bg-gray-100 cursor-not-allowed' : '')
        ]) }}
    >
        {{ $slot }}
    </select>

    @if($hasError)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($helper && !$hasError)
        <p class="mt-1 text-sm text-gray-500">{{ $helper }}</p>
    @endif
</div> 