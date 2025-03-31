<div {{ $attributes->merge(['class' => 'app-option']) }}>
    @if(is_array($value) || is_object($value))
        <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
    @elseif(is_bool($value))
        {{ $value ? 'true' : 'false' }}
    @else
        {{ $value }}
    @endif

    {{ $slot }}
</div> 