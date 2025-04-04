@props([
    'type' => 'text',
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'placeholder' => null,
    'helperText' => null,
    'errorMessage' => null,
    'required' => false,
    'disabled' => false,
    'leadingIcon' => null,
    'trailingIcon' => null,
    'autocomplete' => 'off'
])

@php
    $id = $id ?? $name;
    $hasError = $errorMessage || $errors->has($name);
    $inputClasses = 'block w-full rounded-md shadow-sm ' .
        ($hasError ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500') .
        ($disabled ? ' bg-gray-100 cursor-not-allowed' : '') .
        ($leadingIcon ? ' pl-10' : '') .
        ($trailingIcon ? ' pr-10' : '');
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
    
    <div class="relative rounded-md shadow-sm">
        @if ($leadingIcon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {!! $leadingIcon !!}
            </div>
        @endif
        
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            @if ($value !== null) value="{{ $value }}" @endif
            @if ($placeholder !== null) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            autocomplete="{{ $autocomplete }}"
            {{ $attributes->merge(['class' => $inputClasses]) }}
        />
        
        @if ($trailingIcon)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {!! $trailingIcon !!}
            </div>
        @endif
    </div>
    
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