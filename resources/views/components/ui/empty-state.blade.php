@props([
    'icon' => null, // SVG path data
    'message' => __('No items found.'),
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'text-center border-2 border-dashed border-gray-300 rounded-lg p-8']) }}>
    @if($icon)
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            {!! $icon !!}
        </svg>
    @endif
    <h3 class="mt-2 text-sm font-semibold text-gray-900">{{ $message }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-6">
            {{ $action }}
        </div>
    @endisset
</div> 