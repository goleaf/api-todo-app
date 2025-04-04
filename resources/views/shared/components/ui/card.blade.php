@props(['header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200']) }}>
    @if($header)
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="px-4 py-4 sm:px-6 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            {{ $footer }}
        </div>
    @endif
</div> 