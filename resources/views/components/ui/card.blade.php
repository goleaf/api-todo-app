@props([
    'header' => null,
    'footer' => null,
    'headerClass' => 'border-b border-gray-200 py-4 px-6',
    'bodyClass' => 'p-6',
    'footerClass' => 'border-t border-gray-200 py-4 px-6',
    'rounded' => true
])

<div {{ $attributes->merge(['class' => 'bg-white shadow overflow-hidden' . ($rounded ? ' rounded-lg' : '')]) }}>
    @if ($header)
        <div class="{{ $headerClass }}">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="{{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div> 