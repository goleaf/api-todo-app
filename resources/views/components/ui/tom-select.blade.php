@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => [], 
    'multiple' => false,
    'placeholder' => null,
    'createOption' => false
])

@php
    $id = $id ?? $name;
    $selected = is_array($selected) ? $selected : [$selected];
@endphp

<div x-data="{ initialized: false }" x-init="
    if (!initialized && typeof TomSelect !== 'undefined') {
        new TomSelect($el.querySelector('select'), {
            plugins: ['remove_button'],
            create: {{ $createOption ? 'true' : 'false' }},
            createOnBlur: {{ $createOption ? 'true' : 'false' }},
            allowEmptyOption: true,
            placeholder: '{{ $placeholder }}',
        });
        initialized = true;
    }
">
    <select 
        id="{{ $id }}" 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
        {{ $multiple ? 'multiple' : '' }}
        {{ $attributes->merge(['class' => 'block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700']) }}
    >
        @if(!$multiple && !$placeholder)
            <option value="">{{ __('Select an option') }}</option>
        @endif
        
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ in_array($value, $selected) ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div> 