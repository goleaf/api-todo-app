@props(['currentSort' => 'created_at', 'currentDirection' => 'desc'])

<div class="flex items-center space-x-2">
    <span class="text-sm text-gray-500">{{ __('Sort by:') }}</span>
    
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                @switch($currentSort)
                    @case('title')
                        {{ __('Title') }}
                        @break
                    @case('due_date')
                        {{ __('Due Date') }}
                        @break
                    @case('priority')
                        {{ __('Priority') }}
                        @break
                    @case('created_at')
                        {{ __('Created Date') }}
                        @break
                    @default
                        {{ __('Created Date') }}
                @endswitch

                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('dashboard', ['sort' => 'title', 'direction' => $currentSort === 'title' && $currentDirection === 'asc' ? 'desc' : 'asc'])">
                {{ __('Title') }}
                @if($currentSort === 'title')
                    <span class="ml-1">
                        @if($currentDirection === 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    </span>
                @endif
            </x-dropdown-link>

            <x-dropdown-link :href="route('dashboard', ['sort' => 'due_date', 'direction' => $currentSort === 'due_date' && $currentDirection === 'asc' ? 'desc' : 'asc'])">
                {{ __('Due Date') }}
                @if($currentSort === 'due_date')
                    <span class="ml-1">
                        @if($currentDirection === 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    </span>
                @endif
            </x-dropdown-link>

            <x-dropdown-link :href="route('dashboard', ['sort' => 'priority', 'direction' => $currentSort === 'priority' && $currentDirection === 'asc' ? 'desc' : 'asc'])">
                {{ __('Priority') }}
                @if($currentSort === 'priority')
                    <span class="ml-1">
                        @if($currentDirection === 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    </span>
                @endif
            </x-dropdown-link>

            <x-dropdown-link :href="route('dashboard', ['sort' => 'created_at', 'direction' => $currentSort === 'created_at' && $currentDirection === 'asc' ? 'desc' : 'asc'])">
                {{ __('Created Date') }}
                @if($currentSort === 'created_at')
                    <span class="ml-1">
                        @if($currentDirection === 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    </span>
                @endif
            </x-dropdown-link>
        </x-slot>
    </x-dropdown>
</div> 