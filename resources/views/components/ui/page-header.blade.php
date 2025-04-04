@props([
    'title'
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6']) }}>
    <h1 class="text-2xl font-semibold text-gray-900 mb-2 sm:mb-0">
        {{ $title }}
    </h1>
    @isset($actions)
        <div class="flex space-x-2 flex-shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div> 