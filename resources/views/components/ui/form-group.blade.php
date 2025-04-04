@props([
    'label' => null,
    'for' => null,
    'error' => null,
    'required' => false,
    'helpText' => null
])

@php
    $inputId = $for ?? Str::random(8);
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if ($label)
        <x-ui.input-label :for="$inputId" :value="$label" :required="$required" />
    @endif

    <div class="mt-1">
        {{ $slot }} 
    </div>

    @if ($error)
        <p class="text-sm text-red-600 mt-1">{{ $error }}</p>
    @endif

    @if ($helpText)
        <p class="text-sm text-gray-500 mt-1">{{ $helpText }}</p>
    @endif
</div> 