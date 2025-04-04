@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 ' . $class]) }}>
    {{ $slot }}
</div> 