@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
    $locales = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'ru' => 'Русский',
        'zh' => '中文',
        'ja' => '日本語',
        'ko' => '한국어'
    ];
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = !open">
        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
            <div class="flex items-center">
                <span class="mr-2">{{ strtoupper(app()->getLocale()) }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                </svg>
            </div>
        </button>
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $align === 'right' ? 'origin-top-right right-0' : 'origin-top-left left-0' }}"
         style="display: none;"
         @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            @foreach($locales as $locale => $name)
                <a href="{{ route('language.switch', $locale) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === $locale ? 'bg-gray-100' : '' }}">
                    {{ $name }}
                </a>
            @endforeach
        </div>
    </div>
</div> 