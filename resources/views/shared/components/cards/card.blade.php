@props(['header' => null])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg']) }}>
    @if ($header)
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            {{ $header }}
        </div>
    @endif
    <div class="p-6 bg-white border-b border-gray-200">
        {{ $slot }}
    </div>
</div> 