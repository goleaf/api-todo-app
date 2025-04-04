@props([
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'placeholder' => null,
    'helperText' => null,
    'errorMessage' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 3,
])

@php
    $id = $id ?? $name;
    $hasError = $errorMessage || $errors->has($name);
    $textareaClasses = 'block w-full rounded-md shadow-sm ' .
        ($hasError ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500') .
        ($disabled ? ' bg-gray-100 cursor-not-allowed' : '');
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        rows="{{ $rows }}"
        @if ($placeholder !== null) placeholder="{{ $placeholder }}" @endif
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        {{ $attributes->merge(['class' => $textareaClasses]) }}
    >{{ $value }}</textarea>
    
    @if ($helperText && !$hasError)
        <p class="mt-1 text-sm text-gray-500">{{ $helperText }}</p>
    @endif
    
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    
    @if ($errorMessage)
        <p class="mt-1 text-sm text-red-600">{{ $errorMessage }}</p>
    @endif
</div> 