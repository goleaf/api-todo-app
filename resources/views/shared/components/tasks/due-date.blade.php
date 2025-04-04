@props(['date'])

@if($date)
    @php
        $isOverdue = $date < now() && !request()->routeIs('tasks.completed');
        $isToday = $date->isToday();
        $isTomorrow = $date->isTomorrow();
        
        if ($isOverdue) {
            $colorClass = 'text-red-600';
        } elseif ($isToday) {
            $colorClass = 'text-orange-600';
        } elseif ($isTomorrow) {
            $colorClass = 'text-yellow-600';
        } else {
            $colorClass = 'text-gray-600';
        }
    @endphp
    
    <div class="flex items-center text-sm {{ $colorClass }}">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>
            @if($isOverdue)
                {{ __('Overdue') }}: {{ $date->format('M d, Y') }} ({{ $date->diffForHumans() }})
            @elseif($isToday)
                {{ __('Due Today') }} @ {{ $date->format('H:i') }}
            @elseif($isTomorrow)
                {{ __('Due Tomorrow') }} @ {{ $date->format('H:i') }}
            @else
                {{ __('Due') }} {{ $date->format('M d, Y') }}
            @endif
        </span>
    </div>
@else
    <div class="text-sm text-gray-500">
        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>{{ __('No due date') }}</span>
    </div>
@endif 