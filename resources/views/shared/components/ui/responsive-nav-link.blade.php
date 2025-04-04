@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out ' . 
    ($active 
        ? 'border-indigo-500 text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700' 
        : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
    )
]) }}>
    {{ $slot }}
</a> 