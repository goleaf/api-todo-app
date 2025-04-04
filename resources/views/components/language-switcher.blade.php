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

<div class="relative inline-block text-left">
    <div>
        <button type="button" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="language-menu-button" aria-expanded="false" aria-haspopup="true">
            @switch(app()->getLocale())
                @case('en')
                    <span class="mr-2">🇬🇧</span> {{ __('English') }}
                    @break
                @case('ru')
                    <span class="mr-2">🇷🇺</span> {{ __('Русский') }}
                    @break
                @case('lt')
                    <span class="mr-2">🇱🇹</span> {{ __('Lietuvių') }}
                    @break
                @case('fr')
                    <span class="mr-2">🇫🇷</span> {{ __('Français') }}
                    @break
                @case('de')
                    <span class="mr-2">🇩🇪</span> {{ __('Deutsch') }}
                    @break
                @case('es')
                    <span class="mr-2">🇪🇸</span> {{ __('Español') }}
                    @break
                @case('it')
                    <span class="mr-2">🇮🇹</span> {{ __('Italiano') }}
                    @break
                @case('ja')
                    <span class="mr-2">🇯🇵</span> {{ __('日本語') }}
                    @break
                @default
                    <span class="mr-2">🇬🇧</span> {{ __('English') }}
            @endswitch
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div id="language-menu" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1">
        <div class="py-1" role="none">
            <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇬🇧</span> English
            </a>
            <a href="{{ route('language.switch', 'ru') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇷🇺</span> Русский
            </a>
            <a href="{{ route('language.switch', 'lt') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇱🇹</span> Lietuvių
            </a>
            <a href="{{ route('language.switch', 'fr') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇫🇷</span> Français
            </a>
            <a href="{{ route('language.switch', 'de') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇩🇪</span> Deutsch
            </a>
            <a href="{{ route('language.switch', 'es') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇪🇸</span> Español
            </a>
            <a href="{{ route('language.switch', 'it') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇮🇹</span> Italiano
            </a>
            <a href="{{ route('language.switch', 'ja') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                <span class="mr-2">🇯🇵</span> 日本語
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('language-menu-button');
        const menu = document.getElementById('language-menu');
        
        button.addEventListener('click', function() {
            menu.classList.toggle('hidden');
        });
        
        // Close the menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    });
</script> 